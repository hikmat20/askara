<?php
$download_url = base_url('work_instructions/download/' . $id);
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
$is_pdf = ($file_ext === 'pdf');

// Gunakan file_path dari view jika ada, atau fallback ke default path
$clean_path = isset($file_path) ? ltrim($file_path, './') : 'directory/WI/' . $company_id . '/' . $file_name;
$path_check = FCPATH . $clean_path;

if (!file_exists($path_check)) {
  echo "File not found: " . $file_name;
  return;
}
$web_file_path = base_url($clean_path);
?>
<div class="mb-3 d-flex justify-content-between align-items-center">
    <span class="text-muted">Document: <strong><?= htmlspecialchars($name); ?></strong></span>
    <a href="<?= $download_url; ?>" class="btn btn-sm btn-primary">
        <i class="fa fa-download"></i> Download WI
    </a>
</div>
<?php if ($is_pdf) : ?>
    <iframe src="<?= $web_file_path; ?>#toolbar=0&navpanes=0" style="width: 100%;height: 68vh;" frameborder="0"></iframe>
<?php else : ?>
    <div class="p-5 text-center bg-light rounded border">
        <i class="fa fa-file-word fa-4x text-primary mb-3"></i>
        <h5>Document File</h5>
        <p class="text-muted">Dokumen ini tidak mendukung preview langsung di browser.</p>
        <a href="<?= $download_url; ?>" class="btn btn-primary">
            <i class="fa fa-download"></i> Download File
        </a>
    </div>
<?php endif; ?>