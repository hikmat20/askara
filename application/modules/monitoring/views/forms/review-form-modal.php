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
                    $file_path = base_url('directory/FORMS/1/' . $form->file_name);
                    $file_ext = strtolower($form->ext ? $form->ext : pathinfo($form->file_name, PATHINFO_EXTENSION));
                    ?>

                    <?php if ($file_ext === '.pdf' || $file_ext === 'pdf') : ?>
                        <!-- PDF Preview -->
                        <iframe src="<?= $file_path; ?>#toolbar=0"
                            style="width: 100%; height: 600px; border: none;"
                            frameborder="0">
                            <p>Browser Anda tidak mendukung preview PDF.
                                <?php if ($allow_download_form) : ?>
                                <a href="<?= base_url('forms/download/' . $form->id); ?>" target="_blank">Klik di sini untuk download</a>
                                <?php endif; ?>
                            </p>
                        </iframe>
                    <?php elseif (in_array($file_ext, ['.xlsx', '.xls', 'xlsx', 'xls'])) : ?>
                        <!-- Excel Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-excel fa-5x text-success mb-3"></i>
                            <h4>Excel Document</h4>
                            <p class=""><?= htmlspecialchars($form->file_name); ?></p>
                            <p class="">Size: <?= isset($form->size) ? number_format($form->size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download_form) : ?>
                            <a href="<?= base_url('forms/download/' . $form->id); ?>" class="btn btn-success">
                                <i class="fa fa-download"></i> Download Excel File
                            </a>
                            <?php else : ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted</span>
                            <?php endif; ?>
                        </div>
                    <?php elseif (in_array($file_ext, ['.docx', '.doc', 'docx', 'doc'])) : ?>
                        <!-- Word Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-word fa-5x text-primary mb-3"></i>
                            <h4>Word Document</h4>
                            <p class=""><?= htmlspecialchars($form->file_name); ?></p>
                            <p class="">Size: <?= isset($form->size) ? number_format($form->size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download_form) : ?>
                            <a href="<?= base_url('forms/download/' . $form->id); ?>" class="btn btn-primary">
                                <i class="fa fa-download"></i> Download Word File
                            </a>
                            <?php else : ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted</span>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <!-- Unknown file type -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file fa-5x text-secondary mb-3"></i>
                            <h4>Document File</h4>
                            <p class=""><?= htmlspecialchars($form->file_name); ?></p>
                            <p class="">Size: <?= isset($form->size) ? number_format($form->size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download_form) : ?>
                            <a href="<?= base_url('forms/download/' . $form->id); ?>" class="btn btn-secondary">
                                <i class="fa fa-download"></i> Download File
                            </a>
                            <?php else : ?>
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

    <!-- TAB: Detail Form -->
    <!-- Status History Card -->
    <!-- RIGHT SIDE: Document Information & Review Form (40%) -->
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
                        <th width="130" class="py-1">Form Number</th>
                        <td class="font-weight-bold py-1">: <?= htmlspecialchars($form->number ? $form->number : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Form Name</th>
                        <td class="font-weight-bold py-1">: <?= htmlspecialchars($form->name ? $form->name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Department</th>
                        <td class="py-1">: <?= htmlspecialchars($form->departement_name ? $form->departement_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Procedure</th>
                        <td class="py-1">: <?= htmlspecialchars($form->procedure_name ? $form->procedure_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">PIC Reviewer</th>
                        <td class="py-1">: <?= htmlspecialchars($form->reviewer_position_name ? $form->reviewer_position_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">PIC Approver</th>
                        <td class="py-1">: <?= htmlspecialchars($form->approver_position_name ? $form->approver_position_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Status</th>
                        <td class="py-1">: <?= isset($sts[$form->status]) ? $sts[$form->status] : htmlspecialchars($form->status); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Review Form Card -->
        <div class="card card-custom shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-edit text-warning mr-2"></i>Review Action
                </h5>
            </div>
            <div class="card-body">
                <form id="form-review-form-modal">
                    <input type="hidden" name="id" id="form_id" value="<?= $form->id; ?>">

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Action Review</label>
                        <div class="radio-list">
                            <label class="radio radio-outline radio-outline-2x radio-primary">
                                <input type="radio" name="status" value="APV" />
                                <span></span>
                                I agree to this file, and continue to the next process
                            </label>
                            <span class="form-text">Ready to Approval Process</span>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="radio-list">
                            <label class="radio radio-outline radio-outline-2x radio-danger">
                                <input type="radio" name="status" value="COR" />
                                <span></span>
                                I don't agree, because some need corrections
                            </label>
                        </div>
                        <span class="form-text mb-2">Write down the reason</span>
                        <textarea name="note" disabled id="note_correction_form_modal" rows="4" class="form-control" placeholder="Reason"></textarea>
                        <span class="invalid-feedback text-danger d-none" id="invalid-note-form">Harus di isi</span>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary btn-block" id="save-review-form-modal">
                            <i class="fab fa-telegram-plane"></i> Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    (function() {
        // Handle enable/disable textarea note berdasarkan pilihan radio
        $(document).on('change', '#form-review-form-modal input[name="status"]', function() {
            if ($(this).val() == 'APV') {
                $('#note_correction_form_modal').prop('disabled', true).val('');
                $('#invalid-note-form').addClass('d-none');
            } else {
                $('#note_correction_form_modal').prop('disabled', false);
            }
        });

        // Handle submit form review
        $(document).on('submit', '#form-review-form-modal', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var status = $('input[name="status"]:checked').val();
            var note = $('#note_correction_form_modal').val().trim();

            // Validasi
            if (!status) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih salah satu aksi review.'
                });
                return;
            }

            if (status === 'COR' && note === '') {
                $('#invalid-note-form').removeClass('d-none');
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Catatan wajib diisi jika aksi adalah Kembalikan.'
                });
                return;
            }

            $.ajax({
                url: '<?= base_url('monitoring/save_review_form'); ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    $('#save-review-form-modal').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
                },
                success: function(response) {
                    if (response.status == 1) {
                        $('#modalFormAction').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.msg,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.msg || 'Terjadi kesalahan'
                        });
                        $('#save-review-form-modal').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i> Submit Review');
                    }
                },
                error: function(xhr) {
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
                    $('#save-review-form-modal').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i> Submit Review');
                }
            });
        });
    })();
</script>