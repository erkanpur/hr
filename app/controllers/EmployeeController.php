<?php

require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../core/Database.php'; // For Employee model if not passed explicitly

class EmployeeController {

    public function index() {
        global $title;
        $title = 'Manage Employees';

        $employeeModel = new Employee(); // Database connection is handled by model's constructor
        $employees = $employeeModel->getAll();

        $this->loadView('employees/index', ['employees' => $employees, 'title' => $title]);
    }

    public function add() {
        global $title; // To be used by layout.php
        $title = 'Add New Employee';

        // Any additional data needed for the view can be prepared here
        // For example, if departments were ready:
        // $departmentModel = new Department();
        // $departments = $departmentModel->getAll();

        // Load the view
        $this->loadView('employees/add');
    }

    public function store() {
        global $title;
        $title = 'Add New Employee'; // In case of validation errors, we re-render the add form
        $errors = [];
        $old_input = $_POST; // Keep user's input for repopulation

        // --- Basic Server-Side Validation ---
        if (empty($_POST['first_name'])) {
            $errors['first_name'] = 'First name is required.';
        }
        if (empty($_POST['last_name'])) {
            $errors['last_name'] = 'Last name is required.';
        }
        if (empty($_POST['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }
        if (empty($_POST['job_title'])) {
            $errors['job_title'] = 'Job title is required.';
        }
        if (empty($_POST['hire_date'])) {
            $errors['hire_date'] = 'Hire date is required.';
        }
        // Add more validation rules as needed (e.g., salary format, date formats)

        // --- File Upload Handling (Basic) ---
        $profile_picture_path = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true); // Create directory if it doesn't exist
            }
            $fileName = uniqid() . '-' . basename($_FILES['profile_picture']['name']);
            $targetFilePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
                $profile_picture_path = 'uploads/profile_pictures/' . $fileName; // Path relative to public directory
            } else {
                $errors['profile_picture'] = 'Failed to upload profile picture.';
            }
        } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] != UPLOAD_ERR_NO_FILE) {
            // Handle other upload errors
            $errors['profile_picture'] = 'Error uploading profile picture. Error code: ' . $_FILES['profile_picture']['error'];
        }


        if (!empty($errors)) {
            // Validation failed, reload the form with errors and old input
            $this->loadView('employees/add', ['errors' => $errors, 'old_input' => $old_input]);
            return;
        }

        // Validation passed, proceed to save the employee
        $db = Database::getInstance()->getConnection();
        $employee = new Employee($db);

        $employee->first_name = $_POST['first_name'];
        $employee->last_name = $_POST['last_name'];
        $employee->email = $_POST['email'];
        $employee->phone_number = $_POST['phone_number'] ?? null;
        $employee->job_title = $_POST['job_title'];
        $employee->department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : null;
        $employee->hire_date = $_POST['hire_date'];
        $employee->salary = !empty($_POST['salary']) ? $_POST['salary'] : null;
        $employee->address = $_POST['address'] ?? null;
        $employee->date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $employee->emergency_contact_name = $_POST['emergency_contact_name'] ?? null;
        $employee->emergency_contact_phone = $_POST['emergency_contact_phone'] ?? null;
        $employee->status = $_POST['status'] ?? 'active';
        $employee->profile_picture_path = $profile_picture_path;

        try {
            if ($employee->save()) {
                // Successfully saved
                // For now, redirect to a generic success message or home.
                // Later, redirect to employee list or employee details page.
                $_SESSION['success_message'] = "Employee added successfully!"; // Requires session_start() in index.php
                
                // Basic redirect (adjust path as needed by your routing in index.php)
                $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
                if ($base_url === '.' || $base_url === '') $base_url = ''; // handle root installs

                header("Location: " . $base_url . "/"); // Redirect to homepage for now
                exit;
            } else {
                $errors['database'] = 'Failed to save employee to the database.';
            }
        } catch (PDOException $e) {
            // Log the detailed error message for the admin/developer
            error_log("Database save error: " . $e->getMessage());
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'email') !== false) {
                 $errors['email'] = 'This email address is already registered.';
            } else {
                 $errors['database'] = 'An unexpected database error occurred. Please try again later.';
            }
        }
        
        // If save failed or an exception occurred
        $this->loadView('employees/add', ['errors' => $errors, 'old_input' => $old_input]);
    }

    public function view($id) {
        global $title;

        // Validate ID
        if (!filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            $_SESSION['error_message'] = "Invalid employee ID specified.";
            $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
            if ($base_url === '.' || $base_url === '') $base_url = '';
            header("Location: " . $base_url . "/employees");
            exit;
        }

        $employeeModel = new Employee();
        $employee = $employeeModel->getById((int)$id);

        if ($employee) {
            $employee_name = htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']);
            $title = "View Employee: " . $employee_name; // For layout.php
            $view_title = "Employee Details: " . $employee_name; // More specific for the view itself if needed

            $this->loadView('employees/view', ['employee' => $employee, 'title' => $view_title]);
        } else {
            $_SESSION['error_message'] = "Employee not found with ID: " . htmlspecialchars($id);
            $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
            if ($base_url === '.' || $base_url === '') $base_url = '';
            header("Location: " . $base_url . "/employees");
            exit;
        }
    }

    public function edit($id) {
        global $title;

        // Validate ID
        if (!filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            $_SESSION['error_message'] = "Invalid employee ID specified for editing.";
            $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
            if ($base_url === '.' || $base_url === '') $base_url = '';
            header("Location: " . $base_url . "/employees");
            exit;
        }

        $employeeModel = new Employee();
        $employee = $employeeModel->getById((int)$id);

        if ($employee) {
            $employee_name = htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']);
            $title = "Edit Employee: " . $employee_name; // For layout.php
            $view_title = "Edit Details for " . $employee_name; // More specific for the view itself

            // Pass the employee data to the 'employees/edit' view
            // The view will be responsible for populating the form fields.
            $this->loadView('employees/edit', ['employee' => $employee, 'title' => $view_title, 'old_input' => $employee]);
        } else {
            $_SESSION['error_message'] = "Employee not found with ID: " . htmlspecialchars($id) . " for editing.";
            $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
            if ($base_url === '.' || $base_url === '') $base_url = '';
            header("Location: " . $base_url . "/employees");
            exit;
        }
    }

    public function update_employee($id) {
        global $title;
        $errors = [];
        $old_input = $_POST; // Repopulate form with submitted data in case of error

        // Validate ID
        if (!filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            $_SESSION['error_message'] = "Invalid employee ID specified for update.";
            $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
            if ($base_url === '.' || $base_url === '') $base_url = '';
            header("Location: " . $base_url . "/employees");
            exit;
        }
        $employee_id = (int)$id;

        // Fetch existing employee data first
        $employeeModel = new Employee();
        $existing_employee_data = $employeeModel->getById($employee_id);

        if (!$existing_employee_data) {
            $_SESSION['error_message'] = "Employee not found for update with ID: " . htmlspecialchars($id);
            $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
            if ($base_url === '.' || $base_url === '') $base_url = '';
            header("Location: " . $base_url . "/employees");
            exit;
        }
        
        $title = "Edit Employee: " . htmlspecialchars($existing_employee_data['first_name'] . ' ' . $existing_employee_data['last_name']);

        // --- Basic Server-Side Validation ---
        if (empty($_POST['first_name'])) {
            $errors['first_name'] = 'First name is required.';
        }
        if (empty($_POST['last_name'])) {
            $errors['last_name'] = 'Last name is required.';
        }
        if (empty($_POST['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }
        if (empty($_POST['job_title'])) {
            $errors['job_title'] = 'Job title is required.';
        }
        if (empty($_POST['hire_date'])) {
            $errors['hire_date'] = 'Hire date is required.';
        }
        // Add more validation rules as needed

        // --- File Upload Handling ---
        $profile_picture_path = $existing_employee_data['profile_picture_path']; // Default to existing
        
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $fileName = uniqid() . '-' . basename($_FILES['profile_picture']['name']);
            $targetFilePath = $uploadDir . $fileName;
            
            // Check file type and size (optional but recommended)
            // $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            // $file_extension = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            // if (!in_array($file_extension, $allowed_types)) { $errors['profile_picture'] = 'Invalid file type.'; }
            // if ($_FILES['profile_picture']['size'] > 2000000) { $errors['profile_picture'] = 'File is too large.'; }

            if (empty($errors['profile_picture']) && move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
                // Delete old picture if it exists and is different
                if (!empty($existing_employee_data['profile_picture_path']) && $existing_employee_data['profile_picture_path'] != ('uploads/profile_pictures/' . $fileName)) {
                    $oldPicFullPath = __DIR__ . '/../../public/' . $existing_employee_data['profile_picture_path'];
                    if (file_exists($oldPicFullPath)) {
                        unlink($oldPicFullPath);
                    }
                }
                $profile_picture_path = 'uploads/profile_pictures/' . $fileName;
            } else {
                $errors['profile_picture'] = $errors['profile_picture'] ?? 'Failed to upload new profile picture.';
            }
        } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] != UPLOAD_ERR_NO_FILE) {
            $errors['profile_picture'] = 'Error uploading profile picture. Error code: ' . $_FILES['profile_picture']['error'];
        }

        if (!empty($errors)) {
            // Validation failed, reload the edit form with errors, old input, and original employee data
            // 'employee' here is the original data for the form structure, 'old_input' is the submitted data
            $this->loadView('employees/edit', ['errors' => $errors, 'old_input' => $old_input, 'employee' => $existing_employee_data, 'title' => $title]);
            return;
        }

        // Validation passed, proceed to update the employee
        $employeeToUpdate = new Employee(); // Using a new instance for clarity
        $employeeToUpdate->id = $employee_id;
        $employeeToUpdate->first_name = $_POST['first_name'];
        $employeeToUpdate->last_name = $_POST['last_name'];
        $employeeToUpdate->email = $_POST['email'];
        $employeeToUpdate->phone_number = $_POST['phone_number'] ?? null;
        $employeeToUpdate->job_title = $_POST['job_title'];
        $employeeToUpdate->department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : null;
        $employeeToUpdate->hire_date = $_POST['hire_date'];
        $employeeToUpdate->salary = !empty($_POST['salary']) ? $_POST['salary'] : null;
        $employeeToUpdate->address = $_POST['address'] ?? null;
        $employeeToUpdate->date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $employeeToUpdate->emergency_contact_name = $_POST['emergency_contact_name'] ?? null;
        $employeeToUpdate->emergency_contact_phone = $_POST['emergency_contact_phone'] ?? null;
        $employeeToUpdate->status = $_POST['status'] ?? 'active';
        $employeeToUpdate->profile_picture_path = $profile_picture_path;

        try {
            if ($employeeToUpdate->update()) {
                $_SESSION['success_message'] = "Employee details updated successfully!";
                $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
                if ($base_url === '.' || $base_url === '') $base_url = '';
                header("Location: " . $base_url . "/employees/view/" . $employee_id);
                exit;
            } else {
                $errors['database'] = 'Failed to update employee in the database. No specific error from model.';
            }
        } catch (PDOException $e) {
            error_log("Database update error: " . $e->getMessage());
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'email') !== false) {
                 $errors['email'] = 'This email address is already registered by another employee.';
            } else {
                 $errors['database'] = 'An unexpected database error occurred during update. Please try again later.';
            }
        }
        
        // If update failed or an exception occurred
        $this->loadView('employees/edit', ['errors' => $errors, 'old_input' => $old_input, 'employee' => $existing_employee_data, 'title' => $title]);
    }

    public function delete_employee($id) {
        // Validate ID
        if (!filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            $_SESSION['error_message'] = "Invalid employee ID for deletion.";
        } else {
            $employee_id = (int)$id;
            $employeeModel = new Employee();

            if ($employeeModel->delete($employee_id)) {
                $_SESSION['success_message'] = "Employee (ID: " . htmlspecialchars($employee_id) . ") deleted successfully.";
            } else {
                // The model's delete method might have already printed an error or failed to find the user.
                // A more specific error might come from checking if the employee exists first,
                // but for now, a general message if delete returns false.
                $_SESSION['error_message'] = "Failed to delete employee (ID: " . htmlspecialchars($employee_id) . "). They may have already been deleted or a database error occurred.";
            }
        }

        // Redirect to employee list
        $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($base_url === '.' || $base_url === '') $base_url = ''; // Handle root installs
        header("Location: " . $base_url . "/employees");
        exit;
    }

    // Helper function to load views
    private function loadView($viewName, $data = []) {
        // Make data available to the view
        extract($data); // $errors, $old_input etc.

        // Construct view file path
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';

        if (file_exists($viewFile)) {
            // The $content variable for layout.php will be captured by output buffering in index.php
            include $viewFile;
        } else {
            // In a real app, you'd have a more robust error page
            echo "Error: View '{$viewName}' not found.";
        }
    }
}
?>
