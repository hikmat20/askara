<form id="form-upload" enctype="multipart/form-data">
	<div class="content d-flex flex-column flex-column-fluid p-0">
		<div class="d-flex flex-column-fluid justify-content-between align-items-top">
			<div class="container mt-10">
				<div class="card">
					<div class="card-header py-">
						<h3 class="card-title m-0">New File</h3>
					</div>
					<input type="hidden" name="guide_detail_id" value="<?= $guide_detail_id; ?>">
					<div class="card-body">

						<!-- Document Detail -->
						<h6 class="font-weight-bold mb-3">Document Detail</h6>
						<div class="row mb-3">
							<div class="col-md-6">
								<div class="row mb-3">
									<label class="col-4 col-form-label text-nowrap">Document Number <span class="text-danger">*</span></label>
									<div class="col-8">
										<input type="text" name="doc_number" id="doc_number" placeholder="Document Number" class="form-control">
									</div>
								</div>
								<div class="row mb-3">
									<label class="col-4 col-form-label text-nowrap">Document Name <span class="text-danger">*</span></label>
									<div class="col-8">
										<input type="text" name="doc_name" id="doc_name" placeholder="Document Name" class="form-control">
									</div>
								</div>
								<div class="row mb-3">
									<label class="col-4 col-form-label text-nowrap">Issue Date Rev-0 <span class="text-danger">*</span></label>
									<div class="col-8">
										<input type="text" name="issue_date" id="issue_date" placeholder="dd/mm/yyyy" class="form-control datepicker-doc">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="row mb-3">
									<label class="col-4 col-form-label text-nowrap">Effective Date <span class="text-danger">*</span></label>
									<div class="col-8">
										<input type="text" name="effective_date" id="effective_date" placeholder="dd/mm/yyyy" class="form-control datepicker-doc">
									</div>
								</div>
								<div class="row mb-3">
									<label class="col-4 col-form-label text-nowrap">Revision Number <span class="text-danger">*</span></label>
									<div class="col-8">
										<input type="text" name="doc_revision_number" id="doc_revision_number" placeholder="Revision Number" class="form-control">
									</div>
								</div>
								<div class="row mb-3">
									<label class="col-4 col-form-label text-nowrap">Prepare By <span class="text-danger">*</span></label>
									<div class="col-8">
										<select name="prepare_by" id="prepare_by" class="form-control select2">
											<option value=""></option>
											<?php if (isset($users) && $users) foreach ($users as $u) : ?>
												<option value="<?= $u->id_user; ?>" <?= (isset($current_user_id) && $current_user_id == $u->id_user) ? 'selected' : ''; ?>><?= $u->full_name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>

						<hr>

						<!-- Approval Detail -->
						<h6 class="font-weight-bold mb-3">Approval Detail</h6>
						<div class="row mb-3">
							<div class="col-md-6">
								<div class="row mb-3">
									<label class="col-4 col-form-label text-nowrap">PIC Reviewer <span class="text-danger">*</span></label>
									<div class="col-8">
										<select name="pic_reviewer" id="pic_reviewer" class="form-control select2">
											<option value=""></option>
											<?php if (isset($users) && $users) foreach ($users as $u) : ?>
												<option value="<?= $u->id_user; ?>"><?= $u->full_name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="row mb-3">
									<label class="col-4 col-form-label text-nowrap">PIC Approval <span class="text-danger">*</span></label>
									<div class="col-8">
										<select name="pic_approval" id="pic_approval" class="form-control select2">
											<option value=""></option>
											<?php if (isset($users) && $users) foreach ($users as $u) : ?>
												<option value="<?= $u->id_user; ?>"><?= $u->full_name; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>

						<hr>

						<!-- Upload Document -->
						<h6 class="font-weight-bold mb-3">Upload Document <span class="text-danger">*</span></h6>
						<div class="row mb-3">
							<div class="col-md-8">
								<input type="file" name="doc_file" id="doc_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
								<small class="text-muted">Format: PDF, JPG, JPEG, PNG, DOC, DOCX, XLS, XLSX (Max: 10MB)</small>
							</div>
						</div>

						<div class="d-flex justify-content-between mt-4">
							<button type="button" class="btn btn-success btn-lg px-5 save-files"><i class="fa fa-save"></i> Save</button>
							<a href="<?= base_url($this->uri->segment(1) . '?d=' . $detail->guide_id . '&sub=' . $detail->id); ?>" class="btn btn-danger btn-lg px-5"><i class="fa fa-reply"></i> Back</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</form>
