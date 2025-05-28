<?php
// Loaded by JobPostingController@index
// $postings, $title, $current_filter, $all_statuses are passed.
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Manage Job Postings'); ?></h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/add'); ?>" class="btn btn-primary">
                    Add New Job Posting
                </a>
            </div>
        </div>
    </div>

    <div class="d-flex mb-3">
        <ul class="nav nav-tabs border-0">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_filter === 'all') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/index?status=all'); ?>">
                    All
                </a>
            </li>
            <?php foreach ($all_statuses as $status_val): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_filter === $status_val) ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/index?status=' . $status_val); ?>">
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
                        <th>Title</th>
                        <th>Department</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Posted</th>
                        <th>Closing</th>
                        <th>Created By</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($postings)): ?>
                        <tr><td colspan="10" class="text-center">No job postings found for this filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($postings as $posting): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($posting['id']); ?></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/view/' . $posting['id']); ?>">
                                        <?php echo htmlspecialchars($posting['title']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($posting['department_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($posting['location'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $posting['employment_type']))); ?></td>
                                <td>
                                    <span class="badge bg-secondary-lt"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $posting['status']))); ?></span>
                                </td>
                                <td><?php echo $posting['posted_date'] ? htmlspecialchars(date('M j, Y', strtotime($posting['posted_date']))) : 'N/A'; ?></td>
                                <td><?php echo $posting['closing_date'] ? htmlspecialchars(date('M j, Y', strtotime($posting['closing_date']))) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($posting['created_by_employee_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/edit/' . $posting['id']); ?>" class="btn btn-sm">Edit</a>
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/delete_posting/' . $posting['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This will also delete all applications for this job if ON DELETE CASCADE is set.');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- TODO: Add pagination if $postingsModel->getAll() supports it and passes total count -->
    </div>
</div>
