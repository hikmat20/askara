<div class="alert bg-light-danger border border-danger" role="alert">
    <h4 class="alert-heading"><strong><i class="fa fa-info-circle text-danger mr-2"></i>WARNING!!!</strong></h4>
    <p>Dengan ini menyatakan bahwa dokumen tersebut akan dihapus. Mohon berikan alasan dengan jelas. Terima Kasih.</p>

</div>
<div class="form-group mb-3">
    <input type="hidden" name="id" value="<?= $file->id; ?>">
    <textarea name="note" id="note" class="form-control" placeholder="Reason for deletion" cols="30" rows="10"></textarea>

</div>
<div class="d-flex justify-content-between align-items-center">
    <button type="button" class="btn btn-light-danger save-deletion"><i class="fa fa-paper-plane"></i>Submit to Deletion</button>
    <!-- <button type="button" class="btn btn-light-success" data-dismiss="modal"><i class="fa fa-times"></i>Cancel</button> -->

</div>