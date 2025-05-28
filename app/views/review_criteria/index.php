<?php
// Loaded by ReviewCriteriaController@index
// $all_criteria (array of criteria data) and $title are passed.
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Manage Review Criteria'); ?></h2>
                <div class="text-muted mt-1"><?php echo count($all_criteria); ?> criteria found.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_criteria/add'); ?>" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    Add New Criterion
                </a>
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
                        <th>Description</th>
                        <th>Is Active?</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_criteria)): ?>
                        <tr><td colspan="5" class="text-center">No review criteria defined yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($all_criteria as $criterion): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($criterion['id']); ?></td>
                                <td><?php echo htmlspecialchars($criterion['name']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($criterion['description'] ?? 'N/A')); ?></td>
                                <td>
                                    <span class="badge <?php echo $criterion['is_active'] ? 'bg-success-lt' : 'bg-secondary-lt'; ?>">
                                        <?php echo $criterion['is_active'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_criteria/edit/' . $criterion['id']); ?>" class="btn btn-sm">Edit</a>
                                        <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_criteria/delete_criteria/' . $criterion['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this criterion?');">Delete</a>
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
