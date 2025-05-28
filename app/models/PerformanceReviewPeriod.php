<?php
require_once __DIR__ . '/../core/Database.php';

class PerformanceReviewPeriod {
    private $conn;
    private $table_name = "performance_review_periods";

    public $id;
    public $name;
    public $start_date;
    public $end_date;
    public $status; // ENUM('setup', 'open_for_input', 'in_review', 'closed', 'archived')
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
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, start_date=:start_date, end_date=:end_date, status=:status";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->status = htmlspecialchars(strip_tags($this->status ?? 'setup'));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        printf("Error creating PerformanceReviewPeriod: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    private function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, start_date=:start_date, end_date=:end_date, status=:status WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = (int)htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        printf("Error updating PerformanceReviewPeriod: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function getAll($status_filter = null) {
        $query = "SELECT * FROM " . $this->table_name;
        $params = [];
        if ($status_filter) {
            $query .= " WHERE status = :status";
            $params[':status'] = $status_filter;
        }
        $query .= " ORDER BY start_date DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute($params)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching PerformanceReviewPeriods: %s.\n", $stmt->errorInfo()[2]);
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
        printf("Error fetching PerformanceReviewPeriod by ID: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
    
    public function delete($id) {
        // Add check for related performance_reviews if strict deletion is needed (rely on DB constraint for now)
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        if ($stmt->errorInfo()[1] == 1451) { // FK violation
             printf("Error deleting period: It is currently in use by performance reviews.\n");
        } else {
            printf("Error deleting PerformanceReviewPeriod: %s.\n", $stmt->errorInfo()[2]);
        }
        return false;
    }
}
?>
