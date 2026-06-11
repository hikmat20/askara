<!-- Previous Audit Evaluation Section -->
<?php if (empty($temuan)) : ?>
	<div class="alert alert-info">
		<i class="fa fa-info-circle mr-2"></i> Tidak ada data audit sebelumnya yang tersedia untuk referensi.
	</div>
<?php else : ?>
	<div class="form-group row">
		<label class="col-md-3 col-form-label font-weight-bold">Audit Reference</label>
		<div class="col-md-9">
			<select id="select_temuan" class="form-control select2" data-placeholder="Pilih Audit Temuan">
				<option></option>
				<?php foreach ($temuan as $t) : ?>
					<option value="<?= $t->id; ?>"><?= $t->company_name . ' - ' . $t->badan_sertifikasi_name; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<!-- Temuan Details Container (read-only, loaded via AJAX) -->
	<div id="temuan-details" class="mb-3"></div>

	<div class="form-group row">
		<label class="col-md-3 col-form-label font-weight-bold">Improvement Action</label>
		<div class="col-md-9">
			<textarea name="eval_improvement" id="eval_improvement" class="form-control" rows="4" maxlength="2000" placeholder="Tindakan perbaikan (max 2000 karakter)"></textarea>
			<small class="text-muted"><span id="eval_improvement_count">0</span>/2000 karakter</small>
		</div>
	</div>

	<div class="form-group row">
		<div class="col-md-9 offset-md-3">
			<button type="button" id="btn-add-evaluation" class="btn btn-sm btn-primary"><i class="fa fa-plus mr-1"></i> Add</button>
			<span id="eval-max-info" class="text-muted ml-2" style="display:none;">Maksimal 50 entri telah tercapai.</span>
		</div>
	</div>
<?php endif; ?>

