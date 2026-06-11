<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<form id="formProgram">
				<input type="hidden" name="id" id="program_id" value="<?= isset($program->id) ? $program->id : ''; ?>">

				<div class="card card-stretch shadow card-custom">
					<div class="card-header justify-content-between d-flex align-items-center">
						<h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i><?= $title; ?></h2>
						<a href="<?= site_url('audit_preparation'); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Back</a>
					</div>

					<div class="card-body">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs nav-pills border-0 mb-5" id="tabProgram" role="tablist">
							<li class="nav-item" role="presentation">
								<button class="nav-link active" id="tab-header-tab" data-toggle="tab" data-target="#tab-header" type="button" role="tab" aria-controls="tab-header" aria-selected="true">Header</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link" id="tab-evaluation-tab" data-toggle="tab" data-target="#tab-evaluation" type="button" role="tab" aria-controls="tab-evaluation" aria-selected="false" style="display:none;">Evaluasi Audit Sebelumnya</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link" id="tab-critical-tab" data-toggle="tab" data-target="#tab-critical" type="button" role="tab" aria-controls="tab-critical" aria-selected="false">Improvement Program Audit</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link" id="tab-opportunity-tab" data-toggle="tab" data-target="#tab-opportunity" type="button" role="tab" aria-controls="tab-opportunity" aria-selected="false">Isu Proses</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link" id="tab-schedule-tab" data-toggle="tab" data-target="#tab-schedule" type="button" role="tab" aria-controls="tab-schedule" aria-selected="false">Jadwal Audit</button>
							</li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content p-3 rounded-lg border">
							<!-- Tab 1: Header -->
							<div class="tab-pane fade show active" id="tab-header" role="tabpanel" aria-labelledby="tab-header-tab">
								<?php $this->load->view('audit_preparation/partials/header'); ?>
							</div>

							<!-- Tab 2: Evaluasi Audit Sebelumnya -->
							<div class="tab-pane fade" id="tab-evaluation" role="tabpanel" aria-labelledby="tab-evaluation-tab">
								<?php $this->load->view('audit_preparation/partials/evaluation'); ?>
							</div>

							<!-- Tab 3: Critical Issue -->
							<div class="tab-pane fade" id="tab-critical" role="tabpanel" aria-labelledby="tab-critical-tab">
								<?php $this->load->view('audit_preparation/partials/critical'); ?>
							</div>

							<!-- Tab 4: Potensi Peluang/Masalah -->
							<div class="tab-pane fade" id="tab-opportunity" role="tabpanel" aria-labelledby="tab-opportunity-tab">
								<?php $this->load->view('audit_preparation/partials/opportunity'); ?>
							</div>

							<!-- Tab 5: Jadwal Audit -->
							<div class="tab-pane fade" id="tab-schedule" role="tabpanel" aria-labelledby="tab-schedule-tab">
								<?php $this->load->view('audit_preparation/partials/schedule'); ?>
							</div>
						</div>

						<!-- Save Button -->
						<div class="mt-4 text-right">
							<button type="button" class="btn btn-primary save-program"><i class="fa fa-save mr-1"></i> Save</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	var siteurl = '<?= site_url(); ?>';

	$(document).ready(function() {
		// Fix Select2 width when a hidden tab becomes visible
		$('a[data-toggle="tab"], button[data-toggle="tab"]').on('shown.bs.tab', function(e) {
			var targetPane = $($(e.target).data('target') || $(e.target).attr('href'));
			if (targetPane.length) {
				// Initialize Select2 that haven't been initialized yet (for hidden tabs)
				targetPane.find('.select2-schedule-process').each(function() {
					if (!$(this).hasClass('select2-hidden-accessible')) {
						$(this).select2({ placeholder: "Select Process", allowClear: true, width: "100%" });
					}
				});
				targetPane.find('.select2-schedule-auditor').each(function() {
					if (!$(this).hasClass('select2-hidden-accessible')) {
						$(this).select2({ placeholder: "Select Auditor", allowClear: true, width: "100%" });
					}
				});
				targetPane.find('.select2-schedule-auditee').each(function() {
					if (!$(this).hasClass('select2-hidden-accessible')) {
						$(this).select2({ placeholder: "Select Department", allowClear: true, width: "100%" });
					}
				});
				// Also init generic .select2 that haven't been initialized
				targetPane.find('select.select2').each(function() {
					if (!$(this).hasClass('select2-hidden-accessible')) {
						$(this).select2({ width: '100%' });
					}
				});
			}
		});
	});
</script>

<script>
/**
 * Client-side validation for Audit Preparation form
 * Validates required fields, company max length, schedule time/date constraints
 */
