<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
					<div class="mt-4 float-right ">
						<button type="button" class="btn btn-primary" id="add" title="Add New Position">
							<i class="fa fa-plus mr-1"></i>Add New Position
						</button>
					</div>
				</div>
				<div class="card-body">
					<!-- Nav tabs -->
					<!-- <ul class="nav nav-tabs nav-pills pb-3" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active btn-sm" id="Published-tab" data-toggle="tab" data-target="#Published" type="button" role="tab" aria-controls="Published" aria-selected="true">Published <span class="badge badge-circle badge-white text-primary ml-2"></span></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link btn-sm" id="Draft-tab" data-toggle="tab" data-target="#Draft" type="button" role="tab" aria-controls="Draft" aria-selected="false">Draft <span class="badge badge-circle badge-white text-primary ml-2"></span></button>
						</li>
					</ul> -->

					<!-- Tab panes -->
					<div class="tab-content mt-3">
						<div class="tab-pane fade active show" id="Published" role="tabpanel"
							aria-labelledby="Published-tab">
							<table id="example1"
								class="table table-bordered table-sm table-condensed table-hover datatable">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th width="3%">No.</th>
										<th class="text-left">Position Nama</th>
										<th class="text-left">Employee Name</th>
										<th width="150">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (isset($data) && $data):
										$n = 0;
										foreach ($data as $dt):
											$n++; ?>
											<tr class="">
												<td><?= $n; ?></td>
												<td class="text-left"><?= $dt->name; ?></td>
												<td class=""><?= ($dt->full_name) ?: '~'; ?></td>
												<td class="text-center">
													<button type="button"
														class="btn btn-sm btn-icon rounded-circle btn-success assign"
														data-id="<?= $dt->id; ?>" title="Assign User"><i
															class="fa fa-user-tie"></i></button>
													<button type="button"
														class="btn btn-sm btn-icon rounded-circle btn-warning edit"
														data-id="<?= $dt->id; ?>" title="Edit Data"><i
															class="fa fa-edit"></i></button>
													<button type="button"
														class="btn btn-sm btn-icon rounded-circle btn-danger delete"
														data-id="<?= $dt->id; ?>" title="View Data"><i
															class="fa fa-trash"></i></button>
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
<div class="modal fade" id="modalView" data-backdrop="static" data-keyboard="false" tabindex="-1"
	aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">View Standard</h5>
				<span class="close btn-cls" data-dismiss="modal" aria-label="Close"></span>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-primary save w-100px"><i class="fa fa-save"></i>Save</button>
				<button type="button" class="btn btn-danger text-end"
					onclick="clear($('.modal-body'));setTimeout(()=>{$('.save').removeClass('d-none')},500)"
					data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		$('button[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			$.fn.dataTable.tables({
				visible: true,
				api: true
			}).columns.adjust();
		});

		$('#example1').DataTable({
			orderCellsTop: false,
			// fixedHeader: true,
			// scrollX: true,
			ordering: false,
			// info: false
		});

		$(document).on('click', '#add', function () {
			const url = siteurl + active_controller + 'add';
			$('.modal-title').html('Add New Position')
			$('#modalView').modal('show')
			$('.modal-body').load(url)
		})

		$(document).on('click', '.edit', function () {
			const id = $(this).data('id')
			const url = siteurl + active_controller + 'edit/' + id;
			$('.modal-title').html('Edit Position')
			$('#modalView').modal('show')
			$('.modal-body').load(url)
		})

		$(document).on('click', '.assign', function () {
			const id = $(this).data('id')
			const url = siteurl + active_controller + 'assign/' + id;
			$('.modal-title').html('Edit Position')
			$('#modalView').modal('show')
			$('.modal-body').load(url)
			$('.save').addClass('d-none')
		})

		$(document).on('click', '.save', function () {
			const form = $('#modalView').find('form')
			const name = form.find('input[name="name"]')
			validation(name)

			if (!name.val() || $.trim(name.val()) === '') {
				Swal.fire({
					title: 'Warning!',
					icon: 'warning',
					text: 'Position Name tidak boleh kosong!',
					timer: 2000
				});
				return false;
			}

			let formdata = form.serialize()
			let btn = $(this)
			$.ajax({
				url: siteurl + active_controller + 'save',
				data: formdata,
				type: 'POST',
				dataType: 'JSON',
				cache: false,
				beforeSend: function () {
					btn.attr('disabled', true)
					btn.html('<i class="spinner spinner-border-sm"></i>Loading...')
				},
				complete: function () {
					btn.attr('disabled', false)
					btn.html('<i class="fa fa-save"></i>Save')
				},
				success: function (result) {
					if (result.status == 1) {
						Swal.fire({
							title: 'Success!',
							icon: 'success',
							text: result.msg,
							timer: 2000
						}).then(function () {
							$('#modalView').modal('hide')
							clear($('.modal-body'))
							// $('table#example1 tbody').load(siteurl + active_controller + 'load_data')
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
				error: function (result) {
					Swal.fire({
						title: 'Error!',
						icon: 'error',
						text: 'Server timeout, becuase error!',
						timer: 4000
					})
				}
			})
		})

		$(document).on('click', '.choose-user', function (e) {
			const user_id = $(this).data('id')
			const position = $(this).data('position')
			let btn = $(this)
			Swal.fire({
				title: 'Select User!',
				icon: 'question',
				text: 'Are you sure you want to select this user??',
				showCancelButton: true,
			}).then((value) => {
				if (value.isConfirmed) {
					$.ajax({
						url: siteurl + active_controller + 'save_assign',
						data: {
							user_id,
							position
						},
						type: 'POST',
						dataType: 'JSON',
						beforeSend: function () {
							btn.attr('disabled', true).removeClass('btn-icon')
							btn.html('<i class="spinner spinner-border-sm"></i> Loading..')
						},
						complete: function () {
							btn.attr('disabled', false).addClass('btn-icon')
							btn.html('<i class="fa fa-check"></i>')
						},
						success: function (result) {
							if (result.status == 1) {
								Swal.fire({
									title: 'Success!',
									icon: 'success',
									text: result.msg,
									timer: 2000
								}).then(function () {
									$('#modalView').modal('hide')
									clear($('.modal-body'))
									// $('table#example1 tbody').load(siteurl + active_controller + 'load_data')
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
						error: function (result) {
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

		$(document).on('click', '.remove-user', function (e) {
			const user_id = $(this).data('id')
			const position = $(this).data('position')
			let btn = $(this)
			Swal.fire({
				title: 'Select User!',
				icon: 'question',
				text: 'Are you sure you want to remove this user??',
				showCancelButton: true,
			}).then((value) => {
				if (value.isConfirmed) {
					$.ajax({
						url: siteurl + active_controller + 'remove_assign',
						data: {
							user_id,
							position
						},
						type: 'POST',
						dataType: 'JSON',
						beforeSend: function () {
							btn.attr('disabled', true).removeClass('btn-icon')
							btn.html('<i class="spinner spinner-border-sm"></i> Loading..')
						},
						complete: function () {
							btn.attr('disabled', false).addClass('btn-icon')
							btn.html('<i class="fa fa-check"></i>')
						},
						success: function (result) {
							if (result.status == 1) {
								Swal.fire({
									title: 'Success!',
									icon: 'success',
									text: result.msg,
									timer: 2000
								}).then(function () {
									$('#modalView').modal('hide')
									clear($('.modal-body'))
									// $('table#example1 tbody').load(siteurl + active_controller + 'load_data')
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
						error: function (result) {
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

		$(document).on('click', '.delete', function (e) {
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
						beforeSend: function () {
							btn.attr('disabled', true).html('<i class="spinner spinner-border-sm"></i>')
						},
						complete: function () {
							btn.attr('disabled', false).html('<i class="fa fa-trash"></i>')
						},
						success: function (result) {
							if (result.status == 1) {
								Swal.fire({
									title: 'Success!',
									icon: 'success',
									text: result.msg,
									timer: 2000
								}).then(function () {
									$('#modalView').modal('hide')
									clear($('.modal-body'))
									// $('table#example1 tbody').load(siteurl + active_controller + 'load_data')
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
						error: function (result) {
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

	function validation(field) {
		if (field.val() == '' || field.val() == null) {
			field.addClass('is-invalid')
		} else {
			field.removeClass('is-invalid')
		}
	}

	function clear(e) {
		setTimeout(() => {
			$(e).html('');
		}, 500)
	}
</script>