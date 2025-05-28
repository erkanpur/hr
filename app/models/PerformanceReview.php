<?php
require_once __DIR__ . '/../core/Database.php';

class PerformanceReview {
    private $conn;
    private $table_name = "performance_reviews";

    public $id;
    public $employee_id;
    public $review_period_id;
    public $reviewer_id;
    public $review_date;
    public $overall_score;
    public $overall_manager_comments;
    public $employee_self_assessment_comments;
    public $strengths;
    public $areas_for_improvement;
    public $goals_for_next_period;
    public $status; // ENUM('not_started', 'employee_input', 'manager_review', 'pending_acknowledgement', 'completed', 'archived')
    public $employee_acknowledged_at;
    public $created_at;
    public $updated_at;

    // Joined properties (for reads)
    public $employee_name;
    public $reviewer_name;
    public $review_period_name;


    public function __construct($db = null) {
        $this->conn = $db ?? Database::getInstance()->getConnection();
    }

    // Method to initiate a new review (basic version)
    public function createInitialReview() {
        $query = "INSERT INTO " . $this->table_name . 
                 " SET employee_id=:employee_id, review_period_id=:review_period_id, reviewer_id=:reviewer_id, status=:status";
        $stmt = $this->conn->prepare($query);

        $this->employee_id = (int)htmlspecialchars(strip_tags($this->employee_id));
        $this->review_period_id = (int)htmlspecialchars(strip_tags($this->review_period_id));
        $this->reviewer_id = ($this->reviewer_id === null || $this->reviewer_id === '') ? null : (int)htmlspecialchars(strip_tags($this->reviewer_id));
        $this->status = htmlspecialchars(strip_tags($this->status ?? 'not_started'));

        $stmt->bindParam(":employee_id", $this->employee_id, PDO::PARAM_INT);
        $stmt->bindParam(":review_period_id", $this->review_period_id, PDO::PARAM_INT);
        $stmt->bindParam(":reviewer_id", $this->reviewer_id, $this->reviewer_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        printf("Error creating initial PerformanceReview: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
    
    // General update method - can be expanded
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                    reviewer_id=:reviewer_id, 
                    review_date=:review_date, 
                    overall_score=:overall_score, 
                    overall_manager_comments=:overall_manager_comments, 
                    employee_self_assessment_comments=:employee_self_assessment_comments,
                    strengths=:strengths,
                    areas_for_improvement=:areas_for_improvement,
                    goals_for_next_period=:goals_for_next_period,
                    status=:status,
                    employee_acknowledged_at=:employee_acknowledged_at,
                    employee_final_comments=:employee_final_comments
                  WHERE id=:id AND employee_id=:employee_id AND review_period_id=:review_period_id"; 
        // Added employee_id and review_period_id to WHERE for more safety, though ID should be unique.
        
        $stmt = $this->conn->prepare($query);

        // Sanitize all fields that can be updated
        $this->id = (int)htmlspecialchars(strip_tags($this->id));
        $this->employee_id = (int)htmlspecialchars(strip_tags($this->employee_id));
        $this->review_period_id = (int)htmlspecialchars(strip_tags($this->review_period_id));
        $this->reviewer_id = ($this->reviewer_id === null || $this->reviewer_id === '') ? null : (int)htmlspecialchars(strip_tags($this->reviewer_id));
        $this->review_date = ($this->review_date === null || $this->review_date === '') ? null : htmlspecialchars(strip_tags($this->review_date));
        $this->overall_score = ($this->overall_score === null || $this->overall_score === '') ? null : htmlspecialchars(strip_tags($this->overall_score)); // Assuming decimal, bind as string
        $this->overall_manager_comments = ($this->overall_manager_comments === null) ? null : htmlspecialchars(strip_tags($this->overall_manager_comments));
        $this->employee_self_assessment_comments = ($this->employee_self_assessment_comments === null) ? null : htmlspecialchars(strip_tags($this->employee_self_assessment_comments));
        $this->strengths = ($this->strengths === null) ? null : htmlspecialchars(strip_tags($this->strengths));
        $this->areas_for_improvement = ($this->areas_for_improvement === null) ? null : htmlspecialchars(strip_tags($this->areas_for_improvement));
        $this->goals_for_next_period = ($this->goals_for_next_period === null) ? null : htmlspecialchars(strip_tags($this->goals_for_next_period));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->employee_acknowledged_at = ($this->employee_acknowledged_at === null || $this->employee_acknowledged_at === '') ? null : htmlspecialchars(strip_tags($this->employee_acknowledged_at));
        $this->employee_final_comments = ($this->employee_final_comments === null) ? null : htmlspecialchars(strip_tags($this->employee_final_comments)); // Sanitize new field

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":employee_id", $this->employee_id, PDO::PARAM_INT);
        $stmt->bindParam(":review_period_id", $this->review_period_id, PDO::PARAM_INT);
        $stmt->bindParam(":reviewer_id", $this->reviewer_id, $this->reviewer_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":review_date", $this->review_date, $this->review_date === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":overall_score", $this->overall_score, $this->overall_score === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":overall_manager_comments", $this->overall_manager_comments, $this->overall_manager_comments === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":employee_self_assessment_comments", $this->employee_self_assessment_comments, $this->employee_self_assessment_comments === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":strengths", $this->strengths, $this->strengths === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":areas_for_improvement", $this->areas_for_improvement, $this->areas_for_improvement === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":goals_for_next_period", $this->goals_for_next_period, $this->goals_for_next_period === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":employee_acknowledged_at", $this->employee_acknowledged_at, $this->employee_acknowledged_at === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":employee_final_comments", $this->employee_final_comments, $this->employee_final_comments === null ? PDO::PARAM_NULL : PDO::PARAM_STR); // Bind new field
        
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        printf("Error updating PerformanceReview: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function getReviewById($id) {
        $query = "SELECT pr.*, 
                         CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                         CONCAT(r.first_name, ' ', r.last_name) as reviewer_name,
                         p.name as review_period_name
                  FROM " . $this->table_name . " pr
                  JOIN employees e ON pr.employee_id = e.id
                  LEFT JOIN employees r ON pr.reviewer_id = r.id
                  JOIN performance_review_periods p ON pr.review_period_id = p.id
                  WHERE pr.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        }
        printf("Error fetching PerformanceReview by ID: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Add getReviewsByEmployee, getReviewsByReviewer, getReviewsByPeriod etc. as needed
    // Example:

        public function exists($employee_id, $review_period_id) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE employee_id = :employee_id AND review_period_id = :review_period_id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt->bindParam(':review_period_id', $review_period_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() !== false;
        }

        public function createMultipleInitialReviews(array $reviewsData) {
            $success_count = 0;
            $failed_count = 0;
            $skipped_count = 0;

            $this->conn->beginTransaction();

            try {
                foreach ($reviewsData as $reviewData) {
                    if ($this->exists($reviewData['employee_id'], $reviewData['review_period_id'])) {
                        $skipped_count++;
                        continue; // Skip if review already exists
                    }

                    // Use current object instance to set properties and call createInitialReview
                    $this->employee_id = $reviewData['employee_id'];
                    $this->review_period_id = $reviewData['review_period_id'];
                    $this->reviewer_id = $reviewData['reviewer_id'] ?? null; // Ensure reviewer_id is handled
                    $this->status = $reviewData['status'] ?? 'not_started';
                    // Reset id for each new record
                    $this->id = null; 

                    if ($this->createInitialReview()) { // createInitialReview sets $this->id
                        $success_count++;
                    } else {
                        $failed_count++;
                    }
                }
                $this->conn->commit();
            } catch (Exception $e) {
                $this->conn->rollBack();
                // Log this exception $e->getMessage()
                $failed_count = count($reviewsData) - $success_count - $skipped_count; // Assume all non-success/skipped are failed on global error
                error_log("Error in createMultipleInitialReviews transaction: " . $e->getMessage());
            }
            return ['success' => $success_count, 'failed' => $failed_count, 'skipped' => $skipped_count];
        }

    public function getReviewsByEmployee($employee_id, $period_id = null) {
        $query = "SELECT pr.*,                             
                         CONCAT(r.first_name, ' ', r.last_name) as reviewer_name,
                         p.name as review_period_name
                  FROM " . $this->table_name . " pr                      
                  LEFT JOIN employees r ON pr.reviewer_id = r.id
                  JOIN performance_review_periods p ON pr.review_period_id = p.id
                  WHERE pr.employee_id = :employee_id";
        $params = [':employee_id' => (int)$employee_id];
        if ($period_id) {
            $query .= " AND pr.review_period_id = :period_id";
            $params[':period_id'] = (int)$period_id;
        }
        $query .= " ORDER BY p.start_date DESC, pr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute($params)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching reviews by employee: %s.\n", $stmt->errorInfo()[2]);
        return [];
    }

    public function getReviewsForEmployeeByStatus($employee_id, array $statuses) {
        if (empty($statuses)) {
            return [];
        }

        // Create placeholders for IN clause
        $statusPlaceholders = implode(',', array_fill(0, count($statuses), '?'));

        $query = "SELECT pr.*, 
                         p.name as review_period_name,
                         p.start_date as review_period_start_date,
                         p.end_date as review_period_end_date,
                         CONCAT(r.first_name, ' ', r.last_name) as reviewer_name
                  FROM " . $this->table_name . " pr
                  JOIN performance_review_periods p ON pr.review_period_id = p.id
                  LEFT JOIN employees r ON pr.reviewer_id = r.id
                  WHERE pr.employee_id = ? AND pr.status IN (" . $statusPlaceholders . ")
                  ORDER BY p.start_date DESC, pr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters: employee_id first, then all statuses
        $params = array_merge([(int)$employee_id], $statuses);
        
        if ($stmt->execute($params)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching reviews by employee and status: %s.\n", $stmt->errorInfo()[2]);
        return [];
    }

    public function getReviewsForReviewerByStatus($reviewer_id, array $statuses) {
        if (empty($statuses)) {
            return [];
        }

        $statusPlaceholders = implode(',', array_fill(0, count($statuses), '?'));

        $query = "SELECT pr.*, 
                         p.name as review_period_name,
                         p.start_date as review_period_start_date,
                         p.end_date as review_period_end_date,
                         CONCAT(e.first_name, ' ', e.last_name) as employee_name 
                  FROM " . $this->table_name . " pr
                  JOIN performance_review_periods p ON pr.review_period_id = p.id
                  JOIN employees e ON pr.employee_id = e.id
                  WHERE pr.reviewer_id = ? AND pr.status IN (" . $statusPlaceholders . ")
                  ORDER BY p.start_date ASC, e.first_name ASC, e.last_name ASC"; // Or other relevant order for manager
        
        $stmt = $this->conn->prepare($query);
        
        $params = array_merge([(int)$reviewer_id], $statuses);
        
        if ($stmt->execute($params)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching reviews by reviewer and status: %s.\n", $stmt->errorInfo()[2]);
        return [];
    }

    public function getReviewForAcknowledgement($employee_id, $review_id) {
        $query = "SELECT pr.*, 
                         CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                         CONCAT(r.first_name, ' ', r.last_name) as reviewer_name,
                         p.name as review_period_name,
                         p.start_date as review_period_start_date,
                         p.end_date as review_period_end_date
                  FROM " . $this->table_name . " pr
                  JOIN employees e ON pr.employee_id = e.id
                  LEFT JOIN employees r ON pr.reviewer_id = r.id
                  JOIN performance_review_periods p ON pr.review_period_id = p.id
                  WHERE pr.id = :review_id AND pr.employee_id = :employee_id AND pr.status = 'pending_acknowledgement'
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        $review_id = (int)htmlspecialchars(strip_tags($review_id));
        $employee_id = (int)htmlspecialchars(strip_tags($employee_id));

        $stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        }
        printf("Error fetching review for acknowledgement: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
    
    public function delete($id) {
        // Deleting a review will also delete its ratings due to ON DELETE CASCADE on review_ratings table
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        printf("Error deleting PerformanceReview: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
}
?>
