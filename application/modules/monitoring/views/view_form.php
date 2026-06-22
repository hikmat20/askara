<?php
$file_ext = strtolower(pathinfo($form->file_name, PATHINFO_EXTENSION));
$is_pdf = ($file_ext === 'pdf');
$download_url = base_url('forms/download/' . $form->id);
?>
<div class="mb-3 d-flex justify-content-between align-items-center">
    <span class="text-muted">Document: <strong><?= htmlspecialchars($form->name); ?></strong></span>
    <a href="<?= $download_url; ?>" class="btn btn-sm btn-primary">
        <i class="fa fa-download"></i> Download Form
    </a>
</div>
<?php if ($form->form_type !== 'upload_file') : ?>
    <iframe class="w-100" style="height: 70vh;" src="<?= $form->link_form; ?>" frameborder="0"></iframe>
<?php elseif ($is_pdf) : ?>
    <iframe class="w-100" style="height: 70vh;" src="<?= base_url('directory/FORMS/' . $form->company_id . '/' . $form->file_name); ?>#toolbar=0&navpanes=0" frameborder="0"></iframe>
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