<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
  <title>Procedure</title>
  <style>
    *,

    body {
      font-family: 'Calibri', Helvetica, sans-serif;
      font-size: 12pt;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    .clearfix::after {
      content: "";
      clear: both;
      display: table;
    }

    .box {
      float: left;
      width: 47%;
      padding: 0px 10px;
    }

    table,
    table.table-data {
      border-collapse: collapse;
      width: 100%;
    }

    table.table-data td,
    table.table-data th {
      /* word-wrap: break-word; */
      border: 1px solid black;
      vertical-align: top;
    }

    /* Horizontal */
    .text-center {
      text-align: center;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    /* Vertical */
    .text-middle {
      vertical-align: middle;
    }

    .text-top {
      vertical-align: top;
    }

    .text-bottom {
      vertical-align: bottom;
    }

    /* Padding */
    .p-1 {
      padding: 1px;
    }

    .p-2 {
      padding: 1px;
    }

    .p-3 {
      padding: 3px;
    }

    .p-4 {
      padding: 4px;
    }

    .p-5 {
      padding: 5px;
    }

    ul,
    ol {
      margin: 0;
      padding-left: 20px;
    }

    ol ol,
    ul ul {
      padding-left: 20px;
    }

    ol ol ol,
    ul ul ul {
      padding-left: 20px;

    }

    li {
      padding-left: 20px;
      display: inline;
    }
  </style>
</head>

<body>
  <!-- header -->
  <div class="text-center">
    <div>
      <table class="" width="100%" border="1">
        <thead>
          <tr>
            <th class="text-center" colspan="3"><b>RIWAYAT DOKUMEN</b></th>
          </tr>
          <tr style="background-color: #c8c8c8ff;">
            <th>REVISI</th>
            <th>TANGGAL REVISI</th>
            <th>URAIAN PERUBAHAN</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
            <td>-</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <pagebreak></pagebreak>

  <div style="border-top: 0px solid;height:100%;">
    <div class="clearfix">
      <div class="box">
        <h4><strong>1. TUJUAN</strong></h4>
        <?= ($procedure->object); ?>
        <br>
      </div>
      <div class="box" style="color: #0088ffff;">
        <h4><strong>1. OBJECT</strong></h4>
        <span style="font-style: italic;">
          <?= (isset($procedure_bilingual->object) ? $procedure_bilingual->object : ''); ?>
        </span>
        <br>
      </div>
    </div>
    <div class="clearfix">
      <div class="box">
        <h4><strong>2. RUANG LINGKUP</strong></h4>
        <?= $procedure->scope; ?>
        <br>
      </div>
      <div class="box" style="color: #0088ffff;">
        <h4><strong>2. SCOPE</strong></h4>
        <i>
          <?= (isset($procedure_bilingual->scope) ? $procedure_bilingual->scope : ''); ?>
        </i>
        <br>
      </div>
    </div>
    <div class="clearfix">
      <div class="box">
        <h4><strong>3. TANGGUNG JAWAB</strong></h4>
        <?= $procedure->responsibility; ?>
        <br>
      </div>
      <div class="box" style="color: #0088ffff;">
        <h4><strong>3. RESPONSIBILITY</strong></h4>
        <i>
          <?= (isset($procedure_bilingual->responsibility) ? $procedure_bilingual->responsibility : ''); ?>
        </i>
        <br>
      </div>
    </div>
    <div class="clearfix">
      <div class="box">
        <h4><strong>4. DEFINISI</strong></h4>
        <?= $procedure->define; ?>
        <br>
      </div>
      <div class="box" style="color: #0088ffff;">
        <h4><strong>4. DEFINE</strong></h4>
        <i>
          <?= (isset($procedure_bilingual->define) ? $procedure_bilingual->define : ''); ?>
        </i>
        <br>
      </div>
    </div>
    <div class="clearfix">
      <div class="box">
        <h4><strong>5. PERFORMA INDIKATOR</strong></h4>
        <?= $procedure->performance; ?>
        <br>
      </div>
      <div class="box" style="color: #0088ffff;">
        <h4><strong>5. INDICATOR PERFORMANCE</strong></h4>
        <i>
          <?php (isset($procedure_bilingual->performance) ? $procedure_bilingual->performance : ''); ?>
        </i>
        <br>
      </div>
    </div>
    <div class="clearfix">
      <div class="box">
        <h4><strong>6. KETENTUAN UMUM</strong></h4>
        <?= $procedure->general_requirement; ?>
        <br>
      </div>
      <div class="box" style="color: #0088ffff;">
        <h4><strong>6. GENERAL REQUIREMENT</strong></h4>
        <i>
          <?php (isset($procedure_bilingual->general_requirement) ? $procedure_bilingual->general_requirement : ''); ?>
        </i>
        <br>
      </div>
    </div>
    <div class="clearfix">
      <div class="box">
        <h4>7. REFERENSI</h4>
        <?php if ($ArrStd) : ?>
          <?php foreach ($ArrStd as $std) : ?>
            <h4><?= $std->name; ?></h4>
            <ul>
              <?php if ($ArrData['standards'][$std->requirement_id]) : ?>
                <?php $n = 0;
                foreach ($ArrData['standards'][$std->requirement_id] as $dtStd) : $n++; ?>
                  <li><?= $dtStd->chapter; ?></li>
              <?php endforeach;
              endif; ?>
            </ul>
            <br>
          <?php endforeach; ?>
        <?php endif; ?>
        <br>
      </div>
      <div class="box" style="color: #0088ffff;">
        <h4>7. REFERENCE</h4>
        <?php if ($ArrStd) : ?>
          <?php foreach ($ArrStd as $std) : ?>
            <h4><?= $std->name; ?></h4>
            <ul>
              <?php if ($ArrData['standards'][$std->requirement_id]) : ?>
                <?php $n = 0;
                foreach ($ArrData['standards'][$std->requirement_id] as $dtStd) : $n++; ?>
                  <li><?= $dtStd->chapter; ?></li>
              <?php endforeach;
              endif; ?>
            </ul>
            <br>
          <?php endforeach; ?>
        <?php endif; ?>
        <br>
      </div>
    </div>

    <?php if ($procedure->supplier): ?>
      <div>
        <h4><strong>SIPOCOR</strong></h4>
        <table width="100%" class="">
          <tr>
            <td width="50%" style="padding: 10px;">
              <h5 style="padding-bottom:10px;">1. Supplier</h5>
              <p><?= $procedure->supplier; ?></p>
              <br><br>
              <h5 style="padding-bottom:10px;">3. Process</h5>
              <?= $procedure->process; ?>
              <br><br>
              <h5 style="padding-bottom:10px;">5. Customer</h5>
              <?= $procedure->customer; ?>
              <br><br>
              <h5 style="padding-bottom:10px;">7. Risk</h5>
              <?= $procedure->risk; ?>
              <br><br>
            </td>
            <td width="50%" style="padding: 10px;">
              <h5 style="padding-bottom:10px;">2. Input</h5>
              <?= $procedure->input; ?>
              <br><br>
              <h5 style="padding-bottom:10px;">4. Output</h5>
              <?= $procedure->output; ?>
              <br><br>
              <h5 style="padding-bottom:10px;">6. Objective</h5>
              <?= $procedure->objective; ?>
              <br><br>
              <h5 style="padding-bottom:10px;">8. Mitigation</h5>
              <?= $procedure->mitigation; ?>
              <br><br>
            </td>
          </tr>
        </table>
      </div>
    <?php endif; ?>

    <pagebreak></pagebreak>
    <!-- Deskripsi Procedure -->
    <div>
      <h4>8. DESKRIPSI PROSEDUR</h4>
      <table class="table-data" width="100%" cellpadding="5" cellspacing="0">
        <thead>
          <tr class="table-secondary">
            <th class="py-1 text-center">No.</th>
            <th class="py-1 text-center">PIC/TANGGUNG JAWAB</th>
            <th class="py-1 text-center" colspan="2">DESKRIPSI</th>
            <th class="py-1 text-center">DOKUMEN TERKAIT</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($detail) :
            foreach ($detail as $dtl) : ?>
              <tr>
                <td class="text-center"><?= $dtl->number; ?></td>
                <td class="text-center"><?= $dtl->pic; ?></td>
                <td style=""><?= $dtl->description; ?></td>
                <td style="color:#0088ffff"><i><?= $dtl->description_2; ?></i></td>
                <td class="">
                  <?php $relDocs = json_decode($dtl->relate_doc, true); ?>
                  <?php if (is_array($relDocs)) : ?>
                    <ul>
                      <?php foreach ($relDocs as $relDoc) { ?>
                        <li><?= $ArrForms[$relDoc]->name; ?></li>
                      <?php } ?>
                    </ul>
                  <?php endif; ?>

                  <?php $relIk = json_decode($dtl->relate_ik_doc, true);
                  if (is_array($relIk) && count($relIk) > 0) : ?>
                    <ul>
                      <?php foreach ($relIk as $ik) { ?>
                        <li><?= $ArrGuides[$ik]->name; ?></li>
                      <?php } ?>
                    </ul>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach;
          else : ?>
            <tr>
              <td colspan=" 4" class="text-center">~ Not available data ~</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <br>
    </div>

    <div>
      <h4>9. DISTRIBUSI</h4>
      <ol>
        <?php $lsDept = explode(',', $procedure->distribute_id);
        if (count($lsDept) > 0) :
          foreach ($lsDept as $dep) {
            echo "<li>" . (isset($ArrDept[$dep]->id) && $ArrDept[$dep]->id ? $ArrDept[$dep]->name : '~')  . "</li>";
          }
        else:
          echo "~";
        endif;
        ?>
      </ol>
    </div>
  </div>

  <?php if ($procedure->image_flow_1 || $procedure->image_flow_2 || $procedure->image_flow_3) : ?>
    <pagebreak></pagebreak>
    <div style="border: 0px solid;height:100%;">
      <h4>10. FLOW PROCEDURE</h4>
      <?php if ($procedure->image_flow_1) : ?>
        <img width="100%" src="<?= base_url("directory/FLOW_IMG/$procedure->company_id/$procedure->image_flow_1"); ?>"
          alt="image_flow_1" class="img-fluid">
      <?php endif; ?>
      <?php if ($procedure->image_flow_2) : ?>
        <img width="100%" src="<?= base_url("directory/FLOW_IMG/$procedure->company_id/$procedure->image_flow_2"); ?>"
          alt="image_flow_2" class="img-fluid">
      <?php endif; ?>
      <?php if ($procedure->image_flow_3) : ?>
        <img width="100%" src="<?= base_url("directory/FLOW_IMG/$procedure->company_id/$procedure->image_flow_3"); ?>"
          alt="image_flow_3" class="img-fluid">
      <?php endif; ?>
    </div>
  <?php endif; ?>
</body>

</html>