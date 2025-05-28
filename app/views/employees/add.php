<?php
// This view would be included by a controller action.
// Variables like $title, $errors, $old_input would ideally be passed by the controller.
// For now, we'll define placeholders or use them conditionally.

// Example: $title = 'Add New Employee'; (set in controller)
// Example: $errors = []; (passed from controller on validation failure)
// Example: $old_input = []; (passed from controller on validation failure)

global $title; // Assuming title is set globally from controller for layout.php
$errors = $errors ?? []; // Initialize if not set
$old_input = $old_input ?? []; // Initialize if not set

?>

<div class="container-xl">
    <!-- Page title is already handled by layout.php based on $title -->
    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim($_SERVER['SCRIPT_NAME'], 'index.php') . '/employees/store'); ?>" method="POST" enctype="multipart/form-data">
            <div class="card-header">
                <h3 class="card-title">Employee Details</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">First Name</label>
                            <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" name="first_name" placeholder="Enter first name" value="<?php echo htmlspecialchars($old_input['first_name'] ?? ''); ?>" required>
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['first_name']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Last Name</label>
                            <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" name="last_name" placeholder="Enter last name" value="<?php echo htmlspecialchars($old_input['last_name'] ?? ''); ?>" required>
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['last_name']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Email address</label>
                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" name="email" placeholder="Enter email" value="<?php echo htmlspecialchars($old_input['email'] ?? ''); ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control <?php echo isset($errors['phone_number']) ? 'is-invalid' : ''; ?>" name="phone_number" placeholder="Enter phone number" value="<?php echo htmlspecialchars($old_input['phone_number'] ?? ''); ?>">
                            <?php if (isset($errors['phone_number'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['phone_number']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Job Title</label>
                            <input type="text" class="form-control <?php echo isset($errors['job_title']) ? 'is-invalid' : ''; ?>" name="job_title" placeholder="Enter job title" value="<?php echo htmlspecialchars($old_input['job_title'] ?? ''); ?>" required>
                            <?php if (isset($errors['job_title'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['job_title']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Hire Date</label>
                            <input type="date" class="form-control <?php echo isset($errors['hire_date']) ? 'is-invalid' : ''; ?>" name="hire_date" value="<?php echo htmlspecialchars($old_input['hire_date'] ?? ''); ?>" required>
                            <?php if (isset($errors['hire_date'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['hire_date']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Salary</label>
                            <input type="number" step="0.01" class="form-control <?php echo isset($errors['salary']) ? 'is-invalid' : ''; ?>" name="salary" placeholder="Enter salary" value="<?php echo htmlspecialchars($old_input['salary'] ?? ''); ?>">
                            <?php if (isset($errors['salary'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['salary']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>" name="address" rows="3" placeholder="Enter address"><?php echo htmlspecialchars($old_input['address'] ?? ''); ?></textarea>
                    <?php if (isset($errors['address'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['address']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control <?php echo isset($errors['date_of_birth']) ? 'is-invalid' : ''; ?>" name="date_of_birth" value="<?php echo htmlspecialchars($old_input['date_of_birth'] ?? ''); ?>">
                            <?php if (isset($errors['date_of_birth'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['date_of_birth']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>">
                                <option value="active" <?php echo (isset($old_input['status']) && $old_input['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="on_leave" <?php echo (isset($old_input['status']) && $old_input['status'] == 'on_leave') ? 'selected' : ''; ?>>On Leave</option>
                                <option value="terminated" <?php echo (isset($old_input['status']) && $old_input['status'] == 'terminated') ? 'selected' : ''; ?>>Terminated</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['status']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Emergency Contact Name</label>
                            <input type="text" class="form-control <?php echo isset($errors['emergency_contact_name']) ? 'is-invalid' : ''; ?>" name="emergency_contact_name" placeholder="Emergency contact name" value="<?php echo htmlspecialchars($old_input['emergency_contact_name'] ?? ''); ?>">
                             <?php if (isset($errors['emergency_contact_name'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['emergency_contact_name']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Emergency Contact Phone</label>
                            <input type="tel" class="form-control <?php echo isset($errors['emergency_contact_phone']) ? 'is-invalid' : ''; ?>" name="emergency_contact_phone" placeholder="Emergency contact phone" value="<?php echo htmlspecialchars($old_input['emergency_contact_phone'] ?? ''); ?>">
                            <?php if (isset($errors['emergency_contact_phone'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['emergency_contact_phone']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                 <div class="mb-3">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" class="form-control <?php echo isset($errors['profile_picture']) ? 'is-invalid' : ''; ?>" name="profile_picture">
                    <?php if (isset($errors['profile_picture'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['profile_picture']); ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Department ID will be a select dropdown populated from a departments table later -->
                <input type="hidden" name="department_id" value=""> 


            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim($_SERVER['SCRIPT_NAME'], 'index.php') . '/employees'); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Employee</button>
            </div>
        </form>
    </div>
</div>
