<div class="row">
    <!-- LEFT SIDE: Document Preview (60%) -->
    <div class="col-lg-7">
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-file-pdf text-danger mr-2"></i>Document Preview
                </h5>
            </div>
            <div class="card-body p-0">
                <?php 
                $allow_download_wi = isset($allow_download_wi) ? $allow_download_wi : false;
                $display_file_name = !empty($wi->display_file_name) ? $wi->display_file_name : (!empty($wi->file_name) ? $wi->file_name : '');
                $display_file_path = !empty($wi->display_file_path) ? $wi->display_file_path : (!empty($wi->file_path) ? $wi->file_path : '');
                $display_ext = !empty($wi->display_ext) ? $wi->display_ext : (!empty($wi->ext) ? $wi->ext : pathinfo($display_file_name, PATHINFO_EXTENSION));
                $display_size = isset($wi->display_size) ? $wi->display_size : (isset($wi->size) ? $wi->size : null);
                ?>

                <?php if (!empty($display_file_name)) : ?>
                    <?php 
                    $file_path = base_url('work_instructions/view_file/' . $wi->id);
                    $file_ext = strtolower($display_ext);
                    ?>
                    
                    <?php if ($file_ext === '.pdf' || $file_ext === 'pdf') : ?>
                        <!-- PDF Preview -->
                        <iframe src="<?= $file_path; ?>#toolbar=0" 
                                style="width: 100%; height: 600px; border: none;" 
                                frameborder="0">
                            <p>Browser Anda tidak mendukung preview PDF. 
                                <?php if ($allow_download_wi) : ?>
                                <a href="<?= base_url('work_instructions/download/' . $wi->id); ?>" target="_blank">Klik di sini untuk download</a>
                                <?php endif; ?>
                            </p>
                        </iframe>
                    <?php elseif (in_array($file_ext, ['.xlsx', '.xls', 'xlsx', 'xls'])) : ?>
                        <!-- Excel Preview -->
                        <div class="alert alert-info m-3">
                            <i class="fa fa-info-circle mr-2"></i>
                            Jika dokumen tidak muncul, pasang ekstensi <a
                                href="https://chromewebstore.google.com/detail/office-editing-for-docs-s/gbkeegbaiigmenfmjfclcdgdpimamgkj"
                                target="_blank" class="font-weight-bold text-dark">Office Editing</a> di browser Chrome Anda,
                            atau unduh dokumen secara manual di bawah.
                        </div>
                        <iframe src="<?= $file_path; ?>" style="width: 100%; height: 600px; border: none;" frameborder="0">
                        </iframe>
                    <?php elseif (in_array($file_ext, ['.docx', '.doc', 'docx', 'doc'])) : ?>
                        <!-- Word Preview -->
                        <div class="alert alert-info m-3">
                            <i class="fa fa-info-circle mr-2"></i>
                            Jika dokumen tidak muncul, pasang ekstensi <a
                                href="https://chromewebstore.google.com/detail/office-editing-for-docs-s/gbkeegbaiigmenfmjfclcdgdpimamgkj"
                                target="_blank" class="font-weight-bold text-dark">Office Editing</a> di browser Chrome Anda,
                            atau unduh dokumen secara manual di bawah.
                        </div>
                        <iframe src="<?= $file_path; ?>" style="width: 100%; height: 600px; border: none;" frameborder="0">
                        </iframe>
                    <?php else : ?>
                        <!-- Unknown file type -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file fa-5x text-secondary mb-3"></i>
                            <h4>Document File</h4>
                            <p class=""><?= htmlspecialchars($display_file_name); ?></p>
                            <p class="">Size: <?= isset($display_size) ? number_format($display_size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download_wi) : ?>
                            <a href="<?= base_url('work_instructions/download/' . $wi->id); ?>" class="btn btn-secondary">
                                <i class="fa fa-download"></i> Download File
                            </a>
                            <?php else: ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="p-5 text-center">
                        <i class="fa fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                        <h4>No Document File</h4>
                        <p class="">Tidak ada file dokumen yang di-upload untuk Work Instruction ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Document Information & Approval Form (40%) -->
    <div class="col-lg-5">
        <!-- Document Information Card -->
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-info-circle text-primary mr-2"></i>Document Information
                </h5>
            </div>
            <div class="card-body py-2">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th width="130" class=" py-1">Document Number</th>
                        <td class="font-weight-bold py-1">: <?= htmlspecialchars($wi->number ? $wi->number : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Document Name</th>
                        <td class="font-weight-bold py-1">: <?= htmlspecialchars($wi->name ? $wi->name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Department</th>
                        <td class="py-1">: <?= htmlspecialchars($wi->departement_name ? $wi->departement_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Procedure</th>
                        <td class="py-1">: <?= htmlspecialchars($wi->procedure_name ? $wi->procedure_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Issue Date</th>
                        <td class="py-1">: <?= isset($wi->issue_date) ? date('d M Y', strtotime($wi->issue_date)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Effective Date</th>
                        <td class="py-1">: <?= isset($wi->effective_date) ? date('d M Y', strtotime($wi->effective_date)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Revision Number</th>
                        <td class="py-1">: <span class="badge badge-light-primary"><?= htmlspecialchars($wi->revision_number ? $wi->revision_number : '0'); ?></span></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Status</th>
                        <td class="py-1">: <?= isset($sts[$wi->status]) ? $sts[$wi->status] : htmlspecialchars($wi->status); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Approval Form Card -->
        <div class="card card-custom shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-check text-success mr-2"></i>Approval Action
                </h5>
            </div>
            <div class="card-body">
                <form id="form-approval-modal">
                    <input type="hidden" name="id" value="<?= $wi->id; ?>">
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Action Approval</label>
                        <div class="radio-list">
                            <label class="radio radio-outline radio-outline-2x radio-success">
                                <input type="radio" name="status" value="PUB" />
                                <span></span>
                                Approve & Publish this document
                            </label>
                            <span class="form-text  mb-2">Set published date</span>
                            <input type="date" name="published_date" disabled id="published_date_modal" class="form-control mb-2" />
                            <span class="invalid-feedback text-danger d-none" id="invalid-published-date">Published date is required</span>
                            
                            <span class="form-text  mb-2">Revision description (optional)</span>
                            <textarea name="revision_description" disabled id="revision_description_modal" rows="3" class="form-control" placeholder="Describe what changed in this version (optional)"></textarea>
                            <span class="form-text text-muted">Catatan perubahan untuk version history</span>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="radio-list">
                            <label class="radio radio-outline radio-outline-2x radio-danger">
                                <input type="radio" name="status" value="COR" />
                                <span></span>
                                Request correction
                            </label>
                        </div>
                        <span class="form-text  mb-2">Write down the reason</span>
                        <textarea name="note" disabled id="note_correction_approval_modal" rows="4" class="form-control" placeholder="Reason"></textarea>
                        <span class="invalid-feedback text-danger d-none" id="invalid-note-approval">Harus di isi</span>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-success btn-block" id="save-approval-modal">
                            <i class="fab fa-telegram-plane"></i> Submit Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // Handle enable/disable fields berdasarkan pilihan radio
    $(document).on('change', '#form-approval-modal input[name="status"]', function () {
        if ($(this).val() == 'PUB') {
            $('#published_date_modal').prop('disabled', false);
            $('#revision_description_modal').prop('disabled', false);
            $('#note_correction_approval_modal').prop('disabled', true).val('');
            $('#invalid-published-date').addClass('d-none');
            $('#invalid-note-approval').addClass('d-none');
        } else {
            $('#published_date_modal').prop('disabled', true).val('');
            $('#revision_description_modal').prop('disabled', true).val('');
            $('#note_correction_approval_modal').prop('disabled', false);
            $('#invalid-published-date').addClass('d-none');
            $('#invalid-note-approval').addClass('d-none');
        }
    });

    // Handle submit form approval
    $(document).on('submit', '#form-approval-modal', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var status = $('input[name="status"]:checked').val();
        var published_date = $('#published_date_modal').val().trim();
        var note = $('#note_correction_approval_modal').val().trim();
        
        // Validasi
        if (!status) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih salah satu aksi approval.' });
            return;
        }
        
        if (status === 'PUB' && published_date === '') {
            $('#invalid-published-date').removeClass('d-none');
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Published date wajib diisi jika approve & publish.' });
            return;
        }
        
        if (status === 'COR' && note === '') {
            $('#invalid-note-approval').removeClass('d-none');
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Catatan wajib diisi jika request correction.' });
            return;
        }
        
        $.ajax({
            url: '<?= base_url('work_instructions/saveApprove'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                $('#save-approval-modal').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            },
            success: function (response) {
                if (response.status == 1) {
                    $('#modalWIAction').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.msg,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.msg || 'Terjadi kesalahan'
                    });
                    $('#save-approval-modal').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i> Submit Approval');
                }
            },
            error: function (xhr) {
                var msg = 'Terjadi kesalahan. Silakan coba lagi.';
                try {
                    var json = JSON.parse(xhr.responseText);
                    if (json.msg) msg = json.msg;
                } catch (e) {}
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
                $('#save-approval-modal').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i> Submit Approval');
            }
        });
    });
})();
</script>
