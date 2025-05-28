<?php
// This view is loaded by EmployeeController@view
// The $employee variable (associative array of employee data) and $title are passed via extract() in loadView().

// global $title; // Already available from controller setting it for layout.php
?>

<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? 'Employee Details'); ?>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees'); ?>" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                        Back to Employee List
                    </a>
                    <!-- Future: Edit and Delete buttons -->
                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/edit/' . $employee['id']); ?>" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path><path d="M16 5l3 3"></path></svg>
                        Edit Employee
                    </a>
                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/delete_employee/' . $employee['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 7l16 0"></path><path d="M10 11l0 6"></path><path d="M14 11l0 6"></path><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg>
                        Delete Employee
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <?php if (!empty($employee['profile_picture_path'])): ?>
                        <img src="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/public/' . $employee['profile_picture_path']); ?>" alt="<?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?> Profile Picture" class="avatar avatar-xl avatar-rounded mb-3">
                    <?php else: ?>
                        <span class="avatar avatar-xl avatar-rounded mb-3"><?php echo strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)); ?></span>
                    <?php endif; ?>
                    <h3 class="card-title mb-1"><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></h3>
                    <p class="text-muted"><?php echo htmlspecialchars($employee['job_title']); ?></p>
                     <?php 
                        $status_class = '';
                        switch ($employee['status']) {
                            case 'active': $status_class = 'badge bg-success-lt'; break;
                            case 'on_leave': $status_class = 'badge bg-warning-lt'; break;
                            case 'terminated': $status_class = 'badge bg-danger-lt'; break;
                            default: $status_class = 'badge bg-secondary-lt'; break;
                        }
                    ?>
                    <span class="<?php echo $status_class; ?> mb-2"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $employee['status']))); ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employee Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Employee ID:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($employee['id']); ?></dd>

                        <dt class="col-sm-3">Full Name:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></dd>

                        <dt class="col-sm-3">Email Address:</dt>
                        <dd class="col-sm-9"><a href="mailto:<?php echo htmlspecialchars($employee['email']); ?>"><?php echo htmlspecialchars($employee['email']); ?></a></dd>

                        <dt class="col-sm-3">Phone Number:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($employee['phone_number'] ?? 'N/A'); ?></dd>
                        
                        <dt class="col-sm-3">Job Title:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($employee['job_title']); ?></dd>

                        <dt class="col-sm-3">Hire Date:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars(date('F j, Y', strtotime($employee['hire_date']))); ?></dd>

                        <dt class="col-sm-3">Salary:</dt>
                        <dd class="col-sm-9"><?php echo ($employee['salary'] !== null) ? '$' . htmlspecialchars(number_format($employee['salary'], 2)) : 'N/A'; ?></dd>
                        
                        <dt class="col-sm-3">Date of Birth:</dt>
                        <dd class="col-sm-9"><?php echo ($employee['date_of_birth'] !== null) ? htmlspecialchars(date('F j, Y', strtotime($employee['date_of_birth']))) : 'N/A'; ?></dd>
                        
                        <dt class="col-sm-3">Address:</dt>
                        <dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($employee['address'] ?? 'N/A')); ?></dd>
                        
                        <dt class="col-sm-3">Emergency Contact:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($employee['emergency_contact_name'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($employee['emergency_contact_phone'] ?? 'N/A'); ?>)</dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9"><span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $employee['status']))); ?></span></dd>

                        <dt class="col-sm-3">Department ID:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($employee['department_id'] ?? 'N/A'); ?> <!-- To be replaced with Department Name later --></dd>

                        <dt class="col-sm-3">Joined On:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($employee['created_at']))); ?></dd>

                        <dt class="col-sm-3">Last Updated:</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($employee['updated_at']))); ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
