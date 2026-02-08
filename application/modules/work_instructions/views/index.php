<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $template['page_icon']; ?> mr-2"></i><?= $template['title']; ?></h2>
					<div class="mt-4 float-right ">
						<a href="<?= base_url($this->uri->segment(1) . '/add'); ?>" class="btn btn-primary" data-toggle="tooltip" title="Add New WI">
							<i class="fa fa-plus mr-1"></i>Add New WI
						</a>
					</div>
				</div>
				<div class="card-body py-3">
					<!-- Nav tabs -->
					<!-- <ul class="nav nav-tabs nav-pills pb-3" id="myTab" role="tablist">
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
					</ul> -->

					<!-- Tab panes -->
					<div class="tab-content mt-3">
						<div class="tab-pane fade show active" id="Draft" role="tabpanel" aria-labelledby="Draft-tab">
							<table class="datatable table table-bordered table-sm table-hover datatable">
								<thead class="table-light">
									<tr>
										<th class="p-2" width="50">No.</th>
										<th class="p-2">Departement</th>
										<th class="p-2">Number</th>
										<th class="p-2">Name</th>
										<th class="p-2">Issue Date</th>
										<th class="p-2">Effective Date</th>
										<th class="p-2">Revision Number</th>
										<th class="p-2" width="100">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataDraft) && $dataDraft) :
										$n = 0;
										foreach ($dataDraft as $draft) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $draft->departement_name; ?></td>
												<td><?= $draft->number; ?></td>
												<td><?= $draft->name; ?></td>
												<td><?= $draft->issue_date; ?></td>
												<td><?= $draft->effective_date; ?></td>
												<td><?= $draft->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs	 btn-icon btn-info view" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $draft->id); ?>" class="btn btn-xs	 btn-icon btn-warning edit" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="Edit Data"><i class="fa fa-edit"></i></a>
													<!-- <button type="button" class="btn btn-xs	 btn-icon btn-primary toReview" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="Review WI"><i class="fa fa-check"></i></button> -->
													<!-- <button type="button" class="btn btn-xs	 btn-icon btn-danger delete" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button> -->
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
										<th class="p-2">Departement</th>
										<th class="p-2">Number</th>
										<th class="p-2">Name</th>
										<th class="p-2">Issue Date</th>
										<th class="p-2">Effective Date</th>
										<th class="p-2">Revision Number</th>
										<th class="p-2" width="100">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataReview) && $dataReview) :
										$n = 0;
										foreach ($dataReview as $review) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $review->departement_name; ?></td>
												<td><?= $review->number; ?></td>
												<td><?= $review->name; ?></td>
												<td><?= $review->issue_date; ?></td>
												<td><?= $review->effective_date; ?></td>
												<td><?= $review->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs	 btn-icon btn-info view" data-id="<?= $review->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<button type="button" class="btn btn-xs	 btn-icon btn-success process-review" data-id="<?= $review->id; ?>" data-toggle="tooltip" title="Process to Approval"><i class="fa fa-check"></i></button>
													<!-- <button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $review->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button> -->
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
										<th class="p-2">Departement</th>
										<th class="p-2">Number</th>
										<th class="p-2">Name</th>
										<th class="p-2">Issue Date</th>
										<th class="p-2">Effective Date</th>
										<th class="p-2">Revision Number</th>
										<th class="p-2" width="100">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataCorrection) && $dataCorrection) :
										$n = 0;
										foreach ($dataCorrection as $cor) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $cor->departement_name; ?></td>
												<td><?= $cor->number; ?></td>
												<td><?= $cor->name; ?></td>
												<td><?= $cor->issue_date; ?></td>
												<td><?= $cor->effective_date; ?></td>
												<td><?= $cor->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs	 btn-icon btn-info view" data-id="<?= $cor->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<a href="<?= base_url($this->uri->segment(1) . '/edit/' . $draft->id); ?>" class="btn btn-xs	 btn-icon btn-warning edit" data-id="<?= $draft->id; ?>" data-toggle="tooltip" title="Edit Data"><i class="fa fa-edit"></i></a>
													<button type="button" class="btn btn-xs	 btn-icon btn-success toReview" data-id="<?= $cor->id; ?>" data-toggle="tooltip" title="Process to Approval"><i class="fa fa-check"></i></button>
													<!-- <button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $cor->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button> -->
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
										<th class="p-2">Departement</th>
										<th class="p-2">Number</th>
										<th class="p-2">Name</th>
										<th class="p-2">Issue Date</th>
										<th class="p-2">Effective Date</th>
										<th class="p-2">Revision Number</th>
										<th class="p-2" width="100">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataApproval) && $dataApproval) :
										$n = 0;
										foreach ($dataApproval as $apv) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $apv->departement_name; ?></td>
												<td><?= $apv->number; ?></td>
												<td><?= $apv->name; ?></td>
												<td><?= $apv->issue_date; ?></td>
												<td><?= $apv->effective_date; ?></td>
												<td><?= $apv->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs	 btn-icon btn-info view" data-id="<?= $apv->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<button type="button" class="btn btn-xs	 btn-icon btn-success process-approval" data-id="<?= $apv->id; ?>" data-toggle="tooltip" title="Process to Approval"><i class="fa fa-check"></i></button>
													<!-- <button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $apv->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button> -->
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
										<th class="p-2">Departement</th>
										<th class="p-2">Number</th>
										<th class="p-2">Name</th>
										<th class="p-2">Issue Date</th>
										<th class="p-2">Effective Date</th>
										<th class="p-2">Revision Number</th>
										<th class="p-2" width="100">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataRevision) && $dataRevision) :
										$n = 0;
										foreach ($dataRevision as $rev) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $rev->departement_name; ?></td>
												<td><?= $rev->number; ?></td>
												<td><?= $rev->name; ?></td>
												<td><?= $rev->issue_date; ?></td>
												<td><?= $rev->effective_date; ?></td>
												<td><?= $rev->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs	 btn-icon btn-info view" data-id="<?= $rev->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<button type="button" class="btn btn-xs	 btn-icon btn-success process-rev" data-id="<?= $rev->id; ?>" data-toggle="tooltip" title="Process to Approval"><i class="fa fa-check"></i></button>
													<!-- <button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $rev->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button> -->
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
										<th class="p-2">Departement</th>
										<th class="p-2">Number</th>
										<th class="p-2">Name</th>
										<th class="p-2">Issue Date</th>
										<th class="p-2">Effective Date</th>
										<th class="p-2">Revision Number</th>
										<th class="p-2" width="100">Opsi</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($dataPublished) && $dataPublished) :
										$n = 0;
										foreach ($dataPublished as $pub) : $n++; ?>
											<tr>
												<td><?= $n; ?></td>
												<td><?= $pub->departement_name; ?></td>
												<td><?= $pub->number; ?></td>
												<td><?= $pub->name; ?></td>
												<td><?= $pub->issue_date; ?></td>
												<td><?= $pub->effective_date; ?></td>
												<td><?= $pub->revision_number; ?></td>
												<td class="text-center">
													<button type="button" class="btn btn-xs	btn-icon btn-info view" data-id="<?= $pub->id; ?>" data-toggle="tooltip" title="View Data"><i class="fa fa-eye"></i></button>
													<!-- <a href="#" target="_blank" class="btn btn-xs btn-icon btn-light" data-id="<?= $pub->id; ?>" data-toggle="tooltip" title="Print Document"><i class="fa fa-print"></i></a> -->
													<!-- <button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $pub->id; ?>" data-toggle="tooltip" title="Delete Data"><i class="fa fa-trash"></i></button> -->
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

		$(document).on('click', '.view', function() {
			let id = $(this).data('id')
			$('#modalId').modal('show')
			$('#modalId .modal-title').html('<i class="fa fa-eye" aria-hidden="true"></i> View WI')
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

		$(document).on('change', '[name="status"]', function() {
			console.log($(this).val());

			if ($(this).val() == 'APV') {
				$('#note_correction').prop('disabled', true).val('');
			} else {
				$('#note_correction').prop('disabled', false);
			}
		})

		$(document).on('click', '.process-review', function() {
			$('#modalId').modal('show')
			$('#modalId .modal-title').html('<i class="fa fa-check" aria-hidden="true"></i> Process Review')
			$('#modalId .modal-body').load(base_url + active_controller + 'form_review/' + $(this).data('id'))
		})

		$(document).on('submit', '#form-review', function(e) {
			e.preventDefault()

			$('#form-review .form-control').removeClass('is-invalid');
			$('#form-review .invalid-feedback').html('');

			let btn = $('#save-review')
			let formdata = new FormData($(this)[0])

			$.ajax({
				url: siteurl + active_controller + 'saveReview',
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
					btn.html('<i class="fab fa-telegram-plane"></i> Submit Review')
				},
				success: function(result) {
					if (result.status == '0') {

						// tampilkan error per field
						$.each(result.errors, function(field, message) {
							let input = $('[name="' + field + '"]');
							input.addClass('is-invalid');
							input.closest('.mb-3').find('span.invalid-feedback').html(message);
						});
						return;
					}

					Swal.fire({
						title: 'Success!',
						text: result.msg,
						icon: 'success',
						timer: 2000
					}).then(() => {
						location.href = siteurl + active_controller
					});
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

		$(document).on('click', '.process-approval', function() {
			$('#modalId').modal('show')
			$('#modalId .modal-title').html('<i class="fa fa-check" aria-hidden="true"></i> Process Approval')
			$('#modalId .modal-body').load(base_url + active_controller + 'form_approval/' + $(this).data('id'))
		})

		$(document).on('submit', '#form-approval', function(e) {
			e.preventDefault()
			// reset error
			$('.invalid-feedback').html('');
			$('#invalid-action').addClass('d-none');

			let btn = $('#save-approval')
			let formdata = new FormData($(this)[0])

			$.ajax({
				url: siteurl + active_controller + 'saveApprove',
				data: formdata,
				dataType: 'JSON',
				type: 'POST',
				processData: false,
				contentType: false,
				cache: false,
				beforeSend: function() {
					btn.attr('disabled', true)
					btn.html('<i class="spinner spinner-border-sm mr-3"></i>Loading...')
				},
				complete: function() {
					btn.attr('disabled', false)
					btn.html('<i class="fab fa-telegram-plane"></i> Submit Approval')
				},
				success: function(result) {
					if (result.status == '0') {
						// tampilkan error per field
						console.log(result.errors);
						$.each(result.errors, function(field, message) {
							console.log(field);
							console.log(message);

							let input = $('[name="' + field + '"]');
							input.addClass('is-invalid');
							input.closest('.mb-3').find('.invalid-feedback').text(message);
							console.log(input.closest('.mb-3').find('.invalid-feedback'));

						});
						if (result.errors['status']) {
							$('#invalid-action').removeClass('d-none').html(`<p class="mb-0 text-danger">${result.errors['status']}</p>`);
						}
						return;
					}

					Swal.fire({
						title: 'Success!',
						text: result.msg,
						icon: 'success',
						timer: 2000
					}).then(() => {
						location.href = siteurl + active_controller
					});
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