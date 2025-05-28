<?php
// Loaded by PerformanceReviewController@my_pending_reviews
// $title, $reviews are passed.
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'My Pending Reviews'); ?></h2>
    </div>

    <?php if (isset($_SESSION['info_message'])): ?>
        <div class="alert alert-info alert-dismissible" role="alert">
            <div><?php echo htmlspecialchars($_SESSION['info_message']); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['info_message']); ?>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Reviews Awaiting Your Self-Assessment</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th>Review Period</th>
                        <th>Reviewer</th>
                        <th>Status</th>
                        <th>Due By (End of Period)</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                        <tr><td colspan="5" class="text-center">You have no performance reviews awaiting your self-assessment.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['review_period_name']); ?></td>
                                <td><?php echo htmlspecialchars($review['reviewer_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-yellow-lt"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $review['status']))); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($review['review_period_end_date']))); ?></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/performance_reviews/self_assessment_form/' . $review['id']); ?>" class="btn btn-sm btn-primary">
                                        <?php echo ($review['status'] == 'not_started') ? 'Start Self-Assessment' : 'Continue Self-Assessment'; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
