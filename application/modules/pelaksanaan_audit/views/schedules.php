<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header justify-content-between d-flex align-items-center">
					<h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i>Pelaksanaan Audit: <?= $program->id; ?></h2>
					<a href="<?= site_url('pelaksanaan_audit'); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Kembali</a>
				</div>
				<div class="card-body">
					<!-- Header Info -->
					<div class="mb-4">
						<table class="table table-bordered table-sm" style="max-width:600px;">
							<tr><th width="180">ID Program</th><td><?= $program->id; ?></td></tr>
							<tr><th>Company</th><td><?= $program->company; ?></td></tr>
							<tr><th>Lead Auditor</th><td><?= $program->auditor_name; ?></td></tr>
							<tr><th>Audit Scope</th><td><?= $program->audit_scope; ?></td></tr>
						</table>
					</div>

					<!-- Schedule List -->
					<h5 class="font-weight-bold border-bottom pb-2 mt-4"><i class="fa fa-calendar-alt text-primary mr-2"></i>Pilih Proses Audit</h5>
					<div class="table-responsive">
					<table id="dtSchedules" class="table table-bordered table-sm table-hover">
						<thead class="text-center table-light">
							<tr>
								<th width="40">No</th>
								<th>Process</th>
								<th>Department</th>
								<th>Auditor</th>
								<th width="120">Tanggal</th>
								<th width="120">Jam</th>
								<th width="130" class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($schedules)) foreach ($schedules as $k => $v) : $k++; ?>
								<tr>
									<td class="text-center"><?= $k; ?></td>
									<td><?= !empty($v->requirement_name) ? htmlspecialchars($v->requirement_name) : (!empty($v->process_name) ? strip_tags($v->process_name) : htmlspecialchars($v->process_name_free)); ?></td>
									<td><?= isset($v->department_name) ? htmlspecialchars($v->department_name) : '-'; ?></td>
									<td><?= isset($v->auditor_name) ? htmlspecialchars($v->auditor_name) : '-'; ?></td>
									<td class="text-center"><?= date('d/m/Y', strtotime($v->audit_date)); ?></td>
									<td class="text-center"><?= substr($v->start_time, 0, 5); ?> - <?= substr($v->end_time, 0, 5); ?></td>
									<td class="text-center">
										<?php if (!empty($has_audit[$v->schedule_id])) : ?>
											<a href="<?= site_url('pelaksanaan_audit/view/' . $v->schedule_id); ?>" class="btn btn-xs btn-icon btn-info" title="View">
												<i class="fa fa-eye"></i>
											</a>
											<a href="<?= site_url('pelaksanaan_audit/audit/' . $v->schedule_id); ?>" class="btn btn-xs btn-icon btn-warning" title="Edit Audit">
												<i class="fa fa-edit"></i>
											</a>
											<a href="<?= site_url('pelaksanaan_audit/print_pdf/' . $v->schedule_id); ?>" class="btn btn-xs btn-icon btn-danger" title="Print PDF" target="_blank">
												<i class="fa fa-file-pdf"></i>
											</a>
										<?php else : ?>
											<a href="<?= site_url('pelaksanaan_audit/audit/' . $v->schedule_id); ?>" class="btn btn-xs btn-icon btn-primary" title="Audit">
												<i class="fa fa-plus"></i>
											</a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	var table = $('#dtSchedules').DataTable({
		fixedHeader: true,
		processing: true,
		destroy: true,
		order: [[4, 'desc']],
		columnDefs: [{
			targets: 0,
			searchable: false,
			orderable: false
		}]
	});

	// Auto-number the "No" column after every draw (sort, page, search)
	table.on('order.dt search.dt draw.dt', function() {
		var i = 1;
		table.cells(null, 0, { search: 'applied', order: 'applied' }).every(function() {
			this.data(i++);
		});
	}).draw();
});
</script>
