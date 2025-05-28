<?php

require_once __DIR__ . '/../models/PerformanceReviewPeriod.php';
require_once __DIR__ . '/../core/Database.php';

class ReviewPeriodController {

    private function loadView($viewName, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "Error: View '{$viewName}' not found."; 
        }
    }

    private function redirect($url_path) {
        $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($base_url === '.' || $base_url === '') $base_url = '';
        header("Location: " . $base_url . $url_path);
        exit;
    }

    public function index() {
        global $title;
        $title = 'Manage Review Periods';
        
        $periodModel = new PerformanceReviewPeriod();
        $status_filter = $_GET['status'] ?? null;
        $periods = $periodModel->getAll($status_filter); 

        $this->loadView('review_periods/index', ['periods' => $periods, 'title' => $title, 'current_filter' => $status_filter ?? 'all']);
    }

    public function add() {
        global $title;
        $title = 'Add New Review Period';
        // Define available statuses for the form dropdown
        $available_statuses = ['setup', 'open_for_input', 'in_review', 'closed', 'archived'];
        $this->loadView('review_periods/add', ['title' => $title, 'old_input' => [], 'available_statuses' => $available_statuses]);
    }

    public function store() {
        global $title;
        $title = 'Add New Review Period'; 
        $errors = [];
        $old_input = $_POST;
        $available_statuses = ['setup', 'open_for_input', 'in_review', 'closed', 'archived'];


        if (empty($_POST['name'])) {
            $errors['name'] = 'Period name is required.';
        }
        if (empty($_POST['start_date'])) {
            $errors['start_date'] = 'Start date is required.';
        }
        if (empty($_POST['end_date'])) {
            $errors['end_date'] = 'End date is required.';
        }
        if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            if (new DateTime($_POST['start_date']) > new DateTime($_POST['end_date'])) {
                $errors['end_date'] = 'End date cannot be before start date.';
            }
        }
        if (empty($_POST['status']) || !in_array($_POST['status'], $available_statuses)) {
            $errors['status'] = 'Invalid status selected.';
        }


        if (!empty($errors)) {
            $this->loadView('review_periods/add', ['errors' => $errors, 'old_input' => $old_input, 'title' => $title, 'available_statuses' => $available_statuses]);
            return;
        }

        $period = new PerformanceReviewPeriod();
        $period->name = $_POST['name'];
        $period->start_date = $_POST['start_date'];
        $period->end_date = $_POST['end_date'];
        $period->status = $_POST['status'];

        try {
            if ($period->save()) {
                $_SESSION['success_message'] = "Review period '{$period->name}' added successfully!";
                $this->redirect('/review_periods');
            } else {
                $errors['database'] = 'Failed to save review period.';
                 $this->loadView('review_periods/add', ['errors' => $errors, 'old_input' => $old_input, 'title' => $title, 'available_statuses' => $available_statuses]);
            }
        } catch (PDOException $e) {
             $errors['database'] = 'Database error: ' . $e->getMessage();
             $this->loadView('review_periods/add', ['errors' => $errors, 'old_input' => $old_input, 'title' => $title, 'available_statuses' => $available_statuses]);
        }
    }

    public function edit($id) {
        global $title;
        $available_statuses = ['setup', 'open_for_input', 'in_review', 'closed', 'archived'];
        
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            $_SESSION['error_message'] = "Invalid Review Period ID.";
            $this->redirect('/review_periods');
            return;
        }

        $periodModel = new PerformanceReviewPeriod();
        $period = $periodModel->getById($id);

        if (!$period) {
            $_SESSION['error_message'] = "Review Period not found with ID: {$id}.";
            $this->redirect('/review_periods');
            return;
        }
        
        $title = 'Edit Review Period: ' . htmlspecialchars($period['name']);
        $this->loadView('review_periods/edit', ['period' => $period, 'old_input' => $period, 'title' => $title, 'available_statuses' => $available_statuses]);
    }

    public function update_period($id) {
        global $title;
        $errors = [];
        $old_input = $_POST; 
        $available_statuses = ['setup', 'open_for_input', 'in_review', 'closed', 'archived'];
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if (!$id) {
            $_SESSION['error_message'] = "Invalid Review Period ID for update.";
            $this->redirect('/review_periods');
            return;
        }
        
        $periodModel = new PerformanceReviewPeriod();
        $existing_period = $periodModel->getById($id);
        if (!$existing_period) {
            $_SESSION['error_message'] = "Review Period not found for update.";
            $this->redirect('/review_periods');
            return;
        }
        $title = 'Edit Review Period: ' . htmlspecialchars($existing_period['name']);

        if (empty($_POST['name'])) {
            $errors['name'] = 'Period name is required.';
        }
        if (empty($_POST['start_date'])) {
            $errors['start_date'] = 'Start date is required.';
        }
        if (empty($_POST['end_date'])) {
            $errors['end_date'] = 'End date is required.';
        }
        if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
            if (new DateTime($_POST['start_date']) > new DateTime($_POST['end_date'])) {
                $errors['end_date'] = 'End date cannot be before start date.';
            }
        }
        if (empty($_POST['status']) || !in_array($_POST['status'], $available_statuses)) {
            $errors['status'] = 'Invalid status selected.';
        }

        if (!empty($errors)) {
            $this->loadView('review_periods/edit', ['errors' => $errors, 'old_input' => $old_input, 'period' => $existing_period, 'title' => $title, 'available_statuses' => $available_statuses]);
            return;
        }

        $periodToUpdate = new PerformanceReviewPeriod();
        $periodToUpdate->id = $id;
        $periodToUpdate->name = $_POST['name'];
        $periodToUpdate->start_date = $_POST['start_date'];
        $periodToUpdate->end_date = $_POST['end_date'];
        $periodToUpdate->status = $_POST['status'];

        try {
            if ($periodToUpdate->save()) { 
                $_SESSION['success_message'] = "Review period '{$periodToUpdate->name}' updated successfully!";
                $this->redirect('/review_periods');
            } else {
                $errors['database'] = 'Failed to update period. No changes or error.';
                $this->loadView('review_periods/edit', ['errors' => $errors, 'old_input' => $old_input, 'period' => $existing_period, 'title' => $title, 'available_statuses' => $available_statuses]);
            }
        } catch (PDOException $e) {
             $errors['database'] = 'Database error: ' . $e->getMessage();
             $this->loadView('review_periods/edit', ['errors' => $errors, 'old_input' => $old_input, 'period' => $existing_period, 'title' => $title, 'available_statuses' => $available_statuses]);
        }
    }

    public function delete_period($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            $_SESSION['error_message'] = "Invalid Review Period ID for deletion.";
            $this->redirect('/review_periods');
            return;
        }

        $periodModel = new PerformanceReviewPeriod();
        // Fetch to display name in message, though model handles "in use" error.
        $period_to_delete = $periodModel->getById($id); 
        $name = $period_to_delete ? $period_to_delete['name'] : "ID: {$id}";

        if ($periodModel->delete($id)) {
            $_SESSION['success_message'] = "Review Period '{$name}' deleted successfully.";
        } else {
            $fk_error_message = "Failed to delete review period '{$name}'. It might be in use by performance reviews or already deleted.";
            // Check if a more specific message was printed by the model (not ideal to rely on printf output)
            // For now, we'll use a generic one if delete returns false.
            $_SESSION['error_message'] = $fk_error_message;
        }
        $this->redirect('/review_periods');
    }
}
?>
