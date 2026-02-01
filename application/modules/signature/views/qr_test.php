<!DOCTYPE html>
<html>

<head>
  <title>QR Test</title>
  <style>
    body {
      font-family: Arial;
      text-align: center;
      margin-top: 40px;
    }

    img {
      border: 1px solid #ccc;
      padding: 10px;
    }

    small {
      color: #777;
    }
  </style>
</head>

<body>

  <h2>QR Code Generated</h2>

  <img src="<?= $qr_image ?>" alt="QR Code">

  <p><small>Token:</small><br><?= $token ?></p>
  <p><small>URL:</small><br><?= $url ?></p>

</body>

</html>