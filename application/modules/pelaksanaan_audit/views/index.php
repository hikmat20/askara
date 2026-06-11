<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> text-primary mr-2"></i><?= $title; ?></h2>
				</div>
				<div class="card-body">
					<table id="dtTable" class="table table-bordered table-sm table-condensed table-hover">
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
									<td><?= !empty($v->process_name) ? strip_tags($v->process_name) : htmlspecialchars($v->process_name_free); ?></td>
									<td><?= isset($v->department_name) ? $v->department_name : '-'; ?></td>
									<td><?= isset($v->auditor_name) ? $v->auditor_name : '-'; ?></td>
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

<script>
$(document).ready(function() {
	$('#dtTable').DataTable({
		fixedHeader: true,
		processing: true,
		destroy: true,
		order: [[4, 'desc']]
	});
});
</script>
