<style>
	.cursor-pointer:hover div.dir-tools .btn-dropdown {
		display: block !important;
	}
</style>

<div class="content d-flex flex-column flex-column-fluid">
	<div class="d-flex flex-column-fluid justify-content-between align-items-top">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header justify-content-between d-flex align-items-center">
					<h3 class="m-0">
						<a href="<?= base_url($this->uri->segment(1) . '/?p=' . $parent->id); ?>" title="Back" class="btn btn-light btn-sm btn-icon"><i class="fa fa-arrow-left text-dark"></i></a> List Folder
					</h3>
					<button type="button" id="add-folder" class="btn btn-primary"><i class="fa fa-plus"></i> Add Folder</button>
				</div>
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<div class="searching">
							<div class="input-group">
								<div class="input-group-text input-group-prepend rounded-right-0"><i class="fa fa-search"></i></div>
								<input data type="text" name="search" id="search" class="form-control w-200px d-inline-block" placeholder="Search">
							</div>
						</div>
						<input type="hidden" id="process_id" value="<?= $sub->process_id; ?>">
						<input type="hidden" id="sub_id" value="<?= $sub->id; ?>">
						<nav class="breadcrumb py-2 line-height-0 m-0">
							<a class="breadcrumb-item" href="<?= base_url($this->uri->segment(1)); ?>"><i class="fa fa-home"></i></a>
							<a class="breadcrumb-item" href="<?= base_url($this->uri->segment(1)) . '?p=' . $parent->id; ?>"><?= $parent->name; ?></a>
							<span class="breadcrumb-item active"><?= $sub->name; ?></span>
						</nav>
					</div>
					<div class="table-responsive">
						<table class="table table-sm" id="datatable">
						<thead class="table-light">
							<tr>
								<th class="p-2 text-left">Directory Name</th>
								<th class="p-2 text-center" width="20%">Last Created</th>
								<th class="p-2 text-center" width="80">Opsi</th>
							</tr>
						</thead>
						<tbody>
							<?php $n = 0;
							if ($data) foreach ($data as $dt) : $n++; ?>
								<tr>
									<td class="py-2">
										<a href="<?= base_url($this->uri->segment(1) . "/?p=" . $dt->process_id . "&sub=" . $dt->sub_id . "&checksheet=" . $dt->id); ?>">
											<h4 class="mb-0 d-flex align-items-end">
												<i class="fa fa-folder mr-2 text-warning" style="font-size: 28px;"></i><?= $dt->name; ?>
											</h4>
										</a>
									</td>
									<td class="py-2 text-center"><?= $dt->created_at; ?></td>
									<td class="py-2 text-center">
										<button type="button" class="btn btn-xs btn-icon btn-warning edit" data-id="<?= $dt->id; ?>"><i class="fa fa-edit"></i></button>
										<button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $dt->id; ?>"><i class="fa fa-trash"></i></button>
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

<!-- Modal -->
<div class="modal fade" id="modalId" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<div class="btn-save"></div>
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalView" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>Close</button>
			</div>
		</div>
	</div>
</div>

<style>
	div.dt-search,
	div.dt-length {
		display: none;
	}
</style>

<script>
	$(document).ready(function() {
		oTable = $('#datatable').DataTable({
			searchPanes: {
				cascadePanes: true
			},
			language: {
				searchPanes: {
					i18n: {
						emptyMessage: "<i></b>No results returned</b></i>"
					}
				},
				infoEmpty: "No results returned",
				zeroRecords: "No results returned",
				emptyTable: "No results returned",
			},
			lengthChange: true,
			// paging: true,
			info: true,
			pageLength: 20,
		})

		$(document).on('input paste', '#search', function() {
			oTable.search($(this).val()).draw();
		})
	})

	/* DIRECTORY */

	$(document).on('click', '#add-folder', function() {
		$('#modalId .modal-title').text('New Folder')
		$('#modalId').modal('show')
		$('#modalId .modal-body').html(`
			<label for="">Folder Name</label>
			<input type="text" id="folder-name" class="form-control" placeholder="Folder Name">
			<span class="invalid-feedback">Folder Name not be empty</span>`)
		$('.btn-save').html(`
		<button type="button" class="btn btn-primary" id="save-folder"><i class="fa fa-save"></i> Save</button>
		`)
	})

	$(document).on('click', '.edit', function() {
		const id = $(this).data('id')
		$.getJSON(siteurl + active_controller + 'edit_process_dir/' + id, function(data) {
			var items = [];
			$('#modalId .modal-title').text('Edit Folder')
			$('#modalId').modal('show')
			$('#modalId .modal-body').html(`
			<label for="">Folder Name</label>
			<input type="text" id="id" class="form-control d-none" value="` + data.data.id + `">
			<input type="text" id="folder-name" class="form-control" placeholder="New Folder" value="` + data.data.name + `">
			<span class="invalid-feedback">Folder Name not be empty</span>
			`)
			$('.btn-save').html(`<button type="button" class="btn btn-primary" id="save-folder"><i class="fas fa-save"></i> Save</button>`)
		});
	})

	$(document).on('click', '#save-folder', function() {
		const name = $('#folder-name').val();
		const process_id = $('#process_id').val();
		const sub_id = $('#sub_id').val();
		const id = $('#id').val();

		if (!name) {
			$('#folder-name').addClass('is-invalid')
			return false;
		}

		$('#folder-name').removeClass('is-invalid')

		Swal.fire({
			title: 'Confirm!',
			text: 'Are you sure you want to save this new folder?',
			icon: 'question',
			showCancelButton: true,
		}).then((value) => {
			if (value.isConfirmed) {
				$.ajax({
					url: siteurl + active_controller + 'save_process_dir',
					dataType: 'JSON',
					type: 'POST',
					data: {
						process_id,
						sub_id,
						name,
						id,
					},
					success: function(result) {
						if (result.status == 1) {
							Swal.fire({
								title: "Success!",
								text: result.msg,
								icon: "success",
								timer: 3000
							}).then(function() {
								location.reload()
							})
						} else {
							Swal.fire({
								title: "Warning!",
								text: result.msg,
								icon: "warning",
								timer: 3000
							})
						}
					},
					error: function(result) {
						Swal.fire({
							title: "Error!",
							text: "Server time out.",
							icon: "error",
							timer: 3000
						})
					}
				})
			}
		})
	})

	$(document).on('click', '.delete', function() {
		const id = $(this).data('id')

		Swal.fire({
			title: 'Confirm',
			text: 'Are sure you want to delete this folder?',
			icon: 'question',
			showCancelButton: true,
		}).then((value) => {
			if (value.isConfirmed) {
				$.ajax({
					url: siteurl + active_controller + 'delete_process_dir',
					dataType: 'JSON',
					type: 'POST',
					data: {
						id
					},
					success: function(result) {
						if (result.status == 1) {
							Swal.fire({
								title: "Success!",
								text: result.msg,
								icon: "success",
								timer: 3000
							}).then(function() {
								location.reload()
							})
						} else {
							Swal.fire({
								title: "Warning!",
								text: result.msg,
								icon: "warning",
								timer: 3000
							})
						}
					},
					error: function(result) {
						Swal.fire({
							title: "Error!",
							text: "Server time out.",
							icon: "error",
							timer: 3000
						})

					}
				})
			}
		})
	})
</script>