<?php

require_once __DIR__ . '/../core/Database.php';

class LeaveType {
    private $conn;
    private $table_name = "leave_types";

    // Object Properties
    public $id;
    public $name;
    public $description;
    public $days_allocated_annually;
    public $is_paid;
    public $is_active;
    public $created_at;
    public $updated_at;

    public function __construct($db = null) {
        if ($db) {
            $this->conn = $db;
        } else {
            $this->conn = Database::getInstance()->getConnection();
        }
    }

    // Create or Update (Save) Leave Type
    public function save() {
        if (!empty($this->id)) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    private function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    name=:name,
                    description=:description,
                    days_allocated_annually=:days_allocated_annually,
                    is_paid=:is_paid,
                    is_active=:is_active";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = ($this->description === null) ? null : htmlspecialchars(strip_tags($this->description));
        $this->days_allocated_annually = ($this->days_allocated_annually === '' || $this->days_allocated_annually === null) ? null : (int)htmlspecialchars(strip_tags($this->days_allocated_annually));
        $this->is_paid = isset($this->is_paid) ? (bool)$this->is_paid : true; // Default to true if not set
        $this->is_active = isset($this->is_active) ? (bool)$this->is_active : true; // Default to true if not set

        // Bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description, $this->description === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":days_allocated_annually", $this->days_allocated_annually, $this->days_allocated_annually === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":is_paid", $this->is_paid, PDO::PARAM_BOOL);
        $stmt->bindParam(":is_active", $this->is_active, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        printf("Error creating record: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    private function update() {
        $query = "UPDATE " . $this->table_name . " SET
                    name = :name,
                    description = :description,
                    days_allocated_annually = :days_allocated_annually,
                    is_paid = :is_paid,
                    is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->id = (int)htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = ($this->description === null) ? null : htmlspecialchars(strip_tags($this->description));
        $this->days_allocated_annually = ($this->days_allocated_annually === '' || $this->days_allocated_annually === null) ? null : (int)htmlspecialchars(strip_tags($this->days_allocated_annually));
        $this->is_paid = isset($this->is_paid) ? (bool)$this->is_paid : true;
        $this->is_active = isset($this->is_active) ? (bool)$this->is_active : true;

        // Bind data
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description, $this->description === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":days_allocated_annually", $this->days_allocated_annually, $this->days_allocated_annually === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":is_paid", $this->is_paid, PDO::PARAM_BOOL);
        $stmt->bindParam(":is_active", $this->is_active, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            return true; // Check rowCount if you want to ensure something changed: $stmt->rowCount() > 0
        }
        printf("Error updating record: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function getAll($only_active = false) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($only_active) {
            $query .= " WHERE is_active = TRUE";
        }
        $query .= " ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching records: %s.\n", $stmt->errorInfo()[2]);
        return []; // Return empty array on failure
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);

        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $leaveType = $stmt->fetch(PDO::FETCH_ASSOC);
            return $leaveType ? $leaveType : false; // Return data or false if not found
        }
        printf("Error fetching record by ID: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function delete($id) {
        // Before deleting, check if this leave type is used in any leave_requests
        // This is to enforce ON DELETE RESTRICT behavior at application level if needed,
        // or if the DB doesn't support it / you want custom message.
        // For now, we rely on DB's ON DELETE RESTRICT.

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // Return true if a row was deleted
        }
        // Check for foreign key constraint violation
        if ($stmt->errorInfo()[1] == 1451) { // MySQL error code for foreign key constraint
             printf("Error deleting record: This leave type is currently in use and cannot be deleted.\n");
        } else {
            printf("Error deleting record: %s.\n", $stmt->errorInfo()[2]);
        }
        return false;
    }
}
?>
