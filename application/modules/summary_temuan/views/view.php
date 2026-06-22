<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header justify-content-between d-flex align-items-center">
					<h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i><?= $title; ?>: <?= $program->id; ?></h2>
					<div>
						<a href="<?= site_url('summary_temuan/print_pdf/' . $program->id); ?>" class="btn btn-warning mr-2" target="_blank"><i class="fa fa-file-pdf"></i> Print PDF</a>
						<a href="<?= site_url('summary_temuan'); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Kembali</a>
					</div>
				</div>
				<div class="card-body">

					<!-- Header Info -->
					<div class="mb-4">
						<div class="row">
							<div class="col-md-4">
								<h5 class="font-weight-bold">Company</h5>
								<p><?= htmlspecialchars($program->company); ?></p>
							</div>
							<div class="col-md-4">
								<h5 class="font-weight-bold">Lead Auditor</h5>
								<p><?= htmlspecialchars($program->auditor_name); ?></p>
							</div>
							<div class="col-md-4">
								<h5 class="font-weight-bold">Audit Scope</h5>
								<p><?= htmlspecialchars($program->audit_scope); ?></p>
							</div>
						</div>
					</div>

					<!-- Summary Temuan Audit (Total) -->
					<div class="mb-5">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-chart-pie text-primary mr-2"></i>Summary Temuan Audit</h5>
						<table class="table table-bordered table-sm" style="max-width:400px;">
							<tr class="text-center">
								<td>Major</td>
								<td class="bg-danger text-white font-weight-bold"><?= $total_counts['Major']; ?></td>
								<td>Minor</td>
								<td class="bg-warning text-white font-weight-bold"><?= $total_counts['Minor']; ?></td>
								<td>OFI</td>
								<td class="bg-info text-white font-weight-bold"><?= $total_counts['OFI']; ?></td>
							</tr>
						</table>
					</div>

					<!-- Rincian Temuan Audit Per Proses -->
					<div class="mb-4">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-list-alt text-primary mr-2"></i>Rincian Temuan Audit Per Proses</h5>

						<?php foreach ($schedule_data as $idx => $item) : $no = $idx + 1; $sched = $item->schedule; ?>
							<div class="border rounded p-3 mb-4">
								<!-- Proses Header -->
								<div class="row mb-2">
									<div class="col-auto pr-2"><strong><?= $no; ?></strong></div>
									<div class="col-md-5">
										<table class="table table-sm table-borderless mb-0">
											<tr><th width="120">Proses</th><td><?= !empty($sched->process_name) ? strip_tags($sched->process_name) : htmlspecialchars($sched->process_name_free); ?></td></tr>
											<tr><th>Tanggal Audit</th><td><?= date('d-m-Y', strtotime($sched->audit_date)); ?></td></tr>
										</table>
									</div>
									<div class="col-md-5">
										<table class="table table-sm table-borderless mb-0">
											<tr><th width="100">Auditor</th><td><?= htmlspecialchars($sched->auditor_name); ?></td></tr>
											<tr><th>Auditee</th><td><?= isset($item->audit->auditee_text) ? htmlspecialchars($item->audit->auditee_text) : (isset($sched->department_name) ? $sched->department_name : '-'); ?></td></tr>
										</table>
									</div>
								</div>

								<!-- Kategori Count Per Proses -->
								<table class="table table-bordered table-sm mb-3" style="max-width:500px;">
									<tr class="text-center">
										<td>Major</td>
										<td class="bg-danger text-white font-weight-bold"><?= $item->counts['Major']; ?></td>
										<td>Minor</td>
										<td class="bg-warning text-white font-weight-bold"><?= $item->counts['Minor']; ?></td>
										<td>OFI</td>
										<td class="bg-info text-white font-weight-bold"><?= $item->counts['OFI']; ?></td>
									</tr>
								</table>

								<!-- Strong Point -->
								<div class="mb-3">
									<strong>Strong Point</strong>
									<div class="border rounded p-2 bg-light mt-1">
										<?php if (!empty($item->conformity)) :
											$cf_texts = [];
											foreach ($item->conformity as $cf) { $cf_texts[] = htmlspecialchars($cf->description); }
											echo implode('<br>', $cf_texts);
										else : ?>
											<em class="text-muted">-</em>
										<?php endif; ?>
									</div>
								</div>

								<!-- Temuan Table -->
								<div class="mb-2">
									<strong>Temuan</strong>
									<?php if (!empty($item->temuan)) : ?>
										<table class="table table-bordered table-sm mt-1">
											<thead class="table-light text-center">
												<tr>
													<th width="40">No</th>
													<th>Temuan</th>
													<th width="80">Kategori</th>
													<th width="150">Reference Standard</th>
													<th width="200">Pasal</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($item->temuan as $tk => $tm) : $tk++; ?>
													<tr>
														<td class="text-center"><?= $tk; ?></td>
														<td><?= nl2br(htmlspecialchars($tm->description)); ?></td>
														<td class="text-center"><?= $tm->kategori; ?></td>
														<td><?= isset($tm->iso_id) && isset($std_map[$tm->iso_id]) ? htmlspecialchars($std_map[$tm->iso_id]) : '-'; ?></td>
														<td><?php
															if (!empty($tm->pasal_id)) {
																$pasal_ids = json_decode($tm->pasal_id, true);
																if (!is_array($pasal_ids)) $pasal_ids = [$tm->pasal_id];
																foreach ($pasal_ids as $pid) {
																	$pasal = $this->db->get_where('requirement_details', ['id' => $pid])->row();
																	if ($pasal) echo htmlspecialchars($pasal->chapter) . '<br>';
																}
															} else { echo '-'; }
														?></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									<?php else : ?>
										<p class="text-muted mt-1"><em>Tidak ada temuan.</em></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
