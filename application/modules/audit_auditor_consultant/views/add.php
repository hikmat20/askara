<form id="form">
  <div class="form-group">
    <label for="" class="h6 font-weight-bold mb-5">Name <span class="text-danger">*</span></label>
    <input type="name" name="name" placeholder="Process Name" class="form-control form-control-lg required" aria-describedby="helpId">
  </div>
  <div class="form-group">
    <label for="" class="h6 font-weight-bold mb-5">Position <span class="text-danger">*</span></label>
    <select name="position[]" class="form-control select2-position required" multiple data-placeholder="Select Position" data-allow-clear="true" id="position">
      <option value="1">Auditor</option>
      <option value="2">Consultant</option>
    </select>
  </div>
  <div class="form-group">
    <label for="" class="h6 font-weight-bold mb-5">Email <span class="text-danger">*</span></label>
    <input type="email" name="email" placeholder="Email" class="form-control form-control-lg required" aria-describedby="helpId">
  </div>
  <div class="form-group">
    <label for="" class="h6 font-weight-bold mb-5">Description</label>
    <textarea name="description" placeholder="Description" rows="5" class="form-control  form-control-lg" aria-describedby="helpId"></textarea>
  </div>
</form>

<script>
  $(document).ready(function() {
    $('.select2-position').select2({
      width: '100%'
    })
  })
</script>