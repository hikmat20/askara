<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="d-flex flex-column-fluid">
    <div class="container">
      <div class="card card-stretch shadow card-custom">
        <div class="card-header">
          <h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
          <div class="mt-4 float-right ">
            <button type="button" onclick="history.go(-1)" class="btn btn-danger"><i class="fa fa-reply"></i>Back</button>
          </div>
        </div>
        <div class="card-body">
          <form id="form-audit">
            <?php if (isset($audit) && $audit) : ?>
              <input type="hidden" name="id" value="<?= $audit->id; ?>">
            <?php endif; ?>
            <input type="hidden" name="checklist_id" value="<?= $cklst->id; ?>">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-2 row">
                  <label for="" class="d-none" id="procedure_id"><?= $cklst->procedure_id; ?></label>
                  <label for="" class="col-md-4 h6 font-weight-bold">Procedure</label>
                  <label for="" class="col h6">: <?= isset($cklst->name) ? $cklst->name : ''; ?></label>
                </div>
                <div class="mb-2 row">
                  <label for="" class="col-md-4 h6 font-weight-bold">Date</label>
                  <label for="" class="col h6">
                    <input type="date" class="form-control required w-md-200px" name="date" value="<?= (isset($audit) && $audit) ? $audit->date : ''; ?>" id="date">
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-2 row">
                  <label for="" class="col-md-4 h6 font-weight-bold">Auditor</label>
                  <div for="" class="col h6">
                    <select name="auditor[]" class="form-control select2" data-placeholder="Select Auditor" data-allow-clear="true" data-width="100%" multiple id="auditor">
                      <option value=""></option>
                      <?php if ($users) foreach ($users as $a) : ?>
                        <option value="<?= $a->id_user; ?>" <?= isset($audit) ? (in_array($a->id_user, json_decode($audit->auditor)) ? 'selected' : '') : ''; ?>><?= $a->full_name; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="mb-2 row">
                  <label for="" class="col-md-4 h6 font-weight-bold">Auditee</label>
                  <div for="" class="col h6">
                    <select name="auditee[]" class="form-control select2" data-placeholder="Select Auditee" data-allow-clear="true" data-width="100%" multiple id="auditee">
                      <?php if ($users) foreach ($users as $a) : ?>
                        <option value="<?= $a->id_user; ?>" <?= isset($audit) ? (in_array($a->id_user, json_decode($audit->auditee)) ? 'selected' : '') : ''; ?>><?= $a->full_name; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <hr>
            <div class="load_data mb-10">
              <div class="accordion" id="accordionExample">
                <div class="card">
                  <div class="card-header" id="headingOne">
                    <h4 class="mb-0 p-5" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                      List Standard
                    </h4>
                  </div>

                  <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                      <?php if ($ArrStd) : ?>
                        <?php foreach ($ArrStd as $std) : ?>

                          <h3>Standard : <?= $std->name; ?></h3>
                          <table class="table table-bordered table-striped">
                            <thead>
                              <tr>
                                <th>No</th>
                                <th width="100">Pasal</th>
                                <th>Desc. Indonesian</th>
                                <th>Desc. English</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if ($ArrData['standards'][$std->requirement_id]) : ?>
                                <?php $n = 0;
                                foreach ($ArrData['standards'][$std->requirement_id] as $dtStd) : $n++; ?>

                                  <tr>
                                    <td><?= $n; ?></td>
                                    <td><?= $dtStd->chapter; ?>
                                    </td>
                                    <td>
                                      <?= limit_text(strip_tags($dtStd->desc_indo), 100) . ' <a href="javascript:void(0)" class="link read" data-id="' . $dtStd->chapter_id . '">[read]</a>'; ?>
                                    </td>
                                    <td>
                                      <?= limit_text(strip_tags($dtStd->desc_eng), 100) . ' <a href="javascript:void(0)" class="link read" data-id="' . $dtStd->chapter_id . '">[read]</a>'; ?>
                                    </td>
                                  </tr>
                              <?php endforeach;
                              endif; ?>
                            </tbody>
                          </table>
                        <?php endforeach; ?>
                      <?php else : ?>
                        <div class="text-center">~ Not available data ~</div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- CHECKLIT TEMUAN -->
            <h4 class="card-title mb-3"><i class="far fa-check-circle text-primary" aria-hidden="true"></i> Audit Non Checklist</h4>
            <div class="mb-15">
              <table id="tblTemuan" class="table datatable table-bordered table-sm table-condensed mb-5">
                <thead class="text-center table-light">
                  <tr>
                    <th width="10">No</th>
                    <th width="">Temuan <span class="text-danger">*</span></th>
                    <th width="150">Kategori</th>
                    <th width="250">ISO & Pasal</th>
                    <th width="80">Action</th>
                  </tr>
                </thead>
                <tbody class="">
                  <?php if (isset($AdtAudit)) foreach ($AdtAudit as $k => $a) : $k++; ?>
                    <tr>
                      <td class="text-center">
                        <?= $k; ?>
                        <input type="hidden" name="temuan[<?= $k; ?>][id]" value="<?= $a->id; ?>">
                      </td>
                      <td>
                        <textarea name="temuan[<?= $k; ?>][description]" class="form-control summernote description" placeholder="Description"><?= $a->description; ?></textarea>
                      </td>
                      <td>
                        <select name="temuan[<?= $k; ?>][category]" class="form-control select2 category" data-minimum-results-for-search="Infinity" data-placeholder="Kategori">
                          <option value=""></option>
                          <option value="0" <?= ($a->category == '0') ? 'selected' : ''; ?>>OK</option>
                          <option value="1" <?= ($a->category == '1') ? 'selected' : ''; ?>>Minor</option>
                          <option value="2" <?= ($a->category == '2') ? 'selected' : ''; ?>>Major</option>
                          <option value="3" <?= ($a->category == '3') ? 'selected' : ''; ?>>OFI</option>
                        </select>
                      </td>
                      <td>
                        <select name="temuan[<?= $k; ?>][standard]" data-row="<?= $k; ?>" class="form-control select2 temuan-standard"
                          data-minimum-results-for-search="Infinity" data-placeholder="Standard">
                          <option value=""></option>
                          <?php if ($ArrStd)
                            foreach ($ArrStd as $s): ?>
                              <option value="<?= $s->requirement_id; ?>" <?= ($s->requirement_id == $a->standard) ? 'selected' : ''; ?>>
                                <?= $s->name; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <select name="temuan[<?= $k; ?>][pasal]" id="temuan_pasal_<?= $k; ?>" class="form-control select2 pasal" data-placeholder="Pasal">
                          <option></option>
                          <?php
                          if (isset($ArrDtlStd[$a->standard])) foreach ($ArrDtlStd[$a->standard] as $k => $p) : ?>
                            <option value="<?= $p->id; ?>" <?= ($a->pasal == $p->id) ? "selected" : ''; ?>><?= $p->chapter; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-icon btn-danger del-temuan" data-id="<?= $a->id; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <button type="button" class="btn btn-sm btn-primary" id="add-item-audit"><i class="fa fa-plus" aria-hidden="true"></i>Add Temuan</button>
            </div>
            <hr class="mb-10">
            <h4 class="card-title mb-3"><i class="fa fa-check-circle text-primary" aria-hidden="true"></i> Audit Checklist</h4>
            <table id="tblChecklist" class="table table-sm table-bordered table-condensed table-hover">
              <thead class="table-light">
                <tr class="text-center">
                  <th width="30">No</th>
                  <th class="w-md-250px">Checklist</th>
                  <th class="w-md-400px">Temuan</th>
                  <th width="150">Kategori</th>
                  <th width="250">ISO & Pasal</th>
                  <th width="30">File</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($checklist) foreach ($checklist as $n => $v) : $n++; ?>
                  <tr>
                    <td class="text-center"><?= $n; ?>
                      <?php if (isset($ArrDtl)) : ?>
                        <input type="hidden" name="detail[<?= $n; ?>][id]" value="<?= $ArrDtl[$v->id]->id; ?>">
                      <?php endif; ?>
                    </td>
                    <td class="bg-light">
                      <input type="hidden" name="detail[<?= $n; ?>][checklist_detail_id]" value="<?= $v->id; ?>">
                      <?= $v->description; ?>
                    </td>
                    <td>
                      <textarea name="detail[<?= $n; ?>][description]" id="description_<?= $n; ?>" placeholder="Description" class="form-control summernote description"><?= isset($ArrDtl) ? $ArrDtl[$v->id]->description : ''; ?></textarea>
                      <label class="invalid-feedback">This value not be empty</label>
                    </td>
                    <td>
                      <select name="detail[<?= $n; ?>][category]" data-row="<?= $n; ?>" id="category_<?= $n; ?>" class="form-control select2" data-minimum-results-for-search="Infinity" data-placeholder="Kategori" data-allow-clear="true" data-width="100%">
                        <option value=""></option>
                        <option value="0" <?= (isset($ArrDtl) && ($ArrDtl[$v->id]->category == '0')) ? 'selected' : ''; ?>>OK</option>
                        <option value="1" <?= (isset($ArrDtl) && ($ArrDtl[$v->id]->category == '1')) ? 'selected' : ''; ?>>Minor</option>
                        <option value="2" <?= (isset($ArrDtl) && ($ArrDtl[$v->id]->category == '2')) ? 'selected' : ''; ?>>Major</option>
                        <option value="3" <?= (isset($ArrDtl) && ($ArrDtl[$v->id]->category == '3')) ? 'selected' : ''; ?>>OFI</option>
                      </select>
                    </td>
                    <td>
                        <select name="detail[<?= $n; ?>][standard]" data-row="<?= $n; ?>" id="standard_<?= $n; ?>"
                          data-minimum-results-for-search="Infinity" class="form-control select2 standard" data-placeholder="Standard"
                          data-allow-clear="true" data-width="100%">
                          <option value=""></option>
                          <?php if ($ArrStd)
                            foreach ($ArrStd as $k => $s): ?>
                              <option value="<?= $s->requirement_id; ?>"
                                <?= (isset($ArrDtl) && ($ArrDtl[$v->id]->standard == $s->requirement_id)) ? "selected" : ''; ?>><?= $s->name; ?>
                              </option>
                            <?php endforeach; ?>
                        </select>

                      <select name="detail[<?= $n; ?>][pasal]" data-row="<?= $n; ?>" id="pasal_<?= $n; ?>" class="form-control select2" data-placeholder="Pasal" data-allow-clear="true">
                        <option></option>
                        <?php if (isset($ArrDtl)) :
                          if (isset($ArrDtlStd[$ArrDtl[$v->id]->standard])) foreach ($ArrDtlStd[$ArrDtl[$v->id]->standard] as $k => $s) : ?>
                            <option value="<?= $s->id; ?>" <?= ($ArrDtl[$v->id]->pasal == $s->id) ? "selected" : ''; ?>><?= $s->chapter; ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </td>
                    <td class="text-center">
                      <!-- <button type="button" onclick="$('#document_'+<?= $n; ?>).click()" class="btn btn-sm btn-icon btn-success upload" id="btn_upload_<?= $n; ?>"><i class="fa fa-upload" aria-hidden="true"></i></button>
                      <input type="file" class="d-none doc" accept="image/*,.pdf" name="document" id="document_<?= $n; ?>">
                      <img src="/assets/images/" class="img-fluid img-bordered" width="80" alt=""> -->

                      <div class="w-100px">
                        <div class="dropzone-wrapper h-100px d-flex justify-content-center align-items-center">
                          <div class="dropzone-desc dropzone-desc-<?= $n; ?>">
                            <i class="fa fa-upload"></i>
                            <!-- <p>Choose an PDF file or drag it here.</p> -->
                          </div>
                          <input type="file" id="document_<?= $n; ?>" data-id="<?= isset($ArrDtl) ? $ArrDtl[$v->id]->id : ''; ?>" name="document" accept="image/*,application/pdf" data-row="<?= $n; ?>" class="dropzone min-h-100px dropzone-1 drop-file" />
                          <div class="for-delete-<?= $n; ?> d-none">
                            <div class="middle d-flex justify-content-center align-items-center">
                              <button type="button" class="btn btn-sm mr-1 btn-icon btn-warning change-image rounded-circle" data-row="<?= $n; ?>"><i class="fa fa-edit"></i></button>
                              <button type="button" class="btn btn-sm mr-1 btn-icon btn-danger remove-file rounded-circle" data-row="<?= $n; ?>"><i class="fa fa-trash"></i></button>
                            </div>
                          </div>
                          <canvas id="preview_<?= $n; ?>" class="d-none" width="80"></canvas>
                          <img id="img_preview_<?= $n; ?>" class="d-none" width="80">
                        </div>
                      </div>

                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div class="text-center">
              <button type="submit" class="btn btn-info w-100px btn-lg" id="save"><i class="fa fa-save" aria-hidden="true"></i>Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalView" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
        <span class="btn-close cursor-pointer" data-dismiss="modal" aria-label="Close">
          <div class="fa fa-times"></div>
        </span>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>
  $(document).ready(function() {
	$('.select2').select2({
		allowClear: true,
		width: "100%",
    placeholder:'Choose one'
	});
    $(document).on('change', '.temuan-standard', function() {
      const e = $(this)
      changeStd(e, '#temuan_pasal_')
    })

    $(document).on('change', '.standard', function() {
      const e = $(this)
      changeStd(e, '#pasal_')
    })

    $(document).on('submit', '#form-audit', function(e) {
      e.preventDefault();
      let btn = $('#save')

      let valid = getValidation('#form-audit')
      let formdata = new FormData($(this)[0])

      if (valid) {
        Swal.fire({
          title: 'Confirmation!',
          icon: 'question',
          text: 'Are you sure to save this data?',
          showCancelButton: true,
        }).then((value) => {
          if (value.isConfirmed) {
            $.ajax({
              url: siteurl + active_controller + 'saveAudit',
              data: formdata,
              type: 'POST',
              dataType: 'JSON',
              processData: false,
              contentType: false,
              cache: false,
              beforeSend: function() {
                btn.attr('disabled', true).html('<i class="spinner spinner-border-sm mr-2"></i> Loading...')
              },
              complete: function() {
                btn.attr('disabled', false).html('<i class="fa fa-save"></i>Save')
              },
              success: function(result) {
                if (result.status == 1) {
                  Swal.fire({
                    title: 'Success!',
                    icon: 'success',
                    text: result.msg,
                    timer: 3000
                  }).then(function() {
                    location.href = siteurl + active_controller + "results";
                  })
                } else {
                  Swal.fire({
                    title: 'Warning!',
                    icon: 'warning',
                    text: result.msg,
                    timer: 3000
                  })
                }
              },
              error: function(result) {
                Swal.fire({
                  title: 'Error!',
                  icon: 'error',
                  text: 'Server timeout, becuase error!',
                  timer: 3000
                })
              }
            })
          }
        })
      }
    })

    /* AUDIT NON CHECKLIST */
    $(document).on('click', '#add-item-audit', function() {
      let n = $('#tblTemuan tbody tr').length + 1;
      let html = '';
      html = `
      <tr>
        <td class="text-center">${n}</td>
        <td><textarea name="temuan[${n}][description]" class="form-control required summernote text-left description" placeholder="Description"></textarea>
          <label class="invalid-feedback">This value not be empty</label>
        </td>
        <td>
          <select name="temuan[${n}][category]" class="form-control select2 category" data-minimum-results-for-search="Infinity" data-placeholder="Kategori">
            <option value=""></option>
            <option value="0">OK</option>
            <option value="1">Minor</option>
            <option value="2">Major</option>
            <option value="3">OFI</option>
          </select>
        </td>
        <td>
         <select name="temuan[${n}][standard]" data-row="${n}" class="form-control select2 temuan-standard" data-minimum-results-for-search="Infinity" data-placeholder="Standard">
          <option value=""></option>
          <?php if ($ArrStd)
            foreach ($ArrStd as $k => $s): ?>
                <option value="<?= $s->requirement_id; ?>"><?= $s->name; ?></option>
            <?php endforeach; ?>
          </select>
          <select name="temuan[${n}][pasal]" id="temuan_pasal_${n}" class="form-control select2 pasal" data-placeholder="Pasal">
           <option></option>
          </select>
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-icon btn-danger del-temuan"><i class="fa fa-trash" aria-hidden="true"></i></button>
        </td>
      </tr>`;
      $('#tblTemuan tbody').append(html)

      $('.select2').select2({
        allowClear: true,
        width: "100%"
      })

      $('textarea.summernote').summernote({
        height: 150, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null,
        inheritPlaceholder: true
      });
    })

    $(document).on('click', '.del-temuan', function() {
      const id = $(this).data('id')
      const btn = $(this)
      if (id) {
        Swal.fire({
          title: 'Confirmation!',
          icon: 'question',
          text: 'Are you sure to Delete this data?',
          showCancelButton: true,
        }).then((value) => {
          if (value.isConfirmed) {
            $.ajax({
              url: siteurl + active_controller + 'deleteNonChacklistAudit',
              data: {
                id
              },
              type: 'POST',
              dataType: 'JSON',
              success: function(result) {
                if (result.status == 1) {
                  Swal.fire({
                    title: 'Success!',
                    icon: 'success',
                    text: result.msg,
                    timer: 3000
                  }).then(function() {
                    // location.reload();
                    btn.parents('tr').remove()
                  })
                } else {
                  Swal.fire({
                    title: 'Warning!',
                    icon: 'warning',
                    text: result.msg,
                    timer: 3000
                  })
                }
              },
              error: function(result) {
                Swal.fire({
                  title: 'Error!',
                  icon: 'error',
                  text: 'Server timeout, becuase error!',
                  timer: 3000
                })
              }
            })
          }
        })
      } else {
        $(this).parents('tr').remove()
      }
      renumberList()
    })


    /* DROPZONE */
    /* Selected File has changed */
    $(document).on('change', ".drop-file", function() {

      // user selected file
      var file = $(this)[0].files[0];
      var r = $(this).data('row')
      const upload = uploadFile(file, r)
      if (upload) {
        // allowed MIME types
        var mime_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/bmp', 'image/webp', 'application/pdf', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel.sheet.macroEnabled.12'];
        // Validate whether PDF
        if (mime_types.indexOf(file.type) == -1) {
          alert('Error : Incorrect file type');
          return;
        }

        // validate file size
        if (file.size > 50 * 1024 * 1024) {
          alert('Error : Exceeded size 50MB');
          return;
        }

        // object url of PDF 
        _OBJECT_URL = URL.createObjectURL(file)

        // send the object url of the pdf to the PDF preview function
        if (file.type == 'application/pdf') {
          showPDF(_OBJECT_URL, r);
        } else {
          drawImage(r);
        }
      }
    });

    $(document).on('click', '.change-image', function() {
      let r = $(this).data('row')
      $('#document_' + r).click()
    })

    /* Reset file input */
    $(document).on('click', "#cancel-pdf,.remove-file", function() {
      let row = $(this).data('row')
      // $("#remove-document").val('x');
      $("#img_preview_" + row).addClass('d-none');
      $("#preview_" + row).addClass('d-none');
      $(".for-delete-" + row).addClass('d-none');
      $('.dropzone-desc-' + row).removeClass('d-none')

      // reset to no selection
      $(this).val('');
    });
  })

  var _PDF_DOC,
    _OBJECT_URL;

  function changeStd(e, pasal) {
    const r = e.data('row')
    const std = e.val()
    const pro = $('#procedure_id').text()
    $(pasal + r).html('')
    if (std && pro) {
      $.ajax({
        url: siteurl + active_controller + "listPasal/" + pro + "/" + std,
        type: 'GET',
        success: (res) => {
          $(pasal + r).html(res)
        },
        error: () => {
          alert('Data not valid!')
        }
      })
    }
  }

  function renumberList() {
    let n = 0
    $('#tblTemuan tbody tr').each(function() {
      n++;
      $(this).find('td:first').text(n)
      $(this).find('textarea[name^="temuan"].description').attr('name', "temuan[" + n + "][description]")
      $(this).find('select[name^="temuan"].category').attr('name', "temuan[" + n + "][category]")
      $(this).find('select[name^="temuan"].standard').attr('name', "temuan[" + n + "][standard]")
      $(this).find('select[name^="temuan"].pasal').attr('name', "temuan[" + n + "][pasal]")
    })
  }

  function showPDF(pdf_url, r) {
    PDFJS.getDocument({
      url: pdf_url
    }).then(function(pdf_doc) {
      _PDF_DOC = pdf_doc;

      // Show the first page
      showPage(1, r);

      // destroy previous object url
      URL.revokeObjectURL(_OBJECT_URL);
    }).catch(function(error) {
      // trigger Cancel on error
      // $("#cancel-pdf").click();

      // error reason
      alert(error.message);
    });;
  }

  function showPage(page_no, r) {
    var _CANVAS = document.querySelector('#preview_' + r)
    // fetch the page
    // console.log(page_no);
    // console.log(_PDF_DOC.getPage(page_no));
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
        $('#preview_' + r).removeClass('d-none');
        $('#img_preview_' + r).addClass('d-none');
        $('.for-delete-' + r).removeClass('d-none');
        $('.dropzone-desc-' + r).addClass('d-none')
      });
    });
  }

  function showImage(file, r) {
    var _CANVAS = document.querySelector('#preview_' + r)

    var scale_required = _CANVAS.width / file.getViewport(1).width;

    // get viewport of the page at required scale
    var viewport = file.getViewport(scale_required);

    // set canvas height
    _CANVAS.height = viewport.height;

    var renderContext = {
      canvasContext: _CANVAS.getContext('2d'),
      viewport: viewport
    };

    // render the page contents in the canvas
    file.render(renderContext).then(function() {
      $('#preview_' + r).removeClass('d-none');
      $('.for-delete-' + r).removeClass('d-none');
      $('.dropzone-desc-' + r).addClass('d-none')
    });
  }

  function drawImage(r) {
    let pict = $('#document_' + r)[0].files[0];
    if (pict) {
      $('#img_preview_' + r).attr('src', URL.createObjectURL(pict)).removeClass('d-none')
      $('#preview_' + r).addClass('d-none');
      $('.for-delete-' + r).removeClass('d-none');
      $('.dropzone-desc-' + r).addClass('d-none')
    }

    // img.src = siteurl + "assets/images/excel.png";
    // console.log(pict);
  }

  function uploadFile(file, row) {
    let res
    let formData = new FormData();
    formData.append('document', file);
    formData.append('id', $('#document_' + row).data('id'));
    return $.ajax({
      url: siteurl + active_controller + 'uploadFile',
      dataType: 'JSON',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
    });
  }
</script>