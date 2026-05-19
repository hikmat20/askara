<input type="hidden" name="id" value="<?= $data->id; ?>">
<div class="form-group">
  <label for="" class="h6 font-weight-bold">Audit Date <span class="text-danger">*</span></label>
  <input type="date" name="date" class="form-control required" aria-describedby="helpId" value="<?= $data->date; ?>">
</div>
<div class="form-group">
  <label for="" class="h6 font-weight-bold">Pasal <span class="text-danger">*</span></label>
  <select name="pasal_id[]" data-allow-clear="true" multiple="multiple" data-placeholder="Select Pasal" class="form-select required select2">

    <?php $dataPasal = json_decode($data->pasal_id); ?>
    <?php if ($pasals) foreach ($pasals as $k => $v) : ?>
      <option value="<?= $v->id; ?>" <?= (in_array($v->id, $dataPasal)) ? 'selected' : ''; ?>><?= $v->chapter; ?></option>
    <?php endforeach; ?>
  </select>
  <span class="invalid-feedback">Pilih pasal terlebih dahulu!</span>
</div>
<div class="form-group">
  <label for="" class="h6 font-weight-bold">Temuan <span class="text-danger">*</span></label>
  <textarea name="description" id="description" val class="form-control summernote required" rows="5" placeholder="Deskripsi Temuan">
    <?= $data->description; ?>
  </textarea>
  <span class="invalid-feedback">Deskripsi temuan tidak boleh kosong!</span>
</div>
<div class="form-group">
  <label for="" class="h6 font-weight-bold">Kategori <span class="text-danger">*</span></label>
  <select name="category" id="category" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Kategori">
    <option value=""></option>
    <option value="1" <?= ($data->category == '1') ? 'selected' : ''; ?>>Minor</option>
    <option value="2" <?= ($data->category == '2') ? 'selected' : ''; ?>>Major</option>
    <option value="3" <?= ($data->category == '3') ? 'selected' : ''; ?>>OFI</option>
  </select>
</div>
<div class="form-group">
  <label for="" class="h6 font-weight-bold">Proses <span class="text-danger">*</span></label>
  <select name="process" id="process" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Proses">
    <option value=""></option>
    <?php if ($process) foreach ($process as $k => $v) : ?>
      <option value="<?= $v->id; ?>" <?= ($v->id == $data->process) ? 'selected' : ''; ?>><?= $v->process_name; ?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="form-group">
  <label for="" class="h6 font-weight-bold">Auditee <span class="text-danger">*</span></label>
  <input type="text" name="auditee" id="auditee" class="form-control required" placeholder="Auditee" value="<?= $data->auditee; ?>">
</div>
<div class="form-group">
  <label for="" class="h6 font-weight-bold">Auditor <span class="text-danger">*</span></label>
  <select name="auditor" id="auditor" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Auditor">
    <option></option>
    <?php if ($auditors) foreach ($auditors as $k => $v) : ?>
      <option value="<?= $v->id; ?>" <?= ($data->auditor == $v->id) ? 'selected' : ''; ?>><?= $v->name; ?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="form-group">
  <label for="" class="h6 font-weight-bold">Auditor Internal <span class="text-danger">*</span></label>
  <select name="auditor_internal" id="auditor_internal" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Auditor">
    <option value=""></option>
    <?php if ($auditorInternal) foreach ($auditorInternal as $k => $v) : ?>
      <option value="<?= $v->id; ?>" <?= ($data->auditor_internal == $v->id) ? 'selected' : ''; ?>><?= $v->name; ?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="form-group">
  <label for="" class="h6 font-weight-bold">Konsultan <span class="text-danger">*</span></label>
  <select name="consultant" id="consultant" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Auditor">
    <option value=""></option>
    <?php if ($consultant) foreach ($consultant as $k => $v) : ?>
      <option value="<?= $v->id; ?>" <?= ($data->consultant == $v->id) ? 'selected' : ''; ?>><?= $v->name; ?></option>
    <?php endforeach; ?>
  </select>
</div>

<script>
  $(document).ready(() => {
    $('.select2').select2({
     	dropdownParent: $('#modelId2 .modal-body'),
				width: "100%",
				closeOnSelect: true
    })

//    $('#modelId .select2').select2({
//				dropdownParent: $('#modelId .modal-body'),
//				width: "100%",
//				closeOnSelect: true
//			});
//			$('#modelId textarea.summernote').summernote({
//				dialogsInBody: true,
//				height: 150
//			});

    $('textarea.summernote').summernote({
      inheritePlacholder: true,
      dialogsInBody: true,
      height: 150
      // airMode: true
    })
  })
</script>