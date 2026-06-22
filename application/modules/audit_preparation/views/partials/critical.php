<!-- Critical Issues Section -->
<div class="row">
    <div class="col-md-12">
        <h5 class="font-weight-bold mb-3">Management Critical Issues</h5>
    </div>
</div>

<!-- Input Form -->
<div class="row">
    <div class="col-md-12">
        <div class="mb-3 row">
            <label class="col-3 col-form-label font-weight-bold">Issue Description <span class="text-danger">*</span></label>
            <div class="col-9">
                <textarea id="issue_description" class="form-control required" rows="3" maxlength="2000" placeholder="Pelaksanaan audit bisa lebih tajam dalam menemukan kelemahan sistem. “Kita sudah menerapkan sistem sejak 5 tahun yang lalu, tapi saya merasakan bahwa sistem kita masih sekedar dokumen saja, aktualnya masih banyak problem."></textarea>
                <small class="text-muted"><span id="issue_desc_count">0</span>/2000 characters</small>
                <span class="invalid-feedback">Issue Description can't be empty</span>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-3 col-form-label font-weight-bold">Improvement</label>
            <div class="col-9">
                <textarea id="management_input" class="form-control" rows="3" maxlength="2000" placeholder="Pelaksanaan audit tidak hanya cek dokumen, tetapi melakukan cek hasil"></textarea>
                <small class="text-muted"><span id="mgmt_input_count">0</span>/2000 characters</small>
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-9 offset-3">
                <button type="button" id="btn-add-critical" class="btn btn-sm btn-primary"><i class="fa fa-plus mr-1"></i> Add</button>
                <button type="button" id="btn-cancel-critical" class="btn btn-sm btn-secondary ml-2" style="display:none;"><i class="fa fa-times mr-1"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Critical Issues Table -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="table-critical" class="table table-sm table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Issue Description</th>
                        <th>Improvement</th>
                        <th width="100" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <p class="text-muted small" id="critical-count-info">0 of 50 entries</p>
    </div>
</div>

