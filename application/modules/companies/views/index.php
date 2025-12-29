<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
					<div class="mt-4 float-right ">
						<?php if ($this->session->company->id_perusahaan == '1') : ?>
							<!-- <button type="button" class="btn btn-primary" id="addCompany" title="Add New Company">
								<i class="fa fa-plus mr-1"></i>Add New Company
							</button> -->
						<?php endif; ?>

					</div>
				</div>
				<div class="card-body">
					<table id="example1" class="table table-bordered table-hover table-striped rounded-lg overflow-hidden border">
						<thead class="text-center">
							<tr class="text-center">
								<th width="10">No.</th>
								<th class="text-center no-sort" width="100">Logo</th>
								<th class="text-center" width="200">Company Nama</th>
								<th class="text-center">Address</th>
								<th>City</th>
								<th>Inisial</th>
								<th width="150" class="text-center no-sort">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if (isset($data) && $data) :
								$n = 0;
								foreach ($data as $dt) : $n++; ?>
									<tr class="">
										<td><?= $n; ?></td>
										<td class=""><img width="100" src="<?= base_url($dt->path_logo) . $dt->id_perusahaan . '/' . $dt->logo; ?>" alt=""></td>
										<td class=""><?= $dt->nm_perusahaan; ?></td>
										<td><?= $dt->alamat; ?></td>
										<td><?= $dt->kota; ?></td>
										<td class=""><?= $dt->inisial; ?></td>
										<td class="text-center no-sort">
											<button type="button" class="btn btn-xs btn-icon btn-info view" data-toggle="tooltip" data-id="<?= $dt->id_perusahaan; ?>" title="View Data"><i class="fa fa-search"></i></button>
											<button type="button" class="btn btn-xs btn-icon btn-warning edit" data-toggle="tooltip" data-id="<?= $dt->id_perusahaan; ?>" title="Edit Data"><i class="fa fa-edit"></i></button>
											<?php if ($this->session->company->id_perusahaan == '1') : ?>
												<!-- <button type="button" class="btn btn-xs btn-icon btn-danger delete" data-toggle="tooltip" data-id="<?= $dt->id_perusahaan; ?>" title="View Data"><i class="fa fa-trash"></i></button> -->
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
<!-- Modal -->

