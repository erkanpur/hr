<?php

require_once __DIR__ . '/../models/JobPosting.php';
require_once __DIR__ . '/../core/Database.php'; // For model if not passed explicitly

class JobPostingController {

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

    // Placeholder for logged-in user ID (assumed HR/Admin)
    private function getCurrentUserId() { 
        return $_SESSION['user']['employee_id'] ?? 1; // Default to 1 if no session for testing
    }
    
    // Define valid statuses for job postings for use in forms/validation
    private $valid_statuses = ['draft', 'open', 'closed', 'archived'];
    private $valid_employment_types = ['full_time', 'part_time', 'contract', 'internship'];


    public function index() {
        global $title;
        $title = 'Manage Job Postings';
        
        $postingModel = new JobPosting();
        $status_filter = $_GET['status'] ?? null;
        if ($status_filter === 'all' || !in_array($status_filter, $this->valid_statuses)) {
            $status_filter = null;
        }
        
        $postings = $postingModel->getAll($status_filter); 

        $this->loadView('job_postings/index', [
            'postings' => $postings, 
            'title' => $title,
            'current_filter' => $status_filter ?? 'all',
            'all_statuses' => $this->valid_statuses
        ]);
    }

    public function add() {
        global $title;
        $title = 'Add New Job Posting';
        $this->loadView('job_postings/add', [
            'title' => $title, 
            'old_input' => [],
            'statuses' => $this->valid_statuses,
            'employment_types' => $this->valid_employment_types
        ]);
    }

    public function store() {
        global $title;
        $title = 'Add New Job Posting'; 
        $errors = [];
        $old_input = $_POST;

        if (empty($_POST['title'])) {
            $errors['title'] = 'Job title is required.';
        }
        if (empty($_POST['description'])) {
            $errors['description'] = 'Job description is required.';
        }
        if (!empty($_POST['closing_date']) && !empty($_POST['posted_date']) && new DateTime($_POST['closing_date']) < new DateTime($_POST['posted_date'])) {
            $errors['closing_date'] = 'Closing date cannot be before posted date.';
        }
        if (empty($_POST['status']) || !in_array($_POST['status'], $this->valid_statuses)) {
            $errors['status'] = 'Invalid status selected.';
        }
         if (empty($_POST['employment_type']) || !in_array($_POST['employment_type'], $this->valid_employment_types)) {
            $errors['employment_type'] = 'Invalid employment type selected.';
        }


        if (!empty($errors)) {
            $this->loadView('job_postings/add', ['errors' => $errors, 'old_input' => $old_input, 'title' => $title, 'statuses' => $this->valid_statuses, 'employment_types' => $this->valid_employment_types]);
            return;
        }

        $posting = new JobPosting();
        $posting->title = $_POST['title'];
        $posting->description = $_POST['description']; // Consider using a proper HTML sanitizer if allowing HTML
        $posting->department_name = $_POST['department_name'] ?? null;
        $posting->location = $_POST['location'] ?? null;
        $posting->employment_type = $_POST['employment_type'];
        $posting->salary_range = $_POST['salary_range'] ?? null;
        $posting->status = $_POST['status'];
        $posting->posted_date = !empty($_POST['posted_date']) ? $_POST['posted_date'] : null;
        $posting->closing_date = !empty($_POST['closing_date']) ? $_POST['closing_date'] : null;
        $posting->created_by_employee_id = $this->getCurrentUserId();


        try {
            if ($posting->save()) {
                $_SESSION['success_message'] = "Job posting '{$posting->title}' added successfully!";
                $this->redirect('/job_postings');
            } else {
                $errors['database'] = 'Failed to save job posting.';
            }
        } catch (PDOException $e) {
             $errors['database'] = 'Database error: ' . $e->getMessage();
        }
        
        $this->loadView('job_postings/add', ['errors' => $errors, 'old_input' => $old_input, 'title' => $title, 'statuses' => $this->valid_statuses, 'employment_types' => $this->valid_employment_types]);
    }

    public function edit($id) {
        global $title;
        
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            $_SESSION['error_message'] = "Invalid Job Posting ID.";
            $this->redirect('/job_postings');
            return;
        }

        $postingModel = new JobPosting();
        $posting = $postingModel->getById($id);

        if (!$posting) {
            $_SESSION['error_message'] = "Job Posting not found with ID: {$id}.";
            $this->redirect('/job_postings');
            return;
        }
        
