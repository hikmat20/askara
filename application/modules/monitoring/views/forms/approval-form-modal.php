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
                <?php if (!empty($form->file_name)) : ?>
                    <?php
                    $file_path = base_url($form->file_path ? $form->file_path : 'directory/FORMS/1/' . $form->file_name);
                    $file_ext = strtolower($form->ext ? $form->ext : pathinfo($form->file_name, PATHINFO_EXTENSION));
                    ?>

                    <?php if ($file_ext === '.pdf' || $file_ext === 'pdf') : ?>
                        <!-- PDF Preview -->
                        <iframe src="<?= $file_path; ?>#toolbar=0"
                            style="width: 100%; height: 600px; border: none;"
                            frameborder="0">
                            <p>Browser Anda tidak mendukung preview PDF.
                                <a href="<?= $file_path; ?>" target="_blank">Klik di sini untuk download</a>
                            </p>
                        </iframe>
                    <?php elseif (in_array($file_ext, ['.xlsx', '.xls', 'xlsx', 'xls'])) : ?>
                        <!-- Excel Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-excel fa-5x text-success mb-3"></i>
                            <h4>Excel Document</h4>
                            <p class=""><?= htmlspecialchars($form->file_name); ?></p>
                            <p class="">Size: <?= isset($form->size) ? number_format($form->size) . ' KB' : '-'; ?></p>
                            <a href="<?= $file_path; ?>" target="_blank" class="btn btn-success">
                                <i class="fa fa-download"></i> Download Excel File
                            </a>
                        </div>
                    <?php elseif (in_array($file_ext, ['.docx', '.doc', 'docx', 'doc'])) : ?>
                        <!-- Word Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-word fa-5x text-primary mb-3"></i>
                            <h4>Word Document</h4>
                            <p class=""><?= htmlspecialchars($form->file_name); ?></p>
                            <p class="">Size: <?= isset($form->size) ? number_format($form->size) . ' KB' : '-'; ?></p>
                            <a href="<?= $file_path; ?>" target="_blank" class="btn btn-primary">
                                <i class="fa fa-download"></i> Download Word File
                            </a>
                        </div>
                    <?php else : ?>
                        <!-- Unknown file type -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file fa-5x text-secondary mb-3"></i>
                            <h4>Document File</h4>
                            <p class=""><?= htmlspecialchars($form->file_name); ?></p>
                            <p class="">Size: <?= isset($form->size) ? number_format($form->size) . ' KB' : '-'; ?></p>
                            <a href="<?= $file_path; ?>" target="_blank" class="btn btn-secondary">
                                <i class="fa fa-download"></i> Download File
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="p-5 text-center">
                        <i class="fa fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                        <h4>No Document File</h4>
                        <p class="">Tidak ada file dokumen yang di-upload untuk Form ini.</p>
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
                        <th width="130" class=" py-1">Form Number</th>
                        <td class="font-weight-bold py-1">: <?= htmlspecialchars($form->number ? $form->number : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Form Name</th>
                        <td class="font-weight-bold py-1">: <?= htmlspecialchars($form->name ? $form->name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Department</th>
                        <td class="py-1">: <?= htmlspecialchars($form->departement_name ? $form->departement_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Procedure</th>
                        <td class="py-1">: <?= htmlspecialchars($form->procedure_name ? $form->procedure_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Issue Date</th>
                        <td class="py-1">: <?= isset($form->issue_date) ? date('d M Y', strtotime($form->issue_date)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Effective Date</th>
                        <td class="py-1">: <?= isset($form->effective_date) ? date('d M Y', strtotime($form->effective_date)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Revision Number</th>
                        <td class="py-1">: <span class="badge badge-light-primary"><?= htmlspecialchars($form->revision_number ? $form->revision_number : '0'); ?></span></td>
                    </tr>
                    <tr>
                        <th class=" py-1">Status</th>
                        <td class="py-1">: <?= isset($sts[$form->status]) ? $sts[$form->status] : htmlspecialchars($form->status); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Approval Form Card -->
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-check text-success mr-2"></i>Approval Action
                </h5>
            </div>
            <div class="card-body">
                <form id="form-approval-form">
                    <input type="hidden" name="id" id="form_id" value="<?= $form->id; ?>">
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Action Approval</label>
                        <div class="radio-list">
                            <label class="radio radio-outline radio-outline-2x radio-success">
                                <input type="radio" name="status" value="PUB" id="status_pub" />
                                <span></span>
                                Approve & Publish this document
                            </label>
                            <span class="form-text  mb-2">Set published date</span>
                            <input type="date" name="published_date" disabled id="published_date" class="form-control" />
                            <span class="invalid-feedback text-danger d-none" id="invalid-published-date">Published date is required</span>

                            <span class="form-text mt-2 mb-2">Revision Description (Change Log)</span>
                            <textarea name="revision_description" disabled id="revision_description" rows="3" class="form-control" placeholder="Write version change log..."></textarea>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="radio-list">
                            <label class="radio radio-outline radio-outline-2x radio-danger">
                                <input type="radio" name="status" value="COR" id="status_cor" />
                                <span></span>
                                Request correction
                            </label>
                        </div>
                        <span class="form-text  mb-2">Write down the reason</span>
                        <textarea name="note" disabled id="note" rows="4" class="form-control" placeholder="Reason"></textarea>
                        <span class="invalid-feedback text-danger d-none" id="invalid-note">Harus di isi</span>
                    </div>

                    <div class="form-group mb-0">
                        <button type="button" class="btn btn-success btn-block" id="btn-save-approval">
                            <i class="fab fa-telegram-plane"></i> Submit Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Status History Card -->
        <div class="card card-custom shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-history text-info mr-2"></i>Status History
                </h5>
            </div>
            <div class="card-body py-2">
                <?php if (!empty($history)) : ?>
                    <div class="timeline timeline-5">
                        <div class="timeline-items">
                            <?php foreach ($history as $log) : ?>
                                <div class="timeline-item">
                                    <div class="timeline-media <?= ($log->new_status === 'APV' || $log->new_status === 'PUB') ? 'bg-light-success' : 'bg-light-danger'; ?>">
                                        <span class="<?= ($log->new_status === 'APV' || $log->new_status === 'PUB') ? 'fa fa-check text-success' : 'fa fa-circle text-danger'; ?>"></span>
                                    </div>
                                    <div class="timeline-desc timeline-desc-light-primary mb-4">
                                        <span class="font-weight-bolder text-primary"><?= htmlspecialchars($log->action_at); ?></span>
                                        <p class="mb-1">
                                            Status:
                                            <?= isset($sts[$log->old_status]) ? $sts[$log->old_status] : htmlspecialchars($log->old_status ?? '-'); ?>
                                            <i class="fa fa-arrow-right mx-1"></i>
                                            <?= isset($sts[$log->new_status]) ? $sts[$log->new_status] : htmlspecialchars($log->new_status ?? '-'); ?>
                                        </p>
                                        <p class="mb-1">
                                            Oleh: <strong><?= isset($ArrUsers[$log->action_by]) ? htmlspecialchars($ArrUsers[$log->action_by]->full_name ?? $ArrUsers[$log->action_by]->username) : 'User #' . $log->action_by; ?></strong>
                                        </p>
                                        <?php if (!empty($log->note)) : ?>
                                            <p class="mb-0 text-muted">Catatan: <?= htmlspecialchars($log->note); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <p class="text-muted text-center py-3">Belum ada riwayat perubahan status.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // Handle enable/disable fields berdasarkan pilihan radio
    $(document).on('change', '#form-approval-form input[name="status"]', function () {
        if ($(this).val() == 'PUB') {
            $('#published_date').prop('disabled', false);
            $('#revision_description').prop('disabled', false);
            $('#note').prop('disabled', true).val('');
            $('#invalid-published-date').addClass('d-none');
            $('#invalid-note').addClass('d-none');
        } else {
            $('#published_date').prop('disabled', true).val('');
            $('#revision_description').prop('disabled', true).val('');
            $('#note').prop('disabled', false);
            $('#invalid-published-date').addClass('d-none');
            $('#invalid-note').addClass('d-none');
        }
    });

    // Handle submit form approval
    $(document).on('click', '#btn-save-approval', function (e) {
        e.preventDefault();
        var btn           = $(this);
        var status        = $('input[name="status"]:checked').val();
        var note          = $('#note').val().trim();
        var publishedDate = $('#published_date').val().trim();
        var revisionDesc  = $('#revision_description').val().trim();
        var valid         = true;

        if (!status) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih salah satu aksi approval.' });
            return;
        }
        
        if (status === 'PUB' && publishedDate === '') {
            $('#invalid-published-date').removeClass('d-none');
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Published date wajib diisi jika approve & publish.' });
            return;
        }
        
        if (status === 'COR' && note === '') {
            $('#invalid-note').removeClass('d-none');
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Catatan wajib diisi jika request correction.' });
            return;
        }

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');

        $.ajax({
            url:      '<?= base_url($this->uri->segment(1) . '/save_approval_form'); ?>',
            type:     'POST',
            data:     { id: $('#form_id').val(), status: status, note: note, published_date: publishedDate, revision_description: revisionDesc },
            dataType: 'json',
            success: function (res) {
                if (res.status == 1) {
                    $('#modalFormAction').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.msg, confirmButtonText: 'OK' })
                        .then(function () { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.msg });
                    btn.prop('disabled', false).html('<i class="fab fa-telegram-plane"></i> Submit Approval');
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan. Silakan coba lagi.' });
                btn.prop('disabled', false).html('<i class="fab fa-telegram-plane"></i> Submit Approval');
            }
        });
    });
})();
</script>
