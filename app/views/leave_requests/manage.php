<?php
// Loaded by LeaveRequestController@manage_requests
// $leave_requests, $title, $current_filter are passed via extract().
?>

<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? 'Manage Leave Requests'); ?>
                </h2>
                <?php if (isset($leave_requests)): ?>
                    <div class="text-muted mt-1"><?php echo count($leave_requests); ?> request(s) found for the current filter.</div>
                <?php endif; ?>
            </div>
            <!-- Optional: Add button for bulk actions or other controls if needed later -->
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="d-flex mb-3">
        <ul class="nav nav-tabs border-0">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_filter === 'all') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/manage?status=all'); ?>">
                    All Requests
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_filter === 'pending') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/manage?status=pending'); ?>">
                    Pending <span class="badge bg-yellow-lt ms-1"><?php /* echo $pending_count; */ ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_filter === 'approved') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/manage?status=approved'); ?>">
                    Approved
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_filter === 'rejected') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/manage?status=rejected'); ?>">
                    Rejected
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_filter === 'cancelled') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/manage?status=cancelled'); ?>">
                    Cancelled
                </a>
            </li>
        </ul>
    </div>


    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th>Req. ID</th>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Dates (Start - End)</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Requested On</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leave_requests)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No leave requests found for this filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leave_requests as $request): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($request['id']); ?></td>
                                <td><?php echo htmlspecialchars($request['employee_first_name'] . ' ' . $request['employee_last_name']); ?> (ID: <?php echo htmlspecialchars($request['employee_id']); ?>)</td>
                                <td><?php echo htmlspecialchars($request['leave_type_name']); ?></td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($request['start_date']))); ?> - <?php echo htmlspecialchars(date('M j, Y', strtotime($request['end_date']))); ?></td>
                                <td><?php echo htmlspecialchars(rtrim(rtrim(number_format($request['days_requested'], 1), '0'), '.')); ?></td>
                                <td>
                                    <?php if (!empty($request['reason'])): ?>
                                    <span title="<?php echo htmlspecialchars($request['reason']); ?>" data-bs-toggle="tooltip">
                                        <?php echo htmlspecialchars(mb_strimwidth($request['reason'], 0, 30, "...")); ?>
                                    </span>
                                    <?php else: echo 'N/A'; endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $status_class = 'badge ';
                                    switch (strtolower($request['status'])) {
                                        case 'approved': $status_class .= 'bg-success-lt'; break;
                                        case 'rejected': $status_class .= 'bg-danger-lt'; break;
                                        case 'cancelled': $status_class .= 'bg-secondary-lt'; break;
                                        case 'pending': default: $status_class .= 'bg-yellow-lt'; break; // Changed pending to yellow
                                    }
                                    ?>
                                    <span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars(ucfirst($request['status'])); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars(date('M j, Y, g:i a', strtotime($request['requested_at']))); ?></td>
                                <td>
                                    <?php if (strtolower($request['status']) === 'pending'): ?>
                                        <div class="btn-list flex-nowrap">
                                            <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/approve_request/' . $request['id']); ?>" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to APPROVE this leave request?');">Approve</a>
                                            <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_requests/reject_request/' . $request['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to REJECT this leave request?');">Reject</a>
                                            <!-- Future: View Details button -->
                                        </div>
                                    <?php else: ?>
                                        <!-- Optionally show who actioned it and when, and comments -->
                                        <?php if (!empty($request['action_taken_by_user_id'])): ?>
                                            <small class="text-muted d-block">
                                                Action by ID: <?php echo htmlspecialchars($request['action_taken_by_user_id']); ?>
                                                <?php if (!empty($request['action_taken_at'])): ?>
                                                    on <?php echo htmlspecialchars(date('M j, Y', strtotime($request['action_taken_at']))); ?>
                                                <?php endif; ?>
                                            </small>
                                        <?php endif; ?>
                                        <?php if (!empty($request['comments_by_approver'])): ?>
                                            <small class="text-muted d-block" title="<?php echo htmlspecialchars($request['comments_by_approver']); ?>" data-bs-toggle="tooltip">
                                                Comment: <?php echo htmlspecialchars(mb_strimwidth($request['comments_by_approver'], 0, 30, "...")); ?>
                                            </small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Optional: Add pagination controls here -->
    </div>
</div>

<!-- Initialize tooltips -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
})
</script>
