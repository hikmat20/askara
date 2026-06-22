<?php
$file_path = '';
$download_url = base_url('forms/download/' . $id);
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
$is_pdf = ($file_ext === 'pdf');

if ($form_type == 'upload_file') {
  $clean_path = 'directory/FORMS/' . $company_id . '/' . $file_name;
  $file_path_check = FCPATH . $clean_path;

  if (!file_exists($file_path_check)) {
    echo "File not found: " . $file_name;
    return;
  }
  $file_path = base_url($clean_path);
} else {
  // Check is valid link
  if (!function_exists('checkUrl')) {
    function checkUrl($link_form)
    {
      $ch = curl_init($link_form);
      curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

      curl_exec($ch);

      $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      return ($status >= 200 && $status < 400);
    }
  }

  if (!filter_var($link_form, FILTER_VALIDATE_URL)) {
    echo "Format URL tidak valid";
    return;
  } elseif (!checkUrl($link_form)) {
    echo "File not found: " . $link_form;
    exit;
  } 
  $file_path = $link_form;
}
?>
<div class="mb-3 d-flex justify-content-between align-items-center">
    <span class="text-muted">Document: <strong><?= htmlspecialchars($name); ?></strong></span>
    <a href="<?= $download_url; ?>" class="btn btn-sm btn-primary">
        <i class="fa fa-download"></i> Download Form
    </a>
</div>
<?php if ($form_type !== 'upload_file') : ?>
    <iframe src="<?= $file_path; ?>" style="width: 100%;height: 68vh;" frameborder="0"></iframe>
<?php elseif ($is_pdf) : ?>
    <iframe src="<?= $file_path; ?>#toolbar=0&navpanes=0" style="width: 100%;height: 68vh;" frameborder="0"></iframe>
<?php else : ?>
    <div class="p-5 text-center bg-light rounded border">
        <i class="fa fa-file-excel fa-4x text-success mb-3"></i>
        <h5>Excel/Word Document</h5>
        <p class="text-muted">Dokumen ini tidak mendukung preview langsung di browser.</p>
        <a href="<?= $download_url; ?>" class="btn btn-success">
            <i class="fa fa-download"></i> Download File
        </a>
    </div>
<?php endif; ?>