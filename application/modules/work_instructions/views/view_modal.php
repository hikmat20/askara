<div class="row">
        <!-- LEFT SIDE: Document Preview (60%) -->
    <div class="col-lg-7">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title font-weight-bolder">
                    <i class="fa fa-file-pdf text-danger mr-2"></i>Document Preview
                    <?php if (isset($wi->showing_old_version) && $wi->showing_old_version) : ?>
                        <span class="badge badge-warning ml-2">
                            <i class="fa fa-exclamation-triangle"></i> Showing previous version (under revision)
                        </span>
                    <?php endif; ?>
                </h3>
                <?php if (!empty($wi->display_file_name) && $allow_download) : ?>
                    <div class="card-toolbar">
                        <a href="<?= base_url('work_instructions/download/' . $wi->id); ?>" target="_blank" class="btn btn-sm btn-light-primary">
                            <i class="fa fa-download"></i> Download
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($wi->display_file_name)) : ?>
                    <?php 
                    $file_path = base_url($wi->display_file_path);
                    $file_ext = strtolower($wi->display_ext ? $wi->display_ext : pathinfo($wi->display_file_name, PATHINFO_EXTENSION));
                    ?>
                    
                    <?php if ($file_ext === '.pdf' || $file_ext === 'pdf') : ?>
                        <!-- PDF Preview -->
                        <iframe src="<?= $file_path; ?>#toolbar=0&navpanes=0" 
                                style="width: 100%; height: 800px; border: none;" 
                                frameborder="0">
                            <p>Browser Anda tidak mendukung preview PDF. 
                                <?php if ($allow_download) : ?>
                                    <a href="<?= base_url('work_instructions/download/' . $wi->id); ?>" target="_blank">Klik di sini untuk download</a>
                                <?php endif; ?>
                            </p>
                        </iframe>
                    <?php elseif (in_array($file_ext, ['.xlsx', '.xls', 'xlsx', 'xls'])) : ?>
                        <!-- Excel Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-excel fa-5x text-success mb-3"></i>
                            <h4>Excel Document</h4>
                            <p class="text-muted"><?= htmlspecialchars($wi->display_file_name); ?></p>
                            <p class="text-muted">Size: <?= isset($wi->display_size) ? number_format($wi->display_size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download) : ?>
                                <a href="<?= base_url('work_instructions/download/' . $wi->id); ?>" target="_blank" class="btn btn-success">
                                    <i class="fa fa-download"></i> Download Excel File
                                </a>
                            <?php else : ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted to Admin</span>
                            <?php endif; ?>
                        </div>
                    <?php elseif (in_array($file_ext, ['.docx', '.doc', 'docx', 'doc'])) : ?>
                        <!-- Word Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-word fa-5x text-primary mb-3"></i>
                            <h4>Word Document</h4>
                            <p class="text-muted"><?= htmlspecialchars($wi->display_file_name); ?></p>
                            <p class="text-muted">Size: <?= isset($wi->display_size) ? number_format($wi->display_size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download) : ?>
                                <a href="<?= base_url('work_instructions/download/' . $wi->id); ?>" target="_blank" class="btn btn-primary">
                                    <i class="fa fa-download"></i> Download Word File
                                </a>
                            <?php else : ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted to Admin</span>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <!-- Unknown file type -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file fa-5x text-secondary mb-3"></i>
                            <h4>Document File</h4>
                            <p class="text-muted"><?= htmlspecialchars($wi->display_file_name); ?></p>
                            <p class="text-muted">Size: <?= isset($wi->display_size) ? number_format($wi->display_size) . ' KB' : '-'; ?></p>
                            <?php if ($allow_download) : ?>
                                <a href="<?= base_url('work_instructions/download/' . $wi->id); ?>" target="_blank" class="btn btn-secondary">
                                    <i class="fa fa-download"></i> Download File
                                </a>
                            <?php else : ?>
                                <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted to Admin</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="p-5 text-center">
                        <i class="fa fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                        <h4>No Document File</h4>
                        <p class="">Tidak ada file dokumen yang di-upload untuk Work Instruction ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Document Information (40%) -->
    <div class="col-lg-5">
        <!-- Document Information Card -->
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title font-weight-bolder">
                    <i class="fa fa-info-circle text-primary mr-2"></i>Document Information
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="140" class="">Document Number</th>
                        <td class="font-weight-bold">: <?= htmlspecialchars($wi->number ? $wi->number : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Document Name</th>
                        <td class="font-weight-bold">: <?= htmlspecialchars($wi->name ? $wi->name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Department</th>
                        <td>: <?= htmlspecialchars($wi->departement_name ? $wi->departement_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Procedure</th>
                        <td>: <?= htmlspecialchars($wi->procedure_name ? $wi->procedure_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Issue Date</th>
                        <td>: <?= isset($wi->issue_date) ? date('d M Y', strtotime($wi->issue_date)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th class="">Effective Date</th>
                        <td>: <?= isset($wi->effective_date) ? date('d M Y', strtotime($wi->effective_date)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th class="">Revision Number</th>
                        <td>: <span class="badge badge-light-primary"><?= htmlspecialchars($wi->revision_number ? $wi->revision_number : '0'); ?></span></td>
                    </tr>
                    <tr>
                        <th class="">Current Version</th>
                        <td>: <span class="badge badge-info">v<?= htmlspecialchars($wi->current_version ? $wi->current_version : '1'); ?></span></td>
                    </tr>
                    <tr>
                        <th class="">Status</th>
                        <td>: <?= isset($sts[$wi->status]) ? $sts[$wi->status] : htmlspecialchars($wi->status); ?>
                            <?php if (isset($wi->is_under_revision) && $wi->is_under_revision == 1) : ?>
                                <span class="badge badge-warning ml-2">
                                    <i class="fa fa-sync-alt"></i> Under Revision
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if (isset($wi->published_date) && $wi->status === 'PUB') : ?>
                    <tr>
                        <th class="">Published Date</th>
                        <td>: <?= date('d M Y', strtotime($wi->published_date)); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- PIC Information Card -->
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title font-weight-bolder">
                    <i class="fa fa-users text-success mr-2"></i>PIC Information
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="140" class="">PIC Reviewer</th>
                        <td>: <?= htmlspecialchars($wi->reviewer_position_name ? $wi->reviewer_position_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">PIC Approver</th>
                        <td>: <?= htmlspecialchars($wi->approver_position_name ? $wi->approver_position_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-2"></td>
                    </tr>
                    <tr>
                        <th class="">Reviewed By</th>
                        <td>: <?= htmlspecialchars(isset($wi->reviewed_by_name) ? $wi->reviewed_by_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Reviewed At</th>
                        <td>: <?= isset($wi->reviewed_at) ? date('d M Y H:i', strtotime($wi->reviewed_at)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-2"></td>
                    </tr>
                    <tr>
                        <th class="">Approved By</th>
                        <td>: <?= htmlspecialchars(isset($wi->approved_by_name) ? $wi->approved_by_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Approved At</th>
                        <td>: <?= isset($wi->approved_at) ? date('d M Y H:i', strtotime($wi->approved_at)) : '-'; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Status History Card -->
        <?php if (!empty($status_logs)) : ?>
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title font-weight-bolder">
                    <i class="fa fa-history text-warning mr-2"></i>Status History
                </h3>
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
                                            <br><small class=""><i class="fa fa-comment"></i> <?= htmlspecialchars($log->note); ?></small>
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
        <div class="card card-custom shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title font-weight-bolder">
                    <i class="fa fa-code-branch text-info mr-2"></i>Version History
                </h3>
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
                                <button type="button" 
                                        class="btn btn-sm btn-info btn-view-version" 
                                        data-wi-id="<?= $wi->id; ?>" 
                                        data-version="<?= $version->version_number; ?>">
                                    <i class="fa fa-eye"></i> View
                                </button>
                                <?php if ($allow_download) : ?>
                                    <a href="<?= base_url('work_instructions/download_version/' . $wi->id . '/' . $version->version_number); ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                <?php else : ?>
                                    <span class="badge badge-secondary"><i class="fa fa-lock"></i> Download Restricted to Admin</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>


    </div>
</div>


<!-- Modal untuk View Version -->
<div class="modal fade" id="modal-view-version" tabindex="-1" role="dialog" aria-labelledby="modalViewVersionLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalViewVersionLabel">
                    <i class="fa fa-code-branch text-info mr-2"></i>View Document Version
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-view-version-body">
                <div class="text-center p-5">
                    <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">Loading version...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle View Version button click
    $('.btn-view-version').on('click', function() {
        var wiId = $(this).data('wi-id');
        var version = $(this).data('version');
        
        // Show modal
        $('#modal-view-version').modal('show');
        
        // Reset modal body to loading state
        $('#modal-view-version-body').html(`
            <div class="text-center p-5">
                <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                <p class="mt-3">Loading version...</p>
            </div>
        `);
        
        // Load version content via AJAX
        $.ajax({
            url: '<?= base_url('work_instructions/view_version/'); ?>' + wiId + '/' + version,
            type: 'GET',
            success: function(response) {
                $('#modal-view-version-body').html(response);
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Failed to load version.';
                
                if (xhr.status === 403) {
                    errorMsg = 'Access Denied: You do not have permission to access this document.';
                } else if (xhr.status === 404) {
                    errorMsg = 'Version not found: The requested version does not exist or file has been deleted.';
                } else if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                }
                
                $('#modal-view-version-body').html(`
                    <div class="alert alert-danger m-3">
                        <i class="fa fa-exclamation-triangle mr-2"></i>
                        <strong>Error:</strong> ${errorMsg}
                    </div>
                `);
            }
        });
    });
});
</script>
