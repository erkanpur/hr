<?php
// This view is loaded by EmployeeController@index
// The $employees variable (array of employee data) and $title are passed via extract() in loadView().

// global $title; // Already available from controller setting it for layout.php

?>

<div class="container-xl">
    <!-- Page title is usually handled by layout.php, but if you need it in the view explicitly: -->
    <!-- <h1 class="page-title"><?php echo htmlspecialchars($title ?? 'Manage Employees'); ?></h1> -->

    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? 'Manage Employees'); ?>
                </h2>
                <div class="text-muted mt-1"><?php echo count($employees); ?> employee(s) found.</div>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/add'); ?>" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        Add New Employee
                    </a>
                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/add'); ?>" class="btn btn-primary d-sm-none btn-icon" aria-label="Add new employee">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Job Title</th>
                        <th>Hire Date</th>
                        <th>Status</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No employees found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($employee['id']); ?></td>
                                <td><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($employee['email']); ?>"><?php echo htmlspecialchars($employee['email']); ?></a></td>
                                <td><?php echo htmlspecialchars($employee['job_title']); ?></td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($employee['hire_date']))); ?></td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    switch ($employee['status']) {
                                        case 'active': $status_class = 'badge bg-success-lt'; break;
                                        case 'on_leave': $status_class = 'badge bg-warning-lt'; break;
                                        case 'terminated': $status_class = 'badge bg-danger-lt'; break;
                                        default: $status_class = 'badge bg-secondary-lt'; break;
                                    }
                                    ?>
                                    <span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $employee['status']))); ?></span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/view/' . $employee['id']); ?>" class="btn btn-sm">View</a>
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/edit/' . $employee['id']); ?>" class="btn btn-sm">Edit</a>
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/employees/delete_employee/' . $employee['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Optional: Add pagination controls here if implementing pagination later -->
        <?php if (!empty($employees) && count($employees) > 10): // Example condition for pagination ?>
        <!--
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">Showing <span>1</span> to <span>10</span> of <span><?php echo count($employees); ?></span> entries</p>
            <ul class="pagination m-0 ms-auto">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">‹</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">›</a>
                </li>
            </ul>
        </div>
        -->
        <?php endif; ?>
    </div>
</div>
