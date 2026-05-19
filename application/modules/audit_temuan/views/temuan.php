<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<form id="formData">
				<div class="card card-stretch shadow card-custom">
					<div class="card-header">
						<h2 class="mt-5"><i class="<?= $icon; ?> text-primary mr-2"></i><?= $title; ?></h2>
						<div class="mt-4 float-right ">
							<a href="<?= base_url($this->uri->segment(1) . "/detail/$data->id"); ?>" class="btn btn-danger w-100px" title="Back">
								<i class="fa fa-reply mr-1"></i>Back</a>
						</div>
					</div>
					<div class="card-body">
						<input type="hidden" name="company_id" value="<?= $data->company_id; ?>">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-2 row">
									<label for="" class="col-md-4 h6 font-weight-bold">Company</label>
									<label for="" class="col h6">: <?= $data->company_name; ?></label>
								</div>
								<div class="mb-2 row">
									<label for="" class="col-md-4 h6 font-weight-bold">Badan Sertifikasi</label>
									<label for="" class="col h6">: <?= $data->name; ?></label>
								</div>
							</div>
						</div>
						<hr>
						<div class="form-group h4 row">
							<span for="" class="col-md-2 font-weight-bolder">Standard</span>
							<span for="" class="col font-weight-bolder">: <?= $standard->standard_name; ?></span>
						</div>

						<label for="" class="contol-label font-weight-bolder h6"><i class="fa fa-clipboard-list text-dark" aria-hidden="true"></i> Detail Temuan</label>
						<div class="mb-2">
							<table id="dtTemuan" class="table table-sm dataTable display table-bordered table-condensed table-hover">
								<thead class="table-light">
									<tr class="text-center">
										<th width="10">No</th>
										<th>Pasal</th>
										<th>Temuan</th>
										<th>Kategori</th>
										<th>Proses</th>
										<th>Auditee</th>
										<th>Auditor</th>
										<th width="50">Auditor Internal</th>
										<th>Konsultan</th>
										<th width="75">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$cat = [
										'1' => '<span class="label label-success label-inline">Minor</span>',
										'2' => '<span class="label label-danger label-inline">Major</span>',
										'3' => '<span class="label label-warning label-inline">OFI</span>',
									];

									if ($details) foreach ($details as $k => $v) : $k++; ?>
										<tr class="text-center">
											<td><?= $k; ?></td>
											<td class="text-left">
												<?php if ($v->pasal_name) : ?>
													<ul class="pl-2">
														<li><?= implode("</li><li>", json_decode($v->pasal_name)); ?></li>
													</ul>
												<?php endif; ?>
											</td>
											<td class="text-left"><?= $v->description; ?></td>
											<td><?= $cat[$v->category]; ?></td>
											<td><?= $v->process; ?></td>
											<td><?= $v->auditee; ?></td>
											<td><?= $v->auditor_name; ?></td>
											<td><?= $v->auditor_internal_name; ?></td>
											<td><?= $v->audit_consultant_name; ?></td>
											<td class="text-center">
												<!-- <button type="button" class="btn btn-xs btn-icon btn-info view" data-id="<?= $v->id; ?>"><i class="fa fa-eye" aria-hidden="true"></i></button> -->
												<button type="button" class="btn btn-xs btn-icon btn-warning edit" data-id="<?= $v->id; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
												<button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $v->id; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
											</td>
										</tr>
									<?php endforeach; ?>

								</tbody>
							</table>
						</div>
						<button type="button" class="btn btn-sm btn-primary" id="add-temuan"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Temuan</button>
					</div>
					<div class="card-footer text-center"></div>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modelId" data-backdrop="static" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form id="formTemuan">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Temuan</h5>
					<button type="button" class="btn btn-xs btn-icon" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fa fa-times text-dark" aria-hidden="true"></i></span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="audit_id" value="<?= $data->id; ?>">
					<input type="hidden" name="audit_standard_id" value="<?= $standard->id; ?>">
					<input type="hidden" name="standard_id" value="<?= $standard->standard_id; ?>">
					<div class="form-group">
						<label for="" class="h6 font-weight-bold">Audit Date <span class="text-danger">*</span></label>
						<input type="date" name="date" class="form-control required" aria-describedby="helpId">
					</div>
					<div class="form-group">
						<label for="" class="h6 font-weight-bold">Pasal <span class="text-danger">*</span></label>
						<select name="pasal_id[]" id="pasal_id" data-allow-clear="true" multiple="multiple" data-placeholder="Select Pasal" class="form-select required select2">
							<?php if ($pasals) foreach ($pasals as $k => $v) : ?>
								<option value="<?= $v->id; ?>"><?= $v->chapter; ?></option>
							<?php endforeach; ?>
						</select>
						<span class="invalid-feedback">Pilih pasal terlebih dahulu!</span>
					</div>
					<div class="form-group">
						<label for="" class="h6 font-weight-bold">Temuan <span class="text-danger">*</span></label>
						<textarea name="description" id="description" class="form-control summernote required" rows="5" placeholder="Deskripsi Temuan"></textarea>
						<span class="invalid-feedback">Deskripsi temuan tidak boleh kosong!</span>
					</div>
					<div class="form-group">
						<label for="" class="h6 font-weight-bold">Kategori <span class="text-danger">*</span></label>
						<select name="category" id="category" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Kategori">
							<option value=""></option>
							<option value="1">Minor</option>
							<option value="2">Major</option>
							<option value="3">OFI</option>
						</select>
					</div>
					<div class="form-group">
						<label for="" class="h6 font-weight-bold">Proses <span class="text-danger">*</span></label>
						<select name="process" id="process" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Proses">
							<option value=""></option>
							<?php if ($process) foreach ($process as $k => $v) : ?>
								<option value="<?= $v->id; ?>"><?= $v->process_name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="" class="h6 font-weight-bold">Auditee <span class="text-danger">*</span></label>
						<input type="text" name="auditee" id="auditee" class="form-control required" placeholder="Auditee">
					</div>
					<div class="form-group">
						<label for="" class="h6 font-weight-bold">Auditor <span class="text-danger">*</span></label>
						<select name="auditor" id="auditor" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Auditor">
							<option></option>
							<?php if ($auditors) foreach ($auditors as $k => $v) : ?>
								<option value="<?= $v->id; ?>"><?= $v->name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="" class="h6 font-weight-bold">Auditor Internal <span class="text-danger">*</span></label>
						<select name="auditor_internal" id="auditor_internal" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Auditor">
							<option value=""></option>
							<?php if ($auditorInternal) foreach ($auditorInternal as $k => $v) : ?>
								<option value="<?= $v->id; ?>"><?= $v->name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="" class="h6 font-weight-bold">Konsultan <span class="text-danger">*</span></label>
						<select name="consultant" id="consultant" data-allow-clear="true" class="form-select select2 required" data-placeholder="Select Auditor">
							<option value=""></option>
							<?php if ($consultant) foreach ($consultant as $k => $v) : ?>
								<option value="<?= $v->id; ?>"><?= $v->name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="save" class="btn btn-primary min-w-100px"><i class="fa fa-save"></i>Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modelId2" data-backdrop="static" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form id="formEditTemuan">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-edit" aria-hidden="true"></i> Edit Temuan</h5>
					<button type="button" class="btn btn-xs btn-icon" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fa fa-times text-dark" aria-hidden="true"></i></span>
					</button>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer">
					<button type="submit" id="update" class="btn btn-primary min-w-100px"><i class="fa fa-save"></i>Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#dtTemuan').DataTable({
			autoWidth: false
		});

		// Initialize Select2 once, appending to body for perfect positioning and behavior
		$('#modelId .select2').select2({
			width: "100%",
			closeOnSelect: true,
			allowClear: true,
			placeholder: "Choose one"
		});

		// Initialize Summernote once
		$('#modelId textarea.summernote').summernote({
			dialogsInBody: true,
			height: 150
		});

		/* Tambah Temuan */
		$(document).on('click', '#add-temuan', function() {
			$("#modelId").modal('show');
		});

		$(document).on('click', '.edit', function() {
			const id = $(this).data('id')
			const url = siteurl + active_controller + 'edit/' + id;
			if (id) {
				$("#modelId2").modal()
				$('#modelId2 .modal-body').load(url)
			}
		})

		/* Save */
		$(document).on('submit', '#formTemuan', function(e) {
			e.preventDefault();
			let formdata = new FormData($(this)[0])
			let btn = $('#save')
			let valid = getValidation('#formTemuan')
			if (valid == true) {
				Swal.fire({
					title: 'Confirmation!',
					icon: 'question',
					text: 'Are you sure to save this data?',
					showCancelButton: true,
				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: siteurl + active_controller + 'save_temuan',
							data: formdata,
							type: 'POST',
							dataType: 'JSON',
							processData: false,
							contentType: false,
							cache: false,
							beforeSend: function() {
								btn.attr('disabled', true).html('<i class="spinner spinner-border-sm mr-2"></i>Loading...')
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
										timer: 2000
									})
									location.reload()
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
			}

		})
		/* Save */
		$(document).on('submit', '#formEditTemuan', function(e) {
			e.preventDefault();
			let formdata = new FormData($(this)[0])
			let btn = $('#update')
			let valid = getValidation('#formEditTemuan')
			if (valid == true) {
				Swal.fire({
					title: 'Confirmation!',
					icon: 'question',
					text: 'Are you sure to save this data?',
					showCancelButton: true,
				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: siteurl + active_controller + 'save_temuan',
							data: formdata,
							type: 'POST',
							dataType: 'JSON',
							processData: false,
							contentType: false,
							cache: false,
							beforeSend: function() {
								btn.attr('disabled', true).html('<i class="spinner spinner-border-sm mr-2"></i>Loading...')
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
										timer: 2000
									})
									location.reload()
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
			}

		})

		/* Delete */
		$(document).on('click', '.delete', function(e) {
			const id = $(this).data('id')
			const btn = $(this)

			if (id) {
				Swal.fire({
					title: 'Delete!',
					icon: 'question',
					text: 'Are you sure to delete this data?',
					showCancelButton: true,
				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: siteurl + active_controller + 'delete_detail',
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
			}
		})
	})
</script>