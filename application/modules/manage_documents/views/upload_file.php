<form id="form-upload">
	<div class="row">
		<label class="col-12 col-form-label">Document Name :</label>
		<div class="col-12">
			<input type="hidden" name="folder" value="<?= $folder; ?>">
			<input type="hidden" id="id" name="id" class="form-control" placeholder="" value="<?= isset($file) ? $file->id : ''; ?>" />
			<input type="hidden" id="parent_id" name="parent_id" class="form-control" placeholder="" value="<?= $parent_id; ?>" />
			<input type="text" class="form-control" id="description" placeholder="Description" name="description" value="<?= isset($file) ? $file->name : ''; ?>" autocomplete="off" />
			<span class="form-text text-danger invalid-feedback">Deskripsi harus di isi</span>
		</div>
	</div>

	<div class="row">
		<label class="col-12 col-form-label">Prepared By :</label>
		<div class="col-12">
			<select name="prepared_by" id="prepared_by" class="form-control select2">;
				<option value=""></option>
				<?php foreach ($users as $usr) : ?>
					<option value="<?= $usr->id_user; ?>" <?= (isset($file) && $file->prepared_by == $usr->id_user) ? 'selected' : ''; ?>><?= $usr->full_name; ?></option>
				<?php endforeach; ?>
			</select>
			<span class="form-text text-danger invalid-feedback">Prepared By harus di isi</span>
		</div>
	</div>

	<div class="row">
		<!-- <label class="col-12 col-form-label">File Type :</label> -->
		<div class="col-12 col-form-label">
			<input type="radio" class="d-none" checked name="flag_record" value="Y" />
			<!-- <div class="radio-inline">
						<label class="radio radio-primary">
							<input type="radio" name="flag_record" checked="checked" value="N" />
							<span></span>
							Need Approval
						</label>
						<label class="radio radio-primary">
							<span></span>
							Without Approval
						</label>
					</div>
					<span class="form-text text-muted">pilih salah satu</span> -->
		</div>
	</div>

	<!-- <div id="file-type">
				<div class="row">
					<label class="col-12 col-form-label">Review By :</label>
					<div class="col-12">
						<select name="reviewer_id" id="reviewer_id" class="form-control select2">;
							<option value=""></option>
							<?php foreach ($jabatan as $jbt) : ?>
								<option value="<?= $jbt->id; ?>" <?= (isset($file) && $file->reviewer_id == $jbt->id) ? 'selected' : ''; ?>><?= $jbt->nm_jabatan; ?></option>
							<?php endforeach; ?>
						</select>
						<span class="form-text text-danger invalid-feedback">Review By harus di isi</span>
					</div>
				</div>

				<div class="row">
					<label class="col-12 col-form-label">Approval By :</label>
					<div class="col-12">
						<select name="approval_id" id="approval_id" class="form-control select2">;
							<option value=""></option>
							<?php foreach ($jabatan as $jbt) : ?>
								<option value="<?= $jbt->id; ?>" <?= (isset($file) && $file->approval_id == $jbt->id) ? 'selected' : ''; ?>><?= $jbt->nm_jabatan; ?></option>
							<?php endforeach; ?>
						</select>
						<span class="form-text text-danger invalid-feedback">Approval By harus di isi</span>
					</div>
				</div>

				<div class="row">
					<label class="col-12 col-form-label">Distribusi :</label>
					<div class="col-12">
						<select name="distribute_id[]" multiple id="distribute_id" data-placeholder="Choose an options" class="form-control select2">;
							<option value=""></option>
							<?php foreach ($jabatan as $jbt) : ?>
								<option value="<?= $jbt->id; ?>" <?= isset($file) ? ((in_array($jbt->id, explode(',', $file->distribute_id))) ? 'selected' : '') : ''; ?>><?= $jbt->nm_jabatan; ?></option>
							<?php endforeach; ?>
						</select>
						<span class="form-text text-danger invalid-feedback">Distribusi By harus di isi</span>
					</div>
				</div>
			</div> -->

	<div class="form-group row mb-0">
		<label class="col-12 col-form-label">Upload Document :</label>
		<div class="col-12">
			<!-- Dropzone container -->
			<div id="dropzone-area" class="border border-2 border-dashed rounded p-5 text-center position-relative" style="border-style: dashed !important; border-color: #cbd5e0 !important; border-width: 2px !important; background-color: #f8f9fa; cursor: pointer; transition: all 0.3s ease;">
				<input type="file" name="image" id="image" class="position-absolute w-100 h-100" style="top: 0; left: 0; z-index: 2; cursor: pointer; opacity: 0;">
				
				<!-- Tampilan awal -->
				<div class="dropzone-msg py-3">
					<i class="fa fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
					<p class="font-weight-bold mb-1">Tarik & lepas file di sini, atau klik untuk memilih</p>
					<p class="text-muted small mb-0">Mendukung file baru via copy-paste (Ctrl + V)</p>
				</div>
				
				<!-- Tampilan preview saat file sudah dipilih -->
				<div class="dropzone-preview d-none py-3">
					<i class="far fa-file-alt fa-3x text-success mb-2"></i>
					<p class="font-weight-bold mb-1 file-name-label" style="word-break: break-all;"></p>
					<span class="badge badge-success file-size-label py-1 px-2"></span>
					<p class="text-muted small mt-3 mb-0">Klik atau seret file baru untuk mengganti</p>
				</div>
			</div>
			
			<span class="form-text text-muted mt-2">Tipe file: PDF, XLSX, DOCX (Maks. 10MB)</span>
			<span class="form-text text-danger invalid-feedback" id="image-error">Upload Document By harus di isi</span>
		</div>
		<?php if (isset($file)) : ?>
			<input type="hidden" name="old_file" id="old_file" value="<?= isset($file) ? $file->file_name : ''; ?>">
		<?php endif; ?>
	</div>

	<!-- <div class="col-6">
			<div class="row">
				<label class="col-12 col-form-label">Nomor :</label>
				<div class="col-12">
					<input type="text" name="number" id="number" class="form-control" placeholder="Nomor">
					<span class="form-text text-danger invalid-feedback">Prepared By harus di isi</span>
				</div>
			</div>

			<div class="row">
				<label class="col-12 col-form-label">Determination Date :</label>
				<div class="col-12">
					<input type="date" name="determination_date" id="determination_date" class="form-control datepicker" placeholder="<?= date('Y-m-d'); ?>">
					<span class="form-text text-danger invalid-feedback">Prepared By harus di isi</span>
				</div>
			</div>

			<div class="row">
				<label class="col-12 col-form-label">About :</label>
				<div class="col-12">
					<input type="text" name="about" id="about" class="form-control" placeholder="About">
					<span class="form-text text-danger invalid-feedback">Prepared By harus di isi</span>
				</div>
			</div>

			<div class="row">
				<label class="col-12 col-form-label">Status :</label>
				<div class="col-12">
					<input type="text" name="doc_status" id="doc_status" class="form-control" placeholder="Doc. Status">
					<span class="form-text text-danger invalid-feedback">Prepared By harus di isi</span>
				</div>
			</div>

			<div class="row">
				<label class="col-12 col-form-label">Publisher :</label>
				<div class="col-12">
					<input type="text" name="publisher" id="publisher" class="form-control" placeholder="Publisher">
					<span class="form-text text-danger invalid-feedback">Prepared By harus di isi</span>
				</div>
			</div>
		</div> -->


