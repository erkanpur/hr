<?php
require_once __DIR__ . '/../core/Database.php';

class JobApplication {
    private $conn;
    private $table_name = "job_applications";

    public $id;
    public $job_posting_id;
    public $candidate_first_name;
    public $candidate_last_name;
    public $candidate_email;
    public $candidate_phone;
    public $resume_path; // Relative path from a defined uploads directory
    public $cover_letter_path; // Relative path
    public $application_date; // DB default
    public $status; // ENUM, default 'received'
    public $notes_by_recruiter;
    public $rejection_reason;
    public $expected_salary;
    public $source;
    public $created_at;
    public $updated_at;

    // Joined properties for display
    public $job_posting_title;

    public function __construct($db = null) {
        $this->conn = $db ?? Database::getInstance()->getConnection();
    }

    // Method for candidate to submit an application
    public function submitApplication() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    job_posting_id=:job_posting_id,
                    candidate_first_name=:candidate_first_name,
                    candidate_last_name=:candidate_last_name,
                    candidate_email=:candidate_email,
                    candidate_phone=:candidate_phone,
                    resume_path=:resume_path,
                    cover_letter_path=:cover_letter_path,
                    status=:status, /* Default is 'received' in DB */
                    expected_salary=:expected_salary,
                    source=:source";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->job_posting_id = (int)htmlspecialchars(strip_tags($this->job_posting_id));
        $this->candidate_first_name = htmlspecialchars(strip_tags($this->candidate_first_name));
        $this->candidate_last_name = htmlspecialchars(strip_tags($this->candidate_last_name));
        $this->candidate_email = htmlspecialchars(strip_tags($this->candidate_email));
        $this->candidate_phone = ($this->candidate_phone === null || $this->candidate_phone === '') ? null : htmlspecialchars(strip_tags($this->candidate_phone));
        $this->resume_path = htmlspecialchars(strip_tags($this->resume_path)); // Path should be clean
        $this->cover_letter_path = ($this->cover_letter_path === null || $this->cover_letter_path === '') ? null : htmlspecialchars(strip_tags($this->cover_letter_path));
        $this->status = $this->status ?? 'received'; // Ensure status is set
        $this->expected_salary = ($this->expected_salary === null || $this->expected_salary === '') ? null : htmlspecialchars(strip_tags($this->expected_salary));
        $this->source = ($this->source === null || $this->source === '') ? null : htmlspecialchars(strip_tags($this->source));


        // Bind
        $stmt->bindParam(":job_posting_id", $this->job_posting_id, PDO::PARAM_INT);
        $stmt->bindParam(":candidate_first_name", $this->candidate_first_name);
        $stmt->bindParam(":candidate_last_name", $this->candidate_last_name);
        $stmt->bindParam(":candidate_email", $this->candidate_email);
        $stmt->bindParam(":candidate_phone", $this->candidate_phone, $this->candidate_phone === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":resume_path", $this->resume_path);
        $stmt->bindParam(":cover_letter_path", $this->cover_letter_path, $this->cover_letter_path === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":expected_salary", $this->expected_salary, $this->expected_salary === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":source", $this->source, $this->source === null ? PDO::PARAM_NULL : PDO::PARAM_STR);


        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        // Check for unique constraint violation (job_posting_id, candidate_email)
        if ($stmt->errorInfo()[1] == 1062) { // MySQL error code for duplicate entry
             printf("Error: This email address has already applied for this job posting.\n");
        } else {
            printf("Error submitting application: %s.\n", $stmt->errorInfo()[2]);
        }
        return false;
    }
    
    public function getApplicationById($id) {
        $query = "SELECT ja.*, jp.title as job_posting_title 
                  FROM " . $this->table_name . " ja
                  JOIN job_postings jp ON ja.job_posting_id = jp.id
                  WHERE ja.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        }
        printf("Error fetching JobApplication by ID: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function getApplicationsForJob($job_posting_id, $status_filter = null, $limit = 100, $offset = 0) {
        $sql_params = [':job_id' => (int)$job_posting_id];
        $query = "SELECT ja.*, jp.title as job_posting_title 
                  FROM " . $this->table_name . " ja
                  JOIN job_postings jp ON ja.job_posting_id = jp.id
                  WHERE ja.job_posting_id = :job_id";
        
        if ($status_filter) {
            $query .= " AND ja.status = :status";
            $sql_params[':status'] = $status_filter;
        }
        
        $query .= " ORDER BY ja.application_date DESC LIMIT :limit OFFSET :offset";
        // These are added to sql_params but then bound separately for type consistency
        // $sql_params[':limit'] = (int)$limit; 
        // $sql_params[':offset'] = (int)$offset;
        
        $stmt = $this->conn->prepare($query);

        // Bind all params
        $stmt->bindParam(':job_id', $sql_params[':job_id'], PDO::PARAM_INT);
        if ($status_filter) {
             $stmt->bindParam(':status', $sql_params[':status']);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT); // Use bindValue for literals
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT); // Use bindValue for literals
        
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching JobApplications for job: %s.\n", $stmt->errorInfo()[2]);
        return [];
    }

    // Method for HR/Admin to update application status and add notes
    public function updateApplicationDetails() {
        $query = "UPDATE " . $this->table_name . " SET
                    status = :status,
                    notes_by_recruiter = :notes_by_recruiter,
                    rejection_reason = :rejection_reason
                    /* Add other fields admin might update, e.g., interview dates */
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = (int)htmlspecialchars(strip_tags($this->id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->notes_by_recruiter = ($this->notes_by_recruiter === null) ? null : htmlspecialchars(strip_tags($this->notes_by_recruiter));
        $this->rejection_reason = ($this->rejection_reason === null) ? null : htmlspecialchars(strip_tags($this->rejection_reason));

        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':notes_by_recruiter', $this->notes_by_recruiter, $this->notes_by_recruiter === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':rejection_reason', $this->rejection_reason, $this->rejection_reason === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        printf("Error updating JobApplication details: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
    
    public function delete($id) {
        // Consider implications: deleting an application might be needed for GDPR, or just archived.
        // This physically deletes the record and associated files should be handled by controller if paths are stored.
        // For now, this model method only deletes the DB record. File deletion for resume/cover letter needs to be handled by controller calling this.
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        printf("Error deleting JobApplication: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
}
?>
