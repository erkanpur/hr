<?php
// Loaded by ReviewPeriodController@add or @store (on error)
// $title, $errors, $old_input, $available_statuses are passed.
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3"><h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Add New Review Period'); ?></h2></div>
    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_periods/store'); ?>" method="POST">
            <div class="card-header"><h3 class="card-title">Period Details</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required">Name</label>
                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" name="name" placeholder="e.g., Annual Review 2024, Q3 Review" value="<?php echo htmlspecialchars($old_input['name'] ?? ''); ?>" required>
                    <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div><?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Start Date</label>
                        <input type="date" class="form-control <?php echo isset($errors['start_date']) ? 'is-invalid' : ''; ?>" name="start_date" value="<?php echo htmlspecialchars($old_input['start_date'] ?? ''); ?>" required>
                        <?php if (isset($errors['start_date'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['start_date']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">End Date</label>
                        <input type="date" class="form-control <?php echo isset($errors['end_date']) ? 'is-invalid' : ''; ?>" name="end_date" value="<?php echo htmlspecialchars($old_input['end_date'] ?? ''); ?>" required>
                        <?php if (isset($errors['end_date'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['end_date']); ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Status</label>
                    <select name="status" class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>" required>
                        <option value="">-- Select Status --</option>
                        <?php foreach($available_statuses as $status_val): ?>
                            <option value="<?php echo $status_val; ?>" <?php echo (isset($old_input['status']) && $old_input['status'] == $status_val) ? 'selected' : ''; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $status_val)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['status']); ?></div><?php endif; ?>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_periods'); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Period</button>
            </div>
        </form>
    </div>
</div>
