<?php
// Loaded by PerformanceReviewController@view_for_acknowledgement
// Vars: $title, $review, $criteria_list, $all_ratings (keyed by criteria_id), $old_input (for final comments)
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Acknowledge Performance Review'); ?></h2>
                <p class="text-muted">
                    Review Period: <?php echo htmlspecialchars($review['review_period_name']); ?>
                    (<?php echo htmlspecialchars(date('M j, Y', strtotime($review['review_period_start_date']))) . ' - ' . htmlspecialchars(date('M j, Y', strtotime($review['review_period_end_date']))); ?>)
                </p>
                <p class="text-muted">Review for: <?php echo htmlspecialchars($review['employee_name']); ?></p>
                <p class="text-muted">Manager/Reviewer: <?php echo htmlspecialchars($review['reviewer_name'] ?? 'N/A'); ?></p>
                 <p class="text-muted">Status: <span class="badge bg-info-lt"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ',$review['status']))); ?></span></p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Completed Review Details</h3>
        </div>
        <div class="card-body">
            <h4 class="mb-3 text-primary">Overall Manager Assessment:</h4>
            <div class="mb-3 p-3 border rounded bg-light">
                <p><strong>Overall Comments:</strong><br><?php echo nl2br(htmlspecialchars($review['overall_manager_comments'] ?? 'N/A')); ?></p>
                <p><strong>Strengths:</strong><br><?php echo nl2br(htmlspecialchars($review['strengths'] ?? 'N/A')); ?></p>
                <p><strong>Areas for Improvement:</strong><br><?php echo nl2br(htmlspecialchars($review['areas_for_improvement'] ?? 'N/A')); ?></p>
                <p><strong>Goals for Next Period:</strong><br><?php echo nl2br(htmlspecialchars($review['goals_for_next_period'] ?? 'N/A')); ?></p>
                <?php if($review['overall_score'] !== null): ?>
                <p><strong>Overall Score:</strong> <?php echo htmlspecialchars($review['overall_score']); ?></p>
                <?php endif; ?>
            </div>

            <hr class="my-4">
            <h4 class="mb-3 text-primary">Detailed Criteria Assessment:</h4>
            <?php foreach ($criteria_list as $criterion): 
                $current_rating_data = $all_ratings[$criterion['id']] ?? [];
            ?>
                <div class="mb-4 p-3 border rounded">
                    <h5><?php echo htmlspecialchars($criterion['name']); ?></h5>
                    <?php if (!empty($criterion['description'])): ?>
                        <p class="text-muted form-hint small"><?php echo nl2br(htmlspecialchars($criterion['description'])); ?></p>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="p-2 border bg-azure-lt rounded mb-2">
                                <strong>Your Self-Assessment:</strong><br>
                                Rating: <?php echo htmlspecialchars($current_rating_data['rating_score_employee'] ?? 'Not rated'); ?><br>
                                Comments: <div class="text-muted ps-2" style="white-space: pre-wrap;"><?php echo htmlspecialchars($current_rating_data['employee_self_assessment_comments_on_criteria'] ?? 'No comments.'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="p-2 border bg-green-lt rounded mb-2">
                                <strong>Manager's Assessment:</strong><br>
                                Rating: <?php echo htmlspecialchars($current_rating_data['rating_score_manager'] ?? 'Not rated'); ?><br>
                                Comments: <div class="text-muted ps-2" style="white-space: pre-wrap;"><?php echo htmlspecialchars($current_rating_data['manager_comments_on_criteria'] ?? 'No comments.'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <hr class="my-4">
            <h4 class="mb-3 text-primary">Your Final Comments & Acknowledgement:</h4>
            <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/performance_reviews/acknowledge_review_submit/' . $review['id']); ?>" method="POST">
                 <div class="mb-3">
                    <label class="form-label">Your Final Comments (Optional)</label>
                    <textarea name="employee_final_comments" class="form-control" rows="4" placeholder="Add any final comments you have about this review..."><?php echo htmlspecialchars($old_input['employee_final_comments'] ?? $review['employee_final_comments'] ?? ''); ?></textarea>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" name="acknowledged" id="acknowledged" required>
                    <label class="form-check-label required" for="acknowledged">I acknowledge that I have read and discussed this performance review with my manager.</label>
                </div>
                <div class="text-end">
                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/performance_reviews/my_pending_reviews'); ?>" class="btn btn-secondary me-2">Back to My Reviews</a>
                    <button type="submit" class="btn btn-success">Acknowledge and Finalize Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
