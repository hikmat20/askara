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
								<th width="40">No.</th>
								<th width="120">ID</th>
								<th class="text-left">Company</th>
								<th>Lead Auditor</th>
								<th width="120">Audit Scope</th>
								<th width="120">Created Date</th>
								<th width="100" class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($programs)) foreach ($programs as $k => $v) : $k++; ?>
								<tr>
									<td class="text-center"><?= $k; ?></td>
									<td><?= $v->id; ?></td>
									<td><?= $v->company; ?></td>
									<td><?= $v->auditor_name; ?></td>
									<td class="text-center"><?= $v->audit_scope; ?></td>
									<td class="text-center"><?= date('d-m-Y', strtotime($v->created_at)); ?></td>
									<td class="text-center">
										<a href="<?= site_url('pelaksanaan_audit/schedules/' . $v->id); ?>" class="btn btn-xs btn-primary" title="Audit">
											<i class="fa fa-clipboard-check mr-1"></i> Audit
										</a>
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
		destroy: true
	});
});
</script>
