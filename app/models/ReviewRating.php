<?php
require_once __DIR__ . '/../core/Database.php';

class ReviewRating {
    private $conn;
    private $table_name = "review_ratings";

    public $id;
    public $performance_review_id;
    public $criteria_id;
    public $rating_score_manager;
    public $manager_comments_on_criteria;
    public $rating_score_employee;
    public $employee_self_assessment_comments_on_criteria;
    public $created_at;
    public $updated_at;

    public function __construct($db = null) {
        $this->conn = $db ?? Database::getInstance()->getConnection();
    }

    // Save or update a batch of ratings for a review
    // $ratingsData is an array like: [['criteria_id' => X, 'rating_score_manager' => Y, ...], ...]
    public function saveRatingsForReview($performance_review_id, $ratingsData) {
        $this->conn->beginTransaction();
        try {
            // Optional: Delete existing ratings for this review if replacing all
            // $deleteQuery = "DELETE FROM " . $this->table_name . " WHERE performance_review_id = :pr_id";
            // $delStmt = $this->conn->prepare($deleteQuery);
            // $delStmt->bindParam(':pr_id', $performance_review_id, PDO::PARAM_INT);
            // $delStmt->execute();

            $query = "INSERT INTO " . $this->table_name . 
                     " (performance_review_id, criteria_id, rating_score_manager, manager_comments_on_criteria, rating_score_employee, employee_self_assessment_comments_on_criteria)
                     VALUES (:pr_id, :c_id, :rsm, :m_coc, :rse, :e_sacoc)
                     ON DUPLICATE KEY UPDATE 
                     rating_score_manager = VALUES(rating_score_manager),
                     manager_comments_on_criteria = VALUES(manager_comments_on_criteria),
                     rating_score_employee = VALUES(rating_score_employee),
                     employee_self_assessment_comments_on_criteria = VALUES(employee_self_assessment_comments_on_criteria)";
            
            $stmt = $this->conn->prepare($query);

            foreach ($ratingsData as $rating) {
                $stmt->bindParam(":pr_id", $performance_review_id, PDO::PARAM_INT);
                $stmt->bindParam(":c_id", $rating['criteria_id'], PDO::PARAM_INT);
                
                $rsm = ($rating['rating_score_manager'] === '' || $rating['rating_score_manager'] === null) ? null : (int)$rating['rating_score_manager'];
                $m_coc = empty($rating['manager_comments_on_criteria']) ? null : htmlspecialchars(strip_tags($rating['manager_comments_on_criteria']));
                $rse = ($rating['rating_score_employee'] === '' || $rating['rating_score_employee'] === null) ? null : (int)$rating['rating_score_employee'];
                $e_sacoc = empty($rating['employee_self_assessment_comments_on_criteria']) ? null : htmlspecialchars(strip_tags($rating['employee_self_assessment_comments_on_criteria']));

                $stmt->bindParam(":rsm", $rsm, $rsm === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                $stmt->bindParam(":m_coc", $m_coc, $m_coc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->bindParam(":rse", $rse, $rse === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                $stmt->bindParam(":e_sacoc", $e_sacoc, $e_sacoc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                
                if (!$stmt->execute()) {
                    throw new PDOException("Failed to save one of the ratings.");
                }
            }
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            printf("Error saving ratings: %s.\n", $e->getMessage());
            return false;
        }
    }

    public function getRatingsForReview($performance_review_id) {
        $query = "SELECT rr.*, rc.name as criteria_name, rc.description as criteria_description 
                  FROM " . $this->table_name . " rr
                  JOIN review_criteria rc ON rr.criteria_id = rc.id
                  WHERE rr.performance_review_id = :pr_id
                  ORDER BY rc.name ASC"; // Or by a predefined order for criteria
        
        $stmt = $this->conn->prepare($query);
        $pr_id = (int)htmlspecialchars(strip_tags($performance_review_id));
        $stmt->bindParam(':pr_id', $pr_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching ratings for review: %s.\n", $stmt->errorInfo()[2]);
        return [];
    }
}
?>
