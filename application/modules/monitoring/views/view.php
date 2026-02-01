<ul class="nav nav-pills nav-sm nav-light-success" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link nav-sm active" data-toggle="tab" href="#data">
            <span class="nav-icon">
                <i class="fa fa-list"></i>
            </span>
            <span class="nav-text">Data Procedure</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link nav-sm" data-toggle="tab" href="#revision">
            <span class="nav-icon">
                <i class="fa fa-history"></i>
            </span>
            <span class="nav-text">History Revision</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link nav-sm" data-toggle="tab" href="#approval">
            <span class="nav-icon">
                <i class="fa fa-list-alt"></i>
            </span>
            <span class="nav-text">Data Approval</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link nav-sm" data-toggle="tab" href="#file">
            <span class="nav-icon">
                <i class="fa fa-file-alt"></i>
            </span>
            <span class="nav-text">Preview File</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link nav-sm" data-toggle="tab" href="#log">
            <span class="nav-icon">
                <i class="fa fa-history"></i>
            </span>
            <span class="nav-text">Activity Log</span>
        </a>
    </li>
</ul>
<div class="tab-content mt-5">
    <div class="tab-pane fade show active" id="data" role="tabpanel" aria-labelledby="file-tab">
        <table class="table table-sm table-bordered border-dark">
            <tr>
                <td rowspan="5" width="30%" class="text-center" style="vertical-align: middle;border-right:0px">
                    <div class="d-flex justify-content-center align-items-center g-3 gap-3">
                        <img width="80" class="img-fluid mr-4" src="<?= base_url() . $company->path_logo . $company->id_perusahaan . '/' . $company->logo; ?>" alt="">
                        <h2><?= $company->nm_perusahaan; ?></h2>
                    </div>
                </td>
                <td rowspan="5" width="40%" class="text-center" style="vertical-align: middle;border-left:0px">
                    <h2><?= $data->name; ?></h2>
                    <h3 style="color: #0088ffff;">(<?= isset($bilingual->name) ? $bilingual->name : ''; ?>)</h3>
                </td>
                <td width="150">Dept</td>
                <td width=""><?= $data->departement_name; ?></td>
            </tr>
            <tr>
                <td>No. Dok</td>
                <td><?= $data->nomor; ?></td>
            </tr>
            <tr>
                <td>Revisi</td>
                <td><?= $data->revision; ?></td>
            </tr>
            <tr>
                <td>Tgl. Terbit</td>
                <td><?= $data->published_at; ?></td>
            </tr>
            <tr>
                <td>Kelompok Proses</td>
                <td><?= $data->group_name; ?></td>
            </tr>
        </table>
        <div class="card rounded-10">
            <div class="card-body p-3">
                <table class="table table-borderless rounded-lg mb-6">
                    <tr>
                        <td class="py-6 w-50">
                            <h3 class="fw-extra-bold"><strong>TUJUAN</strong></h3>
                            <div>
                                <?= (isset($data->object) ? $data->object : ''); ?>
                            </div>
                        </td>
                        <td style="color:#0088ffff">
                            <h3 class="fw-extra-bold"><strong>OBJECT</strong></h3>
                            <div>
                                <?= (isset($bilingual->object) ? $bilingual->object : ''); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-6">
                            <h3 class="fw-extra-bold"><strong>RUANG LINGKUP</strong></h3>
                            <div>
                                <?= (isset($data->scope) ? $data->scope : ''); ?>
                            </div>
                        </td>
                        <td style="color:#0088ffff" class="py-6">
                            <h3 class="fw-extra-bold"><strong>SCOPE</strong></h3>
                            <div>
                                <?= (isset($bilingual->scope) ? $bilingual->scope : ''); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-6">
                            <h3 class="fw-extra-bold"><strong>TANGGUNG JAWAB</strong></h3>
                            <div>
                                <?= (isset($data->responsibility) ? $data->responsibility : ''); ?>
                            </div>
                        </td>
                        <td style="color:#0088ffff" class="py-6">
                            <h3 class="fw-extra-bold"><strong>RESPONSIBILITY</strong></h3>
                            <div>
                                <?= (isset($bilingual->responsibility) ? $bilingual->responsibility : ''); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-6">
                            <h3 class="fw-extra-bold"><strong>DEFINISI</strong></h3>
                            <div>
                                <?= (isset($data->define) ? $data->define : ''); ?>
                            </div>
                        </td>
                        <td style="color:#0088ffff" class="py-6">
                            <h3 class="fw-extra-bold"><strong>DEFINE</strong></h3>
                            <div>
                                <?= (isset($bilingual->define) ? $bilingual->define : ''); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-6">
                            <h3 class="fw-extra-bold"><strong>PERFORMA INDIKATOR</strong></h3>
                            <div>
                                <?= (isset($data->performance) ? $data->performance : ''); ?>
                            </div>
                        </td>
                        <td style="color:#0088ffff" class="py-6">
                            <h3 class="fw-extra-bold"><strong>INDICATOR PERFORMANCE</strong></h3>
                            <div>
                                <?= (isset($bilingual->performance) ? $bilingual->performance : ''); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-6">
                            <h3 class="fw-extra-bold"><strong>KETENTUAN UMUM</strong></h3>
                            <div>
                                <?= (isset($data->general_requirement) ? $data->general_requirement : ''); ?>
                            </div>
                        </td>
                        <td style="color:#0088ffff" class="py-6">
                            <h3 class="fw-extra-bold"><strong>GENERAL REQUIREMENT</strong></h3>
                            <div>
                                <?= (isset($bilingual->general_requirement) ? $bilingual->general_requirement : ''); ?>
                            </div>
                        </td>
                    </tr>
                </table>
                <!-- SIPOCOR -->
                <?php if ($data->supplier): ?>
                    <table class="table table-bordered mb-6">
                        <thead>
                            <tr class="table-secondary">
                                <th colspan="2">
                                    <h3>SIPOCOR</h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="50%">
                                    <label for="Supplier" class="font-weight-bold font-size-"><strong>Supplier</strong></label>
                                    <div class="">
                                        <?= $data->supplier; ?>
                                    </div>
                                </td>
                                <td>
                                    <label for="Input" class="font-weight-bold font-size-"><strong>2. Input</strong></label>
                                    <div class="">
                                        <?= $data->input; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="Proses" class="font-weight-bold font-size-"><strong>3. Proses</strong></label>
                                    <div class="">
                                        <?= $data->process; ?>
                                    </div>
                                </td>
                                <td>
                                    <label for="Output" class="font-weight-bold font-size-"><strong>4. Output</strong></label>
                                    <div class="">
                                        <?= $data->output; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="Customer" class="font-weight-bold font-size-"><strong>5. Customer</strong></label>
                                    <div class="">
                                        <?= $data->customer; ?>
                                    </div>
                                </td>
                                <td>
                                    <label for="Objective" class="font-weight-bold font-size-"><strong>6. Objective</strong></label>
                                    <div class="">
                                        <?= $data->objective; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="Risk" class="font-weight-bold font-size-"><strong>7. Risk</strong></label>
                                    <div class="">
                                        <?= $data->risk; ?>
                                    </div>
                                </td>
                                <td>
                                    <label for="mitigation" class="font-weight-bold font-size-"><strong>8. Mitigation</strong></label>
                                    <div class="">
                                        <?= $data->mitigation; ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
                <hr>

                <!-- FLOW IMAGE -->
                <h3>FLOW IMAGE & FILE</h3>
                <?php if ($data->image_flow_1 || $data->image_flow_2 || $data->image_flow_3) : ?>
                    <div class="d-flex justify-content-start align-items-center">
                        <?php if ($data->image_flow_1) : ?>
                            <div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 200px;height:200px;border:1px solid #eaeaea">
                                <div class="dropzone-desc">
                                    <?php if ($data->image_flow_1) : ?>
                                        <img src="<?= base_url("directory/FLOW_IMG/$data->company_id/$data->image_flow_1"); ?>" alt="image_flow_1" class="img-fluid">
                                    <?php endif; ?>
                                </div>
                                <?php if ($data->image_flow_1) : ?>
                                    <div class="middle d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-sm mr-1 btn-icon btn-default view-image rounded-circle"><i class="fa fa-search"></i></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($data->image_flow_2) : ?>
                            <div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 200px;height:200px;border:1px solid #eaeaea">
                                <div class="dropzone-desc">
                                    <?php if ($data->image_flow_2) : ?>
                                        <img src="<?= base_url("directory/FLOW_IMG/$data->company_id/$data->image_flow_2"); ?>" alt="image_flow_2" class="img-fluid">
                                    <?php endif; ?>
                                </div>
                                <?php if ($data->image_flow_2) : ?>
                                    <div class="middle d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-sm mr-1 btn-icon btn-default view-image rounded-circle"><i class="fa fa-search"></i></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($data->image_flow_3) : ?>
                            <div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 200px;height:200px;border:1px solid #eaeaea">
                                <div class="dropzone-desc">
                                    <?php if ($data->image_flow_3) : ?>
                                        <img src="<?= base_url("directory/FLOW_IMG/$data->company_id/$data->image_flow_3"); ?>" alt="image_flow_3" class="img-fluid">
                                    <?php endif; ?>
                                </div>
                                <?php if ($data->image_flow_3) : ?>
                                    <div class="middle d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-sm mr-1 btn-icon btn-default view-image rounded-circle"><i class="fa fa-search"></i></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
                <?php if ($data->flow_file): ?>
                    <div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 200px;height:200px;border:1px solid #eaeaea">
                        <div class="dropzone-desc">
                            <?php if ($data->flow_file) : ?>
                                <canvas id="pdf-preview" class="" width="150"></canvas>
                            <?php endif; ?>
                        </div>
                        <?php if ($data->flow_file) : ?>
                            <div class="middle d-flex justify-content-center align-items-center">
                                <a target="_blank" href="<?= base_url("directory/FLOW_FILE/$data->company_id/$data->flow_file"); ?>" class="btn btn-sm mr-1 btn-icon btn-default rounded-circle"><i class="fa fa-eye"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <hr>

                <!-- VIDEO -->
                <h3>VIDEO</h3>
                <?php if ($data->link_video) : ?>
                    <?= ($data->link_video); ?>
                <?php else : ?>
                    <span>~</span>
                <?php endif; ?>

                <hr>
                <!-- FLOW DETAIL -->
                <h3>DETAIL PROSES</h3>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr class="table-secondary">
                            <th class="py-1 text-center">No.</th>
                            <th class="py-1 text-center">PIC/TANGGUNG JAWAB</th>
                            <th class="py-1 text-center" colspan="2">DESKRIPSI</th>
                            <th class="py-1 text-center">DOKUMEN TERKAIT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($detail) :
                            foreach ($detail as $dtl) : ?>
                                <tr>
                                    <td class="text-center"><?= $dtl->number; ?></td>
                                    <td class="text-center"><?= $dtl->pic; ?></td>
                                    <td class="wd-25"><?= $dtl->description; ?></td>
                                    <td class="wd-25" style="color:#0088ffff"><?= $dtl->description_2; ?></td>
                                    <td class="">
                                        <?php $relDocs = json_decode($dtl->relate_doc); ?>
                                        <?php if (is_array($relDocs)) : ?>
                                            <?php foreach ($relDocs as $relDoc) { ?>
                                                <span class="d-block badge btn <?= ($ArrForms[$relDoc]->status == 'DEL') ? 'btn-light' : 'bg-success btn-success'; ?>  view-form mb-1" data-id="<?= $relDoc; ?>"><?= $ArrForms[$relDoc]->name; ?> <?= ($ArrForms[$relDoc]->status == 'DEL') ? '<i class="fa fa-exclamation-circle text-danger" title="File has been deleted!"></i>' : ''; ?></span>
                                            <?php } ?>
                                        <?php endif; ?>

                                        <?php $relIk = json_decode($dtl->relate_ik_doc); ?>
                                        <?php if (is_array($relIk)) : ?>
                                            <?php foreach ($relIk as $ik) { ?>
                                                <span class="d-block badge btn <?= ($ArrGuides[$ik]->status == 'DEL') ? 'btn-light' : 'bg-danger btn-danger'; ?> view-guide mb-1" data-id="<?= $ik; ?>"><?= $ArrGuides[$ik]->name; ?> <?= ($ArrGuides[$ik]->status == 'DEL') ? '<i class="fa fa-exclamation-circle text-danger"  title="File has been deleted!"></i>' : ''; ?></span>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else : ?>
                            <tr>
                                <td colspan="4" class="text-center">~ Not available data ~</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="revision" role="tabpanel" aria-labelledby="file-tab">
        <h4 class="mb-3">RIWAYAT DOKUMEN</h4>
        <table class="table table-sm">
            <thead>
                <tr class="bg-secondary">
                    <th class="py-2" width="100">REVISI</th>
                    <th class="py-2" width="150">TANGGAL REVISI</th>
                    <th class="py-2">URAIAN PERUBAHAN</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($revision_logs) && count($revision_logs) > 0): foreach ($revision_logs as $revisionLog): ?>
                        <tr>
                            <td class="text-center"><?= $revisionLog->revision_number; ?></td>
                            <td class="text-center"><?= $revisionLog->revision_date; ?></td>
                            <td><?= $revisionLog->description; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="text-center">~</td>
                        <td class="text-center">~</td>
                        <td>~</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="tab-pane fade" id="approval" role="tabpanel" aria-labelledby="file-tab">
        <h4 class="mb-3">DATA APPROVAL</h4>
        <table class="table table-sm table-bordered">
            <thead>
                <tr class="bg-secondary">
                    <th class="py-2 text-center">DIBUAT</th>
                    <th class="py-2 text-center">DIPERIKSA</th>
                    <th class="py-2 text-center">DISETUJUI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center"><?= ($data->prepare_name) ?: '~'; ?></td>
                    <td class="text-center"><?= ($data->reviewer_name) ?: '~'; ?></td>
                    <td class="text-center"><?= ($data->approval_name) ?: '~'; ?></td>
                </tr>
                <tr>
                    <td class="text-center" style="vertical-align: middle;padding:10px">
                        <?php if (isset($ArrSign['create'])): ?>
                            <img src="<?= base_url($ArrSign['create']); ?>" width="100" alt="Create QR Sign">
                        <?php endif; ?>
                    </td>
                    <td class="text-center" style="vertical-align: middle;padding:10px">
                        <?php if (isset($ArrSign['review'])): ?>
                            <img src="<?= base_url($ArrSign['review']); ?>" width="100" alt="Review QR Sign">
                        <?php endif; ?>
                    </td>
                    <td class="text-center" style="vertical-align: middle;padding:10px">
                        <?php if (isset($ArrSign['approve'])): ?>
                            <img src="<?= base_url($ArrSign['approve']); ?>" width="100" alt="Approve QR Sign">
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="text-center bg-secondary">
                    <th class="text-center" style="vertical-align: middle;"><?= ($data->user_prepared_name) ?: '~'; ?></th>
                    <th class="text-center" style="vertical-align: middle;"><?= ($data->user_reviewed_name) ?: '~'; ?></th>
                    <th class="text-center" style="vertical-align: middle;"><?= ($data->user_approved_name) ?: '~'; ?></th>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="tab-pane fade" id="file" role="tabpanel" aria-labelledby="file-tab">
        <iframe class="w-100" style="height: 70vh;" src="<?= base_url("directory/PROCEDURES/2026/02/" . $file->nomor . '-FINAL.pdf'); ?>#toolbar=0&navpanes=0" frameborder="1"></iframe>
        <?php if ($view_data == false) : ?>
            <!-- <button type="button" class="btn btn-default revision" data-id="<?= $file->id; ?>" data-type="procedure"><i class="fa fa-info-circle text-"></i>Submit this document for Revision</button>
            <button type="button" class="btn btn-light-danger deletion" data-id="<?= $file->id; ?>" data-type="procedure"><i class="fa fa-info-circle text-"></i>Submit this document for Deletion</button> -->
        <?php endif; ?>
    </div>

    <div class="tab-pane fade" id="log" role="tabpanel" aria-labelledby="log-tab">
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

