<form id="form-add">
	<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
		<div class="d-flex flex-column-fluid">
			<div class="container">
				<div class="card card card-stretch shadow card-custom">
					<div class="card-header">
						<h2 class="mt-5"><i class="fa fa-plus mr-2"></i><?= $template['title']; ?></h2>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Dept./Group Process <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="hidden" id="id" name="id" class="form-control" value="<?= isset($data) ? $data->id : ''; ?>" />
										<select name="departement_id" id="departement" class="form-control select2">
											<option value=""></option>
											<?php if ($departements) foreach ($departements as $departement): ?>
												<option value="<?= $departement['id']; ?>"><?= $departement['name']; ?></option>
											<?php endforeach; ?>
										</select>
										<span class="form-text text-danger invalid-feedback">Departement/Group Process harus di isi</span>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Document Name <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="text" class="form-control" id="name" placeholder="Document Name" name="name" value="<?= isset($data) ? $data->name : ''; ?>" autocomplete="off" />
										<span class="form-text text-danger invalid-feedback">Deskripsi harus di isi</span>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Document Number <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="text" class="form-control" id="number" placeholder="Document Number" name="number" value="<?= isset($data) ? $data->number : ''; ?>" autocomplete="off" />
										<span class="form-text text-danger invalid-feedback">Dokumen Number</span>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Procedure <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<select name="procedure_id" id="procedure" class="form-control select2">
											<option value=""></option>
											<?php if ($procedures) foreach ($procedures as $k => $procedure): ?>
												<option value="<?= $procedure['id']; ?>"><?= $procedure['name']; ?></option>
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
											<option value="ACT">Active</option>
											<option value="OBS">Obsolete</option>
										</select>
										<span class="form-text text-danger invalid-feedback">Procedure harus di isi</span>
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Issue Date Rev-0 <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="date" name="issue_date" class="form-control" id="issue_date">
										<span class="form-text text-danger invalid-feedback">Issue Date harus di isi</span>
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Effective Date <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="date" name="effective_date" class="form-control" id="effective_date">
										<span class="form-text text-danger invalid-feedback">Effective Date harus di isi</span>
									</div>
								</div>

								<div class="mb-3 row">
									<label class="col-md-4 col-form-label">Revision Number <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<input type="number" readonly name="revision_number" class="form-control text-right" value="0" placeholder="0" id="revision_number">
										<span class="form-text text-danger invalid-feedback">Revision Number harus di isi</span>
									</div>
								</div>

							</div>
						</div>
						<hr>

						<div class="row">
							<label class="col-md-2">Document Type <span class="text-danger">*</span></label>
							<div class="col-md-10">
								<div class="form-group mb-0">
									<div class="form-check form-check-inline">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" <?= (isset($data) && $data->file_name) ? 'checked' : ''; ?> name="form_type" value="upload_file"> Upload File
											<span class="invalid-feedback text-danger"></span>
										</label>
									</div>
									<div class="form-check form-check-inline">
										<label class="form-check-label">
											<input class="form-check-input" type="radio" name="form_type" value="online_form"> Online Form
										</label>
									</div>
								</div>
								<div id="type-form"></div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3 row">
									<label class="col-md-4">Prepared By <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<select class="form-control select2" name="prepared_by" id="prepared_by">
											<option value=""></option>
											<?php if ($users) foreach ($users as $user): ?>
												<option value="<?= $user->id_user; ?>" <?= ($this->auth->user_id() == $user->id_user) ? 'selected' : ''; ?>><?= $user->full_name; ?></option>
											<?php endforeach; ?>
										</select>
										<!-- <input type="text" readonly class="form-control bg-dark-o-20" placeholder="Prepared By Name" value="<?= $user->full_name; ?>"> -->
										<!-- <input type="hidden" name="prepared_by" class="form-control" id="prepared_by" placeholder="Name" value="<?= $this->auth->user_id(); ?>"> -->
										<span class="form-text text-danger invalid-feedback">harus di isi</span>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-4">Reviewer By <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<select class="form-control select2" name="reviewer_id" id="reviewer_id">
											<option value=""></option>
											<?php if ($positions) foreach ($positions as $position): ?>
												<option value="<?= $position->id; ?>"><?= $position->name; ?></option>
											<?php endforeach; ?>
										</select>
										<span class="form-text text-danger invalid-feedback">harus di isi</span>
									</div>
								</div>
								<div class="mb-3 row">
									<label class="col-md-4">Approval By <span class="text-danger">*</span></label>
									<div class="col-md-8">
										<select class="form-control select2" name="approval_id" id="approval_id">
											<option value=""></option>
											<?php if ($positions) foreach ($positions as $position): ?>
												<option value="<?= $position->id; ?>"><?= $position->name; ?></option>
											<?php endforeach; ?>
										</select>
										<span class="form-text text-danger invalid-feedback">harus di isi</span>
									</div>
								</div>
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
					<div class="form-group row mb-3">
						<label class="col-12 col-form-label"><span class="text-danger">*</span> Upload Document :</label>
						<div class="col-12">
							<input type="file" name="form_file" accept=".pdf" class="form-control" placeholder="Upload File">
							<span class="form-text text-muted">File type : PDF</span>
							<span class="form-text text-danger invalid-feedback">Upload Document By harus di isi</span>
						</div>
					</div>`
			} else if (form_type == 'online_form') {
				html = `
					<div class="form-group mb-3">
						<label class="col-form-label"><span class="text-danger">*</span> Link Google Form</label>
						<div class="input-group">
							<span class="input-group-text rounded-right-0"><i class="fa fa-link"></i></span>
							<input type="text" class="form-control" id="link-form" placeholder="Link Form" name="link_form" autocomplete="off" />
						</div>
						<span class="form-text text-danger invalid-feedback">Link Form harus di isi</span>
					</div>`
			}
			$('#type-form').html(html)
		})

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
					console.log(result);
					if (result.status == 0) {

						// tampilkan error per field
						$.each(result.errors, function(field, message) {
							let input = $('[name="' + field + '"]');
							input.addClass('is-invalid');
							input.closest('.mb-3').find('span.invalid-feedback').html(message);
							input.closest('.form-check-label').find('span.invalid-feedback').text(message);

							if (input.is('select')) {
								input.addClass('is-invalid');

								// select2
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

	})
</script>