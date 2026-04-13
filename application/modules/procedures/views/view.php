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


<!-- Nav tabs -->
<ul class="nav nav-tabs" id="navId">
  <li class="nav-item">
    <a href="#tab1Id" data-toggle="tab" data-target="#tab1Id" class="nav-link active"><i
        class="fa fa-list-alt mr-2"></i> Data Procedure</a>
  </li>
  <li class="nav-item">
    <a href="#tab2Id" data-toggle="tab" data-target="#tab2Id" class="nav-link"><i class="fa fa-history mr-2"></i>
      Revision History</a>
  </li>
  <li class="nav-item">
    <a href="#tab3Id" data-toggle="tab" data-target="#tab3Id" class="nav-link"><i class="fa fa-check-double mr-2"></i>
      Approval</a>
  </li>
  <li class="nav-item">
    <a href="#tab4Id" data-toggle="tab" data-target="#tab4Id" class="nav-link"><i class="fa fa-file-alt mr-2"></i>
      Preview File</a>
  </li>
  <li class="nav-item">
    <a href="#tab5Id" data-toggle="tab" data-target="#tab5Id" class="nav-link"><i class="far fa-file-alt mr-2"></i>
      Logs</a>
  </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane fade show active" id="tab1Id" role="tabpanel">
    <table class="table table-sm table-bordered border-dark">
      <tr>
        <td rowspan="5" width="30%" class="text-center" style="vertical-align: middle;border-right:0px">
          <div class="d-flex justify-content-center align-items-center g-3 gap-3">
            <img width="80" class="img-fluid mr-4"
              src="<?= base_url() . $company->path_logo . $company->id_perusahaan . '/' . $company->logo; ?>" alt="">
            <h3><?= $company->nm_perusahaan; ?></h3>
          </div>
        </td>
        <td rowspan="5" width="40%" class="text-center" style="vertical-align: middle;border-left:0px">
          <h3 class="text-dark font-weight-bolder"><?= $procedure->name; ?></h3>
          <h4 style="color: #0088ffff;">(<?= isset($bilingual->name) ? $bilingual->name : ''; ?>)</h4>
        </td>
        <td width="150">Dept</td>
        <td width=""><?= $procedure->departement_name; ?></td>
      </tr>
      <tr>
        <td>No. Dok</td>
        <td><?= $procedure->nomor; ?></td>
      </tr>
      <tr>
        <td>Revisi</td>
        <td><?= $procedure->revision; ?></td>
      </tr>
      <tr>
        <td>Tgl. Terbit</td>
        <td><?= $procedure->published_at; ?></td>
      </tr>
      <tr>
        <td>Kelompok Proses</td>
        <td><?= $procedure->group_name; ?></td>
      </tr>
    </table>

    <div class="card rounded-10">
      <div class="card-body p-3">
        <table class="table table-borderless rounded-lg mb-6">
          <tr>
            <td class="w-50">
              <h3 class="fw-extra-bold"><strong>TUJUAN</strong></h3>
              <div>
                <?= (isset($procedure->object) ? $procedure->object : ''); ?>
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
            <td class="">
              <h3 class="fw-extra-bold"><strong>RUANG LINGKUP</strong></h3>
              <div>
                <?= (isset($procedure->scope) ? $procedure->scope : ''); ?>
              </div>
            </td>
            <td style="color:#0088ffff" class="">
              <h3 class="fw-extra-bold"><strong>SCOPE</strong></h3>
              <div>
                <?= (isset($bilingual->scope) ? $bilingual->scope : ''); ?>
              </div>
            </td>
          </tr>
          <tr>
            <td class="">
              <h3 class="fw-extra-bold"><strong>TANGGUNG JAWAB</strong></h3>
              <div>
                <?= (isset($procedure->responsibility) ? $procedure->responsibility : ''); ?>
              </div>
            </td>
            <td style="color:#0088ffff" class="">
              <h3 class="fw-extra-bold"><strong>RESPONSIBILITY</strong></h3>
              <div>
                <?= (isset($bilingual->responsibility) ? $bilingual->responsibility : ''); ?>
              </div>
            </td>
          </tr>
          <tr>
            <td class="">
              <h3 class="fw-extra-bold"><strong>DEFINISI</strong></h3>
              <div>
                <?= (isset($procedure->define) ? $procedure->define : ''); ?>
              </div>
            </td>
            <td style="color:#0088ffff" class="">
              <h3 class="fw-extra-bold"><strong>DEFINE</strong></h3>
              <div>
                <?= (isset($bilingual->define) ? $bilingual->define : ''); ?>
              </div>
            </td>
          </tr>
          <tr>
            <td class="">
              <h3 class="fw-extra-bold"><strong>PERFORMA INDIKATOR</strong></h3>
              <div>
                <?= (isset($procedure->performance) ? $procedure->performance : ''); ?>
              </div>
            </td>
            <td style="color:#0088ffff" class="">
              <h3 class="fw-extra-bold"><strong>INDICATOR PERFORMANCE</strong></h3>
              <div>
                <?= (isset($bilingual->performance) ? $bilingual->performance : ''); ?>
              </div>
            </td>
          </tr>
          <tr>
            <td class="">
              <h3 class="fw-extra-bold"><strong>KETENTUAN UMUM</strong></h3>
              <div>
                <?= (isset($procedure->general_requirement) ? $procedure->general_requirement : ''); ?>
              </div>
            </td>
            <td style="color:#0088ffff" class="">
              <h3 class="fw-extra-bold"><strong>GENERAL REQUIREMENT</strong></h3>
              <div>
                <?= (isset($bilingual->general_requirement) ? $bilingual->general_requirement : ''); ?>
              </div>
            </td>
          </tr>
        </table>
        <!-- SIPOCOR -->
        <?php if ($procedure->supplier): ?>
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
                    <?= $procedure->supplier; ?>
                  </div>
                </td>
                <td>
                  <label for="Input" class="font-weight-bold font-size-"><strong>2. Input</strong></label>
                  <div class="">
                    <?= $procedure->input; ?>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="Proses" class="font-weight-bold font-size-"><strong>3. Proses</strong></label>
                  <div class="">
                    <?= $procedure->process; ?>
                  </div>
                </td>
                <td>
                  <label for="Output" class="font-weight-bold font-size-"><strong>4. Output</strong></label>
                  <div class="">
                    <?= $procedure->output; ?>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="Customer" class="font-weight-bold font-size-"><strong>5. Customer</strong></label>
                  <div class="">
                    <?= $procedure->customer; ?>
                  </div>
                </td>
                <td>
                  <label for="Objective" class="font-weight-bold font-size-"><strong>6. Objective</strong></label>
                  <div class="">
                    <?= $procedure->objective; ?>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="Risk" class="font-weight-bold font-size-"><strong>7. Risk</strong></label>
                  <div class="">
                    <?= $procedure->risk; ?>
                  </div>
                </td>
                <td>
                  <label for="mitigation" class="font-weight-bold font-size-"><strong>8. Mitigation</strong></label>
                  <div class="">
                    <?= $procedure->mitigation; ?>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        <?php endif; ?>
        <hr>

        <!-- FLOW IMAGE -->
        <h3>FLOW IMAGE & FILE</h3>
        <?php if ($procedure->image_flow_1 || $procedure->image_flow_2 || $procedure->image_flow_3): ?>
          <div class="d-flex justify-content-start align-items-center">
            <?php if ($procedure->image_flow_1): ?>
              <div class="dropzone-wrapper mr-2 d-flex align-items-center"
                style="width: 200px;height:200px;border:1px solid #eaeaea">
                <div class="dropzone-desc">
                  <?php if ($procedure->image_flow_1): ?>
                    <img src="<?= base_url("directory/FLOW_IMG/$procedure->company_id/$procedure->image_flow_1"); ?>"
                      alt="image_flow_1" class="img-fluid">
                  <?php endif; ?>
                </div>
                <?php if ($procedure->image_flow_1): ?>
                  <div class="middle d-flex justify-content-center align-items-center">
                    <button type="button" class="btn btn-sm mr-1 btn-icon btn-default view-image rounded-circle"><i
                        class="fa fa-search"></i></button>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php if ($procedure->image_flow_2): ?>
              <div class="dropzone-wrapper mr-2 d-flex align-items-center"
                style="width: 200px;height:200px;border:1px solid #eaeaea">
                <div class="dropzone-desc">
                  <?php if ($procedure->image_flow_2): ?>
                    <img src="<?= base_url("directory/FLOW_IMG/$procedure->company_id/$procedure->image_flow_2"); ?>"
                      alt="image_flow_2" class="img-fluid">
                  <?php endif; ?>
                </div>
                <?php if ($procedure->image_flow_2): ?>
                  <div class="middle d-flex justify-content-center align-items-center">
                    <button type="button" class="btn btn-sm mr-1 btn-icon btn-default view-image rounded-circle"><i
                        class="fa fa-search"></i></button>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php if ($procedure->image_flow_3): ?>
              <div class="dropzone-wrapper mr-2 d-flex align-items-center"
                style="width: 200px;height:200px;border:1px solid #eaeaea">
                <div class="dropzone-desc">
                  <?php if ($procedure->image_flow_3): ?>
                    <img src="<?= base_url("directory/FLOW_IMG/$procedure->company_id/$procedure->image_flow_3"); ?>"
                      alt="image_flow_3" class="img-fluid">
                  <?php endif; ?>
                </div>
                <?php if ($procedure->image_flow_3): ?>
                  <div class="middle d-flex justify-content-center align-items-center">
                    <button type="button" class="btn btn-sm mr-1 btn-icon btn-default view-image rounded-circle"><i
                        class="fa fa-search"></i></button>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

          </div>
        <?php endif; ?>
        <?php if (isset($procedure->flow_file)): ?>
          <div class="dropzone-wrapper mr-2 d-flex align-items-center"
            style="width: 200px;height:200px;border:1px solid #eaeaea">
            <div class="dropzone-desc">
              <?php if ($procedure->flow_file): ?>
                <canvas id="pdf-preview" class="" width="150"></canvas>
              <?php endif; ?>
            </div>
            <?php if ($procedure->flow_file): ?>
              <div class="middle d-flex justify-content-center align-items-center">
                <a target="_blank"
                  href="<?= base_url("directory/FLOW_FILE/$procedure->company_id/$procedure->flow_file"); ?>"
                  class="btn btn-sm mr-1 btn-icon btn-default rounded-circle"><i class="fa fa-eye"></i></a>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <hr>

        <!-- VIDEO -->
        <h3>VIDEO</h3>
        <?php if ($procedure->link_video): ?>
          <?= ($procedure->link_video); ?>
        <?php else: ?>
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
            <?php if ($detail):
              foreach ($detail as $dtl): ?>
                <tr>
                  <td class="text-center"><?= $dtl->number; ?></td>
                  <td class="text-center"><?= $dtl->pic; ?></td>
                  <td class="wd-25"><?= $dtl->description; ?></td>
                  <td class="wd-25" style="color:#0088ffff"><?= $dtl->description_2; ?></td>
                  <td class="">
                    <?php $relDocs = json_decode($dtl->relate_doc); ?>
                    <?php if (is_array($relDocs)): ?>
                      <?php foreach ($relDocs as $relDoc) { ?>
                        <?php if (isset($forms[$relDoc])): ?>
                          <span class="badge btn bg-success btn-success view-form mb-1"
                            data-id="<?= $relDoc; ?>"><?= $forms[$relDoc]; ?></span>
                        <?php endif; ?>
                      <?php } ?>
                    <?php endif; ?>

                    <?php $relIk = json_decode($dtl->relate_ik_doc); ?>
                    <?php if (is_array($relIk)): ?>
                      <?php foreach ($relIk as $ik) { ?>
                        <?php if (isset($work_instructions[$ik])): ?>
                          <span class="badge btn bg-danger btn-danger view-guide mb-1"
                            data-id="<?= $ik; ?>"><?= $work_instructions[$ik]; ?></span>
                        <?php endif; ?>
                      <?php } ?>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach;
            else: ?>
              <tr>
                <td colspan="5" class="text-center">~ Not available data ~</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="tab2Id" role="tabpanel">
    <div class="card mb-3">
      <div class="card-body p-2">
        <br>
        <h4>RIWAYAT DOKUMEN</h4>
        <table class="table table-sm">
          <thead>
            <tr class="bg-secondary">
              <th class="py-2" width="100">REVISI</th>
              <th class="py-2" width="150">TANGGAL REVISI</th>
              <th class="py-2">REVISI OLEH</th>
              <th class="py-2">URAIAN PERUBAHAN</th>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($revision_logs) && count($revision_logs) > 0):
              foreach ($revision_logs as $revisionLog): ?>
                <tr>
                  <td class="text-center"><?= $revisionLog->revision_number; ?></td>
                  <td class="text-center"><?= $revisionLog->revision_date; ?></td>
                  <td class=""><?= $users[$revisionLog->created_by]; ?></td>
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
    </div>
  </div>
  <div class="tab-pane fade" id="tab3Id" role="tabpanel">
    <br>
    <h4>DATA APPROVAL</h4>
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
          <td class="text-center"><?= ($procedure->prepare_name) ?: '~'; ?></td>
          <td class="text-center"><?= ($procedure->reviewer_name) ?: '~'; ?></td>
          <td class="text-center"><?= ($procedure->approval_name) ?: '~'; ?></td>
        </tr>
        <!-- <tr>
          <td class="text-center" style="vertical-align: middle;padding:10px">
            QR
          </td>
          <td class="text-center" style="vertical-align: middle;padding:10px">
            QR
          </td>
          <td class="text-center" style="vertical-align: middle;padding:10px">
            QR
          </td>
        </tr> -->
        <tr class="text-center bg-secondary">
          <th class="text-center" style="vertical-align: middle;"><?= ($procedure->user_prepared_name) ?: '~'; ?></th>
          <th class="text-center" style="vertical-align: middle;"><?= ($procedure->user_reviewed_name) ?: '~'; ?></th>
          <th class="text-center" style="vertical-align: middle;"><?= ($procedure->user_approved_name) ?: '~'; ?></th>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="tab-pane fade" id="tab4Id" role="tabpanel">
    <iframe
      src="<?= base_url($this->uri->segment(1) . '/printfile/' . $procedure->id); ?>#toolbar=0&navpanes=0&scrollbar=0"
      frameborder="0" style="width: 100%;height:70vh"></iframe>
  </div>
  <div class="tab-pane fade" id="tab5Id" role="tabpanel">
    <br>
    <div class="row overflow-auto">
      <div class="col-md-1"></div>
      <div class="col-md-8">
        <!-- <label for="">Tracking File</label> -->
        <div class="timeline timeline-5">
          <div class="timeline-items">
            <div class="timeline-item">
              <!-- <div class="timeline-media bg-light-primary">
                <i class="fa fa-upload text-success"></i>
              </div>
              <div class="timeline-desc timeline-desc-light-primary">
                <span class="font-weight-bolder text-primary"> <?= date('Y-m-d'); ?> 09:30 AM</span>
                <span class="label label-pill label-inline label-light-danger">Upload File</span>
                <p class="font-weight-normal text-dark-50 pb-2">
                  To start a blog, think of a topic about and first brainstorm ways to write details
                </p>
              </div> -->
            </div>
            <?php if (isset($logs)):
              foreach ($logs as $log): ?>
                <div class="timeline-item">
                  <div class="timeline-media <?= ($log->new_status == 'OPN') ? 'bg-light-success' : 'bg-light-primary'; ?>">
                    <span
                      class="<?= ($log->new_status == 'OPN') ? 'fa fa-upload text-success' : 'fa fa-circle text-primary'; ?>"></span>
                  </div>

                  <div class="timeline-desc timeline-desc-light-primary">
                    <p class="font-weight-bolder text-primary mb-0"> <?= $log->note; ?></p>
                    <!-- <div class="card card-stretch d-inline-block border-left border-y-0 border-right-0 my-1 bg-light border-primary border-3">
                      <div class="card-body p-2"></div>
                    </div> -->
                    <p class="font-weight-normal text-dark-50 pt-1">
                      <span class=" text-muted">Updated at <strong><?= $log->updated_at; ?></strong> by
                        <strong><?= $log->full_name; ?></strong></span>
                    </p>
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

<script>
  $(document).ready(function () {
    let id = '<?= $procedure->id; ?>'
    $.getJSON(siteurl + active_controller + 'load_file_flow/' + id, function (result) {
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
    }).then(function (pdf_doc) {
      _PDF_DOC = pdf_doc;

      // Show the first page
      showPage(1);

      // destroy previous object url
      URL.revokeObjectURL(_OBJECT_URL);
    }).catch(function (error) {
      // trigger Cancel on error
      $("#cancel-pdf").click();
      // alert(error.message);
    });;
  }

  function showPage(page_no) {
    var _CANVAS = document.querySelector('#pdf-preview');
    // fetch the page
    _PDF_DOC.getPage(page_no).then(function (page) {
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
      page.render(renderContext).then(function () {
        $("#pdf-preview").css('display', 'inline-block');
        $("#pdf-loader").css('display', 'none');
      });
    });
  }
</script>