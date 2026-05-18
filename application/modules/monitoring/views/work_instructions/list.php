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
                        <?php if (!empty($work_instructions)): ?>
                            <?php $n = 0;
                            foreach ($work_instructions as $wi):
                                $n++;
                                $can = !empty($wi->can_action);
                                ?>
                                <tr>
                                    <td class="text-center" style="vertical-align: middle;"><?= $n; ?></td>
                                    <td style="vertical-align: middle;">
                                        <?= htmlspecialchars($wi->number ? $wi->number : '-'); ?>
                                    </td>
                                    <td class="font-weight-bolder h6" style="vertical-align: middle;">
                                        <?= htmlspecialchars($wi->name ? $wi->name : '-'); ?>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <?= htmlspecialchars($wi->departement_name ? $wi->departement_name : '-'); ?>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <?= htmlspecialchars($wi->reviewer_position_name ? $wi->reviewer_position_name : '-'); ?>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <?= htmlspecialchars($wi->approver_position_name ? $wi->approver_position_name : '-'); ?>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?= isset($sts[$wi->status]) ? $sts[$wi->status] : htmlspecialchars($wi->status); ?>
                                        <?php if (isset($wi->is_under_revision) && $wi->is_under_revision == 1): ?>
                                            <br><span class="badge badge-warning mt-1">
                                                <i class="fa fa-sync-alt"></i> Under Revision
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?php if ($wi->status === 'REV'): ?>
                                            <?php if ($can): ?>
                                                <button type="button" class="btn btn-sm btn-light-warning btn-open-modal"
                                                    data-id="<?= $wi->id; ?>" data-type="review"
                                                    data-title="Review Work Instruction — <?= htmlspecialchars($wi->name ? $wi->name : ''); ?>">
                                                    <i class="fa fa-edit"></i> Review
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary btn-no-access"
                                                    data-msg="Akses ditolak. Anda bukan PIC Reviewer yang berwenang untuk Work Instruction ini."
                                                    disabled>
                                                    <i class="fa fa-lock"></i> Review
                                                </button>
                                            <?php endif; ?>

                                        <?php elseif ($wi->status === 'COR'): ?>
                                            <?php if ($can): ?>
                                                <a href="<?= base_url('work_instructions/edit/' . $wi->id); ?>"
                                                    class="btn btn-sm btn-light-danger">
                                                    <i class="fa fa-wrench"></i> Correction
                                                </a>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary btn-no-access"
                                                    data-msg="Akses ditolak. Hanya pembuat Work Instruction yang dapat mengajukan koreksi."
                                                    disabled>
                                                    <i class="fa fa-lock"></i> Correction
                                                </button>
                                            <?php endif; ?>

                                        <?php elseif ($wi->status === 'APV'): ?>
                                            <?php if ($can): ?>
                                                <button type="button" class="btn btn-sm btn-light-info btn-open-modal"
                                                    data-id="<?= $wi->id; ?>" data-type="approval"
                                                    data-title="Approval Work Instruction — <?= htmlspecialchars($wi->name ? $wi->name : ''); ?>">
                                                    <i class="fa fa-check"></i> Approval
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary btn-no-access"
                                                    data-msg="Akses ditolak. Anda bukan PIC Approver yang berwenang untuk Work Instruction ini."
                                                    disabled>
                                                    <i class="fa fa-lock"></i> Approval
                                                </button>
                                            <?php endif; ?>

                                        <?php elseif ($wi->status === 'PUB'): ?>
                                            <button type="button" class="btn btn-sm btn-light-primary btn-open-modal"
                                                data-id="<?= $wi->id; ?>" data-type="view"
                                                data-title="View Work Instruction — <?= htmlspecialchars($wi->name ? $wi->name : ''); ?>">
                                                <i class="fa fa-eye"></i> View
                                            </button>

                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
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

<!-- Modal: Review / Approval WI -->
<div class="modal fade" id="modalWIAction" tabindex="-1" role="dialog" aria-labelledby="modalWIActionLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h5 class="modal-title font-weight-bolder" id="modalWIActionLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalWIActionBody">
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
        var baseUrl = '<?= base_url('work_instructions'); ?>';
        var monitoringUrl = '<?= base_url('monitoring'); ?>';
        var urlReview = monitoringUrl + '/load_wi_review_modal/';
        var urlApproval = monitoringUrl + '/load_wi_approval_modal/';
        var urlView = baseUrl + '/view_modal/';

        // Tombol buka modal (REV / APV / VIEW)
        $(document).on('click', '.btn-open-modal', function () {
            var id = $(this).data('id');
            var type = $(this).data('type');
            var title = $(this).data('title');
            var url;

            if (type === 'review') url = urlReview + id;
            else if (type === 'approval') url = urlApproval + id;
            else if (type === 'view') url = urlView + id;

            $('#modalWIActionLabel').text(title);
            $('#modalWIActionBody').html(
                '<div class="text-center py-5"><span class="spinner-border text-primary"></span><p class="mt-2 text-muted">Memuat data...</p></div>'
            );
            $('#modalWIAction').modal('show');

            $.ajax({
                url: url,
                type: 'GET',
                success: function (html) {
                    // Cek apakah response adalah JSON error (akses ditolak dari server)
                    try {
                        var json = JSON.parse(html);
                        if (json.status === 0) {
                            $('#modalWIAction').modal('hide');
                            Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: json.msg });
                            return;
                        }
                    } catch (e) {
                        // Bukan JSON, berarti HTML partial — tampilkan di modal
                    }
                    $('#modalWIActionBody').html(html);
                },
                error: function (xhr) {
                    $('#modalWIAction').modal('hide');
                    var msg = 'Terjadi kesalahan. Silakan coba lagi.';
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.msg) msg = json.msg;
                    } catch (e) { }
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

        // Handle enable/disable textarea note_correction berdasarkan pilihan radio
        $(document).on('change', 'input[name="status"]', function () {
            if ($(this).val() == 'APV') {
                $('#note_correction').prop('disabled', true).val('');
                $('#note_correction_modal').prop('disabled', true).val('');
            } else {
                $('#note_correction').prop('disabled', false);
                $('#note_correction_modal').prop('disabled', false);
            }
        });

        // Handle submit form review (dari modal review dengan preview)
        $(document).on('submit', '#form-review-modal', function (e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: baseUrl + '/saveReview',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function () {
                    $('#save-review-modal').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
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
                        $('#save-review-modal').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i> Submit Review');
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan. Silakan coba lagi.';
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.msg) msg = json.msg;
                    } catch (e) { }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                    $('#save-review-modal').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i> Submit Review');
                }
            });
        });

        // Handle submit form review (dari form_review.php yang simple)
        $(document).on('submit', '#form-review', function (e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: baseUrl + '/saveReview',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function () {
                    $('#save-review').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
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
                        $('#save-review').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i>Submit Review');
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan. Silakan coba lagi.';
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.msg) msg = json.msg;
                    } catch (e) { }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                    $('#save-review').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i>Submit Review');
                }
            });
        });

        // Handle submit form approval
        $(document).on('submit', '#form-approval', function (e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: baseUrl + '/saveApprove',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function () {
                    $('#save-approve').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
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
                        $('#save-approve').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i>Submit Approval');
                    }
                },
                error: function (xhr) {
                    var msg = 'Terjadi kesalahan. Silakan coba lagi.';
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.msg) msg = json.msg;
                    } catch (e) { }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                    $('#save-approve').prop('disabled', false).html('<i class="fab fa-telegram-plane"></i>Submit Approval');
                }
            });
        });
    })();
</script>