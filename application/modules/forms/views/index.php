<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $template['page_icon']; ?> mr-2"></i><?= $template['title']; ?></h2>
					<div class="mt-4 float-right ">
						<a href="<?= base_url($this->uri->segment(1) . '/add'); ?>" class="btn btn-primary" data-toggle="tooltip" title="Add New Form">
							<i class="fa fa-plus mr-1"></i>Add New Form
						</a>
					</div>
				</div>
				<div class="card-body py-3">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs nav-pills pb-3" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active btn-sm" id="Draft-tab" data-toggle="tab" data-target="#Draft" type="button" role="tab" aria-controls="Draft" aria-selected="false">Draft <span class="badge badge-circle badge-white text-primary ml-2"><?= count($dataDraft); ?></span></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link btn-sm" id="Review-tab" data-toggle="tab" data-target="#Review" type="button" role="tab" aria-controls="Review" aria-selected="false">Review <span class="badge badge-circle badge-white text-primary ml-2"><?= count($dataReview); ?></span></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link btn-sm" id="Correction-tab" data-toggle="tab" data-target="#Correction" type="button" role="tab" aria-controls="Correction" aria-selected="false">Correction <span class="badge badge-circle badge-white text-primary ml-2"><?= count($dataCorrection); ?></span></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link btn-sm" id="Approval-tab" data-toggle="tab" data-target="#Approval" type="button" role="tab" aria-controls="Approval" aria-selected="false">Approval <span class="badge badge-circle badge-white text-primary ml-2"><?= count($dataApproval); ?></span></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link btn-sm" id="Revision-tab" data-toggle="tab" data-target="#Revision" type="button" role="tab" aria-controls="Revision" aria-selected="false">Revision <span class="badge badge-circle badge-white text-primary ml-2"><?= count($dataRevision); ?></span></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link btn-sm" id="Published-tab" data-toggle="tab" data-target="#Published" type="button" role="tab" aria-controls="Published" aria-selected="true">Published <span class="badge badge-circle badge-white text-primary ml-2"><?= count($dataPublished); ?></span></button>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content mt-3">
						<div class="tab-pane fade show active" id="Draft" role="tabpanel" aria-labelledby="Draft-tab">
							<table class="datatable table table-bordered border table-sm table-hover datatable">
								<thead class="table-light">
									<tr>
										<th class="p-2" width="50">No.</th>
										<th class="p-2">Name</th>
										<th class="p-2">Number</th>
										<th class="p-2">Procedure</th>
										<th class="p-2 text-center">Issue Date</th>
										<th class="p-2 text-center">Effective Date</th>
										<th class="p-2 text-center" width="100">Rev. Number</th>
										<!-- <th class="p-2 text-center">Status</th> -->
										<th class="p-2 nosort text-center" width="80">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataDraft) && $dataDraft) :
										$n = 0;
										foreach ($dataDraft as $draft) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $draft->name; ?></td>
												<td><?= $draft->number; ?></td>
												<td><?= $draft->procedure_name; ?></td>
												<td class="text-center"><?= date("d M Y",strtotime($draft->issue_date)); ?></td>
												<td class="text-center"><?= date("d M Y",strtotime($draft->effective_date)); ?></td>
												<td class="text-center"><?= $draft->revision_number; ?></td>
												<!-- <td><?= $status[$draft->status]; ?></td> -->
												<td class="text-center">
													<button type="button" class="btn btn-xs	 btn-icon btn-info view" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<div class="dropdown open d-inline">
														<button class="btn btn-success btn-xs btn-icon" type="button" id="triggerId_draft_<?= $draft->id; ?>" data-toggle="dropdown" title="Opsi" aria-haspopup="true" aria-expanded="false">
															<i class="fa fa-cog"></i>
														</button>
														<div class="dropdown-menu" aria-labelledby="triggerId_draft_<?= $draft->id; ?>">
															<a href="<?= base_url('forms/download/' . $draft->id); ?>" class="dropdown-item" title="Download Form"><i class="fa fa-download mr-2 text-success"></i>Download</a>
															<div class="dropdown-divider my-0"></div>
															<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $draft->id); ?>" class="dropdown-item edit" data-id="<?= $draft->id; ?>" title="Edit Data"><i class="fa fa-edit mr-2 text-warning"></i>Edit</a>
															<div class="dropdown-divider my-0"></div>
															<a href="javascript:void(0)" class="dropdown-item toReview" data-id="<?= $draft->id; ?>" title="Process to Review"><i class="fa fa-check mr-2 text-primary"></i>Process to Review</a>
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
						<div class="tab-pane fade" id="Review" role="tabpanel" aria-labelledby="Review-tab">
							<table class="datatable table table-bordered table-sm table-hover datatable">
								<thead class="table-light">
									<tr>
										<th class="p-2" width="50">No.</th>
										<th class="p-2">Name</th>
										<th class="p-2">Number</th>
										<th class="p-2">Procedure</th>
										<th class="p-2 text-center">Issue Date</th>
										<th class="p-2 text-center">Effective Date</th>
										<th class="p-2 text-center">Revision Number</th>
										<th class="p-2 text-center" width="80">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataReview) && $dataReview) :
										$n = 0;
										foreach ($dataReview as $review) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $review->name; ?></td>
												<td><?= $review->number; ?></td>
												<td><?= $review->procedure_name; ?></td>
												<td class="text-center"><?= $review->issue_date; ?></td>
												<td class="text-center"><?= $review->effective_date; ?></td>
												<td class="text-center"><?= $review->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-id="<?= $review->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<div class="dropdown open d-inline">
														<button class="btn btn-success btn-xs btn-icon" type="button" id="triggerId_review_<?= $review->id; ?>" data-toggle="dropdown" title="Opsi" aria-haspopup="true" aria-expanded="false">
															<i class="fa fa-cog"></i>
														</button>
														<div class="dropdown-menu" aria-labelledby="triggerId_review_<?= $review->id; ?>">
															<a href="<?= base_url('forms/download/' . $review->id); ?>" class="dropdown-item" title="Download Form"><i class="fa fa-download mr-2 text-success"></i>Download</a>
															<div class="dropdown-divider my-0"></div>
															<a href="javascript:void(0)" class="dropdown-item cancelReview" data-id="<?= $review->id; ?>" title="Cancel Review"><i class="fa fa-undo mr-2 text-danger"></i>Cancel Review</a>
														</div>
													</div>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="Correction" role="tabpanel" aria-labelledby="Correction-tab">
							<table class="datatable table table-bordered table-sm table-hover datatable">
								<thead class="table-light">
									<tr>
										<th class="p-2" width="50">No.</th>
										<th class="p-2">Name</th>
										<th class="p-2">Number</th>
										<th class="p-2">Procedure</th>
										<th class="p-2 text-center">Issue Date</th>
										<th class="p-2 text-center">Effective Date</th>
										<th class="p-2 text-center">Revision Number</th>
										<th class="p-2 text-center" width="80">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataCorrection) && $dataCorrection) :
										$n = 0;
										foreach ($dataCorrection as $cor) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $cor->name; ?></td>
												<td><?= $cor->number; ?></td>
												<td><?= $cor->procedure_name; ?></td>
												<td class="text-center"><?= $cor->issue_date; ?></td>
												<td class="text-center"><?= $cor->effective_date; ?></td>
												<td class="text-center"><?= $cor->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-id="<?= $cor->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<div class="dropdown open d-inline">
														<button class="btn btn-success btn-xs btn-icon" type="button" id="triggerId_cor_<?= $cor->id; ?>" data-toggle="dropdown" title="Opsi" aria-haspopup="true" aria-expanded="false">
															<i class="fa fa-cog"></i>
														</button>
														<div class="dropdown-menu" aria-labelledby="triggerId_cor_<?= $cor->id; ?>">
															<a href="<?= base_url('forms/download/' . $cor->id); ?>" class="dropdown-item" title="Download Form"><i class="fa fa-download mr-2 text-success"></i>Download</a>
															<div class="dropdown-divider my-0"></div>
															<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $cor->id); ?>" class="dropdown-item edit" data-id="<?= $cor->id; ?>" title="Edit Data"><i class="fa fa-edit mr-2 text-warning"></i>Edit</a>
															<div class="dropdown-divider my-0"></div>
															<a href="javascript:void(0)" class="dropdown-item correctionToReview" data-id="<?= $cor->id; ?>" title="Process to Review"><i class="fa fa-check mr-2 text-primary"></i>Process to Review</a>
														</div>
													</div>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="Approval" role="tabpanel" aria-labelledby="Approval-tab">
							<table class="datatable table table-bordered table-sm table-hover datatable">
								<thead class="table-light">
									<tr>
										<th class="p-2" width="50">No.</th>
										<th class="p-2">Name</th>
										<th class="p-2">Number</th>
										<th class="p-2">Procedure</th>
										<th class="p-2 text-center">Issue Date</th>
										<th class="p-2 text-center">Effective Date</th>
										<th class="p-2 text-center">Revision Number</th>
										<th class="p-2 text-center" width="80">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataApproval) && $dataApproval) :
										$n = 0;
										foreach ($dataApproval as $apv) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $apv->name; ?></td>
												<td><?= $apv->number; ?></td>
												<td><?= $apv->procedure_name; ?></td>
												<td class="text-center"><?= $apv->issue_date; ?></td>
												<td class="text-center"><?= $apv->effective_date; ?></td>
												<td class="text-center"><?= $apv->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs btn-icon btn-info view" data-id="<?= $apv->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<div class="dropdown open d-inline">
														<button class="btn btn-success btn-xs btn-icon" type="button" id="triggerId_apv_<?= $apv->id; ?>" data-toggle="dropdown" title="Opsi" aria-haspopup="true" aria-expanded="false">
															<i class="fa fa-cog"></i>
														</button>
														<div class="dropdown-menu" aria-labelledby="triggerId_apv_<?= $apv->id; ?>">
															<a href="<?= base_url('forms/download/' . $apv->id); ?>" class="dropdown-item" title="Download Form"><i class="fa fa-download mr-2 text-success"></i>Download</a>
														</div>
													</div>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="Revision" role="tabpanel" aria-labelledby="Revision-tab">
							<table class="datatable table table-bordered table-sm table-hover datatable">
								<thead class="table-light">
									<tr>
										<th class="p-2" width="50">No.</th>
										<th class="p-2">Name</th>
										<th class="p-2">Number</th>
										<th class="p-2">Procedure</th>
										<th class="p-2 text-center">Issue Date</th>
										<th class="p-2 text-center">Effective Date</th>
										<th class="p-2 text-center">Revision Number</th>
										<th class="p-2 text-center" width="80">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataRevision) && $dataRevision) :
										$n = 0;
										foreach ($dataRevision as $rev) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $rev->name; ?></td>
												<td><?= $rev->number; ?></td>
												<td><?= $rev->procedure_name; ?></td>
												<td class="text-center"><?= $rev->issue_date; ?></td>
												<td class="text-center"><?= $rev->effective_date; ?></td>
												<td class="text-center"><?= $rev->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs	 btn-icon btn-info view" data-id="<?= $rev->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<div class="dropdown open d-inline">
														<button class="btn btn-success btn-xs btn-icon" type="button" id="triggerId_rev_<?= $rev->id; ?>" data-toggle="dropdown" title="Opsi" aria-haspopup="true" aria-expanded="false">
															<i class="fa fa-cog"></i>
														</button>
														<div class="dropdown-menu" aria-labelledby="triggerId_rev_<?= $rev->id; ?>">
															<a href="<?= base_url('forms/download/' . $rev->id); ?>" class="dropdown-item" title="Download Form"><i class="fa fa-download mr-2 text-success"></i>Download</a>
															<div class="dropdown-divider my-0"></div>
															<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $rev->id); ?>" class="dropdown-item edit" data-id="<?= $rev->id; ?>" title="Edit Data"><i class="fa fa-edit mr-2 text-warning"></i>Edit</a>
															<div class="dropdown-divider my-0"></div>
															<a href="javascript:void(0)" class="dropdown-item toReview" data-id="<?= $rev->id; ?>" title="Process to Approval"><i class="fa fa-check mr-2 text-primary"></i>Process to Approval</a>
														</div>
													</div>
												</td>
											</tr>
									<?php endforeach;
									endif; ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="Published" role="tabpanel" aria-labelledby="Published-tab">
							<table class="datatable table table-bordered table-sm table-hover datatable">
								<thead class="table-light">
									<tr>
										<th class="p-2" width="50">No.</th>
										<th class="p-2">Name</th>
										<th class="p-2">Number</th>
										<th class="p-2">Procedure</th>
										<th class="p-2 text-center">Issue Date</th>
										<th class="p-2 text-center">Effective Date</th>
										<th class="p-2 text-center">Revision Number</th>
										<th class="p-2 text-center" width="80">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataPublished) && $dataPublished) :
										$n = 0;
										foreach ($dataPublished as $pub) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td>
													<?= $pub->name; ?>
													<?php if (isset($pub->is_under_revision) && $pub->is_under_revision == 1) : ?>
														<span class="badge badge-warning ml-2 py-1 px-2" data-toggle="tooltip" title="Under Revision">
															<i class="fa fa-sync-alt fa-spin text-dark mr-1"></i>Under Revision
														</span>
													<?php endif; ?>
												</td>
												<td><?= $pub->number; ?></td>
												<td><?= $pub->procedure_name; ?></td>
												<td class="text-center"><?= $pub->issue_date; ?></td>
												<td class="text-center"><?= $pub->effective_date; ?></td>
												<td class="text-center"><?= $pub->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs	btn-icon btn-info view" data-id="<?= $pub->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<div class="dropdown open d-inline">
														<button class="btn btn-success btn-xs btn-icon" type="button" id="triggerId_pub_<?= $pub->id; ?>" data-toggle="dropdown" title="Opsi" aria-haspopup="true" aria-expanded="false">
															<i class="fa fa-cog"></i>
														</button>
														<div class="dropdown-menu" aria-labelledby="triggerId_pub_<?= $pub->id; ?>">
															<a href="<?= base_url('forms/download/' . $pub->id); ?>" class="dropdown-item" title="Download Form"><i class="fa fa-download mr-2 text-success"></i>Download</a>
														</div>
													</div>
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
<div class="modal fade" id="modalId" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header py-2">
				<h5 class="modal-title" id="staticBackdropLabel"></h5>
				<span class="btn btn-icon btn-xs" data-dismiss="modal" aria-label="Close"><i class="fa fa-times text-secondary" aria-hidden="true"></i></span>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer py-2">
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

		$('.datatable').DataTable({
			orderCellsTop: false,
		});

		$(document).on('change', '.status', function() {

			if ($(this).val() == 'APV') {
				$('#note').attr('disabled', true).val('');
			} else {
				$('#note').attr('disabled', false);
			}
		})

		$(document).on('click', '.view', function() {
			let id = $(this).data('id')
			$('#modalId').modal('show')
			$('#modalId .modal-title').html('<i class="fa fa-eye" aria-hidden="true"></i> View Form')
			$('#modalId .modal-body').load(siteurl + active_controller + 'view/' + id)
		})

		$(document).on('click', '.toReview', function() {
			let id = $(this).data('id')
			if (id) {
				Swal.fire({
					title: 'Confirm!',
					text: 'Are you sure you want to process this data?',
					icon: 'question',
					showCancelButton: true,
					customClass: {
						cancelButton: 'btn btn-danger',
						confirmButton: 'btn btn-primary w-70px'
					},
					buttonsStyling: false

				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: base_url + active_controller + 'process_to_review',
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

		// Cancel Review — kembalikan dari REV ke DFT
		$(document).on('click', '.cancelReview', function() {
			let id = $(this).data('id')
			if (id) {
				Swal.fire({
					title: 'Cancel Review?',
					text: 'Form akan dikembalikan ke status Draft.',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Ya, Cancel Review',
					cancelButtonText: 'Batal',
					customClass: {
						cancelButton: 'btn btn-secondary',
						confirmButton: 'btn btn-danger'
					},
					buttonsStyling: false
				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: base_url + active_controller + 'cancel_review',
							type: 'POST',
							dataType: 'JSON',
							data: { id },
							success: function(res) {
								if (res.status == 1) {
									Swal.fire({
										title: 'Success!',
										icon: 'success',
										text: res.msg,
										timer: 2000
									}).then(() => {
										location.reload();
									})
								} else {
									Swal.fire({
										title: 'Warning!',
										icon: 'warning',
										text: res.msg,
										timer: 3000
									})
								}
							},
							error: function() {
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

		// Correction to Review — kembalikan dari COR ke REV
		$(document).on('click', '.correctionToReview', function() {
			let id = $(this).data('id')
			if (id) {
				Swal.fire({
					title: 'Confirm!',
					text: 'Form sudah dikoreksi dan siap untuk direview kembali?',
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Ya, Process to Review',
					cancelButtonText: 'Batal',
					customClass: {
						cancelButton: 'btn btn-secondary',
						confirmButton: 'btn btn-success'
					},
					buttonsStyling: false
				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: base_url + active_controller + 'correction_to_review',
							type: 'POST',
							dataType: 'JSON',
							data: { id },
							success: function(res) {
								if (res.status == 1) {
									Swal.fire({
										title: 'Success!',
										icon: 'success',
										text: res.msg,
										timer: 2000
									}).then(() => {
										location.reload();
									})
								} else {
									Swal.fire({
										title: 'Warning!',
										icon: 'warning',
										text: res.msg,
										timer: 3000
									})
								}
							},
							error: function() {
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

		$(document).on('click', '.process-review', function() {
			$('#modalId').modal('show')
			$('#modalId .modal-title').html('<i class="fa fa-check" aria-hidden="true"></i> Process Review')
			$('#modalId .modal-body').load(base_url + active_controller + 'form_review/' + $(this).data('id'))
		})


		$(document).on('change', '.status_approve', function() {
			// uncheck semua
			$('.status_approve').prop('checked', false);

			// check yang diklik
			$(this).prop('checked', true);
			// cek apakah PUB sedang ter-check
			let isPubChecked = $('input.status_approve[value="PUB"]').is(':checked');

			if (isPubChecked) {
				$('#published_date').prop('disabled', false);
			} else {
				$('#published_date').prop('disabled', true).val('').removeClass('is-invalid');
				$('#published_date').closest('.mb-3').find('.invalid-feedback').html('');
			}

			let isCorrChecked = $('input.status_approve[value="COR"]').is(':checked');
			if (isCorrChecked) {
				$('#note').prop('disabled', false);
			} else {
				$('#note').prop('disabled', true).val('').removeClass('is-invalid');
				$('#note').closest('.mb-3').find('.invalid-feedback').html('');
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
							url: base_url + active_controller + 'delete/' + id,
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