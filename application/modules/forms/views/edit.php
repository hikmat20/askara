<form id="form-add">
	<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
		<div class="d-flex flex-column-fluid">
			<div class="container">
				<div class="card card card-stretch shadow card-custom">
					<div class="card-header">
						<h2 class="mt-5"><i class="<?= $template['page_icon']; ?> mr-2"></i><?= $template['title']; ?></h2>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Dept./Group Process <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="hidden" id="id" name="id" class="form-control" value="<?= isset($dataForm) ? $dataForm->id : ''; ?>" />
										<select name="departement_id" id="departement" class="form-control select2">
											<option value=""></option>
											<?php if ($departements) foreach ($departements as $departement): ?>
												<option value="<?= $departement['id']; ?>" <?= (isset($dataForm) && $dataForm->departement_id == $departement['id'] ? 'selected' : ''); ?>><?= $departement['name']; ?></option>
											<?php endforeach; ?>
										</select>
										<span class="form-text text-danger invalid-feedback">Departement/Group Process harus di isi</span>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Document Name <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="text" class="form-control" id="name" placeholder="Document Name" name="name" value="<?= isset($dataForm) ? $dataForm->name : ''; ?>" autocomplete="off" />
										<span class="form-text text-danger invalid-feedback">Deskripsi harus di isi</span>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Document Number <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="text" class="form-control" id="number" placeholder="Document Number" name="number" value="<?= isset($dataForm) ? $dataForm->number : ''; ?>" autocomplete="off" />
										<span class="form-text text-danger invalid-feedback">Dokumen Number</span>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Procedure <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<select name="procedure_id" id="procedure" class="form-control select2">
											<option value=""></option>
											<?php if ($procedures) foreach ($procedures as $k => $procedure): ?>
												<option value="<?= $procedure['id']; ?>" <?= (isset($dataForm) && $dataForm->procedure_id == $procedure['id'] ? 'selected' : ''); ?>><?= $procedure['name']; ?></option>
											<?php endforeach; ?>
										</select>
										<span class="form-text text-danger invalid-feedback">Procedure harus di isi</span>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Active <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<select name="is_active" id="is_active" class="form-control select2">
											<option value=""></option>
											<option value="ACT" <?= (isset($dataForm) && $dataForm->is_active == 'ACT' ? 'selected' : ''); ?>>Active</option>
											<option value="OBS" <?= (isset($dataForm) && $dataForm->is_active == 'OBS' ? 'selected' : ''); ?>>Obsolete</option>
										</select>
										<span class="form-text text-danger invalid-feedback">Procedure harus di isi</span>
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Issue Date Rev-0 <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="date" name="issue_date" value="<?= (isset($dataForm->issue_date) ? $dataForm->issue_date : ''); ?>" class="form-control" id="issue_date">
										<span class="form-text text-danger invalid-feedback">Issue Date harus di isi</span>
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Effective Date <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="date" name="effective_date" value="<?= (isset($dataForm->effective_date) ? $dataForm->effective_date : ''); ?>" class="form-control" id="effective_date">
										<span class="form-text text-danger invalid-feedback">Effective Date harus di isi</span>
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Revision Number <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input readonly type="number" name="revision_number" value="<?= (isset($dataForm->revision_number) ? $dataForm->revision_number : ''); ?>" class="form-control text-right" placeholder="0" id="revision_number">
										<span class="form-text text-danger invalid-feedback">Revision Number harus di isi</span>
									</div>
								</div>

							</div>
						</div>
						<hr>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">PIC Reviewer <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<select class="form-control select2" name="reviewer_position_id" id="reviewer_position_id">
											<option value=""></option>
											<?php if ($positions) foreach ($positions as $position): ?>
												<option value="<?= $position->id; ?>" <?= (isset($dataForm) && $dataForm->reviewer_position_id == $position->id ? 'selected' : ''); ?>><?= $position->name; ?></option>
											<?php endforeach; ?>
										</select>
										<span class="form-text text-muted">Jabatan yang bertugas mereview form ini.</span>
										<span class="form-text text-danger invalid-feedback">PIC Reviewer harus di isi</span>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">PIC Approver <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<select class="form-control select2" name="approver_position_id" id="approver_position_id">
											<option value=""></option>
											<?php if ($positions) foreach ($positions as $position): ?>
												<option value="<?= $position->id; ?>" <?= (isset($dataForm) && $dataForm->approver_position_id == $position->id ? 'selected' : ''); ?>><?= $position->name; ?></option>
											<?php endforeach; ?>
										</select>
										<span class="form-text text-muted">Jabatan yang bertugas menyetujui form ini.</span>
										<span class="form-text text-danger invalid-feedback">PIC Approver harus di isi</span>
									</div>
								</div>
							</div>
						</div>

						<hr>

						<div class="row">
							<label class="col-md-2">Document Type <span class="text-danger">*</span></label>
							<div class="col-md-6">
								<div class="form-group mb-0">
									<div class="form-check form-check-inline">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="form_type" value="upload_file"> Upload File
											<span></span>
										</label>
									</div>
									<div class="form-check form-check-inline">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="form_type" value="online_form"> Online Form
											<span></span>
										</label>
									</div>
								</div>
								<div id="type-form"></div>
							</div>
							<div class="col-md-4">
								<label for="">Document Form</label>
								<button type="button" data-id="<?= isset($dataForm) ? $dataForm->id : ''; ?>" class="btn btn-block btn-success view" data-toggle="modal" data-target="#modelId"><i class="fa fa-eye" aria-hidden="true"></i> View Form</button>
							</div>
						</div>
					</div>
					<div class="d-flex card-footer justify-content-between align-items-center">
						<button class="btn btn-primary save min-w-100px"><i class="fa fa-save"></i>Save</button>
						<a href="<?= base_url($this->uri->segment(1)); ?>" class="btn btn-danger min-w-100px" data-dismiss="modal"><i class="fa fa-reply"></i>Back</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Modal -->
