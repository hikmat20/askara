<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
					<div class="mt-4 float-right ">
						<a href="<?= base_url($this->uri->segment(1) . '/add'); ?>" class="btn btn-primary" data-toggle="tooltip" title="Add New Procedure">
							<i class="fa fa-plus mr-1"></i>Add New Procedure
						</a>
					</div>
				</div>
				<div class="card-body">

					<!-- Nav tabs -->
					<ul class="nav nav-tabs nav-fill font-weight-bold nav-success nav-sm nav-pills pb-3 mb-3" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link active p-2" id="draft-tab" data-toggle="tab" data-target="#draft" type="button" role="tab" aria-controls="draft" aria-selected="false">Draft <span class="badge bg-white badge-circle text-secondary ml-2"><?= count($dataDraft); ?></span></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link p-2" id="review-tab" data-toggle="tab" data-target="#review" type="button" role="tab" aria-controls="review" aria-selected="true">Review <span class="badge badge-circle bg-white text-warning ml-2"><?= count($dataRev); ?></span></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link p-2" id="Correction-tab" data-toggle="tab" data-target="#Correction" type="button" role="tab" aria-controls="Correction" aria-selected="true">Correction <span class="badge badge-circle bg-white text-danger ml-2"><?= count($dataCor); ?></span></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link p-2" id="Approval-tab" data-toggle="tab" data-target="#Approval" type="button" role="tab" aria-controls="Approval" aria-selected="true">Approval <span class="badge badge-circle bg-white text-info ml-2"><?= count($dataApv); ?></span></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link p-2" id="Revision-tab" data-toggle="tab" data-target="#Revision" type="button" role="tab" aria-controls="Revision" aria-selected="true">Revision <span class="badge badge-circle bg-white text-success ml-2"><?= count($dataRvi); ?></span></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link p-2" id="published-tab" data-toggle="tab" data-target="#published" type="button" role="tab" aria-controls="published" aria-selected="true">Published <span class="badge badge-circle bg-white text-primary ml-2"><?= count($dataPub); ?></span></a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link p-2" id="deletion-tab" data-toggle="tab" data-target="#deletion" type="button" role="tab" aria-controls="deletion" aria-selected="true">Deletion <span class="badge badge-circle bg-white text-danger ml-2"><?= count($dataDel); ?></span></a>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">

						<!-- Draft -->
						<div class="tab-pane fade active show" id="draft" role="tabpanel" aria-labelledby="draft-tab">
							<table id="example2" class="table datatable table-bordered table-sm table-hover datatable">
								<thead class="table-secondary">
									<tr>
										<th class="" width="40">No.</th>
										<th class="">Nama</th>
										<th class="">Nomor</th>
										<th class="text-center">Departement</th>
										<th class="text-center">Kelompok</th>
										<th class="text-center" width="130">Status</th>
										<th class="text-center" width="70">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataDraft) && $dataDraft) :
										$n = 0;
										foreach ($dataDraft as $draft) : $n++; ?>
											<tr>
												<td class="text-center"><?= $n; ?> </td>
												<td class="font-weight-bolder h6"><?= $draft->name; ?></td>
												<td class=""><?= $draft->nomor; ?></td>
												<td class=""><?= $draft->departement_name; ?></td>
												<td class=""><?= $draft->group_name; ?></td>
												<td class=""><?= $status[$draft->status]; ?></td>
												<td class="">
													<a href="<?= base_url($this->uri->segment(1) . '/printout/' . $draft->id); ?>" target="_blank" class="btn btn-xs btn-icon btn-light print" data-status="<?= $draft->status; ?>" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="Print Data"><i class="fa fa-print"></i></a>
													<div class="dropdown open d-inline">
														<button class="btn btn-success btn-xs btn-icon" type="button" id="triggerId" data-toggle="dropdown" title="Opsi" aria-haspopup="true" aria-expanded="false">
															<i class="fa fa-cog"></i>
														</button>
														<div class="dropdown-menu" aria-labelledby="triggerId">
															<a href="javascript:void(0)" class="dropdown-item view" data-status="<?= $draft->status; ?>" data-id="<?= $draft->id; ?>" title="View Data"><i class="fa fa-search mr-2 text-info"></i>View</a>
															<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $draft->id); ?>" class="dropdown-item" data-id="<?= $draft->id; ?>" title="Edit Data"><i class="fa fa-edit mr-2 text-warning"></i>Edit</a>
															<div class="dropdown-divider my-0"></div>
															<a href="javascript:void(0)" class="dropdown-item review" data-id="<?= $draft->id; ?>" title="Process to Review"><i class="fa fa-sync mr-2 text-primary"></i>Process to Review</a>
															<div class="dropdown-divider my-0"></div>
															<a href="javascript:void(0)" class="dropdown-item delete" data-id="<?= $draft->id; ?>" title="Delete Data"><i class="fa fa-trash mr-2 text-danger"></i>Delete</a>
														</div>
													</div>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>

						<!-- Review -->
						<div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
							<table id="tblReview" class="table datatable table-bordered table-sm table-condensed table-hover datatable">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th class="p-2" width="40">No.</th>
										<th class="p-2 text-left">Nama</th>
										<th class="p-2" width="130">Status</th>
										<th class="p-2" width="70">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataRev) && $dataRev) :
										$n = 0;
										foreach ($dataRev as $dt) : $n++; ?>
											<tr class="text-center">
												<td class="p-2"><?= $n; ?></td>
												<td class="p-2 text-left">
													<h6 class="my-0"><?= $dt->name; ?></h6>
												</td>
												<td class="p-2"><?= $status[$dt->status]; ?></td>
												<td class="p-2">
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-status="<?= $dt->status; ?>" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-search"></i></button>
													<button type="button" class="btn btn-xs btn-icon btn-light-danger cancle-review" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Cancel Review"><i class="fa fa-undo"></i></button>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>

						<!-- Correction -->
						<div class="tab-pane fade" id="Correction" role="tabpanel" aria-labelledby="Correction-tab">
							<table id="tblCorrection" class="table datatable table-bordered table-sm table-condensed table-hover datatable">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th class="p-2" width="40">No.</th>
										<th class="p-2 text-left">Nama</th>
										<th class="p-2" width="130">Status</th>
										<th class="p-2" width="70">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataCor) && $dataCor) :
										$n = 0;
										foreach ($dataCor as $dt) : $n++; ?>
											<tr class="text-center">
												<td class="p-2"><?= $n; ?></td>
												<td class="p-2 text-left">
													<h6 class="my-0"><?= $dt->name; ?></h6>
												</td>
												<td class="p-2"><?= $status[$dt->status]; ?></td>
												<td class="p-2">
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-status="<?= $dt->status; ?>" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-search"></i></button>
													<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $dt->id); ?>" class="btn btn-xs btn-icon btn-warning" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Edit Data"><i class="fa fa-edit"></i></a>
													<!-- <button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button> -->
													<button type="button" class="btn btn-icon btn-primary review btn-xs" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Process to Review"><i class="fa fa-sync"></i></button>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>

						<!-- Approval -->
						<div class="tab-pane fade" id="Approval" role="tabpanel" aria-labelledby="Approval-tab">
							<table id="tblApproval" class="table datatable table-bordered table-sm table-condensed table-hover datatable">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th class="p-2" width="40">No.</th>
										<th class="p-2 text-left">Nama</th>
										<th class="p-2" width="130">Status</th>
										<th class="p-2" width="70">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataApv) && $dataApv) :
										$n = 0;
										foreach ($dataApv as $dt) : $n++; ?>
											<tr class="text-center">
												<td class="p-2"><?= $n; ?></td>
												<td class="p-2 text-left">
													<h6 class="my-0"><?= $dt->name; ?></h6>
												</td>
												<td class="p-2"><?= $status[$dt->status]; ?></td>
												<td class="p-2">
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-status="<?= $dt->status; ?>" data-id="<?= $dt->id; ?>" title="View Data"><i class="fa fa-search"></i></button>
													<?php if ($dt->approval_id == $this->auth->user_id()) : ?>
														<!-- <a href="<?= base_url($this->uri->segment(1) . '/edit/' . $dt->id); ?>" class="btn btn-xs btn-icon btn-warning" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Edit Data"><i class="fa fa-edit"></i></a> -->
													<?php endif; ?>
													<!-- <button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button> -->
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>

						<!-- Revision -->
						<div class="tab-pane fade" id="Revision" role="tabpanel" aria-labelledby="Revision-tab">
							<table id="tblRevision" class="table datatable table-bordered table-sm table-condensed table-hover datatable">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th class="p-2" width="40">No.</th>
										<th class="p-2 text-left">Nama</th>
										<th class="p-2 text-left">Reason</th>
										<th class="p-2" width="130">Status</th>
										<th class="p-2" width="70">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataRvi) && $dataRvi) :
										$n = 0;
										foreach ($dataRvi as $dt) : $n++; ?>
											<tr class="text-center">
												<td class="p-2"><?= $n; ?></td>
												<td class="p-2 text-left">
													<h6 class="my-0"><?= $dt->name; ?></h6>
												</td>
												<td class="p-2 text-left">
													<?= $ArrReason[$dt->id]->note; ?>
												</td>
												<td class="p-2"><?= $status[$dt->status]; ?></td>
												<td class="p-2">
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-status="<?= $dt->status; ?>" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-search"></i></button>
													<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $dt->id); ?>" class="btn btn-xs btn-icon btn-warning" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Edit Data"><i class="fa fa-edit"></i></a>
													<button type="button" class="btn btn-xs btn-icon btn-success review" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Process to Review"><i class="fa fa-sync-alt"></i></button>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>

						<!-- Published -->
						<div class="tab-pane fade" id="published" role="tabpanel" aria-labelledby="published-tab">
							<table id="example1" class="table datatable table-bordered table-sm table-condensed table-hover datatable">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th class="p-2" width="40">No.</th>
										<th class="p-2 text-left">Nama</th>
										<th class="p-2" width="130">Status</th>
										<th class="p-2" width="70">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataPub) && $dataPub) :
										$n = 0;
										foreach ($dataPub as $dt) : $n++; ?>
											<tr class="text-center">
												<td class="p-2"><?= $n; ?></td>
												<td class="p-2 text-left">
													<h6 class="my-0"><?= $dt->name; ?></h6>
												</td>
												<td class="p-2"><?= $status[$dt->status]; ?></td>
												<td class="p-2">
													<a href="<?= base_url($this->uri->segment(1) . '/printout/' . $dt->id); ?>" target="_blank" class="btn btn-xs btn-icon btn-light print" data-status="<?= $dt->status; ?>" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Print Data"><i class="fa fa-print"></i></a>
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-status="<?= $dt->status; ?>" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-search"></i></button>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>

						<!-- Deletion -->
						<div class="tab-pane fade" id="deletion" role="tabpanel" aria-labelledby="deletion-tab">
							<table class="table datatable table-bordered table-sm table-condensed table-hover datatable">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th class="p-2" width="40">No.</th>
										<th class="p-2 text-left">Nama</th>
										<th class="p-2" width="130">Status</th>
										<th class="p-2" width="70">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataDel) && $dataDel) :
										$n = 0;
										foreach ($dataDel as $dt) : $n++; ?>
											<tr class="text-center">
												<td class="p-2"><?= $n; ?></td>
												<td class="p-2 text-left">
													<h6 class="my-0"><?= $dt->name; ?></h6>
												</td>
												<td class="p-2"><?= $status[$dt->status]; ?></td>
												<td class="p-2">
													<!-- <a href="<?= base_url($this->uri->segment(1) . '/printout/' . $dt->id); ?>" target="_blank" class="btn btn-xs btn-icon btn-light print" data-status="<?= $dt->status; ?>" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Print Data"><i class="fa fa-print"></i></a> -->
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-status="<?= $dt->status; ?>" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-search"></i></button>
													<button type="button" class="btn btn-xs btn-icon btn-danger delete" data-status="<?= $dt->status; ?>" data-id="<?= $dt->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button>
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

