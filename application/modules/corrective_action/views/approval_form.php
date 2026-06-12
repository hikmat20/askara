<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?> - Approval</h2>
				</div>
				<div class="card-body">

					<!-- Header Section (Read-Only) -->
					<div class="row mb-4">
						<div class="col-md-6">
							<div class="form-group">
								<label class="font-weight-bold">Prosedur</label>
								<p class="form-control-plaintext"><?= isset($header->process_name) && $header->process_name ? strip_tags($header->process_name) : '-'; ?></p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="font-weight-bold">Date</label>
								<p class="form-control-plaintext"><?= isset($header->audit_date) && $header->audit_date ? date('d-m-Y', strtotime($header->audit_date)) : '-'; ?></p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="font-weight-bold">Department</label>
								<p class="form-control-plaintext"><?= isset($header->department_name) && $header->department_name ? $header->department_name : '-'; ?></p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="font-weight-bold">Auditor</label>
								<p class="form-control-plaintext"><?= isset($header->auditor_name) && $header->auditor_name ? $header->auditor_name : '-'; ?></p>
							</div>
						</div>
					</div>

					<hr>

					<!-- Rejection History -->
					<?php if (isset($rejections) && !empty($rejections)) : ?>
						<div class="alert alert-warning mb-4">
							<h5 class="font-weight-bold mb-3"><i class="fa fa-history mr-2"></i>Riwayat Penolakan</h5>
							<?php foreach ($rejections as $rejection) : ?>
								<div class="border-bottom pb-2 mb-2">
									<p class="mb-1"><strong>Alasan:</strong> <?= htmlspecialchars($rejection->reason); ?></p>
									<small class="text-muted">
										Ditolak pada: <?= date('d-m-Y H:i', strtotime($rejection->rejected_at)); ?>
										<?php if (isset($rejection->rejected_by_name)) : ?>
											| Oleh: <?= htmlspecialchars($rejection->rejected_by_name); ?>
										<?php endif; ?>
									</small>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<!-- Temuan Details (Read-Only) -->
					<?php if (isset($temuan) && $temuan) :
						$temuanIndex = 0;
						foreach ($temuan as $t) : $temuanIndex++;
							// Find matching detail for this temuan
							$detail = null;
							if (isset($details) && $details) {
								foreach ($details as $d) {
									if ($d->temuan_id == $t->id) {
										$detail = $d;
										break;
									}
								}
							}
							// Find matching file for this temuan
							$file = null;
							if (isset($files) && $files) {
								foreach ($files as $f) {
									if ($f->temuan_id == $t->id) {
										$file = $f;
										break;
									}
								}
							}
					?>
						<div class="card card-custom card-border mb-4">
							<div class="card-header py-3">
								<div class="card-title">
									<h4 class="mb-0">
										Temuan <?= $temuanIndex; ?>: <?= htmlspecialchars($t->description); ?>
										<?php
										$kategori = isset($t->kategori) ? $t->kategori : '';
										$badgeClass = 'label-light-info';
										if (strtolower($kategori) == 'major') {
											$badgeClass = 'label-light-danger';
										} elseif (strtolower($kategori) == 'minor') {
											$badgeClass = 'label-light-warning';
										} elseif (strtolower($kategori) == 'ofi') {
											$badgeClass = 'label-light-info';
										}
										?>
										<span class="label label-lg <?= $badgeClass; ?> label-inline ml-2"><?= $kategori; ?></span>
									</h4>
								</div>
							</div>
							<div class="card-body">
								<div class="form-group">
									<label class="font-weight-bold">Fakta</label>
									<div class="border rounded p-3 bg-light"><?= $detail ? nl2br(htmlspecialchars($detail->fakta)) : '-'; ?></div>
								</div>
								<div class="form-group">
									<label class="font-weight-bold">Kesimpulan Penyebab</label>
									<div class="border rounded p-3 bg-light"><?= $detail ? nl2br(htmlspecialchars($detail->kesimpulan_penyebab)) : '-'; ?></div>
								</div>
								<div class="form-group">
									<label class="font-weight-bold">Correction</label>
									<div class="border rounded p-3 bg-light"><?= $detail ? nl2br(htmlspecialchars($detail->correction)) : '-'; ?></div>
								</div>
								<div class="form-group">
									<label class="font-weight-bold">Corrective Action</label>
									<div class="border rounded p-3 bg-light"><?= $detail ? nl2br(htmlspecialchars($detail->corrective_action)) : '-'; ?></div>
								</div>
								<?php if ($file) : ?>
									<div class="form-group">
										<label class="font-weight-bold">Evidence</label>
										<div>
											<a href="<?= site_url('corrective_action/download/' . $file->id); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
												<i class="fa fa-download mr-1"></i><?= htmlspecialchars($file->file_name_original); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach;
					endif; ?>

					<hr>

					<!-- Approval Decision Section -->
					<?php if (isset($ca) && $ca->status_ca == 'waiting_approval') : ?>
						<div class="card card-custom card-border mb-4">
							<div class="card-header py-3">
								<div class="card-title">
									<h4 class="mb-0"><i class="fa fa-gavel mr-2"></i>Keputusan Approval</h4>
								</div>
							</div>
							<div class="card-body">
								<div class="form-group">
									<label class="font-weight-bold">Alasan Reject</label>
									<textarea id="alasan_reject" class="form-control" rows="4" maxlength="2000" placeholder="Isi alasan penolakan jika ingin menolak..."></textarea>
									<small class="text-muted"><span id="char_count">0</span> / 2000 karakter</small>
								</div>
								<div class="mt-4">
									<button type="button" id="btn-approve" class="btn btn-success mr-2">
										<i class="fa fa-check mr-1"></i>Approve
									</button>
									<button type="button" id="btn-reject" class="btn btn-danger mr-2">
										<i class="fa fa-times mr-1"></i>Reject
									</button>
									<a href="<?= site_url('corrective_action/approval_index'); ?>" class="btn btn-secondary">
										<i class="fa fa-arrow-left mr-1"></i>Back
									</a>
								</div>
							</div>
						</div>
					<?php else : ?>
						<!-- Status is approved or other: show only Back button -->
						<div class="mt-4">
							<a href="<?= site_url('corrective_action/approval_index'); ?>" class="btn btn-secondary">
								<i class="fa fa-arrow-left mr-1"></i>Back
							</a>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
