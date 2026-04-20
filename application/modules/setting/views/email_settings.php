<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-stretch shadow card-custom">
                <div class="card-header">
                    <h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
                </div>
                <form id="form-email-setting">
                    <div class="card-body">
                        <div class="alert alert-custom alert-light-info fade show mb-5" role="alert">
                            <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
                            <div class="alert-text">
                                Pengaturan ini digunakan untuk mengirim pesan notifikasi dokumen (Review, Approve, dll) ke pengguna. 
                                Pastikan menggunakan <strong>App Password</strong> jika memakai Gmail.
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="smtp_host" class="col-sm-3 col-form-label font-weight-bold">SMTP Host</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?= isset($data['smtp_host']) ? $data['smtp_host'] : 'ssl://smtp.googlemail.com' ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="smtp_port" class="col-sm-3 col-form-label font-weight-bold">SMTP Port</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="smtp_port" name="smtp_port" value="<?= isset($data['smtp_port']) ? $data['smtp_port'] : '465' ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="smtp_user" class="col-sm-3 col-form-label font-weight-bold">SMTP Email User</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="smtp_user" name="smtp_user" value="<?= isset($data['smtp_user']) ? $data['smtp_user'] : '' ?>" placeholder="contoh: admin@perusahaan.com" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="smtp_pass" class="col-sm-3 col-form-label font-weight-bold">SMTP App Password</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" value="<?= isset($data['smtp_pass']) ? $data['smtp_pass'] : '' ?>" placeholder="16 karakter khusus" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="toggle-password" style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="smtp_crypto" class="col-sm-3 col-form-label font-weight-bold">Enkripsi (Crypto)</label>
                            <div class="col-sm-9">
                                <select class="form-control" id="smtp_crypto" name="smtp_crypto">
                                    <option value="ssl" <?= isset($data['smtp_crypto']) && $data['smtp_crypto'] == 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                                    <option value="tls" <?= isset($data['smtp_crypto']) && $data['smtp_crypto'] == 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-warning font-weight-bold mr-2" id="btn-test"><i class="fas fa-paper-plane"></i> Kirim Test Email</button>
                        <button type="button" class="btn btn-primary font-weight-bold" id="btn-save"><i class="fas fa-save"></i> Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Fitur Toggle Password
        $('#toggle-password').click(function() {
            var input = $('#smtp_pass');
            var icon = $(this).find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fas fa-eye').addClass('fas fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fas fa-eye-slash').addClass('fas fa-eye');
            }
        });

        $('#btn-save').click(function() {
            var btn = $(this);
            var formdata = $('#form-email-setting').serialize();
            
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Simpan pengaturan email ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: siteurl + 'setting/email_settings/save',
                        type: 'POST',
                        dataType: 'json',
                        data: formdata,
                        success: function(res) {
                            if (res.status == 1) {
                                Swal.fire('Sukses!', res.msg, 'success');
                            } else {
                                Swal.fire('Gagal!', res.msg, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                        }
                    });
                }
            });
        });

        $('#btn-test').click(function() {
            var btn = $(this);
            var formdata = $('#form-email-setting').serialize();
            var target_email = $('#smtp_user').val();
            
            if (!target_email) {
                Swal.fire('Peringatan', 'Harap isi kolom SMTP Email User untuk tujuan test pengiriman.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Kirim Test Email?',
                text: 'Saya akan mencoba mengirim email notifikasi ke ' + target_email + ' menggunakan pengaturan yang ada pada form ini.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim Test!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: siteurl + 'setting/email_settings/test_email',
                        type: 'POST',
                        dataType: 'json',
                        data: formdata,
                        beforeSend: function() {
                            btn.attr('disabled', true);
                            btn.html('<i class="spinner spinner-border-sm"></i> Mengirim...');
                        },
                        complete: function() {
                            btn.attr('disabled', false);
                            btn.html('<i class="fas fa-paper-plane"></i> Kirim Test Email');
                        },
                        success: function(res) {
                            if (res.status == 1) {
                                Swal.fire('Sukses!', res.msg, 'success');
                            } else {
                                Swal.fire('Gagal!', res.msg, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Terjadi kesalahan komunikasi dengan server.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
