<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Preview PDF</title>

  <!-- FAVICON -->
  <link rel="icon" type="image/png" href="<?= base_url('assets/logo/' . $this->session->company->id_perusahaan . '/' . $this->session->company->logo); ?>" />

  <style>
    html,
    body {
      margin: 0;
      height: 100%;
      background-color: rgb(225, 225, 225);
    }

    iframe {
      width: 100%;
      height: 100%;
      border: none;
    }
  </style>
</head>

<body>
  <iframe src="<?= $url ?>#toolbar=0&navpanes=0&scrollbar=0"></iframe>
  <script>
    document.addEventListener('contextmenu', function(e) {
      e.preventDefault();
    });
  </script>


</body>

</html>
<?= exit; ?>