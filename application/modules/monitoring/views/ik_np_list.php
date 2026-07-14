<div class="content d-flex flex-column flex-column-fluid p-0">
	<div class="d-flex flex-column-fluid">
		<div class="container mt-5">
			<h2 class="text-white font-weight-bolder mb-4"><i class="fa fa-file-alt text-white mr-2"></i><?= $title; ?></h2>

			<div class="card">
				<div class="card-body">
					<div class="d-flex justify-content-between mb-3">
						<div class="input-group w-25">
							<span class="input-group-text"><i class="fa fa-search"></i></span>
							<input type="text" id="searchInput" class="form-control" placeholder="Search">
						</div>
						<a href="<?= base_url('monitoring'); ?>" class="btn btn-danger"><i class="fa fa-reply mr-1"></i> Back</a>
					</div>
					<table id="dtTable" class="table table-bordered table-sm table-hover datatable">
						<thead class="table-light">
							<tr>
								<th width="30" class="text-center">No</th>
								<th>Document Number</th>
								<th>Document Name</th>
								<th width="110">Effective Date</th>
								<th width="60" class="text-center">Revision</th>
								<th>Prepare By</th>
								<th><?php if ($doc_status == 'PUB') : ?>PIC Reviewer<?php elseif ($doc_status == 'APV') : ?>PIC Approval<?php else : ?>PIC Reviewer<?php endif; ?></th>
								<?php if ($doc_status == 'PUB') : ?><th>PIC Approval</th><?php endif; ?>
								<th width="130" class="text-center">Status</th>
								<th width="100" class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if ($data) : $n = 0; foreach ($data as $v) : $n++; ?>
							<?php
								$sts_labels = [
									'DFT' => '<span class="label label-secondary label-inline">Draft</span>',
									'REV' => '<span class="label label-primary label-inline">Waiting Review</span>',
									'COR' => '<span class="label label-danger label-inline">Need Correction</span>',
									'APV' => '<span class="label label-info label-inline">Waiting Approval</span>',
									'RVI' => '<span class="label label-warning label-inline">Revision</span>',
									'PUB' => '<span class="label label-success label-inline">Published</span>',
								];
								$prepare_name = (isset($v->prepare_by) && isset($ArrUsers[$v->prepare_by])) ? $ArrUsers[$v->prepare_by]->full_name : '-';
								$reviewer_name = (isset($v->pic_reviewer) && isset($ArrUsers[$v->pic_reviewer])) ? $ArrUsers[$v->pic_reviewer]->full_name : '-';
								$approval_name = (isset($v->pic_approval) && isset($ArrUsers[$v->pic_approval])) ? $ArrUsers[$v->pic_approval]->full_name : '-';
								$pic_display = ($doc_status == 'APV') ? $approval_name : $reviewer_name;
							?>
							<tr>
								<td class="text-center"><?= $n; ?></td>
								<td><?= $v->doc_number; ?></td>
								<td><?= $v->doc_name; ?></td>
								<td class="text-center"><?= $v->effective_date ? date('d/m/Y', strtotime($v->effective_date)) : '-'; ?></td>
								<td class="text-center"><?= $v->doc_revision_number ?: '-'; ?></td>
								<td><?= $prepare_name; ?></td>
								<td><?= $pic_display; ?></td>
								<?php if ($doc_status == 'PUB') : ?><td><?= $approval_name; ?></td><?php endif; ?>
								<td class="text-center" style="white-space:nowrap;"><?= isset($sts_labels[$v->doc_status]) ? $sts_labels[$v->doc_status] : $v->doc_status; ?></td>
								<td class="text-center">
									<a href="<?= base_url('guides/view_file_page/' . $v->id); ?>" class="btn btn-xs btn-icon btn-info" title="View"><i class="fa fa-eye"></i></a>
									<?php if ($doc_status == 'REV' && isset($v->pic_reviewer) && $v->pic_reviewer == $current_user_id) : ?>
									<button type="button" class="btn btn-xs btn-icon btn-success btn-process-review" data-id="<?= $v->id; ?>" title="Process Review"><i class="fa fa-check-circle"></i></button>
									<?php endif; ?>
									<?php if ($doc_status == 'APV' && isset($v->pic_approval) && $v->pic_approval == $current_user_id) : ?>
									<button type="button" class="btn btn-xs btn-icon btn-success btn-process-approval" data-id="<?= $v->id; ?>" title="Process Approval"><i class="fa fa-check-circle"></i></button>
									<?php endif; ?>
									<?php if ($doc_status == 'PUB' && isset($can_request_revision) && $can_request_revision) : ?>
									<button type="button" class="btn btn-xs btn-icon btn-warning btn-request-revision" data-id="<?= $v->id; ?>" title="Request to Revision"><i class="fa fa-redo"></i></button>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Action Review -->
