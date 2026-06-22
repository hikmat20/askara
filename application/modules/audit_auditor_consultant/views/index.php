<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> text-primary mr-2"></i><?= $title; ?></h2>
					<div class="mt-4 float-right ">
						<button type="button" class="btn btn-primary" id="add" title="Add New Process">
							<i class="fa fa-plus mr-1"></i>Add New
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="tab-content mt-3">
						<div class="tab-pane fade active show" id="Published" role="tabpanel" aria-labelledby="Published-tab">
							<table id="dtTable" class="table table-bordered table-sm table-condensed table-hover">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th width="10">No.</th>
										<th class="text-left">Name</th>
										<th class="text-center">Position</th>
										<th class="text-center">Email</th>
										<th class="text-center">Description</th>
										<th width="50" class="text-center">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if ($data) foreach ($data as $k => $v) : $k++; ?>
										<tr>
											<td class="text-center"><?= $k; ?></td>
											<td><?= $v->name; ?></td>
											<td>
												<?php
												$arr = [];
												if ($v->position) foreach (json_decode($v->position) as $pos) {
													$arr[] = $position[$pos];
												}
												echo implode(', ', $arr);
												?>
											</td>
											<td><?= isset($v->email) ? $v->email : ''; ?></td>
											<td><?= $v->description; ?></td>
											<td class="text-center">
												<button type="button" class="btn btn-xs btn-icon btn-warning edit" data-id="<?= $v->id; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
												<button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $v->id; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
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
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalID" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel"></h5>
				<span class="btn btn-xs btn-icon " data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times text-dark" aria-hidden="true"></i>
				</span>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-primary save min-w-100px"><i class="fa fa-save"></i>Save</button>
				<button type="button" class="btn btn-danger text-end" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		Datatable()

		$(document).on('click', '#add', function() {
			const url = siteurl + active_controller + 'add';
			$('.modal-title').html('Add New')
			$('#modalID').modal()
			$('#modalID .modal-body').load(url)
		})

		$(document).on('click', '.edit', function() {
			const id = $(this).data('id')
			const url = siteurl + active_controller + 'edit/' + id;
			$('.modal-title').html('Edit')
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
				title: 'Delete!',
				icon: 'question',
				text: 'Are you sure to delete this data?',
				showCancelButton: true,
			}).then((value) => {
				if (value.isConfirmed) {
					$.ajax({
						url: siteurl + active_controller + 'delete',
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