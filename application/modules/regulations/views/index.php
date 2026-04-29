<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
					<div class="mt-4 float-right ">
						<a href="<?= base_url($this->uri->segment(1) . '/add'); ?>" class="btn btn-primary" data-toggle="tooltip" title="Add New">
							<i class="fa fa-plus mr-1"></i>Add New
						</a>
					</div>
				</div>
				<div class="card-body">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs nav-pills pb-3" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active btn-sm" id="Published-tab" data-toggle="tab" data-target="#Published" type="button" role="tab" aria-controls="Published" aria-selected="true">Published <span class="badge badge-circle badge-white text-primary ml-2"><?= count($data); ?></span></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link btn-sm" id="Draft-tab" data-toggle="tab" data-target="#Draft" type="button" role="tab" aria-controls="Draft" aria-selected="false">Draft <span class="badge badge-circle badge-white text-primary ml-2"><?= count($drafts); ?></span></button>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content mt-3">
						<div class="tab-pane fade active show" id="Published" role="tabpanel" aria-labelledby="Published-tab">
							<table id="example1" class="table table-bordered table-sm table-hover datatable">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th class="p-2" width="50">No.</th>
										<th class="p-2 text-left">Regulation Name</th>
										<th class="p-2 text-center">About</th>
										<th class="p-2" width="170">Subject</th>
										<th class="p-2" width="100">Last Update</th>
										<th class="p-2" width="100">Status</th>
										<th class="p-2" width="">Description</th>
										<th class="p-2" width="90">Action</th>
									</tr>
								</thead>
								<tbody>

									<?php if (isset($data) && $data) :
										$n = 0;
										foreach ($data as $dt) : $n++;
											$name = $dt->category_name . " " . $dt->nomenclature . (($dt->number) ? " No. " . $dt->number : '') . " " . $dt->year;
									?>
											<tr>
												<td class="p-2 text-center"><?= $n; ?></td>
												<td class="p-2"><?= $name; ?></td>
												<td class="p-2 text"><?= $dt->about; ?></td>
												<td class="p-2 text-center">
													<?php if (isset($ArrRegSjb[$dt->id])) : ?>
														<?php foreach ($ArrRegSjb[$dt->id] as $sbj) : ?>
															<span class="label label-danger label-inline mb-1"><?= $sbj->subject_name; ?></span>
														<?php endforeach; ?>
													<?php endif; ?>
												</td>
												<!-- <td class="p-2 text-center">
													<?php if (isset($ArrRegScp[$dt->id])) : ?>
														<?php foreach ($ArrRegScp[$dt->id] as $scp) : ?>
															<span class="label label-success label-inline mb-1"><?= $scp->scope_name; ?></span>
														<?php endforeach; ?>
													<?php endif; ?>
												</td> -->
												<td class="text-center"><?= ($dt->modified_at) ?: $dt->created_at; ?></td>
												<td class="text-center">
													<span class="label <?= ($dt->status == 'PUB') ? 'label-success' : (($dt->status == 'EXP') ? 'label-danger' : 'label-info'); ?> label-inline"><?= $status[$dt->status] ?></span>
												</td>
												<td class="text-center">
													<?= ($dt->revision_desc); ?>
													<?php if ($dt->status == 'CH'): ?>
														<button type="button" data-id="<?= $dt->regulation_relation; ?>" class="btn btn-link view"><?= $ArrReg[$dt->regulation_relation]; ?></button>
													<?php endif; ?>
												</td>
												<td class="text-center p-2">
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-search"></i></button>
													<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $dt->id); ?>" class="btn btn-xs btn-icon btn-warning edit" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-edit"></i></a>
													<button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="Draft" role="tabpanel" aria-labelledby="Draft-tab">
							<table id="example2" class="table table-bordered table-sm table-hover datatable">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th class="p-2" width="50">No.</th>
										<th class="p-2 text-left">Regulation Name</th>
										<th class="p-2 text-center">Year</th>
										<th class="p-2" width="150">Subject</th>
										<th class="p-2" width="170">Category</th>
										<th class="p-2" width="70">Last Update</th>
										<th class="p-2" width="90">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($drafts) && $drafts) :
										$n = 0;
										foreach ($drafts as $draft) : $n++; ?>
											<tr>
												<td class="p-2 text-center"><?= $n; ?></td>
												<td class="p-2"><?= $draft->name; ?></td>
												<td class="p-2 text-center"><?= $draft->year; ?></td>
												<td class="p-2 text-center">
													<?php if (isset($ArrRegSjb[$draft->id])) : ?>
														<?php foreach ($ArrRegSjb[$draft->id] as $sbj) : ?>
															<span class="label label-danger label-inline mb-1"><?= $sbj->subject_name; ?></span>
														<?php endforeach; ?>
													<?php endif; ?>
												</td>
												<td class="p-2 text-center">
													<?php if (isset($ArrRegScp[$draft->id])) : ?>
														<?php foreach ($ArrRegScp[$draft->id] as $scp) : ?>
															<span class="label label-success label-inline mb-1"><?= $scp->scope_name; ?></span>
														<?php endforeach; ?>
													<?php endif; ?>
												</td>
												<td class="text-center">
													<?= ($draft->modified_at) ?: $draft->created_at; ?>
												</td>
												<td class="text-center p-2">
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-search"></i></button>
													<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $draft->id); ?>" class="btn btn-xs btn-icon btn-warning edit" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-edit"></i></a>
													<button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button>
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
<!-- Modal -->
<div class="modal fade" id="modalView" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">View Standard</h5>
				<span class="close" data-dismiss="modal" aria-label="Close"></span>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('button[data-toggle="tab"]').on('shown.bs.tab', function(e) {
			$.fn.dataTable.tables({
				visible: true,
				api: true
			}).columns.adjust();
		});

		$('#example1,#example2').DataTable({
			orderCellsTop: false,
			// fixedHeader: true,
			// scrollX: true,
			// ordering: false,
			// info: false
		});

		$(document).on('click', '.view', function() {
			let id = $(this).data('id')
			if (id) {
				$('#modalView').modal('show')
				$('#modalView .modal-body').load(base_url + active_controller + 'view/' + id)
			} else {
				Swal.fire({
					title: 'Warning!',
					text: 'Data not valid',
					icon: 'warning',
					timer: 2000
				})
			}
		})

		$(document).on('click', '.delete', function() {
			let id = $(this).data('id')
			if (id) {
				Swal.fire({
					title: 'Confirm!',
					text: 'Are you sure you want to delete this data??',
					icon: 'question',
					showCancelButton: true
				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: base_url + active_controller + 'delete_regulation/' + id,
							type: 'POST',
							dataType: 'JSON',
							data: {
								id
							},
							success: function(res) {
								if (res.status == 1) {
									Swal.fire({
										title: 'Success!',
										icon: 'success',
										text: res.msg,
										timer: 3000
									}).then(() => {
										location.reload();
									})
								} else {
									Swal.fire({
										title: 'Warinng!',
										icon: 'warning',
										text: res.msg,
										timer: 3000
									})
								}
							},
							error: function(res) {
								Swal.fire({
									title: 'Error!',
									icon: 'error',
									text: 'Server timeout, error..',
									timer: 3000
								})
							}
						})
					}
				})
			}
		})

	})
</script>