<script>
$(document).ready(function() {
    var criticalEditIndex = -1;
    var maxCriticalEntries = 50;

    // Pre-populate table with existing critical issues
    <?php if (!empty($critical_issues)) : ?>
    var existingCritical = <?= json_encode($critical_issues); ?>;
    $.each(existingCritical, function(index, item) {
        appendCriticalRow(item.issue_description, item.management_input || '', item.id || '');
    });
    <?php endif; ?>

    // Character count for issue description
    $('#issue_description').on('input', function() {
        $('#issue_desc_count').text($(this).val().length);
    });

    // Character count for management input
    $('#management_input').on('input', function() {
        $('#mgmt_input_count').text($(this).val().length);
    });

    // Add critical issue entry
    $(document).on('click', '#btn-add-critical', function() {
        var issueDesc = $.trim($('#issue_description').val());
        var mgmtInput = $.trim($('#management_input').val());

        // Validate issue description is not empty
        if (issueDesc === '') {
            $('#issue_description').addClass('is-invalid');
            return false;
        }
        $('#issue_description').removeClass('is-invalid');

        // Check max entries
        var rowCount = $('#table-critical tbody tr').length;
        if (criticalEditIndex === -1 && rowCount >= maxCriticalEntries) {
            Swal.fire({
                title: 'Limit Reached',
                text: 'Maximum ' + maxCriticalEntries + ' critical issue entries allowed.',
                icon: 'info'
            });
            return false;
        }

        if (criticalEditIndex >= 0) {
            // Update existing row
            var row = $('#table-critical tbody tr').eq(criticalEditIndex);
            row.find('td:eq(1)').contents().filter(function() { return this.nodeType === 3; }).first().replaceWith(issueDesc);
            row.find('td:eq(2)').contents().filter(function() { return this.nodeType === 3; }).first().replaceWith(mgmtInput);
            row.find('input[name="issue_desc[]"]').val(issueDesc);
            row.find('input[name="management_input[]"]').val(mgmtInput);
            criticalEditIndex = -1;
            $('#btn-add-critical').html('<i class="fa fa-plus mr-1"></i> Add');
            $('#btn-cancel-critical').hide();

            Swal.fire({
                title: 'Updated!',
                text: 'Critical issue entry updated successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            // Add new row
            appendCriticalRow(issueDesc, mgmtInput, '');

            Swal.fire({
                title: 'Added!',
                text: 'Critical issue entry added successfully.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Clear inputs
        $('#issue_description').val('');
        $('#management_input').val('');
        $('#issue_desc_count').text('0');
        $('#mgmt_input_count').text('0');

        updateCriticalNumbers();
        updateCriticalCountInfo();
    });

    // Cancel edit
    $(document).on('click', '#btn-cancel-critical', function() {
        criticalEditIndex = -1;
        $('#btn-add-critical').html('<i class="fa fa-plus mr-1"></i> Add');
        $('#btn-cancel-critical').hide();
        $('#issue_description').val('');
        $('#management_input').val('');
        $('#issue_desc_count').text('0');
        $('#mgmt_input_count').text('0');
    });

    // Edit critical issue entry
    $(document).on('click', '.btn-edit-critical', function() {
        var row = $(this).closest('tr');
        criticalEditIndex = row.index();

        var issueDesc = row.find('input[name="issue_desc[]"]').val();
        var mgmtInput = row.find('input[name="management_input[]"]').val();

        $('#issue_description').val(issueDesc);
        $('#management_input').val(mgmtInput);
        $('#issue_desc_count').text(issueDesc.length);
        $('#mgmt_input_count').text(mgmtInput.length);

        $('#btn-add-critical').html('<i class="fa fa-save mr-1"></i> Update');
        $('#btn-cancel-critical').show();

        // Scroll to input
        $('html, body').animate({
            scrollTop: $('#issue_description').offset().top - 100
        }, 300);
    });

    // Delete critical issue entry
    $(document).on('click', '.btn-delete-critical', function() {
        var row = $(this).closest('tr');
        Swal.fire({
            title: 'Delete Entry?',
            text: 'Are you sure you want to delete this critical issue entry?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                row.remove();
                updateCriticalNumbers();
                updateCriticalCountInfo();

                // Reset edit state if deleting the edited row
                criticalEditIndex = -1;
                $('#btn-add-critical').html('<i class="fa fa-plus mr-1"></i> Add');
                $('#btn-cancel-critical').hide();

                Swal.fire({
                    title: 'Deleted!',
                    text: 'Critical issue entry has been deleted.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });

    // Append a row to critical issues table
    function appendCriticalRow(issueDesc, mgmtInput, recordId) {
        var rowCount = $('#table-critical tbody tr').length + 1;
        var escapedIssue = $('<div>').text(issueDesc).html();
        var escapedMgmt = $('<div>').text(mgmtInput).html();
        var idVal = recordId || '';

        var html = '<tr>';
        html += '<td class="text-center">' + rowCount + '</td>';
        html += '<td>' + escapedIssue + '<input type="hidden" name="critical_id[]" value="' + idVal + '"><input type="hidden" name="issue_desc[]" value="' + escapedIssue + '"></td>';
        html += '<td>' + escapedMgmt + '<input type="hidden" name="management_input[]" value="' + escapedMgmt + '"></td>';
        html += '<td class="text-center">';
        html += '<button type="button" class="btn btn-xs btn-icon btn-warning btn-edit-critical" title="Edit"><i class="fa fa-edit"></i></button> ';
        html += '<button type="button" class="btn btn-xs btn-icon btn-danger btn-delete-critical" title="Delete"><i class="fa fa-trash"></i></button>';
        html += '</td>';
        html += '</tr>';

        $('#table-critical tbody').append(html);
        updateCriticalCountInfo();
    }

    // Update row numbers
    function updateCriticalNumbers() {
        $('#table-critical tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    // Update count info
    function updateCriticalCountInfo() {
        var count = $('#table-critical tbody tr').length;
        $('#critical-count-info').text(count + ' of ' + maxCriticalEntries + ' entries');

        // Disable/enable add button based on count
        if (count >= maxCriticalEntries && criticalEditIndex === -1) {
            $('#btn-add-critical').prop('disabled', true);
        } else {
            $('#btn-add-critical').prop('disabled', false);
        }
    }
});
</script>
