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
  $file_path = FCPATH . 'directory/FORMS/1/' . $file_name;

  if (!file_exists($file_path)) {
    echo "File not found: " . $file_path;
    return;
  }
  $file_path = base_url('directory/FORMS/1/' . $file_name);
} else {
  // Check is valid link

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

  if (checkUrl("https://example.com")) {
    echo "Link valid & bisa diakses";
  }



  if (!filter_var($link_form, FILTER_VALIDATE_URL)) {
    echo "Format URL tidak valid";
    return;
  } elseif (!checkUrl($link_form)) {
    echo "File not found: " . $file_path;
    exit;
  } 
  $file_path = $link_form;
}
?>
<div class="cover"></div>
<iframe src="<?= $file_path; ?>#toolbar=0&navpanes=0" style="width: 100%;height: 68vh;" frameborder="0"></iframe>