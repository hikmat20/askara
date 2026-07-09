
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header justify-content-between d-flex align-items-center">
					<h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i>View Pelaksanaan Audit</h2>
					<a href="<?= site_url('pelaksanaan_audit/schedules/' . $schedule->program_id); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Kembali</a>
				</div>

				<div class="card-body">
					<!-- HEADER -->
					<div class="mb-4">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-calendar-alt text-primary mr-2"></i><span class="text-primary">Header</span></h5>
						<table class="table table-bordered table-sm">
							<tr><th width="200">Prosedur</th><td><?= !empty($schedule->requirement_name) ? htmlspecialchars($schedule->requirement_name) : (!empty($schedule->process_name) ? strip_tags($schedule->process_name) : htmlspecialchars($schedule->process_name_free)); ?></td></tr>
							<tr><th>Date</th><td><?= date('d/m/Y', strtotime($schedule->audit_date)); ?></td></tr>
							<tr><th>Department</th><td><?= isset($schedule->department_name) ? htmlspecialchars($schedule->department_name) : '-'; ?></td></tr>
							<tr><th>Auditor</th><td><?= isset($schedule->auditor_name) ? htmlspecialchars($schedule->auditor_name) : '-'; ?></td></tr>
							<tr><th>Auditee</th><td><?= isset($audit_data->auditee_text) && $audit_data->auditee_text ? nl2br(htmlspecialchars($audit_data->auditee_text)) : '-'; ?></td></tr>
						</table>
					</div>

					<!-- ISU PROSES -->
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
							<p class="text-muted"><em>Tidak ada isu proses.</em></p>
						<?php endif; ?>
					</div>

					<!-- CHECKLIST BERDASARKAN PERSYARATAN - khusus Audit Persyaratan, sebelum Kesimpulan Audit -->
					<?php if (!empty($schedule->requirement_id) && !empty($requirement_details)) :
						$req_detail_map = [];
						if (!empty($audit_requirement_details)) {
							foreach ($audit_requirement_details as $d) {
								$req_detail_map[$d->requirement_detail_id] = $d;
							}
						}
						// Filter: hanya tampilkan baris yang setidaknya salah satu (aktual/temuan/rekomendasi) terisi
						$has_filled_rows = false;
						foreach ($requirement_details as $rd) {
							$existing = isset($req_detail_map[$rd->id]) ? $req_detail_map[$rd->id] : null;
							if ($existing && (trim($existing->aktual) !== '' || trim($existing->temuan) !== '' || trim($existing->rekomendasi) !== '')) {
								$has_filled_rows = true;
								break;
							}
						}
					?>
					<?php if ($has_filled_rows) : ?>
					<div class="mb-4">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-clipboard-check text-info mr-2"></i><span class="text-info">Checklist Berdasarkan Persyaratan</span></h5>
						<div class="table-responsive">
							<table class="table table-bordered table-sm">
								<thead class="text-center table-light">
									<tr>
										<th width="180">Pasal</th>
										<th>Requirement (Des. Inggris)</th>
										<th width="180">Aktual</th>
										<th width="180">Temuan</th>
										<th width="180">Rekomendasi</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($requirement_details as $rd) :
										$existing = isset($req_detail_map[$rd->id]) ? $req_detail_map[$rd->id] : null;
										// Skip baris jika ketiga kolom kosong
										if (!$existing || (trim($existing->aktual) === '' && trim($existing->temuan) === '' && trim($existing->rekomendasi) === '')) continue;
									?>
										<tr>
											<td><?= htmlspecialchars($rd->chapter); ?></td>
											<td><?= $rd->desc_eng; ?></td>
											<td><?= trim($existing->aktual) !== '' ? nl2br(htmlspecialchars($existing->aktual)) : '-'; ?></td>
											<td><?= trim($existing->temuan) !== '' ? nl2br(htmlspecialchars($existing->temuan)) : '-'; ?></td>
											<td><?= trim($existing->rekomendasi) !== '' ? nl2br(htmlspecialchars($existing->rekomendasi)) : '-'; ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
					<?php endif; ?>
					<?php endif; ?>

					<!-- KESIMPULAN AUDIT -->
					<div class="mb-4">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-star text-success mr-2"></i><span class="text-success">Kesimpulan Audit</span></h5>

						<!-- Conformity (hanya Strong Point) -->
						<h6 class="font-weight-bold mt-3 mb-2">Conformity / Strong Point</h6>
						<?php if (!empty($audit_conformity)) : ?>
							<table class="table table-bordered table-sm">
								<thead class="text-center table-light"><tr><th width="30">No</th><th>Strong Point</th></tr></thead>
								<tbody>
									<?php foreach ($audit_conformity as $k => $cf) : $k++; ?>
										<tr><td class="text-center"><?= $k; ?></td><td><?= nl2br(htmlspecialchars($cf->description)); ?></td></tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<p class="text-muted"><em>Tidak ada data conformity.</em></p>
						<?php endif; ?>

						<!-- Temuan - HIDE untuk Audit Persyaratan -->
						<?php if (empty($schedule->requirement_id)) : ?>
						<h6 class="font-weight-bold mt-3 mb-2">Temuan</h6>
						<?php if (!empty($audit_temuan)) : ?>
							<table class="table table-bordered table-sm">
								<thead class="text-center table-light">
									<tr><th width="30">No</th><th>Temuan</th><th width="100">Kategori</th><th width="130">Reference Standard</th><th style="width:400px;min-width:400px;max-width:400px;">Pasal</th><th width="80">Evidence</th></tr>
								</thead>
								<tbody>
									<?php foreach ($audit_temuan as $k => $tm) : $k++; ?>
										<tr>
											<td class="text-center"><?= $k; ?></td>
											<td><?= nl2br(htmlspecialchars($tm->description)); ?></td>
											<td class="text-center">
												<?php if ($tm->kategori) : ?>
													<span class="font-weight-bold text-<?= ($tm->kategori == 'OK') ? 'success' : (($tm->kategori == 'Minor') ? 'warning' : (($tm->kategori == 'Major') ? 'danger' : 'info')); ?>"><?= $tm->kategori; ?></span>
												<?php endif; ?>
											</td>
											<td><?php if ($tm->iso_id) : $iso = $this->db->get_where('requirements', ['id' => $tm->iso_id])->row(); echo $iso ? htmlspecialchars($iso->name) : '-'; endif; ?></td>
											<td><?php if ($tm->pasal_id) :
												$pasal_ids = json_decode($tm->pasal_id, true);
												if (!is_array($pasal_ids)) $pasal_ids = [$tm->pasal_id];
												foreach ($pasal_ids as $pid) {
													$pasal = $this->db->get_where('requirement_details', ['id' => $pid])->row();
													if ($pasal) echo htmlspecialchars($pasal->chapter) . '<br>';
												}
											endif; ?></td>
											<td class="text-center">
												<?php if (!empty($tm->file_name)) : ?>
													<a href="<?= base_url('directory/AUDIT_PELAKSANAAN/' . $this->session->company->id_perusahaan . '/' . $schedule->schedule_id . '/' . $tm->file_name); ?>" target="_blank" class="text-success"><i class="fa fa-download"></i></a>
												<?php else : ?>-<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<p class="text-muted"><em>Tidak ada data temuan.</em></p>
						<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
