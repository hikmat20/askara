<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-5">
                <div>
                    <h2 class="font-weight-bolder text-warning mb-1"><i class="<?= $icon; ?> mr-2 text-primary"></i><?= $title; ?></h2>
                    <div class="text-muted font-size-sm">Monitor status pengiriman semua notifikasi email sistem</div>
                </div>
                <div class="d-flex">
                    <a href="<?= site_url('setting/email_settings'); ?>" class="btn btn-light-primary font-weight-bold mr-2">
                        <i class="fa fa-cog"></i> Pengaturan SMTP
                    </a>
                    <button type="button" id="btn-clear-sent" class="btn btn-light-danger font-weight-bold">
                        <i class="fa fa-trash"></i> Hapus Sent
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-6" id="summary-cards">
                <div class="col-md-3">
                    <div class="card card-custom bg-light-warning card-shadowless">
                        <div class="card-body d-flex align-items-center py-6 px-7">
                            <div class="mr-5">
                                <span class="svg-icon svg-icon-3x svg-icon-warning d-block">
                                    <i class="fa fa-clock text-warning fa-2x"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-size-h4 font-weight-bolder" id="count-pending">-</div>
                                <div class="font-size-sm font-weight-bold text-muted">Pending</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom bg-light-success card-shadowless">
                        <div class="card-body d-flex align-items-center py-6 px-7">
                            <div class="mr-5">
                                <span class="svg-icon svg-icon-3x svg-icon-success d-block">
                                    <i class="fa fa-check-circle text-success fa-2x"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-size-h4 font-weight-bolder" id="count-sent">-</div>
                                <div class="font-size-sm font-weight-bold text-muted">Sent</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom bg-light-danger card-shadowless">
                        <div class="card-body d-flex align-items-center py-6 px-7">
                            <div class="mr-5">
                                <span class="svg-icon svg-icon-3x svg-icon-danger d-block">
                                    <i class="fa fa-times-circle text-danger fa-2x"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-size-h4 font-weight-bolder" id="count-failed">-</div>
                                <div class="font-size-sm font-weight-bold text-muted">Failed</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom bg-light-primary card-shadowless">
                        <div class="card-body d-flex align-items-center py-6 px-7">
                            <div class="mr-5">
                                <span class="svg-icon svg-icon-3x svg-icon-primary d-block">
                                    <i class="fa fa-envelope text-primary fa-2x"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-size-h4 font-weight-bolder" id="count-total">-</div>
                                <div class="font-size-sm font-weight-bold text-muted">Total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="card card-custom shadow-sm mb-5">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <label class="font-weight-bold mr-3 mb-0">Filter Status:</label>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary filter-btn active" data-status="">Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-status="PND">Pending</button>
                            <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-status="SND">Sent</button>
                            <button type="button" class="btn btn-sm btn-outline-danger filter-btn" data-status="FAI">Failed</button>
                        </div>
                        <div class="ml-auto">
                            <button type="button" id="btn-refresh" class="btn btn-sm btn-light-primary">
                                <i class="fa fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Queue Table -->
            <div class="card card-custom shadow-sm">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fa fa-list-ul text-primary mr-2"></i> Daftar Antrean Email
                    </h3>
                </div>
                <div class="card-body pt-0 pb-3">
                    <div class="table-responsive">
                        <table id="table-queue" class="table table-head-custom table-vertical-center table-hover">
                            <thead>
                                <tr class="text-uppercase text-left font-size-xs font-weight-bolder text-muted bg-gray-100">
                                    <th width="50">No</th>
                                    <th width="130">Waktu Buat</th>
                                    <th>Penerima (To)</th>
                                    <th>Subject</th>
                                    <th width="100">Status</th>
                                    <th width="80" class="text-center">Coba</th>
                                    <th width="130">Terkirim Pada</th>
                                    <th width="100" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Preview Email -->
