<?php
require_once __DIR__ . '/../core/Database.php';

class JobPosting {
    private $conn;
    private $table_name = "job_postings";

    public $id;
    public $title;
    public $description;
    public $department_name;
    public $location;
    public $employment_type; // ENUM('full_time', 'part_time', 'contract', 'internship')
    public $salary_range;
    public $status; // ENUM('draft', 'open', 'closed', 'archived')
    public $posted_date;
    public $closing_date;
    public $created_by_employee_id;
    public $created_at;
    public $updated_at;

    // For display purposes
    public $created_by_employee_name; 

    public function __construct($db = null) {
        $this->conn = $db ?? Database::getInstance()->getConnection();
    }

    public function save() { // Handles both create and update
        if (!empty($this->id)) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    private function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    title=:title, description=:description, department_name=:department_name, location=:location,
                    employment_type=:employment_type, salary_range=:salary_range, status=:status,
                    posted_date=:posted_date, closing_date=:closing_date, created_by_employee_id=:created_by_employee_id";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description)); // Basic sanitize, consider allowing some HTML later with a proper library
        $this->department_name = ($this->department_name === null || $this->department_name === '') ? null : htmlspecialchars(strip_tags($this->department_name));
        $this->location = ($this->location === null || $this->location === '') ? null : htmlspecialchars(strip_tags($this->location));
        $this->employment_type = ($this->employment_type === null || $this->employment_type === '') ? 'full_time' : htmlspecialchars(strip_tags($this->employment_type));
        $this->salary_range = ($this->salary_range === null || $this->salary_range === '') ? null : htmlspecialchars(strip_tags($this->salary_range));
        $this->status = ($this->status === null || $this->status === '') ? 'draft' : htmlspecialchars(strip_tags($this->status));
        $this->posted_date = ($this->posted_date === '' || $this->posted_date === null) ? null : htmlspecialchars(strip_tags($this->posted_date));
        $this->closing_date = ($this->closing_date === '' || $this->closing_date === null) ? null : htmlspecialchars(strip_tags($this->closing_date));
        $this->created_by_employee_id = ($this->created_by_employee_id === '' || $this->created_by_employee_id === null) ? null : (int)htmlspecialchars(strip_tags($this->created_by_employee_id));

        // Bind
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":department_name", $this->department_name, $this->department_name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":location", $this->location, $this->location === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":employment_type", $this->employment_type);
        $stmt->bindParam(":salary_range", $this->salary_range, $this->salary_range === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":posted_date", $this->posted_date, $this->posted_date === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":closing_date", $this->closing_date, $this->closing_date === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":created_by_employee_id", $this->created_by_employee_id, $this->created_by_employee_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        printf("Error creating JobPosting: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    private function update() {
        $query = "UPDATE " . $this->table_name . " SET
                    title=:title, description=:description, department_name=:department_name, location=:location,
                    employment_type=:employment_type, salary_range=:salary_range, status=:status,
                    posted_date=:posted_date, closing_date=:closing_date, created_by_employee_id=:created_by_employee_id
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->id = (int)htmlspecialchars(strip_tags($this->id));
        // (Sanitize other properties as in create())
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->department_name = ($this->department_name === null || $this->department_name === '') ? null : htmlspecialchars(strip_tags($this->department_name));
        $this->location = ($this->location === null || $this->location === '') ? null : htmlspecialchars(strip_tags($this->location));
        $this->employment_type = ($this->employment_type === null || $this->employment_type === '') ? 'full_time' : htmlspecialchars(strip_tags($this->employment_type));
        $this->salary_range = ($this->salary_range === null || $this->salary_range === '') ? null : htmlspecialchars(strip_tags($this->salary_range));
        $this->status = ($this->status === null || $this->status === '') ? 'draft' : htmlspecialchars(strip_tags($this->status));
        $this->posted_date = ($this->posted_date === '' || $this->posted_date === null) ? null : htmlspecialchars(strip_tags($this->posted_date));
        $this->closing_date = ($this->closing_date === '' || $this->closing_date === null) ? null : htmlspecialchars(strip_tags($this->closing_date));
        $this->created_by_employee_id = ($this->created_by_employee_id === '' || $this->created_by_employee_id === null) ? null : (int)htmlspecialchars(strip_tags($this->created_by_employee_id));


        // Bind
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        // (Bind other params as in create())
        $stmt->bindParam(":department_name", $this->department_name, $this->department_name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":location", $this->location, $this->location === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":employment_type", $this->employment_type);
        $stmt->bindParam(":salary_range", $this->salary_range, $this->salary_range === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":posted_date", $this->posted_date, $this->posted_date === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":closing_date", $this->closing_date, $this->closing_date === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":created_by_employee_id", $this->created_by_employee_id, $this->created_by_employee_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);


        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // True if any row was affected
        }
        printf("Error updating JobPosting: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function getById($id) {
        $query = "SELECT jp.*, CONCAT(e.first_name, ' ', e.last_name) as created_by_employee_name 
                  FROM " . $this->table_name . " jp
                  LEFT JOIN employees e ON jp.created_by_employee_id = e.id
                  WHERE jp.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        }
        printf("Error fetching JobPosting by ID: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function getAll($status = null, $limit = 1000, $offset = 0) {
        $sql_params = [];
        $query = "SELECT jp.*, CONCAT(e.first_name, ' ', e.last_name) as created_by_employee_name 
                  FROM " . $this->table_name . " jp
                  LEFT JOIN employees e ON jp.created_by_employee_id = e.id";
        
        if ($status) {
            $query .= " WHERE jp.status = :status";
            $sql_params[':status'] = $status;
        }
        
        $query .= " ORDER BY jp.posted_date DESC, jp.created_at DESC LIMIT :limit OFFSET :offset";
        // $sql_params[':limit'] = (int)$limit; // PDO requires int params to be bound as int
        // $sql_params[':offset'] = (int)$offset;
        
        $stmt = $this->conn->prepare($query);

        // Bind status if it's set
        if ($status) {
             $stmt->bindParam(':status', $sql_params[':status']);
        }
        // Bind limit and offset directly as integers.
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        if ($stmt->execute()) { 
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching JobPostings: %s.\n", $stmt->errorInfo()[2]);
        return [];
    }
    
    public function delete($id) {
        // Relies on ON DELETE CASCADE for job_applications if that's the desired behavior for related applications.
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        // Check for FK constraint if ON DELETE RESTRICT was used for job_applications
        // if ($stmt->errorInfo()[1] == 1451) { ... } 
        printf("Error deleting JobPosting: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
}
?>
