<?php
// Loaded by ReviewPeriodController@index
// $periods, $title, $current_filter are passed.
$statuses = ['all', 'setup', 'open_for_input', 'in_review', 'closed', 'archived'];
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Manage Review Periods'); ?></h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_periods/add'); ?>" class="btn btn-primary">
                    Add New Review Period
                </a>
            </div>
        </div>
    </div>

    <div class="d-flex mb-3">
        <ul class="nav nav-tabs border-0">
            <?php foreach ($statuses as $status_val): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_filter === $status_val) ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_periods/index?status=' . $status_val); ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $status_val)); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($periods)): ?>
                        <tr><td colspan="6" class="text-center">No review periods found for this filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($periods as $period): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($period['id']); ?></td>
                                <td><?php echo htmlspecialchars($period['name']); ?></td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($period['start_date']))); ?></td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($period['end_date']))); ?></td>
                                <td>
                                    <span class="badge bg-secondary-lt"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $period['status']))); ?></span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_periods/edit/' . $period['id']); ?>" class="btn btn-sm">Edit</a>
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_periods/delete_period/' . $period['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? Deleting a period might affect associated reviews if not handled by DB constraints.');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
