<?php
// Loaded by LeaveRequestController@apply or LeaveRequestController@store_request (on validation error)
// $title, $leave_types (array of active leave types), $errors (optional), $old_input (optional) are passed.
?>

<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? 'Apply for Leave'); ?>
                </h2>
            </div>
        </div>
    </div>

    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/store_request'); ?>" method="POST">
            <div class="card-header">
                <h3 class="card-title">Leave Application Form</h3>
            </div>
            <div class="card-body">
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($errors['general']); ?>
                    </div>
                <?php endif; ?>
                 <?php if (isset($errors['database'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($errors['database']); ?>
                    </div>
                <?php endif; ?>


                <div class="mb-3">
                    <label class="form-label required">Leave Type</label>
                    <select name="leave_type_id" class="form-select <?php echo isset($errors['leave_type_id']) ? 'is-invalid' : ''; ?>" required>
                        <option value="">-- Select Leave Type --</option>
                        <?php if (!empty($leave_types)): ?>
                            <?php foreach ($leave_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type['id']); ?>" 
                                        <?php echo (isset($old_input['leave_type_id']) && $old_input['leave_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['name']); ?> 
                                    <?php if ($type['days_allocated_annually'] !== null): ?>
                                        (Allocated: <?php echo htmlspecialchars($type['days_allocated_annually']); ?> days)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (isset($errors['leave_type_id'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['leave_type_id']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Start Date</label>
                            <input type="date" class="form-control <?php echo isset($errors['start_date']) ? 'is-invalid' : ''; ?>" name="start_date" value="<?php echo htmlspecialchars($old_input['start_date'] ?? ''); ?>" required>
                            <?php if (isset($errors['start_date'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['start_date']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">End Date</label>
                            <input type="date" class="form-control <?php echo isset($errors['end_date']) ? 'is-invalid' : ''; ?>" name="end_date" value="<?php echo htmlspecialchars($old_input['end_date'] ?? ''); ?>" required>
                            <?php if (isset($errors['end_date'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['end_date']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Calculated Days</label>
                    <input type="text" class="form-control" id="calculated_days" value="To be calculated upon submission" readonly>
                    <small class="form-hint">This is an indicative calculation. Final days approved may vary. For half-day requests, please specify "half day" in the reason.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Reason for Leave</label>
                    <textarea class="form-control <?php echo isset($errors['reason']) ? 'is-invalid' : ''; ?>" name="reason" rows="4" placeholder="Please provide a reason for your leave request..." required><?php echo htmlspecialchars($old_input['reason'] ?? ''); ?></textarea>
                    <?php if (isset($errors['reason'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['reason']); ?></div>
                    <?php endif; ?>
                </div>

            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/'); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- Optional: JavaScript for live calculation of days (very basic example) -->
<!-- <script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const calculatedDaysInput = document.getElementById('calculated_days');

    function calculateDaysDisplay() {
        if (startDateInput.value && endDateInput.value) {
            try {
                const start = new Date(startDateInput.value);
                const end = new Date(endDateInput.value);
                if (end < start) {
                    calculatedDaysInput.value = 'End date before start date';
                    return;
                }
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Inclusive days
                calculatedDaysInput.value = diffDays + (diffDays === 1 ? ' day' : ' days');
            } catch (e) {
                calculatedDaysInput.value = 'Invalid date format';
            }
        } else {
            calculatedDaysInput.value = 'Dates not fully selected';
        }
    }

    if(startDateInput && endDateInput && calculatedDaysInput) {
        startDateInput.addEventListener('change', calculateDaysDisplay);
        endDateInput.addEventListener('change', calculateDaysDisplay);
        // Initial calculation if dates are pre-filled (e.g. on validation error)
        // calculateDaysDisplay(); // This might be complex if dates are invalid from server
    }
});
</script> -->
