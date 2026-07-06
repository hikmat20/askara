<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header justify-content-between d-flex align-items-center">
					<h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i>Create Checklist Audit Berdasarkan Kinerja</h2>
					<a href="<?= site_url('audit_checklist_non_standard'); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Kembali</a>
				</div>

				<div class="card-body">
					<form id="formChecklist">
						<input type="hidden" name="schedule_id" value="<?= $schedule->schedule_id; ?>">

						<!-- Section: Info dari Jadwal Audit -->
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-calendar-alt text-primary mr-2"></i>Informasi Jadwal Audit</h5>
							<table class="table table-bordered table-sm">
								<tr>
									<th width="200">Proses</th>
									<td><?= !empty($schedule->requirement_name) ? htmlspecialchars($schedule->requirement_name) : (!empty($schedule->process_name) ? strip_tags($schedule->process_name) : htmlspecialchars($schedule->process_name_free)); ?></td>
								</tr>
								<tr>
									<th>Department - Company</th>
									<td><?= isset($schedule->department_name) ? $schedule->department_name : '-'; ?></td>
								</tr>
								<tr>
									<th>Auditor</th>
									<td><?= isset($schedule->auditor_name) ? $schedule->auditor_name : '-'; ?></td>
								</tr>
								<tr>
									<th>Tanggal</th>
									<td><?= date('d/m/Y', strtotime($schedule->audit_date)); ?></td>
								</tr>
								<tr>
									<th>Jam</th>
									<td><?= substr($schedule->start_time, 0, 5); ?> - <?= substr($schedule->end_time, 0, 5); ?></td>
								</tr>
							</table>
						</div>

						<!-- Section: Isu Proses (read-only, from matching process) -->
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-lightbulb text-warning mr-2"></i>Isu Proses</h5>
							<?php if (!empty($issues)) : ?>
								<div class="table-responsive">
									<table class="table table-bordered table-sm table-hover">
										<thead class="table-light">
											<tr class="text-center">
												<th width="200">Issue</th>
												<th>Investigasi</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($issues as $issue) : ?>
												<tr>
													<td><?= htmlspecialchars($issue->description); ?></td>
													<td><?= htmlspecialchars(isset($issue->investigation) ? $issue->investigation : ''); ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php else : ?>
								<p class="text-muted"><em>Tidak ada isu proses yang terkait dengan proses ini.</em></p>
							<?php endif; ?>
						</div>

						<!-- Section: Checklist (free text, dynamic add/delete) -->
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-check-square text-success mr-2"></i>Checklist</h5>
							<div class="table-responsive">
								<table id="table-checklist" class="table table-bordered table-sm table-hover">
									<thead class="table-light">
										<tr class="text-center">
											<th width="60">No</th>
											<th>Checklist</th>
											<th width="100">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($existing)) : ?>
											<?php foreach ($existing as $k => $item) : ?>
												<tr class="checklist-row">
													<td class="text-center row-number"><?= $k + 1; ?></td>
													<td>
														<input type="text" name="checklist[<?= $k; ?>][text]" class="form-control checklist-input" value="<?= htmlspecialchars($item->checklist_text); ?>" placeholder="Apakah laporan kinerja vendor (on-time delivery rate) direview secara berkala?">
													</td>
													<td class="text-center">
														<button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-checklist" title="Delete"><i class="fa fa-trash"></i></button>
													</td>
												</tr>
											<?php endforeach; ?>
										<?php else : ?>
											<tr class="checklist-row">
												<td class="text-center row-number">1</td>
												<td>
													<input type="text" name="checklist[0][text]" class="form-control checklist-input" placeholder="Apakah laporan kinerja vendor (on-time delivery rate) direview secara berkala?">
												</td>
												<td class="text-center">
													<button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-checklist" title="Delete"><i class="fa fa-trash"></i></button>
												</td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>

							<div class="mt-2">
								<button type="button" class="btn btn-sm btn-info" id="btn-add-checklist"><i class="fa fa-plus mr-1"></i> Add Checklist</button>
								<button type="button" class="btn btn-sm btn-success ml-2 btn-save-checklist"><i class="fa fa-save mr-1"></i> Save</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	var rowIndex = $('#table-checklist tbody tr.checklist-row').length;

	// Add new checklist row
	$('#btn-add-checklist').on('click', function() {
		var html = '<tr class="checklist-row">' +
			'<td class="text-center row-number"></td>' +
			'<td><input type="text" name="checklist[' + rowIndex + '][text]" class="form-control checklist-input" placeholder="Apakah laporan kinerja vendor (on-time delivery rate) direview secara berkala?"></td>' +
			'<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-checklist" title="Delete"><i class="fa fa-trash"></i></button></td>' +
			'</tr>';
		$('#table-checklist tbody').append(html);
		rowIndex++;
		renumberRows();
	});

	// Delete checklist row
	$(document).on('click', '.btn-delete-checklist', function() {
		var rowCount = $('#table-checklist tbody tr.checklist-row').length;
		if (rowCount <= 1) {
			Swal.fire({title: 'Info', icon: 'info', text: 'Minimal harus ada 1 baris checklist.', timer: 2000});
			return;
		}
		$(this).closest('tr').remove();
		renumberRows();
	});

	// Save checklist
	$(document).on('click', '.btn-save-checklist', function() {
		var $btn = $(this);
		var hasContent = false;

		$('.checklist-input').each(function() {
			if ($.trim($(this).val()) !== '') {
				hasContent = true;
				return false;
			}
		});

		if (!hasContent) {
			Swal.fire({title: 'Warning!', icon: 'warning', text: 'Minimal isi 1 item checklist.'});
			return;
		}

		Swal.fire({
			title: 'Simpan Checklist?',
			icon: 'question',
			text: 'Apakah Anda yakin ingin menyimpan checklist ini?',
			showCancelButton: true,
			confirmButtonText: 'Ya, Simpan',
			cancelButtonText: 'Batal'
		}).then(function(result) {
			if (result.isConfirmed) {
				var formData = new FormData($('#formChecklist')[0]);
				$.ajax({
					url: '<?= site_url("audit_checklist_non_standard/save"); ?>',
					data: formData,
					type: 'POST',
					dataType: 'JSON',
					processData: false,
					contentType: false,
					beforeSend: function() {
						$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');
					},
					complete: function() {
						$btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Save');
					},
					success: function(res) {
						if (res.status == 1) {
							Swal.fire({title: 'Success!', icon: 'success', text: res.msg, timer: 2000}).then(function() {
								window.location.href = '<?= site_url("audit_checklist_non_standard"); ?>';
							});
						} else {
							Swal.fire({title: 'Warning!', icon: 'warning', text: res.msg});
						}
					},
					error: function() {
						Swal.fire({title: 'Error!', icon: 'error', text: 'Server error, silakan coba lagi.'});
					}
				});
			}
		});
	});

	function renumberRows() {
		var n = 0;
		$('#table-checklist tbody tr.checklist-row').each(function() {
			n++;
			$(this).find('.row-number').text(n);
		});
	}
});
</script>
