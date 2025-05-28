<?php

class Employee {
    // Database connection and table name
    private $conn;
    private $table_name = "employees";

    // Object Properties
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone_number;
    public $job_title;
    public $department_id;
    public $hire_date;
    public $salary;
    public $address;
    public $date_of_birth;
    public $emergency_contact_name;
    public $emergency_contact_phone;
    public $status; // ENUM('active', 'on_leave', 'terminated')
    public $profile_picture_path;
    public $created_at;
    public $updated_at;

    // Constructor with $db as database connection
    public function __construct($db = null) {
        if ($db) {
            $this->conn = $db;
        } else {
            // Fallback to getting instance if no connection is passed
            $this->conn = Database::getInstance()->getConnection();
        }
    }

    // Create Employee
    public function save() {
        // SQL query to insert a record
        // We don't include id, created_at, updated_at as they are auto-managed by MySQL
        $query = "INSERT INTO " . $this->table_name . " SET
                    first_name=:first_name,
                    last_name=:last_name,
                    email=:email,
                    phone_number=:phone_number,
                    job_title=:job_title,
                    department_id=:department_id,
                    hire_date=:hire_date,
                    salary=:salary,
                    address=:address,
                    date_of_birth=:date_of_birth,
                    emergency_contact_name=:emergency_contact_name,
                    emergency_contact_phone=:emergency_contact_phone,
                    status=:status,
                    profile_picture_path=:profile_picture_path";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Sanitize input (though PDO prepared statements handle SQL injection, good practice for other contexts)
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number ?? ''));
        $this->job_title = htmlspecialchars(strip_tags($this->job_title));
        $this->department_id = ($this->department_id === '') ? null : htmlspecialchars(strip_tags($this->department_id));
        $this->hire_date = htmlspecialchars(strip_tags($this->hire_date));
        $this->salary = ($this->salary === '') ? null : htmlspecialchars(strip_tags($this->salary));
        $this->address = htmlspecialchars(strip_tags($this->address ?? ''));
        $this->date_of_birth = ($this->date_of_birth === '') ? null : htmlspecialchars(strip_tags($this->date_of_birth));
        $this->emergency_contact_name = htmlspecialchars(strip_tags($this->emergency_contact_name ?? ''));
        $this->emergency_contact_phone = htmlspecialchars(strip_tags($this->emergency_contact_phone ?? ''));
        $this->status = htmlspecialchars(strip_tags($this->status ?? 'active'));
        $this->profile_picture_path = htmlspecialchars(strip_tags($this->profile_picture_path ?? ''));


        // Bind values
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":job_title", $this->job_title);
        $stmt->bindParam(":department_id", $this->department_id, $this->department_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":hire_date", $this->hire_date);
        $stmt->bindParam(":salary", $this->salary, $this->salary === null ? PDO::PARAM_NULL : PDO::PARAM_STR); // DECIMAL is bound as STR
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":date_of_birth", $this->date_of_birth, $this->date_of_birth === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":emergency_contact_name", $this->emergency_contact_name);
        $stmt->bindParam(":emergency_contact_phone", $this->emergency_contact_phone);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":profile_picture_path", $this->profile_picture_path);

        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId(); // Get the ID of the newly created record
            return true;
        }

        // Print error if something goes wrong - for development
        // In production, you would log this error.
        printf("Error: %s.\n", $stmt->errorInfo()[2]); // $stmt->error (deprecated) or $stmt->errorInfo()
        return false;
    }

    // Read all Employees
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id ASC";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return []; // Return empty array on failure
    }

    // Read single Employee by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind ID parameter
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute query
        if ($stmt->execute()) {
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            return $employee ? $employee : false; // Return employee data or false if not found
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false; // Return false on failure
    }

    // Update Employee
    public function update() {
        // SQL query to update a record
        $query = "UPDATE " . $this->table_name . " SET
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    phone_number = :phone_number,
                    job_title = :job_title,
                    department_id = :department_id,
                    hire_date = :hire_date,
                    salary = :salary,
                    address = :address,
                    date_of_birth = :date_of_birth,
                    emergency_contact_name = :emergency_contact_name,
                    emergency_contact_phone = :emergency_contact_phone,
                    status = :status,
                    profile_picture_path = :profile_picture_path
                  WHERE id = :id";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number ?? ''));
        $this->job_title = htmlspecialchars(strip_tags($this->job_title));
        $this->department_id = ($this->department_id === '' || $this->department_id === null) ? null : htmlspecialchars(strip_tags($this->department_id));
        $this->hire_date = htmlspecialchars(strip_tags($this->hire_date));
        $this->salary = ($this->salary === '' || $this->salary === null) ? null : htmlspecialchars(strip_tags($this->salary));
        $this->address = htmlspecialchars(strip_tags($this->address ?? ''));
        $this->date_of_birth = ($this->date_of_birth === '' || $this->date_of_birth === null) ? null : htmlspecialchars(strip_tags($this->date_of_birth));
        $this->emergency_contact_name = htmlspecialchars(strip_tags($this->emergency_contact_name ?? ''));
        $this->emergency_contact_phone = htmlspecialchars(strip_tags($this->emergency_contact_phone ?? ''));
        $this->status = htmlspecialchars(strip_tags($this->status ?? 'active'));
        $this->profile_picture_path = htmlspecialchars(strip_tags($this->profile_picture_path ?? ''));

        // Bind values
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":job_title", $this->job_title);
        $stmt->bindParam(":department_id", $this->department_id, $this->department_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":hire_date", $this->hire_date);
        $stmt->bindParam(":salary", $this->salary, $this->salary === null ? PDO::PARAM_NULL : PDO::PARAM_STR); // DECIMAL is bound as STR
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":date_of_birth", $this->date_of_birth, $this->date_of_birth === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":emergency_contact_name", $this->emergency_contact_name);
        $stmt->bindParam(":emergency_contact_phone", $this->emergency_contact_phone);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":profile_picture_path", $this->profile_picture_path);

        // Execute query
        if ($stmt->execute()) {
            // Check if any row was affected. rowCount() is not always reliable for UPDATEs with all DB drivers.
            // For simplicity, we'll return true if execute() succeeded.
            // To be more precise, one might check $stmt->rowCount() > 0, but this can be tricky.
            return true;
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Delete Employee by ID
    public function delete($id) {
        // First, get the employee to retrieve profile_picture_path
        $employee_data = $this->getById($id);

        if (!$employee_data) {
            // getById already prints an error or handles not found
            // printf("Error: Employee with ID %d not found for deletion.\n", $id);
            return false; 
        }
        $profile_picture_path = $employee_data['profile_picture_path'];

        // SQL query to delete a record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Sanitize ID (though binding helps prevent SQL injection)
        $id = htmlspecialchars(strip_tags($id));

        // Bind ID parameter
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute query
        if ($stmt->execute()) {
            // If delete was successful, try to remove the profile picture
            if (!empty($profile_picture_path)) {
                $full_image_path = __DIR__ . '/../../public/' . $profile_picture_path;
                if (file_exists($full_image_path)) {
                    if (!unlink($full_image_path)) {
                        // Optional: Log unlink failure e.g., error_log("Failed to delete image: " . $full_image_path);
                        // For now, we consider DB deletion primary success.
                    }
                }
            }
            return true;
        }

        // Print error if something goes wrong
        printf("Error deleting record: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
    
    // You can add other methods like read(), update(), delete() later.
}

?>
