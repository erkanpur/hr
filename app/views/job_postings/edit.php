<?php
// Loaded by JobPostingController@edit or @update_posting (on error)
// $posting, $old_input, $errors, $title, $statuses, $employment_types are passed.
$posting_id = $posting['id'] ?? null;
function getPostEditVal($field, $old, $orig) { return htmlspecialchars($old[$field] ?? $orig[$field] ?? ''); }
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3"><h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Edit Job Posting'); ?></h2></div>
    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/update_posting/' . $posting_id); ?>" method="POST">
            <div class="card-header"><h3 class="card-title">Job Posting Details</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required">Title</label>
                    <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" name="title" value="<?php echo getPostEditVal('title', $old_input, $posting); ?>" required>
                    <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['title']); ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Description</label>
                    <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" name="description" rows="6"><?php echo getPostEditVal('description', $old_input, $posting); ?></textarea>
                    <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['description']); ?></div><?php endif; ?>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" class="form-control <?php echo isset($errors['department_name']) ? 'is-invalid' : ''; ?>" name="department_name" value="<?php echo getPostEditVal('department_name', $old_input, $posting); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control <?php echo isset($errors['location']) ? 'is-invalid' : ''; ?>" name="location" value="<?php echo getPostEditVal('location', $old_input, $posting); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Employment Type</label>
                        <select name="employment_type" class="form-select <?php echo isset($errors['employment_type']) ? 'is-invalid' : ''; ?>" required>
                            <?php foreach($employment_types as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo (getPostEditVal('employment_type', $old_input, $posting) == $type) ? 'selected' : ''; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $type)); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                         <?php if (isset($errors['employment_type'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['employment_type']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Salary Range (Optional)</label>
                        <input type="text" class="form-control" name="salary_range" value="<?php echo getPostEditVal('salary_range', $old_input, $posting); ?>">
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Posted Date (Optional)</label>
                        <input type="date" class="form-control <?php echo isset($errors['posted_date']) ? 'is-invalid' : ''; ?>" name="posted_date" value="<?php echo getPostEditVal('posted_date', $old_input, $posting); ?>">
                        <?php if (isset($errors['posted_date'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['posted_date']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Closing Date (Optional)</label>
                        <input type="date" class="form-control <?php echo isset($errors['closing_date']) ? 'is-invalid' : ''; ?>" name="closing_date" value="<?php echo getPostEditVal('closing_date', $old_input, $posting); ?>">
                         <?php if (isset($errors['closing_date'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['closing_date']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Status</label>
                        <select name="status" class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>" required>
                            <?php foreach($statuses as $status_val): ?>
                            <option value="<?php echo $status_val; ?>" <?php echo (getPostEditVal('status', $old_input, $posting) == $status_val) ? 'selected' : ''; ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $status_val)); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                         <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['status']); ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/view/' . $posting_id); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
