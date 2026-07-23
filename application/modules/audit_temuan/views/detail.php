<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="d-flex flex-column-fluid">
    <div class="container">
      <form id="formData">
        <div class="card card-stretch shadow card-custom">
          <div class="card-header">
            <h2 class="mt-5"><i class="<?= $icon; ?> text-primary mr-2"></i><?= $title; ?></h2>
            <div class="mt-4 float-right ">
              <a href="<?= base_url($this->uri->segment(1) . "/print_pdf/" . (isset($data->id) ? $data->id : '')); ?>" target="_blank" class="btn btn-danger mr-2" title="Print PDF">
                <i class="fa fa-file-pdf mr-1"></i>Print PDF</a>
              <a href="<?= base_url($this->uri->segment(1)); ?>" class="btn btn-secondary w-100px" title="Back">
                <i class="fa fa-reply mr-1"></i>Back</a>
            </div>
          </div>
          <div class="card-body">
            <input type="hidden" name="audit_id" value="<?= isset($data->id) ? $data->id : ''; ?>">
            <input type="hidden" name="company_id" value="<?= isset($data->company_id) ? $data->company_id : ''; ?>">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-2 row">
                  <label for="" class="col-md-4 h6 font-weight-bold">Company</label>
                  <label for="" class="col h6">: <?= isset($data->company_name) ? $data->company_name : ''; ?></label>
                </div>
                <div class="mb-2 row">
                  <label for="" class="col-md-4 h6 font-weight-bold">Badan Sertifikasi</label>
                  <label for="" class="col h6">: <?= isset($data->name) ? $data->name : ''; ?></label>
                </div>
              </div>
            </div>
            <hr>
            <div class="mb-3">
              <table id="tblStandard" class="table table-bordered table-sm table-condensed table-hover">
                <thead class="table-light">
                  <tr>
                    <th width="30" class="text-center">No</th>
                    <th class="text-center">Standard</th>
                    <th width="100" class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($dataStd) :
                    foreach ($dataStd as $k => $v) : $k++; ?>
                      <tr>
                        <td><?= $k; ?></td>
                        <td>
                          <span class="d-none dataIdStd"><?= $v->standard_id; ?></span>
                          <?= $v->standard_name; ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-icon btn-xs btn-danger delete" data-id="<?= $v->id; ?>"><i class="fa fa-trash" title="Delete Standard" data-toggle="tooltip" aria-hidden="true"></i></button>
                          <button type="button" class="btn btn-icon btn-xs btn-success view" data-id="<?= $v->id; ?>"><i class="fa fa-eye" title="View" data-toggle="tooltip" aria-hidden="true"></i></button>
                          <a href="<?= base_url($this->uri->segment(1) . "/detail/$v->audit_id/$v->id"); ?>" class="btn btn-icon btn-xs btn-info temuan"><i class="fa fa-arrow-right" title="Data Temuan" data-toggle="tooltip" aria-hidden="true"></i></a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <!-- <tr>
                      <td colspan="3" class="text-center"><i class="small">Not available data</i></td>
                    </tr> -->
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <button type="button" class="btn btn-primary btn-sm" id="add-standard"><i class="fa fa-plus" aria-hidden="true"></i> Add Standard</button>
          </div>
          <div class="card-footer text-center"></div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="min-width: 85%;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">View Temuan</h5>
        <button type="button" class="btn btn-xs btn-icon" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times  text-dark" aria-hidden="true"></i>
        </button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i>Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#tblStandard').DataTable({
      destroy: true,
      autoWidth: false
    })

    /* Add Standard */
    $(document).on('click', '#add-standard', function() {
      let html = '';
      let n = $('#tblStandard tbody tr').length + 1

      html += `
    	<tr data-row="${n}">
    		<td class="number text-center">${n}</td>
    		<td class="">
    			<select name="standard[${n}][standard_id]" data-placeholder="Select Standard" class="form-control std select2-std required" required data-row="${n}">
    				<option></option>
            <?php if ($requirement) foreach ($requirement as $k => $v) : ?>
              <option value="<?= $v->id; ?>"><?= $v->name; ?></option>
              <?php endforeach; ?>
    			</select>
    		</td>
    		<td class="text-center">
    			<button type="button" class="btn btn-xs btn-danger btn-icon delete"><i class="fa fa-trash" aria-hidden="true"></i></button>
    		</td>
    	</tr>`;

      $('#tblStandard tbody').append(html)
      $('.select2-std').select2({
        allowClear: true,
        width: "100%"
      })
      selectStd('.select2-std', '.dataIdStd')
      checkStd('.select2-std', '.dataIdStd')
    })

    $(document).on('change', '.select2-std', function() {
      selectStd('.select2-std', '.dataIdStd')
    })


    /* Save */
    $(document).on('submit', '#formData', function(e) {
      e.preventDefault();
      let formdata = new FormData($(this)[0])
      let btn = $('#save')

      Swal.fire({
        title: 'Confirmation!',
        icon: 'question',
        text: 'Are you sure to save this data?',
        showCancelButton: true,
      }).then((value) => {
        if (value.isConfirmed) {
          $.ajax({
            url: siteurl + active_controller + 'save_detail',
            data: formdata,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
              btn.attr('disabled', true).html('<i class="spinner spinner-border-sm mr-2"></i>Loading...')
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
                  timer: 2000
                })
                location.reload()
              } else {
                Swal.fire({
                  title: 'Warning!',
                  icon: 'warning',
                  text: result.msg,
                  timer: 2000
                })
              }
            },
            error: function(result) {
              Swal.fire({
                title: 'Error!',
                icon: 'error',
                text: 'Server timeout, becuase error!',
                timer: 4000
              })
            }
          })
        }
      })
    })

    /* Delete */
    $(document).on('click', '.delete', function(e) {
      const id = $(this).data('id')
      const btn = $(this)

      if (id) {
        Swal.fire({
          title: 'Delete!',
          icon: 'question',
          text: 'Are you sure to delete this data?',
          showCancelButton: true,
        }).then((value) => {
          if (value.isConfirmed) {
            $.ajax({
              url: siteurl + active_controller + 'delete_standard',
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
                    timer: 2000
                  }).then(function() {
                    location.reload();
                  })

                } else {
                  Swal.fire({
                    title: 'Warning!',
                    icon: 'warning',
                    text: result.msg,
                    timer: 2000
                  })
                }
              },
              error: function(result) {
                Swal.fire({
                  title: 'Error!',
                  icon: 'error',
                  text: 'Server timeout, becuase error!',
                  timer: 4000
                })
              }
            })
          }
        })
      } else {
        btn.parents('tr').remove()
        checkStd()
      }
    })

    /* View */
    $(document).on('click', '.view', function() {
      const id = $(this).data('id')
      if (id) {
        const url = siteurl + active_controller + 'view/' + id
        $('#modelId').modal()
        $('#modelId .modal-body').load(url)
      }
    })

  })

  function selectStd(s, d) {
    const selectedValue = [];
    $(s)
      .find(':selected')
      .filter(function(idx, el) {
        return $(el).attr('value');
      })
      .each(function(idx, el) {
        selectedValue.push($(el).attr('value'));
      });
    $(d).each(function(idx, el) {
      selectedValue.push($(el).text());
    });

    $(s)
      .find('option')
      .each(function(idx, option) {
        if (selectedValue.indexOf($(option).attr('value')) > -1) {
          if ($(option).is(':checked')) {
            return;
          } else {
            $(this).attr('disabled', true);
          }
        } else {
          $(this).attr('disabled', false);
        }
      });
  }

  function checkStd() {
    const btnSave = `<button type="submit" class="btn btn-info btn-lg  text-center min-w-100px" id="save"><i class="fa fa-save"></i>Save</button>`
    const sl = $('select.select2-std').length

    if (sl > 0) {
      $('.card-footer').html(btnSave)
    } else {
      $('.card-footer').html('')
    }
  }
</script>