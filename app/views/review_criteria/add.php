<?php
// Loaded by ReviewCriteriaController@add or @store (on error)
// $title, $errors (optional), $old_input (optional) are passed.
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Add New Review Criterion'); ?></h2>
    </div>
    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_criteria/store'); ?>" method="POST">
            <div class="card-header"><h3 class="card-title">Criterion Details</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required">Name</label>
                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" name="name" placeholder="Enter criterion name (e.g., Technical Skills)" value="<?php echo htmlspecialchars($old_input['name'] ?? ''); ?>" required>
                    <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" name="description" rows="4" placeholder="Detailed explanation of what this criterion measures."><?php echo htmlspecialchars($old_input['description'] ?? ''); ?></textarea>
                    <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['description']); ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?php echo (isset($old_input['is_active']) && $old_input['is_active'] == '1' || !isset($old_input['is_active']) && !$_POST) ? 'checked' : ''; ?>>
                        <label class="form-check-label">Is this criterion active?</label>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_criteria'); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Criterion</button>
            </div>
        </form>
    </div>
</div>
