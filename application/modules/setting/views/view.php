<form id="form-company">
  <div class="mb-3 row flex-nowrap">
    <label for="Name" class="col-3 col-form-label font-weight-bold">Company Name</label>
    <div class="col-9">
      <input type="hidden" name="id_perusahaan" id="" value="">
      <span class="">: <?= $data->nm_perusahaan; ?></span></span>
    </div>
  </div>
  <div class="mb-3 row flex-nowrap">
    <label for="" class="col-3 col-form-label font-weight-bold">Address</label>
    <div class="col-9">
      <span class="">: <?= $data->alamat; ?></span></span>
    </div>
  </div>
  <div class="mb-3 row flex-nowrap">
    <label for="" class="col-3 col-form-label font-weight-bold">City</label>
    <div class="col-4">
      <span class="">: <?= $data->kota; ?></span></span>
    </div>
  </div>
  <div class="mb-3 row flex-nowrap">
    <label for="" class="col-3 col-form-label font-weight-bold">Inisial</label>
    <div class="col-4">
      <span class="">: <?= $data->inisial; ?></span></span>
    </div>
  </div>
</form>