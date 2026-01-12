<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $procedure->name; ?></title>
  <title>Print Preview</title>
  <!-- <link rel="stylesheet" href="print-preview.css"> -->
  <style>
    body {
      background: #777;
      margin: 0;
      font-family: Calibri, Arial, sans-serif;
    }

    /* TOOLBAR */
    .toolbar {
      position: fixed;
      top: 0;
      width: 100%;
      background: #333;
      color: #fff;
      padding: 8px;
      text-align: center;
      z-index: 1000;
    }

    .toolbar button {
      padding: 4px 10px;
      font-size: 16px;
    }

    /* VIEWER */
    #viewer {
      margin-top: 50px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transform-origin: top center;
    }

    /* PAGE A4 */
    .page {
      width: 210mm;
      height: 297mm;
      background: #fff;
      margin: 20px 0;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
      position: relative;
      page-break-after: always;
    }

    /* CONTENT */
    .page-content {
      padding: 20mm;
      box-sizing: border-box;
      height: calc(297mm - 30mm);
      overflow: hidden;
    }

    /* FOOTER */
    .page-footer {
      position: absolute;
      bottom: 10mm;
      width: 100%;
      text-align: center;
      font-size: 12px;
      color: #555;
    }

    /* PRINT MODE */
    @media print {
      body {
        background: none;
      }

      .toolbar {
        display: none;
      }

      .page {
        box-shadow: none;
        margin: 0;
      }
    }
  </style>
</head>

<body>

  <!-- TOOLBAR -->
  <div class="toolbar">
    <button onclick="zoomOut()">−</button>
    <span id="zoomLabel">100%</span>
    <button onclick="zoomIn()">+</button>
  </div>

  <!-- DOCUMENT -->
  <div id="viewer">

    <div class="page">
      <div class="page-content">
        <h1>Judul Dokumen</h1>
        <p>Isi halaman pertama...</p>
      </div>
      <div class="page-footer">
        Halaman <span class="pageNumber"></span>
      </div>
    </div>

    <div class="page">
      <div class="page-content">
        <h2>Halaman Kedua</h2>
        <p>Konten lanjutan...</p>
      </div>
      <div class="page-footer">
        Halaman <span class="pageNumber"></span>
      </div>
    </div>

  </div>

  <!-- <script src="print-preview.js"></script> -->
  <script>
    let zoomLevel = 1;

    function zoomIn() {
      zoomLevel += 0.1;
      applyZoom();
    }

    function zoomOut() {
      zoomLevel = Math.max(0.5, zoomLevel - 0.1);
      applyZoom();
    }

    function applyZoom() {
      const viewer = document.getElementById('viewer');
      viewer.style.transform = `scale(${zoomLevel})`;
      document.getElementById('zoomLabel').innerText = Math.round(zoomLevel * 100) + '%';
    }

    // PAGE NUMBER AUTO
    document.querySelectorAll('.page').forEach((page, index) => {
      page.querySelector('.pageNumber').innerText = index + 1;
    });
  </script>
</body>

</html>