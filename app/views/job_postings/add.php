<?php
// Loaded by JobPostingController@add or @store (on error)
// $title, $errors, $old_input, $statuses, $employment_types are passed.
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3"><h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Add New Job Posting'); ?></h2></div>
    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings/store'); ?>" method="POST">
            <div class="card-header"><h3 class="card-title">Job Posting Details</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required">Title</label>
                    <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" name="title" placeholder="e.g., Senior Software Engineer" value="<?php echo htmlspecialchars($old_input['title'] ?? ''); ?>" required>
                    <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['title']); ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Description</label>
                    <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" name="description" rows="6" placeholder="Full job description, responsibilities, qualifications..."><?php echo htmlspecialchars($old_input['description'] ?? ''); ?></textarea>
                    <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['description']); ?></div><?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" class="form-control <?php echo isset($errors['department_name']) ? 'is-invalid' : ''; ?>" name="department_name" placeholder="e.g., Engineering, Marketing" value="<?php echo htmlspecialchars($old_input['department_name'] ?? ''); ?>">
                        <?php if (isset($errors['department_name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['department_name']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control <?php echo isset($errors['location']) ? 'is-invalid' : ''; ?>" name="location" placeholder="e.g., New York Office, Remote" value="<?php echo htmlspecialchars($old_input['location'] ?? ''); ?>">
                        <?php if (isset($errors['location'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['location']); ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Employment Type</label>
                        <select name="employment_type" class="form-select <?php echo isset($errors['employment_type']) ? 'is-invalid' : ''; ?>" required>
                            <?php foreach($employment_types as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo (isset($old_input['employment_type']) && $old_input['employment_type'] == $type) ? 'selected' : ($type == 'full_time' && !isset($old_input['employment_type']) ? 'selected' : ''); ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $type)); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['employment_type'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['employment_type']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Salary Range (Optional)</label>
                        <input type="text" class="form-control <?php echo isset($errors['salary_range']) ? 'is-invalid' : ''; ?>" name="salary_range" placeholder="e.g., $80k - $120k" value="<?php echo htmlspecialchars($old_input['salary_range'] ?? ''); ?>">
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Posted Date (Optional)</label>
                        <input type="date" class="form-control <?php echo isset($errors['posted_date']) ? 'is-invalid' : ''; ?>" name="posted_date" value="<?php echo htmlspecialchars($old_input['posted_date'] ?? ''); ?>">
                        <small class="form-hint">Leave blank to set automatically if status is 'open'.</small>
                         <?php if (isset($errors['posted_date'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['posted_date']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Closing Date (Optional)</label>
                        <input type="date" class="form-control <?php echo isset($errors['closing_date']) ? 'is-invalid' : ''; ?>" name="closing_date" value="<?php echo htmlspecialchars($old_input['closing_date'] ?? ''); ?>">
                         <?php if (isset($errors['closing_date'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['closing_date']); ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Status</label>
                        <select name="status" class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>" required>
                            <?php foreach($statuses as $status_val): ?>
                            <option value="<?php echo $status_val; ?>" <?php echo (isset($old_input['status']) && $old_input['status'] == $status_val) ? 'selected' : ($status_val == 'draft' && !isset($old_input['status']) ? 'selected' : ''); ?>>
                                <?php echo ucfirst(str_replace('_', ' ', $status_val)); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['status']); ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/job_postings'); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Job Posting</button>
            </div>
        </form>
    </div>
</div>
