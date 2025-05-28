<?php
// Loaded by PerformanceReviewController@self_assessment_form
// $title, $review (main review data), $criteria_list (array of active criteria), 
// $existing_ratings (array of ratings, keyed by criteria_id), $errors (optional), $old_input (optional) are passed.

// Helper to get old value for overall comments or rating comments
function getSelfAssessmentVal($field_key, $criteria_id = null, $old_input, $review_data, $existing_ratings_data) {
    if ($criteria_id !== null) { // For specific criterion comment/score
        $post_key_score = "ratings[{$criteria_id}][score_employee]";
        $post_key_comment = "ratings[{$criteria_id}][comments_employee]";
        if ($field_key === 'score_employee' && isset($old_input['ratings'][$criteria_id]['score_employee'])) {
            return htmlspecialchars($old_input['ratings'][$criteria_id]['score_employee']);
        }
        if ($field_key === 'comments_employee' && isset($old_input['ratings'][$criteria_id]['comments_employee'])) {
            return htmlspecialchars($old_input['ratings'][$criteria_id]['comments_employee']);
        }
        // If not in old_input (initial load), use existing_ratings_data
        if ($field_key === 'score_employee' && isset($existing_ratings_data[$criteria_id]['rating_score_employee'])) {
            return htmlspecialchars($existing_ratings_data[$criteria_id]['rating_score_employee']);
        }
        if ($field_key === 'comments_employee' && isset($existing_ratings_data[$criteria_id]['employee_self_assessment_comments_on_criteria'])) {
            return htmlspecialchars($existing_ratings_data[$criteria_id]['employee_self_assessment_comments_on_criteria']);
        }
    } else { // For overall review comment
        if (isset($old_input[$field_key])) {
            return htmlspecialchars($old_input[$field_key]);
        }
        if (isset($review_data[$field_key])) { // e.g., $review_data['employee_self_assessment_comments']
            return htmlspecialchars($review_data[$field_key]);
        }
    }
    return '';
}
?>

<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Employee Self-Assessment'); ?></h2>
                <p class="text-muted">
                    Review Period: <?php echo htmlspecialchars($review['review_period_name']); ?>
                    (<?php echo htmlspecialchars(date('M j, Y', strtotime($review['review_period_start_date']))) . ' - ' . htmlspecialchars(date('M j, Y', strtotime($review['review_period_end_date']))); ?>)
                </p>
                <p class="text-muted">Review for: <?php echo htmlspecialchars($review['employee_name']); ?></p>
                <?php if($review['reviewer_name']): ?>
                <p class="text-muted">Manager/Reviewer: <?php echo htmlspecialchars($review['reviewer_name']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/performance_reviews/save_self_assessment/' . $review['id']); ?>" method="POST">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Self-Assessment</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-title">Please correct the following errors:</h4>
                        <ul>
                            <?php foreach ($errors as $error_key => $error_message): ?>
                                <li>
                                <?php 
                                // Attempt to make error key more friendly for criteria ratings
                                if (strpos($error_key, 'rating_crit_') === 0) {
                                    $crit_id_for_error = substr($error_key, strlen('rating_crit_'));
                                    // Find criteria name for better message - assumes $criteria_list is available
                                    $crit_name_for_error = "Criteria ID {$crit_id_for_error}"; // Fallback
                                    foreach($criteria_list as $crit_item_for_error) {
                                        if ($crit_item_for_error['id'] == $crit_id_for_error) {
                                            $crit_name_for_error = $crit_item_for_error['name'];
                                            break;
                                        }
                                    }
                                    echo htmlspecialchars($crit_name_for_error) . ": " . htmlspecialchars($error_message);
                                } else {
                                    echo htmlspecialchars($error_message); 
                                }
                                ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php foreach ($criteria_list as $criterion): ?>
                    <div class="mb-4 p-3 border rounded">
                        <h4 class="mb-2"><?php echo htmlspecialchars($criterion['name']); ?></h4>
                        <?php if (!empty($criterion['description'])): ?>
                            <p class="text-muted form-hint"><?php echo nl2br(htmlspecialchars($criterion['description'])); ?></p>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Your Rating (1-5)</label>
                            <select name="ratings[<?php echo $criterion['id']; ?>][score_employee]" class="form-select <?php echo isset($errors['rating_crit_'.$criterion['id']]) ? 'is-invalid' : ''; ?>" style="max-width: 200px;">
                                <option value="">-- Select Rating --</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo (getSelfAssessmentVal('score_employee', $criterion['id'], $old_input, $review, $existing_ratings) == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                            <?php /* Hidden error display, main one is at top for ratings
                            <?php if (isset($errors['rating_crit_'.$criterion['id']])): ?>
                                <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['rating_crit_'.$criterion['id']]); ?></div>
                            <?php endif; ?> */ ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Your Comments on this Criterion</label>
                            <textarea name="ratings[<?php echo $criterion['id']; ?>][comments_employee]" class="form-control" rows="3" placeholder="Your specific comments for <?php echo htmlspecialchars($criterion['name']); ?>..."><?php echo getSelfAssessmentVal('comments_employee', $criterion['id'], $old_input, $review, $existing_ratings); ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>

                <hr class="my-4">
                <div class="mb-3">
                    <label class="form-label">Overall Self-Assessment Comments</label>
                    <textarea name="overall_employee_comments" class="form-control <?php echo isset($errors['overall_employee_comments']) ? 'is-invalid' : ''; ?>" rows="5" placeholder="Summarize your performance, achievements, and challenges during this review period..."><?php echo getSelfAssessmentVal('employee_self_assessment_comments', null, $old_input, $review, $existing_ratings); ?></textarea>
                    <?php if (isset($errors['overall_employee_comments'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['overall_employee_comments']); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/performance_reviews/my_pending_reviews'); ?>" class="btn btn-secondary me-2">Cancel / Save for Later</a>
                <button type="submit" class="btn btn-primary">Submit Self-Assessment</button>
            </div>
        </div>
    </form>
</div>