<div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header py-2">
				<h5 class="modal-title"></h5>
				<button type="button" class="btn p-0 btn-xs" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
				</button>
			</div>
			<div class="modal-body">Add rows here</div>
			<div class="modal-footer py-2">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: 'Choose an options',
			width: '100%',
			allowClear: true
		})

		/* change form type */
		$(document).on('change', 'input[name="form_type"]:checked', function() {
			const form_type = $(this).val()
			if (form_type == 'upload_file') {
				html = `
					<div class="form-group row mb-0">
						<label class="col-12 col-form-label"><span class="text-danger">*</span> Upload Document :</label>
						<div class="col-12">
							<div class="upload-drop-zone p-4 border border-primary border-dashed rounded text-center" id="drop-zone-file" style="cursor:pointer; border-style: dashed !important; border-width: 2px !important; background-color: #f8f9fa;">
								<i class="fa fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
								<h5>Drag & Drop file di sini atau Paste (Ctrl+V)</h5>
								<p class="text-muted mb-0">Atau klik untuk memilih file manual</p>
								<input type="file" name="form_file" id="form_file" accept=".pdf,.xlsx,.xls,.docx" class="form-control d-none">
								<p id="file-name" class="mt-2 text-success font-weight-bold mb-0"></p>
							</div>
							<span class="form-text text-muted mt-2">Format yang diizinkan: .pdf, .xlsx, .xls, .docx. Ukuran maksimal: 10MB</span>
							<span class="form-text text-danger invalid-feedback">Upload Document By harus di isi</span>
						</div>
					</div>`
			} else if (form_type == 'online_form') {
				html = `
					<div class="form-group row">
						<label class="col-12 col-form-label"><span class="text-danger">*</span> Link Google Form</label>
						<div class="col-12">
							<div class="input-group">
								<span class="input-group-text rounded-right-0"><i class="fa fa-link"></i></span>
								<input type="text" class="form-control" id="link-form" placeholder="Link Form" name="link_form" autocomplete="off" />
							</div>
							<span class="form-text text-danger invalid-feedback">Link Form harus di isi</span>
						</div>
					</div>`
			}
			$('#type-form').html(html)

			if (form_type == 'upload_file') {
				// Initialize File Upload Handler
				const inputElement = document.getElementById('form_file');
				const dropZone = document.getElementById('drop-zone-file');
				const fileNameDisplay = dropZone.querySelector('#file-name');
				
				dropZone.addEventListener('click', () => inputElement.click());

				const handleFile = (files) => {
					if (files.length > 0) {
						let file = files[0];
						
						// Validate size (10MB)
						if (file.size > 10 * 1024 * 1024) {
							Swal.fire('Error!', 'Ukuran file maksimal 10MB', 'error');
							inputElement.value = '';
							fileNameDisplay.textContent = '';
							return;
						}
						
						// Validate extension
						const allowedExtensions = ['pdf', 'xlsx', 'xls', 'docx'];
						const fileExt = file.name.split('.').pop().toLowerCase();
						if (!allowedExtensions.includes(fileExt)) {
							Swal.fire('Error!', 'Format file tidak diizinkan. Hanya .pdf, .xlsx, .xls, .docx', 'error');
							inputElement.value = '';
							fileNameDisplay.textContent = '';
							return;
						}

						const dataTransfer = new DataTransfer();
						dataTransfer.items.add(file);
						inputElement.files = dataTransfer.files;
						fileNameDisplay.textContent = file.name;
					}
				};

				inputElement.addEventListener('change', function() {
					handleFile(this.files);
				});

				dropZone.addEventListener('dragover', (e) => {
					e.preventDefault();
					dropZone.style.backgroundColor = '#e9ecef';
				});
				dropZone.addEventListener('dragleave', () => {
					dropZone.style.backgroundColor = '#f8f9fa';
				});
				dropZone.addEventListener('drop', (e) => {
					e.preventDefault();
					dropZone.style.backgroundColor = '#f8f9fa';
					handleFile(e.dataTransfer.files);
				});

				// We need to attach paste event to document, but make sure it only works for this input and doesn't duplicate
				document.onpaste = function(e) {
					if (e.clipboardData && e.clipboardData.files && e.clipboardData.files.length > 0) {
						const currentDropZone = document.getElementById('drop-zone-file');
						if (currentDropZone) {
							handleFile(e.clipboardData.files);
						}
					}
				};
			} else {
				document.onpaste = null;
			}
		})
		
		// Trigger change to set up the form correctly on load if one is checked
		if($('input[name="form_type"]:checked').length > 0) {
		    $('input[name="form_type"]:checked').trigger('change');
		}

		$(document).on('submit', '#form-add', function(e) {
			e.preventDefault();

			$('#form-add .form-control').removeClass('is-invalid');
			$('#form-add .invalid-feedback').html('');

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
					if (result.status == '0') {
						// tampilkan error per field
						$.each(result.errors, function(field, message) {
							let input = $('[name="' + field + '"]');
							input.addClass('is-invalid');
							input.closest('.mb-3').find('span.invalid-feedback').html(message);

							if (input.is('select')) {
								input.addClass('is-invalid');

								// select2
								if (input.hasClass('select2')) {
									input.next('.select2-container')
										.find('.select2-selection')
										.addClass('is-invalid');
								}

								input.closest('.mb-3')
									.find('.invalid-feedback')
									.html(message);
							}
						});

						if (result.msg) {
							Swal.fire({
								title: 'Error!',
								html: result.msg,
								icon: 'error',
								timer: 3000
							})
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

		$(document).on('click', '.view', function() {
			let id = $(this).data('id')
			$('#modalId').modal('show')
			$('#modalId .modal-title').html('<i class="fa fa-eye" aria-hidden="true"></i> View Form')
			$('#modalId .modal-body').load(siteurl + active_controller + 'view/' + id)
		})

	})
</script>