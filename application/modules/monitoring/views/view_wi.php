<?php
$file_ext = strtolower(pathinfo($wi->file_name, PATHINFO_EXTENSION));
$is_pdf = ($file_ext === 'pdf');
$download_url = base_url('work_instructions/download/' . $wi->id);
?>
<div class="mb-3 d-flex justify-content-between align-items-center">
    <span class="text-muted">Document: <strong><?= htmlspecialchars($wi->name); ?></strong></span>
    <?php if ($allow_download_wi): ?>
    <a href="<?= $download_url; ?>" class="btn btn-sm btn-primary">
        <i class="fa fa-download"></i> Download WI
    </a>
    <?php endif; ?>
</div>
<?php if ($is_pdf) : ?>
    <iframe class="w-100" style="height: 70vh;" src="<?= base_url($wi->file_path); ?>#toolbar=0&navpanes=0" frameborder="0"></iframe>
<?php else : ?>
    <div class="p-5 text-center bg-light rounded border">
        <i class="fa fa-file-word fa-4x text-primary mb-3"></i>
        <h5>Document File</h5>
        <p class="text-muted">Dokumen ini tidak mendukung preview langsung di browser.</p>
        <?php if ($allow_download_wi): ?>
        <a href="<?= $download_url; ?>" class="btn btn-primary">
            <i class="fa fa-download"></i> Download File
        </a>
        <?php else: ?>
            <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted</span>
        <?php endif; ?>
    </div>
<?php endif; ?>