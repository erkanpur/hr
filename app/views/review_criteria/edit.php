<?php
// Loaded by ReviewCriteriaController@edit or @update_criteria (on error)
// $criterion (original data), $old_input, $errors, $title are passed.
$criterion_id = $criterion['id'] ?? null;
function getCritEditVal($field, $old, $orig) { return htmlspecialchars($old[$field] ?? $orig[$field] ?? ''); }
function isCritEditChecked($field, $old, $orig) {
    if (isset($old[$field])) return $old[$field] == '1';
    if (isset($orig[$field])) return (bool)$orig[$field];
    return true; // Default to active for new if somehow not set
}
?>
<div class="container-xl">
    <div class="page-header d-print-none mb-3">
        <h2 class="page-title"><?php echo htmlspecialchars($title ?? 'Edit Review Criterion'); ?></h2>
    </div>
    <div class="card">
        <form action="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_criteria/update_criteria/' . $criterion_id); ?>" method="POST">
            <div class="card-header"><h3 class="card-title">Criterion Details</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required">Name</label>
                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" name="name" value="<?php echo getCritEditVal('name', $old_input, $criterion); ?>" required>
                    <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" name="description" rows="4"><?php echo getCritEditVal('description', $old_input, $criterion); ?></textarea>
                    <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['description']); ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?php echo isCritEditChecked('is_active', $old_input, $criterion) ? 'checked' : ''; ?>>
                        <label class="form-check-label">Is this criterion active?</label>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo htmlspecialchars(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/review_criteria'); ?>" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