<div class="modal fade" id="modalForm" data-backdrop="static" data-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<form class="form-horiontal" id="form-input">
				<div class="modal-header">
					<h6 class="modal-title" id="staticBackdropLabel">Modal title</h6>
					<span type="button" onclick="$('#name').val('')" class="btn-close" data-dismiss="modal" aria-label="Close">
						<div class="fa fa-times"></div>
					</span>
				</div>
				<div class="modal-body overflow-auto">
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="save"><i class="fa fa-save"></i>Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="$('#name').val('')"><i class="fa fa-times"></i>Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modalView" data-keyboard="true" tabindex="-1" aria-labelledby="modal-view" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-scrollable" style="min-width: 90%;" role="document">
		<div class="modal-content" data-scroll="true" data-height="700">
			<form class="form-horiontal" id="form-input">
				<div class="modal-header">
					<h6 class="modal-title" id="modal-view">View Procedure</h6>
					<span type="button" onclick="$('#name').val('')" class="btn-close" data-dismiss="modal" aria-label="Close">
						<i class="fa fa-times"></i>
					</span>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="$('#name').val('')"><i class="fa fa-times"></i>Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modelId" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width:90%">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title">Modal title</h6>
				<button type="button" class="close" onclick="$('#content_modal').html('')" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="content_modal">
			</div>
		</div>
	</div>
