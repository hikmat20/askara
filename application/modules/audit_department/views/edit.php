<form id="form">
  <input type="hidden" name="id" value="<?= $data->id; ?>">
  <div class="form-group">
    <label class="h6 font-weight-bold mb-5">Department Name <span class="text-danger">*</span></label>
    <input type="text" name="department_name" value="<?= $data->department_name; ?>" placeholder="Masukkan nama department" class="form-control form-control-lg required" maxlength="100" aria-describedby="helpId">
    <small class="form-text text-muted">Maks. 100 karakter.</small>
  </div>
  <div class="form-group">
    <label class="h6 font-weight-bold mb-5">Status <span class="text-danger">*</span></label>
    <select name="status" class="form-control form-control-lg required">
      <option value="">Pilih status</option>
      <option value="1" <?= ($data->status == '1') ? 'selected' : ''; ?>>Active</option>
      <option value="2" <?= ($data->status == '2') ? 'selected' : ''; ?>>Inactive</option>
    </select>
  </div>
</form>
