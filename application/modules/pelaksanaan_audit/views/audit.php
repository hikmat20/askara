<style>
#formAudit textarea {
	overflow: hidden;
	resize: vertical;
}
#tblTemuan th:nth-child(5),
#tblTemuan td:nth-child(5) {
	width: 300px;
	min-width: 300px;
	max-width: 300px;
}
</style>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header justify-content-between d-flex align-items-center">
					<h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i>Pelaksanaan Audit</h2>
					<a href="<?= site_url('pelaksanaan_audit/schedules/' . $schedule->program_id); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Kembali</a>
				</div>

				<div class="card-body">
					<form id="formAudit">
						<input type="hidden" name="schedule_id" value="<?= $schedule->schedule_id; ?>">

						<!-- ================ HEADER INFO ================ -->
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-calendar-alt text-primary mr-2"></i><span class="text-primary">Header</span></h5>
							<table class="table table-bordered table-sm">
								<tr><th width="200">Prosedur</th><td><?= !empty($schedule->requirement_name) ? htmlspecialchars($schedule->requirement_name) : (!empty($schedule->process_name) ? strip_tags($schedule->process_name) : htmlspecialchars($schedule->process_name_free)); ?></td></tr>
								<tr><th>Date</th><td><?= date('d/m/Y', strtotime($schedule->audit_date)); ?></td></tr>
								<tr><th>Department</th><td><?= isset($schedule->department_name) ? htmlspecialchars($schedule->department_name) : '-'; ?></td></tr>
								<tr><th>Auditor</th><td><?= isset($schedule->auditor_name) ? htmlspecialchars($schedule->auditor_name) : '-'; ?></td></tr>
								<tr>
									<th>Auditee</th>
									<td><textarea name="auditee_text" class="form-control form-control-sm" rows="3" placeholder="Input Auditee..."><?= isset($audit_data->auditee_text) ? htmlspecialchars($audit_data->auditee_text) : ''; ?></textarea></td>
								</tr>
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
													<td><textarea name="ns_detail[<?= $k; ?>][catatan]" class="form-control form-control-sm" rows="2" placeholder="OK. Bukti jadwal audit sudah dikirim ke auditee tanggal 30 Mei 2026"><?= $existing_detail ? htmlspecialchars($existing_detail->catatan) : ''; ?></textarea></td>
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
													<td><textarea name="std_detail[<?= $k; ?>][catatan]" class="form-control form-control-sm" rows="2" placeholder="Tidak Ok. Terdapat 3 audit yang belum terlaksana (penjualan, maintenance, kepuasan pelanggan), tetapi ada temuan audit"><?= $existing_detail ? htmlspecialchars($existing_detail->catatan) : ''; ?></textarea></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php else : ?>
								<p class="text-muted"><em>Tidak ada checklist standard untuk proses ini.</em></p>
							<?php endif; ?>
						</div>

						<!-- ================ LIST NON CHECKLIST (free text, tidak wajib) - HIDE untuk Audit Persyaratan ================ -->
						<?php if (empty($schedule->requirement_id)) : ?>
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-list-alt text-purple mr-2"></i><span style="color:#6f42c1;">List Non Checklist</span> <small class="text-muted">(tidak wajib diisi semua)</small></h5>
							<div class="table-responsive">
								<table class="table table-bordered table-sm table-hover" id="tblFreeChecklist">
									<thead class="table-light text-center">
										<tr><th width="30">No</th><th width="45%">Checklist</th><th width="45%">Catatan</th><th width="60">Action</th></tr>
									</thead>
									<tbody>
										<?php if (!empty($audit_free_checklist)) : foreach ($audit_free_checklist as $k => $fc) : $k++; ?>
											<tr class="free-checklist-row">
												<td class="text-center row-num"><?= $k; ?></td>
												<td>
													<input type="hidden" name="free_checklist[<?= $k; ?>][id]" value="<?= $fc->id; ?>">
													<textarea name="free_checklist[<?= $k; ?>][checklist_text]" class="form-control form-control-sm" rows="2" placeholder="Apakah ada bukti implementasi internal audit terlaksana sesuai dengan jadwal yang ditetapkan? (Seperti: checklist internal audit yang sudah terisi, foto kegiatan audit, bukti sampling dokumen, dll)"><?= htmlspecialchars($fc->checklist_text); ?></textarea>
												</td>
												<td><textarea name="free_checklist[<?= $k; ?>][catatan]" class="form-control form-control-sm" rows="2" placeholder="Tidak Ok. Terdapat 3 audit yang belum terlaksana (penjualan, maintenance, kepuasan pelanggan), tetapi ada temuan audit"><?= htmlspecialchars($fc->catatan); ?></textarea></td>
												<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-free-checklist" data-id="<?= $fc->id; ?>"><i class="fa fa-trash"></i></button></td>
											</tr>
										<?php endforeach; else : ?>
											<tr class="free-checklist-row">
												<td class="text-center row-num">1</td>
												<td><textarea name="free_checklist[1][checklist_text]" class="form-control form-control-sm" rows="2" placeholder="Apakah ada bukti implementasi internal audit terlaksana sesuai dengan jadwal yang ditetapkan? (Seperti: checklist internal audit yang sudah terisi, foto kegiatan audit, bukti sampling dokumen, dll)"></textarea></td>
												<td><textarea name="free_checklist[1][catatan]" class="form-control form-control-sm" rows="2" placeholder="Tidak Ok. Terdapat 3 audit yang belum terlaksana (penjualan, maintenance, kepuasan pelanggan), tetapi ada temuan audit"></textarea></td>
												<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-free-checklist"><i class="fa fa-trash"></i></button></td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
							<button type="button" class="btn btn-sm btn-outline-primary mb-2" id="btn-add-free-checklist"><i class="fa fa-plus mr-1"></i> Add Item</button>
						</div>
						<?php endif; ?>

						<!-- ================ CHECKLIST BERDASARKAN PERSYARATAN (khusus Audit Persyaratan) ================ -->
						<?php if (!empty($schedule->requirement_id) && !empty($requirement_details)) : ?>
						<div class="mb-4">
							<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-clipboard-check text-info mr-2"></i><span class="text-info">Checklist Berdasarkan Persyaratan</span> <small class="text-muted">(tidak wajib diisi semua)</small></h5>
							<?php
							$req_detail_map = [];
							if (!empty($audit_requirement_details)) {
								foreach ($audit_requirement_details as $d) {
									$req_detail_map[$d->requirement_detail_id] = $d;
								}
							}
							?>
							<div class="table-responsive">
								<table class="table table-bordered table-sm table-hover" id="tblReqChecklist">
									<thead class="table-light text-center">
										<tr>
											<th width="180">Pasal</th>
											<th>Requirement (Des. Inggris)</th>
											<th width="180">Aktual</th>
											<th width="180">Temuan</th>
											<th width="180">Rekomendasi</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($requirement_details as $k => $rd) :
											$existing = isset($req_detail_map[$rd->id]) ? $req_detail_map[$rd->id] : null;
										?>
											<tr class="req-checklist-row">
												<td style="vertical-align:top;">
													<input type="hidden" name="req_detail[<?= $k; ?>][requirement_detail_id]" value="<?= $rd->id; ?>">
													<?php if ($existing) : ?><input type="hidden" name="req_detail[<?= $k; ?>][id]" value="<?= $existing->id; ?>"><?php endif; ?>
													<?= htmlspecialchars($rd->chapter); ?>
												</td>
												<td style="vertical-align:top;" class="req-desc-cell"><?= $rd->desc_eng; ?></td>
												<td style="vertical-align:top;"><textarea name="req_detail[<?= $k; ?>][aktual]" class="form-control form-control-sm req-textarea" rows="2" placeholder="Input free text"><?= $existing ? htmlspecialchars($existing->aktual) : ''; ?></textarea></td>
												<td style="vertical-align:top;"><textarea name="req_detail[<?= $k; ?>][temuan]" class="form-control form-control-sm req-textarea" rows="2" placeholder="Input free text"><?= $existing ? htmlspecialchars($existing->temuan) : ''; ?></textarea></td>
												<td style="vertical-align:top;"><textarea name="req_detail[<?= $k; ?>][rekomendasi]" class="form-control form-control-sm req-textarea" rows="2" placeholder="Input free text"><?= $existing ? htmlspecialchars($existing->rekomendasi) : ''; ?></textarea></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
						<?php endif; ?>

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
													<textarea name="conformity[<?= $k; ?>][description]" class="form-control form-control-sm" rows="5" placeholder="Pencatatan rapih, record mudah ditelusur, personal auditee terbuka tidak menutup-nutupi masalah"><?= htmlspecialchars($cf->description); ?></textarea>
												</td>
												<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-conformity" data-id="<?= $cf->id; ?>"><i class="fa fa-trash"></i></button></td>
											</tr>
										<?php endforeach; else : ?>
											<tr class="conformity-row">
												<td class="text-center row-num">1</td>
												<td><textarea name="conformity[1][description]" class="form-control form-control-sm" rows="5" placeholder="Pencatatan rapih, record mudah ditelusur, personal auditee terbuka tidak menutup-nutupi masalah"></textarea></td>
												<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-conformity"><i class="fa fa-trash"></i></button></td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
							<button type="button" class="btn btn-sm btn-outline-success mb-4" id="btn-add-conformity"><i class="fa fa-plus mr-1"></i> Add Item</button>

							<!-- Temuan (full: Kategori, ISO, Pasal, Evidence, Action) - HIDE untuk Audit Persyaratan -->
							<?php if (empty($schedule->requirement_id)) : ?>
							<div class="d-flex justify-content-between align-items-center mb-2">
								<h6 class="font-weight-bold mt-3 mb-0">Temuan</h6>
								<small class="text-muted"><em>Format: PDF, JPG, JPEG, PNG, DOC, DOCX, XLS, XLSX (Max: 10MB)</em></small>
							</div>
							<div class="table-responsive">
								<table class="table table-bordered table-sm" id="tblTemuan">
									<thead class="text-center table-light">
										<tr>
											<th width="30">No</th>
											<th>Temuan</th>
											<th width="120">Kategori</th>
											<th width="150">Reference Standard</th>
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
													<textarea name="temuan[<?= $k; ?>][description]" class="form-control form-control-sm" rows="5" placeholder="Verifikasi hasil temuan internal audit tidak berjalan efektif. Ditemukan untuk tindakan perbaikan CAR002 terkait dengan claim keterlambatan pengiriman memiliki waktu perbaikan sampai tanggal 2 februari 2026, tetapi yang melakukan verifikasi tindakan temuan adalah departement delivery dan tidak diverifikasi langsung oleh internal auditor sebagai penerbit CAR"><?= htmlspecialchars($tm->description); ?></textarea>
												</td>
												<td>
													<select name="temuan[<?= $k; ?>][kategori]" class="form-control select2" data-placeholder="OFI">
														<option value=""></option>
														<option value="OFI" <?= ($tm->kategori == 'OFI') ? 'selected' : ''; ?>>OFI</option>
														<option value="Minor" <?= ($tm->kategori == 'Minor') ? 'selected' : ''; ?>>Minor</option>
														<option value="Major" <?= ($tm->kategori == 'Major') ? 'selected' : ''; ?>>Major</option>
													</select>
												</td>
												<td>
													<select name="temuan[<?= $k; ?>][iso_id]" class="form-control select2 iso-select" data-row="tm_<?= $k; ?>" data-placeholder="ISO 9001:2015">
														<option value=""></option>
														<?php foreach ($standards as $std) : ?>
															<option value="<?= $std->id; ?>" <?= ($tm->iso_id == $std->id) ? 'selected' : ''; ?>><?= htmlspecialchars($std->name); ?></option>
														<?php endforeach; ?>
													</select>
												</td>
												<td>
													<select name="temuan[<?= $k; ?>][pasal_id][]" id="pasal_tm_<?= $k; ?>" class="form-control select2 pasal-select" data-placeholder="9.2.2 Program Audit" multiple>
														<?php if ($tm->pasal_id) :
															$pasal_ids = json_decode($tm->pasal_id, true);
															if (!is_array($pasal_ids)) $pasal_ids = [$tm->pasal_id];
															foreach ($pasal_ids as $pid) :
																$pasal_row = $this->db->get_where('requirement_details', ['id' => $pid])->row();
																if ($pasal_row) : ?>
																	<option value="<?= $pasal_row->id; ?>" selected><?= htmlspecialchars($pasal_row->chapter); ?></option>
														<?php endif; endforeach; endif; ?>
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
												<td><textarea name="temuan[1][description]" class="form-control form-control-sm" rows="5" placeholder="Verifikasi hasil temuan internal audit tidak berjalan efektif. Ditemukan untuk tindakan perbaikan CAR002 terkait dengan claim keterlambatan pengiriman memiliki waktu perbaikan sampai tanggal 2 februari 2026, tetapi yang melakukan verifikasi tindakan temuan adalah departement delivery dan tidak diverifikasi langsung oleh internal auditor sebagai penerbit CAR"></textarea></td>
												<td>
													<select name="temuan[1][kategori]" class="form-control select2" data-placeholder="OFI">
														<option value=""></option><option value="OFI">OFI</option><option value="Minor">Minor</option><option value="Major">Major</option>
													</select>
												</td>
												<td>
													<select name="temuan[1][iso_id]" class="form-control select2 iso-select" data-row="tm_1" data-placeholder="ISO 9001:2015">
														<option value=""></option>
														<?php foreach ($standards as $std) : ?>
															<option value="<?= $std->id; ?>"><?= htmlspecialchars($std->name); ?></option>
														<?php endforeach; ?>
													</select>
												</td>
												<td>
													<select name="temuan[1][pasal_id][]" id="pasal_tm_1" class="form-control select2 pasal-select" data-placeholder="9.2.2 Program Audit" multiple>
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
							<?php endif; ?>
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
		$('select.select2').not('.select2-hidden-accessible').each(function() {
			$(this).select2({ placeholder: $(this).data('placeholder') || 'Choose an options', allowClear: true, width: '100%' });
		});
	}
	initSelect2();

	// ISO CHANGE -> LOAD PASAL
	$(document).on('change', '.iso-select', function() {
		var row = $(this).data('row'), iso_id = $(this).val();
		var $pasal = $('#pasal_' + row);
		$pasal.empty();
		if (iso_id) {
			$.get('<?= site_url("pelaksanaan_audit/get_pasal/"); ?>' + iso_id, function(html) { $pasal.html(html).trigger('change.select2'); });
		}
	});

	// On page load: load pasal options for existing temuan rows that have ISO selected
	$('.iso-select').each(function() {
		var iso_id = $(this).val();
		var row = $(this).data('row');
		var $pasal = $('#pasal_' + row);
		var currentPasal = $pasal.val(); // preserve currently selected pasal (array for multiple)
		if (iso_id && (!currentPasal || currentPasal.length === 0)) {
			// ISO is selected but pasal is empty - load pasal options
			$.get('<?= site_url("pelaksanaan_audit/get_pasal/"); ?>' + iso_id, function(html) {
				$pasal.html(html).trigger('change.select2');
			});
		} else if (iso_id && currentPasal && currentPasal.length > 0) {
			// ISO and pasal both selected - load full options and re-select
			var selectedVals = currentPasal;
			$.get('<?= site_url("pelaksanaan_audit/get_pasal/"); ?>' + iso_id, function(html) {
				$pasal.html(html).val(selectedVals).trigger('change.select2');
			});
		}
	});

	// ADD FREE CHECKLIST
	$('#btn-add-free-checklist').on('click', function() {
		var n = $('#tblFreeChecklist tbody tr.free-checklist-row').length + 1;
		$('#tblFreeChecklist tbody').append(`<tr class="free-checklist-row">
			<td class="text-center row-num">${n}</td>
			<td><textarea name="free_checklist[${n}][checklist_text]" class="form-control form-control-sm" rows="2" placeholder="Apakah ada bukti jadwal internal audit yang sudah dikirimkan ke seluruh auditee?"></textarea></td>
			<td><textarea name="free_checklist[${n}][catatan]" class="form-control form-control-sm" rows="2" placeholder="OK. Bukti jadwal audit sudah dikirim ke auditee tanggal 30 Mei 2026"></textarea></td>
			<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-free-checklist"><i class="fa fa-trash"></i></button></td>
		</tr>`);
	});

	// DELETE FREE CHECKLIST
	$(document).on('click', '.btn-delete-free-checklist', function() {
		var $row = $(this).closest('tr');
		if ($('#tblFreeChecklist tbody tr.free-checklist-row').length <= 1) {
			Swal.fire({ title: 'Info', icon: 'info', text: 'Minimal harus ada 1 baris.', timer: 2000 });
			return;
		}
		$row.remove();
		renumberFreeChecklist();
	});

	function renumberFreeChecklist() { var n = 0; $('#tblFreeChecklist tbody tr.free-checklist-row').each(function() { n++; $(this).find('.row-num').text(n); }); }

	// ADD CONFORMITY
	$('#btn-add-conformity').on('click', function() {
		var n = $('#tblConformity tbody tr.conformity-row').length + 1;
		$('#tblConformity tbody').append(`<tr class="conformity-row">
			<td class="text-center row-num">${n}</td>
			<td><textarea name="conformity[${n}][description]" class="form-control form-control-sm" rows="5" placeholder="Pencatatan rapih, record mudah ditelusur, personal auditee terbuka tidak menutup-nutupi masalah"></textarea></td>
			<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-conformity"><i class="fa fa-trash"></i></button></td>
		</tr>`);
	});

	// ADD TEMUAN
	$('#btn-add-temuan').on('click', function() {
		var n = $('#tblTemuan tbody tr.temuan-row').length + 1;
		$('#tblTemuan tbody').append(`<tr class="temuan-row">
			<td class="text-center row-num">${n}</td>
			<td><textarea name="temuan[${n}][description]" class="form-control form-control-sm" rows="5" placeholder="Verifikasi hasil temuan internal audit tidak berjalan efektif. Ditemukan untuk tindakan perbaikan CAR002 terkait dengan claim keterlambatan pengiriman memiliki waktu perbaikan sampai tanggal 2 februari 2026, tetapi yang melakukan verifikasi tindakan temuan adalah departement delivery dan tidak diverifikasi langsung oleh internal auditor sebagai penerbit CAR"></textarea></td>
			<td><select name="temuan[${n}][kategori]" class="form-control select2" data-placeholder="OFI"><option value=""></option><option value="OFI">OFI</option><option value="Minor">Minor</option><option value="Major">Major</option></select></td>
			<td><select name="temuan[${n}][iso_id]" class="form-control select2 iso-select" data-row="tm_${n}" data-placeholder="ISO 9001:2015"><option value=""></option><?php foreach ($standards as $std) : ?><option value="<?= $std->id; ?>"><?= htmlspecialchars($std->name); ?></option><?php endforeach; ?></select></td>
			<td><select name="temuan[${n}][pasal_id][]" id="pasal_tm_${n}" class="form-control select2 pasal-select" data-placeholder="9.2.2 Program Audit" multiple></select></td>
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
						if (res.status == 1) { Swal.fire({ title: 'Success!', icon: 'success', text: res.msg, timer: 2000 }).then(function() { window.location.href = '<?= site_url("pelaksanaan_audit/schedules/" . $schedule->program_id); ?>'; }); }
						else { Swal.fire({ title: 'Warning!', icon: 'warning', text: res.msg }); }
					},
					error: function() { Swal.fire({ title: 'Error!', icon: 'error', text: 'Server error, silakan coba lagi.' }); }
				});
			}
		});
	});

	function renumberConformity() { var n = 0; $('#tblConformity tbody tr.conformity-row').each(function() { n++; $(this).find('.row-num').text(n); }); }
	function renumberTemuan() { var n = 0; $('#tblTemuan tbody tr.temuan-row').each(function() { n++; $(this).find('.row-num').text(n); }); }

	// Auto-resize textareas (exclude requirement checklist textareas)
	function autoResizeTextarea(el) {
		el.style.height = 'auto';
		el.style.height = el.scrollHeight + 'px';
	}

	// Apply on page load to all existing textareas (except req-textarea)
	$('#formAudit textarea').not('.req-textarea').each(function() { autoResizeTextarea(this); });

	// Apply on input (except req-textarea)
	$(document).on('input', '#formAudit textarea:not(.req-textarea)', function() { autoResizeTextarea(this); });

	// Resize requirement checklist textareas: min-height = desc_eng cell height, grows with content
	function resizeReqTextareas() {
		$('#tblReqChecklist tbody tr.req-checklist-row').each(function() {
			var $row = $(this);
			var descHeight = $row.find('.req-desc-cell').outerHeight();
			var minH = (descHeight && descHeight > 50) ? (descHeight - 16) : 50;
			$row.find('.req-textarea').each(function() {
				$(this).css('min-height', minH + 'px');
				// Set height based on content, but not less than minH
				this.style.height = 'auto';
				var contentH = this.scrollHeight;
				this.style.height = Math.max(contentH, minH) + 'px';
			});
		});
	}

	// On input for req-textarea: grow with content but respect min-height
	$(document).on('input', '.req-textarea', function() {
		var $row = $(this).closest('tr.req-checklist-row');
		var descHeight = $row.find('.req-desc-cell').outerHeight();
		var minH = (descHeight && descHeight > 50) ? (descHeight - 16) : 50;
		this.style.height = 'auto';
		var contentH = this.scrollHeight;
		this.style.height = Math.max(contentH, minH) + 'px';
	});

	resizeReqTextareas();
	$(window).on('resize', resizeReqTextareas);
});
</script>
