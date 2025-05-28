<?php
// Loaded by PerformanceReviewController@team_reviews_pending_manager
// $title, $reviews are passed.
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Team Reviews Pending Your Input'); ?></h2>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Performance Reviews Awaiting Your Assessment</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th>Review Period</th>
                        <th>Employee</th>
                        <th>Status</th>
                        <th>Review Due By (End of Period)</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                        <tr><td colspan="5" class="text-center">You have no performance reviews currently awaiting your input.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['review_period_name']); ?></td>
                                <td><?php echo htmlspecialchars($review['employee_name']); ?></td>
                                <td>
                                    <span class="badge bg-orange-lt"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $review['status']))); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($review['review_period_end_date']))); ?></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/performance_reviews/manager_review_form/' . $review['id']); ?>" class="btn btn-sm btn-primary">
                                        Complete Manager Review
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
