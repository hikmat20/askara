<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> text-primary mr-2"></i><?= $title; ?></h2>
					<div class="mt-4 float-right ">
					</div>
				</div>
				<div class="card-body">
					<table id="dtTable" class="table table-bordered table-sm table-condensed table-hover">
						<thead class="text-center table-light">
							<tr class="text-center">
								<th width="10">No.</th>
								<th width="100">Date</th>
								<th class="text-left">Process Name</th>
								<th width="">Temuan</th>
								<th width="100" class="text-center">Action</th>
								<th width="150" class="text-center">Edit Limits</th>
							</tr>
						</thead>
						<tbody>
							<?php if ($results) foreach ($results as $k => $v) : $k++;
								$date1 = date_create($v->date);
								$date2 = date_create(date("Y-m-d"));
								$diff = date_diff($date1, $date2)->format("%a");
								$colorText = "text-dark";
								$lblColor = "";

								if ($diff <= 1) {
									$colorText = "text-danger";
									$lblColor = "label-outline-danger";
								} elseif ($diff <= 3) {
									$colorText = "text-white";
									$lblColor = "label-danger";
								} elseif ($diff <= 5) {
									$colorText = "text-white";
									$lblColor = "label-warning";
								}
							?>
								<tr>
									<td class="text-center"><?= $k; ?></td>
									<td class="text-center"><?= date('d M Y', strtotime($v->date)); ?></td>
									<td><?= $v->procedure_name; ?></td>
									<td>
										<?php
										if ($ArrDtl[$v->id]) foreach ($ArrDtl[$v->id] as $n => $x) : ?>
											<?= $x->description; ?>
										<?php endforeach; ?>
									</td>
									<td class="text-center">
										<button type="button" class="btn btn-xs btn-icon btn-success view" data-toggle="tooltip" title="View" data-id="<?= $v->id; ?>"><i class="fa fa-eye" aria-hidden="true"></i></button>
										<button type="button" class="btn btn-xs btn-icon btn-danger delete" data-toggle="tooltip" title="View" data-id="<?= $v->id; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
										<?php if ($diff <= 7) : ?>
											<a href="<?= base_url($this->uri->segment(1) . "/edit_audit/" . $v->id); ?>" class="btn btn-xs btn-icon btn-warning" data-toggle="tooltip" title="Edit" data-id="<?= $v->id; ?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<?php if ($diff <= 7) : ?>
											<span class="label label-inline <?= $lblColor; ?> rounded-pill font-weight-bolder">
												<i class="far fa-clock <?= $colorText; ?> font-weight-bold mr-1"></i><?= date('d M; 23:59', strtotime($v->created_at . ' + 7 days')); ?>
											<?php endif; ?>
											</span>
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

<!-- Modal -->
<div class="modal fade" id="modalID" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel"></h5>
				<span class="btn btn-xs btn-icon " data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times text-dark" aria-hidden="true"></i>
				</span>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-danger text-end" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		Datatable()

		$(document).on('click', '.view', function() {
			const id = $(this).data('id')
			const url = siteurl + active_controller + 'view_audit/' + id;
			$('.modal-title').html('View Audit Checklist')
			$('#modalID').modal('show')
			$('.modal-body').load(url)
		})

		$(document).on('click', '.save', function(e) {
			let r = validation()
			if (r > 0) {
				return false
			}

			let formdata = new FormData($('#form')[0])
			let btn = $(this)
			Swal.fire({
				title: 'Confirmation!',
				icon: 'question',
				text: 'Are you sure to save this data?',
				showCancelButton: true,
			}).then((value) => {
				if (value.isConfirmed) {
					$.ajax({
						url: siteurl + active_controller + 'save',
						data: formdata,
						type: 'POST',
						dataType: 'JSON',
						processData: false,
						contentType: false,
						cache: false,
						beforeSend: function() {
							btn.attr('disabled', true).html('<i class="spinner spinner-border-sm mr-2"></i> Loading...')
						},
						complete: function() {
							btn.attr('disabled', false).html('<i class="fa fa-save"></i>Save')
						},
						success: function(result) {
							if (result.status == 1) {
								Swal.fire({
									title: 'Success!',
									icon: 'success',
									text: result.msg,
									timer: 3000
								}).then(function() {
									location.reload();
								})
							} else {
								Swal.fire({
									title: 'Warning!',
									icon: 'warning',
									text: result.msg,
									timer: 3000
								})
							}
						},
						error: function(result) {
							Swal.fire({
								title: 'Error!',
								icon: 'error',
								text: 'Server timeout, becuase error!',
								timer: 3000
							})
						}
					})
				}
			})


		})

		$(document).on('click', '.delete', function(e) {
			const id = $(this).data('id')
			const btn = $(this)
			Swal.fire({
				title: 'Confirmation!',
				icon: 'question',
				text: 'Are you sure to delete this data?',
				showCancelButton: true,
			}).then((value) => {
				if (value.isConfirmed) {
					$.ajax({
						url: siteurl + active_controller + 'delete_audit',
						data: {
							id
						},
						type: 'POST',
						dataType: 'JSON',
						success: function(result) {
							if (result.status == 1) {
								Swal.fire({
									title: 'Success!',
									icon: 'success',
									text: result.msg,
									timer: 2000
								}).then(function() {
									location.reload();
								})

							} else {
								Swal.fire({
									title: 'Warning!',
									icon: 'warning',
									text: result.msg,
									timer: 2000
								})
							}
						},
						error: function(result) {
							Swal.fire({
								title: 'Error!',
								icon: 'error',
								text: 'Server timeout, becuase error!',
								timer: 4000
							})
						}
					})
				}
			})
		})
	})

	function validation() {
		let e = 0;
		$('.required').each(function() {
			if ($(this).val() == '' || $(this).val() == null) {
				$(this).addClass('is-invalid')
				e++
			} else {
				$(this).removeClass('is-invalid')
			}
		})
		return e;
	}

	function Datatable() {
		$('#dtTable').DataTable({
			fixedHeader: true,
			processing: true,
			destroy: true
			// ordering: false,
			// scrollX: true,
			// info: false
		});
	}
</script>