</form>

<script>
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: 'Choose an options',
			width: '100%',
			allowClear: true
		});

		const $dropzone = $('#dropzone-area');
		const $fileInput = $('#image');
		const $msgView = $('.dropzone-msg');
		const $previewView = $('.dropzone-preview');
		const $nameLabel = $('.file-name-label');
		const $sizeLabel = $('.file-size-label');

		// Efek hover & dragover
		$dropzone.on('dragover dragenter', function() {
			$dropzone.css({
				'border-color': '#3699ff',
				'background-color': '#f3f6f9'
			});
		});

		$dropzone.on('dragleave dragend drop', function() {
			$dropzone.css({
				'border-color': '#cbd5e0',
				'background-color': '#f8f9fa'
			});
		});

		// Format byte ke text readable
		function formatBytes(bytes, decimals = 2) {
			if (bytes === 0) return '0 Bytes';
			const k = 1024;
			const dm = decimals < 0 ? 0 : decimals;
			const sizes = ['Bytes', 'KB', 'MB', 'GB'];
			const i = Math.floor(Math.log(bytes) / Math.log(k));
			return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
		}

		// Update UI preview saat berkas dipilih
		$fileInput.on('change', function() {
			const files = this.files;
			if (files && files.length > 0) {
				const file = files[0];
				$nameLabel.text(file.name);
				$sizeLabel.text(formatBytes(file.size));
				
				$msgView.addClass('d-none');
				$previewView.removeClass('d-none');
				
				$fileInput.removeClass('is-invalid');
				$('#image-error').hide();
			} else {
				$msgView.removeClass('d-none');
				$previewView.addClass('d-none');
			}
		});

		// Paste handler (Ctrl + V)
		$(document).on('paste.uploadFile', function(e) {
			if ($('#upload').hasClass('show')) {
				const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
				if (clipboardData && clipboardData.files.length > 0) {
					const files = clipboardData.files;
					
					// Set file ke input
					const dataTransfer = new DataTransfer();
					dataTransfer.items.add(files[0]);
					$fileInput[0].files = dataTransfer.files;
					
					// Trigger change
					$fileInput.trigger('change');
				}
			}
		});

		// Bersihkan event paste jika modal ditutup/dihancurkan
		$('#upload').on('hidden.bs.modal', function () {
			$(document).off('paste.uploadFile');
		});
	});
</script>