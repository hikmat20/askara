<div class="row">
    <!-- LEFT SIDE: Document Preview (60%) -->
    <div class="col-lg-7">
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-file-pdf text-danger mr-2"></i>Document Preview
                    <?php if (isset($form->showing_old_version) && $form->showing_old_version) : ?>
                        <span class="badge badge-warning ml-2">
                            <i class="fa fa-exclamation-triangle"></i> Showing previous version (under revision)
                        </span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($form->display_file_name)) : ?>
                    <?php
                    $file_path = base_url($form->display_file_path ? $form->display_file_path : 'directory/FORMS/1/' . $form->display_file_name);
                    $file_ext = strtolower($form->display_ext ? $form->display_ext : pathinfo($form->display_file_name, PATHINFO_EXTENSION));
                    ?>

                    <?php if ($file_ext === '.pdf' || $file_ext === 'pdf') : ?>
                        <!-- PDF Preview -->
                        <iframe src="<?= $file_path; ?>#toolbar=0"
                            style="width: 100%; height: 600px; border: none;"
                            frameborder="0">
                            <p>Browser Anda tidak mendukung preview PDF.
                                <a href="<?= $file_path; ?>" target="_blank">Klik di sini untuk download</a>
                            </p>
                        </iframe>
                    <?php elseif (in_array($file_ext, ['.xlsx', '.xls', 'xlsx', 'xls'])) : ?>
                        <!-- Excel Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-excel fa-5x text-success mb-3"></i>
                            <h4>Excel Document</h4>
                            <p class=""><?= htmlspecialchars($form->display_file_name); ?></p>
                            <p class="">Size: <?= isset($form->display_size) ? number_format($form->display_size) . ' KB' : '-'; ?></p>
                            <a href="<?= $file_path; ?>" target="_blank" class="btn btn-success">
                                <i class="fa fa-download"></i> Download Excel File
                            </a>
                        </div>
                    <?php elseif (in_array($file_ext, ['.docx', '.doc', 'docx', 'doc'])) : ?>
                        <!-- Word Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-word fa-5x text-primary mb-3"></i>
                            <h4>Word Document</h4>
                            <p class=""><?= htmlspecialchars($form->display_file_name); ?></p>
                            <p class="">Size: <?= isset($form->display_size) ? number_format($form->display_size) . ' KB' : '-'; ?></p>
                            <a href="<?= $file_path; ?>" target="_blank" class="btn btn-primary">
                                <i class="fa fa-download"></i> Download Word File
                            </a>
                        </div>
                    <?php else : ?>
                        <!-- Unknown file type -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file fa-5x text-secondary mb-3"></i>
                            <h4>Document File</h4>
                            <p class=""><?= htmlspecialchars($form->display_file_name); ?></p>
                            <p class="">Size: <?= isset($form->display_size) ? number_format($form->display_size) . ' KB' : '-'; ?></p>
                            <a href="<?= $file_path; ?>" target="_blank" class="btn btn-secondary">
                                <i class="fa fa-download"></i> Download File
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="p-5 text-center">
                        <i class="fa fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                        <h4>No Document File</h4>
                        <p class="">Tidak ada file dokumen yang di-upload untuk Form ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Document Information & History (40%) -->
    <div class="col-lg-5">
        <!-- Document Information Card -->
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-info-circle text-primary mr-2"></i>Document Information
                </h5>
            </div>
            <div class="card-body py-2">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th width="130" class="py-1">Form Number</th>
                        <td class="font-weight-bold py-1">: <?= htmlspecialchars($form->number ? $form->number : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Form Name</th>
                        <td class="font-weight-bold py-1">: <?= htmlspecialchars($form->name ? $form->name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Department</th>
                        <td class="py-1">: <?= htmlspecialchars($form->departement_name ? $form->departement_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Procedure</th>
                        <td class="py-1">: <?= htmlspecialchars($form->procedure_name ? $form->procedure_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Revision Number</th>
                        <td class="py-1">: <span class="badge badge-light-primary"><?= htmlspecialchars($form->revision_number ? $form->revision_number : '0'); ?></span></td>
                    </tr>
                    <tr>
                        <th class="py-1">Current Version</th>
                        <td class="py-1">: <span class="badge badge-info">v<?= htmlspecialchars($form->current_version ? $form->current_version : '1'); ?></span></td>
                    </tr>
                    <tr>
                        <th class="py-1">Status</th>
                        <td class="py-1">: <?= isset($sts[$form->status]) ? $sts[$form->status] : htmlspecialchars($form->status); ?>
                            <?php if (isset($form->is_under_revision) && $form->is_under_revision == 1) : ?>
                                <span class="badge badge-warning ml-2">
                                    <i class="fa fa-sync-alt animate-spin"></i> Under Revision
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- PIC Information Card -->
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-users text-success mr-2"></i>PIC Information
                </h5>
            </div>
            <div class="card-body py-2">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th width="130" class="py-1">PIC Reviewer</th>
                        <td class="py-1">: <?= htmlspecialchars($form->reviewer_position_name ? $form->reviewer_position_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">PIC Approver</th>
                        <td class="py-1">: <?= htmlspecialchars($form->approver_position_name ? $form->approver_position_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-1"></td>
                    </tr>
                    <tr>
                        <th class="py-1">Reviewed By</th>
                        <td class="py-1">: <?= htmlspecialchars(isset($form->reviewed_by_name) ? $form->reviewed_by_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Reviewed At</th>
                        <td class="py-1">: <?= isset($form->reviewed_at) ? date('d M Y H:i', strtotime($form->reviewed_at)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-1"></td>
                    </tr>
                    <tr>
                        <th class="py-1">Approved By</th>
                        <td class="py-1">: <?= htmlspecialchars(isset($form->approved_by_name) ? $form->approved_by_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="py-1">Approved At</th>
                        <td class="py-1">: <?= isset($form->approved_at) ? date('d M Y H:i', strtotime($form->approved_at)) : '-'; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Status History Card -->
        <?php if (!empty($status_logs)) : ?>
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-history text-warning mr-2"></i>Status History
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="30" class="p-2">No</th>
                                <th class="p-2">Status Change</th>
                                <th class="p-2">By</th>
                                <th class="p-2">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $n = 0; foreach ($status_logs as $log) : $n++; ?>
                                <tr>
                                    <td class="p-2 text-center"><?= $n; ?></td>
                                    <td class="p-2">
                                        <small>
                                            <?= isset($sts[$log->old_status]) ? $sts[$log->old_status] : htmlspecialchars($log->old_status); ?>
                                            <i class="fa fa-arrow-right mx-1"></i>
                                            <?= isset($sts[$log->new_status]) ? $sts[$log->new_status] : htmlspecialchars($log->new_status); ?>
                                        </small>
                                        <?php if (!empty($log->note)) : ?>
                                            <br><small><i class="fa fa-comment"></i> <?= htmlspecialchars($log->note); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-2"><small><?= htmlspecialchars($log->action_by_name ? $log->action_by_name : $log->action_by); ?></small></td>
                                    <td class="p-2"><small><?= date('d M Y H:i', strtotime($log->action_at)); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Version History Card -->
        <?php if (!empty($version_history)) : ?>
        <div class="card card-custom shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-code-branch text-info mr-2"></i>Version History
                </h5>
            </div>
            <div class="card-body">
                <?php foreach ($version_history as $version) : ?>
                    <div class="card mb-3 <?= $version->is_current ? 'border-success' : 'border-secondary'; ?>">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1">
                                        <span class="font-weight-bold">v<?= $version->version_number; ?></span>
                                        <?php if ($version->is_current) : ?>
                                            <span class="badge badge-success ml-2">Current</span>
                                        <?php else : ?>
                                            <span class="badge badge-secondary ml-2">Superseded</span>
                                        <?php endif; ?>
                                    </h5>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fa fa-calendar mr-1"></i>
                                    <strong>Published:</strong> <?= date('d M Y', strtotime($version->published_date)); ?>
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fa fa-user mr-1"></i>
                                    <strong>Published by:</strong> <?= htmlspecialchars($version->publisher_name ? $version->publisher_name : '-'); ?>
                                </small>
                            </div>
                            
                            <?php if (!empty($version->description)) : ?>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fa fa-info-circle mr-1"></i>
                                        <strong>Description:</strong> <?= htmlspecialchars($version->description); ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <a href="<?= base_url('forms/download_version/' . $form->id . '/' . $version->version_number); ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fa fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