<style>
	.flatpickr-wrapper {
		width: 100%;
	}
	.flatpickr-wrapper .form-control {
		width: 100%;
	}
	span.selection span.select2-selection.select2-selection--single.is-invalid,
	span.selection span.select2-selection.select2-selection--multiple.is-invalid {
		border-color: #f64e60;
		padding-right: calc(1.5em + 1.3rem);
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23F64E60' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23F64E60' stroke='none'/%3e%3c/svg%3e");
		background-repeat: no-repeat;
		background-position: right calc(0.375em + 0.325rem) center;
		background-size: calc(0.75em + 0.65rem) calc(0.75em + 0.65rem);
	}
</style>
<script>
	$(document).ready(function() {
		$('.select2').select2({
			width: '100%',
			placeholder: 'Choose an options',
			allowClear: true,
			closeOnSelect: false
		})

		/* DATEPICKER */
		$('.datepicker-doc').flatpickr({
			dateFormat: "d/m/Y",
			static: true,
		})
	})

	$(document).on('click', '.save-files', function() {
		let btn = $(this)
		let c = 0;

		/* Document Detail Validation */
		$('#doc_number').removeClass('is-invalid')
		if (!$('#doc_number').val()) {
			$('#doc_number').addClass('is-invalid')
			c++;
		}

		$('#doc_name').removeClass('is-invalid')
		if (!$('#doc_name').val()) {
			$('#doc_name').addClass('is-invalid')
			c++;
		}

		$('#issue_date').removeClass('is-invalid')
		if (!$('#issue_date').val()) {
			$('#issue_date').addClass('is-invalid')
			c++;
		}

		$('#effective_date').removeClass('is-invalid')
		if (!$('#effective_date').val()) {
			$('#effective_date').addClass('is-invalid')
			c++;
		}

		$('#doc_revision_number').removeClass('is-invalid')
		if (!$('#doc_revision_number').val()) {
			$('#doc_revision_number').addClass('is-invalid')
			c++;
		}

		/* Approval Detail Validation */
		$('select#pic_reviewer').next().find('span.selection .select2-selection.select2-selection--single').removeClass('is-invalid')
		if (!$('#pic_reviewer').val()) {
			$('select#pic_reviewer').next().find('span.selection .select2-selection.select2-selection--single').addClass('is-invalid')
			c++;
		}

		$('select#pic_approval').next().find('span.selection .select2-selection.select2-selection--single').removeClass('is-invalid')
		if (!$('#pic_approval').val()) {
			$('select#pic_approval').next().find('span.selection .select2-selection.select2-selection--single').addClass('is-invalid')
			c++;
		}

		/* Upload Document Validation */
		$('#doc_file').removeClass('is-invalid')
		if (!$('#doc_file').val()) {
			$('#doc_file').addClass('is-invalid')
			c++;
		}

		// Validate file size (max 10MB)
		var fileInput = $('#doc_file')[0];
		if (fileInput.files.length > 0 && fileInput.files[0].size > 10 * 1024 * 1024) {
			$('#doc_file').addClass('is-invalid')
			Swal.fire('Warning!', 'File size exceeds 10MB limit.', 'warning')
			c++;
		}

		if (c > 0) {
			return false;
		}

		const formdata = new FormData($('#form-upload')[0])
		$.ajax({
			url: siteurl + active_controller + 'upload_document',
			type: 'POST',
			dataType: 'JSON',
			data: formdata,
			contentType: false,
			processData: false,
			cache: false,
			beforeSend: function() {
				btn.html('<i class="spinner-border spinner-border" aria-hidden="true"></i> Loading...').prop('disabled', true)
			},
			complete: function() {
				btn.html('<i class="fa fa-save" aria-hidden="true"></i> Save').prop('disabled', false)
			},
			success: function(result) {
				if (result.status == 1) {
					Swal.fire("Success!", result.msg, "success", 3000).then(function() {
						window.location.href = '<?= base_url($this->uri->segment(1) . "?d=" . $detail->guide_id . "&sub=" . $detail->id); ?>'
					})
				} else {
					Swal.fire("Warning!", result.msg, "warning", 3000)
				}
			},
			error: function(result) {
				Swal.fire("Error!", "Server time out.", "error", 3000)
			}
		})
	})
</script>