<div class="modal fade" id="modalReview" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header py-3">
				<h5 class="modal-title font-weight-bold">Action Review</h5>
				<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="review_doc_id">

				<div class="form-group">
					<div class="radio-inline">
						<label class="radio radio-success mb-2">
							<input type="radio" name="review_action" value="approve">
							<span></span>
							I agree to this file, and continue to the next process
						</label>
						<small class="text-success d-block ml-8 mb-4">Ready to Approval Process</small>

						<label class="radio radio-danger mb-2">
							<input type="radio" name="review_action" value="correction">
							<span></span>
							I don't agree, because some need corrections
						</label>
						<small class="text-muted d-block ml-8 mb-2">write down the reason</small>
					</div>
				</div>

				<div class="form-group" id="reason-group" style="display:none;">
					<textarea name="review_reason" id="review_reason" rows="4" class="form-control" placeholder="Reason" style="background-color: #f5f8fa;"></textarea>
				</div>
			</div>
			<div class="modal-footer py-2">
				<button type="button" class="btn btn-success" id="btnSubmitReview"><i class="fa fa-paper-plane mr-1"></i> Submit Review</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Action Approval -->
<div class="modal fade" id="modalApproval" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header py-3">
				<h5 class="modal-title font-weight-bold">Action Approval</h5>
				<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="approval_doc_id">

				<!-- Setujui & Publikasikan -->
				<div class="mb-4">
					<label class="radio radio-success mb-2">
						<input type="radio" name="approval_action" value="publish">
						<span></span>
						<strong class="ml-2 font-size-lg">Setujui & Publikasikan</strong>
					</label>
					<div class="ml-8 pl-2 border-left border-success border-3">
						<p class="text-success font-italic mb-2">Menyatakan bahwa dokumen ini telah melalui proses peninjauan dan disetujui, sehingga dapat dipublikasikan dan diberlakukan secara resmi.</p>
					</div>
					<div class="ml-8 mt-2" id="publish-date-group" style="display:none;">
						<label class="font-weight-bold">Published Date</label>
						<input type="text" id="published_date" class="form-control" placeholder="dd/mm/yyyy">
					</div>
				</div>

				<!-- Perlu Perbaikan -->
				<div class="mb-4">
					<label class="radio radio-danger mb-2">
						<input type="radio" name="approval_action" value="correction">
						<span></span>
						<strong class="ml-2 font-size-lg">Perlu Perbaikan</strong>
					</label>
					<div class="ml-8 pl-2 border-left border-danger border-3">
						<p class="text-danger font-italic mb-2">Menyatakan bahwa dokumen ini belum dapat disetujui dan memerlukan perbaikan serta koreksi sesuai dengan catatan dan alasan yang disampaikan.</p>
					</div>
					<div class="ml-8 mt-2" id="approval-note-group" style="display:none;">
						<textarea name="approval_note" id="approval_note" rows="4" class="form-control" placeholder="Note" style="background-color: #f5f8fa;"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer py-2">
				<button type="button" class="btn btn-outline-primary" id="btnSubmitApproval"><i class="fa fa-paper-plane mr-1"></i> Submit to Publish</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Request Revision -->
