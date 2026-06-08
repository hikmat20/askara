<div class="alert bg-light-warning border border-warning mb-4" role="alert">
    <h5 class="alert-heading font-weight-bolder">
        <i class="fa fa-exclamation-triangle text-warning mr-2"></i>PERHATIAN!
    </h5>
    <p class="mb-0">
        Dengan mengajukan revisi, work instruction ini akan dikembalikan ke status <strong>Revision</strong>
        dan perlu melalui proses pembuatan ulang, review, serta approval sebelum dapat dipublikasikan kembali.
        Mohon berikan alasan dengan jelas.
    </p>
</div>

<form id="wi-revision-modal">
    <input type="hidden" name="id" id="revision_wi_id" value="<?= $wi->id; ?>">

    <div class="form-group mb-4">
        <label class="col-form-label font-weight-bold">
            Alasan Revisi <span class="text-danger">*</span>
        </label>
        <textarea name="note" id="revision_note" rows="5" class="form-control"
            placeholder="Tuliskan alasan revisi secara jelas..."></textarea>
        <span id="invalid-revision-note" class="d-none text-danger small mt-1 d-block">Alasan revisi wajib diisi.</span>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <button type="button" class="btn btn-light-warning" id="btn-submit-revision">
            <i class="fa fa-paper-plane mr-1"></i> Submit Revision
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    </div>
</form>

<script>
(function () {
    document.getElementById('btn-submit-revision').addEventListener('click', function () {
        var btn  = this;
        var note = document.getElementById('revision_note').value.trim();

        if (!note) {
            document.getElementById('invalid-revision-note').classList.remove('d-none');
            return;
        }
        document.getElementById('invalid-revision-note').classList.add('d-none');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';

        $.ajax({
            url:      '<?= base_url('work_instructions/save_wi_revision'); ?>',
            type:     'POST',
            dataType: 'json',
            data: {
                id:   document.getElementById('revision_wi_id').value,
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
                    btn.innerHTML = '<i class="fa fa-paper-plane mr-1"></i> Submit Revision';
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan. Silakan coba lagi.' });
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-paper-plane mr-1"></i> Submit Revision';
            }
        });
    });
})();
</script>
