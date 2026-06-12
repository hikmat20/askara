<?php
/**
 * Record Audit - Read-Only Archive View
 *
 * Variables from controller:
 * - $header (object): audit header info
 * - $ca (object): corrective action record
 * - $temuan (array): list of temuan objects
 * - $details (array): corrective action detail objects
 * - $files (array): evidence file objects
 */

// Build lookup maps for details and files by temuan_id
$detail_map = [];
foreach ($details as $d) {
    $detail_map[$d->temuan_id] = $d;
}

$file_map = [];
foreach ($files as $f) {
    if (!isset($file_map[$f->ca_detail_id])) {
        $file_map[$f->ca_detail_id] = [];
    }
    $file_map[$f->ca_detail_id][] = $f;
}
?>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-stretch shadow card-custom">
                <div class="card-header justify-content-between d-flex align-items-center">
                    <h2 class="m-0"><i class="<?= $icon; ?> text-primary mr-2"></i>Record Audit</h2>
                    <a href="javascript:history.back()" class="btn btn-danger"><i class="fa fa-reply"></i> Kembali</a>
                </div>

                <div class="card-body">
                    <!-- ================ HEADER INFO ================ -->
                    <div class="mb-4">
                        <h5 class="font-weight-bold border-bottom pb-2"><i class="fa fa-calendar-alt text-primary mr-2"></i><span class="text-primary">Header</span></h5>
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th width="200">Prosedur</th>
                                <td><?= !empty($header->process_name) ? strip_tags($header->process_name) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td><?= !empty($header->audit_date) ? date('d/m/Y', strtotime($header->audit_date)) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Department</th>
                                <td><?= !empty($header->department_name) ? htmlspecialchars($header->department_name) : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Auditor</th>
                                <td><?= !empty($header->auditor_name) ? htmlspecialchars($header->auditor_name) : '-'; ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- ================ TEMUAN DETAILS ================ -->
                    <?php if (!empty($temuan)) : ?>
                        <?php foreach ($temuan as $k => $t) :
                            $n = $k + 1;
                            $detail = isset($detail_map[$t->id]) ? $detail_map[$t->id] : null;
                            $temuan_files = ($detail && isset($file_map[$detail->id])) ? $file_map[$detail->id] : [];

                            // Kategori badge color
                            $kat_class = 'secondary';
                            if (isset($t->kategori)) {
                                switch ($t->kategori) {
                                    case 'Minor': $kat_class = 'warning'; break;
                                    case 'Major': $kat_class = 'danger'; break;
                                    case 'OK': $kat_class = 'success'; break;
                                    case 'OFI': $kat_class = 'info'; break;
                                }
                            }
                        ?>
                            <div class="card card-custom mb-4 border">
                                <div class="card-header bg-light d-flex align-items-center justify-content-between py-3">
                                    <h5 class="m-0 font-weight-bold">
                                        Temuan Audit #<?= $n; ?>
                                        <?php if (!empty($t->kategori)) : ?>
                                            <span class="badge badge-<?= $kat_class; ?> ml-2"><?= htmlspecialchars($t->kategori); ?></span>
                                        <?php endif; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Temuan Description -->
                                    <div class="mb-3">
                                        <label class="font-weight-bold text-dark">Temuan:</label>
                                        <p class="mb-0"><?= !empty($t->description) ? nl2br(htmlspecialchars($t->description)) : '-'; ?></p>
                                    </div>

                                    <!-- Fakta -->
                                    <div class="mb-3">
                                        <label class="font-weight-bold text-dark">Fakta:</label>
                                        <p class="mb-0"><?= ($detail && !empty($detail->fakta)) ? nl2br(htmlspecialchars($detail->fakta)) : '-'; ?></p>
                                    </div>

                                    <!-- Kesimpulan Penyebab -->
                                    <div class="mb-3">
                                        <label class="font-weight-bold text-dark">Kesimpulan Penyebab:</label>
                                        <p class="mb-0"><?= ($detail && !empty($detail->kesimpulan_penyebab)) ? nl2br(htmlspecialchars($detail->kesimpulan_penyebab)) : '-'; ?></p>
                                    </div>

                                    <!-- Correction -->
                                    <div class="mb-3">
                                        <label class="font-weight-bold text-dark">Correction:</label>
                                        <p class="mb-0"><?= ($detail && !empty($detail->correction)) ? nl2br(htmlspecialchars($detail->correction)) : '-'; ?></p>
                                    </div>

                                    <!-- Corrective Action -->
                                    <div class="mb-3">
                                        <label class="font-weight-bold text-dark">Corrective Action:</label>
                                        <p class="mb-0"><?= ($detail && !empty($detail->corrective_action)) ? nl2br(htmlspecialchars($detail->corrective_action)) : '-'; ?></p>
                                    </div>

                                    <!-- Evidence Files -->
                                    <div class="mb-0">
                                        <label class="font-weight-bold text-dark">Evidence:</label>
                                        <?php if (!empty($temuan_files)) : ?>
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach ($temuan_files as $file) : ?>
                                                    <li>
                                                        <a href="<?= site_url('corrective_action/download/' . $file->id); ?>" class="text-primary">
                                                            <i class="fa fa-paperclip mr-1"></i><?= htmlspecialchars($file->file_name_original); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else : ?>
                                            <p class="mb-0 text-muted"><em>Tidak ada file evidence.</em></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="text-muted"><em>Tidak ada temuan audit.</em></p>
                    <?php endif; ?>

                    <!-- ================ END ================ -->
                </div>
            </div>
        </div>
    </div>
</div>
