<div class="mb-3 row">
  <label for="Name" class="col-3 col-form-label font-weight-bold">Position Name</label>
  <div class="col-9">
    <label class="col-form-label">: <?= $pos->name; ?></label></span>
  </div>
</div>
<hr>
<h5>List User</h5>
<table class="table table-bordered table-sm">
  <thead>
    <tr class="table-light">
      <th>No</th>
      <th>Name</th>
      <th class="text-center">Opsi</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($users) : ?>
      <?php $n = 0;
      foreach ($users as $usr) : $n++; ?>
        <tr>
          <td><?= $n; ?></td>
          <td><?= $usr->full_name; ?></td>
          <td class="text-center">
            <?php if (!$pos->assign_user) : ?>
              <button type="button" data-id="<?= $usr->user_id; ?>" data-position="<?= $pos->id; ?>" class="btn btn-xs btn-light-primary btn-icon choose-user"><i class="fa fa-check"></i></button>
            <?php elseif ($pos->assign_user == $usr->user_id) : ?>
              <button type="button" data-id="<?= $usr->user_id; ?>" data-position="<?= $pos->id; ?>" class="btn btn-xs btn-light-danger btn-icon remove-user"><i class="fa fa-times"></i></button>
            <?php else : ?>
              <button type="button" data-id="<?= $usr->user_id; ?>" data-position="<?= $pos->id; ?>" class="btn btn-xs btn-light-primary btn-icon choose-user"><i class="fa fa-check"></i></button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>