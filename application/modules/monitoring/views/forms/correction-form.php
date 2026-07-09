<div class="content d-flex flex-column flex-column-fluid p-0">
    <div class="container mt-3">
        <div class="mb-5">
            <div style="font-size: 36px;" class="text-white font-weight-bolder">
                <a style="font-size: 30px;" href="<?= base_url($this->uri->segment(1) . '/forms_correction'); ?>" class="text-danger" title="Kembali ke Daftar Correction">
                    <span class="fa fa-arrow-circle-left"></span>
                </a>
                CORRECTION FORM
            </div>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <ul class="nav nav-pills nav-light-warning py-0 mb-4" id="correctionTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-correction">
                            <span class="nav-icon"><i class="fa fa-wrench"></i></span>
                            <span class="nav-text">Submit Correction</span>
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
                    <!-- TAB: Submit Correction -->
                    <div class="tab-pane fade show active" id="tab-correction" role="tabpanel">
                        <?php
                        // Ambil catatan koreksi dari log terakhir
                        $lastCor = null;
                        if (!empty($history)) {
                            foreach (array_reverse($history) as $log) {
                                if ($log->new_status === 'COR') { $lastCor = $log; break; }
                            }
                        }
                        ?>
                        <?php if ($lastCor && !empty($lastCor->note)) : ?>
                            <div class="alert alert-warning mb-4">
                                <strong>Catatan Koreksi dari Reviewer:</strong><br>
                                <?= htmlspecialchars($lastCor->note); ?>
                            </div>
                        <?php endif; ?>

                        <form id="form-correction-form">
                            <input type="hidden" name="id" id="form_id" value="<?= $form->id; ?>">

                            <div class="form-group">
                                <label class="col-form-label font-weight-bold">Catatan Perbaikan <span class="text-muted font-weight-normal">(opsional)</span></label>
                                <textarea name="note" id="note" rows="4" class="form-control" placeholder="Tuliskan keterangan perbaikan yang dilakukan..."></textarea>
                            </div>

                            <button type="button" class="btn btn-light-warning mt-2" id="btn-save-correction">
                                <i class="fab fa-telegram-plane"></i> Submit — Kembalikan ke Review
                            </button>
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
                                    <?php $this->load->view('partials/activity_log_ui', ['history' => $history, 'sts' => isset($sts) ? $sts : []]); ?>
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
    document.getElementById('btn-save-correction').addEventListener('click', function () {
        var btn      = this;
        var formData = {
            id:   document.getElementById('form_id').value,
            note: document.getElementById('note').value.trim()
        };

        // Cegah double-click sebelum Swal muncul
        btn.disabled = true;

        Swal.fire({
            title: 'Yakin sudah diperbaiki?',
            text: 'Form akan dikembalikan ke proses Review.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Submit',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (!result.isConfirmed) {
                btn.disabled = false;
                return;
            }

            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';

            $.ajax({
                url:      '<?= base_url($this->uri->segment(1) . '/save_correction_form'); ?>',
                type:     'POST',
                data:     formData,
                dataType: 'json',
                success: function (res) {
                    if (res.status == 1) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.msg, confirmButtonText: 'OK' })
                            .then(function () {
                                window.location.href = '<?= base_url($this->uri->segment(1) . '/forms_correction'); ?>';
                            });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.msg });
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fab fa-telegram-plane"></i> Submit — Kembalikan ke Review';
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan. Silakan coba lagi.' });
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fab fa-telegram-plane"></i> Submit — Kembalikan ke Review';
                }
            });
        });
    });
})();
</script>
