<div class="content d-flex flex-column flex-column-fluid p-0">
    <div class="container mt-3">
        <div class="mb-5">
            <div style="font-size: 36px;" class="text-white font-weight-bolder">
                <a style="font-size: 30px;" href="<?= base_url($this->uri->segment(1)); ?>" class="text-danger"
                    title="Kembali ke Monitoring">
                    <span class="fa fa-arrow-circle-left"></span>
                </a>
                <?= $title; ?>
            </div>
        </div>
        <div class="card">
            <div class="pt-1 px-3 card-body">
                <table class="table table-hover datatable">
                    <thead>
                        <tr class="table-light">
                            <th width="40px">No</th>
                            <th>Nomor</th>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>PIC Reviewer</th>
                            <th>PIC Approver</th>
                            <th>Alasan</th>
                            <th class="text-center" width="150px">Status</th>
                            <th class="text-center" width="160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($work_instructions)) : ?>
                            <?php $n = 0; foreach ($work_instructions as $wi) : $n++;
                                $isPmMr = !empty($ArrPosts) && in_array(1, $ArrPosts);
                                $isReviewDeletion  = ($wi->deletion_status === 'REV');
                                $isApprovalDeletion = ($wi->deletion_status === 'APV');
                            ?>
                                <tr>
                                    <td class="text-center" style="vertical-align: middle;"><?= $n; ?></td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars(isset($wi->number) ? $wi->number : '-'); ?></td>
                                    <td class="font-weight-bolder h6" style="vertical-align: middle;">
                                        <?= htmlspecialchars(isset($wi->name) ? $wi->name : '-'); ?>
                                    </td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars(isset($wi->departement_name) ? $wi->departement_name : '-'); ?></td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars(isset($wi->reviewer_position_name) ? $wi->reviewer_position_name : '-'); ?></td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars(isset($wi->approver_position_name) ? $wi->approver_position_name : '-'); ?></td>
                                    <td style="vertical-align: middle; max-width:200px;">
                                        <small class="text-muted"><?= htmlspecialchars(isset($wi->note) ? $wi->note : '-'); ?></small>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?php if ($wi->deletion_status === 'REV') : ?>
                                            <span class="label label-light-warning label-pill label-inline">Menunggu Review</span>
                                        <?php elseif ($wi->deletion_status === 'APV') : ?>
                                            <span class="label label-light-danger label-pill label-inline">Menunggu Approval</span>
                                        <?php else : ?>
                                            <?= isset($sts[$wi->status]) ? $sts[$wi->status] : htmlspecialchars($wi->status); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?php if ($isPmMr) : ?>
                                            <?php if ($isReviewDeletion) : ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-light-success btn-del-action"
                                                    data-id="<?= $wi->id; ?>"
                                                    data-action="APV"
                                                    data-confirm="Setujui pengajuan deletion ini dan lanjutkan ke Approval?">
                                                    <i class="fa fa-check"></i> Setujui
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-light-danger btn-del-action"
                                                    data-id="<?= $wi->id; ?>"
                                                    data-action="REJ"
                                                    data-confirm="Tolak pengajuan deletion? Work Instruction akan dikembalikan ke Published.">
                                                    <i class="fa fa-times"></i> Tolak
                                                </button>
                                            <?php elseif ($isApprovalDeletion) : ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-del-action"
                                                    data-id="<?= $wi->id; ?>"
                                                    data-action="DEL"
                                                    data-confirm="Setujui dan hapus work instruction ini secara permanen? Tindakan ini tidak dapat dibatalkan.">
                                                    <i class="fa fa-trash-alt"></i> Hapus
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-light-secondary btn-del-action"
                                                    data-id="<?= $wi->id; ?>"
                                                    data-action="REJ"
                                                    data-confirm="Tolak pengajuan deletion? Work Instruction akan dikembalikan ke Published.">
                                                    <i class="fa fa-times"></i> Tolak
                                                </button>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <span class="text-muted small">Tidak ada akses</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Belum ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var baseUrl = '<?= base_url($this->uri->segment(1)); ?>';

    $(document).on('click', '.btn-del-action', function () {
        var btn     = $(this);
        var id      = btn.data('id');
        var action  = btn.data('action');
        var confirm = btn.data('confirm');

        // Warna icon sesuai aksi
        var icon = (action === 'DEL' || action === 'REJ') ? 'warning' : 'question';

        Swal.fire({
            title: 'Konfirmasi',
            text:  confirm,
            icon:  icon,
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText:  'Batal',
            customClass: {
                confirmButton: (action === 'DEL') ? 'btn btn-danger' : 'btn btn-primary',
                cancelButton:  'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (!result.isConfirmed) return;

            btn.prop('disabled', true);

            // Tentukan endpoint berdasarkan deletion_status saat ini
            // REV → save_wi_rev_deletion | APV → save_wi_apv_deletion
            var isReview = btn.closest('tr').find('.label-light-warning').length > 0;
            var endpoint = isReview
                ? baseUrl + '/save_wi_rev_deletion'
                : baseUrl + '/save_wi_apv_deletion';

            $.ajax({
                url:      endpoint,
                type:     'POST',
                dataType: 'json',
                data:     { id: id, action: action },
                success: function (res) {
                    if (res.status == 1) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.msg, confirmButtonText: 'OK' })
                            .then(function () { location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.msg });
                        btn.prop('disabled', false);
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan. Silakan coba lagi.' });
                    btn.prop('disabled', false);
                }
            });
        });
    });
})();
</script>
