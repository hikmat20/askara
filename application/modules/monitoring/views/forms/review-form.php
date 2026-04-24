<div class="content d-flex flex-column flex-column-fluid p-0">
    <div class="container mt-3">
        <div class="mb-5">
            <div style="font-size: 36px;" class="text-white font-weight-bolder">
                <a style="font-size: 30px;" href="<?= base_url($this->uri->segment(1) . '/forms_review'); ?>" class="text-danger" title="Kembali ke Daftar Review">
                    <span class="fa fa-arrow-circle-left"></span>
                </a>
                REVIEW FORM
            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <ul class="nav nav-pills nav-light-success py-0 mb-4" id="reviewTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-review">
                            <span class="nav-icon"><i class="fa fa-edit"></i></span>
                            <span class="nav-text">Submit Review</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-detail">
                            <span class="nav-icon"><i class="fa fa-list"></i></span>
                            <span class="nav-text">Detail Form</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-history">
                            <span class="nav-icon"><i class="fa fa-history"></i></span>
                            <span class="nav-text">Riwayat Status</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- TAB: Submit Review -->
                    <div class="tab-pane fade show active" id="tab-review" role="tabpanel">
                        <form id="form-review-form">
                            <input type="hidden" name="id" id="form_id" value="<?= $form->id; ?>">

                            <div class="form-group">
                                <label class="col-form-label font-weight-bold">Aksi Review</label>
                                <span id="invalid-action" class="d-none text-danger d-block">Pilih salah satu aksi.</span>

                                <div class="col-form-label mb-3">
                                    <div class="radio-inline">
                                        <label class="radio radio-outline radio-outline-2x radio-primary">
                                            <input type="radio" name="status" value="APV" id="status_apv" />
                                            <span></span>
                                            Setujui — lanjutkan ke proses Approval
                                        </label>
                                    </div>
                                    <span class="form-text text-muted pl-7">Form akan diteruskan ke PIC Approver.</span>
                                </div>

                                <div class="col-form-label mb-3">
                                    <div class="radio-inline">
                                        <label class="radio radio-outline radio-outline-2x radio-danger">
                                            <input type="radio" name="status" value="COR" id="status_cor" />
                                            <span></span>
                                            Kembalikan — perlu koreksi
                                        </label>
                                    </div>
                                    <span class="form-text text-muted pl-7">Tuliskan alasan koreksi di bawah ini.</span>
                                    <div class="pl-7 mt-2">
                                        <textarea name="note" id="note" rows="5" class="form-control" placeholder="Alasan koreksi (wajib diisi jika memilih Kembalikan)" disabled></textarea>
                                        <span id="invalid-note" class="d-none text-danger">Catatan wajib diisi jika aksi adalah Kembalikan.</span>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-light-primary mt-3" id="btn-save-review">
                                    <i class="fab fa-telegram-plane"></i> Submit Review
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB: Detail Form -->
                    <div class="tab-pane fade" id="tab-detail" role="tabpanel">
                        <table class="table table-sm table-bordered border-dark mb-4">
                            <tr>
                                <td width="200"><strong>Nomor Form</strong></td>
                                <td><?= htmlspecialchars(isset($form->nomor) ? $form->nomor : '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Form</strong></td>
                                <td><?= htmlspecialchars(isset($form->name) ? $form->name : '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Departemen</strong></td>
                                <td><?= htmlspecialchars(isset($form->departement_name) ? $form->departement_name : '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>PIC Reviewer</strong></td>
                                <td><?= htmlspecialchars(isset($form->reviewer_position_name) ? $form->reviewer_position_name : '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>PIC Approver</strong></td>
                                <td><?= htmlspecialchars(isset($form->approver_position_name) ? $form->approver_position_name : '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td><?= isset($sts[$form->status]) ? $sts[$form->status] : htmlspecialchars($form->status); ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- TAB: Riwayat Status -->
                    <div class="tab-pane fade" id="tab-history" role="tabpanel">
                        <?php if (!empty($history)) : ?>
                            <div class="timeline timeline-5">
                                <div class="timeline-items">
                                    <?php foreach ($history as $log) : ?>
                                        <div class="timeline-item">
                                            <div class="timeline-media <?= ($log->new_status === 'APV' || $log->new_status === 'PUB') ? 'bg-light-success' : 'bg-light-danger'; ?>">
                                                <span class="<?= ($log->new_status === 'APV' || $log->new_status === 'PUB') ? 'fa fa-check text-success' : 'fa fa-circle text-danger'; ?>"></span>
                                            </div>
                                            <div class="timeline-desc timeline-desc-light-primary mb-5">
                                                <span class="font-weight-bolder text-primary"><?= htmlspecialchars($log->action_at); ?></span>
                                                <p class="mb-1">
                                                    Status:
                                                    <?= isset($sts[$log->old_status]) ? $sts[$log->old_status] : htmlspecialchars(isset($log->old_status) ? $log->old_status : '-'); ?>
                                                    <i class="fa fa-arrow-right mx-1"></i>
                                                    <?= isset($sts[$log->new_status]) ? $sts[$log->new_status] : htmlspecialchars(isset($log->new_status) ? $log->new_status : '-'); ?>
                                                </p>
                                                <p class="mb-1">
                                                    Oleh: <strong><?= isset($ArrUsers[$log->action_by]) ? htmlspecialchars(isset($ArrUsers[$log->action_by]->full_name) ? $ArrUsers[$log->action_by]->full_name : $ArrUsers[$log->action_by]->username) : 'User #' . $log->action_by; ?></strong>
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
                            <p class="text-muted text-center py-4">Belum ada riwayat perubahan status.</p>
                        <?php endif; ?>
                    </div>
                </div><!-- /.tab-content -->
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // Enable/disable note textarea based on selected status
    document.querySelectorAll('input[name="status"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            var noteEl = document.getElementById('note');
            if (this.value === 'COR') {
                noteEl.disabled = false;
                noteEl.focus();
            } else {
                noteEl.disabled = true;
                noteEl.value = '';
                document.getElementById('invalid-note').classList.add('d-none');
            }
            document.getElementById('invalid-action').classList.add('d-none');
        });
    });

    document.getElementById('btn-save-review').addEventListener('click', function () {
        var status = document.querySelector('input[name="status"]:checked');
        var note   = document.getElementById('note').value.trim();
        var valid  = true;

        if (!status) {
            document.getElementById('invalid-action').classList.remove('d-none');
            valid = false;
        }

        if (status && status.value === 'COR' && note === '') {
            document.getElementById('invalid-note').classList.remove('d-none');
            valid = false;
        }

        if (!valid) return;

        var formData = {
            id:     document.getElementById('form_id').value,
            status: status.value,
            note:   note
        };

        $.ajax({
            url:      '<?= base_url($this->uri->segment(1) . '/save_review_form'); ?>',
            type:     'POST',
            data:     formData,
            dataType: 'json',
            success: function (res) {
                if (res.status == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.msg,
                        confirmButtonText: 'OK'
                    }).then(function () {
                        window.location.href = '<?= base_url($this->uri->segment(1) . '/forms_review'); ?>';
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.msg });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan. Silakan coba lagi.' });
            }
        });
    });
})();
</script>
