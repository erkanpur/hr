<?php
// Loaded by LeaveTypeController@add or LeaveTypeController@store (on validation error)
// $title, $errors (optional), $old_input (optional) are passed via extract().
?>

<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? 'Add New Leave Type'); ?>
                </h2>
            </div>
        </div>
    </div>

    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_types/store'); ?>" method="POST">
            <div class="card-header">
                <h3 class="card-title">Leave Type Details</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required">Name</label>
                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" name="name" placeholder="Enter leave type name (e.g., Annual Leave, Sick Leave)" value="<?php echo htmlspecialchars($old_input['name'] ?? ''); ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" name="description" rows="3" placeholder="Enter a brief description of the leave type"><?php echo htmlspecialchars($old_input['description'] ?? ''); ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['description']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Days Allocated Annually</label>
                    <input type="number" min="0" step="1" class="form-control <?php echo isset($errors['days_allocated_annually']) ? 'is-invalid' : ''; ?>" name="days_allocated_annually" placeholder="Enter number of days (e.g., 20) or leave blank if not applicable" value="<?php echo htmlspecialchars($old_input['days_allocated_annually'] ?? ''); ?>">
                    <small class="form-hint">Leave blank if this leave type does not have a fixed annual allocation (e.g., Unpaid Leave).</small>
                    <?php if (isset($errors['days_allocated_annually'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['days_allocated_annually']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_paid" value="1" <?php echo (isset($old_input['is_paid']) && $old_input['is_paid'] == '1' || !isset($old_input['is_paid']) && !$_POST) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_paid">Is this leave paid?</label>
                    </div>
                     <?php if (isset($errors['is_paid'])): ?>
                        <div class="text-danger small mt-1"><?php echo htmlspecialchars($errors['is_paid']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?php echo (isset($old_input['is_active']) && $old_input['is_active'] == '1' || !isset($old_input['is_active']) && !$_POST) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">Is this leave type active?</label>
                         <small class="form-hint d-block">Inactive leave types cannot be used for new leave requests but will remain for historical records.</small>
                    </div>
                    <?php if (isset($errors['is_active'])): ?>
                        <div class="text-danger small mt-1"><?php echo htmlspecialchars($errors['is_active']); ?></div>
                    <?php endif; ?>
                </div>

            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_types'); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Leave Type</button>
            </div>
        </form>
    </div>
</div>
