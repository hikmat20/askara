<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header justify-content-between d-flex align-items-center">
					<h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i>Pelaksanaan Audit</h2>
					<a href="<?= site_url('pelaksanaan_audit'); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Kembali</a>
				</div>

				<div class="card-body">
					<form id="formAudit">
						<input type="hidden" name="schedule_id" value="<?= $schedule->schedule_id; ?>">

						<!-- ================ HEADER INFO ================ -->
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-calendar-alt text-primary mr-2"></i><span class="text-primary">Header</span></h5>
							<table class="table table-bordered table-sm">
								<tr><th width="200">Prosedur</th><td><?= !empty($schedule->process_name) ? strip_tags($schedule->process_name) : htmlspecialchars($schedule->process_name_free); ?></td></tr>
								<tr><th>Date</th><td><?= date('d/m/Y', strtotime($schedule->audit_date)); ?></td></tr>
								<tr><th>Department</th><td><?= isset($schedule->department_name) ? $schedule->department_name : '-'; ?></td></tr>
								<tr><th>Auditor</th><td><?= isset($schedule->auditor_name) ? $schedule->auditor_name : '-'; ?></td></tr>
							</table>
						</div>

						<!-- ================ ISU PROSES ================ -->
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-lightbulb text-warning mr-2"></i><span class="text-warning">Isu Proses</span></h5>
							<?php if (!empty($issues)) : ?>
								<table class="table table-bordered table-sm table-hover">
									<thead class="table-light"><tr class="text-center"><th width="200">Issue</th><th>Investigasi</th></tr></thead>
									<tbody>
										<?php foreach ($issues as $issue) : ?>
											<tr><td><?= htmlspecialchars($issue->description); ?></td><td><?= htmlspecialchars(isset($issue->investigation) ? $issue->investigation : ''); ?></td></tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php else : ?>
								<p class="text-muted"><em>Tidak ada isu proses yang terkait.</em></p>
							<?php endif; ?>
						</div>

						<!-- ================ LIST CHECKLIST AUDIT BERDASARKAN KINERJA (hanya Checklist + Catatan) ================ -->
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-clipboard-list text-danger mr-2"></i><span class="text-danger">List Checklist Audit Berdasarkan Kinerja</span> <small class="text-danger">(wajib diisi semua)</small></h5>
							<?php if (!empty($ns_checklist)) : ?>
								<?php
								$ns_detail_map = [];
								if (!empty($audit_ns_details)) { foreach ($audit_ns_details as $d) { $ns_detail_map[$d->checklist_id] = $d; } }
								?>
								<div class="table-responsive">
									<table class="table table-bordered table-sm table-hover">
										<thead class="table-light text-center">
											<tr><th width="30">No</th><th width="45%">Checklist</th><th width="45%">Catatan</th></tr>
										</thead>
										<tbody>
											<?php foreach ($ns_checklist as $k => $item) : $k++;
												$existing_detail = isset($ns_detail_map[$item->id]) ? $ns_detail_map[$item->id] : null;
											?>
												<tr>
													<td class="text-center"><?= $k; ?></td>
													<td>
														<input type="hidden" name="ns_detail[<?= $k; ?>][checklist_id]" value="<?= $item->id; ?>">
														<?php if ($existing_detail) : ?><input type="hidden" name="ns_detail[<?= $k; ?>][id]" value="<?= $existing_detail->id; ?>"><?php endif; ?>
														<?= htmlspecialchars($item->checklist_text); ?>
													</td>
													<td><textarea name="ns_detail[<?= $k; ?>][catatan]" class="form-control form-control-sm" rows="2" placeholder="Catatan..."><?= $existing_detail ? htmlspecialchars($existing_detail->catatan) : ''; ?></textarea></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php else : ?>
								<p class="text-muted"><em>Tidak ada Checklist Audit Berdasarkan Kinerja untuk proses ini.</em></p>
							<?php endif; ?>
						</div>

						<!-- ================ LIST CHECKLIST STANDARD (hanya Checklist + Catatan) ================ -->
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-check-double text-info mr-2"></i><span class="text-info">List Checklist Standard</span> <small class="text-muted">(tidak wajib diisi semua)</small></h5>
							<?php if (!empty($std_checklist)) : ?>
								<?php
								$std_detail_map = [];
								if (!empty($audit_std_details)) { foreach ($audit_std_details as $d) { $std_detail_map[$d->checklist_detail_id] = $d; } }
								?>
								<div class="table-responsive">
									<table class="table table-bordered table-sm table-hover">
										<thead class="table-light text-center">
											<tr><th width="30">No</th><th width="45%">Checklist</th><th width="45%">Catatan</th></tr>
										</thead>
										<tbody>
											<?php foreach ($std_checklist as $k => $item) : $k++;
												$existing_detail = isset($std_detail_map[$item->id]) ? $std_detail_map[$item->id] : null;
											?>
												<tr>
													<td class="text-center"><?= $k; ?></td>
													<td>
														<input type="hidden" name="std_detail[<?= $k; ?>][checklist_detail_id]" value="<?= $item->id; ?>">
														<?php if ($existing_detail) : ?><input type="hidden" name="std_detail[<?= $k; ?>][id]" value="<?= $existing_detail->id; ?>"><?php endif; ?>
														<?= isset($item->description) ? htmlspecialchars($item->description) : ''; ?>
													</td>
													<td><textarea name="std_detail[<?= $k; ?>][catatan]" class="form-control form-control-sm" rows="2" placeholder="Catatan..."><?= $existing_detail ? htmlspecialchars($existing_detail->catatan) : ''; ?></textarea></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php else : ?>
								<p class="text-muted"><em>Tidak ada checklist standard untuk proses ini.</em></p>
							<?php endif; ?>
						</div>

						<!-- ================ KESIMPULAN AUDIT ================ -->
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-star text-success mr-2"></i><span class="text-success">Kesimpulan Audit</span></h5>

							<!-- Conformity / Strong Point (hanya Strong Point + Action) -->
							<h6 class="font-weight-bold mt-3 mb-2">Conformity / Strong Point</h6>
							<div class="table-responsive">
								<table class="table table-bordered table-sm" id="tblConformity">
									<thead class="text-center table-light">
										<tr><th width="30">No</th><th>Strong Point</th><th width="70">Action</th></tr>
									</thead>
									<tbody>
										<?php if (!empty($audit_conformity)) : foreach ($audit_conformity as $k => $cf) : $k++; ?>
											<tr class="conformity-row">
												<td class="text-center row-num"><?= $k; ?></td>
												<td>
													<input type="hidden" name="conformity[<?= $k; ?>][id]" value="<?= $cf->id; ?>">
													<textarea name="conformity[<?= $k; ?>][description]" class="form-control form-control-sm" rows="5" placeholder="Problem :&#10;Lokasi :&#10;Objectives evidence :&#10;Reference :&#10;Temuan audit :"><?= htmlspecialchars($cf->description); ?></textarea>
												</td>
												<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-conformity" data-id="<?= $cf->id; ?>"><i class="fa fa-trash"></i></button></td>
											</tr>
										<?php endforeach; else : ?>
											<tr class="conformity-row">
												<td class="text-center row-num">1</td>
												<td><textarea name="conformity[1][description]" class="form-control form-control-sm" rows="5" placeholder="Problem :&#10;Lokasi :&#10;Objectives evidence :&#10;Reference :&#10;Temuan audit :"></textarea></td>
												<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-conformity"><i class="fa fa-trash"></i></button></td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
							<button type="button" class="btn btn-sm btn-outline-success mb-4" id="btn-add-conformity"><i class="fa fa-plus mr-1"></i> Add Item</button>

							<!-- Temuan (full: Kategori, ISO, Pasal, Evidence, Action) -->
							<h6 class="font-weight-bold mt-3 mb-2">Temuan</h6>
							<div class="table-responsive">
								<table class="table table-bordered table-sm" id="tblTemuan">
									<thead class="text-center table-light">
										<tr>
											<th width="30">No</th>
											<th>Temuan</th>
											<th width="120">Kategori</th>
											<th width="150">ISO</th>
											<th width="200">Pasal</th>
											<th width="80">Evidence</th>
											<th width="60">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($audit_temuan)) : foreach ($audit_temuan as $k => $tm) : $k++; ?>
											<tr class="temuan-row">
												<td class="text-center row-num"><?= $k; ?></td>
												<td>
													<input type="hidden" name="temuan[<?= $k; ?>][id]" value="<?= $tm->id; ?>">
													<textarea name="temuan[<?= $k; ?>][description]" class="form-control form-control-sm" rows="5" placeholder="Problem :&#10;Lokasi :&#10;Objectives evidence :&#10;Reference :&#10;Temuan audit :"><?= htmlspecialchars($tm->description); ?></textarea>
												</td>
												<td>
													<select name="temuan[<?= $k; ?>][kategori]" class="form-control select2" data-placeholder="Pilih">
														<option value=""></option>
														<option value="OFI" <?= ($tm->kategori == 'OFI') ? 'selected' : ''; ?>>OFI</option>
														<option value="Minor" <?= ($tm->kategori == 'Minor') ? 'selected' : ''; ?>>Minor</option>
														<option value="Major" <?= ($tm->kategori == 'Major') ? 'selected' : ''; ?>>Major</option>
													</select>
												</td>
												<td>
													<select name="temuan[<?= $k; ?>][iso_id]" class="form-control select2 iso-select" data-row="tm_<?= $k; ?>" data-placeholder="Pilih ISO">
														<option value=""></option>
														<?php foreach ($standards as $std) : ?>
															<option value="<?= $std->id; ?>" <?= ($tm->iso_id == $std->id) ? 'selected' : ''; ?>><?= htmlspecialchars($std->name); ?></option>
														<?php endforeach; ?>
													</select>
												</td>
												<td>
													<select name="temuan[<?= $k; ?>][pasal_id]" id="pasal_tm_<?= $k; ?>" class="form-control select2 pasal-select" data-placeholder="Pilih Pasal">
														<option value=""></option>
														<?php if ($tm->pasal_id) :
															$pasal_row = $this->db->get_where('requirement_details', ['id' => $tm->pasal_id])->row();
															if ($pasal_row) : ?>
																<option value="<?= $pasal_row->id; ?>" selected><?= htmlspecialchars($pasal_row->chapter); ?></option>
														<?php endif; endif; ?>
													</select>
												</td>
												<td class="text-center">
													<?php if (!empty($tm->file_name)) : ?>
														<a href="<?= base_url('directory/AUDIT_PELAKSANAAN/' . $this->session->company->id_perusahaan . '/' . $schedule->schedule_id . '/' . $tm->file_name); ?>" target="_blank" title="<?= $tm->file_name; ?>"><i class="fa fa-eye text-success"></i></a>
													<?php endif; ?>
													<label style="cursor:pointer;" title="Upload Evidence">
														<i class="fa fa-upload text-primary"></i>
														<input type="file" name="evidence_tm_<?= $k; ?>" class="d-none" accept="*/*">
													</label>
												</td>
												<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-temuan" data-id="<?= $tm->id; ?>"><i class="fa fa-trash"></i></button></td>
											</tr>
										<?php endforeach; else : ?>
											<tr class="temuan-row">
												<td class="text-center row-num">1</td>
												<td><textarea name="temuan[1][description]" class="form-control form-control-sm" rows="5" placeholder="Problem :&#10;Lokasi :&#10;Objectives evidence :&#10;Reference :&#10;Temuan audit :"></textarea></td>
												<td>
													<select name="temuan[1][kategori]" class="form-control select2" data-placeholder="Pilih">
														<option value=""></option><option value="OFI">OFI</option><option value="Minor">Minor</option><option value="Major">Major</option>
													</select>
												</td>
												<td>
													<select name="temuan[1][iso_id]" class="form-control select2 iso-select" data-row="tm_1" data-placeholder="Pilih ISO">
														<option value=""></option>
														<?php foreach ($standards as $std) : ?>
															<option value="<?= $std->id; ?>"><?= htmlspecialchars($std->name); ?></option>
														<?php endforeach; ?>
													</select>
												</td>
												<td>
													<select name="temuan[1][pasal_id]" id="pasal_tm_1" class="form-control select2 pasal-select" data-placeholder="Pilih Pasal">
														<option value=""></option>
													</select>
												</td>
												<td class="text-center">
													<label style="cursor:pointer;" title="Upload Evidence">
														<i class="fa fa-upload text-primary"></i>
														<input type="file" name="evidence_tm_1" class="d-none" accept="*/*">
													</label>
												</td>
												<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-temuan"><i class="fa fa-trash"></i></button></td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
							<button type="button" class="btn btn-sm btn-outline-danger mb-4" id="btn-add-temuan"><i class="fa fa-plus mr-1"></i> Add Item</button>
						</div>

						<!-- ================ SAVE BUTTON ================ -->
						<div class="text-center mt-5">
							<button type="button" class="btn btn-lg btn-success btn-save-audit"><i class="fa fa-save mr-2"></i> Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	function initSelect2() {
		$('select.select2').not('.select2-hidden-accessible').select2({ placeholder: 'Choose an options', allowClear: true, width: '100%' });
	}

	// ISO CHANGE -> LOAD PASAL
	$(document).on('change', '.iso-select', function() {
		var row = $(this).data('row'), iso_id = $(this).val();
		var $pasal = $('#pasal_' + row);
		$pasal.empty().append('<option value=""></option>');
		if (iso_id) {
			$.get('<?= site_url("pelaksanaan_audit/get_pasal/"); ?>' + iso_id, function(html) { $pasal.html(html).trigger('change.select2'); });
		}
	});

	// ADD CONFORMITY
	$('#btn-add-conformity').on('click', function() {
		var n = $('#tblConformity tbody tr.conformity-row').length + 1;
		$('#tblConformity tbody').append(`<tr class="conformity-row">
			<td class="text-center row-num">${n}</td>
			<td><textarea name="conformity[${n}][description]" class="form-control form-control-sm" rows="5" placeholder="Problem :&#10;Lokasi :&#10;Objectives evidence :&#10;Reference :&#10;Temuan audit :"></textarea></td>
			<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-conformity"><i class="fa fa-trash"></i></button></td>
		</tr>`);
	});

	// ADD TEMUAN
	$('#btn-add-temuan').on('click', function() {
		var n = $('#tblTemuan tbody tr.temuan-row').length + 1;
		$('#tblTemuan tbody').append(`<tr class="temuan-row">
			<td class="text-center row-num">${n}</td>
			<td><textarea name="temuan[${n}][description]" class="form-control form-control-sm" rows="5" placeholder="Problem :&#10;Lokasi :&#10;Objectives evidence :&#10;Reference :&#10;Temuan audit :"></textarea></td>
			<td><select name="temuan[${n}][kategori]" class="form-control select2" data-placeholder="Pilih"><option value=""></option><option value="OFI">OFI</option><option value="Minor">Minor</option><option value="Major">Major</option></select></td>
			<td><select name="temuan[${n}][iso_id]" class="form-control select2 iso-select" data-row="tm_${n}" data-placeholder="Pilih ISO"><option value=""></option><?php foreach ($standards as $std) : ?><option value="<?= $std->id; ?>"><?= htmlspecialchars($std->name); ?></option><?php endforeach; ?></select></td>
			<td><select name="temuan[${n}][pasal_id]" id="pasal_tm_${n}" class="form-control select2 pasal-select" data-placeholder="Pilih Pasal"><option value=""></option></select></td>
			<td class="text-center"><label style="cursor:pointer;" title="Upload Evidence"><i class="fa fa-upload text-primary"></i><input type="file" name="evidence_tm_${n}" class="d-none" accept="*/*"></label></td>
			<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-temuan"><i class="fa fa-trash"></i></button></td>
		</tr>`);
		initSelect2();
	});

	// DELETE CONFORMITY
	$(document).on('click', '.btn-delete-conformity', function() {
		var id = $(this).data('id'), $row = $(this).closest('tr');
		if ($('#tblConformity tbody tr.conformity-row').length <= 1) { Swal.fire({ title: 'Info', icon: 'info', text: 'Minimal harus ada 1 baris.', timer: 2000 }); return; }
		if (id) { $.ajax({ url: '<?= site_url("pelaksanaan_audit/delete_conformity"); ?>', type: 'POST', data: { id: id }, dataType: 'JSON', success: function() { $row.remove(); renumberConformity(); } }); }
		else { $row.remove(); renumberConformity(); }
	});

	// DELETE TEMUAN
	$(document).on('click', '.btn-delete-temuan', function() {
		var id = $(this).data('id'), $row = $(this).closest('tr');
		if ($('#tblTemuan tbody tr.temuan-row').length <= 1) { Swal.fire({ title: 'Info', icon: 'info', text: 'Minimal harus ada 1 baris.', timer: 2000 }); return; }
		if (id) { $.ajax({ url: '<?= site_url("pelaksanaan_audit/delete_temuan"); ?>', type: 'POST', data: { id: id }, dataType: 'JSON', success: function() { $row.remove(); renumberTemuan(); } }); }
		else { $row.remove(); renumberTemuan(); }
	});

	// FILE CHANGE INDICATOR
	$(document).on('change', 'input[type="file"]', function() {
		if ($(this).val()) { $(this).closest('label').find('i').removeClass('fa-upload text-primary').addClass('fa-check-circle text-success'); }
	});

	// SAVE AUDIT
	$(document).on('click', '.btn-save-audit', function() {
		var $btn = $(this);
		Swal.fire({ title: 'Simpan Pelaksanaan Audit?', icon: 'question', text: 'Apakah Anda yakin ingin menyimpan data audit ini?', showCancelButton: true, confirmButtonText: 'Ya, Simpan', cancelButtonText: 'Batal' }).then(function(result) {
			if (result.isConfirmed) {
				var formData = new FormData($('#formAudit')[0]);
				$.ajax({
					url: '<?= site_url("pelaksanaan_audit/save"); ?>', data: formData, type: 'POST', dataType: 'JSON', processData: false, contentType: false,
					beforeSend: function() { $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i> Saving...'); },
					complete: function() { $btn.prop('disabled', false).html('<i class="fa fa-save mr-2"></i> Save'); },
					success: function(res) {
						if (res.status == 1) { Swal.fire({ title: 'Success!', icon: 'success', text: res.msg, timer: 2000 }).then(function() { window.location.href = '<?= site_url("pelaksanaan_audit"); ?>'; }); }
						else { Swal.fire({ title: 'Warning!', icon: 'warning', text: res.msg }); }
					},
					error: function() { Swal.fire({ title: 'Error!', icon: 'error', text: 'Server error, silakan coba lagi.' }); }
				});
			}
		});
	});

	function renumberConformity() { var n = 0; $('#tblConformity tbody tr.conformity-row').each(function() { n++; $(this).find('.row-num').text(n); }); }
	function renumberTemuan() { var n = 0; $('#tblTemuan tbody tr.temuan-row').each(function() { n++; $(this).find('.row-num').text(n); }); }
});
</script>
