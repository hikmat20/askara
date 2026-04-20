<style>
  .cover
  {
    position: absolute;
    width: 95%;
    height: 95%;
    background-color: red;
    opacity: 0
  }
</style>
<?php
$file_path = '';
if ($form_type == 'upload_file') {
  $file_path = FCPATH . 'directory/FORMS/' . $company_id . '/' . $file_name;

  if (!file_exists($file_path)) {
    echo "File not found.";
    return;
  }
  $file_path = base_url('directory/FORMS/' . $company_id . '/' . $file_name);
} else {
  // Validasi URL menggunakan helper checkUrl() dari app_helper.php
  if (!filter_var($link_form, FILTER_VALIDATE_URL)) {
    echo "Format URL tidak valid";
    return;
  } elseif (!checkUrl($link_form)) {
    echo "Link tidak dapat diakses: " . htmlspecialchars($link_form);
    return;
  }
  $file_path = $link_form;
}
?>
<div class="cover"></div>
<iframe src="<?= $file_path; ?>#toolbar=0&navpanes=0" style="width: 100%;height: 68vh;" frameborder="0"></iframe>