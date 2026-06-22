<form id="form">
  <div class="mb-5">
    <input type="hidden" name="id" value="<?= $data->id; ?>">
    <div class="form-group">
      <label for="" class="h6 font-weight-bold mb-5">Instansi <span class="text-danger">*</span></label>
      <input type="name" name="name" placeholder="Instansi" value="<?= $data->name; ?>" class="form-control form-control-lg required" aria-describedby="helpId">
    </div>
    <div class="form-group">
      <label for="" class="h6 font-weight-bold mb-5">Description</label>
      <textarea name="description" placeholder="Cescription" class="form-control form-control-lg" aria-describedby="helpId"><?= $data->description; ?></textarea>
    </div>
  </div>


  <h5><i class="fa fa-user" aria-hidden="true"></i> Auditor</h5>
  <table id="tblAuditor" class="table table-bordered table-condensed table-sm mb-3 table-hover">
    <thead>
      <tr>
        <th width="30">No</th>
        <th>Name</th>
        <th width="50">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($auditors) foreach ($auditors as $k => $v) : $k++; ?>
        <tr class="text-center">
          <td><?= $k; ?></td>
          <td>
            <input type="hidden" name="auditor[<?= $k ; ?>][id]" value="<?= $v->id ; ?>">
            <input type="text" name="auditor[<?= $k ; ?>][name]" class="form-control" placeholder="Auditor Name" value="<?= $v->name ; ?>">
          <td>
            <button type="button" class="btn btn-xs btn-icon btn-danger del-auditor" data-id="<?= $v->id ; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
          </td>
        </tr>
      <?php endforeach; ?>

    </tbody>
  </table>
  <button type="button" class="btn btn-primary btn-sm" id="add-auditor"><i class="fa fa-plus" aria-hidden="true"></i> Add Auditor</button>


</form>