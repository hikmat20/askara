<div class="alert bg-light-danger border border-danger mb-4" role="alert">
    <h5 class="alert-heading font-weight-bolder">
        <i class="fa fa-exclamation-triangle text-danger mr-2"></i>PERHATIAN!
    </h5>
    <p class="mb-0">
        Dengan mengajukan penghapusan, work instruction ini akan dihapus secara permanen atau tidak dapat diakses lagi.
        Proses ini mungkin memerlukan review dan approval sebelum sepenuhnya dihapus.
        Mohon berikan alasan dengan jelas.
    </p>
</div>

<form id="wi-deletion-modal">
    <input type="hidden" name="id" id="deletion_wi_id" value="<?= $wi->id; ?>">

    <div class="form-group mb-4">
        <label class="col-form-label font-weight-bold">
            Alasan Penghapusan <span class="text-danger">*</span>
        </label>
        <textarea name="note" id="deletion_note" rows="5" class="form-control"
            placeholder="Tuliskan alasan penghapusan secara jelas..."></textarea>
        <span id="invalid-deletion-note" class="d-none text-danger small mt-1 d-block">Alasan penghapusan wajib diisi.</span>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <button type="button" class="btn btn-light-danger" id="btn-submit-deletion">
            <i class="fa fa-trash-alt mr-1"></i> Submit Deletion
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    </div>
</form>

<script>
(function () {
    document.getElementById('btn-submit-deletion').addEventListener('click', function () {
        var btn  = this;
        var note = document.getElementById('deletion_note').value.trim();

        if (!note) {
            document.getElementById('invalid-deletion-note').classList.remove('d-none');
            return;
        }
        document.getElementById('invalid-deletion-note').classList.add('d-none');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';

        $.ajax({
            url:      '<?= base_url('work_instructions/save_wi_deletion'); ?>',
            type:     'POST',
            dataType: 'json',
            data: {
                id:   document.getElementById('deletion_wi_id').value,
                note: note
            },
            success: function (res) {
                if (res.status == 1) {
                    $('#modalWIAction').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.msg, confirmButtonText: 'OK' })
                        .then(function () { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.msg });
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-trash-alt mr-1"></i> Submit Deletion';
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan. Silakan coba lagi.' });
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-trash-alt mr-1"></i> Submit Deletion';
            }
        });
    });
})();
</script>