</div>

<div class="modal fade px-0 py-0 modalViewImg" id="" tabindex="-1" role="dialog" aria-labelledby="modelTitleImg" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title" id="modelTitleImg">Modal title</h6>
				<span type="button" class="btn-close" aria-label="Close">
					<i class="fa fa-times"></i>
				</span>
			</div>
			<div class="modal-body overflow-auto">
			</div>
		</div>
	</div>
</div>

<div id="modalViewImg" class="modal-img">
	<div class="d-flex w-100 position-fixed justify-content-center" style="top:20px;z-index: inherit;">
		<button type="button" class="btn btn-xs shadow-xs border w-60px btn-icon bg-white mr-2 no-zoom" data-toggle="tooltip" title="Normal"><i class="fa fa-sync-alt"></i></button>
		<button type="button" class="btn btn-xs shadow-xs border w-60px btn-icon bg-white mr-2 zoom-out" data-toggle="tooltip" title="Zoom Out"><i class="fa fa-minus"></i></button>
		<button type="button" class="btn btn-xs shadow-xs border w-60px btn-icon bg-white mr-2 zoom-in" data-toggle="tooltip" title="Zoom In"><i class="fa fa-plus"></i></button>
		<button type="button" class="btn btn-xs shadow-xs border w-60px btn-icon bg-white mr-2" id="close-modal"><i class="fa fa-times"></i></button>
	</div>

	<img class="modal-content-img" id="img01">
