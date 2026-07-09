<div class="mb-3">
    <h5 class="font-weight-bold mb-3"><i class="fa fa-calendar-alt text-primary mr-2"></i>Jadwal Audit</h5>
    <div class="table-responsive">
        <table id="table-schedule" class="table table-sm table-bordered table-condensed table-hover">
            <thead class="table-light text-center">
                <tr>
                    <th width="40">No</th>
                    <th width="200">Process</th>
                    <th width="180">Auditor</th>
                    <th width="180">Department - Company</th>
                    <th width="130">Date</th>
                    <th width="100">Start Time</th>
                    <th width="100">End Time</th>
                    <th width="60">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($schedules)) : ?>
                    <?php foreach ($schedules as $k => $schedule) : ?>
                        <tr class="schedule-row">
                            <td class="text-center row-number"><?= $k + 1; ?></td>
                            <td>
                                <input type="hidden" name="schedule_record_id[]" value="<?= $schedule->id; ?>">
                                <?php if (!empty($schedule->requirement_id)) : ?>
                                    <input type="hidden" name="schedule_process_id[]" value="">
                                    <input type="hidden" name="schedule_process_name_free[]" value="">
                                    <select name="schedule_requirement_id[]" class="form-control select2-schedule-requirement required" data-placeholder="Select Standard">
                                        <option value=""></option>
                                        <?php if (!empty($requirements)) foreach ($requirements as $r) : ?>
                                            <option value="<?= $r->id; ?>" <?= ($r->id == $schedule->requirement_id) ? 'selected' : ''; ?>><?= htmlspecialchars($r->nama); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif (!empty($schedule->process_name_free)) : ?>
                                    <input type="hidden" name="schedule_process_id[]" value="">
                                    <input type="hidden" name="schedule_requirement_id[]" value="">
                                    <input type="text" name="schedule_process_name_free[]" class="form-control required" value="<?= htmlspecialchars($schedule->process_name_free); ?>">
                                <?php else : ?>
                                    <input type="hidden" name="schedule_process_name_free[]" value="">
                                    <input type="hidden" name="schedule_requirement_id[]" value="">
                                    <select name="schedule_process_id[]" class="form-control select2-schedule-process required" data-placeholder="Select Process">
                                        <option value=""></option>
                                        <?php if (!empty($procedures)) foreach ($procedures as $p) : ?>
                                            <option value="<?= $p->id; ?>" <?= ($p->id == $schedule->process_id) ? 'selected' : ''; ?>><?= strip_tags($p->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </td>
                            <td>
                                <select name="schedule_auditor_id[]" class="form-control select2-schedule-auditor required" data-placeholder="Select Auditor">
                                    <option value=""></option>
                                    <?php if (!empty($auditors)) foreach ($auditors as $a) : ?>
                                        <option value="<?= $a->id; ?>" <?= ($a->id == $schedule->auditor_id) ? 'selected' : ''; ?>><?= $a->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <?php if (!empty($schedule->auditee_name_free)) : ?>
                                    <input type="hidden" name="schedule_auditee_id[]" value="">
                                    <input type="text" name="schedule_auditee_name_free[]" class="form-control required" value="<?= htmlspecialchars($schedule->auditee_name_free); ?>">
                                <?php else : ?>
                                    <input type="hidden" name="schedule_auditee_name_free[]" value="">
                                    <?php
                                        $selectedDeptId = '';
                                        if (!empty($schedule->auditees) && isset($schedule->auditees[0])) {
                                            $selectedDeptId = $schedule->auditees[0]->department_id;
                                        }
                                    ?>
                                    <select name="schedule_auditee_id[]" class="form-control select2-schedule-auditee required" data-placeholder="Select Department - Company">
                                        <option value=""></option>
                                        <?php if (!empty($departments)) foreach ($departments as $d) : ?>
                                            <option value="<?= $d->id; ?>" <?= ($d->id == $selectedDeptId) ? 'selected' : ''; ?>><?= $d->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="date" name="schedule_date[]" class="form-control audit-date required" value="<?= $schedule->audit_date; ?>">
                            </td>
                            <td>
                                <input type="time" name="schedule_start_time[]" class="form-control start-time required" value="<?= substr($schedule->start_time, 0, 5); ?>">
                            </td>
                            <td>
                                <input type="time" name="schedule_end_time[]" class="form-control end-time required" value="<?= substr($schedule->end_time, 0, 5); ?>">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-schedule" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-2">
        <button type="button" class="btn btn-sm btn-primary" id="btn-add-schedule"><i class="fa fa-plus mr-1"></i>Audit Proses</button>
        <button type="button" class="btn btn-sm btn-success" id="btn-add-schedule-free"><i class="fa fa-plus mr-1"></i>Audit Free</button>
        <button type="button" class="btn btn-sm btn-warning" id="btn-add-schedule-requirement"><i class="fa fa-plus mr-1"></i>Audit Persyaratan</button>
        <span class="text-muted ml-2 schedule-count-info">
            <?php $count = !empty($schedules) ? count($schedules) : 0; ?>
            (<span id="schedule-row-count"><?= $count; ?></span> / 50 rows)
        </span>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2 only if the schedule tab is currently visible
    if ($('#tab-schedule').hasClass('show') || $('#tab-schedule').hasClass('active') || $('#tab-schedule').is(':visible')) {
        initScheduleSelect2();
    }

    // Audit Proses button (Process = Select from procedures)
    $(document).on('click', '#btn-add-schedule', function() {
        if (!checkScheduleMax()) return false;
        addScheduleRow('process');
    });

    // Audit Free button (Process = text input)
    $(document).on('click', '#btn-add-schedule-free', function() {
        if (!checkScheduleMax()) return false;
        addScheduleRow('free');
    });

    // Audit Persyaratan button (Process = Select from requirements/standards)
    $(document).on('click', '#btn-add-schedule-requirement', function() {
        if (!checkScheduleMax()) return false;
        addScheduleRow('requirement');
    });

    // Delete row with SweetAlert confirmation
    $(document).on('click', '.btn-delete-schedule', function() {
        var $row = $(this).closest('tr');
        Swal.fire({
            title: 'Hapus Jadwal?',
            icon: 'question',
            text: 'Apakah Anda yakin ingin menghapus baris jadwal ini?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $row.find('.select2-schedule-process').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
                $row.find('.select2-schedule-auditor').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
                $row.find('.select2-schedule-auditee').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
                $row.find('.select2-schedule-requirement').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
                });
                $row.remove();
                renumberScheduleRows();
                updateScheduleRowCount();
            }
        });
    });
});

