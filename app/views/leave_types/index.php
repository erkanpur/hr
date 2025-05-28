<?php
// Loaded by LeaveTypeController@index
// $leave_types (array of leave type data) and $title are passed via extract().
?>

<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? 'Manage Leave Types'); ?>
                </h2>
                <div class="text-muted mt-1"><?php echo count($leave_types); ?> leave type(s) found.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_types/add'); ?>" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        Add New Leave Type
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
                        <th>Name</th>
                        <th>Days Allocated Annually</th>
                        <th>Is Paid?</th>
                        <th>Is Active?</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leave_types)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No leave types found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leave_types as $type): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($type['id']); ?></td>
                                <td><?php echo htmlspecialchars($type['name']); ?>
                                    <?php if (!empty($type['description'])): ?>
                                        <small class="d-block text-muted"><?php echo nl2br(htmlspecialchars($type['description'])); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo ($type['days_allocated_annually'] !== null) ? htmlspecialchars($type['days_allocated_annually']) : 'N/A'; ?></td>
                                <td>
                                    <span class="badge <?php echo $type['is_paid'] ? 'bg-success-lt' : 'bg-secondary-lt'; ?>">
                                        <?php echo $type['is_paid'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $type['is_active'] ? 'bg-success-lt' : 'bg-secondary-lt'; ?>">
                                        <?php echo $type['is_active'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_types/edit/' . $type['id']); ?>" class="btn btn-sm">Edit</a>
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/leave_types/delete_type/' . $type['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this leave type? This may affect existing leave requests if not handled by database constraints.');">Delete</a>
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
