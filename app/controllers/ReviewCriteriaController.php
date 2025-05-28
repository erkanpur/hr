<?php

require_once __DIR__ . '/../models/ReviewCriteria.php';
require_once __DIR__ . '/../core/Database.php'; // For model if not passed explicitly

class ReviewCriteriaController {

    private function loadView($viewName, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            include $viewFile; // Content captured by ob_start() in index.php
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
        $title = 'Manage Review Criteria';
        
        $criteriaModel = new ReviewCriteria();
        // Get all criteria, including inactive ones for the admin view
        $all_criteria = $criteriaModel->getAll(false); 

        $this->loadView('review_criteria/index', ['all_criteria' => $all_criteria, 'title' => $title]);
    }

    public function add() {
        global $title;
        $title = 'Add New Review Criterion';
        $this->loadView('review_criteria/add', ['title' => $title, 'old_input' => []]);
    }

    public function store() {
        global $title;
        $title = 'Add New Review Criterion'; 
        $errors = [];
        $old_input = $_POST;

        if (empty($_POST['name'])) {
            $errors['name'] = 'Criterion name is required.';
        }
        // Add more validation as needed

        if (!empty($errors)) {
            $this->loadView('review_criteria/add', ['errors' => $errors, 'old_input' => $old_input, 'title' => $title]);
            return;
        }

        $criterion = new ReviewCriteria();
        $criterion->name = $_POST['name'];
        $criterion->description = $_POST['description'] ?? null;
        $criterion->is_active = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : false;

        try {
            if ($criterion->save()) {
                $_SESSION['success_message'] = "Review criterion '{$criterion->name}' added successfully!";
                $this->redirect('/review_criteria'); // Assuming base path for criteria index
            } else {
                $errors['database'] = 'Failed to save review criterion.';
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'name') !== false) {
                 $errors['name'] = 'This criterion name already exists.';
            } else {
                 $errors['database'] = 'An unexpected database error occurred: ' . $e->getMessage();
            }
        }
        
        $this->loadView('review_criteria/add', ['errors' => $errors, 'old_input' => $old_input, 'title' => $title]);
    }

    public function edit($id) {
        global $title;
        
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            $_SESSION['error_message'] = "Invalid Review Criterion ID specified.";
            $this->redirect('/review_criteria');
            return;
        }

        $criteriaModel = new ReviewCriteria();
        $criterion = $criteriaModel->getById($id);

        if (!$criterion) {
            $_SESSION['error_message'] = "Review Criterion not found with ID: {$id}.";
            $this->redirect('/review_criteria');
            return;
        }
        
        $title = 'Edit Review Criterion: ' . htmlspecialchars($criterion['name']);
        $this->loadView('review_criteria/edit', ['criterion' => $criterion, 'old_input' => $criterion, 'title' => $title]);
    }

    public function update_criteria($id) { // Renamed from 'update'
        global $title;
        $errors = [];
        $old_input = $_POST; 
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if (!$id) {
            $_SESSION['error_message'] = "Invalid Review Criterion ID for update.";
            $this->redirect('/review_criteria');
            return;
        }
        
        $criteriaModel = new ReviewCriteria();
        $existing_criterion = $criteriaModel->getById($id);
        if (!$existing_criterion) {
            $_SESSION['error_message'] = "Review Criterion not found for update.";
            $this->redirect('/review_criteria');
            return;
        }
        $title = 'Edit Review Criterion: ' . htmlspecialchars($existing_criterion['name']);

        if (empty($_POST['name'])) {
            $errors['name'] = 'Criterion name is required.';
        }

        if (!empty($errors)) {
            $this->loadView('review_criteria/edit', ['errors' => $errors, 'old_input' => $old_input, 'criterion' => $existing_criterion, 'title' => $title]);
            return;
        }

        $criterionToUpdate = new ReviewCriteria();
        $criterionToUpdate->id = $id;
        $criterionToUpdate->name = $_POST['name'];
        $criterionToUpdate->description = $_POST['description'] ?? null;
        $criterionToUpdate->is_active = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : false;

        try {
            if ($criterionToUpdate->save()) { // save() handles update due to ID being set
                $_SESSION['success_message'] = "Review criterion '{$criterionToUpdate->name}' updated successfully!";
                $this->redirect('/review_criteria');
            } else {
                $errors['database'] = 'Failed to update criterion. No changes or error.';
                $this->loadView('review_criteria/edit', ['errors' => $errors, 'old_input' => $old_input, 'criterion' => $existing_criterion, 'title' => $title]);
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'name') !== false) {
                 $errors['name'] = 'This criterion name already exists.';
            } else {
                 $errors['database'] = 'An unexpected database error occurred: ' . $e->getMessage();
            }
            $this->loadView('review_criteria/edit', ['errors' => $errors, 'old_input' => $old_input, 'criterion' => $existing_criterion, 'title' => $title]);
        }
    }

    public function delete_criteria($id) { // Renamed from 'delete'
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            $_SESSION['error_message'] = "Invalid Review Criterion ID for deletion.";
            $this->redirect('/review_criteria');
            return;
        }

        $criteriaModel = new ReviewCriteria();
        if ($criteriaModel->delete($id)) {
            $_SESSION['success_message'] = "Review Criterion (ID: {$id}) deleted successfully.";
        } else {
            // Model's delete method handles specific FK violation message
            $_SESSION['error_message'] = $_SESSION['error_message'] ?? "Failed to delete criterion (ID: {$id}). It might be in use or already deleted.";
        }
        $this->redirect('/review_criteria');
    }
}
?>
