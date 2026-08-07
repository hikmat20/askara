<form id="form">
  <input type="hidden" name="id" value="<?= $data->id; ?>">
  <div class="form-group">
    <label for="" class="h6 font-weight-bold mb-5">Process Name <span class="text-danger">*</span></label>
    <input type="process_name" name="process_name" placeholder="Process Name" value="<?= $data->process_name; ?>" class="form-control form-control-lg required" aria-describedby="helpId">
  </div>
  <div class="form-group">
    <label for="" class="h6 font-weight-bold mb-5">Remarks</label>
    <textarea name="description" placeholder="Remarks" class="form-control form-control-lg" aria-describedby="helpId"><?= $data->description; ?></textarea>
  </div>
</form>