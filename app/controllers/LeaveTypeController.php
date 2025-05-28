<?php

require_once __DIR__ . '/../models/LeaveType.php';
require_once __DIR__ . '/../core/Database.php'; // For model if not passed explicitly

class LeaveTypeController {

    private function loadView($viewName, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            include $viewFile; // Content captured by ob_start() in index.php
        } else {
            echo "Error: View '{$viewName}' not found."; // Basic error
        }
    }

    public function index() {
        global $title;
        $title = 'Manage Leave Types';
        
        $leaveTypeModel = new LeaveType();
        $leave_types = $leaveTypeModel->getAll(); // Get all, including inactive for management view

        $this->loadView('leave_types/index', ['leave_types' => $leave_types, 'title' => $title]);
    }

    public function add() {
        global $title;
        $title = 'Add New Leave Type';
        $this->loadView('leave_types/add', ['title' => $title]);
    }

    public function store() {
        global $title;
        $title = 'Add New Leave Type'; // For re-displaying form on error
        $errors = [];
        $old_input = $_POST;

        if (empty($_POST['name'])) {
            $errors['name'] = 'Leave type name is required.';
        }
        // days_allocated_annually can be 0 or positive, or null if empty string submitted
        if (isset($_POST['days_allocated_annually']) && $_POST['days_allocated_annually'] !== '' && !ctype_digit($_POST['days_allocated_annually'])) {
            $errors['days_allocated_annually'] = 'Days allocated must be a non-negative integer if provided.';
        }


        if (!empty($errors)) {
            $this->loadView('leave_types/add', ['errors' => $errors, 'old_input' => $old_input, 'title' => $title]);
            return;
        }

        $leaveType = new LeaveType();
        $leaveType->name = $_POST['name'];
        $leaveType->description = $_POST['description'] ?? null;
        $leaveType->days_allocated_annually = (isset($_POST['days_allocated_annually']) && $_POST['days_allocated_annually'] !== '') ? (int)$_POST['days_allocated_annually'] : null;
        $leaveType->is_paid = isset($_POST['is_paid']) ? (bool)$_POST['is_paid'] : false; // Checkbox might not be sent if unchecked
        $leaveType->is_active = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : false; // Checkbox

        try {
            if ($leaveType->save()) {
                $_SESSION['success_message'] = "Leave type '{$leaveType->name}' added successfully!";
                $this->redirect('/leave_types');
            } else {
                $errors['database'] = 'Failed to save leave type.';
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'name') !== false) {
                 $errors['name'] = 'This leave type name already exists.';
            } else {
                 $errors['database'] = 'An unexpected database error occurred: ' . $e->getMessage();
            }
        }
        
        $this->loadView('leave_types/add', ['errors' => $errors, 'old_input' => $old_input, 'title' => $title]);
    }

    public function edit($id) {
        global $title;
        
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            $_SESSION['error_message'] = "Invalid Leave Type ID specified for editing.";
            $this->redirect('/leave_types');
            return;
        }

        $leaveTypeModel = new LeaveType();
        $leave_type = $leaveTypeModel->getById($id);

        if (!$leave_type) {
            $_SESSION['error_message'] = "Leave Type not found with ID: {$id} for editing.";
            $this->redirect('/leave_types');
            return;
        }
        
        $title = 'Edit Leave Type: ' . htmlspecialchars($leave_type['name']);
        $this->loadView('leave_types/edit', ['leave_type' => $leave_type, 'old_input' => $leave_type, 'title' => $title]);
    }

    public function update_type($id) {
        global $title;
        $errors = [];
        $old_input = $_POST; // Submitted data
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if (!$id) {
            $_SESSION['error_message'] = "Invalid Leave Type ID for update.";
            $this->redirect('/leave_types');
            return;
        }
        
        $leaveTypeModel = new LeaveType();
        $existing_leave_type = $leaveTypeModel->getById($id); // For title and to ensure it exists
        if (!$existing_leave_type) {
            $_SESSION['error_message'] = "Leave Type not found for update.";
            $this->redirect('/leave_types');
            return;
        }
        $title = 'Edit Leave Type: ' . htmlspecialchars($existing_leave_type['name']);


        if (empty($_POST['name'])) {
            $errors['name'] = 'Leave type name is required.';
        }
        if (isset($_POST['days_allocated_annually']) && $_POST['days_allocated_annually'] !== '' && !ctype_digit($_POST['days_allocated_annually'])) {
            $errors['days_allocated_annually'] = 'Days allocated must be a non-negative integer if provided.';
        }

        if (!empty($errors)) {
            $this->loadView('leave_types/edit', ['errors' => $errors, 'old_input' => $old_input, 'leave_type' => $existing_leave_type, 'title' => $title]);
            return;
        }

        $leaveTypeToUpdate = new LeaveType();
        $leaveTypeToUpdate->id = $id;
        $leaveTypeToUpdate->name = $_POST['name'];
        $leaveTypeToUpdate->description = $_POST['description'] ?? null;
        $leaveTypeToUpdate->days_allocated_annually = (isset($_POST['days_allocated_annually']) && $_POST['days_allocated_annually'] !== '') ? (int)$_POST['days_allocated_annually'] : null;
        $leaveTypeToUpdate->is_paid = isset($_POST['is_paid']) ? (bool)$_POST['is_paid'] : false;
        $leaveTypeToUpdate->is_active = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : false;

        try {
            if ($leaveTypeToUpdate->save()) { // save() handles update due to ID being set
                $_SESSION['success_message'] = "Leave type '{$leaveTypeToUpdate->name}' updated successfully!";
                $this->redirect('/leave_types');
            } else {
                $errors['database'] = 'Failed to update leave type. No changes might have been made or an error occurred.';
                 // Repopulate form with submitted data that failed to save
                $this->loadView('leave_types/edit', ['errors' => $errors, 'old_input' => $old_input, 'leave_type' => $existing_leave_type, 'title' => $title]);
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'name') !== false) {
                 $errors['name'] = 'This leave type name already exists.';
            } else {
                 $errors['database'] = 'An unexpected database error occurred: ' . $e->getMessage();
            }
            $this->loadView('leave_types/edit', ['errors' => $errors, 'old_input' => $old_input, 'leave_type' => $existing_leave_type, 'title' => $title]);
        }
    }

    public function delete_type($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            $_SESSION['error_message'] = "Invalid Leave Type ID for deletion.";
            $this->redirect('/leave_types');
            return;
        }

        $leaveTypeModel = new LeaveType();
        // Optional: Fetch name before deleting for message, but model handles "in use" error.
        // $leave_type = $leaveTypeModel->getById($id);
        // if (!$leave_type) {
        //     $_SESSION['error_message'] = "Leave Type not found with ID: {$id}.";
        //     $this->redirect('/leave_types');
        //     return;
        // }

        if ($leaveTypeModel->delete($id)) {
            $_SESSION['success_message'] = "Leave Type (ID: {$id}) deleted successfully.";
        } else {
            // Model's delete method now prints specific error for FK violation
            $_SESSION['error_message'] = "Failed to delete Leave Type (ID: {$id}). It might be in use or already deleted.";
        }
        $this->redirect('/leave_types');
    }
    
    private function redirect($url_path) {
        $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($base_url === '.' || $base_url === '') $base_url = '';
        header("Location: " . $base_url . $url_path);
        exit;
    }
}
?>