<!-- Evaluation Table -->
<div class="mt-3">
	<table id="table-evaluations" class="table table-sm table-bordered table-hover">
		<thead class="table-light">
			<tr class="text-center">
				<th width="40">No</th>
				<th>Audit Reference</th>
				<th>Weakness</th>
				<th>Improvement Action</th>
				<th width="100">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($evaluations)) : ?>
				<?php foreach ($evaluations as $k => $eval) : $k++; ?>
					<tr data-row-id="<?= $k; ?>">
						<td class="text-center eval-no"><?= $k; ?></td>
						<td><?= isset($eval->company_name) ? $eval->company_name . ' - ' . $eval->badan_sertifikasi_name : $eval->audit_temuan_id; ?></td>
						<td><?php
							// The weakness_description uses \n\n as separator between numbered items
							// Split on the pattern: newline(s) followed by a digit and dot at start of line
							$wd = $eval->weakness_description;
							// Split by double-newline or single-newline before numbered items
							$parts = preg_split('/\n+(?=\d+\.\s)/', $wd);
							$output = [];
							foreach ($parts as $part) {
								$part = trim($part);
								if ($part !== '') {
									$output[] = htmlspecialchars($part);
								}
							}
							echo implode('<br><br>', $output);
						?></td>
						<td><?= $eval->improvement_action; ?></td>
						<td class="text-center">
							<button type="button" class="btn btn-xs btn-icon btn-warning eval-edit" title="Edit"><i class="fa fa-edit"></i></button>
							<button type="button" class="btn btn-xs btn-icon btn-danger eval-delete" title="Delete"><i class="fa fa-trash"></i></button>
							<!-- Hidden inputs for form submission -->
							<input type="hidden" name="evaluations[<?= $k - 1; ?>][audit_temuan_id]" value="<?= $eval->audit_temuan_id; ?>">
							<input type="hidden" name="evaluations[<?= $k - 1; ?>][temuan_detail_id]" value="<?= isset($eval->temuan_detail_id) ? $eval->temuan_detail_id : ''; ?>">
							<input type="hidden" name="evaluations[<?= $k - 1; ?>][weakness_description]" value="<?= htmlspecialchars($eval->weakness_description); ?>">
							<input type="hidden" name="evaluations[<?= $k - 1; ?>][improvement_action]" value="<?= htmlspecialchars($eval->improvement_action); ?>">
							<input type="hidden" name="evaluations[<?= $k - 1; ?>][audit_reference_label]" value="<?= isset($eval->company_name) ? htmlspecialchars($eval->company_name . ' - ' . $eval->badan_sertifikasi_name) : $eval->audit_temuan_id; ?>">
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<script>
$(document).ready(function() {
	// Initialize Select2 for temuan dropdown
	$('#select_temuan').select2({
		allowClear: true,
		width: '100%'
	});

	// Character counter for improvement action textarea
	$('#eval_improvement').on('input', function() {
		$('#eval_improvement_count').text($(this).val().length);
	});

	// Load temuan details via AJAX on selection change
	$('#select_temuan').on('change', function() {
		var temuanId = $(this).val();
		if (temuanId) {
			$.ajax({
				url: siteurl + 'audit_preparation/get_temuan_details/' + temuanId,
				type: 'GET',
				beforeSend: function() {
					$('#temuan-details').html('<div class="text-center py-3"><i class="spinner-border spinner-border-sm"></i> Loading...</div>');
				},
				success: function(html) {
					$('#temuan-details').html(html);
				},
				error: function() {
					$('#temuan-details').html('<div class="alert alert-danger">Gagal memuat detail temuan.</div>');
				}
			});
		} else {
			$('#temuan-details').html('');
		}
	});

	// Add evaluation entry - combines checked temuan into one row with numbered weaknesses
	$(document).on('click', '#btn-add-evaluation', function() {
		var temuanId = $('#select_temuan').val();
		var temuanLabel = $('#select_temuan option:selected').text();
		var improvement = $('#eval_improvement').val();

		// Validation
		if (!temuanId) {
			Swal.fire({title: 'Warning!', icon: 'warning', text: 'Pilih Audit Reference terlebih dahulu.'});
			return;
		}

		// Get checked temuan items
		var checkedItems = $('#temuan-details .check-temuan-item:checked');
		if (checkedItems.length === 0) {
			Swal.fire({title: 'Warning!', icon: 'warning', text: 'Pilih minimal 1 temuan/weakness.'});
			return;
		}

		// Check max 50 entries
		var rowCount = $('#table-evaluations tbody tr').length;
		if (rowCount >= 50) {
			Swal.fire({title: 'Warning!', icon: 'warning', text: 'Maksimal 50 entri evaluasi telah tercapai.'});
			return;
		}

		// Collect all checked weaknesses and detail IDs
		var weaknessList = [];
		var detailIds = [];
		var counter = 1;
		checkedItems.each(function() {
			weaknessList.push(counter + '. ' + $(this).data('desc'));
			detailIds.push($(this).data('id'));
			counter++;
		});

		var weaknessDisplay = weaknessList.join('<br><br>');
		var weaknessValue = weaknessList.join('\n\n');
		var detailIdValue = detailIds.join(',');

		var idx = rowCount;
		var newRow = '<tr data-row-id="' + (idx + 1) + '">' +
			'<td class="text-center eval-no">' + (idx + 1) + '</td>' +
			'<td>' + escapeHtml(temuanLabel) + '</td>' +
			'<td>' + weaknessDisplay + '</td>' +
			'<td>' + escapeHtml(improvement) + '</td>' +
			'<td class="text-center">' +
				'<button type="button" class="btn btn-xs btn-icon btn-warning eval-edit" title="Edit"><i class="fa fa-edit"></i></button> ' +
				'<button type="button" class="btn btn-xs btn-icon btn-danger eval-delete" title="Delete"><i class="fa fa-trash"></i></button>' +
				'<input type="hidden" name="evaluations[' + idx + '][audit_temuan_id]" value="' + temuanId + '">' +
				'<input type="hidden" name="evaluations[' + idx + '][temuan_detail_id]" value="' + escapeHtml(String(detailIdValue)) + '">' +
				'<input type="hidden" name="evaluations[' + idx + '][weakness_description]" value="' + escapeHtml(weaknessValue) + '">' +
				'<input type="hidden" name="evaluations[' + idx + '][improvement_action]" value="' + escapeHtml(improvement) + '">' +
				'<input type="hidden" name="evaluations[' + idx + '][audit_reference_label]" value="' + escapeHtml(temuanLabel) + '">' +
			'</td>' +
		'</tr>';

		$('#table-evaluations tbody').append(newRow);

		// Reset form fields
		$('#select_temuan').val(null).trigger('change');
		$('#eval_improvement').val('');
		$('#eval_improvement_count').text('0');
		$('#temuan-details').html('');

		// Show max info if 50 reached
		if ($('#table-evaluations tbody tr').length >= 50) {
			$('#btn-add-evaluation').prop('disabled', true);
			$('#eval-max-info').show();
		}
	});

	// Edit evaluation entry
	$(document).on('click', '.eval-edit', function() {
		var row = $(this).closest('tr');
		var temuanId = row.find('input[name$="[audit_temuan_id]"]').val();
		var temuanLabel = row.find('input[name$="[audit_reference_label]"]').val();
		var improvement = row.find('input[name$="[improvement_action]"]').val();
		var detailIds = row.find('input[name$="[temuan_detail_id]"]').val();

		// Set values back to input fields
		$('#select_temuan').val(temuanId).trigger('change');
		$('#eval_improvement').val(improvement);
		$('#eval_improvement_count').text(improvement.length);

		// After temuan details load via AJAX, auto-check the saved detail IDs
		if (detailIds) {
			var savedIds = detailIds.split(',');
			// Wait for AJAX to complete, then check the boxes
			var checkInterval = setInterval(function() {
				if ($('#temuan-details .check-temuan-item').length > 0) {
					clearInterval(checkInterval);
					$('#temuan-details .check-temuan-item').each(function() {
						var itemId = String($(this).data('id'));
						for (var i = 0; i < savedIds.length; i++) {
							if (itemId === savedIds[i].trim()) {
								$(this).prop('checked', true);
								break;
							}
						}
					});
				}
			}, 300);
			// Timeout after 5 seconds
			setTimeout(function() { clearInterval(checkInterval); }, 5000);
		}

		// Remove the row being edited
		row.remove();
		reindexEvaluationTable();
	});

	// Delete evaluation entry
	$(document).on('click', '.eval-delete', function() {
		var row = $(this).closest('tr');
		Swal.fire({
			title: 'Hapus Evaluasi?',
			text: 'Apakah Anda yakin ingin menghapus entri ini?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Ya, hapus',
			cancelButtonText: 'Batal'
		}).then(function(result) {
			if (result.isConfirmed) {
				row.remove();
				reindexEvaluationTable();

				// Re-enable add button if below max
				if ($('#table-evaluations tbody tr').length < 50) {
					$('#btn-add-evaluation').prop('disabled', false);
					$('#eval-max-info').hide();
				}
			}
		});
	});

	// Reindex table rows after edit/delete
	function reindexEvaluationTable() {
		$('#table-evaluations tbody tr').each(function(index) {
			$(this).attr('data-row-id', index + 1);
			$(this).find('.eval-no').text(index + 1);
			$(this).find('input[name$="[audit_temuan_id]"]').attr('name', 'evaluations[' + index + '][audit_temuan_id]');
			$(this).find('input[name$="[temuan_detail_id]"]').attr('name', 'evaluations[' + index + '][temuan_detail_id]');
			$(this).find('input[name$="[weakness_description]"]').attr('name', 'evaluations[' + index + '][weakness_description]');
			$(this).find('input[name$="[improvement_action]"]').attr('name', 'evaluations[' + index + '][improvement_action]');
			$(this).find('input[name$="[audit_reference_label]"]').attr('name', 'evaluations[' + index + '][audit_reference_label]');
		});
	}

	// HTML escape utility
	function escapeHtml(text) {
		if (!text) return '';
		var map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
		return text.replace(/[&<>"']/g, function(m) { return map[m]; });
	}

	// Disable add button if already at 50 on page load
	if ($('#table-evaluations tbody tr').length >= 50) {
		$('#btn-add-evaluation').prop('disabled', true);
		$('#eval-max-info').show();
	}
});
</script>
