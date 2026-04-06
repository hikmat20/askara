<style>
	.record-item:hover td,
	.record-item:hover td>span {
		color: #0bb783;
	}


	.bg-hover-light-o:hover {
		-webkit-transition: all .15s ease;
		transition: all .15s ease;
		background-color: rgba(243, 246, 249, .1) !important
	}

	.bg-light-o-15.nav-link.active {
		background-color: rgba(255, 255, 255, 1) !important;
		color: #000000 !important;
	}

	.bg-light-o-15.nav-link.active i {
		color: #5d5d5dff !important;
	}


	.bg-light-o-15 {
		background-color: rgba(243, 246, 249, .15)
	}

	.nav.nav-pills.nav-light-white .nav-item .nav-link.active .badge {
		background-color: rgba(232, 178, 0, 1) !important;
		color: #ffffffff !important;
		opacity: 1 !important;
	}

	.border-light-o-10 {
		border-color: rgba(243, 246, 249, .1) !important
	}

	.border-warning-o-10 {
		border-color: rgba(212, 178, 6, 1)
	}
</style>
<div class="content d-flex flex-column flex-column-fluid p-0">
	<div class="d-flex flex-column-fluid justify-content-between align-items-top">
		<div class="container">
			<div class="d-flex align-items-center flex-wrap py-3">
				<a href="<?= base_url('dashboard'); ?>">
					<h4 class="text-dark font-weight-bold my-1 mr-2"><i class="fa fa-home"></i></h4>
				</a>
				<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
					<li class="breadcrumb-item text-muted">
						<a href="<?= base_url('list/procedures/'); ?>" class="text-muted">PROSEDUR, FORM, IK DAN RECORD</a>
					</li>
					<li class="breadcrumb-item text-muted">
						<span class="text-muted"><?= $procedures[0]->name; ?></span>
					</li>
				</ul>
			</div>
			<h1 class="text-warning fa-3x mb-5"><?= $procedures[0]->name; ?></h1>
			<div class="row mb-5">
				<!-- <div class="col-md-4">
					<input type="text" name="serarch" id="search" placeholder="Pencarian" class="form-control rounded form-control-sm">
				</div> -->
			</div>
			<ul class="nav nav-pills nav-light-white" id="myTab2" role="tablist">
				<li class="nav-item mr-2">
					<a class="rounded-bottom-0 bg-light-o-15 bg-hover-light-o border-light-o-10 border-x border-top nav-link active" id="tab_procedure" data-toggle="tab" href="#data_procedure">
						<span class="mr-2"><i class="fa fa-file-alt"></i></span>
						<span class="">Prosedur</span>
						<span class="ml-2">
							<strong class="badge bg-warning-o-70 text-warning text-bolder"><?= (isset($procedures)) ? count($procedures) : '0'; ?></strong>
						</span>
					</a>
				</li>
				<li class="nav-item mr-2">
					<a class="rounded-bottom-0 bg-light-o-15 bg-hover-light-o border-light-o-10 border-x border-top nav-link" id="tab_form" data-toggle="tab" href="#data_form">
						<span class="mr-2"><i class="fa fa-file-alt"></i></span>
						<span class="">Form </span>
						<span class="ml-2">
							<strong class="badge bg-warning-o-70 text-warning text-bolder"><?= (isset($forms)) ? count($forms) : '0'; ?></strong>
						</span>
					</a>
				</li>
				<li class="nav-item mr-2">
					<a class="rounded-bottom-0 bg-light-o-15 bg-hover-light-o border-light-o-10 border-x border-top nav-link" id="tab_guide" data-toggle="tab" href="#data_guide">
						<span class="mr-2"><i class="fa fa-file-alt"></i></span>
						<span class="">IK</span>
						<span class="ml-2">
							<strong class="badge bg-warning-o-70  text-warning text-bolder"><?= (isset($ik)) ? count($ik) : '0'; ?></strong>
						</span>
					</a>
				</li>
				<li class="nav-item mr-2">
					<a class="rounded-bottom-0 bg-light-o-15 bg-hover-light-o border-light-o-10 border-x border-top nav-link" id="tab_record" data-toggle="tab" href="#data_record">
						<span class="mr-2"><i class="fa fa-file-alt"></i></span>
						<span class="">Records</span>
						<span class="ml-2">
							<strong class="badge bg-warning-o-70  text-warning text-bolder"><?= (isset($countRecords)) ? ($countRecords) : '0'; ?></strong>
						</span>
					</a>
				</li>
			</ul>
			<div class="card rounded-top-0 border-0" style="background-color: zrgba(255,255,255,0.85);">
				<div class="card-body p-5">
					<div class="tab-content" id="myTabContent2">
						<div class="tab-pane fade active show" id="data_procedure" role="tabpanel" aria-labelledby="tab_procedure">
							<table class="table datatable table-hover">
								<thead>
									<tr class="">
										<th class="" width="15px">No.</th>
										<th class="" width="200">Number</th>
										<th class="">File Name</th>
										<th class="text-center" width="200">Published At</th>
										<th class="text-center" width="100">Revision</th>
										<th class="text-center" width="50px">View</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($procedures)) :
										$no = 0;
										foreach ($procedures as $pro) : $no++; ?>
											<tr class="">
												<td class="align-middle"><?= $no; ?></td>
												<td><?= $pro->nomor; ?></td>
												<td class="align-middle d-flex align-items-center">
													<i class="fa fa-file-alt text-primary font-size-h2 mr-2"></i>
													<strong> <?= $pro->name; ?></strong>
												</td>
												<td class="text-center"><?= $pro->published_at; ?></td>
												<td class="text-center"><?= $pro->revision; ?></td>
												<td class="text-center" style="vertical-align: middle;">
													<button type="button" class="btn btn-icon btn-xs shadow-xs btn-info view-procedure" data-id="<?= $pro->id; ?>" data-toggle="tooltip" data-theme="dark" title="View Document"><i class="fa fa-eye"></i></button>
												</td>
											</tr>
										<?php endforeach;
									else : ?>
										<tr>
											<td colspan="6" class="text-center h4"><i>No data available</i></td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="data_form" role="tabpanel" aria-labelledby="tab_form">
							<table class="table datatable table-hover">
								<thead>
									<tr class="">
										<th class="" width="15px">No.</th>
										<th class="" width="200">Number</th>
										<th class="">File Name</th>
										<th class="text-center" width="100">Type</th>
										<th class="text-center" width="100">Revision</th>
										<th class="text-center" width="100">Issue Date</th>
										<th class="text-center" width="100">Effective Date</th>
										<th class="text-center" width="80">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($forms)) :
										$no = 0;
										foreach ($forms as $frm) : $no++; ?>
											<tr class="view-orm" data-id="<?= $frm->id; ?>">
												<td class="text-dark"><?= $no; ?></td>
												<td class="text-dark align-middle"><strong><?= $frm->number; ?></strong></td>
												<td class="align-middle">
													<strong><?= $frm->name; ?></strong>
													<p class="text-muted small mb-0">Last update : <?= ($frm->modified_at) ? date('d M Y', strtotime($frm->modified_at)) : (isset($frm->created_at) ? date('d M Y', strtotime($frm->created_at)) : '~'); ?></p>
												</td>
												<td class="text-center align-middle"><i class="fa <?= extention($frm->ext) ?>"></i>
													<p class="small text-muted mb-0"><?= ($frm->size) ? formatBytes($frm->size) : '~'; ?></p>
												</td>
												<td class="text-center align-middle">Rev <?= $frm->revision_number; ?></td>
												<td class="text-center align-middle"><?= ($frm->issue_date) ? date('d M Y', strtotime($frm->issue_date)) : '-'; ?></td>
												<td class="text-center align-middle"><?= ($frm->effective_date) ? date('d M Y', strtotime($frm->effective_date)) : '-'; ?></td>
												<td class="text-center" style="vertical-align: middle;">
													<button type="button" class="btn btn-icon btn-xs btn-light-info view-form" data-id="<?= $frm->id; ?>" data-toggle="tooltip" data-theme="dark" title="View Document"><i class="fa fa-eye"></i></button>
													<button type="button" class="btn btn-icon btn-xs btn-light-primary download-form" data-id="<?= $frm->id; ?>" data-toggle="tooltip" data-theme="dark" title="Download Document"><i class="fa fa-download"></i></button>
												</td>
											</tr>
										<?php endforeach;
									else : ?>
										<tr>
											<td colspan="2" class="text-center h4"><i>No data available</i></td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="data_guide" role="tabpanel" aria-labelledby="tab_guide">
							<table class="table datatable table-hover">
								<thead>
									<tr class="">
										<th class="" width="15px">No.</th>
										<th class="" width="200">Number</th>
										<th class="">File Name</th>
										<th class="text-center" width="100px">Type</th>
										<th class="text-center" width="100px">Revision</th>
										<th class="text-center" width="100px">Issue Date</th>
										<th class="text-center" width="100px">Effective Date</th>
										<th class="text-center" width="80px">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($ik)) :
										$no = 0;
										foreach ($ik as $i) : $no++; ?>
											<tr class="view-" data-id="<?= $i->id; ?>">
												<td class="text-dark"><?= $no; ?></td>
												<td class="text-dark align-middle"><strong><?= $i->number; ?></strong></td>
												<td class="align-middle">
													<strong><?= $i->name; ?></strong>
													<p class="text-muted small mb-0">Last update : <?= ($i->modified_at) ? date('d M Y', strtotime($i->modified_at)) : (isset($i->created_at) ? date('d M Y', strtotime($i->created_at)) : '~'); ?></p>
												</td>
												<td class="text-center align-middle"><i class="fa <?= extention($i->ext) ?>"></i>
													<p class="small text-muted mb-0"><?= ($i->size) ? formatBytes($i->size) : '~'; ?></p>
												</td>
												<td class="text-center align-middle">Rev <?= $i->revision_number; ?></td>
												<td class="text-center align-middle"><?= ($i->issue_date) ? date('d M Y', strtotime($i->issue_date)) : '-'; ?></td>
												<td class="text-center align-middle"><?= ($i->effective_date) ? date('d M Y', strtotime($i->effective_date)) : '-'; ?></td>
												<td class="text-center" style="vertical-align: middle;">
													<button type="button" class="btn btn-icon btn-xs btn-light-info view-wi" data-id="<?= $i->id; ?>" data-toggle="tooltip" data-theme="dark" title="View Document"><i class="fa fa-eye"></i></button>
												</td>
											</tr>
										<?php endforeach;
									else : ?>
										<tr>
											<td colspan="2" class="text-center h4"><i>No data available</i></td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="data_record" role="tabpanel" aria-labelledby="tab_record">
							<div id="data-records">
								<!-- Nav tabs -->
								<ul class="nav pb-2 nav-success nav-tabs nav-pills" id="navId">
									<li class="nav-item mr-2">
										<a href="javascript:void(0)" id="home" data-id="" data-procedure="<?= $procedures[0]->id; ?>" class="nav-link py-2 px-3">
											<i class="fa fa-home mr-2"></i>
											Home
										</a>
									</li>
									<li class="nav-item mr-2">
										<a href="javascript:void(0)" id="back" data-parent="<?= isset($parent_id) ? $parent_id : ''; ?>" data-procedure="<?= $procedures[0]->id; ?>" class="disabled nav-link py-2 px-3">
											<i class="fa fa-arrow-up mr-2"></i>
											Up Folder
										</a>
									</li>
									<li class="nav-item mr-2">
										<a href="javascript:void(0)" id="refresh" data-parent="<?= isset($parent_id) ? $parent_id : ''; ?>" data-procedure="<?= $procedures[0]->id; ?>" class="nav-link py-2 px-3">
											<i class="fa fa-sync-alt mr-2"></i>
											Refresh
										</a>
									</li>
								</ul>
								<table class="table table-condensed table-hover">
									<thead>
										<tr class="">
											<th class="py-1">File Name</th>
											<th class="py-1 text-right" width="150">Last Update</th>
											<th class="py-1 text-center" width="50px"></th>
										</tr>
									</thead>
									<tbody>
										<?php if (isset($records)) :
											$no = 0;
											foreach ($records as $lsRec) : $no++; ?>
												<tr class="cursor-pointer text-dark record-item" data-id="<?= $lsRec->id; ?>" data-procedure="<?= $procedures[0]->id; ?>">
													<td class=" d-flex align-items-center h6">
														<?php if ($lsRec->flag_type == 'FOLDER') : ?>
															<i class="fa fa-folder text-warning font-size-h2 mr-3"></i>
														<?php else : ?>
															<i class="fa fa-file-alt text-info font-size-h2 mr-3"></i>
														<?php endif; ?>
														<span class=""><?= $lsRec->name; ?></span>
													</td>
													<td style="vertical-align: middle;" class="text-right pt-1"><?= ($lsRec->modified_at) ?: $lsRec->created_at; ?></td>
													<td class="text-center pt-1" style="vertical-align: middle;">
														<?php if ($lsRec->flag_type == 'FILE') : ?>
															<button type="button" class="btn btn-icon btn-xs shadow-xs btn-info view-record" data-id="<?= $lsRec->id; ?>" data-toggle="tooltip" data-theme="dark" title="View Document"><i class="fa fa-eye"></i></button>
														<?php endif; ?>
													</td>
												</tr>
											<?php endforeach;
										else : ?>
											<tr>
												<td colspan="2" class="text-center h4"><i>No data available</i></td>
											</tr>
										<?php endif; ?>
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

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document" style="min-width: 75%;">
		<div class="modal-content" data-scroll="true" data-height="700">
			<div class="modal-header py-3">
				<h5 class="modal-title">View Document</h5>
				<button type="button" class="btn btn-xs btn-icon btn-light" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fa fa-times text-secondary" aria-hidden="true"></i></span>
				</button>
			</div>
			<div class="modal-body pt-1  py-5" id="data-file">
				<h3 class="text-center">File not found</h3>
			</div>
			<div class="modal-footer py-2">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalViewForm" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<div class="modal-content" data-height="700">
			<div class="modal-header">
				<h5 class="modal-title">View Document</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body p-0" id="data-file">
				<h3 class="text-center">File not found</h3>
			</div>
			<div class="modal-footer py-2">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document" style="min-width:90%">
		<div class="modal-content" data-scroll="true" data-height="700">
			<div class="modal-header">
				<h5 class="modal-title">View Document</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pt-1">
				File not found
			</div>
			<div class="modal-footer py-2">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div id="modalViewImg" class="modal-img">
	<div class="d-flex w-100 position-fixed justify-content-center" style="top:20px;z-index: inherit;">
		<button type="button" class="btn btn-sm shadow-xs border w-60px btn-icon bg-white mr-2 no-zoom" title="Normal"><i class="fa fa-sync-alt"></i></button>
		<button type="button" class="btn btn-sm shadow-xs border w-60px btn-icon bg-white mr-2 zoom-out" title="Zoom Out"><i class="fa fa-minus"></i></button>
		<button type="button" class="btn btn-sm shadow-xs border w-60px btn-icon bg-white mr-2 zoom-in" title="Zoom In"><i class="fa fa-plus"></i></button>
		<button type="button" class="btn btn-sm shadow-xs border w-60px btn-icon bg-white mr-2" id="close-modal"><i class="fa fa-times"></i></button>
	</div>

	<img class="modal-content-img" id="img01">
