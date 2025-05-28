<?php
require_once __DIR__ . '/../models/PerformanceReview.php';
require_once __DIR__ . '/../models/PerformanceReviewPeriod.php';
require_once __DIR__ . '/../models/Employee.php'; // Needed to get all employees
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/ReviewCriteria.php'; // Add this
require_once __DIR__ . '/../models/ReviewRating.php';   // Add this

class PerformanceReviewController {

    private function loadView($viewName, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "Error: View '{$viewName}' not found."; 
        }
    }

    private function redirect($url_path) {
        $base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($base_url === '.' || $base_url === '') $base_url = '';
        header("Location: " . $base_url . $url_path);
        exit;
    }

    // Placeholder for logged-in user ID
    private function getCurrentUserId() { 
        // Assume admin/manager for these actions for now
        return $_SESSION['user']['employee_id'] ?? 1; // Default to 1 if no session for testing
    }

    public function initiate_form() {
        global $title;
        $title = 'Initiate Performance Reviews';

        $periodModel = new PerformanceReviewPeriod();
        // Get periods that are in 'setup' or 'open_for_input' status, eligible for initiating reviews
        $available_periods = $periodModel->getAll('setup'); 
        $open_periods = $periodModel->getAll('open_for_input');
        $periods = array_merge($available_periods, $open_periods);
        
        // In a more complex system, you might list employees or departments here
        // For now, we'll initiate for ALL employees.

        $this->loadView('performance_reviews/initiate_form', [
            'title' => $title,
            'periods' => $periods
        ]);
    }

    public function process_initiation() {
        global $title;
        $title = 'Initiate Performance Reviews'; // For re-display or errors
        $errors = [];
        $success_count = 0;
        $failed_count = 0;
        $skipped_count = 0;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/performance_reviews/initiate_form'); // Or some other appropriate path
            return;
        }

        $review_period_id = filter_input(INPUT_POST, 'review_period_id', FILTER_VALIDATE_INT);
        $initiator_id = $this->getCurrentUserId(); // This user is initiating

        if (!$review_period_id) {
            $errors['review_period_id'] = "Please select a valid review period.";
        }

        if (empty($errors)) {
            $employeeModel = new Employee();
            $all_employees = $employeeModel->getAll(); // Assuming this method exists and gets all employees

            $reviewModel = new PerformanceReview();
            $reviewsData = [];

            foreach ($all_employees as $employee) {
                // Basic check: Does a review for this employee in this period already exist?
                // More robust check needed in model or by unique constraints if possible
                // For now, let's assume createInitialReview handles duplicates or we add a check.
                // A proper check would be: $reviewModel->exists($employee['id'], $review_period_id)
                
                // For now, reviewer_id is the initiator. This needs to be employee's actual manager.
                // This will be addressed when manager_id is added to employees table.
                // If manager_id is null for an employee, reviewer_id could be null or a default.
                $reviewer_id_for_employee = $employee['manager_id'] ?? $initiator_id; // Use actual manager_id if available, else initiator.

                $reviewsData[] = [
                    'employee_id' => $employee['id'],
                    'review_period_id' => $review_period_id,
                    'reviewer_id' => $reviewer_id_for_employee, 
                    'status' => 'not_started' // Or 'employee_input' to make it immediately available
                ];
            }
            
            // The createMultipleInitialReviews method will be defined in the next sub-task for the model
            if (!empty($reviewsData)) {
                 // Assume createMultipleInitialReviews returns an array ['success' => count, 'failed' => count, 'skipped' => count]
                $result = $reviewModel->createMultipleInitialReviews($reviewsData);
                $success_count = $result['success'];
                $failed_count = $result['failed'];
                $skipped_count = $result['skipped'];

                if ($success_count > 0) {
                    $_SESSION['success_message'] = "{$success_count} performance reviews initiated successfully.";
                    if($failed_count > 0) $_SESSION['warning_message'] = "{$failed_count} reviews failed to initiate.";
                    if($skipped_count > 0) $_SESSION['info_message'] = "{$skipped_count} reviews were skipped (possibly already exist).";
                    $this->redirect('/review_periods'); // Redirect to review periods list or a success page
                    return;
                } else {
                    $errors['database'] = "No reviews were initiated. {$failed_count} failed, {$skipped_count} skipped.";
                }
            } else {
                $errors['no_employees'] = "No employees found to initiate reviews for.";
            }

        }

        // If errors, re-display form
        $periodModel = new PerformanceReviewPeriod();
        $periods = $periodModel->getAll(); // Get all for form re-population
        $this->loadView('performance_reviews/initiate_form', [
            'title' => $title,
            'periods' => $periods,
            'errors' => $errors,
            'old_input' => $_POST
        ]);
    }

    public function my_pending_reviews() {
        global $title;
        $title = 'My Pending Performance Reviews';

        $employee_id = $this->getCurrentUserId(); // Uses existing placeholder
        if (!$employee_id) { // Assuming getCurrentUserId might return 0 or null if no user
            $_SESSION['error_message'] = "User not identified. Please log in.";
            $this->redirect('/'); // Or login page
            return;
        }

        $reviewModel = new PerformanceReview();
        // Fetch reviews that are 'not_started' or 'employee_input'
        $pending_statuses = ['not_started', 'employee_input'];
        $reviews = $reviewModel->getReviewsForEmployeeByStatus($employee_id, $pending_statuses);

        $this->loadView('performance_reviews/my_pending_reviews', [
            'title' => $title,
            'reviews' => $reviews
        ]);
    }

    public function self_assessment_form($review_id) {
        global $title;

        $employee_id = $this->getCurrentUserId();
        if (!$employee_id) {
            $_SESSION['error_message'] = "User not identified. Please log in.";
            $this->redirect('/'); return;
        }

        $review_id = filter_var($review_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$review_id) {
            $_SESSION['error_message'] = "Invalid review ID.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }

        $reviewModel = new PerformanceReview();
        $review = $reviewModel->getReviewById($review_id);

        // Validate review exists, belongs to current employee, and is in correct status
        if (!$review) {
            $_SESSION['error_message'] = "Performance review not found.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }
        if ($review['employee_id'] != $employee_id) {
            $_SESSION['error_message'] = "You are not authorized to access this review.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }
        $allowed_statuses = ['not_started', 'employee_input'];
        if (!in_array($review['status'], $allowed_statuses)) {
            $_SESSION['error_message'] = "This review is not currently open for self-assessment (Status: " . $review['status'] . ").";
            // Optionally redirect to a 'view only' version if one exists, or back to list.
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }
        
        $title = 'Self-Assessment: ' . htmlspecialchars($review['review_period_name']);

        $criteriaModel = new ReviewCriteria();
        $active_criteria = $criteriaModel->getAll(true); // Get all active criteria

        $ratingModel = new ReviewRating();
        $existing_ratings_raw = $ratingModel->getRatingsForReview($review_id);
        $existing_ratings = []; // Re-key by criteria_id for easier access in view
        foreach ($existing_ratings_raw as $rating) {
            $existing_ratings[$rating['criteria_id']] = $rating;
        }

        $this->loadView('performance_reviews/self_assessment_form', [
            'title' => $title,
            'review' => $review,
            'criteria_list' => $active_criteria,
            'existing_ratings' => $existing_ratings,
            'old_input' => $review // For overall comments, potentially ratings if structure matches
        ]);
    }

    public function save_self_assessment($review_id) {
        global $title; // For re-displaying form on error

        $employee_id = $this->getCurrentUserId();
        if (!$employee_id) {
            $_SESSION['error_message'] = "User not identified. Please log in.";
            $this->redirect('/'); return;
        }

        $review_id = filter_var($review_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$review_id) {
            $_SESSION['error_message'] = "Invalid review ID for saving assessment.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }

        $reviewModel = new PerformanceReview();
        $review = $reviewModel->getReviewById($review_id);

        // Validate review exists, belongs to employee, and is in correct status
        if (!$review) {
            $_SESSION['error_message'] = "Performance review not found for saving.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }
        if ($review['employee_id'] != $employee_id) {
            $_SESSION['error_message'] = "You are not authorized to save this review.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }
        $allowed_statuses = ['not_started', 'employee_input'];
         if (!in_array($review['status'], $allowed_statuses)) {
            $_SESSION['error_message'] = "This review is not currently open for self-assessment changes (Status: " . $review['status'] . ").";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Invalid request method.";
            $this->redirect('/performance_reviews/self_assessment_form/' . $review_id); return;
        }

        $errors = [];
        $old_input = $_POST; // For repopulating form
        $ratingsData = [];

        // Process ratings for each criterion
        if (isset($_POST['ratings']) && is_array($_POST['ratings'])) {
            foreach ($_POST['ratings'] as $criteria_id => $rating_values) {
                // Basic validation: ensure score is integer if provided, comments are string
                $score = $rating_values['score_employee'] ?? null;
                if ($score !== null && $score !== '' && !filter_var($score, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]])) { // Assuming 1-5 rating scale
                    $errors['rating_crit_' . $criteria_id] = "Invalid rating score for criteria ID {$criteria_id}. Must be between 1-5.";
                }
                $ratingsData[] = [
                    'criteria_id' => (int)$criteria_id,
                    'rating_score_employee' => ($score === '' || $score === null) ? null : (int)$score,
                    'employee_self_assessment_comments_on_criteria' => $rating_values['comments_employee'] ?? null,
                    // Manager fields are not set here
                    'rating_score_manager' => null, 
                    'manager_comments_on_criteria' => null
                ];
            }
        }
        
        // Overall self-assessment comments
        $overall_employee_comments = $_POST['overall_employee_comments'] ?? null;
        // Basic validation for overall comments (e.g., max length if any) could be added here.

        if (!empty($errors)) {
            $title = 'Self-Assessment: ' . htmlspecialchars($review['review_period_name']);
            $criteriaModel = new ReviewCriteria();
            $active_criteria = $criteriaModel->getAll(true);
            $ratingModel = new ReviewRating(); // Fetch existing again for consistency if some were saved before error
            $existing_ratings_raw = $ratingModel->getRatingsForReview($review_id);
            $existing_ratings = [];
            foreach ($existing_ratings_raw as $rating) { $existing_ratings[$rating['criteria_id']] = $rating; }

            $this->loadView('performance_reviews/self_assessment_form', [
                'title' => $title, 'review' => $review, 'criteria_list' => $active_criteria,
                'existing_ratings' => $existing_ratings, 'errors' => $errors, 'old_input' => $old_input
            ]);
            return;
        }

        // All seems okay, proceed to save
        $ratingModel = new ReviewRating();
        $this->conn = Database::getInstance()->getConnection(); // For transaction
        
        try {
            $this->conn->beginTransaction();

            if (!empty($ratingsData)) {
                if (!$ratingModel->saveRatingsForReview($review_id, $ratingsData)) {
                    throw new Exception("Failed to save detailed ratings.");
                }
            }
            
            // Update the main review record: overall comments and status
            $reviewToUpdate = new PerformanceReview($this->conn); // Pass connection for transaction
            $reviewToUpdate->id = $review_id;
            $reviewToUpdate->employee_id = $employee_id; // For where clause in update
            $reviewToUpdate->review_period_id = $review['review_period_id']; // For where clause
            $reviewToUpdate->employee_self_assessment_comments = $overall_employee_comments;
            $reviewToUpdate->status = 'manager_review'; // Transition to next status

            // Only update fields that are relevant for this step
            // The existing update() method in PerformanceReview might be too broad,
            // consider a specific method or pass only relevant fields to a flexible update method.
            // For now, we assume update() can handle partial updates or we set all required fields.
            // Let's make sure we set all fields the current update() expects or make update() more flexible.
            // We need to fill the other properties of $reviewToUpdate from $review object.
            $reviewToUpdate->reviewer_id = $review['reviewer_id'];
            $reviewToUpdate->review_date = $review['review_date']; // Not set yet
            $reviewToUpdate->overall_score = $review['overall_score']; // Not set yet
            $reviewToUpdate->overall_manager_comments = $review['overall_manager_comments']; // Not set yet
            $reviewToUpdate->strengths = $review['strengths'];
            $reviewToUpdate->areas_for_improvement = $review['areas_for_improvement'];
            $reviewToUpdate->goals_for_next_period = $review['goals_for_next_period'];
            $reviewToUpdate->employee_acknowledged_at = $review['employee_acknowledged_at'];


            if (!$reviewToUpdate->update()) {
                 throw new Exception("Failed to update main review status or comments.");
            }

            $this->conn->commit();
            $_SESSION['success_message'] = "Self-assessment saved successfully. It is now pending manager review.";
            $this->redirect('/performance_reviews/my_pending_reviews');

        } catch (Exception $e) {
            if ($this->conn && $this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            $_SESSION['error_message'] = "Error saving self-assessment: " . $e->getMessage();
            // Log detailed error $e for admin
            error_log("Error in save_self_assessment for review_id {$review_id}: " . $e->getMessage());
            $this->redirect('/performance_reviews/self_assessment_form/' . $review_id);
        }
    }

    public function team_reviews_pending_manager() {
        global $title;
        $title = 'Team Reviews - Pending Manager Input';

        $manager_id = $this->getCurrentUserId(); // Assumes this is the manager's employee_id
        if (!$manager_id) {
            $_SESSION['error_message'] = "User not identified as manager. Please log in.";
            $this->redirect('/'); // Or login page
            return;
        }

        $reviewModel = new PerformanceReview();
        $pending_manager_statuses = ['manager_review']; // Status indicating it's manager's turn
        $reviews = $reviewModel->getReviewsForReviewerByStatus($manager_id, $pending_manager_statuses);

        $this->loadView('performance_reviews/team_reviews_pending_manager', [
            'title' => $title,
            'reviews' => $reviews
        ]);
    }

    public function manager_review_form($review_id) {
        global $title;

        $manager_id = $this->getCurrentUserId(); // Assumed to be manager's employee_id
        if (!$manager_id) {
            $_SESSION['error_message'] = "User not identified. Please log in.";
            $this->redirect('/'); return;
        }

        $review_id = filter_var($review_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$review_id) {
            $_SESSION['error_message'] = "Invalid review ID.";
            $this->redirect('/performance_reviews/team_reviews_pending_manager'); return;
        }

        $reviewModel = new PerformanceReview();
        $review = $reviewModel->getReviewById($review_id); // Fetches review with employee_name, reviewer_name etc.

        // Validate review exists, is assigned to this manager, and is in correct status
        if (!$review) {
            $_SESSION['error_message'] = "Performance review not found.";
            $this->redirect('/performance_reviews/team_reviews_pending_manager'); return;
        }
        if ($review['reviewer_id'] != $manager_id) {
            $_SESSION['error_message'] = "You are not authorized to review this assessment.";
            $this->redirect('/performance_reviews/team_reviews_pending_manager'); return;
        }
        if ($review['status'] !== 'manager_review') {
            $_SESSION['error_message'] = "This review is not currently open for manager assessment (Status: " . $review['status'] . ").";
            $this->redirect('/performance_reviews/team_reviews_pending_manager'); return;
        }
        
        $title = 'Manager Review: ' . htmlspecialchars($review['employee_name']) . ' - ' . htmlspecialchars($review['review_period_name']);

        $criteriaModel = new ReviewCriteria();
        $active_criteria = $criteriaModel->getAll(true);

        $ratingModel = new ReviewRating();
        $existing_ratings_raw = $ratingModel->getRatingsForReview($review_id);
        $existing_ratings = []; // Re-key by criteria_id
        foreach ($existing_ratings_raw as $rating) {
            $existing_ratings[$rating['criteria_id']] = $rating;
        }

        $this->loadView('performance_reviews/manager_review_form', [
            'title' => $title,
            'review' => $review,
            'criteria_list' => $active_criteria,
            'existing_ratings' => $existing_ratings,
            'old_input' => $review // For overall manager comments, ratings, etc.
        ]);
    }

    public function save_manager_review($review_id) {
        global $title; // For re-displaying form on error

        $manager_id = $this->getCurrentUserId();
        if (!$manager_id) {
            $_SESSION['error_message'] = "User not identified. Please log in.";
            $this->redirect('/'); return;
        }

        $review_id = filter_var($review_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$review_id) {
            $_SESSION['error_message'] = "Invalid review ID for saving manager review.";
            $this->redirect('/performance_reviews/team_reviews_pending_manager'); return;
        }

        $reviewModel = new PerformanceReview();
        $review = $reviewModel->getReviewById($review_id); // Fetch original review

        // Validate review exists, assigned to manager, and is in correct status
        if (!$review) {
            $_SESSION['error_message'] = "Performance review not found for saving.";
            $this->redirect('/performance_reviews/team_reviews_pending_manager'); return;
        }
        if ($review['reviewer_id'] != $manager_id) {
            $_SESSION['error_message'] = "You are not authorized to save this manager review.";
            $this->redirect('/performance_reviews/team_reviews_pending_manager'); return;
        }
        if ($review['status'] !== 'manager_review') {
             $_SESSION['error_message'] = "This review is not currently open for manager assessment changes (Status: " . $review['status'] . ").";
            $this->redirect('/performance_reviews/team_reviews_pending_manager'); return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Invalid request method.";
            $this->redirect('/performance_reviews/manager_review_form/' . $review_id); return;
        }

        $errors = [];
        $old_input = $_POST; 
        $ratingsData = [];

        // Process manager ratings for each criterion
        if (isset($_POST['ratings']) && is_array($_POST['ratings'])) {
            foreach ($_POST['ratings'] as $criteria_id => $rating_values) {
                $score_manager = $rating_values['score_manager'] ?? null;
                if ($score_manager !== null && $score_manager !== '' && !filter_var($score_manager, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]])) {
                    $errors['rating_crit_mgr_' . $criteria_id] = "Invalid manager rating for criteria ID {$criteria_id}. Must be 1-5.";
                }
                // Preserve employee's ratings/comments when preparing data for saveRatingsForReview
                // Fetch existing employee rating for this criterion to pass it along if model needs full set.
                $existing_employee_rating = null;
                $existing_employee_comment = null;
                
                // We need to fetch existing ratings once, not in a loop for each criterion.
                // This part should be optimized by fetching all existing ratings for the review once, 
                // then looking up the specific criterion's employee data.
                // For now, let's assume $review['ratings_by_criteria_id'] exists or fetch once outside loop.
                // Corrected logic: Use $existing_ratings (passed to view, or refetch if not available)
                $ratingModelTemp = new ReviewRating(); 
                $all_existing_ratings_for_review = $ratingModelTemp->getRatingsForReview($review_id);
                $temp_existing_ratings = [];
                foreach($all_existing_ratings_for_review as $ex_rat) {
                    $temp_existing_ratings[$ex_rat['criteria_id']] = $ex_rat;
                }

                if (isset($temp_existing_ratings[(int)$criteria_id])) {
                    $existing_employee_rating = $temp_existing_ratings[(int)$criteria_id]['rating_score_employee'];
                    $existing_employee_comment = $temp_existing_ratings[(int)$criteria_id]['employee_self_assessment_comments_on_criteria'];
                }


                $ratingsData[] = [
                    'criteria_id' => (int)$criteria_id,
                    'rating_score_manager' => ($score_manager === '' || $score_manager === null) ? null : (int)$score_manager,
                    'manager_comments_on_criteria' => $rating_values['comments_manager'] ?? null,
                    'rating_score_employee' => $existing_employee_rating, 
                    'employee_self_assessment_comments_on_criteria' => $existing_employee_comment
                ];
            }
        }
        
        // Overall manager feedback
        $overall_manager_comments = $_POST['overall_manager_comments'] ?? null;
        $strengths = $_POST['strengths'] ?? null;
        $areas_for_improvement = $_POST['areas_for_improvement'] ?? null;
        $goals_for_next_period = $_POST['goals_for_next_period'] ?? null;
        $overall_score = (isset($_POST['overall_score']) && $_POST['overall_score'] !== '') ? $_POST['overall_score'] : null;

        if ($overall_score !== null && !is_numeric($overall_score)) { 
            $errors['overall_score'] = 'Overall score must be a number if provided.';
        }


        if (!empty($errors)) {
            $title = 'Manager Review: ' . htmlspecialchars($review['employee_name']) . ' - ' . htmlspecialchars($review['review_period_name']);
            $criteriaModel = new ReviewCriteria();
            $active_criteria = $criteriaModel->getAll(true);
            $ratingModel = new ReviewRating();
            $existing_ratings_raw = $ratingModel->getRatingsForReview($review_id); // Re-fetch for view
            $existing_ratings = [];
            foreach ($existing_ratings_raw as $rating) { $existing_ratings[$rating['criteria_id']] = $rating; }

            $this->loadView('performance_reviews/manager_review_form', [
                'title' => $title, 'review' => $review, 'criteria_list' => $active_criteria,
                'existing_ratings' => $existing_ratings, 'errors' => $errors, 'old_input' => $old_input
            ]);
            return;
        }

        $ratingModel = new ReviewRating(); // Already instantiated, just ensure connection if needed
        $this->conn = Database::getInstance()->getConnection(); // Ensure controller has connection property for transaction
        
        try {
            $this->conn->beginTransaction();

            if (!empty($ratingsData)) {
                if (!$ratingModel->saveRatingsForReview($review_id, $ratingsData)) {
                    throw new Exception("Failed to save manager's detailed ratings.");
                }
            }
            
            $reviewToUpdate = new PerformanceReview($this->conn);
            $reviewToUpdate->id = $review_id;
            $reviewToUpdate->employee_id = $review['employee_id']; 
            $reviewToUpdate->review_period_id = $review['review_period_id']; 
            
            $reviewToUpdate->overall_manager_comments = $overall_manager_comments;
            $reviewToUpdate->strengths = $strengths;
            $reviewToUpdate->areas_for_improvement = $areas_for_improvement;
            $reviewToUpdate->goals_for_next_period = $goals_for_next_period;
            $reviewToUpdate->overall_score = $overall_score;
            $reviewToUpdate->status = 'pending_acknowledgement'; // Changed from 'completed'
            $reviewToUpdate->review_date = date('Y-m-d'); 

            $reviewToUpdate->reviewer_id = $review['reviewer_id']; 
            $reviewToUpdate->employee_self_assessment_comments = $review['employee_self_assessment_comments'];
            $reviewToUpdate->employee_acknowledged_at = $review['employee_acknowledged_at'];


            if (!$reviewToUpdate->update()) { 
                 throw new Exception("Failed to update main review with manager's feedback.");
            }

            $this->conn->commit();
            $_SESSION['success_message'] = "Manager review for " . htmlspecialchars($review['employee_name']) . " saved successfully.";
            $this->redirect('/performance_reviews/team_reviews_pending_manager');

        } catch (Exception $e) {
            if ($this->conn && $this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            $_SESSION['error_message'] = "Error saving manager review: " . $e->getMessage();
            error_log("Error in save_manager_review for review_id {$review_id}: " . $e->getMessage());
            // To repopulate form correctly, we need to pass all necessary data again
             $title_on_error = 'Manager Review: ' . htmlspecialchars($review['employee_name']) . ' - ' . htmlspecialchars($review['review_period_name']);
             $criteriaModel_on_error = new ReviewCriteria();
             $active_criteria_on_error = $criteriaModel_on_error->getAll(true);
             $ratingModel_on_error = new ReviewRating();
             $existing_ratings_raw_on_error = $ratingModel_on_error->getRatingsForReview($review_id);
             $existing_ratings_on_error = [];
             foreach ($existing_ratings_raw_on_error as $rating_on_error) { $existing_ratings_on_error[$rating_on_error['criteria_id']] = $rating_on_error; }
             $errors['exception'] = $e->getMessage(); // Add exception message to errors

             $this->loadView('performance_reviews/manager_review_form', [
                'title' => $title_on_error, 'review' => $review, 'criteria_list' => $active_criteria_on_error,
                'existing_ratings' => $existing_ratings_on_error, 'errors' => $errors, 'old_input' => $old_input
            ]);
        }
    }

    public function view_for_acknowledgement($review_id) {
        global $title;

        $employee_id = $this->getCurrentUserId();
        if (!$employee_id) {
            $_SESSION['error_message'] = "User not identified. Please log in.";
            $this->redirect('/'); return;
        }

        $review_id = filter_var($review_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$review_id) {
            $_SESSION['error_message'] = "Invalid review ID for acknowledgement.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return; // Or a generic dashboard
        }

        $reviewModel = new PerformanceReview();
        $review = $reviewModel->getReviewForAcknowledgement($employee_id, $review_id);

        if (!$review) {
            $_SESSION['error_message'] = "Performance review not found, not available for acknowledgement, or you are not authorized.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }
        
        $title = 'Acknowledge Performance Review: ' . htmlspecialchars($review['review_period_name']);

        // Fetch criteria and all ratings (employee + manager) to display
        $criteriaModel = new ReviewCriteria();
        $active_criteria = $criteriaModel->getAll(true);

        $ratingModel = new ReviewRating();
        $all_ratings_raw = $ratingModel->getRatingsForReview($review_id);
        $all_ratings = []; // Re-key by criteria_id
        foreach ($all_ratings_raw as $rating) {
            $all_ratings[$rating['criteria_id']] = $rating;
        }

        $this->loadView('performance_reviews/acknowledge_review_form', [
            'title' => $title,
            'review' => $review,
            'criteria_list' => $active_criteria,
            'all_ratings' => $all_ratings, // Contains both employee and manager ratings/comments
            'old_input' => $review // For potential employee_final_comments repopulation
        ]);
    }

    public function acknowledge_review_submit($review_id) {
        global $title; // For potential re-display of form on error

        $employee_id = $this->getCurrentUserId();
        if (!$employee_id) {
            $_SESSION['error_message'] = "User not identified. Please log in.";
            $this->redirect('/'); return;
        }

        $review_id = filter_var($review_id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$review_id) {
            $_SESSION['error_message'] = "Invalid review ID for acknowledgement.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }

        $reviewModel = new PerformanceReview();
        $review = $reviewModel->getReviewById($review_id); 

        if (!$review) {
            $_SESSION['error_message'] = "Performance review not found for acknowledgement.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }
        if ($review['employee_id'] != $employee_id) {
            $_SESSION['error_message'] = "You are not authorized to acknowledge this review.";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }
        if ($review['status'] !== 'pending_acknowledgement') {
            $_SESSION['error_message'] = "This review is not currently awaiting your acknowledgement (Status: " . htmlspecialchars($review['status']) . ").";
            $this->redirect('/performance_reviews/my_pending_reviews'); return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = "Invalid request method for acknowledgement.";
            $this->redirect('/performance_reviews/view_for_acknowledgement/' . $review_id); return;
        }

        if (!isset($_POST['acknowledged'])) {
            $_SESSION['error_message'] = "You must tick the acknowledgement checkbox to finalize the review.";
            
            $title = 'Acknowledge Performance Review: ' . htmlspecialchars($review['review_period_name']);
            $criteriaModel = new ReviewCriteria();
            $active_criteria = $criteriaModel->getAll(true);
            $ratingModel = new ReviewRating();
            $all_ratings_raw = $ratingModel->getRatingsForReview($review_id);
            $all_ratings = []; 
            foreach ($all_ratings_raw as $rating) { $all_ratings[$rating['criteria_id']] = $rating; }
            
            $this->loadView('performance_reviews/acknowledge_review_form', [
                'title' => $title, 'review' => $review, 'criteria_list' => $active_criteria,
                'all_ratings' => $all_ratings, 'old_input' => $_POST,
                'errors' => ['acknowledged' => 'You must tick the acknowledgement checkbox.']
            ]);
            return;
        }
        
        $employee_final_comments = $_POST['employee_final_comments'] ?? null;

        $reviewToUpdate = new PerformanceReview();
        $reviewToUpdate->id = $review_id;
        $reviewToUpdate->employee_id = $employee_id; 
        $reviewToUpdate->review_period_id = $review['review_period_id']; 
        
        $reviewToUpdate->employee_final_comments = $employee_final_comments; 
        $reviewToUpdate->status = 'completed';
        $reviewToUpdate->employee_acknowledged_at = date('Y-m-d H:i:s'); 

        // Carry over other fields
        $reviewToUpdate->reviewer_id = $review['reviewer_id'];
        $reviewToUpdate->review_date = $review['review_date'];
        $reviewToUpdate->overall_score = $review['overall_score'];
        $reviewToUpdate->overall_manager_comments = $review['overall_manager_comments'];
        $reviewToUpdate->employee_self_assessment_comments = $review['employee_self_assessment_comments'];
        $reviewToUpdate->strengths = $review['strengths'];
        $reviewToUpdate->areas_for_improvement = $review['areas_for_improvement'];
        $reviewToUpdate->goals_for_next_period = $review['goals_for_next_period'];
        
        if ($reviewToUpdate->update()) { 
            $_SESSION['success_message'] = "Performance review acknowledged and finalized successfully.";
            $this->redirect('/performance_reviews/my_pending_reviews'); 
        } else {
            $_SESSION['error_message'] = "Failed to finalize review. Please try again.";
            $this->redirect('/performance_reviews/view_for_acknowledgement/' . $review_id);
        }
    }
}
?>
