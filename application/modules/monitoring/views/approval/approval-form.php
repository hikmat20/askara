<ul class="nav nav-pills nav-light-success py-0" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#apv">
            <span class="nav-icon">
                <i class="fa fa-file-alt"></i>
            </span>
            <span class="nav-text">Approval</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#filetab">
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
<div class="tab-content mt-5">
    <div class="tab-pane fade show active p-4" id="apv" role="tabpanel" aria-labelledby="file-">
        <form id="form-approval">
            <input type="hidden" name="id" id="id" value="<?= $file->id; ?>">
            <div class="form-group pb-3">
                <div class="checkbox-inline">
                    <label class="status checkbox checkbox-outline checkbox-outline-2x checkbox-primary">
                        <input type="checkbox" class="status" name="status" value="PUB" />
                        <span></span>
                        <h5>Setujui & Publikasikan</h5>
                    </label>
                    <span class="pl-8 invalid-feedback text-danger">Ceklist terlebuh dahulu.</span>
                    <span class="ml-8 pl-3 border-left border-5 border-success font-italic">Menyatakan bahwa dokumen ini telah melalui proses peninjauan dan disetujui, sehingga dapat dipublikasikan dan diberlakukan secara resmi.</span>
                </div>
                <br>
                <br>
                <div class="pl-8">
                    <strong>Published Date</strong>
                    <input type="date" name="published_date" min="<?= date('Y-m-d', strtotime(date('Y-m-d') . '-7 days')); ?>" id="published_date" class="form-control" placeholder="Published Date">
                    <span class="invalid-feedback text-danger">Harus di isi</span>
                </div>
            </div>
            <hr>
            <div class="form-group pb-5">
                <div class="checkbox-inline">
                    <label class="status checkbox checkbox-outline checkbox-outline-2x checkbox-danger">
                        <input type="checkbox" class="status" name="status" value="COR" />
                        <span></span>
                        <h5>Perlu Perbaikan</h5>
                    </label>
                    <span class="pl-8 invalid-feedback text-danger">Ceklist terlebuh dahulu.</span>
                    <span class="ml-8 pl-3 border-left border-5 border-danger font-italic">Menyatakan bahwa dokumen ini belum dapat disetujui dan memerlukan perbaikan serta koreksi sesuai dengan catatan dan alasan yang disampaikan.</span>
                </div>
            </div>

            <div class="form-group">
                <textarea name="note" disabled rows="5" id="note" class="form-control" placeholder="Note"></textarea>
                <span class="invalid-feedback text-danger">Harus di isi</span>
            </div>
            <button type="button" class="btn btn-light-info" id="save-approval"><i class="fab fa-telegram-plane"></i>Submit</button>
        </form>
    </div>
    <div class="tab-pane fade" id="filetab" role="tabpanel" aria-labelledby="file-">
        <?php if ($type == 'procedures') : ?>
            <iframe class="w-100" style="height: 70vh;" src="<?= base_url("procedures/printfile/" . $file->id); ?>#toolbar=0&navpanes=0"></iframe>
        <?php endif; ?>
    </div>

    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="home-tab">
        <div class="row">
            <div class="col-md-8 offset-md-1">
                <label for="">Tracking File</label>
                <div class="timeline timeline-5">
                    <div class="timeline-items">
                        <?php if (isset($history)) :
                            foreach ($history as $his) : ?>
                                <div class="timeline-item">
                                    <div class="timeline-media <?= ($his->new_status == 'OPN') ? 'bg-light-success' : 'bg-light-danger'; ?>">
                                        <span class="<?= ($his->new_status == 'OPN') ? 'fa fa-upload text-success' : 'fa fa-circle text-danger'; ?>"></span>
                                    </div>

                                    <div class="timeline-desc timeline-desc-light-danger mb-5">
                                        <span class="font-weight-bolder text-danger"> <?= $his->updated_at; ?></span>
                                        <p>Status : <?= $sts[$his->new_status]; ?></p>
                                        <p>Processed by : <strong class="text-dark"><?= $his->full_name; ?></strong></p>
                                        <p>Note : <?= $his->note; ?></p>
                                    </div>
                                </div>
                        <?php endforeach;
                        endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>