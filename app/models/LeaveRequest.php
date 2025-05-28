<?php

require_once __DIR__ . '/../core/Database.php';

class LeaveRequest {
    private $conn;
    private $table_name = "leave_requests";

    // Object Properties
    public $id;
    public $employee_id;
    public $leave_type_id;
    public $start_date;
    public $end_date;
    public $days_requested; // Will be calculated
    public $reason;
    public $status; // ENUM('pending', 'approved', 'rejected', 'cancelled')
    public $comments_by_approver;
    public $requested_at; // Set by DB on insert for new requests
    public $action_taken_by_user_id;
    public $action_taken_at;
    public $created_at; // Set by DB
    public $updated_at; // Set by DB on update

    public function __construct($db = null) {
        if ($db) {
            $this->conn = $db;
        } else {
            $this->conn = Database::getInstance()->getConnection();
        }
    }

    // Calculate days_requested (simple version: includes start and end date)
    // In a real app, this should exclude weekends and public holidays based on company policy.
    private function calculateDays() {
        if ($this->start_date && $this->end_date) {
            try {
                $startDate = new DateTime($this->start_date);
                $endDate = new DateTime($this->end_date);
                
                if ($startDate > $endDate) {
                    return 0; // Or throw an error, handled by validation in controller
                }
                
                // Calculate the difference in days and add 1 to include the start date
                $interval = $startDate->diff($endDate);
                $days = $interval->days + 1;
                
                // Basic half-day logic (example: if reason contains "half day") - this is very rudimentary
                // A more robust solution would involve separate start_day_is_half, end_day_is_half flags or similar
                if (stripos($this->reason ?? '', "half day") !== false || stripos($this->reason ?? '', "half-day") !== false) {
                     if ($days == 1) return 0.5;
                     // More complex half-day logic for multi-day periods is non-trivial
                }
                return (float)$days;

            } catch (Exception $e) {
                // Log error or handle - for now, return 0 if date format is wrong
                error_log("Error calculating days: " . $e->getMessage());
                return 0;
            }
        }
        return 0;
    }

    // Create Leave Request
    public function save() {
        // For new records, ID would not be set.
        // Update functionality can be added later if needed for employees to change their requests (if status is pending).
        // For now, save is only for creating new requests.

        $query = "INSERT INTO " . $this->table_name . " SET
                    employee_id=:employee_id,
                    leave_type_id=:leave_type_id,
                    start_date=:start_date,
                    end_date=:end_date,
                    days_requested=:days_requested,
                    reason=:reason,
                    status=:status, 
                    comments_by_approver=:comments_by_approver,
                    action_taken_by_user_id=:action_taken_by_user_id,
                    action_taken_at=:action_taken_at";
                    // requested_at, created_at, updated_at have DB defaults or are set by DB on update.

        $stmt = $this->conn->prepare($query);

        // Sanitize and prepare data
        $this->employee_id = (int)htmlspecialchars(strip_tags($this->employee_id));
        $this->leave_type_id = (int)htmlspecialchars(strip_tags($this->leave_type_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        
        // Calculate days_requested
        $this->days_requested = $this->calculateDays();
        if ($this->days_requested <= 0 && !(stripos($this->reason ?? '', "half day") !== false || stripos($this->reason ?? '', "half-day") !== false && $this->days_requested == 0.5) ) { // Allow 0.5 for half day even if calc is 0 for same day
            // This check should ideally be in controller validation, but good to have a fallback.
            // For same-day half-day, calculateDays might return 1, then adjusted to 0.5.
            // If calculateDays returns 0 (e.g. end_date < start_date), it's an issue.
            // For now, we proceed, controller validation is primary.
        }

        $this->reason = ($this->reason === null) ? null : htmlspecialchars(strip_tags($this->reason));
        $this->status = htmlspecialchars(strip_tags($this->status ?? 'pending')); // Default to 'pending'

        // Nullable fields for approver details (set when action is taken)
        $this->comments_by_approver = ($this->comments_by_approver === null) ? null : htmlspecialchars(strip_tags($this->comments_by_approver));
        $this->action_taken_by_user_id = ($this->action_taken_by_user_id === null) ? null : (int)htmlspecialchars(strip_tags($this->action_taken_by_user_id));
        $this->action_taken_at = ($this->action_taken_at === null) ? null : htmlspecialchars(strip_tags($this->action_taken_at));


        // Bind data
        $stmt->bindParam(":employee_id", $this->employee_id, PDO::PARAM_INT);
        $stmt->bindParam(":leave_type_id", $this->leave_type_id, PDO::PARAM_INT);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":days_requested", $this->days_requested); // PDO should handle float/decimal
        $stmt->bindParam(":reason", $this->reason, $this->reason === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":status", $this->status);
        
        $stmt->bindParam(":comments_by_approver", $this->comments_by_approver, $this->comments_by_approver === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":action_taken_by_user_id", $this->action_taken_by_user_id, $this->action_taken_by_user_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":action_taken_at", $this->action_taken_at, $this->action_taken_at === null ? PDO::PARAM_NULL : PDO::PARAM_STR);


        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            // Populate requested_at, created_at from DB if needed by fetching the record again, or rely on application logic.
            // For now, we assume these are handled by DB defaults and not immediately needed back in the object after save.
            return true;
        }
        printf("Error creating leave request: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function getById($id) {
        $query = "SELECT lr.*, lt.name as leave_type_name 
                  FROM " . $this->table_name . " lr
                  JOIN leave_types lt ON lr.leave_type_id = lt.id
                  WHERE lr.id = :id 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);

        $id = (int)htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            return $request ? $request : false;
        }
        printf("Error fetching leave request by ID: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    public function getAllByEmployee($employee_id) {
        $query = "SELECT lr.*, lt.name as leave_type_name 
                  FROM " . $this->table_name . " lr
                  JOIN leave_types lt ON lr.leave_type_id = lt.id
                  WHERE lr.employee_id = :employee_id
                  ORDER BY lr.requested_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        $employee_id = (int)htmlspecialchars(strip_tags($employee_id));
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching leave requests by employee: %s.\n", $stmt->errorInfo()[2]);
        return []; // Return empty array on failure
    }

