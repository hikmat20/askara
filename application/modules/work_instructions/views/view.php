<?php
$path = FCPATH . 'directory/WI/1/' . $file_name;

if (!file_exists($path)) {
  echo "File not found: " . $path;
  return;
}
$file_path = base_url('directory/WI/1/' . $file_name);
?>
<iframe src="<?= $file_path; ?>#toolbar=0&navpanes=0" style="width: 100%;height: 68vh;" frameborder="0"></iframe>