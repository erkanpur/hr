<?php
// Loaded by PerformanceReviewController@initiate_form or @process_initiation (on error)
// $title, $periods, $errors (optional), $old_input (optional) are passed.
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Initiate Performance Reviews'); ?></h2>
    </div>
    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/performance_reviews/process_initiation'); ?>" method="POST">
            <div class="card-header"><h3 class="card-title">Select Period and Options</h3></div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <p><strong>Please correct the following errors:</strong></p>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label required">Review Period</label>
                    <select name="review_period_id" class="form-select <?php echo isset($errors['review_period_id']) ? 'is-invalid' : ''; ?>" required>
                        <option value="">-- Select a Review Period --</option>
                        <?php if (!empty($periods)): ?>
                            <?php foreach ($periods as $period): ?>
                                <option value="<?php echo htmlspecialchars($period['id']); ?>" 
                                        <?php echo (isset($old_input['review_period_id']) && $old_input['review_period_id'] == $period['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($period['name']); ?> (<?php echo htmlspecialchars(date('M j, Y', strtotime($period['start_date']))) . ' - ' . htmlspecialchars(date('M j, Y', strtotime($period['end_date']))); ?>) - Status: <?php echo htmlspecialchars($period['status']);?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (isset($errors['review_period_id'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['review_period_id']); ?></div><?php endif; ?>
                </div>

                <div class="mb-3">
                    <p class="form-label">Target Employees</p>
                    <p class="form-hint">Currently, reviews will be initiated for <strong>all employees</strong> in the system for the selected period if they don't already have one. Future enhancements will allow selecting specific employees or departments.</p>
                    <p class="form-hint">The reviewer will be set to the employee's manager (if defined in their profile, feature pending) or the initiating user.</p>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_periods'); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Initiate Reviews</button>
            </div>
        </form>
    </div>
</div>