    // Get all leave requests (for admin/manager view)
    public function getAllRequests($status_filter = null) {
        $query = "SELECT lr.*, lt.name as leave_type_name, e.first_name as employee_first_name, e.last_name as employee_last_name 
                  FROM " . $this->table_name . " lr
                  JOIN leave_types lt ON lr.leave_type_id = lt.id
                  JOIN employees e ON lr.employee_id = e.id";

        $allowed_statuses = ['pending', 'approved', 'rejected', 'cancelled'];
        if ($status_filter !== null && in_array(strtolower($status_filter), $allowed_statuses)) {
            $query .= " WHERE lr.status = :status";
        }
        
        $query .= " ORDER BY lr.requested_at ASC"; // Oldest first

        $stmt = $this->conn->prepare($query);

        if ($status_filter !== null && in_array(strtolower($status_filter), $allowed_statuses)) {
            $status_filter_clean = htmlspecialchars(strip_tags(strtolower($status_filter)));
            $stmt->bindParam(':status', $status_filter_clean);
        }

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        printf("Error fetching all leave requests: %s.\n", $stmt->errorInfo()[2]);
        return [];
    }

    // Update status of a leave request
    public function updateStatus($request_id, $new_status, $approver_employee_id, $comments_by_approver = null) {
        $allowed_statuses = ['pending', 'approved', 'rejected', 'cancelled'];
        $new_status_clean = strtolower(htmlspecialchars(strip_tags($new_status)));

        if (!in_array($new_status_clean, $allowed_statuses)) {
            printf("Error: Invalid status value '%s' provided for update.\n", htmlspecialchars($new_status));
            return false;
        }

        $query = "UPDATE " . $this->table_name . " SET 
                    status = :new_status, 
                    action_taken_by_user_id = :approver_id, 
                    action_taken_at = CURRENT_TIMESTAMP, 
                    comments_by_approver = :comments 
                  WHERE id = :request_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $request_id_clean = (int)htmlspecialchars(strip_tags($request_id));
        $approver_employee_id_clean = ($approver_employee_id === null) ? null : (int)htmlspecialchars(strip_tags($approver_employee_id));
        $comments_by_approver_clean = ($comments_by_approver === null) ? null : htmlspecialchars(strip_tags($comments_by_approver));

        // Bind parameters
        $stmt->bindParam(':new_status', $new_status_clean);
        $stmt->bindParam(':approver_id', $approver_employee_id_clean, $approver_employee_id_clean === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':comments', $comments_by_approver_clean, $comments_by_approver_clean === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':request_id', $request_id_clean, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0; // True if at least one row was affected
        }
        
        printf("Error updating leave request status: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
    
    // Future methods: etc.
}
?>
