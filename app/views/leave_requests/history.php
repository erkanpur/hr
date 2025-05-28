<?php
// Loaded by LeaveRequestController@history
// $leave_requests (array of employee's leave request data) and $title are passed via extract().
?>

<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? 'My Leave History'); ?>
                </h2>
                <?php if (isset($leave_requests)): ?>
                    <div class="text-muted mt-1"><?php echo count($leave_requests); ?> request(s) found.</div>
                <?php endif; ?>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/apply'); ?>" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        Apply for New Leave
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
                        <th>Request ID</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days Requested</th>
                        <th>Status</th>
                        <th>Requested On</th>
                        <!-- <th class="w-1">Actions</th> --> <!-- Placeholder for future actions like view details/cancel -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leave_requests)): ?>
                        <tr>
                            <td colspan="7" class="text-center">You have not made any leave requests yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leave_requests as $request): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($request['id']); ?></td>
                                <td><?php echo htmlspecialchars($request['leave_type_name']); // Joined in model ?></td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($request['start_date']))); ?></td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($request['end_date']))); ?></td>
                                <td><?php echo htmlspecialchars(rtrim(rtrim(number_format($request['days_requested'], 1), '0'), '.')); ?> day(s)</td>
                                <td>
                                    <?php 
                                    $status_class = 'badge ';
                                    switch (strtolower($request['status'])) {
                                        case 'approved': $status_class .= 'bg-success-lt'; break;
                                        case 'rejected': $status_class .= 'bg-danger-lt'; break;
                                        case 'cancelled': $status_class .= 'bg-secondary-lt'; break;
                                        case 'pending': default: $status_class .= 'bg-warning-lt'; break;
                                    }
                                    ?>
                                    <span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars(ucfirst($request['status'])); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars(date('M j, Y, g:i a', strtotime($request['requested_at']))); ?></td>
                                <!-- <td> -->
                                    <!-- Future: View/Cancel buttons -->
                                    <!-- <a href="#" class="btn btn-sm">View</a> -->
                                <!-- </td> -->
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Optional: Add pagination controls here if implementing pagination later -->
    </div>
</div>
