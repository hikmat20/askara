<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Corrective Action Controller
 *
 * Manages the corrective action workflow for audit findings.
 * Provides sub-modules for CA entry (auditee), approval (approver),
 * and record audit (archive).
 */

class Corrective_action extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('corrective_action/Corrective_action_model', 'model');
        $this->template->set([
            'title' => 'Corrective Action',
            'icon'  => 'fa fa-check-circle'
        ]);
        $this->company = $this->session->company->id_perusahaan;
    }

    // =========================================================================
    // CORRECTIVE ACTION (AUDITEE)
    // =========================================================================

    /**
     * Index - List all pelaksanaan audit that have temuan, with CA status
     */
    public function index()
    {
        $data = $this->model->getIndexData($this->company);
        $this->template->set('data', $data);
        $this->template->render('index');
    }

    /**
     * Form - Create or edit corrective action
     *
     * @param int $pelaksanaan_id ID from pelaksanaan_audit table
     */
    public function form($pelaksanaan_id = null)
    {
        if (!$pelaksanaan_id) {
            show_404();
            return;
        }

        $header = $this->_getAuditHeader($pelaksanaan_id);
        if (!$header) {
            show_404();
            return;
        }

        $ca = $this->model->getCorrectionByPelaksanaan($pelaksanaan_id);

        // Block editing if not Draft
        if ($ca && !in_array($ca->status_ca, ['draft'])) {
            redirect('corrective_action/view/' . $pelaksanaan_id);
            return;
        }

        $temuan  = $this->model->getTemuanByAudit($pelaksanaan_id);
        $details = $ca ? $this->model->getCADetails($ca->id) : [];
        $files   = $ca ? $this->model->getCAFiles($ca->id) : [];

        $this->template->set([
            'header'          => $header,
            'ca'              => $ca,
            'pelaksanaan_id'  => $pelaksanaan_id,
            'temuan'          => $temuan,
            'details'         => $details,
            'files'           => $files,
        ]);
        $this->template->render('form');
    }

    /**
     * Save - AJAX save as Draft
     * POST params: pelaksanaan_id, detail[temuan_id][fakta|penyebab|correction|corrective_action]
     */
    public function save()
    {
        $data = $this->input->post();
        if (!$data || empty($data['pelaksanaan_id'])) {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid. Please try again.']);
            return;
        }

        $data['company_id'] = $this->company;
        $userId = $this->auth->user_id();
        $result = $this->model->saveCorrective($data, $userId);

        if ($result['status'] == 1) {
            // Handle file uploads
            $warnings = $this->_handleFileUploads($result['ca_id'], $this->company);
            $msg = 'Data Corrective Action berhasil disimpan.';
            if (!empty($warnings)) {
                $msg .= ' Warning: ' . implode(', ', $warnings);
            }
            echo json_encode(['status' => 1, 'msg' => $msg, 'ca_id' => $result['ca_id']]);
        } else {
            echo json_encode($result);
        }
    }

    /**
     * Submit - AJAX submit for approval (Draft → Waiting Approval)
     * POST params: ca_id
     */
    public function submit()
    {
        $data = $this->input->post();
        if (!$data || empty($data['ca_id'])) {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid. Please try again.']);
            return;
        }

        $userId = $this->auth->user_id();
        $result = $this->model->submitCorrective($data['ca_id'], $userId);
        echo json_encode($result);
    }

    /**
     * View - Read-only view of corrective action
     *
     * @param int $pelaksanaan_id ID from pelaksanaan_audit table
     */
    public function view($pelaksanaan_id = null)
    {
        if (!$pelaksanaan_id) {
            show_404();
            return;
        }

        $header = $this->_getAuditHeader($pelaksanaan_id);
        if (!$header) {
            show_404();
            return;
        }

        $ca = $this->model->getCorrectionByPelaksanaan($pelaksanaan_id);
        if (!$ca) {
            show_404();
            return;
        }

        $temuan  = $this->model->getTemuanByAudit($pelaksanaan_id);
        $details = $this->model->getCADetails($ca->id);
        $files   = $this->model->getCAFiles($ca->id);

        $this->template->set([
            'header'          => $header,
            'ca'              => $ca,
            'pelaksanaan_id'  => $pelaksanaan_id,
            'temuan'          => $temuan,
            'details'         => $details,
            'files'           => $files,
            'readonly'        => true,
        ]);
        $this->template->render('view');
    }

    // =========================================================================
    // APPROVAL (APPROVER)
    // =========================================================================

    /**
     * Approval Index - List CAs with Waiting Approval and Approved status
     */
    public function approval_index()
    {
        $data = $this->model->getApprovalIndexData();
        $this->template->set('data', $data);
        $this->template->render('approval_index');
    }

    /**
     * Approval Form - Review a submitted corrective action
     *
     * @param string $ca_id The corrective action ID
     */
    public function approval_form($ca_id = null)
    {
        if (!$ca_id) {
            show_404();
            return;
        }

        $ca = $this->db->get_where('corrective_action', ['id' => $ca_id, 'deleted' => '0'])->row();
        if (!$ca) {
            show_404();
            return;
        }

        $header  = $this->_getAuditHeader($ca->pelaksanaan_id);
        $temuan  = $this->model->getTemuanByAudit($ca->pelaksanaan_id);
        $details = $this->model->getCADetails($ca_id);
        $files   = $this->model->getCAFiles($ca_id);
        $rejections = $this->model->getRejectionHistory($ca_id);

        $this->template->set([
            'header'     => $header,
            'ca'         => $ca,
            'temuan'     => $temuan,
            'details'    => $details,
            'files'      => $files,
            'rejections' => $rejections,
        ]);
        $this->template->render('approval_form');
    }

    /**
     * Approve - AJAX approve (Waiting Approval → Approved)
     * POST params: ca_id
     */
    public function approve()
    {
        $data = $this->input->post();
        if (!$data || empty($data['ca_id'])) {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid. Please try again.']);
            return;
        }

        $userId = $this->auth->user_id();
        $result = $this->model->approveCorrective($data['ca_id'], $userId);
        echo json_encode($result);
    }

    /**
     * Reject - AJAX reject (Waiting Approval → Draft)
     * POST params: ca_id, alasan_reject
     */
    public function reject()
    {
        $data = $this->input->post();
        if (!$data || empty($data['ca_id'])) {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid. Please try again.']);
            return;
        }

        $alasan = isset($data['alasan_reject']) ? trim($data['alasan_reject']) : '';
        if ($alasan === '') {
            echo json_encode(['status' => 0, 'msg' => 'Alasan reject wajib diisi.']);
            return;
        }
        if (strlen($alasan) > 2000) {
            echo json_encode(['status' => 0, 'msg' => 'Alasan reject maksimal 2000 karakter.']);
            return;
        }

        $userId = $this->auth->user_id();
        $result = $this->model->rejectCorrective($data['ca_id'], $alasan, $userId);
        echo json_encode($result);
    }

    // =========================================================================
    // RECORD AUDIT (ARCHIVE)
    // =========================================================================

    /**
     * Record Index - List approved CAs (archive)
     */
    public function record_index()
    {
        $data = $this->model->getRecordIndexData();
        $this->template->set('data', $data);
        $this->template->render('record_index');
    }

    /**
     * Record View - Read-only archive view of an approved CA
     *
     * @param string $ca_id The corrective action ID
     */
    public function record_view($ca_id = null)
    {
        if (!$ca_id) {
            show_404();
            return;
        }

        $ca = $this->db->get_where('corrective_action', ['id' => $ca_id, 'deleted' => '0'])->row();
        if (!$ca) {
            show_404();
            return;
        }

        $header  = $this->_getAuditHeader($ca->pelaksanaan_id);
        $temuan  = $this->model->getTemuanByAudit($ca->pelaksanaan_id);
        $details = $this->model->getCADetails($ca_id);
        $files   = $this->model->getCAFiles($ca_id);

        $this->template->set([
            'header'   => $header,
            'ca'       => $ca,
            'temuan'   => $temuan,
            'details'  => $details,
            'files'    => $files,
            'readonly' => true,
        ]);
        $this->template->render('record_view');
    }

    // =========================================================================
    // FILE MANAGEMENT
    // =========================================================================

    /**
     * Download evidence file
     *
     * @param int $file_id
     */
    public function download($file_id = null)
    {
        if (!$file_id) {
            show_404();
            return;
        }

        $file = $this->model->getFileById($file_id);
        if (!$file) {
            echo json_encode(['status' => 0, 'msg' => 'File tidak ditemukan.']);
            return;
        }

        // Build path using company from session and ca_id from the joined query
        $path = './directory/CORRECTIVE_ACTION/' . $this->company . '/' . $file->ca_id . '/' . $file->file_name;
        if (!file_exists($path)) {
            echo json_encode(['status' => 0, 'msg' => 'File tidak tersedia di server.']);
            return;
        }

        $this->load->helper('download');
        force_download($path, NULL);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Get audit header data for a pelaksanaan audit
     *
     * @param int $pelaksanaan_id
     * @return object|null
     */
    private function _getAuditHeader($pelaksanaan_id)
    {
        return $this->model->getAuditHeader($pelaksanaan_id);
    }

    /**
     * Handle multi-file uploads per temuan
     *
     * Creates directory, processes each file_<temuan_id> field,
     * saves records via model, and handles file replacement.
     *
     * @param string $ca_id The corrective action ID
     * @param int $company_id The company ID
     * @return array Warning messages for any failed uploads
     */
    private function _handleFileUploads($ca_id, $company_id)
    {
        $warnings = [];

        // Create upload directory if it doesn't exist
        $upload_path = './directory/CORRECTIVE_ACTION/' . $company_id . '/' . $ca_id . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, TRUE);
        }

        // Upload config
        $config = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'pdf|jpg|jpeg|png|doc|docx|xls|xlsx',
            'max_size'      => 10240, // 10MB
            'encrypt_name'  => TRUE,
        ];

        // Get CA details to map temuan_id to ca_detail_id
        $details = $this->model->getCADetails($ca_id);
        $detailMap = [];
        foreach ($details as $detail) {
            $detailMap[$detail->temuan_id] = $detail->id;
        }

        $userId = $this->auth->user_id();

        // Process each file input field (file_{temuan_id})
        foreach ($_FILES as $field_name => $file_info) {
            // Match field names like "file_123" where 123 is temuan_id
            if (!preg_match('/^file_(\d+)$/', $field_name, $matches)) {
                continue;
            }

            $temuan_id = $matches[1];

            // Skip if no ca_detail_id found for this temuan
            if (!isset($detailMap[$temuan_id])) {
                continue;
            }

            $ca_detail_id = $detailMap[$temuan_id];

            // Handle single or multiple files
            if (is_array($file_info['name'])) {
                // Multiple files for this temuan
                $file_count = count($file_info['name']);
                for ($i = 0; $i < $file_count; $i++) {
                    if ($file_info['error'][$i] !== UPLOAD_ERR_OK || $file_info['size'][$i] == 0) {
                        continue;
                    }

                    // Set up single file in $_FILES for CI upload
                    $_FILES['upload_temp'] = [
                        'name'     => $file_info['name'][$i],
                        'type'     => $file_info['type'][$i],
                        'tmp_name' => $file_info['tmp_name'][$i],
                        'error'    => $file_info['error'][$i],
                        'size'     => $file_info['size'][$i],
                    ];

                    $upload_instance = 'upload_ca_' . $temuan_id . '_' . $i;
                    $this->load->library('upload', $config, $upload_instance);
                    $this->{$upload_instance}->initialize($config);

                    if ($this->{$upload_instance}->do_upload('upload_temp')) {
                        $file_data = $this->{$upload_instance}->data();
                        $this->model->saveFile($ca_detail_id, [
                            'file_name'          => $file_data['file_name'],
                            'file_name_original' => $file_info['name'][$i],
                            'file_type'          => $file_data['file_ext'],
                            'file_size'          => $file_data['file_size'],
                            'user_id'            => $userId,
                        ]);
                    } else {
                        $warnings[] = 'Gagal upload file "' . $file_info['name'][$i] . '": ' . strip_tags($this->{$upload_instance}->display_errors());
                    }
                }

                // Clean up temp file reference
                unset($_FILES['upload_temp']);
            } else {
                // Single file for this temuan
                if ($file_info['error'] !== UPLOAD_ERR_OK || $file_info['size'] == 0) {
                    continue;
                }

                $upload_instance = 'upload_ca_' . $temuan_id;
                $this->load->library('upload', $config, $upload_instance);
                $this->{$upload_instance}->initialize($config);

                if ($this->{$upload_instance}->do_upload($field_name)) {
                    $file_data = $this->{$upload_instance}->data();

                    // Check if replacing existing file
                    $existing_files = $this->model->getCAFiles($ca_id);
                    foreach ($existing_files as $existing) {
                        if ($existing->temuan_id == $temuan_id) {
                            // Soft-delete old file record
                            $this->model->deleteFile($existing->id);
                            // Remove old physical file
                            $old_path = $upload_path . $existing->file_name;
                            if (file_exists($old_path)) {
                                @unlink($old_path);
                            }
                        }
                    }

                    $this->model->saveFile($ca_detail_id, [
                        'file_name'          => $file_data['file_name'],
                        'file_name_original' => $file_info['name'],
                        'file_type'          => $file_data['file_ext'],
                        'file_size'          => $file_data['file_size'],
                        'user_id'            => $userId,
                    ]);
                } else {
                    $warnings[] = 'Gagal upload file "' . $file_info['name'] . '": ' . strip_tags($this->{$upload_instance}->display_errors());
                }
            }
        }

        return $warnings;
    }

    /**
     * Validate that a CA has the expected status
     *
     * @param string $ca_id The corrective action ID
     * @param string $expected_status The expected status value
     * @return bool True if status matches, false otherwise
     */
    private function _validateStatus($ca_id, $expected_status)
    {
        $status = $this->model->getStatus($ca_id);
        return ($status === $expected_status);
    }
}