</div>


<style>
	span p {
		margin-bottom: 0;
	}

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
	function show(id) {
		$('#modelId').modal('show')
		$('#data-file').load(siteurl + active_controller + 'show/' + id)
	}

	$(document).ready(function() {

		/* Datatable */

		$('.datatable').DataTable({
			stateSave: true,
			order: [
				[0, 'desc']
			],
		});

		$(document).on('click', '.download-form', function() {
			const id = $(this).data('id')

			if (id) {
				window.open(siteurl + active_controller + '/download_form/' + id, '_blank');
			}

		})


		$(document).on('click', '.view-procedure', function() {
			const id = $(this).data('id') || ''
			if (id) {
				$('#modelId').modal('show')
				$('#modelId .modal-body').load(siteurl + active_controller + 'view_procedure/' + id)
			}
		})
	})

	$(document).ready(function() {
		$(document).on('click', '.view-record', function() {
			const id = $(this).data('id') || ''
			if (id) {
				$('#modelId').modal('show')
				$('#modelId .modal-body').load(siteurl + active_controller + 'view_record/' + id)
			}
		})
	})

	$(document).on('click', '.view-form', function() {
		const id = $(this).data('id') || ''
		if (id) {
			$('#modalViewForm').modal('show')
			$('#modalViewForm .modal-body').load(siteurl + active_controller + 'view_form/' + id)
		}
	})

	$(document).on('click', '.view-wi', function() {
		const id = $(this).data('id') || ''
		if (id) {
			$('#modalViewForm').modal('show')
			$('#modalViewForm .modal-body').load(siteurl + active_controller + 'view_guide/' + id)
		}
	})


	$(document).on('click', '.view-guide-2', function() {
		const id = $(this).data('id')
		if (id) {
			$('#modelId2 .modal-title').html('View IK')
			$('#modelId2 .modal-body').load(siteurl + active_controller + 'view_guide/' + id)
			$('#modelId2').modal('show')
		} else {
			Swal.fire('Warning!!', 'Not available data to process', 'waring', 2000);
		}
	})

	$(document).on('click', '.view-form-2', function() {
		const id = $(this).data('id')
		if (id) {
			$('#modelId2 .modal-title').html('View Form')
			$('#modelId2 .modal-body').load(siteurl + active_controller + 'view_form/' + id)
			$('#modelId2').modal('show')
		} else {
			Swal.fire('Warning!!', 'Not available data to process', 'waring', 2000);
		}
	})

	$(document).on('click', '#home', function() {
		const procedure_id = $(this).data('procedure')
		const url = siteurl + active_controller + 'getRecords/home/' + procedure_id;
		$('#data-records').load(url)
		// alert(url)
	})

	$(document).on('click', '#back', function() {
		const id = $(this).data('id')
		const procedure_id = $(this).data('procedure')
		const url = siteurl + active_controller + 'getRecords/back/' + procedure_id + "/" + id
		$('#data-records').load(url)
		// alert(url)
	})

	$(document).on('click', '#refresh', function() {
		const id = $(this).data('id') || ''
		const procedure_id = $(this).data('procedure')
		const url = siteurl + active_controller + 'getRecords/refresh/' + procedure_id + "/" + id
		$('#data-records').load(url)
		// alert(url)
	})

	$(document).on('click', '.record-item', function() {
		const id = $(this).data('id')
		const procedure_id = $(this).data('procedure')
		const url = siteurl + active_controller + 'getRecords/find/' + procedure_id + '/' + id
		$('#data-records').load(url)
		// alert(url)
	})

	/* PREVIEW IMAGE */
	$(document).on('click', '.view-image', function() {
		const urlImg = $(this).parents('.dropzone-wrapper').find('img').attr('src')
		const img = "<img src='" + urlImg + "' class='img-'>"
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
		var span = document.getElementById("close-modal");

		// When the user clicks on <span> (x), close the modal
		span.onclick = function() {
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
</script>