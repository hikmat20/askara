<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i>Record Audit</h2>
				</div>
				<div class="card-body">
					<div class="tab-content mt-3">
						<div class="tab-pane fade active show" role="tabpanel">
							<div class="table-responsive">
							<table id="table-data" class="table table-bordered table-striped table-sm table-hover">
								<thead class="text-center table-light">
									<tr>
										<th width="3%">No</th>
										<th>Tanggal</th>
										<th>Proses</th>
										<th>Department - Company</th>
										<th>Auditor</th>
										<th>Temuan</th>
										<th>Kategori</th>
										<th width="100">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($data) && $data) :
										$n = 0;
										foreach ($data as $dt) : $n++; ?>
											<tr>
												<td class="text-center"><?= $n; ?></td>
												<td class="text-center"><?= date('d/m/Y', strtotime($dt->audit_date)); ?></td>
												<td><?= strip_tags($dt->process_name); ?></td>
												<td><?= $dt->department_name; ?></td>
												<td><?= $dt->auditor_name; ?></td>
												<td class="text-center"><?= $dt->temuan_count; ?></td>
												<td><?= $dt->kategori; ?></td>
												<td class="text-center">
													<a href="<?= site_url('corrective_action/record_view/' . $dt->ca_id); ?>" class="btn btn-sm btn-info" title="View">
														<i class="fa fa-eye"></i> View
													</a>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#table-data').DataTable();
	});
</script>
