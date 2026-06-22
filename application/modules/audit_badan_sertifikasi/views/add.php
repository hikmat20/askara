<form id="form">
  <div class="mb-5">
    <div class="form-group">
      <label for="" class="h6 font-weight-bold mb-5">Instansi <span class="text-danger">*</span></label>
      <input type="name" name="name" placeholder="Instansi" class="form-control form-control-lg required" aria-describedby="helpId">
    </div>
    <div class="form-group">
      <label for="" class="h6 font-weight-bold mb-5">Description</label>
      <textarea name="description" placeholder="Description" class="form-control form-control-lg" aria-describedby="helpId"></textarea>
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
    <tbody></tbody>
  </table>
  <button type="button" class="btn btn-primary btn-sm" id="add-auditor"><i class="fa fa-plus" aria-hidden="true"></i> Add Auditor</button>
</form>