function checkScheduleMax() {
    var rowCount = $('#table-schedule tbody tr.schedule-row').length;
    if (rowCount >= 50) {
        Swal.fire({
            title: 'Info',
            icon: 'info',
            text: 'Maksimum 50 jadwal audit telah tercapai.',
            timer: 3000
        });
        return false;
    }
    return true;
}

function addScheduleRow(type) {
    var rowCount = $('#table-schedule tbody tr.schedule-row').length;
    var rowNum = rowCount + 1;
    var isFreeText = (type === 'free');
    var isRequirement = (type === 'requirement');

    var processCell = '';
    if (isFreeText) {
        processCell = '<input type="hidden" name="schedule_process_id[]" value="">' +
            '<input type="hidden" name="schedule_requirement_id[]" value="">' +
            '<input type="text" name="schedule_process_name_free[]" class="form-control required" placeholder="Input Process">';
    } else if (isRequirement) {
        var requirementOptions = '<option value=""></option>';
        <?php if (!empty($requirements)) : ?>
            <?php foreach ($requirements as $r) : ?>
                requirementOptions += '<option value="<?= $r->id; ?>"><?= addslashes($r->nama); ?></option>';
            <?php endforeach; ?>
        <?php endif; ?>
        processCell = '<input type="hidden" name="schedule_process_id[]" value="">' +
            '<input type="hidden" name="schedule_process_name_free[]" value="">' +
            '<select name="schedule_requirement_id[]" class="form-control select2-schedule-requirement required" data-placeholder="Select Standard">' + requirementOptions + '</select>';
    } else {
        var processOptions = '<option value=""></option>';
        <?php if (!empty($procedures)) : ?>
            <?php foreach ($procedures as $p) : ?>
                processOptions += '<option value="<?= $p->id; ?>"><?= addslashes(strip_tags($p->name)); ?></option>';
            <?php endforeach; ?>
        <?php endif; ?>
        processCell = '<input type="hidden" name="schedule_process_name_free[]" value="">' +
            '<input type="hidden" name="schedule_requirement_id[]" value="">' +
            '<select name="schedule_process_id[]" class="form-control select2-schedule-process required" data-placeholder="Select Process">' + processOptions + '</select>';
    }

    var auditorOptions = '<option value=""></option>';
    <?php if (!empty($auditors)) : ?>
        <?php foreach ($auditors as $a) : ?>
            auditorOptions += '<option value="<?= $a->id; ?>"><?= addslashes($a->name); ?></option>';
        <?php endforeach; ?>
    <?php endif; ?>

    var auditeeCell = '';
    if (isFreeText || isRequirement) {
        auditeeCell = '<input type="hidden" name="schedule_auditee_id[]" value="">' +
            '<input type="text" name="schedule_auditee_name_free[]" class="form-control required" placeholder="Input Department - Company">';
    } else {
        var auditeeOptions = '<option value=""></option>';
        <?php if (!empty($departments)) : ?>
            <?php foreach ($departments as $d) : ?>
                auditeeOptions += '<option value="<?= $d->id; ?>"><?= addslashes($d->name); ?></option>';
            <?php endforeach; ?>
        <?php endif; ?>
        auditeeCell = '<input type="hidden" name="schedule_auditee_name_free[]" value="">' +
            '<select name="schedule_auditee_id[]" class="form-control select2-schedule-auditee required" data-placeholder="Select Department - Company">' + auditeeOptions + '</select>';
    }

    var html = '<tr class="schedule-row">' +
        '<td class="text-center row-number">' + rowNum + '</td>' +
        '<td><input type="hidden" name="schedule_record_id[]" value="">' + processCell + '</td>' +
        '<td><select name="schedule_auditor_id[]" class="form-control select2-schedule-auditor required" data-placeholder="Select Auditor">' + auditorOptions + '</select></td>' +
        '<td>' + auditeeCell + '</td>' +
        '<td><input type="date" name="schedule_date[]" class="form-control audit-date required"></td>' +
        '<td><input type="time" name="schedule_start_time[]" class="form-control start-time required"></td>' +
        '<td><input type="time" name="schedule_end_time[]" class="form-control end-time required"></td>' +
        '<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-schedule" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></button></td>' +
        '</tr>';

    $('#table-schedule tbody').append(html);

    // Initialize Select2 on the new row
    var $newRow = $('#table-schedule tbody tr.schedule-row:last');
    if (isRequirement) {
        $newRow.find('.select2-schedule-requirement').select2({
            placeholder: "Select Standard",
            allowClear: true,
            width: "100%"
        });
    } else if (!isFreeText) {
        $newRow.find('.select2-schedule-process').select2({
            placeholder: "Select Process",
            allowClear: true,
            width: "100%"
        });
        $newRow.find('.select2-schedule-auditee').select2({
            placeholder: "Select Department - Company",
            allowClear: true,
            width: "100%"
        });
    }
    $newRow.find('.select2-schedule-auditor').select2({
        placeholder: "Select Auditor",
        allowClear: true,
        width: "100%"
    });

    updateScheduleRowCount();
}

