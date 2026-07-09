<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header justify-content-between d-flex align-items-center">
					<h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i>View Checklist Audit Berdasarkan Kinerja</h2>
					<a href="<?= site_url('audit_checklist_non_standard'); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Kembali</a>
				</div>

				<div class="card-body">
					<!-- Section: Info dari Jadwal Audit -->
					<div class="mb-4">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-calendar-alt text-primary mr-2"></i>Informasi Jadwal Audit</h5>
						<table class="table table-bordered table-sm">
							<tr>
								<th width="200">Proses</th>
								<td><?= !empty($schedule->requirement_name) ? htmlspecialchars($schedule->requirement_name) : (!empty($schedule->process_name) ? strip_tags($schedule->process_name) : htmlspecialchars($schedule->process_name_free)); ?></td>
							</tr>
							<tr>
								<th>Department</th>
								<td><?= !empty($schedule->department_name) ? $schedule->department_name : '-'; ?></td>
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

					<!-- Section: Isu Proses -->
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
							<p class="text-muted"><em>Tidak ada isu proses yang terkait.</em></p>
						<?php endif; ?>
					</div>

					<!-- Section: Checklist Items -->
					<div class="mb-4">
						<h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-check-square text-success mr-2"></i>Checklist</h5>
						<?php if (!empty($existing)) : ?>
							<div class="table-responsive">
								<table class="table table-bordered table-sm table-hover">
									<thead class="table-light">
										<tr class="text-center">
											<th width="60">No</th>
											<th>Checklist</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($existing as $k => $item) : ?>
											<tr>
												<td class="text-center"><?= $k + 1; ?></td>
												<td><?= htmlspecialchars($item->checklist_text); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php else : ?>
							<p class="text-muted"><em>Belum ada checklist dibuat.</em></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