<style>
    ol,
    ul {
        padding-left: 20px;
        margin-left: 0;
    }

    ol ol,
    ul ul {
        padding-left: 20px;
    }

    ol ol ol,
    ul ul ul {
        padding-left: 20px;
    }
</style>

<script>
    $(document).ready(function() {
        let id = '<?= $data->id; ?>'
        $.getJSON(siteurl + active_controller + 'load_file_flow/' + id, function(result) {
            var data = result.data
            var d = ''
            const url = siteurl + 'directory/FLOW_FILE/' + data.company_id + '/' + data.flow_file;
            if (!data.flow_file) {
                $("#pdf-preview").css('display', 'none');
            }
            if (data.flow_file) {
                fetch(url)
                    .then((res) => res.blob())
                    .then((myBlob) => {
                        // console.log(myBlob);
                        // logs: Blob { size: 1024, type: "image/jpeg" }
                        myBlob.name = data.flow_file;
                        myBlob.lastModified = new Date();
                        // console.log(myBlob instanceof File);
                        // logs: false
                        _OBJECT_URL = URL.createObjectURL(myBlob)
                        // console.log(_OBJECT_URL);
                        showPDF(_OBJECT_URL);
                    });
            }
        });
    })

    var _PDF_DOC,
        _CANVAS = document.querySelector('#pdf-preview'),
        _OBJECT_URL;

    function showPDF(pdf_url) {

        PDFJS.getDocument({
            url: pdf_url
        }).then(function(pdf_doc) {
            _PDF_DOC = pdf_doc;

            // Show the first page
            showPage(1);

            // destroy previous object url
            URL.revokeObjectURL(_OBJECT_URL);
        }).catch(function(error) {
            // trigger Cancel on error
            $("#cancel-pdf").click();
            // alert(error.message);
        });;
    }

    function showPage(page_no) {
        var _CANVAS = document.querySelector('#pdf-preview');
        // fetch the page
        _PDF_DOC.getPage(page_no).then(function(page) {
            // set the scale of viewport
            var scale_required = _CANVAS.width / page.getViewport(1).width;

            // get viewport of the page at required scale
            var viewport = page.getViewport(scale_required);

            // set canvas height
            _CANVAS.height = viewport.height;

            var renderContext = {
                canvasContext: _CANVAS.getContext('2d'),
                viewport: viewport
            };

            // render the page contents in the canvas
            page.render(renderContext).then(function() {
                $("#pdf-preview").css('display', 'inline-block');
                $("#pdf-loader").css('display', 'none');
            });
        });
    }
</script>