</div>

<?php if (isset($ca) && $ca->status_ca == 'waiting_approval') : ?>
<script>
$(document).ready(function() {
	var caId = '<?= $ca->id; ?>';

	// Character counter for alasan_reject textarea
	$('#alasan_reject').on('input keyup', function() {
		var len = $(this).val().length;
		$('#char_count').text(len);
	});

	// Approve button
	$('#btn-approve').on('click', function() {
		if (!confirm('Apakah Anda yakin ingin menyetujui Corrective Action ini?')) {
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');

		$.ajax({
			url: '<?= site_url("corrective_action/approve"); ?>',
			type: 'POST',
			dataType: 'json',
			data: { ca_id: caId },
			success: function(response) {
				if (response.status == 1) {
					alert(response.msg);
					window.location.href = '<?= site_url("corrective_action/approval_index"); ?>';
				} else {
					alert(response.msg || 'Terjadi kesalahan. Silakan coba lagi.');
					btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Approve');
				}
			},
			error: function() {
				alert('Terjadi kesalahan server. Silakan coba lagi.');
				btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Approve');
			}
		});
	});

	// Reject button
	$('#btn-reject').on('click', function() {
		var alasan = $.trim($('#alasan_reject').val());

		if (alasan === '') {
			alert('Alasan reject wajib diisi.');
			$('#alasan_reject').focus();
			return;
		}

		if (!confirm('Apakah Anda yakin ingin menolak Corrective Action ini?')) {
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');

		$.ajax({
			url: '<?= site_url("corrective_action/reject"); ?>',
			type: 'POST',
			dataType: 'json',
			data: {
				ca_id: caId,
				alasan_reject: alasan
			},
			success: function(response) {
				if (response.status == 1) {
					alert(response.msg);
					window.location.href = '<?= site_url("corrective_action/approval_index"); ?>';
				} else {
					alert(response.msg || 'Terjadi kesalahan. Silakan coba lagi.');
					btn.prop('disabled', false).html('<i class="fa fa-times mr-1"></i>Reject');
				}
			},
			error: function() {
				alert('Terjadi kesalahan server. Silakan coba lagi.');
				btn.prop('disabled', false).html('<i class="fa fa-times mr-1"></i>Reject');
			}
		});
	});
});
</script>
<?php endif; ?>
