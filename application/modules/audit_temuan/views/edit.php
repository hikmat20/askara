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
<div class="form-group mb-3">
  <label for="" class="h6 font-weight-bold">Auditee <span class="text-danger">*</span></label>
  <table id="tblAuditeeEdit" class="table table-bordered table-condensed table-sm mb-2">
    <thead>
      <tr>
        <th width="30" class="text-center">No</th>
        <th>Name</th>
        <th width="50" class="text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $dataAuditee = json_decode($data->auditee, true);
      if (!is_array($dataAuditee)) {
        $dataAuditee = $data->auditee ? [$data->auditee] : [];
      }
      $n = 1;
      foreach ($dataAuditee as $auditeeItem) : 
        $name = htmlspecialchars($auditeeItem);
        // Fallback for ID to name mapping if it was saved as ID
        if (is_numeric($auditeeItem)) {
            $c = $this->db->get_where('audit_auditor_consultant', ['id' => $auditeeItem])->row();
            $name = $c ? htmlspecialchars($c->name) : $name;
        }
      ?>
        <tr>
          <td class="text-center number"><?= $n++; ?></td>
          <td>
            <input type="text" name="auditee[]" class="form-control required" value="<?= $name; ?>" placeholder="Auditee Name">
          </td>
          <td class="text-center">
            <button type="button" class="btn btn-xs btn-danger btn-icon del-row-edit"><i class="fa fa-trash" aria-hidden="true"></i></button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <button type="button" class="btn btn-success btn-sm" id="add-auditee-edit"><i class="fa fa-plus" aria-hidden="true"></i> Add Auditee</button>
</div>
<div class="form-group mb-3">
  <label for="" class="h6 font-weight-bold">Auditor <span class="text-danger">*</span></label>
  <table id="tblAuditorEdit" class="table table-bordered table-condensed table-sm mb-2">
    <thead>
      <tr>
        <th width="30" class="text-center">No</th>
        <th>Name</th>
        <th width="50" class="text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $dataAuditor = json_decode($data->auditor, true);
      if (!is_array($dataAuditor)) {
        $dataAuditor = $data->auditor ? [$data->auditor] : [];
      }
      $n = 1;
      foreach ($dataAuditor as $auditorItem) : 
      ?>
        <tr>
          <td class="text-center number"><?= $n++; ?></td>
          <td>
            <select name="auditor[]" class="form-select select2-auditor-edit required" data-placeholder="Select Auditor">
              <option value=""></option>
              <?php if ($auditors) foreach ($auditors as $k => $v) : ?>
                <option value="<?= $v->id; ?>" <?= ($v->id == $auditorItem || $v->name == $auditorItem) ? 'selected' : ''; ?>><?= $v->name; ?></option>
              <?php endforeach; ?>
            </select>
          </td>
          <td class="text-center">
            <button type="button" class="btn btn-xs btn-danger btn-icon del-row-edit-auditor"><i class="fa fa-trash" aria-hidden="true"></i></button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <button type="button" class="btn btn-success btn-sm" id="add-auditor-edit"><i class="fa fa-plus" aria-hidden="true"></i> Add Auditor</button>
