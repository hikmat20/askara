<div class="content d-flex flex-column flex-column-fluid px-5 pb-0" id="kt_content">
	<div class="container mx-auto">
		<div class="card card-custom">
			<div class="row">
				<div class="col-md-4 bg-dark-25 pr-md-0 min-h-md-550px">
					<div class="card-header pb-8 pt-3 h-20px">
						<span class="card-title text-dark-75"><i class="fa fa-folder mr-2 text-warning"></i>Directories</span>
					</div>
					<div class="card-body px-4 py-1 overflow-auto">
						<table class="table table-condensed table-borderless">
							<?php if ($mainFolder) foreach ($mainFolder as $main) : ?>
								<tr data-id="<?= $main->id; ?>" class="tree-folder cursor-pointer" data-folder="<?= $main->description; ?>" data-parent_id="<?= $main->id; ?>">
									<td>
										<h4 class="mb-0"><?= $main->name; ?></h4>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
						<!-- <div id="kt_tree_2" class="tree-demo">
						</div> -->
					</div>
				</div>

				<div class="col-md-8 pl-md-0 mt-5 mt-md-0 border-left-md">
					<div class="card-header px-0 pb-11 pt-0 h-20px border-top border-top-md-0">
						<!-- <input type="hidden" id="active_parent_id" value="ec"> -->
						<!-- <span class="card-title text-dark-75"><i class="fa fa-file mr-2 text-success"></i>List File & Folder</span> -->
						<div class="px-1">
							<ul class="nav nav-light-success nav-pills flex-nowrap text-nowrap overflow-auto" id="myTab" role="tablist">
								<li class="nav-item text-primary" data-id="0">
									<a class="nav-link px-2 disabled" id="back" data-folder="" data-id="0" href="javascript:void(0)">
										<span class="nav-icon">
											<i class="fa fa-arrow-left mr-2 nav-icons"></i>
										</span>
										<span class="nav-text nav-name">Back</span>
									</a>
								</li>
								<li class="nav-item" data-id="0">
									<a class="nav-link px-2 disabled" id="forward" data-id="0" href="javascript:void(0)">
										<span class="nav-icon">
											<i class="fa fa-arrow-right mr-2 nav-icons"></i>
										</span>
										<span class="nav-text nav-name">Forward</span>
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link px-2 disabled" id="add-folder" data-id="0" data-main="0" href="javascript:void(0)">
										<span class="nav-icon">
											<i class="fa fa-plus nav-icons"></i>
										</span>
										<span class="nav-text nav-name">
											Add Folder
										</span>
									</a>
								</li>
								<li class="nav-item dropdown">
									<a class="nav-link px-2 disabled" id="add-file" data-id="0" data-main="0" href="javascript:void(0)">
										<span class="nav-icon">
											<i class="fa fa-file mr-2 nav-icons"></i>
										</span>
										<span class="nav-text nav-name">
											Upload File
										</span>
									</a>
								</li>
								<li class="nav-item dropdown">
									<a class="nav-link px-2" id="refresh" href="javascript:void(0)" data-id="0">
										<span class="nav-icon">
											<i class="fa fa-redo mr-2 nav-icons text-success"></i>
										</span>
										<span class="nav-text nav-name text-success">
											Refresh
										</span>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div id="data-file" >
						<div class="card-body py-2 overflow-auto min-h-md-550px">
							<div class="d-flex justify-content-center align-items-center py-10 ">
								<img src="<?= base_url('assets\images\dashboard\folder-file.gif'); ?>" alt="" width="300px" class="img-responsive text-center opacity-30">
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal-->
<div class="modal fade" id="upload" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md modal-dialog-centered" role="document" style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Upload File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i aria-hidden="true" class="ki ki-close"></i>
				</button>
			</div>
			<div class="modal-body" data-scroll="true" data-height="500" id="viewData"></div>
			<div class="modal-footer py-1">
				<button type="button" class="btn btn-light-primary save-upload font-weight-bold">Save</button>
				<button type="button" class="btn btn-light-warning font-weight-bold" data-dismiss="modal" onclick="setTimeout(function(){$(' #viewData').html('')},1000)">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">View File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i aria-hidden="true" class="ki ki-close"></i>
				</button>
			</div>
			<div class="modal-body" id="viewDataFile"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light-warning font-weight-bold" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="new-folder" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<!-- <h5 class="modal-title" id="exampleModalLabel">Create New Folder </h5> -->
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i aria-hidden="true" class="ki ki-close"></i>
				</button>
			</div>
			<div class="modal-body" id="form-data">
			</div>
			<div class="modal-footer">
				<button type="button" id="save" class="btn btn-light-primary font-weight-bold">Save</button>
				<button type="button" class="btn btn-light-warning font-weight-bold" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#kt_tree_2').jstree({
			"core": {
				"themes": {
					"responsive": true
				}
			},
			"types": {
				"default": {
					"icon": "fa fa-folder text-warning"
				},
				"file": {
					"icon": "fa fa-file  text-success"
				}
			},
			"plugins": ["types"]
		});

		// handle link clicks in tree nodes(support target="_blank" as well)
		$('#kt_tree_2').on('select_node.jstree', function(e, data) {
			var link = $('#' + data.selected).find('a');
			if (link.attr("href") != "#" && link.attr("href") != "javascript:;" && link.attr("href") != "") {
				if (link.attr("target") == "_blank") {
					link.attr("href").target = "_blank";
				}
				document.location.href = link.attr("href");
				return false;
			}
		});
		// interaction and events
	})

	$(document).on('click', '.procedures', function() {
		$('#data-file').load(siteurl + active_controller + 'procedures')
	})

	$(document).on("click", ".tree-folder", function() {
		const id = $(this).data('id');
		const custome = $(this).data('custome');
		const parent_id = $(this).data('parent_id')
		const folder = $(this).data('folder')

		$('.tree-folder').removeClass('table-active')
		$(this).addClass('table-active')
		$('a#back').data('folder', folder);
		console.log(folder)
		if (id) {
			if (custome == 'Y') {} else {
				$('#data-file').load(siteurl + active_controller + 'load_file/' + id + "/" + folder)
			}

			$('#add-folder').attr('data-id', id);
			$('a#add-file').attr('data-id', id);
			$('#refresh').data('id', id);

			if (id != '0') {
				$('a#back').removeClass('disabled')
				$('#back span').children('i.nav-icons').addClass('text-success')
				$('#back').children('.nav-name').addClass('text-success')

				$('a#add-folder').removeClass('disabled')
				$('#add-folder span').children('i.nav-icons').addClass('text-success')
				$('#add-folder').children('.nav-name').addClass('text-success')

				$('a#add-file').removeClass('disabled')
				$('#add-file span').children('i.nav-icons').addClass('text-success')
				$('#add-file').children('.nav-name').addClass('text-success')

				$('a#back').removeClass('disabled')
				$('#back span').children('i.nav-icons').addClass('text-success')
				$('#back').children('.nav-name').addClass('text-success')
				// console.log(id);
				$('a#add-file').attr('data-main', folder);
				$('a#add-folder').attr('data-main', folder);
			}
		} else {
			$('#data-file').html('<tr class="text-center"><td colspan="3" class="text-center">Not available data</td></tr>')
		}
	});

	$(document).on("dblclick", ".folder", function() {
		let id = $(this).data('id');
		let main = $(this).data('folder');

		if (id) {
			$('#data-file').load(siteurl + active_controller + 'load_file/' + id + "/" + main)
			$('a#back').data('id', id);
			$('a#add-folder').data('id', id);
			$('a#add-file').data('id', id);
			$('#refresh').attr('data-id', id);
		} else {
			$('#data-file').html(`
			<div class="card-body border border-1 border-left py-2 overflow-auto h-550px">
				<div class="d-flex justify-content-center align-items-center py-10">
					<img src="<?= base_url('assets\images\dashboard\folder-file.gif'); ?>" alt="" width="300px" class="img-responsive text-center opacity-30">
				</div>
			</div>
			`)

		}
	});

	$(document).on("dblclick", ".file", function() {
		const id = $(this).data('id');
		const main = $(this).data('folder');
		if (id) {
			$('#modal-view').modal('show')
			$('#viewDataFile').load(siteurl + active_controller + 'view_file/' + id + "/" + main)
		} else {
			$('#viewDataFile').html('')

		}
	});

	function handleDblClick(selector, method) {
		$(document).on("dblclick", selector, function() {
			const id = $(this).data('id');
			if (id) {
				$('#data-file').load(siteurl + active_controller + method + '/' + id);
				$('#back').data('id', id);
				$('#add-folder').data('id', id);
				$('#add-file').data('id', id);
				$('#refresh').data('id', id);
			} else {
				$('#data-file').html(`
				<div class="card-body border border-1 border-left py-2 overflow-auto h-550px">
					<div class="d-flex justify-content-center align-items-center py-10">
						<img src="<?= base_url('assets/images/dashboard/folder-file.gif'); ?>" alt="" width="300px" class="img-responsive text-center opacity-30">
					</div>
				</div>
				`);
			}
		});
	}

	handleDblClick(".folder-procedure", "list_procedures");
	handleDblClick(".procedure", "procedure_details");
	handleDblClick(".getProcedure", "getProcedure");
	handleDblClick(".getForms", "getForms");
	handleDblClick(".getGuides", "getGuides");
	handleDblClick(".getRecords", "getRecords");

	$(document).on("click", "#back", function() {
		let id = $(this).data('id');
		let folder = $(this).data('folder')
		console.log(folder)
		if (id != '0') {
			$.ajax({
				url: siteurl + active_controller + 'previous',
				type: 'POST',
				data: {
					id
				},
				dataType: "JSON",
				success: function(data) {
					console.log(data);
					if (data.parent_id != '0') {
						$('#data-file').load(siteurl + active_controller + 'load_file/' + data.parent_id+'/'+folder)
						$('#back').data('id', data.parent_id);
						$('a#add-folder').data('id', data.parent_id);
						$('a#add-file').data('id', data.parent_id);
						$('#refresh').data('id', data.parent_id);
						console.log(data.parent_id);
					} else {
						$('a#back').addClass('disabled')
						$('#back span').children('i.nav-icons').removeClass('text-success')
						$('#back').children('.nav-name').removeClass('text-success')

						$('a#add-folder').addClass('disabled')
						$('#add-folder span').children('i.nav-icons').removeClass('text-success')
						$('#add-folder').children('.nav-name').removeClass('text-success')

						$('a#add-file').addClass('disabled')
						$('#add-file span').children('i.nav-icons').removeClass('text-success')
						$('#add-file').children('.nav-name').removeClass('text-success')

						$('#data-file').html(`
							<div class="card-body border border-1 border-left py-2 overflow-auto h-550px">
								<div class="d-flex justify-content-center align-items-center py-10">
									<img src="` + siteurl + `assets/images/dashboard/folder-file.gif" alt="" width="300px" class="img-responsive text-center opacity-30">
								</div>
							</div>
						`)
						return false;
					}
				},
				error: function(data) {}
			});
		}

	});

	$(document).on('click', '#save', function() {
		let folder_name = $('#folder_name').val()
		let id = $('#id').val()
		let parent_id = $('#parent_id').val()
		if (folder_name == '') {
			$('#folder_name').addClass('is-invalid');
			$('#feedback').removeClass('d-none');
			return false;
		} else {
			return $.ajax({
				url: base_url + active_controller + 'save',
				type: 'POST',
				dataType: 'JSON',
				data: {
					id,
					parent_id,
					folder_name,
				},
				success: function(res) {
					if (res.status == '1') {
						Swal.fire({
							title: 'Success!!',
							text: res.msg,
							icon: 'success',
							timer: 2000
						})
						$('#data-file').load(siteurl + active_controller + 'load_file/' + parent_id)
						$('#new-folder').modal('hide')
						$('#folder_name').val('')
					} else {
						Swal.fire({
							title: 'Failed!!',
							icon: 'warning',
							text: res.msg,
							timer: 2000
						})
					}
				},
				error: function(res) {
					Swal.fire({
						title: 'Error!',
						icon: 'error',
						text: 'Server timeout, because error.',
						timer: 3000
					})
				}
			})
		}
	})

	$(document).on('click', '.save-upload', function(e) {
		$('#description').removeClass('is-invalid')
		$('#prepared_by').removeClass('is-invalid')
		$('#approval_id').removeClass('is-invalid')
		$('#reviewer_id').removeClass('is-invalid')
		$('#distribute_id').removeClass('is-invalid')
		$('#image').removeClass('is-invalid')

		e.preventDefault();
		const description = $('#description').val();
		const prepared_by = $('#prepared_by').val();
		const reviewer_id = $('#reviewer_id').val();
		const approval_id = $('#approval_id').val();
		const distribute_id = $('#distribute_id').val();
		const id_master = $('#id_master').val();
		const image = $('#image').val();
		const parent_id = $('#parent_id').val();
		const folder = $('input[name="folder"]').val();
		console.log(parent_id)
		if (description == '' || description == null) {
			$('#description').addClass('is-invalid')
			return false;
		}
		if (prepared_by == '' || prepared_by == null) {
			Swal.fire({
				title: "Error Message!",
				text: 'Empty User Prepared, please input User Prepared  first.....',
				icon: "warning"
			});
			$('#prepared_by').addClass('is-invalid')

			return false;
		}
		if ((reviewer_id == '' && reviewer_id != undefined) || (reviewer_id == null && reviewer_id != undefined)) {
			Swal.fire({
				title: "Error Message!",
				text: 'Empty reviewer, please input reviewer first.....',
				icon: "warning"
			});
			$('#reviewer_id').addClass('is-invalid')

			return false;
		}
		if ((approval_id == '' && approval_id != undefined) || (approval_id == null && approval_id != undefined)) {
			Swal.fire({
				title: "Error Message!",
				text: 'Empty approval, please input approval first!',
				icon: "warning"
			});
			$('#approval_id').addClass('is-invalid')

			return false;
		}
		if ((distribute_id == '' && distribute_id != undefined) || (distribute_id == null && distribute_id != undefined)) {
			$('#distribute_id').addClass('is-invalid')
			Swal.fire({
				title: "Error Message!",
				text: 'Empty distribusi, please input distribusi first.....',
				icon: "warning"
			});

			return false;
		}

		if (image == '' || image == null) {
			$('#image').addClass('is-invalid')
			Swal.fire({
				title: "Error Message!",
				text: 'Empty file, please input file first.....',
				icon: "warning"
			});

			return false;
		}

		Swal.fire({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
		}).then((value) => {
			if (value.isConfirmed) {
				var formData = new FormData($('#form-upload')[0]);
				var baseurl = siteurl + active_controller + 'save_upload';
				$.ajax({
					url: baseurl,
					type: "POST",
					data: formData,
					cache: false,
					dataType: 'json',
					processData: false,
					contentType: false,
					success: function(data) {
						if (data.status == 1) {
							Swal.fire({
								title: "Upload Success!",
								text: data.msg,
								icon: "success",
								timer: 2000,
								showCancelButton: false,
								showConfirmButton: false,
								allowOutsideClick: false
							});
							$('#upload').modal('hide')
							$('#data-file').load(siteurl + active_controller + 'load_file/' + parent_id + "/" + folder)
							$('#viewData').html('')
						} else {
							if (data.status == 0) {
								Swal.fire({
									title: "Failed!",
									html: data.msg,
									icon: "warning",
									timer: 5000,
								});
							}
						}
					},
					error: function() {
						Swal.fire({
							title: "Error Message !",
							text: 'An Error Occured During Process. Please try again..',
							icon: "warning",
							timer: 3000,
						});
					}
				});
			}
		});
	});

	$(document).on('change', 'input[name="flag_record"]:checked', function() {
		const mode = $(this).val()

		if (mode == 'Y') {
			$('#file-type').html('')
		} else {
			const html = `
			<div class="form-group row">
				<label class="col-lg-3 col-form-label text-lg-right">Review By :</label>
				<div class="col-lg-7">
					<select name="reviewer_id" id="reviewer_id" class="form-control select2">;
						<option value=""></option>
						<?php foreach ($jabatan as $jbt) : ?>
							<option value="<?= $jbt->id; ?>" <?= (isset($file) && $file->reviewer_id == $jbt->id) ? 'selected' : ''; ?>><?= $jbt->nm_jabatan; ?></option>
						<?php endforeach; ?>
					</select>
					<span class="form-text text-danger invalid-feedback">Review By harus di isi</span>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-lg-3 col-form-label text-lg-right">Approval By :</label>
				<div class="col-lg-7">
					<select name="approval_id" id="approval_id" class="form-control select2">;
						<option value=""></option>
						<?php foreach ($jabatan as $jbt) : ?>
							<option value="<?= $jbt->id; ?>" <?= (isset($file) && $file->approval_id == $jbt->id) ? 'selected' : ''; ?>><?= $jbt->nm_jabatan; ?></option>
						<?php endforeach; ?>
					</select>
					<span class="form-text text-danger invalid-feedback">Approval By harus di isi</span>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-lg-3 col-form-label text-lg-right">Distribusi :</label>
				<div class="col-lg-7">
					<select name="distribute_id[]" multiple id="distribute_id" data-placeholder="Choose an options" class="form-control select2">;
						<option value=""></option>
						<?php foreach ($jabatan as $jbt) : ?>
							<option value="<?= $jbt->id; ?>" <?= isset($file) ? ((in_array($jbt->id, explode(',', $file->distribute_id))) ? 'selected' : '') : ''; ?>><?= $jbt->nm_jabatan; ?></option>
						<?php endforeach; ?>
					</select>
					<span class="form-text text-danger invalid-feedback">Distribusi By harus di isi</span>
				</div>
			</div>`
			$('#file-type').html(html)
			$('.select2').select2({
				placeholder: 'Choose an options',
				width: '100%',
				allowClear: true
			})

		}

	})


	$(document).on('click', '#refresh', function() {
		const id = $(this).data('id')
		refresh(id)
	})

	$(document).on('click', '#add-file', function() {
		let id = $(this).data('id')
		let main = $(this).data('main')
		upload_file(id, main)
	})

	$(document).on('click', '#add-folder', function() {
		let id = $(this).data('id')
		let main = $(this).data('main')
		new_folder(id, main)
	})

	/* FUNCTIONS */

	function refresh(id) {
		if (id) {
			$('#data-file').load(siteurl + active_controller + 'load_file/' + id)
			console.log(id);
		} else {
			$('#data-file').html(`
			<div class="card-body border border-1 border-left py-2 overflow-auto h-550px">
				<div class="d-flex justify-content-center align-items-center py-10">
					<img src="` + "<?= base_url('assets/images/dashboard/folder-file.gif'); ?>" + `" alt="" width="300px" class="img-responsive text-center opacity-30">
				</div>
			</div>
			`)
			console.log('<?= base_url('assets/images/dashboard/folder-file.gif'); ?>');

		}
	}

	function new_folder(parent_id, main) {
		if (main) {
			main = "/" + main
		}
		$('#new-folder').modal('show')
		$('#form-data').load(siteurl + active_controller + 'new_folder/' + parent_id + main)
		$('.parent_id').val(parent_id)
	}

	function review_process(id, parent_id) {
		Swal.fire({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
		}).then((value) => {
			if (value.isConfirmed) {
				var baseurl = siteurl + active_controller + 'process_review';
				$.ajax({
					url: baseurl,
					type: "POST",
					data: {
						id
					},
					dataType: 'json',
					success: function(data) {
						if (data.status == 1) {
							Swal.fire({
								title: "Success!",
								text: data.msg,
								icon: "success",
								timer: 3000,
								showCancelButton: false,
								showConfirmButton: false,
								allowOutsideClick: false
							});
							$('#data-file').load(siteurl + active_controller + 'load_file/' + parent_id)
						} else {
							if (data.status == 0) {
								Swal.fire({
									title: "Failed!",
									html: data.msg,
									icon: "warning",
									timer: 5000,
								});
							}
						}
					},
					error: function() {
						Swal.fire({
							title: "Error Message !",
							text: 'An Error Occured During Process. Please try again..',
							icon: "warning",
							timer: 3000,
						});
					}
				});
			}
		});
	}

	function rename(id) {
		$('#new-folder').modal('show')
		$('#form-data').load(siteurl + active_controller + 'rename/' + id)
	}

	function upload_file(parent_id, main = '') {
		if (main) {
			main = "/" + main
		}
		$('#upload').modal('show')
		$('#viewData').load(siteurl + active_controller + 'upload/' + parent_id + main)
		$('.parent_id').val(parent_id)
	}

	function edit_file(id) {
		$('#upload').modal('show')
		$('#viewData').load(siteurl + active_controller + 'edit_file/' + id)
	}

	function delete_file(id, parent_id) {
		Swal.fire({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
		}).then((value) => {
			if (value.isConfirmed) {
				var baseurl = siteurl + active_controller + 'delete_file';
				$.ajax({
					url: baseurl,
					type: "POST",
					data: {
						id,
						parent_id
					},
					dataType: 'json',
					success: function(data) {
						if (data.status == 1) {
							Swal.fire({
								title: "Delete Success!",
								text: data.msg,
								icon: "success",
								timer: 3000,
								showCancelButton: false,
								showConfirmButton: false,
								allowOutsideClick: false
							});
							$('#upload').modal('hide')
							$('#data-file').load(siteurl + active_controller + 'load_file/' + parent_id)
						} else {
							if (data.status == 0) {
								Swal.fire({
									title: "Delete Failed!",
									html: data.msg,
									icon: "warning",
									timer: 5000,
								});
							}
						}
					},
					error: function() {
						Swal.fire({
							title: "Error Message !",
							text: 'An Error Occured During Process. Please try again..',
							icon: "warning",
							timer: 3000,
						});
					}
				});
			}
		});
	}

	function delete_folder(id, parent_id) {
		Swal.fire({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
		}).then((value) => {
			if (value.isConfirmed) {
				var baseurl = siteurl + active_controller + 'delete_folder';
				$.ajax({
					url: baseurl,
					type: "POST",
					data: {
						id,
						parent_id
					},
					dataType: 'json',
					success: function(data) {
						if (data.status == 1) {
							Swal.fire({
								title: "Delete Success!",
								text: data.msg,
								icon: "success",
								timer: 3000,
								showCancelButton: false,
								showConfirmButton: false,
								allowOutsideClick: false
							});
							$('#upload').modal('hide')
							$('#data-file').load(siteurl + active_controller + 'load_file/' + parent_id)
						} else {
							if (data.status == 0) {
								Swal.fire({
									title: "Delete Failed!",
									html: data.msg,
									icon: "warning",
									timer: 5000,
								});
							}
						}
					},
					error: function() {
						Swal.fire({
							title: "Error Message !",
							text: 'An Error Occured During Process. Please try again..',
							icon: "warning",
							timer: 3000,
						});
					}
				});
			}
		});
	}
</script>
