<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?> - Form</h2>
					<div class="mt-4 float-right">
						<a href="<?= site_url('corrective_action'); ?>" class="btn btn-secondary">
							<i class="fa fa-arrow-left mr-1"></i>Back
						</a>
					</div>
				</div>
				<div class="card-body">

					<!-- Read-only Header Section -->
					<div class="card card-custom mb-5" style="background-color: #f7f8fa;">
						<div class="card-body py-4">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row mb-2">
										<label class="col-sm-4 col-form-label font-weight-bold">Prosedur</label>
										<div class="col-sm-8">
											<p class="form-control-plaintext"><?= isset($header->process_name) && $header->process_name ? strip_tags($header->process_name) : '-'; ?></p>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="col-sm-4 col-form-label font-weight-bold">Date</label>
										<div class="col-sm-8">
											<p class="form-control-plaintext"><?= isset($header->audit_date) && $header->audit_date ? date('d-m-Y', strtotime($header->audit_date)) : '-'; ?></p>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group row mb-2">
										<label class="col-sm-4 col-form-label font-weight-bold">Department</label>
										<div class="col-sm-8">
											<p class="form-control-plaintext"><?= isset($header->department_name) && $header->department_name ? $header->department_name : '-'; ?></p>
										</div>
									</div>
									<div class="form-group row mb-2">
										<label class="col-sm-4 col-form-label font-weight-bold">Auditor</label>
										<div class="col-sm-8">
											<p class="form-control-plaintext"><?= isset($header->auditor_name) && $header->auditor_name ? $header->auditor_name : '-'; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Form -->
					<form id="form-ca" enctype="multipart/form-data">
						<input type="hidden" name="pelaksanaan_id" value="<?= $pelaksanaan_id; ?>">
						<?php if (isset($ca) && $ca) : ?>
							<input type="hidden" name="ca_id" id="ca_id" value="<?= $ca->id; ?>">
						<?php endif; ?>

						<!-- Temuan Panels -->
						<?php if (isset($temuan) && $temuan) :
							$n = 0;
							// Index details by temuan_id for easy lookup
							$detailMap = [];
							if (isset($details) && $details) {
								foreach ($details as $det) {
									$detailMap[$det->temuan_id] = $det;
								}
							}
							// Index files by temuan_id for easy lookup
							$fileMap = [];
							if (isset($files) && $files) {
								foreach ($files as $f) {
									$fileMap[$f->temuan_id] = $f;
								}
							}
							foreach ($temuan as $t) : $n++;
								$detail = isset($detailMap[$t->id]) ? $detailMap[$t->id] : null;
								$file = isset($fileMap[$t->id]) ? $fileMap[$t->id] : null;
						?>
							<div class="card card-custom border mb-5">
								<div class="card-header">
									<h4 class="card-title mt-3">
										Temuan Audit #<?= $n; ?>
										<?php
											$badge_class = 'label-light-warning';
											if (strtolower($t->kategori) == 'major') {
												$badge_class = 'label-light-danger';
											} elseif (strtolower($t->kategori) == 'ofi') {
												$badge_class = 'label-light-info';
											}
										?>
										<span class="label label-lg <?= $badge_class; ?> label-inline ml-2"><?= $t->kategori; ?></span>
									</h4>
								</div>
								<div class="card-body">
									<!-- Temuan Description (read-only) -->
									<div class="form-group">
										<label class="font-weight-bold">Temuan:</label>
										<div class="p-3 border rounded" style="background-color: #f7f8fa;">
											<?= nl2br(htmlspecialchars($t->description)); ?>
										</div>
									</div>

									<!-- Fakta -->
									<div class="form-group">
										<label class="font-weight-bold">Fakta <span class="text-danger">*</span></label>
										<textarea name="detail[<?= $t->id; ?>][fakta]" class="form-control textarea-counter" maxlength="2000" rows="3" placeholder="Masukkan fakta yang ditemukan..."><?= $detail ? htmlspecialchars($detail->fakta) : ''; ?></textarea>
										<small class="form-text text-muted text-right">
											<span class="char-count"><?= $detail ? strlen($detail->fakta) : 0; ?></span>/2000
										</small>
									</div>

									<!-- Kesimpulan Penyebab -->
									<div class="form-group">
										<label class="font-weight-bold">Kesimpulan Penyebab <span class="text-danger">*</span></label>
										<textarea name="detail[<?= $t->id; ?>][penyebab]" class="form-control textarea-counter" maxlength="2000" rows="3" placeholder="Masukkan kesimpulan penyebab..."><?= $detail ? htmlspecialchars($detail->kesimpulan_penyebab) : ''; ?></textarea>
										<small class="form-text text-muted text-right">
											<span class="char-count"><?= $detail ? strlen($detail->kesimpulan_penyebab) : 0; ?></span>/2000
										</small>
									</div>

									<!-- Correction -->
									<div class="form-group">
										<label class="font-weight-bold">Correction <span class="text-danger">*</span></label>
										<textarea name="detail[<?= $t->id; ?>][correction]" class="form-control textarea-counter" maxlength="2000" rows="3" placeholder="Masukkan tindakan koreksi..."><?= $detail ? htmlspecialchars($detail->correction) : ''; ?></textarea>
										<small class="form-text text-muted text-right">
											<span class="char-count"><?= $detail ? strlen($detail->correction) : 0; ?></span>/2000
										</small>
									</div>

									<!-- Corrective Action -->
									<div class="form-group">
										<label class="font-weight-bold">Corrective Action <span class="text-danger">*</span></label>
										<textarea name="detail[<?= $t->id; ?>][corrective_action]" class="form-control textarea-counter" maxlength="2000" rows="3" placeholder="Masukkan tindakan perbaikan..."><?= $detail ? htmlspecialchars($detail->corrective_action) : ''; ?></textarea>
										<small class="form-text text-muted text-right">
											<span class="char-count"><?= $detail ? strlen($detail->corrective_action) : 0; ?></span>/2000
										</small>
									</div>

									<!-- File Upload -->
									<div class="form-group">
										<label class="font-weight-bold">Upload Evidence</label>
										<?php if ($file) : ?>
											<div class="mb-2">
												<a href="<?= site_url('corrective_action/download/' . $file->id); ?>" class="text-primary" target="_blank">
													<i class="fa fa-paperclip mr-1"></i><?= htmlspecialchars($file->file_name_original); ?>
												</a>
											</div>
										<?php endif; ?>
										<input type="file" name="file_<?= $t->id; ?>" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
										<small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG, DOC, DOCX, XLS, XLSX (Max: 10MB)</small>
									</div>
								</div>
							</div>
						<?php endforeach;
						endif; ?>

						<!-- Action Buttons -->
						<div class="text-center mt-5 mb-5">
							<button type="button" class="btn btn-primary btn-lg mr-2" id="btn-save">
								<i class="fa fa-save mr-1"></i>Save
							</button>
							<button type="button" class="btn btn-success btn-lg" id="btn-submit">
								<i class="fa fa-paper-plane mr-1"></i>Ajukan
							</button>
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {

	// Character counter for textareas
	$('.textarea-counter').on('keyup', function() {
		var len = $(this).val().length;
		$(this).closest('.form-group').find('.char-count').text(len);
	});

	// Save function (AJAX with FormData)
	function saveCA(callback) {
		var formData = new FormData($('#form-ca')[0]);
		var btn = $('#btn-save');

		$.ajax({
			url: '<?= site_url("corrective_action/save"); ?>',
			data: formData,
			type: 'POST',
			dataType: 'JSON',
			processData: false,
			contentType: false,
			cache: false,
			beforeSend: function() {
				btn.attr('disabled', true);
				btn.html('<i class="spinner-border spinner-border-sm mr-1"></i>Saving...');
				$('#btn-submit').attr('disabled', true);
			},
			complete: function() {
				btn.attr('disabled', false);
				btn.html('<i class="fa fa-save mr-1"></i>Save');
				$('#btn-submit').attr('disabled', false);
			},
			success: function(result) {
				if (result.status == 1) {
					// Set ca_id for subsequent operations
					if (result.ca_id && !$('#ca_id').length) {
						$('#form-ca').append('<input type="hidden" name="ca_id" id="ca_id" value="' + result.ca_id + '">');
					} else if (result.ca_id) {
						$('#ca_id').val(result.ca_id);
					}

					if (typeof callback === 'function') {
						callback(result);
					} else {
						Swal.fire({
							title: 'Success!',
							icon: 'success',
							text: result.msg,
							timer: 2000
						});
					}
				} else {
					Swal.fire({
						title: 'Warning!',
						icon: 'warning',
						text: result.msg
					});
				}
			},
			error: function() {
				Swal.fire({
					title: 'Error!',
					icon: 'error',
					text: 'Server error. Please try again.',
					timer: 4000
				});
			}
		});
	}

	// Save button click
	$('#btn-save').on('click', function() {
		saveCA();
	});

	// Submit (Ajukan) button click
	$('#btn-submit').on('click', function() {
		Swal.fire({
			title: 'Konfirmasi',
			text: 'Apakah Anda yakin ingin mengajukan Corrective Action ini untuk approval?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Ya, Ajukan',
			cancelButtonText: 'Batal'
		}).then(function(result) {
			if (result.isConfirmed) {
				// First save, then submit
				saveCA(function(saveResult) {
					var caId = saveResult.ca_id || $('#ca_id').val();
					if (!caId) {
						Swal.fire({
							title: 'Warning!',
							icon: 'warning',
							text: 'Gagal mendapatkan ID Corrective Action. Silakan save terlebih dahulu.'
						});
						return;
					}

					$.ajax({
						url: '<?= site_url("corrective_action/submit"); ?>',
						data: { ca_id: caId },
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
									window.location.href = '<?= site_url("corrective_action"); ?>';
								});
							} else {
								Swal.fire({
									title: 'Warning!',
									icon: 'warning',
									text: result.msg
								});
							}
						},
						error: function() {
							Swal.fire({
								title: 'Error!',
								icon: 'error',
								text: 'Server error. Please try again.',
								timer: 4000
							});
						}
					});
				});
			}
		});
	});

});
</script>