<div class="modal fade" id="modalView" data-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel"></h5>
				<span class="close btn-cls" data-dismiss="modal" aria-label="Close"></span>
			</div>
			<div class="modal-body">
				<div class="card" data-load-delay="800">
					<div class="card-body">
						<div class="card-content">
							<div class="text-line" style="width: 95%"></div>
							<!-- <div class="text-line" style="width: 90%"></div>
						<div class="text-line" style="width: 60%"></div> -->
						</div>
						<div class="image-placeholder"></div>
						<div class="card-actions">
							<div class="action-btn"></div>
							<div class="action-btn"></div>
							<div class="action-btn"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary save min-w-100px"><i class="fa fa-save"></i>Save</button>
				<button type="button" class="btn btn-danger text-right" onclick="clear($('.modal-body'));setTimeout(()=>{$('.save').removeClass('d-none')},500)" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Close</button>
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

		$('#example1').DataTable({
			// orderCellsTop: false,
			// fixedHeader: true,
			// scrollX: true,
			// ordering: false,
			// info: false
		});

		$(document).on('click', '#add-branch', function() {
			let branchList, n = 0;
			n = $('.card.branch').length + 1
			branchList = `
			<div class="card branch border-info mb-3">
				<div class="card-header p-3">
					<h4 class="card-title font-weight-bolder mb-0"><i class="fas fa-code-branch text-dark"></i> Branch #${n}</h4>
				</div>
				<div class="card-body p-4">
					<div class="mb-3 row flex-nowrap">
						<label for="" class="col-3 col-form-label font-weight-bold">Company Branch Name <span class="text-danger">*</span></label>
						<div class="col">
							<input type="text" name="branch[${n}][branch_name]" id="branch${n}" class="form-control required" placeholder="Branch Name">
							<span class="invalid-feedback">Company Branch Name can't be empty</span>
						</div>
					</div>
					<div class="mb-3 row flex-nowrap">
						<label for="" class="col-3 col-form-label font-weight-bold">Address <span class="text-danger">*</span></label>
						<div class="col">
							<textarea name="branch[${n}][address]" id="branch${n}" class="form-control required" placeholder="Addrass"></textarea>
							<span class="invalid-feedback">Address Branch can't be empty</span>
						</div>
					</div>
					<div class="mb-3 row flex-nowrap">
						<label for="" class="col-3 col-form-label font-weight-bold">City <span class="text-danger">*</span></label>
						<div class="col-4">
							<input type="text" name="branch[${n}][city]" id="branch${n}" class="form-control required" placeholder="City">
							<span class="invalid-feedback">City Branch can't be empty</span>
						</div>
					</div>
				</div>
			</div>
			`;
			$('div#branch-list').append(branchList)
		})

		$(document).on('click', '#addCompany', function() {
			const url = siteurl + active_controller + 'add';
			$('.modal-title').html('<i class="fa fa-building text-dark" aria-hidden="true"></i> Add New Company')
			$('#modalView').modal('show')
			$('#modalView .modal-body').html(loading())
			$('.modal-body').load(url, function(response, status, xhr) {
				if (status == "error") {
					alert("Terjadi kesalahan: " + xhr.status + " " + xhr.statusText);
				}
			});

			// $('.modal-body').load(url)
		})

		$(document).on('click', '.edit', function() {
			const id = $(this).data('id')
			const url = siteurl + active_controller + 'edit/' + id;
			$('.modal-title').html('Edit Company')
			$('#modalView').modal('show')
			$('#modalView .modal-body').html(loading())
			$('.modal-body').load(url, function(response, status, xhr) {
				if (status == "error") {
					alert("Terjadi kesalahan: " + xhr.status + " " + xhr.statusText);
				}
			});
			// $('.modal-body').load(url)
		})

		$(document).on('click', '.view', function() {
			const id = $(this).data('id')
			const url = siteurl + active_controller + 'view/' + id;
			$('#modalView').modal()
			$('.modal-title').html('Edit Company')
			$('#modalView .modal-body').html(loading())
			$('.modal-body').load(url, function(response, status, xhr) {
				if (status == "error") {
					alert("Terjadi kesalahan: " + xhr.status + " " + xhr.statusText);
				}
			});
			// $('.modal-body').load(url)
			$('.save').addClass('d-none')

		})

		$(document).on('click', '.save', function(e) {
			validation($('.required'))
			let formdata = new FormData($('#form-company')[0])
			let btn = $(this)
			$.ajax({
				url: siteurl + active_controller + 'save',
				data: formdata,
				type: 'POST',
				dataType: 'JSON',
				processData: false,
				contentType: false,
				cache: false,
				beforeSend: function() {
					btn.attr('disabled', true)
					btn.html('<i class="spinner spinner-border-sm mr-3"></i>Loading...')
				},
				complete: function() {
					btn.attr('disabled', false)
					btn.html('<i class="fa fa-save"></i>Save')
				},
				success: function(result) {
					if (result.status == 1) {
						Swal.fire({
							title: 'Success!',
							icon: 'success',
							text: result.msg,
							timer: 2000
						}).then(function() {
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
				error: function(result) {
					Swal.fire({
						title: 'Error!',
						icon: 'error',
						text: 'Server timeout, becuase error!',
						timer: 4000
					})
				}
			})
		})

		$(document).on('click', '.delete', function(e) {
			const id = $(this).data('id')
			Swal.fire({
				title: 'Delete!',
				icon: 'question',
				text: 'Are you sure to delete this data?',
				showCancelButton: true,
			}).then((value) => {
				if (value.isConfirmed) {
					alert('delete')
					alert(id)
				} else {
					alert('no delete')
				}
			})
			exit();

			$.ajax({
				url: siteurl + active_controller + 'save',
				data: formdata,
				type: 'POST',
				dataType: 'JSON',
				processData: false,
				contentType: false,
				cache: false,
				beforeSend: function() {
					btn.attr('disabled', true)
					btn.html('<i class="spinner spinner-border-sm mr-3"></i>Loading...')
				},
				complete: function() {
					btn.attr('disabled', false)
					btn.html('<i class="fa fa-save"></i>Save')
				},
				success: function(result) {
					if (result.status == 1) {
						Swal.fire({
							title: 'Success!',
							icon: 'success',
							text: result.msg,
							timer: 2000
						}).then(function() {
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
				error: function(result) {
					Swal.fire({
						title: 'Error!',
						icon: 'error',
						text: 'Server timeout, becuase error!',
						timer: 4000
					})
				}
			})
		})



		$(document).on('change', ".dropzone", function() {
			readFile(this);
		});


	})

	function readFile(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			index = $(input).data('index')

			reader.onload = function(e) {
				console.log(e)
				var htmlPreview = '<img width="150" src="' + e.target.result + '" />';

				var overlay = `<div class="middle d-flex justify-content-center align-items-center">
				<button type="button" onclick="$(this).parent().parent().find('.dropzone').click()" class="btn btn-sm mr-1 btn-icon btn-warning change-image rounded-circle"><i class="fa fa-edit"></i></button>
				<button type="button" onclick="remove_image(this)" class="btn btn-sm mr-1 btn-icon btn-danger remove-image rounded-circle"><i class="fa fa-trash"></i></button>
				</div>`;
				var wrapperZone = $(input).parent();
				var previewZone = $(input).parent().parent().find('.preview-zone');
				var boxZone = $(input).parent().find('.dropzone-desc');

				wrapperZone.removeClass('dragover');
				previewZone.removeClass('hidden');
				boxZone.html('');
				boxZone.append(htmlPreview);
				wrapperZone.find('.middle').remove();
				wrapperZone.append(overlay);
			};

			reader.readAsDataURL(input.files[0]);
		}
	}

	function remove_image(e) {
		Swal.fire({
			title: 'Are you sure to delete this data?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'Yes, Delete <i class="fa fa-trash text-white"></i>',
		}).then((value) => {
			if ($value.value) {
				let srcFile = $(e).parent().parent().find('.dropzone-desc').find('img').attr('src')
				$(e).parent().parent().find('input.dropzone').val();
				$(e).parent().parent().find('input.dropzone').off();
				$(e).parent().parent().find('.dropzone-desc').empty().append('<i class="fa fa-upload"></i><p> Choose an image file or drag it here. </p>');
				// $(e).parent().parent().find('.for-delete').empty().append('<input type="hidden" name="delete_image[]" value="' + srcFile + '">');
				$(e).parent().remove();
			}
		})

		// $(e).parent().parent().find('input.dropzone').val();
		// $(e).parent().parent().find('input.dropzone').off();
		// $(e).parent().parent().find('.dropzone-desc').empty().append('<i class="fa fa-upload"></i><p> Choose an image file or drag it here. </p>');
		// $(e).parent().remove();

	}


	function validation(field) {
		let err = 0;
		$.each($('.required'), function(i, field) {
			if ($(field).val() == '' || $(field).val() == null) {
				$(field).addClass('is-invalid')
				err++
			} else {
				$(field).removeClass('is-invalid')
			}
		})
		if (err > 0) {
			exit();
		}
	}

	function clear(e) {
		setTimeout(() => {
			$(e).html('');

		}, 500)
	}

	function loading() {
		return `<div class="" data-load-delay="1000">
					<div class="card-content">
					<div class="row">
						<div class="col-9">
							<div class="row">
								<div class="col-3"><div class="username" style="width: 100%"></div></div>
								<div class="col-9"><div class="username" style="width: 100%"></div></div>
							</div>
							<div class="row">
								<div class="col-3"><div class="username" style="width: 100%"></div></div>
								<div class="col-9"><div class="image-placeholder mt-0 h-100px"></div></div>
							</div>
							<div class="row">
								<div class="col-3"><div class="username" style="width: 100%"></div></div>
								<div class="col-9"><div class="username" style="width: 100%"></div></div>
							</div>
							<div class="row">
								<div class="col-3"><div class="username" style="width: 100%"></div></div>
								<div class="col-9"><div class="username" style="width: 100%"></div></div>
							</div>
						</div>
						<div class="col-3">
							<div class="username" style="width: 100%"></div>
							<div class="image-placeholder"></div>
						</div>
					</div>
					</div>
				</div>`
	}
</script>