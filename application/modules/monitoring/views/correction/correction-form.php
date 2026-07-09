<div class="modal-header py-2 px-2">
    <ul class="nav nav-pills nav-light-success py-0" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#filetab">
                <span class="nav-icon">
                    <i class="fa fa-file-alt"></i>
                </span>
                <span class="nav-text">File</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#history">
                <span class="nav-icon">
                    <i class="fa fa-history"></i>
                </span>
                <span class="nav-text">History</span>
            </a>
        </li>
    </ul>
</div>
<div class="tab-content mt-5">
    <div class="tab-pane fade show active" id="filetab" role="tabpanel" aria-labelledby="file-">
        <!-- <iframe class="w-100" style="height: 450px;" src="<?= base_url("directory/$dir_name/$file->file_name"); ?>#toolbar=0&navpanes=0" frameborder="1"></iframe> -->
        <!-- <hr> -->
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form id="form-review">
                    <input type="hidden" name="id" id="id" value="<?= $file->id; ?>">
                    <div class="form-group row">
                        <label class="col-form-label font-weight-bold">Description</label>
                        <input type="text" name="description" id="description" class="form-control required" value="<?= $file->name; ?>">
                        <span class="invalid-feedback text-danger">Harus di isi</span>
                        <input type="hidden" name="reviewer_id" id="reviewer_id" class="form-control" value="<?= $file->reviewer_id; ?>">
                        <input type="hidden" name="approval_id" id="approval_id" class="form-control" value="<?= $file->approval_id; ?>">
                        <input type="hidden" name="status" id="status" class="form-control" value="REV">
                        <select hidden name="distribute_id[]" multiple id="distribute_id" data-placeholder="Choose an options" class="">;
                            <option value=""></option>
                            <?php $this->load->view('partials/activity_log_ui', ['history' => $jabatan, 'sts' => isset($sts) ? $sts : []]); ?>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label font-weight-bold">Upload File</label>
                        <input type="file" name="image" id="file" class="form-control" value="">
                        <span class="invalid-feedback text-danger">Harus di isi</span>
                    </div>
                    <div class="form-group row">
                        <textarea name="note" id="note" class="form-control" placeholder="Reason"></textarea>
                        <span class="invalid-feedback text-danger">Harus di isi</span>
                    </div>
                    <button type="button" class="btn btn-light-primary" id="upload-file"><i class="fab fa-telegram-plane"></i>Submit Review</button>
            </div>
            </form>
        </div>
    </div>

    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="home-tab">
        <div class="row overflow-auto">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <label for="">Tracking File</label>
                <div class="timeline timeline-5">
                    <div class="timeline-items">
                        <!-- <div class="timeline-item">
                            <div class="timeline-media bg-light-primary">
                                <i class="fa fa-upload text-success"></i>
                            </div>
                            <div class="timeline-desc timeline-desc-light-primary">
                                <span class="font-weight-bolder text-primary"> <?= date('Y-m-d'); ?> 09:30 AM</span>
                                <span class="label label-pill label-inline label-light-danger">Upload File</span>
                                <p class="font-weight-normal text-dark-50 pb-2">
                                    To start a blog, think of a topic about and first brainstorm ways to write details
                                </p>
                            </div>
                        </div> -->
                        <?php $this->load->view('partials/activity_log_ui', ['history' => $history, 'sts' => isset($sts) ? $sts : []]); ?>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>