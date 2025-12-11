<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Procedure</title>
  <style>
    *,
    body {
      font-family: Arial, Helvetica, sans-serif;
    }

    table.table-data {
      width: 100%;
    }

    table.table-data td,
    table.table-data th {
      padding: 2px;
      word-wrap: break-word;
      border: 1px solid #444
    }

    table,
    table.table-data {
      border-collapse: collapse;
    }

    table td {
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
      padding: 2px;
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
  </style>

</head>

<body>

  <table border="1" width="100%">
    <tr>
      <td rowspan="3" width="50%" class="text-center">
        <h2><?= $this->session->company->nm_perusahaan; ?></h2>
      </td>
      <td width="120px">Nomor Dokumen</td>
      <td><?= $procedure->nomor; ?></td>
    </tr>
    <tr>
      <td>Revisi</td>
      <td><?= ($procedure->revision) ?: '~'; ?></td>
    </tr>
    <tr>
      <td>Tgl. Revisi</td>
      <td><?= ($procedure->revision_date) ? date_format(date_create($procedure->revision_date), 'd F Y') : '~'; ?>
      </td>
    </tr>
    <tr>
      <td class="text-center" style="background-color: #000;color:#fff">
        <h4><?= strtoupper($procedure->name); ?></h4>
      </td>
      <td>Halaman</td>
      <td>{PAGENO} of {nbpg}</td>
    </tr>
  </table>

  <div class="">
    <!-- HEADER -->

    <h4><strong>TUJUAN</strong></h4>
    <?= ($procedure->object); ?>
    <br>

    <h4><strong>RUANG LINGKUP</strong></h4>
    <?= $procedure->scope; ?>
    <br>

    <h4><strong>DEFINISI</strong></h4>
    <?= $procedure->define; ?>
    <br>

    <h4><strong>PERFORMA INDIKATOR</strong></h4>
    <?= $procedure->performance; ?>
    <br>

    <h4>REFERENSI</h4>
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
    <?php else : ?>
      ~ Not available data ~
    <?php endif; ?>
    <br>

    <h4>SIPOCOR</h4>
    <table width="100%">
      <tr>
        <td width="50%">
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
        <td width="50%">
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
    <br>

    <h4>FLOW PROCEDURE</h4>
    <?php if ($procedure->image_flow_1 || $procedure->image_flow_2 || $procedure->image_flow_3) : ?>
      <?php if ($procedure->image_flow_1) : ?>
        <img height="600px" src="<?= base_url("directory/FLOW_IMG/$procedure->company_id/$procedure->image_flow_1"); ?>"
          alt="image_flow_1" class="img-fluid">
      <?php endif; ?>
      <?php if ($procedure->image_flow_2) : ?>
        <img height="600px" src="<?= base_url("directory/FLOW_IMG/$procedure->company_id/$procedure->image_flow_2"); ?>"
          alt="image_flow_2" class="img-fluid">
      <?php endif; ?>
      <?php if ($procedure->image_flow_3) : ?>
        <img height="600px" src="<?= base_url("directory/FLOW_IMG/$procedure->company_id/$procedure->image_flow_3"); ?>"
          alt="image_flow_3" class="img-fluid">
      <?php endif; ?>
    <?php else : ?>
      ~ Not available data ~
    <?php endif; ?>

    <?php if ($procedure->link_video) : ?>
      <h4>VIDEO</h4>
      <a href="<?= ($procedure->link_video); ?>">Link Video</a>
    <?php endif; ?>
    <br>
    <h3>PROSES TERKAIT</h3>
    <table class="table-data" style="font-size: 11px;">
      <thead>
        <tr class="table-secondary">
          <th class="py-1 text-center">No.</th>
          <th class="py-1 text-center">PIC/TANGGUNG JAWAB</th>
          <th class="py-1 text-center">DESKRIPSI</th>
          <th class="py-1 text-center">DOKUMEN TERKAIT</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($detail) :
          foreach ($detail as $dtl) : ?>
            <tr>
              <td class="text-center"><?= $dtl->number; ?></td>
              <td class="text-center"><?= $dtl->pic; ?></td>
              <td><?= $dtl->description; ?></td>
              <td class="">
                <?php $relDocs = json_decode($dtl->relate_doc); ?>
                <?php if (is_array($relDocs)) : ?>
                  <ul>
                    <?php foreach ($relDocs as $relDoc) { ?>
                      <li><?= $ArrForms[$relDoc]->name; ?></li>
                    <?php } ?>
                  </ul>

                  <?php $relIk = json_decode($dtl->relate_ik_doc); ?>
                  <?php if (is_array($relIk)) : ?>
                    <ul>
                      <?php foreach ($relIk as $ik) { ?>
                        <li><?= $ArrGuides[$ik]->name; ?></li>
                      <?php } ?>
                    </ul>
                  <?php endif; ?>
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
    <h3>DATA APPROVAL</h3>
    <table class="table-data">
      <tr>
        <td class="text-left" width="180">Prepared By</td>
        <td><?= ($procedure->reviewer_id) ? $ArrUsr[$procedure->prepared_by]->full_name : '-'; ?></td>
      </tr>
      <tr>
        <td class="text-left" style="vertical-align: middle;" rowspan="2">Review By</td>
        <td><?= ($procedure->reviewer_id) ? $ArrJab[$procedure->reviewer_id]->name : '-'; ?></td>
      </tr>
      <tr>
        <td><?= ($procedure->reviewed_by) ? $ArrUsr[$procedure->reviewed_by]->full_name : '-'; ?></td>
      </tr>
      <tr>
        <td class="text-left" style="vertical-align: middle;" rowspan="2">Approval By</td>
        <td><?= ($procedure->approval_id) ? $ArrJab[$procedure->approval_id]->name : '-'; ?></td>
      </tr>
      <tr>
        <td><?= ($procedure->approved_by) ? $ArrUsr[$procedure->approved_by]->full_name : '-'; ?></td>
      </tr>
      <tr>
        <td class="text-left" style="vertical-align: middle;">Distribution By</td>
        <td>
          <?php $lsJab = explode(',', $procedure->distribute_id);
          foreach ($lsJab as $jab) {
            echo ($jab) ? $ArrJab[$jab]->name . "<br>" : '-';
          }
          ?>
        </td>
      </tr>
    </table>
  </div>
</body>

</html>