<div class="modal fade" id="modalRevision" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header py-3">
				<h5 class="modal-title font-weight-bold">Request to Revision</h5>
				<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="revision_doc_id">
				<div class="alert alert-warning border border-warning">
					<h5 class="font-weight-bold"><i class="fa fa-info-circle text-warning mr-1"></i> WARNING!!!</h5>
					<p class="mb-0">Dengan ini menyatakan bahwa dokumen tersebut harus di revisi. Mohon berikan alasan dengan jelas. Terima Kasih.</p>
				</div>
				<div class="form-group">
					<textarea id="revision_reason" rows="6" class="form-control" placeholder="Reason for revision" style="background-color: #f5f8fa; border-color: #f5c542;"></textarea>
				</div>
			</div>
			<div class="modal-footer py-2">
				<button type="button" class="btn btn-outline-success" id="btnSubmitRevision"><i class="fa fa-paper-plane mr-1"></i> Submit to Revision</button>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	/* === REVIEW MODAL === */
	$(document).on('click', '.btn-process-review', function() {
		var id = $(this).data('id');
		$('#review_doc_id').val(id);
		$('input[name="review_action"]').prop('checked', false);
		$('#review_reason').val('');
		$('#reason-group').hide();
		$('#modalReview').modal('show');
	});

	$(document).on('change', 'input[name="review_action"]', function() {
		if ($(this).val() == 'correction') {
			$('#reason-group').show();
		} else {
			$('#reason-group').hide();
		}
	});

	$(document).on('click', '#btnSubmitReview', function() {
		var id = $('#review_doc_id').val();
		var action = $('input[name="review_action"]:checked').val();
		var reason = $('#review_reason').val();

		if (!action) {
			Swal.fire('Warning!', 'Please select an action.', 'warning');
			return;
		}

		if (action == 'correction' && !reason) {
			Swal.fire('Warning!', 'Please write down the reason.', 'warning');
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Processing...');

		$.post('<?= base_url("guides/submit_review"); ?>', { id: id, action: action, reason: reason }, function(result) {
			btn.prop('disabled', false).html('<i class="fa fa-paper-plane mr-1"></i> Submit Review');
			if (result.status == 1) {
				$('#modalReview').modal('hide');
				Swal.fire("Success!", result.msg, "success").then(function() { location.reload(); });
			} else {
				Swal.fire("Warning!", result.msg, "warning");
			}
		}, 'json').fail(function() {
			btn.prop('disabled', false).html('<i class="fa fa-paper-plane mr-1"></i> Submit Review');
			Swal.fire("Error!", "Server time out.", "error");
		});
	});

	/* === APPROVAL MODAL === */
	var selectedApprovalAction = '';

	$(document).on('click', '.btn-process-approval', function() {
		var id = $(this).data('id');
		$('#approval_doc_id').val(id);
		$('input[name="approval_action"]').prop('checked', false);
		selectedApprovalAction = '';
		$('#approval_note').val('');
		$('#publish-date-group').hide();
		$('#approval-note-group').hide();
		$('#modalApproval').modal('show');

		// Init flatpickr after modal opens
		setTimeout(function() {
			if (!$('#published_date').hasClass('flatpickr-input')) {
				$('#published_date').flatpickr({ dateFormat: "d/m/Y", defaultDate: "today" });
			}
		}, 300);
	});

	$(document).on('click', 'input[name="approval_action"]', function() {
		selectedApprovalAction = $(this).val();
		if (selectedApprovalAction == 'publish') {
			$('#publish-date-group').show();
			$('#approval-note-group').hide();
		} else {
			$('#publish-date-group').hide();
			$('#approval-note-group').show();
		}
	});

	$(document).on('click', '#btnSubmitApproval', function() {
		var id = $('#approval_doc_id').val();
		var action = selectedApprovalAction;
		var note = $('#approval_note').val();
		var published_date = $('#published_date').val();

		if (!action) {
			Swal.fire('Warning!', 'Pilih salah satu aksi.', 'warning');
			return false;
		}

		if (action == 'correction' && !note) {
			Swal.fire('Warning!', 'Tulis alasan perbaikan.', 'warning');
			return false;
		}

		if (action == 'publish' && !published_date) {
			Swal.fire('Warning!', 'Pilih tanggal publish.', 'warning');
			return false;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Processing...');

		$.post('<?= base_url("guides/submit_approval"); ?>', { id: id, action: action, note: note, published_date: published_date }, function(result) {
			btn.prop('disabled', false).html('<i class="fa fa-paper-plane mr-1"></i> Submit to Publish');
			if (result.status == 1) {
				$('#modalApproval').modal('hide');
				Swal.fire("Success!", result.msg, "success").then(function() { location.reload(); });
			} else {
				Swal.fire("Warning!", result.msg, "warning");
			}
		}, 'json').fail(function() {
			btn.prop('disabled', false).html('<i class="fa fa-paper-plane mr-1"></i> Submit to Publish');
			Swal.fire("Error!", "Server time out.", "error");
		});
	});
	/* === REQUEST REVISION === */
	$(document).on('click', '.btn-request-revision', function() {
		var id = $(this).data('id');
		$('#revision_doc_id').val(id);
		$('#revision_reason').val('');
		$('#modalRevision').modal('show');
	});

	$(document).on('click', '#btnSubmitRevision', function() {
		var id = $('#revision_doc_id').val();
		var reason = $('#revision_reason').val();

		if (!reason) {
			Swal.fire('Warning!', 'Mohon tulis alasan revisi.', 'warning');
			return false;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Processing...');

		$.post('<?= base_url("guides/request_revision"); ?>', { id: id, reason: reason }, function(result) {
			btn.prop('disabled', false).html('<i class="fa fa-paper-plane mr-1"></i> Submit to Revision');
			if (result.status == 1) {
				$('#modalRevision').modal('hide');
				Swal.fire("Success!", result.msg, "success").then(function() { location.reload(); });
			} else {
				Swal.fire("Warning!", result.msg, "warning");
			}
		}, 'json').fail(function() {
			btn.prop('disabled', false).html('<i class="fa fa-paper-plane mr-1"></i> Submit to Revision');
			Swal.fire("Error!", "Server time out.", "error");
		});
	});
});
</script>
