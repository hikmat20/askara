<div class="modal-body">
    <ul class="nav nav-pills nav-light-success py-0" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#file">
                <span class="nav-icon">
                    <i class="fa fa-file-alt"></i>
                </span>
                <span class="nav-text">File</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#home">
                <span class="nav-icon">
                    <i class="fa fa-history"></i>
                </span>
                <span class="nav-text">History</span>
            </a>
        </li>
    </ul>
    <div class="tab-content mt-5">
        <div class="tab-pane position-relative fade show active" id="file" role="tabpanel" aria-labelledby="file-tab">
            <div style="width:92%;min-height:100%;background-color: red;position: absolute;opacity: 0;"></div>
            <?php if (isset($file->link_form) && $file->link_form) : ?>
                <iframe src="<?= $file->link_form ?>#toolbar=0&navpanes=0" frameborder="0" width="100%" height="400px"></iframe>
            <?php else : ?>
                <?php if ($file->status == 'DEL') : ?>
                    <h4>404 Not Found!</h4>
                    <p>File hes been deleted!</p>
                <?php else : ?>
                    <?php if ($file->file_name) : ?>
                        <?php if ($file->ext == '.pdf' || $file->ext == '.PDF') :
                            if ($type == 'form') {
                                $dir = 'FORMS';
                            } else if ($type == 'guide') {
                                $dir = 'GUIDES';
                            } else if ($type == 'record') {
                                $dir = 'RECORDS';
                            }
                        ?>
                            <iframe src="<?= base_url("directory/$dir/$file->company_id/$file->file_name"); ?>#toolbar=0&navpanes=0" frameborder="0" width="100%" height="400px"></iframe>
                        <?php else : ?>
                            <iframe src="https://docs.google.com/gview?embedded=true&url=<?= base_url("directory/$dir/$file->company_id/$file->file_name"); ?>&rm=minimal#toolbar=0&navpanes=0" frameborder="0" width="100%" height="400px"></iframe>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
            <hr>
        </div>
        <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
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
    <div class="justify-content-end d-flex">
        <button type="button" class="btn btn-danger" onclick="$('#modelId').modal('hide');setTimeout(function(){$('#content_modal').html('')},500)"><i class="fa fa-times"></i>Close</button>
    </div>
</div>