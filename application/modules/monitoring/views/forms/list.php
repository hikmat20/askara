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
                            <th class="text-center" width="150px">Status</th>
                            <th class="text-center" width="120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($forms)) : ?>
                            <?php $n = 0; foreach ($forms as $form) : $n++;
                                $can = !empty($form->can_action);
                            ?>
                                <tr>
                                    <td class="text-center" style="vertical-align: middle;"><?= $n; ?></td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars($form->nomor ?? '-'); ?></td>
                                    <td class="font-weight-bolder h6" style="vertical-align: middle;">
                                        <?= htmlspecialchars($form->name ?? '-'); ?>
                                    </td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars($form->departement_name ?? '-'); ?></td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars($form->reviewer_position_name ?? '-'); ?></td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars($form->approver_position_name ?? '-'); ?></td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?= isset($sts[$form->status]) ? $sts[$form->status] : htmlspecialchars($form->status); ?>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?php if ($form->status === 'REV') : ?>
                                            <?php if ($can) : ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-light-warning btn-open-modal"
                                                    data-id="<?= $form->id; ?>"
                                                    data-type="review"
                                                    data-title="Review Form — <?= htmlspecialchars($form->name ?? ''); ?>">
                                                    <i class="fa fa-edit"></i> Review
                                                </button>
                                            <?php else : ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-secondary btn-no-access"
                                                    data-msg="Akses ditolak. Anda bukan PIC Reviewer yang berwenang untuk Form ini."
                                                    disabled>
                                                    <i class="fa fa-lock"></i> Review
                                                </button>
                                            <?php endif; ?>

                                        <?php elseif ($form->status === 'COR') : ?>
                                            <?php if ($can) : ?>
                                                <a href="<?= base_url($this->uri->segment(1) . '/load_form_correction_form/' . $form->id); ?>"
                                                    class="btn btn-sm btn-light-danger">
                                                    <i class="fa fa-wrench"></i> Correction
                                                </a>
                                            <?php else : ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-secondary btn-no-access"
                                                    data-msg="Akses ditolak. Hanya pembuat form yang dapat mengajukan koreksi."
                                                    disabled>
                                                    <i class="fa fa-lock"></i> Correction
                                                </button>
                                            <?php endif; ?>

                                        <?php elseif ($form->status === 'APV') : ?>
                                            <?php if ($can) : ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-light-info btn-open-modal"
                                                    data-id="<?= $form->id; ?>"
                                                    data-type="approval"
                                                    data-title="Approval Form — <?= htmlspecialchars($form->name ?? ''); ?>">
                                                    <i class="fa fa-check"></i> Approval
                                                </button>
                                            <?php else : ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-secondary btn-no-access"
                                                    data-msg="Akses ditolak. Anda bukan PIC Approver yang berwenang untuk Form ini."
                                                    disabled>
                                                    <i class="fa fa-lock"></i> Approval
                                                </button>
                                            <?php endif; ?>

                                        <?php else : ?>
                                            <?php if ($form->status === 'PUB') : ?>
                                                <?php if (!empty($ArrPosts) && in_array(1, $ArrPosts)) : ?>
                                                    <button type="button"
                                                        class="btn btn-sm btn-light-warning btn-open-modal"
                                                        data-id="<?= $form->id; ?>"
                                                        data-type="revision"
                                                        data-title="Revision Form — <?= htmlspecialchars($form->name ?? ''); ?>"
                                                        title="Request Revision">
                                                        <i class="far fa-edit"></i> Revision
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-light-danger btn-open-modal"
                                                        data-id="<?= $form->id; ?>"
                                                        data-type="deletion"
                                                        data-title="Deletion Form — <?= htmlspecialchars($form->name ?? ''); ?>"
                                                        title="Request Deletion">
                                                        <i class="fa fa-trash-alt"></i> Deletion
                                                    </button>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Review / Approval Form -->
<div class="modal fade" id="modalFormAction" tabindex="-1" role="dialog" aria-labelledby="modalFormActionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h5 class="modal-title font-weight-bolder" id="modalFormActionLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalFormActionBody">
                <div class="text-center py-5">
                    <span class="spinner-border text-primary"></span>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var baseUrl     = '<?= base_url($this->uri->segment(1)); ?>';
    var urlReview   = baseUrl + '/load_form_review_form/';
    var urlApproval = baseUrl + '/load_form_approval_form/';
    var urlRevision = baseUrl + '/load_form_revision_form/';
    var urlDeletion = baseUrl + '/load_form_deletion_form/';

    // Tombol buka modal (REV / APV / revision / deletion)
    $(document).on('click', '.btn-open-modal', function () {
        var id    = $(this).data('id');
        var type  = $(this).data('type');
        var title = $(this).data('title');
        var url;

        if (type === 'review')        url = urlReview   + id;
        else if (type === 'approval') url = urlApproval + id;
        else if (type === 'revision') url = urlRevision + id;
        else if (type === 'deletion') url = urlDeletion + id;

        $('#modalFormActionLabel').text(title);
        $('#modalFormActionBody').html(
            '<div class="text-center py-5"><span class="spinner-border text-primary"></span><p class="mt-2 text-muted">Memuat data...</p></div>'
        );
        $('#modalFormAction').modal('show');

        $.ajax({
            url:      url,
            type:     'GET',
            success: function (html) {
                // Cek apakah response adalah JSON error (akses ditolak dari server)
                try {
                    var json = JSON.parse(html);
                    if (json.status === 0) {
                        $('#modalFormAction').modal('hide');
                        Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: json.msg });
                        return;
                    }
                } catch (e) {
                    // Bukan JSON, berarti HTML partial — tampilkan di modal
                }
                $('#modalFormActionBody').html(html);
            },
            error: function (xhr) {
                $('#modalFormAction').modal('hide');
                var msg = 'Terjadi kesalahan. Silakan coba lagi.';
                try {
                    var json = JSON.parse(xhr.responseText);
                    if (json.msg) msg = json.msg;
                } catch (e) {}
                Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: msg });
            }
        });
    });

    // Tombol disabled (tidak punya akses) — klik tetap tampilkan pesan
    $(document).on('click', '.btn-no-access', function () {
        var msg = $(this).data('msg') || 'Anda tidak memiliki akses untuk melakukan aksi ini.';
        Swal.fire({
            icon: 'warning',
            title: 'Akses Ditolak',
            text: msg,
            confirmButtonText: 'Mengerti'
        });
    });
})();
</script>
