<?php
// This view is loaded by EmployeeController@edit
// $employee variable (current employee data) is passed for pre-filling.
// $old_input variable (submitted data on validation fail, or $employee data on initial load) is passed.
// $errors variable (validation errors) is passed.
// $title variable is passed for the page title.

global $title; // Used by layout.php
$employee_id = $employee['id'] ?? null; // Get ID for form action

// Use $old_input for values if available (e.g., after validation failure),
// otherwise use $employee data for initial pre-fill.
function getValue($field_name, $old_input, $employee_data) {
    return htmlspecialchars($old_input[$field_name] ?? $employee_data[$field_name] ?? '');
}
function getSelected($field_name, $value, $old_input, $employee_data) {
    $current_value = $old_input[$field_name] ?? $employee_data[$field_name] ?? '';
    return ($current_value == $value) ? 'selected' : '';
}

?>

<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? 'Edit Employee'); ?>
                </h2>
            </div>
        </div>
    </div>

    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/update_employee/' . $employee_id); ?>" method="POST" enctype="multipart/form-data">
            <div class="card-header">
                <h3 class="card-title">Edit Employee Details</h3>
            </div>
            <div class="card-body">
                <!-- Display existing profile picture -->
                <?php if (!empty($employee['profile_picture_path'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Profile Picture</label>
                        <div>
                            <img src="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/public/' . $employee['profile_picture_path']); ?>" alt="Current Profile Picture" class="avatar avatar-xl avatar-rounded mb-2">
                        </div>
                        <input type="hidden" name="existing_profile_picture_path" value="<?php echo htmlspecialchars($employee['profile_picture_path']); ?>">
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">New Profile Picture (optional)</label>
                    <input type="file" class="form-control <?php echo isset($errors['profile_picture']) ? 'is-invalid' : ''; ?>" name="profile_picture">
                    <?php if (isset($errors['profile_picture'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['profile_picture']); ?></div>
                    <?php endif; ?>
                </div>

                <hr class="my-4">


                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">First Name</label>
                            <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" name="first_name" placeholder="Enter first name" value="<?php echo getValue('first_name', $old_input, $employee); ?>" required>
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['first_name']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Last Name</label>
                            <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" name="last_name" placeholder="Enter last name" value="<?php echo getValue('last_name', $old_input, $employee); ?>" required>
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['last_name']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Email address</label>
                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" name="email" placeholder="Enter email" value="<?php echo getValue('email', $old_input, $employee); ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control <?php echo isset($errors['phone_number']) ? 'is-invalid' : ''; ?>" name="phone_number" placeholder="Enter phone number" value="<?php echo getValue('phone_number', $old_input, $employee); ?>">
                            <?php if (isset($errors['phone_number'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['phone_number']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Job Title</label>
                            <input type="text" class="form-control <?php echo isset($errors['job_title']) ? 'is-invalid' : ''; ?>" name="job_title" placeholder="Enter job title" value="<?php echo getValue('job_title', $old_input, $employee); ?>" required>
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
                            <input type="date" class="form-control <?php echo isset($errors['hire_date']) ? 'is-invalid' : ''; ?>" name="hire_date" value="<?php echo getValue('hire_date', $old_input, $employee); ?>" required>
                            <?php if (isset($errors['hire_date'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['hire_date']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Salary</label>
                            <input type="number" step="0.01" class="form-control <?php echo isset($errors['salary']) ? 'is-invalid' : ''; ?>" name="salary" placeholder="Enter salary" value="<?php echo getValue('salary', $old_input, $employee); ?>">
                            <?php if (isset($errors['salary'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['salary']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>" name="address" rows="3" placeholder="Enter address"><?php echo getValue('address', $old_input, $employee); ?></textarea>
                    <?php if (isset($errors['address'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['address']); ?></div>
                            <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control <?php echo isset($errors['date_of_birth']) ? 'is-invalid' : ''; ?>" name="date_of_birth" value="<?php echo getValue('date_of_birth', $old_input, $employee); ?>">
                            <?php if (isset($errors['date_of_birth'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['date_of_birth']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>">
                                <option value="active" <?php echo getSelected('status', 'active', $old_input, $employee); ?>>Active</option>
                                <option value="on_leave" <?php echo getSelected('status', 'on_leave', $old_input, $employee); ?>>On Leave</option>
                                <option value="terminated" <?php echo getSelected('status', 'terminated', $old_input, $employee); ?>>Terminated</option>
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
                            <input type="text" class="form-control <?php echo isset($errors['emergency_contact_name']) ? 'is-invalid' : ''; ?>" name="emergency_contact_name" placeholder="Emergency contact name" value="<?php echo getValue('emergency_contact_name', $old_input, $employee); ?>">
                            <?php if (isset($errors['emergency_contact_name'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['emergency_contact_name']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Emergency Contact Phone</label>
                            <input type="tel" class="form-control <?php echo isset($errors['emergency_contact_phone']) ? 'is-invalid' : ''; ?>" name="emergency_contact_phone" placeholder="Emergency contact phone" value="<?php echo getValue('emergency_contact_phone', $old_input, $employee); ?>">
                            <?php if (isset($errors['emergency_contact_phone'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['emergency_contact_phone']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Department ID will be a select dropdown populated from a departments table later -->
                <input type="hidden" name="department_id" value="<?php echo getValue('department_id', $old_input, $employee); ?>"> 

            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/view/' . $employee_id); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
