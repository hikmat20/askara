<div class="mb-3 row flex-nowrap">
  <label for="company" class="col-3 col-form-label font-weight-bold">Company <span class="text-danger">*</span></label>
  <div class="col-9">
    <input type="text" name="company" id="company" class="form-control required" maxlength="255" placeholder="Enter company name" value="<?= isset($program->company) ? $program->company : ''; ?>">
    <span class="invalid-feedback">Company can't be empty</span>
  </div>
</div>

<div class="mb-3 row flex-nowrap">
  <label for="lead_auditor_id" class="col-3 col-form-label font-weight-bold">Lead Auditor <span class="text-danger">*</span></label>
  <div class="col-9">
    <select name="lead_auditor_id" id="lead_auditor_id" class="form-control select2 required" data-placeholder="Select Lead Auditor">
      <option></option>
      <?php if ($auditors) foreach ($auditors as $k => $v) : ?>
        <option value="<?= $v->id; ?>" <?= (isset($program->lead_auditor_id) && $program->lead_auditor_id == $v->id) ? 'selected' : ''; ?>><?= $v->name; ?></option>
      <?php endforeach; ?>
    </select>
    <span class="invalid-feedback">Lead Auditor can't be empty</span>
  </div>
</div>

<div class="mb-3 row flex-nowrap">
  <label for="audit_scope" class="col-3 col-form-label font-weight-bold">Audit Scope <span class="text-danger">*</span></label>
  <div class="col-9">
    <select name="audit_scope" id="audit_scope" class="form-control required">
      <option value="">-- Select Audit Scope --</option>
      <option value="Audit Khusus" <?= (isset($program->audit_scope) && $program->audit_scope == 'Audit Khusus') ? 'selected' : ''; ?>>Audit Khusus</option>
      <option value="Audit Regular" <?= (isset($program->audit_scope) && $program->audit_scope == 'Audit Regular') ? 'selected' : ''; ?>>Audit Regular</option>
      <option value="Audit Product" <?= (isset($program->audit_scope) && $program->audit_scope == 'Audit Product') ? 'selected' : ''; ?>>Audit Product</option>
      <option value="Audit Process" <?= (isset($program->audit_scope) && $program->audit_scope == 'Audit Process') ? 'selected' : ''; ?>>Audit Process</option>
    </select>
    <span class="invalid-feedback">Audit Scope can't be empty</span>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#lead_auditor_id').select2({
      allowClear: true,
      width: "100%"
    });
  });
</script>