</div>

<style>
	/* The Modal (background) */
	.modal-img {
		display: none;
		/* Hidden by default */
		position: fixed;
		/* Stay in place */
		z-index: 9999;
		/* Sit on top */
		padding-top: 100px;
		/* Location of the box */
		left: 0;
		top: 0;
		width: 100%;
		/* Full width */
		height: 100%;
		/* Full height */
		overflow: auto;
		/* Enable scroll if needed */
		background-color: rgb(0, 0, 0);
		/* Fallback color */
		background-color: rgba(0, 0, 0, 0.9);
		/* Black w/ opacity */
	}

	/* Modal Content (image) */
	.modal-img .modal-content-img {
		margin: auto;
		display: block;
		width: 100%;
		/* max-width: 700px; */
	}

	/* Caption of Modal Image */
	#caption {
		margin: auto;
		display: block;
		width: 80%;
		max-width: 700px;
		text-align: center;
		color: #ccc;
		padding: 10px 0;
		height: 150px;
	}

	/* Add Animation */
	.modal-img .modal-content-img,
	#caption {
		-webkit-animation-name: zoom;
		-webkit-animation-duration: 0.6s;
		animation-name: zoom;
		animation-duration: 0.6s;
	}

	@-webkit-keyframes zoom {
		from {
			-webkit-transform: scale(0)
		}

		to {
			-webkit-transform: scale(1)
		}
	}

	@keyframes zoom {
		from {
			transform: scale(0)
		}

		to {
			transform: scale(1)
		}
	}

	/* The Close Button */
	.close-modal {
		position: absolute;
		top: 15px;
		right: 35px;
		color: #f1f1f1;
		font-size: 40px;
		font-weight: bold;
		transition: 0.3s;
	}

	.close-modal:hover,
	.close-modal:focus {
		color: #bbb;
		text-decoration: none;
		cursor: pointer;
	}

	/* 100% Image Width on Smaller Screens */
	@media only screen and (max-width: 700px) {
		.modal-content-img {
			width: 100%;
		}
	}
