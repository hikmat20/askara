<div class="alert bg-light-warning border border-warning mb-4" role="alert">
    <h5 class="alert-heading font-weight-bolder">
        <i class="fa fa-exclamation-triangle text-warning mr-2"></i>PERHATIAN!
    </h5>
    <p class="mb-0">
        Dengan mengajukan revisi, form ini akan dikembalikan ke status <strong>Revision</strong>
        dan perlu melalui proses pembuatan ulang, review, serta approval sebelum dapat dipublikasikan kembali.
        Mohon berikan alasan dengan jelas.
    </p>
</div>

<form id="form-revision-modal">
    <input type="hidden" name="id" id="revision_form_id" value="<?= $form->id; ?>">

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
