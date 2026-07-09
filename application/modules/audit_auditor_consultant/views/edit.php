<form id="form">
  <input type="hidden" name="id" value="<?= $data->id; ?>">
  <div class="form-group">
    <label class="h6 font-weight-bold mb-5">Name <span class="text-danger">*</span></label>
    <input type="name" name="name" value="<?= $data->name; ?>" placeholder="Auditor Name" class="form-control form-control-lg required" aria-describedby="helpId">
  </div>
  <div class="form-group">
    <label class="h6 font-weight-bold mb-5">Position <span class="text-danger">*</span></label>
    <select name="position[]" class="form-control select2-position required" multiple data-placeholder="Select Position" data-allow-clear="true" id="position">
      
      <option value="1" <?= ((isset(json_decode($data->position)[0]) && json_decode($data->position)[0]) == '1') ? 'selected' : ''; ?>>Auditor</option>
      <option value="2" <?= ((isset(json_decode($data->position)[1]) && json_decode($data->position)[1]) == '2') ? 'selected' : ''; ?>>Consultant</option>
    </select>
  </div>
  <div class="form-group">
    <label class="h6 font-weight-bold mb-5">Email <span class="text-danger">*</span></label>
    <input type="email" name="email" value="<?= isset($data->email) ? $data->email : ''; ?>" placeholder="Email" class="form-control form-control-lg required" aria-describedby="helpId">
  </div>
  <div class="form-group">
    <label class="h6 font-weight-bold mb-5">Description</label>
    <textarea name="description" placeholder="Description" rows="5" class="form-control  form-control-lg" aria-describedby="helpId"><?= $data->description; ?></textarea>
  </div>
</form>

<script>
  $(document).ready(function() {
    $('.select2-position').select2({
      width: '100%'
    })
  })
</script>