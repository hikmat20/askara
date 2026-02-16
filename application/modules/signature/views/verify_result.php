<!DOCTYPE html>
<html>

<head>
  <title>Verify Signature</title>
  <style>
    body {
      font-family: Arial;
      text-align: center;
      margin-top: 40px;
    }

    table {
      margin: 0 auto;
      border-collapse: collapse;
      width: 50%;
    }

    .table,
    th,
    td {
      border: 1px solid #ccc;
      padding: 8px;
    }
  </style>
</head>

<body>
  <h2>Document Signature Verification</h2>
  <table class="table table-bordered">
    <tr>
      <th>Status Signature</th>
      <td style="vertical-align: middle;">
        <?php if ($status == 'VALID'): ?>
          <?= $status ?> <img src="https://cdn-icons-png.flaticon.com/512/8358/8358886.png" alt="" width="15">
        <?php else: ?>
          <?= $status ?> <img src="https://cdn-icons-png.flaticon.com/512/7641/7641419.png" alt="" width="15">
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <th>Document Number</th>
      <td><?= $document->nomor ?></td>
    </tr>
    <tr>
      <th>Title</th>
      <td><?= $document->name ?></td>
    </tr>
    <tr>
      <th>Revision</th>
      <td><?= $document->revision ?></td>
    </tr>
    <tr>
      <th>Published Date</th>
      <td><?= $document->published_at ?></td>
    </tr>
    <tr>
      <th>Signed At</th>
      <td><?= date("Y-m-d", strtotime($signature->sign_at)) ?></td>
    </tr>
    <tr>
      <th>Signed By</th>
      <td><?= $signature->sign_by_name ?></td>
    </tr>
    <tr>
      <th>Position</th>
      <td><?= $signature->position_name ?></td>
    </tr>
  </table>
</body>

</html>