        $title = 'Edit Job Posting: ' . htmlspecialchars($posting['title']);
        $this->loadView('job_postings/edit', [
            'posting' => $posting, 
            'old_input' => $posting, // For pre-filling
            'title' => $title,
            'statuses' => $this->valid_statuses,
            'employment_types' => $this->valid_employment_types
        ]);
    }

    public function update_posting($id) {
        global $title;
        $errors = [];
        $old_input = $_POST; 
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if (!$id) {
            $_SESSION['error_message'] = "Invalid Job Posting ID for update.";
            $this->redirect('/job_postings');
            return;
        }
        
        $postingModel = new JobPosting();
        $existing_posting = $postingModel->getById($id); // To set title and ensure it exists
        if (!$existing_posting) {
            $_SESSION['error_message'] = "Job Posting not found for update.";
            $this->redirect('/job_postings');
            return;
        }
        $title = 'Edit Job Posting: ' . htmlspecialchars($existing_posting['title']);

        // Validation (similar to store)
        if (empty($_POST['title'])) $errors['title'] = 'Job title is required.';
        if (empty($_POST['description'])) $errors['description'] = 'Job description is required.';
        if (!empty($_POST['closing_date']) && !empty($_POST['posted_date']) && new DateTime($_POST['closing_date']) < new DateTime($_POST['posted_date'])) {
            $errors['closing_date'] = 'Closing date cannot be before posted date.';
        }
        if (empty($_POST['status']) || !in_array($_POST['status'], $this->valid_statuses)) $errors['status'] = 'Invalid status.';
        if (empty($_POST['employment_type']) || !in_array($_POST['employment_type'], $this->valid_employment_types)) $errors['employment_type'] = 'Invalid employment type.';


        if (!empty($errors)) {
            $this->loadView('job_postings/edit', ['errors' => $errors, 'old_input' => $old_input, 'posting' => $existing_posting, 'title' => $title, 'statuses' => $this->valid_statuses, 'employment_types' => $this->valid_employment_types]);
            return;
        }

        $postingToUpdate = new JobPosting();
        $postingToUpdate->id = $id;
        $postingToUpdate->title = $_POST['title'];
        $postingToUpdate->description = $_POST['description'];
        $postingToUpdate->department_name = $_POST['department_name'] ?? null;
        $postingToUpdate->location = $_POST['location'] ?? null;
        $postingToUpdate->employment_type = $_POST['employment_type'];
        $postingToUpdate->salary_range = $_POST['salary_range'] ?? null;
        $postingToUpdate->status = $_POST['status'];
        $postingToUpdate->posted_date = !empty($_POST['posted_date']) ? $_POST['posted_date'] : null;
        $postingToUpdate->closing_date = !empty($_POST['closing_date']) ? $_POST['closing_date'] : null;
        $postingToUpdate->created_by_employee_id = $existing_posting['created_by_employee_id']; // Keep original creator

        try {
            if ($postingToUpdate->save()) { 
                $_SESSION['success_message'] = "Job posting '{$postingToUpdate->title}' updated successfully!";
                $this->redirect('/job_postings');
            } else {
                $errors['database'] = 'Failed to update job posting. No changes made or error occurred.';
                $this->loadView('job_postings/edit', ['errors' => $errors, 'old_input' => $old_input, 'posting' => $existing_posting, 'title' => $title, 'statuses' => $this->valid_statuses, 'employment_types' => $this->valid_employment_types]);
            }
        } catch (PDOException $e) {
             $errors['database'] = 'Database error: ' . $e->getMessage();
             $this->loadView('job_postings/edit', ['errors' => $errors, 'old_input' => $old_input, 'posting' => $existing_posting, 'title' => $title, 'statuses' => $this->valid_statuses, 'employment_types' => $this->valid_employment_types]);
        }
    }

    public function delete_posting($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            $_SESSION['error_message'] = "Invalid Job Posting ID for deletion.";
            $this->redirect('/job_postings');
            return;
        }

        $postingModel = new JobPosting();
        $posting_to_delete = $postingModel->getById($id);
        $name = $posting_to_delete ? $posting_to_delete['title'] : "ID: {$id}";

        if ($postingModel->delete($id)) {
            $_SESSION['success_message'] = "Job Posting '{$name}' deleted successfully.";
        } else {
            // ON DELETE CASCADE for job_applications means related applications will also be deleted.
            // If there were other constraints (e.g., RESTRICT), the model's delete might fail and print an error.
            $_SESSION['error_message'] = "Failed to delete job posting '{$name}'. It might have already been deleted or an error occurred.";
        }
        $this->redirect('/job_postings');
    }
    
    // Optional: view method for a single job posting (could show details and list of applicants)
    public function view($id) {
        global $title;
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            $_SESSION['error_message'] = "Invalid Job Posting ID.";
            $this->redirect('/job_postings');
            return;
        }

        $postingModel = new JobPosting();
        $posting = $postingModel->getById($id);

        if (!$posting) {
            $_SESSION['error_message'] = "Job Posting not found with ID: {$id}.";
            $this->redirect('/job_postings');
            return;
        }
        
        $title = 'View Job Posting: ' . htmlspecialchars($posting['title']);
        
        // Future: Fetch applications for this job posting
        // $applicationModel = new JobApplication();
        // $applications = $applicationModel->getApplicationsForJob($id);

        $this->loadView('job_postings/view', [
            'posting' => $posting, 
            'title' => $title
            // 'applications' => $applications // Pass to view later
        ]);
    }
}
?>
