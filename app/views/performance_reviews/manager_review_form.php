<?php
// Loaded by PerformanceReviewController@manager_review_form
// Vars: $title, $review (main review data), $criteria_list (active criteria), 
//       $existing_ratings (keyed by criteria_id, contains both employee and potentially manager previous input), 
//       $errors (optional), $old_input (optional, defaults to $review for some fields)

// Helper to get value for manager's input fields, prioritizing old_input (POST data)
function getMgrReviewVal($field_key, $criteria_id = null, $old_input, $existing_rating_for_criterion, $review_data) {
    if ($criteria_id !== null) { // For specific criterion comment/score by manager
        $post_key_score = "ratings[{$criteria_id}][score_manager]";
        $post_key_comment = "ratings[{$criteria_id}][comments_manager]";
        if ($field_key === 'score_manager' && isset($old_input['ratings'][$criteria_id]['score_manager'])) {
            return htmlspecialchars($old_input['ratings'][$criteria_id]['score_manager']);
        }
        if ($field_key === 'comments_manager' && isset($old_input['ratings'][$criteria_id]['comments_manager'])) {
            return htmlspecialchars($old_input['ratings'][$criteria_id]['comments_manager']);
        }
        // If not in old_input (initial load or no POST data for this field), use existing_rating_for_criterion
        if ($field_key === 'score_manager' && isset($existing_rating_for_criterion['rating_score_manager'])) {
            return htmlspecialchars($existing_rating_for_criterion['rating_score_manager']);
        }
        if ($field_key === 'comments_manager' && isset($existing_rating_for_criterion['manager_comments_on_criteria'])) {
            return htmlspecialchars($existing_rating_for_criterion['manager_comments_on_criteria']);
        }
    } else { // For overall review comments by manager etc.
        if (isset($old_input[$field_key])) {
            return htmlspecialchars($old_input[$field_key]);
        }
        if (isset($review_data[$field_key])) { 
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
                <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Manager Performance Review'); ?></h2>
                <p class="text-muted">
                    Review Period: <?php echo htmlspecialchars($review['review_period_name']); ?>
                    (<?php echo htmlspecialchars(date('M j, Y', strtotime($review['review_period_start_date']))) . ' - ' . htmlspecialchars(date('M j, Y', strtotime($review['review_period_end_date']))); ?>)
                </p>
                <p class="text-muted">Review For: <strong><?php echo htmlspecialchars($review['employee_name']); ?></strong></p>
                <p class="text-muted">Current Status: <span class="badge bg-orange-lt"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ',$review['status']))); ?></span></p>
            </div>
        </div>
    </div>

    <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/performance_reviews/save_manager_review/' . $review['id']); ?>" method="POST">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manager's Assessment</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-title">Please correct the following errors:</h4>
                        <ul>
                            <?php foreach ($errors as $error_key => $error_message): ?>
                                <li>
                                <?php 
                                if (strpos($error_key, 'rating_crit_mgr_') === 0) {
                                    $crit_id_for_error = substr($error_key, strlen('rating_crit_mgr_'));
                                    $crit_name_for_error = "Criteria ID {$crit_id_for_error}"; 
                                    foreach($criteria_list as $crit_item_for_error) {
                                        if ($crit_item_for_error['id'] == $crit_id_for_error) {
                                            $crit_name_for_error = $crit_item_for_error['name'];
                                            break;
                                        }
                                    }
                                    echo htmlspecialchars($crit_name_for_error) . " (Manager Rating): " . htmlspecialchars($error_message);
                                } else {
                                    echo htmlspecialchars($error_message); 
                                }
                                ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <h3 class="mb-3">Review Criteria & Feedback</h3>
                <?php foreach ($criteria_list as $criterion): 
                    $current_rating_data = $existing_ratings[$criterion['id']] ?? [];
                ?>
                    <div class="mb-4 p-3 border rounded">
                        <h4><?php echo htmlspecialchars($criterion['name']); ?></h4>
                        <?php if (!empty($criterion['description'])): ?>
                            <p class="text-muted form-hint"><?php echo nl2br(htmlspecialchars($criterion['description'])); ?></p>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset class="form-fieldset">
                                    <legend>Employee's Self-Assessment (Read-Only)</legend>
                                    <div class="mb-2">
                                        <strong>Rating:</strong> 
                                        <?php echo htmlspecialchars($current_rating_data['rating_score_employee'] ?? 'Not rated'); ?>
                                    </div>
                                    <div>
                                        <strong>Comments:</strong>
                                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($current_rating_data['employee_self_assessment_comments_on_criteria'] ?? 'No comments.')); ?></p>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-fieldset">
                                    <legend>Manager's Assessment</legend>
                                    <div class="mb-3">
                                        <label class="form-label">Manager Rating (1-5)</label>
                                        <select name="ratings[<?php echo $criterion['id']; ?>][score_manager]" class="form-select <?php echo isset($errors['rating_crit_mgr_'.$criterion['id']]) ? 'is-invalid' : ''; ?>" style="max-width: 200px;">
                                            <option value="">-- Select Rating --</option>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo (getMgrReviewVal('score_manager', $criterion['id'], $old_input, $current_rating_data, $review) == $i) ? 'selected' : ''; ?>>
                                                <?php echo $i; ?>
                                            </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Manager Comments on this Criterion</label>
                                        <textarea name="ratings[<?php echo $criterion['id']; ?>][comments_manager]" class="form-control" rows="3" placeholder="Manager's specific comments for <?php echo htmlspecialchars($criterion['name']); ?>..."><?php echo getMgrReviewVal('comments_manager', $criterion['id'], $old_input, $current_rating_data, $review); ?></textarea>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <hr class="my-4">
                <h3 class="mb-3">Overall Review & Goals</h3>
                
                <div class="mb-3">
                    <label class="form-label">Employee's Overall Self-Assessment Comments (Read-Only)</label>
                    <div class="card card-body bg-light">
                        <?php echo nl2br(htmlspecialchars($review['employee_self_assessment_comments'] ?? 'No overall self-assessment comments provided.')); ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Overall Manager Comments</label>
                    <textarea name="overall_manager_comments" class="form-control <?php echo isset($errors['overall_manager_comments']) ? 'is-invalid' : ''; ?>" rows="5" placeholder="Manager's summary of overall performance..."><?php echo getMgrReviewVal('overall_manager_comments', null, $old_input, [], $review); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Identified Strengths</label>
                    <textarea name="strengths" class="form-control <?php echo isset($errors['strengths']) ? 'is-invalid' : ''; ?>" rows="3" placeholder="Key strengths identified..."><?php echo getMgrReviewVal('strengths', null, $old_input, [], $review); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Areas for Improvement</label>
                    <textarea name="areas_for_improvement" class="form-control <?php echo isset($errors['areas_for_improvement']) ? 'is-invalid' : ''; ?>" rows="3" placeholder="Areas where development is needed..."><?php echo getMgrReviewVal('areas_for_improvement', null, $old_input, [], $review); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Goals for Next Period</label>
                    <textarea name="goals_for_next_period" class="form-control <?php echo isset($errors['goals_for_next_period']) ? 'is-invalid' : ''; ?>" rows="3" placeholder="Specific, measurable goals for the next review cycle..."><?php echo getMgrReviewVal('goals_for_next_period', null, $old_input, [], $review); ?></textarea>
                </div>
                <div class="mb-3" style="max-width: 250px;">
                    <label class="form-label">Overall Score (Optional)</label>
                    <input type="number" step="0.01" name="overall_score" class="form-control <?php echo isset($errors['overall_score']) ? 'is-invalid' : ''; ?>" placeholder="e.g., 4.5" value="<?php echo getMgrReviewVal('overall_score', null, $old_input, [], $review); ?>">
                     <?php if (isset($errors['overall_score'])): ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['overall_score']); ?></div><?php endif; ?>
                </div>
            </div>

            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/performance_reviews/team_reviews_pending_manager'); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Manager Review & Complete</button>
            </div>
        </div>
    </form>
</div>
