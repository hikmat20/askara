<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
				</div>
				<div class="card-body">
					<div class="tab-content mt-3">
						<div class="tab-pane fade active show" role="tabpanel">
							<table id="table-data" class="table table-bordered table-striped table-sm table-hover">
								<thead class="text-center table-light">
									<tr>
										<th width="3%">No</th>
										<th>Tanggal</th>
										<th>Proses</th>
										<th>Department</th>
										<th>Auditor</th>
										<th>Temuan</th>
										<th>Kategori</th>
										<th>Status</th>
										<th width="180">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($data) && $data) :
										$n = 0;
										foreach ($data as $dt) : $n++; ?>
											<tr>
												<td class="text-center"><?= $n; ?></td>
												<td class="text-center"><?= date('d-m-Y', strtotime($dt->audit_date)); ?></td>
												<td><?= strip_tags($dt->process_name); ?></td>
												<td><?= $dt->department_name; ?></td>
												<td><?= $dt->auditor_name; ?></td>
												<td class="text-center"><?= $dt->temuan_count; ?></td>
												<td><?= $dt->kategori; ?></td>
												<td class="text-center">
													<?php if (empty($dt->ca_id)) : ?>
														<span class="label label-lg label-light-warning label-inline">Draft</span>
													<?php elseif ($dt->status_ca == 'draft') : ?>
														<span class="label label-lg label-light-warning label-inline">Draft</span>
													<?php elseif ($dt->status_ca == 'waiting_approval') : ?>
														<span class="label label-lg label-light-info label-inline">Waiting Approval</span>
													<?php elseif ($dt->status_ca == 'approved') : ?>
														<span class="label label-lg label-light-success label-inline">Closed</span>
													<?php endif; ?>
												</td>
												<td class="text-center">
													<?php if (empty($dt->ca_id)) : ?>
														<a href="<?= site_url('corrective_action/form/' . $dt->pelaksanaan_id); ?>" class="btn btn-sm btn-success" title="Corrective Action">
															<i class="fa fa-plus mr-1"></i>Corrective Action
														</a>
													<?php elseif ($dt->status_ca == 'draft') : ?>
														<a href="<?= site_url('corrective_action/view/' . $dt->pelaksanaan_id); ?>" class="btn btn-sm btn-info" title="View">
															<i class="fa fa-eye"></i>
														</a>
														<a href="<?= site_url('corrective_action/form/' . $dt->pelaksanaan_id); ?>" class="btn btn-sm btn-warning" title="Edit">
															<i class="fa fa-edit"></i>
														</a>
													<?php elseif ($dt->status_ca == 'waiting_approval' || $dt->status_ca == 'approved') : ?>
														<a href="<?= site_url('corrective_action/view/' . $dt->pelaksanaan_id); ?>" class="btn btn-sm btn-info" title="View">
															<i class="fa fa-eye"></i>
														</a>
													<?php endif; ?>
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

<script>
	$(document).ready(function() {
		$('#table-data').DataTable({
			ordering: false
		});
	});
</script>