</div>
<div class="form-group d-none">
  <label for="" class="h6 font-weight-bold">Auditor Internal <span class="text-danger">*</span></label>
  <select name="auditor_internal" id="auditor_internal" data-allow-clear="true" class="form-select select2 " data-placeholder="Select Auditor">
    <option value=""></option>
    <?php if ($auditorInternal) foreach ($auditorInternal as $k => $v) : ?>
      <option value="<?= $v->id; ?>" <?= ($data->auditor_internal == $v->id) ? 'selected' : ''; ?>><?= $v->name; ?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="form-group d-none">
  <label for="" class="h6 font-weight-bold">Konsultan <span class="text-danger">*</span></label>
  <select name="consultant" id="consultant" data-allow-clear="true" class="form-select select2 " data-placeholder="Select Auditor">
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

    $('.select2-tags').select2({
     	dropdownParent: $('#modelId2 .modal-body'),
				width: "100%",
				closeOnSelect: true,
				tags: true,
				placeholder: "Input manual (pisahkan dengan enter)"
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

    let auditorsEdit = '<option value=""></option>';
    <?php if ($auditors) foreach ($auditors as $k => $v) : ?>
      auditorsEdit += `<option value="<?= $v->id; ?>"><?= $v->name; ?></option>`;
    <?php endforeach; ?>

		$('#tblAuditorEdit .select2-auditor-edit').select2({
			width: "100%",
			closeOnSelect: true,
			dropdownParent: $('#modelId2 .modal-body')
		}).on('change', updateAuditorOptionsEdit);

		updateAuditorOptionsEdit();

		$('#add-auditee-edit').off('click').on('click', function() {
			let n = $('#tblAuditeeEdit tbody tr').length + 1;
			let html = `
				<tr>
					<td class="text-center number">${n}</td>
					<td>
						<input type="text" name="auditee[]" class="form-control required" placeholder="Auditee Name">
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-xs btn-danger btn-icon del-row-edit"><i class="fa fa-trash" aria-hidden="true"></i></button>
					</td>
				</tr>`;
			$('#tblAuditeeEdit tbody').append(html);
			resetNumberingEdit('#tblAuditeeEdit');
		});

		$('#add-auditor-edit').off('click').on('click', function() {
			let n = $('#tblAuditorEdit tbody tr').length + 1;
			let html = `
				<tr>
					<td class="text-center number">${n}</td>
					<td>
						<select name="auditor[]" class="form-select select2-auditor-edit required" data-placeholder="Select Auditor">
							${auditorsEdit}
						</select>
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-xs btn-danger btn-icon del-row-edit-auditor"><i class="fa fa-trash" aria-hidden="true"></i></button>
					</td>
				</tr>`;
			$('#tblAuditorEdit tbody').append(html);
			$('#tblAuditorEdit tbody tr:last .select2-auditor-edit').select2({
				width: "100%",
				closeOnSelect: true,
				dropdownParent: $('#modelId2 .modal-body')
			}).on('change', updateAuditorOptionsEdit);
			resetNumberingEdit('#tblAuditorEdit');
			updateAuditorOptionsEdit();
		});

		$(document).off('click', '.del-row-edit-auditor').on('click', '.del-row-edit-auditor', function() {
			$(this).closest('tr').remove();
			resetNumberingEdit('#tblAuditorEdit');
			updateAuditorOptionsEdit();
		});

		$(document).off('click', '.del-row-edit').on('click', '.del-row-edit', function() {
			const tableId = '#' + $(this).closest('table').attr('id');
			$(this).closest('tr').remove();
			resetNumberingEdit(tableId);
		});

		function resetNumberingEdit(tableSelector) {
			$(tableSelector + ' tbody tr').each(function(index) {
				$(this).find('.number').text(index + 1);
			});
		}

		function updateAuditorOptionsEdit() {
			setTimeout(function() {
				let selectedVals = [];
				$('#tblAuditorEdit .select2-auditor-edit').each(function() {
					if($(this).val()) {
						selectedVals.push($(this).val());
					}
				});
				$('#tblAuditorEdit .select2-auditor-edit').each(function() {
					let currentVal = $(this).val();
					$(this).find('option').each(function() {
						if($(this).val() && selectedVals.includes($(this).val()) && $(this).val() != currentVal) {
							$(this).prop('disabled', true);
						} else {
							$(this).prop('disabled', false);
						}
					});
					$(this).select2('destroy').select2({
						width: "100%",
						closeOnSelect: true,
						dropdownParent: $('#modelId2 .modal-body')
					}).off('change').on('change', updateAuditorOptionsEdit);
				});
			}, 50);
		}
  })
</script>