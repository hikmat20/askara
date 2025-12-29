<form id="form-company">
  <div class="row">
    <div class="col-9">
      <div class="mb-3 row">
        <label for="Name" class="col-3 col-form-label font-weight-bold">Company Name <span class="text-danger">*</span></label>
        <div class="col-9">
          <input type="hidden" name="id_perusahaan" value="<?= $data->id_perusahaan; ?>">
          <input type="text" value="<?= $data->nm_perusahaan; ?>" name="nm_perusahaan" class="form-control required" id="nm_perusahaan" placeholder="Company Name" />
          <span class="invalid-feedback">Company Name can't be empty</span>
        </div>
      </div>
      <div class="mb-3 row">
        <label for="" class="col-3 col-form-label font-weight-bold">Address <span class="text-danger">*</span></label>
        <div class="col-9">
          <textarea type="text" name="alamat" id="alamat" class="form-control required" placeholder="Jl. ..."><?= $data->alamat; ?></textarea>
          <span class="invalid-feedback">Address can't be empty</span>
        </div>
      </div>
      <div class="mb-3 row">
        <label for="" class="col-3 col-form-label font-weight-bold">City <span class="text-danger">*</span></label>
        <div class="col-4">
          <input type="text" value="<?= $data->kota; ?>" name="kota" id="kota" class="form-control required" placeholder="Jakarta">
          <span class="invalid-feedback">City can't be empty</span>
        </div>
      </div>
      <div class="mb-3 row">
        <label for="" class="col-3 col-form-label font-weight-bold">Inisial <span class="text-danger">*</span></label>
        <div class="col-4">
          <input type="text" value="<?= $data->inisial; ?>" name="inisial" id="inisial" maxlength="3" class="form-control required" placeholder="SSC">
          <span class="invalid-feedback">Inisial can't be empty</span>
        </div>
      </div>
    </div>

    <div class="col-3">
      <label for="">Logo</label>
      <div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 150px;">
        <div class="dropzone-desc">
          <?php if ($data->logo) : ?>
            <img loading="lazy" width="100" src="<?= base_url($data->path_logo . $data->id_perusahaan . '/' . $data->logo); ?>" />
          <?php else : ?>
            <i class="fa fa-upload"></i>
            <p>Choose an image file or drag it here.</p>
          <?php endif; ?>
        </div>
        <input type="file" name="logo" accept="image/*" data-index="1" class="dropzone dropzone-1">
        <?php if ($data->logo) : ?>
          <div class="middle d-flex justify-content-center align-items-center">
            <button type="button" onclick="$(this).parent().parent().find('.dropzone').click()" class="btn btn-sm mr-1 btn-icon btn-warning change-image rounded-circle"><i class="fa fa-edit"></i></button>
            <button type="button" onclick="remove_image(this)" data-id="" data-img="logo" class="btn btn-sm mr-1 btn-icon btn-danger remove-image rounded-circle"><i class="fa fa-trash text-white"></i></button>
          </div>
        <?php endif; ?>
        <div class="for-delete"></div>

      </div>


    </div>
  </div>
  <div id="branch-list">
    <?php if ($branch) foreach ($branch as $k => $val) : $k++; ?>
      <!-- <div class="card branch border-info mb-3">
        <div class="card-header p-3">
          <h4 class="card-title font-weight-bolder mb-0"><i class="fas fa-code-branch text-dark"></i> Branch #<?= $k; ?></h4>
        </div>
        <div class="card-body p-4">
          <input type="hidden" name="branch[<?= $k; ?>][id]" value="<?= $val->id; ?>">
          <div class="mb-3 row flex-nowrap">
            <label for="" class="col-3 col-form-label font-weight-bold">Company Branch Name <span class="text-danger">*</span></label>
            <div class="col">
              <input type="text" name="branch[<?= $k; ?>][branch_name]" id="branch<?= $k; ?>" value="<?= $val->branch_name; ?>" class="form-control required" placeholder="Branch Name">
              <span class="invalid-feedback">Company Branch Name can't be empty</span>
            </div>
          </div>
          <div class="mb-3 row flex-nowrap">
            <label for="" class="col-3 col-form-label font-weight-bold">Address <span class=" text-danger">*</span></label>
            <div class="col">
              <textarea name="branch[<?= $k; ?>][address]" id="branch<?= $k; ?>" class="form-control required" placeholder="Branch Name"><?= $val->branch_address; ?></textarea>
              <span class="invalid-feedback">Address Branch can't be empty</span>
            </div>
          </div>
          <div class="mb-3 row flex-nowrap">
            <label for="" class="col-3 col-form-label font-weight-bold">City <span class=" text-danger">*</span></label>
            <div class="col-4">
              <input type="text" name="branch[<?= $k; ?>][city]" id="branch<?= $k; ?>" class="form-control required" value="<?= $val->branch_city; ?>" placeholder="Branch Name">
              <span class="invalid-feedback">City Branch can't be empty</span>
            </div>
          </div>
        </div>
      </div> -->
    <?php endforeach; ?>
  </div>
  <!-- <button type="button" class="btn btn-sm btn-outline-info" id="add-branch"><i class="fa fa-plus" aria-hidden="true"></i>Add Branch</button> -->

</form>