function initScheduleSelect2() {
    $('.select2-schedule-process').select2({
        placeholder: "Select Process",
        allowClear: true,
        width: "100%"
    });
    $('.select2-schedule-auditor').select2({
        placeholder: "Select Auditor",
        allowClear: true,
        width: "100%"
    });
    $('.select2-schedule-auditee').select2({
        placeholder: "Select Department - Company",
        allowClear: true,
        width: "100%"
    });
    $('.select2-schedule-requirement').select2({
        placeholder: "Select Standard",
        allowClear: true,
        width: "100%"
    });
}

function renumberScheduleRows() {
    var n = 0;
    $('#table-schedule tbody tr.schedule-row').each(function() {
        n++;
        $(this).find('.row-number').text(n);
    });
}

function updateScheduleRowCount() {
    var count = $('#table-schedule tbody tr.schedule-row').length;
    $('#schedule-row-count').text(count);
    if (count >= 50) {
        $('#btn-add-schedule').prop('disabled', true);
        $('#btn-add-schedule-free').prop('disabled', true);
        $('#btn-add-schedule-requirement').prop('disabled', true);
    } else {
        $('#btn-add-schedule').prop('disabled', false);
        $('#btn-add-schedule-free').prop('disabled', false);
        $('#btn-add-schedule-requirement').prop('disabled', false);
    }
}
</script>
