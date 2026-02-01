<form id="form-revision">
    <div class="alert bg-light-warning border border-warning" role="alert">
        <h4 class="alert-heading"><strong><i class="fa fa-info-circle text-warning mr-2"></i>WARNING!!!</strong></h4>
        <p>Dengan ini menyatakan bahwa dokumen tersebut harus di revisi. Mohon berikan alasan dengan jelas. Terima Kasih.</p>
    
    </div>
    <div class="form-group mb-3">
        <input type="hidden" name="id" value="<?= $file->id; ?>">
        <textarea name="note" id="note" class="form-control" placeholder="Reason for revision" cols="30" rows="10"></textarea>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <button type="button" class="btn btn-light-primary save-revision"><i class="fa fa-paper-plane"></i>Submit to Rrevision</button>
    </div>
</form>