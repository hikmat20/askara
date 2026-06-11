<div class="opportunity-section">
	<!-- v2.1 - fixed duplicate loop -->
	<h5 class="font-weight-bold mb-4">Isu Proses</h5>

	<!-- Input Form -->
	<div class="row mb-3">
		<div class="col-md-4">
			<label class="font-weight-bold">Issue <span class="text-danger">*</span></label>
			<input type="text" id="issue_text" class="form-control" placeholder="Input issue" maxlength="500">
		</div>
		<div class="col-md-4">
			<label class="font-weight-bold">Proses / Prosedur <span class="text-danger">*</span></label>
			<select id="select_procedure_multi" class="form-control select2" multiple="multiple" data-placeholder="Select Proses/Prosedur">
				<?php if (!empty($procedures)) foreach ($procedures as $proc) : ?>
					<option value="<?= $proc->id; ?>" data-name="<?= htmlspecialchars(strip_tags($proc->name)); ?>"><?= strip_tags($proc->name); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="col-md-4">
			<label class="font-weight-bold">Investigasi</label>
			<div id="investigasi-inputs"><small class="text-muted">Pilih proses terlebih dahulu</small></div>
		</div>
	</div>
	<div class="mb-3">
		<button type="button" id="btn-add-opportunity" class="btn btn-primary btn-sm">
			<i class="fa fa-plus mr-1"></i> Add Item
		</button>
	</div>

	<!-- Table: 1 issue = 1 row, with processes & investigations listed inside -->
	<div class="table-responsive">
		<table id="table-opportunities" class="table table-bordered table-sm table-hover">
			<thead class="table-light">
				<tr class="text-center">
					<th width="40">No</th>
					<th>Issue</th>
					<th>Proses / Prosedur</th>
					<th>Investigasi</th>
					<th width="80">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($opportunities)) : ?>
					<?php
					// Group by issue text (description field)
					$grouped = [];
					foreach ($opportunities as $opp) {
						$key = $opp->description;
						if (!isset($grouped[$key])) {
							$grouped[$key] = ['issue' => $key, 'items' => []];
						}
						$grouped[$key]['items'][] = $opp;
					}
					$rowNum = 0;
					foreach ($grouped as $group) :
						$rowNum++;
						$items = $group['items'];
						$rowspan = count($items);
						$hiddenInputs = '';
						foreach ($items as $opp) {
							$pName = isset($opp->procedure_name) ? strip_tags($opp->procedure_name) : '';
							$inv = isset($opp->investigation) ? $opp->investigation : '';
							$oppId = isset($opp->id) ? $opp->id : '';
							$hiddenInputs .= '<input type="hidden" name="opp_id[]" value="' . $oppId . '">';
							$hiddenInputs .= '<input type="hidden" name="opp_issue_text[]" value="' . htmlspecialchars($group['issue']) . '">';
							$hiddenInputs .= '<input type="hidden" name="opp_procedure_id[]" value="' . $opp->procedure_id . '">';
							$hiddenInputs .= '<input type="hidden" name="opp_procedure_name[]" value="' . htmlspecialchars($pName) . '">';
							$hiddenInputs .= '<input type="hidden" name="opp_investigation[]" value="' . htmlspecialchars($inv) . '">';
						}
						foreach ($items as $idx => $opp) :
							$pName = isset($opp->procedure_name) ? strip_tags($opp->procedure_name) : '';
							$inv = isset($opp->investigation) ? $opp->investigation : '';
							if ($idx === 0) :
					?>
					<tr class="opp-group-row">
						<td class="text-center align-top row-number" rowspan="<?= $rowspan; ?>"><?= $rowNum; ?></td>
						<td class="align-top" rowspan="<?= $rowspan; ?>"><?= htmlspecialchars($group['issue']); ?></td>
						<td><?= htmlspecialchars($pName); ?></td>
						<td><?= htmlspecialchars($inv); ?></td>
						<td class="text-center align-top" rowspan="<?= $rowspan; ?>">
							<button type="button" class="btn btn-xs btn-icon btn-warning edit-opportunity" title="Edit"><i class="fa fa-edit"></i></button>
							<button type="button" class="btn btn-xs btn-icon btn-danger delete-opportunity" title="Delete"><i class="fa fa-trash"></i></button>
							<?= $hiddenInputs; ?>
						</td>
					</tr>
					<?php else : ?>
					<tr class="opp-sub-row" data-parent="<?= $rowNum; ?>">
						<td><?= htmlspecialchars($pName); ?></td>
						<td><?= htmlspecialchars($inv); ?></td>
					</tr>
					<?php endif; endforeach; endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<small class="text-muted">Maximum 50 entries allowed.</small>
</div>