</style>

<script>
	$(document).ready(function() {

		$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
			$.fn.dataTable.tables({
				visible: true,
				api: true
			}).columns.adjust();
		});

		$('.datatable').DataTable({
			orderCellsTop: false,
			// fixedHeader: true,
			// scrollX: true,
			// ordering: false,
			// info: false
		});

		$(document).on('click', '#add_new', function() {
			$('#modalForm').modal('show')
			let html = `
			
			`;
			$('.modal-body').html(html);
			load_tinymce('textarea')

		})

		$(document).on('click', '.edit', function() {
			let id = $(this).data('id')
			$('#modalForm').modal('show')
			$.ajax({
				url: siteurl + active_controller + 'edit/' + id,
				type: 'GET',
				dataType: 'JSON',
				success: function(result) {
					if (result) {
						let html = `
						<div class="form-group row">
							<label class="col-3">Name Procedure</label>
							<div class="col-9">
								<input type="hidden" name="id" id="id" class="form-control" value="` + result.id + `">
								<input type="text" name="name" id="name" class="form-control" required placeholder="Name Procedure" value="` + result.name + `">
								<small class="text-danger invalid-feedback">Name procedure</small>
							</div>
						</div>
						<div class="form-group">
							<label class="">Objektif Proses</label>
							<div class="">
								<input type="text" name="object" id="object" class="form-control" value="` + result.object + `" required placeholder="Objektif Proses" aria-describedby="helpId">
								<small class="text-danger invalid-feedback">Objektif Proses</small>
							</div>
						</div>
						<div class="form-group">
							<label class="">Definisi</label>
							<div class="">
								<input type="text" name="define" id="define" class="form-control" value="` + result.define + `" required placeholder="Definisi Proses" aria-describedby="helpId">
								<small class="text-danger invalid-feedback">Definisi Proses</small>
							</div>
						</div>
						<div class="form-group">
							<label class="">Performa Indikator</label>
							<div class="">
								<input type="text" name="performance" id="performance" class="form-control" value="` + result.performance + `" required placeholder="Performa Indikator" aria-describedby="helpId">
								<small class="text-danger invalid-feedback">Performa Indikator</small>
							</div>
						</div>
						<div class="form-group">
							<label class="">Ruang Lingkup</label>
							<div class="">
								<input type="text" name="scope" id="scope" class="form-control" value="` + result.scope + `" required placeholder="Ruang Lingkup" aria-describedby="helpId">
								<small class="text-danger invalid-feedback">Ruang Lingkup</small>
							</div>
						</div>
						<div class="form-group">
							<label for="sipocor" class="">SIPOCOR</label>
							<div class="">
								<textarea  name="sipocor" id="sipocor" class="form-control" placeholder="SIPOCOR"  aria-describedby="helpId">` + result.name + `</textarea>
								<small class="text-danger invalid-feedback">SIPOCOR</small>
							</div>
						</div>
						<div class="form-group">
							<label class="">Nomor</label>
							<div class="">
								<input type="text" name="number" id="number" class="form-control" value="` + result.number + `" required placeholder="Nomor" aria-describedby="helpId">
								<small class="text-danger invalid-feedback">Nomor</small>
							</div>
						</div>
						<div class="form-group">
							<label class="">PIC</label>
							<div class="">
								<input type="text" name="pic" id="pic" class="form-control" required placeholder="PIC" value="` + result.pic + `" aria-describedby="helpId">
								<small class="text-danger invalid-feedback">PIC</small>
							</div>
						</div>
						<div class="form-group">
							<label for="description" class="">Deskripsi</label>
							<div class="">
								<textarea name="description" id="description" class="form-control" placeholder="Deskripsi" aria-describedby="helpId">` + result.name + `</textarea>
								<small class="text-danger invalid-feedback">Deskripsi</small>
							</div>
						</div>
						<div class="form-group">
							<label class="">Dok. Terkait</label>
							<div class="">
								<input type="text" name="relate_doc" id="relate_doc" class="form-control" value="` + result.relate_doc + `" required placeholder="Dokumen terkait" aria-describedby="helpId"/>
								<small class="text-danger invalid-feedback">Dokumen terkait</small>
							</div>
						</div>
						`;
						$('.modal-body').html(html);

						// load_tinymce('textarea')
					} else {
						Swal.fire('Warning', 'Data not valid. Please try again!', 'warning', 3000)
					}

				},
				error: function() {
					Swal.fire('Error!', 'Server timeout. Please try again!', 'error', 5000)
				}
			})
		})

		$(document).on('click', '.view', function() {
			let id = $(this).data('id')
			let status = $(this).data('status')
			$.ajax({
				url: siteurl + active_controller + 'view/' + id + "/" + status,
				type: 'GET',
				success: function(result) {
					if (result) {
						$('#modalView').find('.modal-body').html(result);
						$('#modalView').modal('show')
					} else {
						Swal.fire('Warning', 'Data not valid. Please try again!', 'warning', 3000)
					}
				},
				error: function() {
					Swal.fire('Error!', 'Server timeout. Please try again!', 'error', 5000)
				}
			})
		})

		$(document).on('click', '.delete', function() {
			let id = $(this).data('id')
			Swal.fire({
				title: 'Are you sure to delete this data?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'Yes, Delete <i class="fa fa-trash text-white"></i>',
			}).then((value) => {
				if (value.isConfirmed) {
					$.ajax({
						url: siteurl + active_controller + 'delete_procedure/' + id,
						type: 'GET',
						dataType: 'JSON',
						success: function(result) {
							if (result.status == '1') {
								Swal.fire({
									title: 'Success!!',
									text: result.msg,
									icon: 'success',
									timer: 1500
								}).then(() => {
									location.reload()
								})

							} else {
								Swal.fire('Warning', "Can't delete data. Please try again!", 'warning', 2000)
							}
						},
						error: function() {
							Swal.fire('Error!', 'Server timeout. Please try again!', 'error', 3000)
						}
					})
				}
			})

		})

		$(document).on('click', '.review', function() {
			let id = $(this).data('id')
			Swal.fire({
				title: 'Are you sure to review this data?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Yes',
			}).then((value) => {
				if (value.isConfirmed) {
					$.ajax({
						url: siteurl + active_controller + 'review/' + id,
						type: 'GET',
						dataType: 'JSON',
						success: function(result) {
							if (result.status == '1') {
								Swal.fire({
									title: 'Success!!',
									text: result.msg,
									icon: 'success',
									timer: 1500
								}).then(() => {
									location.reload()
								})

							} else {
								Swal.fire('Warning', result.msg, 'warning', 2000)
							}
						},
						error: function() {
							Swal.fire('Error!', 'Server timeout. Please try again!', 'error', 3000)
						}
					})
				}
			})

		})

		$(document).on('click', '.cancle-review', function() {
			let id = $(this).data('id')
			Swal.fire({
				title: 'Are you sure to cancle review this data?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Yes, Cancel <i class="fa fa-undo text-white"></i>',
			}).then((value) => {
				if (value.isConfirmed) {
					$.ajax({
						url: siteurl + active_controller + 'cancel_review/' + id,
						type: 'GET',
						dataType: 'JSON',
						success: function(result) {
							if (result.status == '1') {
								Swal.fire({
									title: 'Success!!',
									text: result.msg,
									icon: 'success',
									timer: 1500
								}).then(() => {
									location.reload()
								})

							} else {
								Swal.fire('Warning', result.msg, 'warning', 2000)
							}
						},
						error: function() {
							Swal.fire('Error!', 'Server timeout. Please try again!', 'error', 3000)
						}
					})
				}
			})

		})

		$(document).on('submit', '#form-input', function(e) {
			e.preventDefault();
			let btn = $('#save')
			let formdata = new FormData($(this)[0])

			$.ajax({
				url: siteurl + active_controller + 'save',
				data: formdata,
				dataType: 'JSON',
				type: 'POST',
				processData: false,
				contentType: false,
				cache: false,
				beforeSend: function() {
					btn.attr('disabled', true)
					btn.html('<i class="spinner spinner-border-sm"></i>Loading...')
				},
				complete: function() {
					btn.attr('disabled', false)
					btn.html('<i class="fa fa-save"></i>Save')
				},
				success: function(result) {
					if (result.status == '1') {
						Swal.fire({
							title: 'Success!',
							text: result.msg,
							icon: 'success',
							timer: 2000
						})
						$('#modalForm').modal('hide')
						$('#name').val('')
						location.reload();
					} else {
						Swal.fire({
							title: 'Warning!',
							text: result.msg,
							icon: 'warning',
							timer: 3000
						})
					}
				},
				error: function() {
					Swal.fire({
						title: 'Error!',
						text: 'Server timeout, becuase error. Please try again!',
						icon: 'error',
						timer: 5000
					})
				}
			})
		})

		$(document).on('click', '.view-form', function() {
			const id = $(this).data('id')
			if (id) {
				$('.modal-title').html('View Form')
				$('#content_modal').load(siteurl + active_controller + 'view_form/' + id)
				$('#modelId').modal('show')
				$('.modal-dialog').css('max-width', '')
			} else {
				Swal.fire('Warning!!', 'Not available data to process', 'waring', 2000);
			}
		})

		$(document).on('click', '.view-guide', function() {
			const id = $(this).data('id')
			if (id) {
				$('.modal-title').html('View IK')
				$('#content_modal').load(siteurl + active_controller + 'view_guide/' + id)
				$('#modelId').modal('show')
			} else {
				Swal.fire('Warning!!', 'Not available data to process', 'waring', 2000);
			}
		})

		/* PREVIEW IMAGE */
		$(document).on('click', '.view-image', function() {
			const urlImg = $(this).parents('.dropzone-wrapper').find('img').attr('src')
			const img = "<img src='" + urlImg + "' class='img-'>"
			// $('#modalViewImg').modal('show')
			// $('#modalViewImg .modal-body').html(img)
			// var reader = new FileReader();


			// reader.onload = function(e) {
			// 	console.log(e)
			// 	var htmlPreview = '<img width="100" src="' + e.target.result + '" />';
			// 	boxZone.append(htmlPreview);
			// };

			// Get the modal
			var modal = document.getElementById("modalViewImg");

			// Get the image and insert it inside the modal - use its "alt" text as a caption
			// var img = document.getElementById("myImg");
			var modalImg = document.getElementById("img01");
			// var captionText = document.getElementById("caption");
			// img.onclick = function() {
			modal.style.display = "block";
			modalImg.src = urlImg;
			// 	captionText.innerHTML = this.alt;
			// }

			// Get the <span> element that closes the modal
			var btn = document.getElementById("close-modal");

			// When the user clicks on <span> (x), close the modal
			btn.onclick = function() {
				modal.style.display = "none";
			}
		})

		/* ZOOM-IN & ZOOM-OUT */

		let n = 100
		$(document).on('click', '.zoom-in', function() {
			n = n + 5
			$('.modal-img img.modal-content-img ').css('width', n + '%')
		})
		$(document).on('click', '.zoom-out', function() {
			n = n - 5
			$('.modal-img img.modal-content-img ').css('width', n + "%")
		})
		$(document).on('click', '.no-zoom', function() {
			n = 100
			$('.modal-img img.modal-content-img ').css('width', n + "%")
		})

	})
</script>