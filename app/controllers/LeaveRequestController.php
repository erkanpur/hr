<?php

require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/LeaveType.php'; // Needed to fetch leave types for the form
require_once __DIR__ . '/../core/Database.php'; // For models if not passed explicitly

class LeaveRequestController {

    private function loadView($viewName, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            include $viewFile; // Content captured by ob_start() in index.php
        } else {
            // Basic error, or could load a generic 404 view
            echo "Error: View '{$viewName}' not found."; 
        }
    }

    private function redirect($url_path) {
        $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($base_url === '.' || $base_url === '') $base_url = ''; // Adjust for root installs
        header("Location: " . $base_url . $url_path);
        exit;
    }

    // Method to get current employee ID (placeholder - integrate with actual auth later)
    private function getCurrentEmployeeId() {
        // In a real application, this would come from the session after login
        if (isset($_SESSION['user']) && isset($_SESSION['user']['employee_id'])) {
            return (int)$_SESSION['user']['employee_id'];
        }
        // For now, if no session, return a default/test employee ID or handle error
        // This is a placeholder and needs proper authentication integration.
        // Returning 0 or null would cause issues if not handled, so let's use a test ID if no session.
        // Or, better, redirect to login if no user. For now, let's assume an ID for testing.
        // if (!isset($_SESSION['user'])) {
        //     $_SESSION['error_message'] = "You must be logged in to apply for leave.";
        //     $this->redirect('/login'); // Assuming a login route
        // }
        return 1; // Placeholder:  Assume employee ID 1 for now if no session.
    }

    public function apply() {
        global $title;
        $title = 'Apply for Leave';

        $leaveTypeModel = new LeaveType();
        $active_leave_types = $leaveTypeModel->getAll(true); // Fetch only active leave types

        if ($this->getCurrentEmployeeId() === null) { // Example check
             $_SESSION['error_message'] = "Login required to apply for leave.";
             $this->redirect('/'); // Or to login page
             return;
        }

        $this->loadView('leave_requests/apply', [
            'title' => $title,
            'leave_types' => $active_leave_types,
            'old_input' => [] // Initialize for the form
        ]);
    }

    public function store_request() {
        global $title;
        $title = 'Apply for Leave'; // For re-displaying form on error
        $errors = [];
        $old_input = $_POST;

        $employee_id = $this->getCurrentEmployeeId();
        if ($employee_id === null) {
             $_SESSION['error_message'] = "Login required. Could not process leave application.";
             $this->redirect('/'); // Or to login page
             return;
        }

        // --- Validation ---
        if (empty($_POST['leave_type_id'])) {
            $errors['leave_type_id'] = 'Please select a leave type.';
        }
        if (empty($_POST['start_date'])) {
            $errors['start_date'] = 'Start date is required.';
        }
        if (empty($_POST['end_date'])) {
            $errors['end_date'] = 'End date is required.';
        }
        if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            try {
                $startDate = new DateTime($_POST['start_date']);
                $endDate = new DateTime($_POST['end_date']);
                if ($startDate > $endDate) {
                    $errors['end_date'] = 'End date cannot be before the start date.';
                }
            } catch (Exception $e) {
                $errors['start_date'] = 'Invalid date format for start or end date.';
            }
        }
        if (empty($_POST['reason'])) {
            $errors['reason'] = 'Reason for leave is required.';
        }
        // Add more validation as needed (e.g., check against leave balances if implemented)

        if (!empty($errors)) {
            $leaveTypeModel = new LeaveType();
            $active_leave_types = $leaveTypeModel->getAll(true);
            $this->loadView('leave_requests/apply', [
                'errors' => $errors, 
                'old_input' => $old_input, 
                'title' => $title,
                'leave_types' => $active_leave_types
            ]);
            return;
        }

        // --- Process Request ---
        $leaveRequest = new LeaveRequest();
        $leaveRequest->employee_id = $employee_id;
        $leaveRequest->leave_type_id = (int)$_POST['leave_type_id'];
        $leaveRequest->start_date = $_POST['start_date'];
        $leaveRequest->end_date = $_POST['end_date'];
        $leaveRequest->reason = $_POST['reason'];
        // $leaveRequest->status is 'pending' by default in model/DB
        // $leaveRequest->days_requested will be calculated by its save() or calculateDays() method

        try {
            if ($leaveRequest->save()) { // save() also calculates days_requested
                $_SESSION['success_message'] = "Leave request submitted successfully. Your request for " . htmlspecialchars($leaveRequest->days_requested) . " day(s) is pending approval.";
                // Redirect to leave history page (to be created) or a confirmation page
                $this->redirect('/leave_requests/history'); 
            } else {
                $errors['database'] = 'Failed to submit leave request. Please try again.';
            }
        } catch (PDOException $e) {
             $errors['database'] = 'Database error while submitting leave request: ' . $e->getMessage();
        } catch (Exception $e) { // Catch other exceptions like from DateTime
            $errors['general'] = 'An unexpected error occurred: ' . $e->getMessage();
        }
        
        // If save failed or an exception occurred
        $leaveTypeModel = new LeaveType();
        $active_leave_types = $leaveTypeModel->getAll(true);
        $this->loadView('leave_requests/apply', [
            'errors' => $errors, 
            'old_input' => $old_input, 
            'title' => $title,
            'leave_types' => $active_leave_types
        ]);
    }

    public function history() {
        global $title;
        $title = 'My Leave History';

        $employee_id = $this->getCurrentEmployeeId();

        if (!$employee_id) { // Assuming getCurrentEmployeeId returns 0 or null if not found
            $_SESSION['error_message'] = "You must be logged in to view leave history.";
            $this->redirect('/'); // Redirect to homepage or login
            return;
        }

        $leaveRequestModel = new LeaveRequest();
        $requests_data = $leaveRequestModel->getAllByEmployee($employee_id);

        $this->loadView('leave_requests/history', [
            'leave_requests' => $requests_data,
            'title' => $title
        ]);
    }

    // --- Managerial/Administrative Actions ---

    public function manage() { // Renamed from manage_requests
        global $title;
        // Determine status filter from GET parameter
        $status_filter = null;
        $valid_statuses = ['pending', 'approved', 'rejected', 'cancelled', 'all'];
        if (isset($_GET['status']) && in_array(strtolower($_GET['status']), $valid_statuses)) {
            $status_filter = strtolower($_GET['status']);
            if ($status_filter === 'all') {
                $status_filter = null; // Pass null to getAllRequests to fetch all
            }
        }

        $page_subtitle = $status_filter ? ucfirst($status_filter) . " " : "All ";
        $title = $page_subtitle . 'Leave Requests';

        // Future: Add authorization check here to ensure only managers/admins can access.
        // if (!$this->isUserAdminOrManager()) {
        //     $_SESSION['error_message'] = "You are not authorized to manage leave requests.";
        //     $this->redirect('/');
        //     return;
        // }

        $leaveRequestModel = new LeaveRequest();
        $requests = $leaveRequestModel->getAllRequests($status_filter);

        $this->loadView('leave_requests/manage', [
            'title' => $title,
            'leave_requests' => $requests,
            'current_filter' => $status_filter ?? 'all' // Pass current filter to view for active tab styling
        ]);
    }

    public function approve_request($request_id) {
        // Future: Authorization check
        $request_id = filter_var($request_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$request_id) {
            $_SESSION['error_message'] = "Invalid request ID for approval.";
            $this->redirect('/leave_requests/manage');
            return;
        }

        $approver_employee_id = $this->getCurrentEmployeeId(); // Placeholder
        // In a real system, ensure approver_id is valid and authorized

        // For now, comments are not handled with GET-based approval.
        // If using POST, comments would be: $comments = $_POST['comments_by_approver'] ?? null;
        $comments = "Approved via system."; 

        $leaveRequestModel = new LeaveRequest();
        if ($leaveRequestModel->updateStatus($request_id, 'approved', $approver_employee_id, $comments)) {
            $_SESSION['success_message'] = "Leave request #{$request_id} approved successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to approve leave request #{$request_id}. It might have been already actioned or an error occurred.";
        }
        $this->redirect('/leave_requests/manage?status=pending'); // Redirect back to pending, or manage all
    }

    public function reject_request($request_id) {
        // Future: Authorization check
        $request_id = filter_var($request_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$request_id) {
            $_SESSION['error_message'] = "Invalid request ID for rejection.";
            $this->redirect('/leave_requests/manage');
            return;
        }

        $approver_employee_id = $this->getCurrentEmployeeId(); // Placeholder

        // For GET-based rejection, comments are tricky. We could have a default one.
        // If using POST (e.g., from a modal with a textarea for comments):
        // $comments = !empty($_POST['comments_by_approver']) ? $_POST['comments_by_approver'] : 'Rejected without detailed comment.';
        // For now, as it's a GET request as per plan for simplicity:
        $comments = "Rejected via system."; // Or prompt user via JS if possible, or redirect to a page with a comment form.

        $leaveRequestModel = new LeaveRequest();
        if ($leaveRequestModel->updateStatus($request_id, 'rejected', $approver_employee_id, $comments)) {
            $_SESSION['success_message'] = "Leave request #{$request_id} rejected successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to reject leave request #{$request_id}. It might have been already actioned or an error occurred.";
        }
        $this->redirect('/leave_requests/manage?status=pending'); // Redirect back to pending, or manage all
    }

// Make sure these new methods are within the LeaveRequestController class
} // End of LeaveRequestController class (ensure this is correctly placed)
?>
