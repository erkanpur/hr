<?php
require_once __DIR__ . '/../core/Database.php';

class ReviewCriteria {
    private $conn;
    private $table_name = "review_criteria";

    public $id;
    public $name;
    public $description;
    public $is_active;
    public $created_at;
    public $updated_at;

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
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, description=:description, is_active=:is_active";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = ($this->description === null) ? null : htmlspecialchars(strip_tags($this->description));
        $this->is_active = isset($this->is_active) ? (bool)$this->is_active : true;

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description, $this->description === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":is_active", $this->is_active, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        printf("Error creating ReviewCriteria: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    private function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, description=:description, is_active=:is_active WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = (int)htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = ($this->description === null) ? null : htmlspecialchars(strip_tags($this->description));
        $this->is_active = isset($this->is_active) ? (bool)$this->is_active : true;

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description, $this->description === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":is_active", $this->is_active, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        printf("Error updating ReviewCriteria: %s.\n", $stmt->errorInfo()[2]);
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
        printf("Error fetching ReviewCriteria: %s.\n", $stmt->errorInfo()[2]);
        return [];
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        }
        printf("Error fetching ReviewCriteria by ID: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        if ($stmt->errorInfo()[1] == 1451) { // FK violation
             printf("Error deleting criteria: It is currently in use in performance reviews.\n");
        } else {
            printf("Error deleting ReviewCriteria: %s.\n", $stmt->errorInfo()[2]);
        }
        return false;
    }
}
?>