<div class="modal fade" id="modal-preview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"><i class="fa fa-eye mr-2 text-white"></i> Preview Email</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="iframe-preview" style="width:100%;height:500px;border:0;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    // ─── Summary Counts ───────────────────────────────────────────────────────
    function loadCounts() {
        $.getJSON(siteurl + 'setting/email_settings/get_queue_counts', function(res) {
            if (res.status == 1) {
                $('#count-pending').text(res.pending);
                $('#count-sent').text(res.sent);
                $('#count-failed').text(res.failed);
                $('#count-total').text(res.total);
            }
        });
    }

    // ─── Datatables ───────────────────────────────────────────────────────────
    var currentFilter = '';

    var table = $('#table-queue').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: siteurl + 'setting/email_settings/get_queue_data',
            type: 'GET',
            data: function(d) {
                d.status = currentFilter;
                return d;
            }
        },
        columns: [
            {data: 0, width: '50px'},
            {data: 1, width: '130px'},
            {data: 2},
            {data: 3},
            {data: 4, width: '100px', className: 'text-center'},
            {data: 5, width: '60px', className: 'text-center'},
            {data: 6, width: '130px'},
            {data: 7, width: '100px', className: 'text-center', orderable: false}
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: '<div class="d-flex justify-content-center align-items-center"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</div>',
            emptyTable: '<div class="text-center py-5 text-muted"><i class="fa fa-inbox fa-3x mb-3 d-block"></i>Tidak ada data antrean email</div>',
            zeroRecords: '<div class="text-center py-5 text-muted"><i class="fa fa-search fa-3x mb-3 d-block"></i>Tidak ada data yang sesuai pencarian</div>'
        },
        // drawCallback: function() {
        //     loadCounts();
        //     // Re-init tooltip
        //     $('[data-toggle="tooltip"]').tooltip();
        // }
    });

    // ─── Filter by Status ─────────────────────────────────────────────────────
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('status');
        table.draw();
    });

    // ─── Preview Email ────────────────────────────────────────────────────────
    $(document).on('click', '.btn-preview', function() {
        var id = $(this).data('id');
        var url = siteurl + 'setting/email_settings/preview/' + id;
        $('#iframe-preview').attr('src', url);
        $('#modal-preview').modal('show');
    });

    // ─── Refresh Button ───────────────────────────────────────────────────────
    $('#btn-refresh').on('click', function() {
        table.ajax.reload();
    });

    // ─── Resend Failed Email ──────────────────────────────────────────────────
    $(document).on('click', '.btn-resend', function() {
        var id   = $(this).data('id');
        var $btn = $(this);

        Swal.fire({
            title: 'Kirim Ulang?',
            text: 'Email ini akan dimasukkan kembali ke antrean untuk dikirim ulang.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Ulang!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3699FF'
        }).then(function(result) {
            if (result.value) {
                $btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
                $.ajax({
                    url: siteurl + 'setting/email_settings/resend_queue',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: id},
                    success: function(res) {
                        if (res.status == 1) {
                            Swal.fire('Berhasil!', res.msg, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', res.msg, 'error');
                            $btn.html('<i class="fa fa-redo"></i> Resend').prop('disabled', false);
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan server.', 'error');
                        $btn.html('<i class="fa fa-redo"></i> Resend').prop('disabled', false);
                    }
                });
            }
        });
    });

    // ─── Clear Sent Emails ────────────────────────────────────────────────────
    $('#btn-clear-sent').on('click', function() {
        Swal.fire({
            title: 'Hapus Semua Email Terkirim?',
            text: 'Riwayat semua email dengan status "Sent" akan dihapus dari daftar. Email yang sudah terkirim tidak akan terpengaruh.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#F64E60'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: siteurl + 'setting/email_settings/clear_sent',
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == 1) {
                            Swal.fire('Berhasil!', res.msg, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', res.msg, 'error');
                        }
                    }
                });
            }
        });
    });

    // ─── Auto Refresh setiap 30 detik ────────────────────────────────────────
    setInterval(function() {
        table.ajax.reload(null, false); // false = tidak reset halaman
    }, 30000);

});
</script>
