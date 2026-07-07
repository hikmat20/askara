<?php
// Robust type coercion for static analysis
if (!isset($version)) {
    $version = new stdClass();
}
$v = (object)$version;
?>
<div class="row">
    <!-- LEFT SIDE: Document Preview (60%) -->
    <div class="col-lg-7">
        <div class="card card-custom mb-0">
            <div class="card-header">
                <h3 class="card-title font-weight-bolder">
                    <i class="fa fa-file-pdf text-danger mr-2"></i>Document Preview
                </h3>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($v->file_name)) : ?>
                    <?php 
                    $file_path = base_url($v->file_path);
                    $file_ext = strtolower($v->ext ? $v->ext : pathinfo($v->file_name, PATHINFO_EXTENSION));
                    ?>
                    
                    <?php if ($file_ext === '.pdf' || $file_ext === 'pdf') : ?>
                        <!-- PDF Preview -->
                        <iframe src="<?= $file_path; ?>#toolbar=0&navpanes=0" 
                                style="width: 100%; height: 600px; border: none;" 
                                frameborder="0">
                            <p>Browser Anda tidak mendukung preview PDF. 
                                <?php if ($allow_download): ?>
                                <a href="<?= $file_path; ?>" target="_blank">Klik di sini untuk download</a>
                                <?php endif; ?>
                            </p>
                        </iframe>
                    <?php elseif (in_array($file_ext, ['.xlsx', '.xls', 'xlsx', 'xls'])) : ?>
                        <!-- Excel Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-excel fa-5x text-success mb-3"></i>
                            <h4>Excel Document</h4>
                            <p class=""><?= htmlspecialchars($v->file_name); ?></p>
                            <p class="">Size: <?= isset($v->size) ? number_format($v->size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download): ?>
                            <a href="<?= $file_path; ?>" target="_blank" class="btn btn-success">
                                <i class="fa fa-download"></i> Download Excel File
                            </a>
                            <?php else : ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted</span>
                            <?php endif; ?>
                        </div>
                    <?php elseif (in_array($file_ext, ['.docx', '.doc', 'docx', 'doc'])) : ?>
                        <!-- Word Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-word fa-5x text-primary mb-3"></i>
                            <h4>Word Document</h4>
                            <p class=""><?= htmlspecialchars($v->file_name); ?></p>
                            <p class="">Size: <?= isset($v->size) ? number_format($v->size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download): ?>
                            <a href="<?= $file_path; ?>" target="_blank" class="btn btn-primary">
                                <i class="fa fa-download"></i> Download Word File
                            </a>
                            <?php else : ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted</span>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <!-- Unknown file type -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file fa-5x text-secondary mb-3"></i>
                            <h4>Document File</h4>
                            <p class=""><?= htmlspecialchars($v->file_name); ?></p>
                            <p class="">Size: <?= isset($v->size) ? number_format($v->size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download): ?>
                            <a href="<?= $file_path; ?>" target="_blank" class="btn btn-secondary">
                                <i class="fa fa-download"></i> Download File
                            </a>
                            <?php else : ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="p-5 text-center">
                        <i class="fa fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                        <h4>No Document File</h4>
                        <p class="">File tidak ditemukan untuk versi ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Version Information (40%) -->
    <div class="col-lg-5">
        <!-- Version Information Card -->
        <div class="card card-custom shadow-sm mb-0">
            <div class="card-header">
                <h3 class="card-title font-weight-bolder">
                    <i class="fa fa-info-circle text-primary mr-2"></i>Version Information
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="140" class="">Version Number</th>
                        <td class="font-weight-bold">: 
                            <span class="badge badge-info">v<?= htmlspecialchars($v->version_number); ?></span>
                            <?php if ($v->is_current) : ?>
                                <span class="badge badge-success ml-2">Current</span>
                            <?php else : ?>
                                <span class="badge badge-secondary ml-2">Superseded</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-2"></td>
                    </tr>
                    <tr>
                        <th class="">Document Number</th>
                        <td>: <?= htmlspecialchars($v->number ? $v->number : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Document Name</th>
                        <td>: <?= htmlspecialchars($v->name ? $v->name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Revision Number</th>
                        <td>: <span class="badge badge-light-primary"><?= htmlspecialchars($v->revision_number ? $v->revision_number : '0'); ?></span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-2"></td>
                    </tr>
                    <tr>
                        <th class="">Published Date</th>
                        <td>: <?= isset($v->published_date) ? date('d M Y', strtotime($v->published_date)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th class="">Published By</th>
                        <td>: <?= htmlspecialchars(isset($v->publisher_name) ? $v->publisher_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Published At</th>
                        <td>: <?= isset($v->published_at) ? date('d M Y H:i', strtotime($v->published_at)) : '-'; ?></td>
                    </tr>
                    <?php if (!empty($v->description)) : ?>
                    <tr>
                        <td colspan="2"><hr class="my-2"></td>
                    </tr>
                    <tr>
                        <th class="">Description</th>
                        <td>: <?= htmlspecialchars($v->description); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($v->is_current == 0 && isset($v->superseded_at)) : ?>
                    <tr>
                        <td colspan="2"><hr class="my-2"></td>
                    </tr>
                    <tr>
                        <th class="">Superseded At</th>
                        <td>: <?= date('d M Y H:i', strtotime($v->superseded_at)); ?></td>
                    </tr>
                    <tr>
                        <th class="">Superseded By</th>
                        <td>: <span class="badge badge-info">v<?= htmlspecialchars($v->superseded_by); ?></span></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
