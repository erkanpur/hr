<?php
// Loaded by JobPostingController@view
// $posting, $title are passed.
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Job Posting Details'); ?></h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                 <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/edit/' . $posting['id']); ?>" class="btn btn-primary me-2">Edit Posting</a>
                 <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings'); ?>" class="btn btn-outline-secondary">Back to List</a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?php echo htmlspecialchars($posting['title']); ?></h3>
        </div>
        <div class="card-body">
            <p><strong>Department:</strong> <?php echo htmlspecialchars($posting['department_name'] ?? 'N/A'); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($posting['location'] ?? 'N/A'); ?></p>
            <p><strong>Employment Type:</strong> <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $posting['employment_type']))); ?></p>
            <p><strong>Salary Range:</strong> <?php echo htmlspecialchars($posting['salary_range'] ?? 'N/A'); ?></p>
            <p><strong>Status:</strong> <span class="badge bg-secondary-lt"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $posting['status']))); ?></span></p>
            <p><strong>Posted Date:</strong> <?php echo $posting['posted_date'] ? date('M j, Y', strtotime($posting['posted_date'])) : 'N/A'; ?></p>
            <p><strong>Closing Date:</strong> <?php echo $posting['closing_date'] ? date('M j, Y', strtotime($posting['closing_date'])) : 'N/A'; ?></p>
            <p><strong>Created By:</strong> <?php echo htmlspecialchars($posting['created_by_employee_name'] ?? 'N/A'); ?> on <?php echo date('M j, Y', strtotime($posting['created_at'])); ?></p>
            <hr>
            <h4>Job Description</h4>
            <div><?php echo nl2br(htmlspecialchars($posting['description'])); // Consider a more robust HTML sanitizer if allowing HTML ?></div>
        </div>
    </div>
    <!-- Future: Section to list applications for this job -->
</div>
