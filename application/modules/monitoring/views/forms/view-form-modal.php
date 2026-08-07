<div class="row">
    <!-- LEFT SIDE: Document Preview (60%) -->
    <div class="col-lg-7">
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-file-pdf text-danger mr-2"></i>Document Preview
                    <?php if (isset($form->showing_old_version) && $form->showing_old_version) : ?>
                        <span class="badge badge-warning ml-2">
                            <i class="fa fa-exclamation-triangle"></i> Showing previous version (under revision)
                        </span>
                    <?php endif; ?>
                </h5>
                <?php if (!empty($form->file_name) && $allow_download_form) : ?>
                    <div class="card-toolbar">
                        <a href="<?= base_url('forms/download/' . $form->id); ?>" class="btn btn-sm btn-light-primary">
                            <i class="fa fa-download"></i> Download
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($form->file_name)): ?>
                    <?php
                    $file_path = base_url('forms/view_file/' . $form->id);
                    $file_ext = strtolower($form->ext ? $form->ext : pathinfo($form->file_name, PATHINFO_EXTENSION));
                    ?>
                
                    <?php if ($file_ext === '.pdf' || $file_ext === 'pdf'): ?>
                        <!-- PDF Preview -->
                        <iframe src="<?= $file_path; ?>#toolbar=0" style="width: 100%; height: 600px; border: none;" frameborder="0">
                            <p>Browser Anda tidak mendukung preview PDF.
                                <?php if ($allow_download_form) : ?>
                                    <a href="<?= base_url('forms/download/' . $form->id); ?>" target="_blank">Klik di sini untuk download</a>
                                <?php endif; ?>
                            </p>
                        </iframe>
                    <?php elseif (in_array($file_ext, ['.xlsx', '.xls', 'xlsx', 'xls', '.docx', '.doc', 'docx', 'doc'])) : ?>
                        <!-- Office Preview via iframe (Chrome Office Editing extension) -->
                        <div class="alert alert-info m-3">
                            <i class="fa fa-info-circle mr-2"></i>
                            Jika dokumen tidak muncul, pasang ekstensi <a
                                href="https://chromewebstore.google.com/detail/office-editing-for-docs-s/gbkeegbaiigmenfmjfclcdgdpimamgkj"
                                target="_blank" class="font-weight-bold text-dark">Office Editing</a> di browser Chrome Anda,
                            atau <a href="<?= base_url('forms/download/' . $form->id); ?>" target="_blank" class="font-weight-bold text-dark">unduh dokumen secara manual di sini</a>.
                        </div>
                        <iframe src="<?= base_url('forms/view_file/' . $form->id); ?>"
                            style="width: 100%; height: 600px; border: none;"
                            frameborder="0">
                        </iframe>
                    <?php else: ?>
                        <!-- Unknown file type -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file fa-5x text-secondary mb-3"></i>
                            <h4>Document File</h4>
                            <p class=""><?= htmlspecialchars($form->file_name); ?></p>
                            <p class="">Size: <?= isset($form->size) ? number_format($form->size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download_form) : ?>
                                <a href="<?= base_url('forms/download/' . $form->id); ?>" class="btn btn-secondary">
                                    <i class="fa fa-download"></i> Download File
                                </a>
                            <?php else : ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted to Admin</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="p-5 text-center">
                        <i class="fa fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                        <h4>No Document File</h4>
                        <p class="">Tidak ada file dokumen yang di-upload untuk Work Instruction ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Status History Card -->
        <?php if (!empty($status_logs)) : ?>
        <div class="card card-custom shadow-sm mb-3 mt-3">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-history text-warning mr-2"></i>Status History
                </h5>
                <div class="card-toolbar">
                    <a href="<?= base_url('forms/export_history_excel/' . $form->id); ?>" class="btn btn-sm btn-success mr-2">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                    <a href="<?= base_url('forms/export_history_pdf/' . $form->id); ?>" target="_blank" class="btn btn-sm btn-danger">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </div>
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



        <!-- Version History Card -->
        <?php if (!empty($version_history)) : ?>
        <div class="card card-custom shadow-sm">
            <div class="card-header py-2">
                <h5 class="card-title font-weight-bolder mb-0">
                    <i class="fa fa-code-branch text-info mr-2"></i>Version History
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                <?php foreach ($version_history as $version) : $v_node = (object)$version; ?>
                    <div class="col-12 mb-3">
                        <div class="border rounded p-3 <?= $v_node->is_current ? 'border-success bg-light-success' : 'border-secondary'; ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1 font-weight-bold">
                                        v<?= $v_node->version_number; ?>
                                        <?php if ($v_node->is_current) : ?>
                                            <span class="badge badge-success ml-2">Current</span>
                                        <?php else : ?>
                                            <span class="badge badge-secondary ml-2">Superseded</span>
                                        <?php endif; ?>
                                    </h6>
                                </div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted"><i class="fa fa-calendar mr-1"></i> Published: <?= date('d M Y', strtotime($v_node->published_date)); ?></small><br>
                                <small class="text-muted"><i class="fa fa-user mr-1"></i> By: <?= htmlspecialchars($v_node->publisher_name ? $v_node->publisher_name : '-'); ?></small>
                                <?php if (!empty($v_node->description)) : ?>
                                    <br><small class="text-muted"><i class="fa fa-info-circle mr-1"></i> Note: <?= htmlspecialchars($v_node->description); ?></small>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if (isset($allow_download_form) && $allow_download_form): ?>
                                <a href="<?= base_url('forms/download_version/' . $form->id . '/' . $v_node->version_number); ?>" 
                                   class="btn btn-sm btn-primary py-1 px-2">
                                    <i class="fa fa-download"></i> Download
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
