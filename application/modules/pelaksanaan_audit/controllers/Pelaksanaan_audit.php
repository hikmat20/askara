<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Pelaksanaan Audit Controller
 *
 * Menu untuk pelaksanaan audit (konsep & simulasi)
 * Data ditarik dari audit_checklist_non_standard schedule,
 * checklist standard, ISO (requirements), dan pasal (requirement_details)
 */
class Pelaksanaan_audit extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pelaksanaan_audit/Pelaksanaan_audit_model', 'model');
        $this->template->set([
            'title' => 'Pelaksanaan Audit',
            'icon'  => 'fa fa-clipboard-check'
        ]);
        date_default_timezone_set("Asia/Bangkok");
    }

    /**
     * Index - shows audit programs (parent level), same as Jadwal & Persiapan Audit
     */
    public function index()
    {
        $programs = $this->model->getActivePrograms();
        $this->template->set('programs', $programs);
        $this->template->render('index');
    }

    /**
     * Schedules - shows list of proses/schedules for a given program
     *
     * @param string $program_id
     */
    public function schedules($program_id = null)
    {
        if (!$program_id) {
            show_404();
            return;
        }

        $program = $this->db->select('audit_program.*, audit_auditor_consultant.name as auditor_name')
            ->from('audit_program')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program.lead_auditor_id', 'left')
            ->where('audit_program.id', $program_id)
            ->get()->row();

        if (!$program) {
            show_404();
            return;
        }

        $schedules = $this->model->getSchedulesByProgram($program_id);

        // Check which schedules already have audit data
        $has_audit = [];
        foreach ($schedules as $s) {
            $has_audit[$s->schedule_id] = $this->model->countAuditByScheduleId($s->schedule_id) > 0;
        }

        $this->template->set('program', $program);
        $this->template->set('schedules', $schedules);
        $this->template->set('has_audit', $has_audit);
        $this->template->render('schedules');
    }

    /**
     * Audit form - full pelaksanaan audit page
     *
     * @param int $schedule_id
     */
    public function audit($schedule_id = null)
    {
        if (!$schedule_id) {
            show_404();
            return;
        }

        $schedule = $this->model->getScheduleById($schedule_id);
        if (!$schedule) {
            show_404();
            return;
        }

        // Get issues matching the process
        $issues = $this->model->getIssuesByProcess($schedule->program_id, $schedule->process_id);

        // Get non-standard checklist items
        $ns_checklist = $this->model->getNonStandardChecklist($schedule_id);

        // Get standard checklist items (from audit_checklist module)
        $std_checklist = $this->model->getStandardChecklist($schedule->process_id, $this->company);

        // Get ISO standards (requirements)
        $standards = $this->model->getRequirements();

        // Get existing audit data if editing
        $audit_data = $this->model->getAuditByScheduleId($schedule_id);
        $audit_ns_details = [];
        $audit_std_details = [];
        $audit_free_checklist = [];
        $audit_conformity = [];
        $audit_temuan = [];

        if ($audit_data) {
            $audit_ns_details = $this->model->getAuditNsDetails($audit_data->id);
            $audit_std_details = $this->model->getAuditStdDetails($audit_data->id);
            $audit_free_checklist = $this->model->getAuditFreeChecklist($audit_data->id);
            $audit_conformity = $this->model->getAuditConformity($audit_data->id);
            $audit_temuan = $this->model->getAuditTemuan($audit_data->id);
        }

        $this->template->set([
            'schedule'              => $schedule,
            'issues'                => $issues,
            'ns_checklist'          => $ns_checklist,
            'std_checklist'         => $std_checklist,
            'standards'             => $standards,
            'audit_data'            => $audit_data,
            'audit_ns_details'      => $audit_ns_details,
            'audit_std_details'     => $audit_std_details,
            'audit_free_checklist'  => $audit_free_checklist,
            'audit_conformity'      => $audit_conformity,
            'audit_temuan'          => $audit_temuan,
        ]);
        $this->template->render('audit');
    }

    /**
     * Get pasal list by requirement_id (AJAX)
     */
    public function get_pasal($requirement_id = null)
    {
        if (!$requirement_id) {
            echo '<option value=""></option>';
            return;
        }

        $pasal_list = $this->model->getPasalByRequirement($requirement_id);
        $html = '<option value=""></option>';
        if ($pasal_list) {
            foreach ($pasal_list as $p) {
                $html .= '<option value="' . $p->id . '">' . htmlspecialchars($p->chapter) . '</option>';
            }
        }
        echo $html;
    }

    /**
     * Save audit (AJAX)
     */
    public function save()
    {
        $data = $this->input->post();

        if (!$data || empty($data['schedule_id'])) {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid. Please try again.']);
            return;
        }

        $userId = $this->auth->user_id();
        $success = $this->model->saveAudit($data, $userId);

        if ($success) {
            // Handle file uploads after save
            $schedule_id = $data['schedule_id'];
            $this->handleEvidenceUploads($schedule_id);

            echo json_encode(['status' => 1, 'msg' => 'Pelaksanaan Audit berhasil disimpan.']);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Gagal menyimpan. Silakan coba lagi.']);
        }
    }

    /**
     * Handle evidence file uploads after audit save
     */
    private function handleEvidenceUploads($schedule_id)
    {
        $upload_path = './directory/AUDIT_PELAKSANAAN/' . $this->company . '/' . $schedule_id . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, TRUE);
        }

        $audit_data = $this->model->getAuditByScheduleId($schedule_id);
        if (!$audit_data) return;

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = '*';
        $config['max_size']      = 10240;
        $config['encrypt_name']  = TRUE;

        // Upload NS detail files
        $ns_details = $this->model->getAuditNsDetails($audit_data->id);
        foreach ($ns_details as $k => $detail) {
            $field_name = 'evidence_ns_' . ($k + 1);
            if (isset($_FILES[$field_name]) && $_FILES[$field_name]['size'] > 0) {
                $this->load->library('upload', $config, 'upload_' . $field_name);
                $this->{'upload_' . $field_name}->initialize($config);
                if ($this->{'upload_' . $field_name}->do_upload($field_name)) {
                    $file_data = $this->{'upload_' . $field_name}->data();
                    $this->model->updateEvidence('ns_detail', $detail->id, [
                        'file_name' => $file_data['file_name'],
                        'file_type' => $file_data['file_ext'],
                        'file_size' => $file_data['file_size'],
                    ]);
                }
            }
        }

        // Upload STD detail files
        $std_details = $this->model->getAuditStdDetails($audit_data->id);
        foreach ($std_details as $k => $detail) {
            $field_name = 'evidence_std_' . ($k + 1);
            if (isset($_FILES[$field_name]) && $_FILES[$field_name]['size'] > 0) {
                $this->load->library('upload', $config, 'upload_' . $field_name);
                $this->{'upload_' . $field_name}->initialize($config);
                if ($this->{'upload_' . $field_name}->do_upload($field_name)) {
                    $file_data = $this->{'upload_' . $field_name}->data();
                    $this->model->updateEvidence('std_detail', $detail->id, [
                        'file_name' => $file_data['file_name'],
                        'file_type' => $file_data['file_ext'],
                        'file_size' => $file_data['file_size'],
                    ]);
                }
            }
        }

        // Upload Conformity files
        $conformity = $this->model->getAuditConformity($audit_data->id);
        foreach ($conformity as $k => $detail) {
            $field_name = 'evidence_cf_' . ($k + 1);
            if (isset($_FILES[$field_name]) && $_FILES[$field_name]['size'] > 0) {
                $this->load->library('upload', $config, 'upload_' . $field_name);
                $this->{'upload_' . $field_name}->initialize($config);
                if ($this->{'upload_' . $field_name}->do_upload($field_name)) {
                    $file_data = $this->{'upload_' . $field_name}->data();
                    $this->model->updateEvidence('conformity', $detail->id, [
                        'file_name' => $file_data['file_name'],
                        'file_type' => $file_data['file_ext'],
                        'file_size' => $file_data['file_size'],
                    ]);
                }
            }
        }

        // Upload Temuan files
        $temuan = $this->model->getAuditTemuan($audit_data->id);
        foreach ($temuan as $k => $detail) {
            $field_name = 'evidence_tm_' . ($k + 1);
            if (isset($_FILES[$field_name]) && $_FILES[$field_name]['size'] > 0) {
                $this->load->library('upload', $config, 'upload_' . $field_name);
                $this->{'upload_' . $field_name}->initialize($config);
                if ($this->{'upload_' . $field_name}->do_upload($field_name)) {
                    $file_data = $this->{'upload_' . $field_name}->data();
                    $this->model->updateEvidence('temuan', $detail->id, [
                        'file_name' => $file_data['file_name'],
                        'file_type' => $file_data['file_ext'],
                        'file_size' => $file_data['file_size'],
                    ]);
                }
            }
        }
    }

    /**
     * Upload evidence file (AJAX)
     */
    public function upload_evidence()
    {
        $type = $this->input->post('type'); // ns_detail or std_detail or conformity or temuan
        $detail_id = $this->input->post('detail_id');
        $schedule_id = $this->input->post('schedule_id');

        $upload_path = './directory/AUDIT_PELAKSANAAN/' . $this->company . '/' . $schedule_id . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, TRUE);
        }

        $config['upload_path']     = $upload_path;
        $config['allowed_types']   = '*';
        $config['max_size']        = 10240; // 10MB
        $config['encrypt_name']    = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('evidence_file')) {
            echo json_encode([
                'status' => 0,
                'msg'    => strip_tags($this->upload->display_errors()),
            ]);
            return;
        }

        $file_data = $this->upload->data();
        $fileInfo = [
            'file_name' => $file_data['file_name'],
            'file_type' => $file_data['file_ext'],
            'file_size' => $file_data['file_size'],
        ];

        $success = $this->model->updateEvidence($type, $detail_id, $fileInfo);

        if ($success) {
            echo json_encode(['status' => 1, 'msg' => 'File berhasil diupload.', 'file_name' => $file_data['file_name']]);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Gagal menyimpan file.']);
        }
    }

    /**
     * Delete conformity item (AJAX)
     */
    public function delete_conformity()
    {
        $id = $this->input->post('id');
        if ($id) {
            $this->model->deleteConformity($id);
            echo json_encode(['status' => 1, 'msg' => 'Item berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid.']);
        }
    }

    /**
     * Delete temuan item (AJAX)
     */
    public function delete_temuan()
    {
        $id = $this->input->post('id');
        if ($id) {
            $this->model->deleteTemuan($id);
            echo json_encode(['status' => 1, 'msg' => 'Item berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid.']);
        }
    }

    /**
     * View audit result (read-only)
     */
    public function view($schedule_id = null)
    {
        if (!$schedule_id) {
            show_404();
            return;
        }

        $schedule = $this->model->getScheduleById($schedule_id);
        if (!$schedule) {
            show_404();
            return;
        }

        $issues = $this->model->getIssuesByProcess($schedule->program_id, $schedule->process_id);
        $ns_checklist = $this->model->getNonStandardChecklist($schedule_id);
        $std_checklist = $this->model->getStandardChecklist($schedule->process_id, $this->company);
        $standards = $this->model->getRequirements();

        $audit_data = $this->model->getAuditByScheduleId($schedule_id);
        $audit_ns_details = [];
        $audit_std_details = [];
        $audit_free_checklist = [];
        $audit_conformity = [];
        $audit_temuan = [];

        if ($audit_data) {
            $audit_ns_details = $this->model->getAuditNsDetails($audit_data->id);
            $audit_std_details = $this->model->getAuditStdDetails($audit_data->id);
            $audit_free_checklist = $this->model->getAuditFreeChecklist($audit_data->id);
            $audit_conformity = $this->model->getAuditConformity($audit_data->id);
            $audit_temuan = $this->model->getAuditTemuan($audit_data->id);
        }

        $this->template->set([
            'schedule'              => $schedule,
            'issues'                => $issues,
            'ns_checklist'          => $ns_checklist,
            'std_checklist'         => $std_checklist,
            'standards'             => $standards,
            'audit_data'            => $audit_data,
            'audit_ns_details'      => $audit_ns_details,
            'audit_std_details'     => $audit_std_details,
            'audit_free_checklist'  => $audit_free_checklist,
            'audit_conformity'      => $audit_conformity,
            'audit_temuan'          => $audit_temuan,
        ]);
        $this->template->render('view');
    }

    /**
     * Print PDF - generate PDF report of pelaksanaan audit
     *
     * @param int $schedule_id
     */
    public function print_pdf($schedule_id = null)
    {
        if (!$schedule_id) {
            show_404();
            return;
        }

        $schedule = $this->model->getScheduleById($schedule_id);
        if (!$schedule) {
            show_404();
            return;
        }

        $issues = $this->model->getIssuesByProcess($schedule->program_id, $schedule->process_id);
        $ns_checklist = $this->model->getNonStandardChecklist($schedule_id);
        $std_checklist = $this->model->getStandardChecklist($schedule->process_id, $this->company);
        $standards = $this->model->getRequirements();

        $audit_data = $this->model->getAuditByScheduleId($schedule_id);
        $audit_ns_details = [];
        $audit_std_details = [];
        $audit_free_checklist = [];
        $audit_conformity = [];
        $audit_temuan = [];

        if ($audit_data) {
            $audit_ns_details = $this->model->getAuditNsDetails($audit_data->id);
            $audit_std_details = $this->model->getAuditStdDetails($audit_data->id);
            $audit_free_checklist = $this->model->getAuditFreeChecklist($audit_data->id);
            $audit_conformity = $this->model->getAuditConformity($audit_data->id);
            $audit_temuan = $this->model->getAuditTemuan($audit_data->id);
        }

        $data = [
            'schedule'              => $schedule,
            'issues'                => $issues,
            'ns_checklist'          => $ns_checklist,
            'std_checklist'         => $std_checklist,
            'standards'             => $standards,
            'audit_data'            => $audit_data,
            'audit_ns_details'      => $audit_ns_details,
            'audit_std_details'     => $audit_std_details,
            'audit_free_checklist'  => $audit_free_checklist,
            'audit_conformity'      => $audit_conformity,
            'audit_temuan'          => $audit_temuan,
        ];

        $html = $this->load->view('pelaksanaan_audit/pdf', $data, true);

        // Load mPDF directly from MPDF_ folder
        require_once(APPPATH . 'libraries/MPDF_/mpdf.php');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 15, 0, 0);
        $mpdf->SetTitle('Pelaksanaan Audit - ' . $schedule->schedule_id);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Pelaksanaan_Audit_' . $schedule->schedule_id . '.pdf', 'I');
    }
}
