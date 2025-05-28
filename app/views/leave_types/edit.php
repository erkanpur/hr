<?php
// Loaded by LeaveTypeController@edit or LeaveTypeController@update_type (on validation error)
// $leave_type (original leave type data for pre-filling) is passed.
// $old_input (submitted data on validation fail, or $leave_type data on initial load) is passed.
// $errors (validation errors) is passed.
// $title variable is passed for the page title.

$leave_type_id = $leave_type['id'] ?? null;

// Helper function to get value, prioritizing old_input over original $leave_type data
function getEditValue($field_name, $old_input, $leave_type_data) {
    return htmlspecialchars($old_input[$field_name] ?? $leave_type_data[$field_name] ?? '');
}

// Helper function for checkboxes/switches
function isEditChecked($field_name, $old_input, $leave_type_data) {
    // If old_input has the field, its presence (even if '0' from POST) means it was submitted.
    // If it's '1', it was checked. If it's '0' or not set in old_input (for a checkbox that wasn't ticked), it's unchecked.
    // If old_input is not set for this field (initial form load), use $leave_type_data.
    if (isset($old_input[$field_name])) {
        return $old_input[$field_name] == '1';
    } elseif (isset($leave_type_data[$field_name])) {
        return (bool)$leave_type_data[$field_name];
    }
    return false; // Default to unchecked if not found in either (should not happen for existing data)
}

?>

<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? 'Edit Leave Type'); ?>
                </h2>
            </div>
        </div>
    </div>

    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_types/update_type/' . $leave_type_id); ?>" method="POST">
            <div class="card-header">
                <h3 class="card-title">Leave Type Details</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required">Name</label>
                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" name="name" placeholder="Enter leave type name" value="<?php echo getEditValue('name', $old_input, $leave_type); ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" name="description" rows="3" placeholder="Enter a brief description"><?php echo getEditValue('description', $old_input, $leave_type); ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['description']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Days Allocated Annually</label>
                    <input type="number" min="0" step="1" class="form-control <?php echo isset($errors['days_allocated_annually']) ? 'is-invalid' : ''; ?>" name="days_allocated_annually" placeholder="Leave blank if not applicable" value="<?php echo getEditValue('days_allocated_annually', $old_input, $leave_type); ?>">
                    <small class="form-hint">Leave blank if this leave type does not have a fixed annual allocation.</small>
                    <?php if (isset($errors['days_allocated_annually'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['days_allocated_annually']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_paid" value="1" <?php echo isEditChecked('is_paid', $old_input, $leave_type) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_paid">Is this leave paid?</label>
                    </div>
                    <?php if (isset($errors['is_paid'])): ?>
                        <div class="text-danger small mt-1"><?php echo htmlspecialchars($errors['is_paid']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?php echo isEditChecked('is_active', $old_input, $leave_type) ? 'checked' : ''; ?>>
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
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
