<div class="row">
  <div class="col-md-12">
    <div class="mb-3 row">
      <label class="col-md-3 h6 font-weight-bold text-muted mb-0">Company</label>
      <div class="col h6 font-weight-bolder text-dark mb-0">: <?= isset($data->company_name) ? $data->company_name : ''; ?></div>
    </div>
    <div class="mb-3 row">
      <label class="col-md-3 h6 font-weight-bold text-muted mb-0">Badan Sertifikasi</label>
      <div class="col h6 font-weight-bolder text-dark mb-0">: <?= isset($data->name) ? $data->name : ''; ?></div>
    </div>
  </div>
</div>
<hr class="my-4">
<h6 class="font-weight-bolder mb-3 text-primary"><i class="fa fa-list mr-1"></i> Detail Temuan Per Standar</h6>

<?php if (!empty($dataStd)) : ?>
  <?php foreach ($dataStd as $k => $std) : ?>
    <div class="card card-custom gutter-b shadow-sm mb-4 border border-light" style="border-radius: 8px; overflow: hidden;">
      <div class="card-header bg-light-primary py-3 px-4" style="background-color: #E8F0FE; border-bottom: 1px solid #D2E3FC;">
        <h6 class="card-title font-weight-bold text-primary mb-0" style="color: #1A73E8 !important;">
          <i class="fa fa-book mr-2"></i><?= $std->standard_name; ?>
        </h6>
      </div>
      <div class="card-body p-3">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-sm table-hover mb-0">
            <thead>
              <tr class="bg-light">
                <th width="40" class="text-center font-weight-bold text-muted">No</th>
                <th class="text-center font-weight-bold text-muted">Pasal</th>
                <th class="text-center font-weight-bold text-muted">Temuan</th>
                <th width="100" class="text-center font-weight-bold text-muted">Kategori</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $cat = [
                '1' => '<span class="label label-success label-inline font-weight-bold">Minor</span>',
                '2' => '<span class="label label-danger label-inline font-weight-bold">Major</span>',
                '3' => '<span class="label label-warning label-inline font-weight-bold">OFI</span>',
              ];

              if (!empty($std->findings)) :
                foreach ($std->findings as $idx => $v) : $idx++; ?>
                  <tr>
                    <td class="text-center align-middle"><?= $idx; ?></td>
                    <td class="text-left align-middle px-3">
                      <?php if ($v->pasal_name) : ?>
                        <ul class="mb-0 pl-4 font-weight-bold text-dark">
                          <li><?= implode("</li><li>", json_decode($v->pasal_name)); ?></li>
                        </ul>
                      <?php endif; ?>
                    </td>
                    <td class="text-left align-middle px-3 text-muted"><?= $v->description; ?></td>
                    <td class="text-center align-middle"><?= isset($cat[$v->category]) ? $cat[$v->category] : '-'; ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-3"><i class="fa fa-info-circle mr-1"></i> Tidak ada data temuan untuk standar ini.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php else : ?>
  <div class="alert alert-custom alert-light-danger fade show p-4" role="alert">
    <div class="alert-icon"><i class="fa fa-info-circle"></i></div>
    <div class="alert-text">Tidak ada data standar untuk audit ini.</div>
  </div>
<?php endif; ?>
