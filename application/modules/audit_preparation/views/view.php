<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header justify-content-between d-flex align-items-center">
					<h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i>Detail Audit Program: <?= $program->id; ?></h2>
					<a href="<?= site_url('audit_preparation'); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Kembali</a>
				</div>

				<div class="card-body">
					<!-- Section 1: Header -->
					<div class="mb-5">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-file-alt text-primary mr-2"></i>Informasi Header</h5>
						<table class="table table-bordered table-sm">
							<tr>
								<th width="200">ID Program</th>
								<td><?= $program->id; ?></td>
							</tr>
							<tr>
								<th>Perusahaan</th>
								<td><?= $program->company; ?></td>
							</tr>
							<tr>
								<th>Lead Auditor</th>
								<td><?= $program->auditor_name; ?></td>
							</tr>
							<tr>
								<th>Ruang Lingkup</th>
								<td><?= $program->audit_scope; ?></td>
							</tr>
							<tr>
								<th>Dibuat Oleh</th>
								<td><?= isset($program->created_by_name) ? $program->created_by_name : '-'; ?></td>
							</tr>
							<tr>
								<th>Dibuat Pada</th>
								<td><?= isset($program->created_at) ? date('d-m-Y H:i', strtotime($program->created_at)) : '-'; ?></td>
							</tr>
						</table>
					</div>

					<!-- Section 2: Evaluasi Audit Sebelumnya (hidden for now) -->
					<!--
					<div class="mb-5">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-history text-primary mr-2"></i>Evaluasi Audit Sebelumnya</h5>
						<?php if (!empty($evaluations)) : ?>
							<table class="table table-bordered table-sm table-hover">
								<thead class="table-light">
									<tr class="text-center">
										<th width="40">No</th>
										<th>Referensi Audit</th>
										<th>Kelemahan</th>
										<th>Tindakan Perbaikan</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($evaluations as $k => $eval) : ?>
										<tr>
											<td class="text-center"><?= $k + 1; ?></td>
											<td><?= $eval->audit_temuan_id; ?></td>
											<td><?php
												$wd = $eval->weakness_description;
												$parts = preg_split('/\n+(?=\d+\.\s)/', $wd);
												$output = [];
												foreach ($parts as $part) {
													$part = trim($part);
													if ($part !== '') $output[] = htmlspecialchars($part);
												}
												echo implode('<br><br>', $output);
											?></td>
											<td><?= $eval->improvement_action; ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<p class="text-muted"><em>Tidak ada data evaluasi audit sebelumnya.</em></p>
						<?php endif; ?>
					</div>
					-->

					<!-- Section 3: Critical Issue -->
					<div class="mb-5">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-exclamation-triangle text-primary mr-2"></i>Improvement Program Audit</h5>
						<?php if (!empty($critical_issues)) : ?>
							<table class="table table-bordered table-sm table-hover">
								<thead class="table-light">
									<tr class="text-center">
										<th width="40">No</th>
										<th>Deskripsi Issue</th>
										<th>Improvement</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($critical_issues as $k => $issue) : ?>
										<tr>
											<td class="text-center"><?= $k + 1; ?></td>
											<td><?= $issue->issue_description; ?></td>
											<td><?= $issue->management_input; ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<p class="text-muted"><em>Tidak ada critical issue.</em></p>
						<?php endif; ?>
					</div>

					<!-- Section 4: Isu Proses -->
					<div class="mb-5">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-lightbulb text-primary mr-2"></i>Isu Proses</h5>
						<?php if (!empty($opportunities)) : ?>
							<?php
							// Group by issue text (description) same as form
							$grouped_opp = [];
							foreach ($opportunities as $opp) {
								$key = $opp->description;
								if (!isset($grouped_opp[$key])) {
									$grouped_opp[$key] = ['issue' => $key, 'items' => []];
								}
								$grouped_opp[$key]['items'][] = $opp;
							}
							?>
							<table class="table table-bordered table-sm table-hover">
								<thead class="table-light">
									<tr class="text-center">
										<th width="40">No</th>
										<th>Issue</th>
										<th>Proses / Prosedur</th>
										<th>Investigasi</th>
									</tr>
								</thead>
								<tbody>
									<?php $rowNum = 0; foreach ($grouped_opp as $group) : $rowNum++; $items = $group['items']; $rowspan = count($items); ?>
										<?php foreach ($items as $idx => $opp) : ?>
											<tr>
												<?php if ($idx === 0) : ?>
													<td class="text-center align-top" rowspan="<?= $rowspan; ?>"><?= $rowNum; ?></td>
													<td class="align-top" rowspan="<?= $rowspan; ?>"><?= htmlspecialchars($group['issue']); ?></td>
												<?php endif; ?>
												<td><?= isset($opp->procedure_name) ? strip_tags($opp->procedure_name) : $opp->procedure_id; ?></td>
												<td><?= isset($opp->investigation) ? htmlspecialchars($opp->investigation) : ''; ?></td>
											</tr>
										<?php endforeach; ?>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<p class="text-muted"><em>Tidak ada isu proses.</em></p>
						<?php endif; ?>
					</div>

					<!-- Section 5: Jadwal Audit -->
					<div class="mb-5">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-calendar-alt text-primary mr-2"></i>Jadwal Audit</h5>
						<?php if (!empty($schedules)) : ?>
							<table class="table table-bordered table-sm table-hover">
								<thead class="table-light">
									<tr class="text-center">
										<th width="40">No</th>
										<th>Proses</th>
										<th>Auditor</th>
										<th>Department</th>
										<th width="110">Tanggal</th>
										<th width="80">Mulai</th>
										<th width="80">Selesai</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($schedules as $k => $sched) : ?>
										<tr>
											<td class="text-center"><?= $k + 1; ?></td>
											<td><?= !empty($sched->process_name) ? strip_tags($sched->process_name) : htmlspecialchars($sched->process_name_free); ?></td>
											<td><?= $sched->auditor_name; ?></td>
											<td>
												<?php if (!empty($sched->auditees)) : ?>
													<?php
														$auditee_names = [];
														foreach ($sched->auditees as $aud) {
															$auditee_names[] = $aud->department_name;
														}
														echo implode(', ', $auditee_names);
													?>
												<?php else : ?>
													-
												<?php endif; ?>
											</td>
											<td class="text-center"><?= date('d-m-Y', strtotime($sched->audit_date)); ?></td>
											<td class="text-center"><?= substr($sched->start_time, 0, 5); ?></td>
											<td class="text-center"><?= substr($sched->end_time, 0, 5); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<p class="text-muted"><em>Tidak ada jadwal audit.</em></p>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