<script>
$(document).ready(function() {
	$('#select_procedure_multi').select2({
		allowClear: true,
		width: '100%',
		placeholder: 'Select Proses/Prosedur'
	});

	// When procedure selection changes, generate investigasi input fields
	$('#select_procedure_multi').on('change', function() {
		var selected = $(this).find(':selected');
		var container = $('#investigasi-inputs');
		container.empty();

		if (selected.length === 0) {
			container.html('<small class="text-muted">Pilih proses terlebih dahulu</small>');
			return;
		}

		selected.each(function() {
			var procName = $(this).data('name') || $(this).text();
			var procId = $(this).val();
			container.append(
				'<div class="mb-2">' +
				'<label class="small text-muted mb-0">' + procName + ':</label>' +
				'<input type="text" class="form-control form-control-sm investigasi-field" data-proc-id="' + procId + '" data-proc-name="' + procName + '" placeholder="Investigasi untuk ' + procName + '">' +
				'</div>'
			);
		});
	});

	var maxEntries = 50;

	// Add Item - 1 issue = 1 row with multiple processes inside
	$(document).on('click', '#btn-add-opportunity', function() {
		var issueText = $.trim($('#issue_text').val());
		var selectedProcs = $('#select_procedure_multi').find(':selected');

		if (issueText === '') {
			$('#issue_text').addClass('is-invalid');
			return false;
		}
		$('#issue_text').removeClass('is-invalid');

		if (selectedProcs.length === 0) {
			Swal.fire({title: 'Warning!', icon: 'warning', text: 'Pilih minimal 1 Proses/Prosedur.'});
			return false;
		}

		// Build process and investigation lists
		var hiddenInputs = '';
		var rowNum = $('#table-opportunities > tbody > tr.opp-group-row').length + 1;

		var newRow = '';
		var procItems = [];
		$('#investigasi-inputs .investigasi-field').each(function() {
			procItems.push({
				procId: $(this).data('proc-id'),
				procName: $(this).data('proc-name'),
				investigation: $.trim($(this).val())
			});
		});

		// Build rows: first row has rowspan for No, Issue, Actions; subsequent rows only have Proses & Investigasi
		for (var i = 0; i < procItems.length; i++) {
			var item = procItems[i];
			hiddenInputs += '<input type="hidden" name="opp_id[]" value="">';
			hiddenInputs += '<input type="hidden" name="opp_issue_text[]" value="' + escapeHtml(issueText) + '">';
			hiddenInputs += '<input type="hidden" name="opp_procedure_id[]" value="' + item.procId + '">';
			hiddenInputs += '<input type="hidden" name="opp_procedure_name[]" value="' + escapeHtml(item.procName) + '">';
			hiddenInputs += '<input type="hidden" name="opp_investigation[]" value="' + escapeHtml(item.investigation) + '">';
		}

		for (var i = 0; i < procItems.length; i++) {
			var item = procItems[i];
			if (i === 0) {
				newRow += '<tr class="opp-group-row">' +
					'<td class="text-center align-top row-number" rowspan="' + procItems.length + '">' + rowNum + '</td>' +
					'<td class="align-top" rowspan="' + procItems.length + '">' + escapeHtml(issueText) + '</td>' +
					'<td>' + escapeHtml(item.procName) + '</td>' +
					'<td>' + escapeHtml(item.investigation) + '</td>' +
					'<td class="text-center align-top" rowspan="' + procItems.length + '">' +
						'<button type="button" class="btn btn-xs btn-icon btn-warning edit-opportunity" title="Edit"><i class="fa fa-edit"></i></button> ' +
						'<button type="button" class="btn btn-xs btn-icon btn-danger delete-opportunity" title="Delete"><i class="fa fa-trash"></i></button>' +
						hiddenInputs +
					'</td>' +
				'</tr>';
			} else {
				newRow += '<tr class="opp-sub-row" data-parent="' + rowNum + '">' +
					'<td>' + escapeHtml(item.procName) + '</td>' +
					'<td>' + escapeHtml(item.investigation) + '</td>' +
				'</tr>';
			}
		}

		$('#table-opportunities > tbody').append(newRow);

		// Reset
		$('#issue_text').val('');
		$('#select_procedure_multi').val(null).trigger('change');
		$('#investigasi-inputs').html('<small class="text-muted">Pilih proses terlebih dahulu</small>');
	});

	// Delete
	$(document).on('click', '.delete-opportunity', function() {
		var row = $(this).closest('tr.opp-group-row');
		var rowNum = row.find('.row-number').text();
		Swal.fire({
			title: 'Delete?', icon: 'question', showCancelButton: true,
			confirmButtonText: 'Ya', cancelButtonText: 'Batal'
		}).then(function(result) {
			if (result.isConfirmed) {
				$('#table-opportunities > tbody > tr.opp-sub-row[data-parent="' + rowNum + '"]').remove();
				row.remove();
				var num = 0;
				$('#table-opportunities > tbody > tr.opp-group-row').each(function() {
					num++;
					$(this).find('.row-number').text(num);
				});
			}
		});
	});

	// Edit
	$(document).on('click', '.edit-opportunity', function() {
		var row = $(this).closest('tr');
		var issueInputs = row.find('input[name="opp_issue_text[]"]');
		var procIdInputs = row.find('input[name="opp_procedure_id[]"]');
		var invInputs = row.find('input[name="opp_investigation[]"]');

		var issue = issueInputs.first().val();
		var procIds = [];
		procIdInputs.each(function() { procIds.push($(this).val()); });

		$('#issue_text').val(issue);
		$('#select_procedure_multi').val(procIds).trigger('change');

		// Wait for investigasi fields to render, then fill values
		setTimeout(function() {
			$('#investigasi-inputs .investigasi-field').each(function(idx) {
				if (invInputs[idx]) {
					$(this).val($(invInputs[idx]).val());
				}
			});
		}, 300);

		row.remove();
		$('#table-opportunities tbody tr').each(function(idx) {
			$(this).find('.row-number').text(idx + 1);
		});
	});

	function escapeHtml(text) {
		if (!text) return '';
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(text));
		return div.innerHTML;
	}
});
</script>
