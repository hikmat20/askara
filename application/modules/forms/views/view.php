<?php
// Fallbacks for static analysis tools
if (!isset($dataForm)) $dataForm = new stdClass();
if (!isset($display_form)) $display_form = new stdClass();
if (!isset($version_history)) $version_history = [];
if (!isset($status_logs)) $status_logs = [];
if (!isset($reviewed_by_name)) $reviewed_by_name = '';
if (!isset($approved_by_name)) $approved_by_name = '';
if (!isset($sts)) $sts = [];

// Robust type coercion for static analysis
$df = (object)$dataForm;
$disp = (object)$display_form;
$v_hist = (array)$version_history;
$s_logs = (array)$status_logs;

$file_path = '';
if ($disp->form_type == 'upload_file') {
  if (isset($disp->is_from_history) && $disp->is_from_history) {
    $file_path = base_url($disp->file_path);
  } else {
    $file_path = base_url('directory/FORMS/' . $df->company_id . '/' . $disp->file_name);
  }
} else {
  // Validate URL
  if (filter_var($disp->link_form, FILTER_VALIDATE_URL)) {
    $file_path = $disp->link_form;
  } else {
    $file_path = '';
  }
}
?>
<div class="row">
    <!-- LEFT SIDE: Document Preview (60%) -->
    <div class="col-lg-7">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title font-weight-bolder">
                    <i class="fa fa-file-pdf text-danger mr-2"></i>Document Preview
                    <?php if (isset($disp->under_revision) && $disp->under_revision) : ?>
                        <span class="badge badge-warning ml-2">
                            <i class="fa fa-exclamation-triangle"></i> Showing previous version (under revision)
                        </span>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($file_path)) : ?>
                    <?php 
                    $file_ext = strtolower($disp->ext ? $disp->ext : pathinfo($disp->file_name, PATHINFO_EXTENSION));
                    ?>
                    
                    <?php if ($disp->form_type !== 'upload_file') : ?>
                        <!-- Link Form iframe -->
                        <iframe src="<?= $file_path; ?>" 
                                style="width: 100%; height: 800px; border: none;" 
                                frameborder="0">
                        </iframe>
                    <?php elseif ($file_ext === '.pdf' || $file_ext === 'pdf') : ?>
                        <!-- PDF Preview -->
                        <iframe src="<?= $file_path; ?>#toolbar=0&navpanes=0" 
                                style="width: 100%; height: 800px; border: none;" 
                                frameborder="0">
                            <p>Browser Anda tidak mendukung preview PDF. 
                                <a href="<?= base_url('forms/download/' . $df->id); ?>" target="_blank">Klik di sini untuk download</a>
                            </p>
                        </iframe>
                    <?php elseif (in_array($file_ext, ['.xlsx', '.xls', 'xlsx', 'xls'])) : ?>
                        <!-- Excel Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-excel fa-5x text-success mb-3"></i>
                            <h4>Excel Document</h4>
                            <p class=""><?= htmlspecialchars($disp->file_name); ?></p>
                            <p class="">Size: <?= isset($disp->size) ? number_format($disp->size) . ' KB' : '-'; ?></p>
                            <a href="<?= base_url('forms/download/' . $df->id); ?>" target="_blank" class="btn btn-success">
                                <i class="fa fa-download"></i> Download Excel File
                            </a>
                        </div>
                    <?php elseif (in_array($file_ext, ['.docx', '.doc', 'docx', 'doc'])) : ?>
                        <!-- Word Preview -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file-word fa-5x text-primary mb-3"></i>
                            <h4>Word Document</h4>
                            <p class=""><?= htmlspecialchars($disp->file_name); ?></p>
                            <p class="">Size: <?= isset($disp->size) ? number_format($disp->size) . ' KB' : '-'; ?></p>
                            <a href="<?= base_url('forms/download/' . $df->id); ?>" target="_blank" class="btn btn-primary">
                                <i class="fa fa-download"></i> Download Word File
                            </a>
                        </div>
                    <?php else : ?>
                        <!-- Unknown file type -->
                        <div class="p-5 text-center">
                            <i class="fa fa-file fa-5x text-secondary mb-3"></i>
                            <h4>Document File</h4>
                            <p class=""><?= htmlspecialchars($disp->file_name); ?></p>
                            <p class="">Size: <?= isset($disp->size) ? number_format($disp->size) . ' KB' : '-'; ?></p>
                            <a href="<?= base_url('forms/download/' . $df->id); ?>" target="_blank" class="btn btn-secondary">
                                <i class="fa fa-download"></i> Download File
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="p-5 text-center">
                        <i class="fa fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                        <h4>No File or Link</h4>
                        <p class="">Tidak ada file atau link yang valid untuk form ini.</p>
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
                        <td class="font-weight-bold">: <?= htmlspecialchars($df->number ? $df->number : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Document Name</th>
                        <td class="font-weight-bold">: <?= htmlspecialchars($df->name ? $df->name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Department</th>
                        <td>: <?= htmlspecialchars($df->departement_name ? $df->departement_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">Issue Date</th>
                        <td>: <?= isset($df->issue_date) ? date('d M Y', strtotime($df->issue_date)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th class="">Effective Date</th>
                        <td>: <?= isset($df->effective_date) ? date('d M Y', strtotime($df->effective_date)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <th class="">Revision Number</th>
                        <td>: <span class="badge badge-light-primary"><?= htmlspecialchars($df->revision_number ? $df->revision_number : '0'); ?></span></td>
                    </tr>
                    <tr>
                        <th class="">Current Version</th>
                        <td>: <span class="badge badge-info">v<?= htmlspecialchars($df->current_version ? $df->current_version : '1'); ?></span></td>
                    </tr>
                    <tr>
                        <th class="">Status</th>
                        <td>: <?= isset($sts[$df->status]) ? $sts[$df->status] : htmlspecialchars($df->status); ?>
                            <?php if (isset($df->is_under_revision) && $df->is_under_revision == 1) : ?>
                                <span class="badge badge-warning ml-2">
                                    <i class="fa fa-sync-alt animate-spin"></i> Under Revision
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if (isset($df->published_date) && $df->status === 'PUB') : ?>
                    <tr>
                        <th class="">Published Date</th>
                        <td>: <?= date('d M Y', strtotime($df->published_date)); ?></td>
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
                        <td>: <?= htmlspecialchars($df->reviewer_position_name ? $df->reviewer_position_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <th class="">PIC Approver</th>
                        <td>: <?= htmlspecialchars($df->approver_position_name ? $df->approver_position_name : '-'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-2"></td>
                    </tr>
                    <tr>
                        <th class="">Reviewed By</th>
                        <td>: <?= htmlspecialchars($reviewed_by_name); ?></td>
                    </tr>
                    <tr>
                        <th class="">Reviewed At</th>
                        <td>: <?= isset($df->reviewed_at) ? date('d M Y H:i', strtotime($df->reviewed_at)) : '-'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-2"></td>
                    </tr>
                    <tr>
                        <th class="">Approved By</th>
                        <td>: <?= htmlspecialchars($approved_by_name); ?></td>
                    </tr>
                    <tr>
                        <th class="">Approved At</th>
                        <td>: <?= isset($df->approved_at) ? date('d M Y H:i', strtotime($df->approved_at)) : '-'; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Status History Card -->
        <?php if (!empty($s_logs)) : ?>
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
                            <?php $n = 0; foreach ($s_logs as $log) : $n++; $lg = (object)$log; ?>
                                <tr>
                                    <td class="p-2 text-center"><?= $n; ?></td>
                                    <td class="p-2">
                                        <small>
                                            <?= isset($sts[$lg->old_status]) ? $sts[$lg->old_status] : htmlspecialchars($lg->old_status); ?>
                                            <i class="fa fa-arrow-right mx-1"></i>
                                            <?= isset($sts[$lg->new_status]) ? $sts[$lg->new_status] : htmlspecialchars($lg->new_status); ?>
                                        </small>
                                        <?php if (!empty($lg->note)) : ?>
                                            <br><small class=""><i class="fa fa-comment"></i> <?= htmlspecialchars($lg->note); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-2"><small><?= htmlspecialchars($lg->action_by_name ? $lg->action_by_name : $lg->action_by); ?></small></td>
                                    <td class="p-2"><small><?= date('d M Y H:i', strtotime($lg->action_at)); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Version History Card -->
        <?php if (!empty($v_hist)) : ?>
        <div class="card card-custom shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bolder">
                    <i class="fa fa-code-branch text-info mr-2"></i>Version History
                </h3>
            </div>
            <div class="card-body">
                <?php foreach ($v_hist as $version) : $v_node = (object)$version; ?>
                    <div class="card mb-3 <?= $v_node->is_current ? 'border-success' : 'border-secondary'; ?>">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1">
                                        <span class="font-weight-bold">v<?= $v_node->version_number; ?></span>
                                        <?php if ($v_node->is_current) : ?>
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
                                    <strong>Published:</strong> <?= date('d M Y', strtotime($v_node->published_date)); ?>
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fa fa-user mr-1"></i>
                                    <strong>Published by:</strong> <?= htmlspecialchars($v_node->publisher_name ? $v_node->publisher_name : '-'); ?>
                                </small>
                            </div>
                            
                            <?php if (!empty($v_node->description)) : ?>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fa fa-info-circle mr-1"></i>
                                        <strong>Description:</strong> <?= htmlspecialchars($v_node->description); ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <button type="button" 
                                        class="btn btn-sm btn-info btn-view-version" 
                                        data-form-id="<?= $df->id; ?>" 
                                        data-version="<?= $v_node->version_number; ?>">
                                    <i class="fa fa-eye"></i> View
                                </button>
                                <a href="<?= base_url('forms/download_version/' . $df->id . '/' . $v_node->version_number); ?>" 
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
    // Handle View Version button click (using event delegation)
    $(document).on('click', '.btn-view-version', function() {
        var formId = $(this).data('form-id');
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
            url: '<?= base_url('forms/view_version/'); ?>' + formId + '/' + version,
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