function validateForm() {
	var e = 0;

	// Validate all required fields in header section
	$('#tab-header .required').each(function() {
		var val = $(this).val();
		if (val == '' || val == null || (typeof val === 'string' && val.trim() == '')) {
			$(this).addClass('is-invalid');
			e++;
		} else {
			$(this).removeClass('is-invalid');
		}
	});

	// Validate Company max 255 characters
	var companyVal = $('#company').val();
	if (companyVal && companyVal.length > 255) {
		$('#company').addClass('is-invalid');
		e++;
	}

	// Validate schedule rows: end_time > start_time
	$('.schedule-row').each(function() {
		var startTime = $(this).find('.start-time').val();
		var endTime = $(this).find('.end-time').val();
		if (startTime && endTime && endTime <= startTime) {
			$(this).find('.end-time').addClass('is-invalid');
			e++;
		} else {
			$(this).find('.end-time').removeClass('is-invalid');
		}
	});

	// Validate schedule rows: audit_date >= today (only for new/empty program or new rows)
	var isEdit = ($('#program_id').val() !== '');
	if (!isEdit) {
		var today = new Date().toISOString().split('T')[0];
		$('.schedule-row').each(function() {
			var dateVal = $(this).find('.audit-date').val();
			if (dateVal && dateVal < today) {
				$(this).find('.audit-date').addClass('is-invalid');
				e++;
			} else {
				$(this).find('.audit-date').removeClass('is-invalid');
			}
		});
	} else {
		$('.schedule-row').each(function() {
			$(this).find('.audit-date').removeClass('is-invalid');
		});
	}

	return e;
}

/**
 * Detect scheduling conflicts: same auditor with overlapping times on same date
 * Returns array of conflict descriptions
 */
function detectConflicts() {
	var schedules = [];
	$('.schedule-row').each(function(index) {
		var auditorId = $(this).find('.select2-schedule-auditor').val();
		var auditDate = $(this).find('.audit-date').val();
		var startTime = $(this).find('.start-time').val();
		var endTime = $(this).find('.end-time').val();
		var auditorName = $(this).find('.select2-schedule-auditor option:selected').text();

		if (auditorId && auditDate && startTime && endTime) {
			schedules.push({
				index: index + 1,
				auditor_id: auditorId,
				auditor_name: auditorName,
				audit_date: auditDate,
				start_time: startTime,
				end_time: endTime
			});
		}
	});

	var conflicts = [];
	for (var i = 0; i < schedules.length; i++) {
		for (var j = i + 1; j < schedules.length; j++) {
			if (schedules[i].auditor_id === schedules[j].auditor_id &&
				schedules[i].audit_date === schedules[j].audit_date) {
				// Check time overlap: row1.start < row2.end AND row2.start < row1.end
				if (schedules[i].start_time < schedules[j].end_time &&
					schedules[j].start_time < schedules[i].end_time) {
					conflicts.push(
						'Auditor "' + schedules[i].auditor_name + '" pada tanggal ' + schedules[i].audit_date +
						' (Baris ' + schedules[i].index + ' & ' + schedules[j].index + ')'
					);
				}
			}
		}
	}

	return conflicts;
}

/**
 * Save button click handler
 */
$(document).on('click', '.save-program', function(e) {
	e.preventDefault();
	var $btn = $(this);

	// 1. Validate form
	var errors = validateForm();
	if (errors > 0) {
		Swal.fire({
			title: 'Validation Error',
			icon: 'error',
			text: 'Terdapat ' + errors + ' field yang belum diisi atau tidak valid. Silakan periksa kembali.'
		});
		return false;
	}

	// 2. Detect scheduling conflicts (non-blocking warning)
	var conflicts = detectConflicts();
	if (conflicts.length > 0) {
		Swal.fire({
			title: 'Scheduling Conflict Detected',
			icon: 'warning',
			html: '<p>Ditemukan konflik jadwal auditor:</p><ul style="text-align:left;">' +
				conflicts.map(function(c) { return '<li>' + c + '</li>'; }).join('') +
				'</ul><p>Apakah Anda tetap ingin menyimpan?</p>',
			showCancelButton: true,
			confirmButtonText: 'Ya, Simpan',
			cancelButtonText: 'Batal'
		}).then(function(result) {
			if (result.isConfirmed) {
				submitForm($btn);
			}
		});
	} else {
		submitForm($btn);
	}
});

/**
 * Submit form data via AJAX
 */
function submitForm($btn) {
	var formData = new FormData($('#formProgram')[0]);

	$.ajax({
		url: siteurl + 'audit_preparation/save',
		data: formData,
		type: 'POST',
		dataType: 'JSON',
		processData: false,
		contentType: false,
		beforeSend: function() {
			$btn.prop('disabled', true);
			$btn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');
		},
		complete: function() {
			$btn.prop('disabled', false);
			$btn.html('<i class="fa fa-save mr-1"></i> Save');
		},
		success: function(result) {
			if (result.status == 1) {
				Swal.fire({
					title: 'Success!',
					icon: 'success',
					text: result.msg
				}).then(function() {
					window.location.href = siteurl + 'audit_preparation';
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
				text: 'Server error, please try again.'
			});
		}
	});
}
</script>
