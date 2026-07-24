<?php
$file_ext = strtolower(pathinfo($form->file_name, PATHINFO_EXTENSION));
$is_pdf = ($file_ext === 'pdf');
$download_url = base_url('forms/download/' . $form->id);
?>
<div class="mb-3 d-flex justify-content-between align-items-center">
    <span class="text-muted">Document: <strong><?= htmlspecialchars($form->name); ?></strong></span>
    <?php if ($allow_download_form): ?>
    <a href="<?= $download_url; ?>" class="btn btn-sm btn-primary">
        <i class="fa fa-download"></i> Download Form
    </a>
    <?php endif; ?>
</div>
<?php if ($form->form_type !== 'upload_file') : ?>
    <iframe class="w-100" style="height: 70vh;" src="<?= $form->link_form; ?>" frameborder="0"></iframe>
<?php elseif ($is_pdf) : ?>
    <iframe class="w-100" style="height: 70vh;" src="<?= base_url('directory/FORMS/' . $form->company_id . '/' . $form->file_name); ?>#toolbar=0&navpanes=0" frameborder="0"></iframe>
<?php elseif (in_array($file_ext, ['xlsx', 'xls', 'docx', 'doc'])) : ?>
    <div class="alert alert-info mb-3">
        <i class="fa fa-info-circle mr-2"></i>
        Jika dokumen tidak muncul, pasang ekstensi <a
            href="https://chromewebstore.google.com/detail/office-editing-for-docs-s/gbkeegbaiigmenfmjfclcdgdpimamgkj"
            target="_blank" class="font-weight-bold text-dark">Office Editing</a> di browser Chrome Anda,
        atau unduh dokumen secara manual.
    </div>
    <iframe class="w-100" style="height: 70vh;" src="<?= base_url('forms/view_file/' . $form->id); ?>" frameborder="0">
    </iframe>
<?php else : ?>
    <div class="p-5 text-center bg-light rounded border">
        <i class="fa fa-file-alt fa-4x text-secondary mb-3"></i>
        <h5>Document File</h5>
        <p class="text-muted">Dokumen ini tidak mendukung preview langsung di browser.</p>
        <?php if ($allow_download_form): ?>
        <a href="<?= $download_url; ?>" class="btn btn-success">
            <i class="fa fa-download"></i> Download File
        </a>
        <?php else: ?>
            <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted</span>
        <?php endif; ?>
    </div>
<?php endif; ?>