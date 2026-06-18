<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Audit Preparation Controller
 *
 * Manages Audit Programs including creation, editing, viewing,
 * and deletion of audit preparation schedules.
 *
 * @author Kiro
 * @copyright Copyright (c) 2025
 */
class Audit_preparation extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('audit_preparation/Audit_program_model', 'audit_program_model');
        $this->template->set('title', 'Jadwal & Persiapan Audit');
        $this->template->set('icon', 'fa fa-calendar-check-o');
    }

    /**
     * List view - displays all active audit programs
     */
    public function index()
    {
        $data['programs'] = $this->audit_program_model->getActivePrograms();
        $this->template->set('programs', $data['programs']);
        $this->template->render('index');
    }

    /**
     * Create - displays empty form with master data for new audit program
     */
    public function create()
    {
        $data['auditors'] = $this->audit_program_model->getActiveAuditors();
        $data['processes'] = $this->audit_program_model->getActiveProcesses();
        $data['procedures'] = $this->audit_program_model->getActiveProcedures($this->company);
        $data['temuan'] = $this->audit_program_model->getActiveTemuan();
        $data['departments'] = $this->audit_program_model->getDepartments($this->company);
        $data['program'] = null;
        $data['evaluations'] = [];
        $data['critical_issues'] = [];
        $data['opportunities'] = [];
        $data['schedules'] = [];

        $this->template->set($data);
        $this->template->render('form');
    }

    /**
     * Edit - loads existing audit program and all associated data into form
     *
     * @param string $id Program ID
     */
    public function edit($id)
    {
        $program = $this->audit_program_model->getProgramById($id);

        if (!$program) {
            $data = ['heading' => 'Error!', 'message' => 'Data not found..'];
            $this->template->render('../views/errors/html/error_404_custome', $data);
            return;
        }

        // Load master data
        $data['auditors'] = $this->audit_program_model->getActiveAuditors();
        $data['processes'] = $this->audit_program_model->getActiveProcesses();
        $data['procedures'] = $this->audit_program_model->getActiveProcedures($this->company);
        $data['temuan'] = $this->audit_program_model->getActiveTemuan();
        $data['departments'] = $this->audit_program_model->getDepartments($this->company);

        // Load existing program and child records
        $data['program'] = $program;
        $data['evaluations'] = $this->audit_program_model->getEvaluations($id);
        $data['critical_issues'] = $this->audit_program_model->getCriticalIssues($id);
        $data['opportunities'] = $this->audit_program_model->getOpportunities($id);

        // Load schedules with auditees for each schedule row
        $schedules = $this->audit_program_model->getSchedules($id);
        foreach ($schedules as &$schedule) {
            $schedule->auditees = $this->audit_program_model->getScheduleAuditees($schedule->id);
        }
        $data['schedules'] = $schedules;

        $this->template->set($data);
        $this->template->render('form');
    }

    /**
     * Save - create or update an audit program with all child records (AJAX)
     *
     * Handles full transactional save of header + evaluations + critical issues
     * + opportunities + schedules (with auditee junction records).
     * On update, soft-deletes all existing child records then re-inserts from form data.
     *
     * @return void Outputs JSON response
     */
    public function save()
    {
        $data = $this->input->post();

        if (!$data) {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid. Please try again.']);
            return;
        }

        // Server-side validation
        $errors = $this->_validate($data);
        if (!empty($errors)) {
            echo json_encode(['status' => 0, 'msg' => implode(', ', $errors)]);
            return;
        }

        $userId = $this->auth->user_id();
        $now = date('Y-m-d H:i:s');
        $isNew = empty($data['id']);

        $this->db->trans_begin();

        // 1. Save/update header
        $headerData = [
            'company'         => trim($data['company']),
            'lead_auditor_id' => $data['lead_auditor_id'],
            'audit_scope'     => $data['audit_scope'],
        ];

        if ($isNew) {
            $program_id = $this->_getId();
            $headerData['id'] = $program_id;
            $headerData['status'] = '1';
            $headerData['created_at'] = $now;
            $headerData['created_by'] = $userId;
            $this->db->insert('audit_program', $headerData);
        } else {
            $program_id = $data['id'];
            $headerData['modified_at'] = $now;
            $headerData['modified_by'] = $userId;
            $this->db->update('audit_program', $headerData, ['id' => $program_id]);
        }

        // 2. Process evaluations
        // Soft-delete all existing evaluations for this program
        if (!$isNew) {
            $this->db->update('audit_program_evaluation', ['status' => '0'], ['program_id' => $program_id]);
        }
        // Insert new evaluation entries from form
        if (!empty($data['evaluations'])) {
            foreach ($data['evaluations'] as $eval) {
                $evalData = [
                    'program_id'           => $program_id,
                    'audit_temuan_id'      => $eval['audit_temuan_id'],
                    'temuan_detail_id'     => isset($eval['temuan_detail_id']) ? $eval['temuan_detail_id'] : null,
                    'weakness_description' => isset($eval['weakness_description']) ? $eval['weakness_description'] : null,
                    'improvement_action'   => isset($eval['improvement_action']) ? $eval['improvement_action'] : null,
                    'status'               => '1',
                    'created_at'           => $now,
                    'created_by'           => $userId,
                ];
                $this->db->insert('audit_program_evaluation', $evalData);
            }
        }

        // 3. Process critical issues
        $criticalIds = isset($data['critical_id']) ? $data['critical_id'] : [];
        $issueDescs = isset($data['issue_desc']) ? $data['issue_desc'] : [];
        $mgmtInputs = isset($data['management_input']) ? $data['management_input'] : [];

        // Collect submitted IDs that already exist
        $submittedCriticalIds = array_filter($criticalIds);

        // Soft-delete records not in submitted list
        if (!$isNew) {
            $this->db->where('program_id', $program_id);
            $this->db->where('status', '1');
            if (!empty($submittedCriticalIds)) {
                $this->db->where_not_in('id', $submittedCriticalIds);
            }
            $this->db->update('audit_program_critical_issue', ['status' => '0']);
        }

        // Insert or update critical issue entries
        if (!empty($issueDescs)) {
            foreach ($issueDescs as $k => $issueDesc) {
                if (trim($issueDesc) === '') continue;
                $recordId = isset($criticalIds[$k]) ? $criticalIds[$k] : '';
                $issueData = [
                    'program_id'        => $program_id,
                    'issue_description' => trim($issueDesc),
                    'management_input'  => isset($mgmtInputs[$k]) ? trim($mgmtInputs[$k]) : null,
                    'status'            => '1',
                ];

                if (!empty($recordId)) {
                    // Update existing
                    $issueData['modified_at'] = $now;
                    $issueData['modified_by'] = $userId;
                    $this->db->update('audit_program_critical_issue', $issueData, ['id' => $recordId]);
                } else {
                    // Insert new
                    $issueData['created_at'] = $now;
                    $issueData['created_by'] = $userId;
                    $this->db->insert('audit_program_critical_issue', $issueData);
                }
            }
        }

        // 4. Process opportunities
        $oppIds = isset($data['opp_id']) ? $data['opp_id'] : [];
        $oppIssues = isset($data['opp_issue_text']) ? $data['opp_issue_text'] : [];
        $oppProcIds = isset($data['opp_procedure_id']) ? $data['opp_procedure_id'] : [];
        $oppInvestigations = isset($data['opp_investigation']) ? $data['opp_investigation'] : [];

        // Collect submitted IDs that already exist
        $submittedOppIds = array_filter($oppIds);

        // Soft-delete records not in submitted list
        if (!$isNew) {
            $this->db->where('program_id', $program_id);
            $this->db->where('status', '1');
            if (!empty($submittedOppIds)) {
                $this->db->where_not_in('id', $submittedOppIds);
            }
            $this->db->update('audit_program_opportunity', ['status' => '0']);
        }

        // Insert or update opportunity entries
        if (!empty($oppProcIds)) {
            foreach ($oppProcIds as $k => $procId) {
                if (empty($procId)) continue;
                $recordId = isset($oppIds[$k]) ? $oppIds[$k] : '';
                $oppData = [
                    'program_id'    => $program_id,
                    'procedure_id'  => $procId,
                    'description'   => isset($oppIssues[$k]) ? trim($oppIssues[$k]) : '',
                    'investigation' => isset($oppInvestigations[$k]) ? trim($oppInvestigations[$k]) : '',
                    'status'        => '1',
                ];

                if (!empty($recordId)) {
                    // Update existing
                    $oppData['modified_at'] = $now;
                    $oppData['modified_by'] = $userId;
                    $this->db->update('audit_program_opportunity', $oppData, ['id' => $recordId]);
                } else {
                    // Insert new
                    $oppData['created_at'] = $now;
                    $oppData['created_by'] = $userId;
                    $this->db->insert('audit_program_opportunity', $oppData);
                }
            }
        }

        // 5. Process schedules
        $schedRecordIds = isset($data['schedule_record_id']) ? $data['schedule_record_id'] : [];
        $schedProcessIds = isset($data['schedule_process_id']) ? $data['schedule_process_id'] : [];
        $schedProcessFree = isset($data['schedule_process_name_free']) ? $data['schedule_process_name_free'] : [];
        $schedAuditorIds = isset($data['schedule_auditor_id']) ? $data['schedule_auditor_id'] : [];
        $schedAuditeeIds = isset($data['schedule_auditee_id']) ? $data['schedule_auditee_id'] : [];
        $schedAuditeeFree = isset($data['schedule_auditee_name_free']) ? $data['schedule_auditee_name_free'] : [];
        $schedDates = isset($data['schedule_date']) ? $data['schedule_date'] : [];
        $schedStartTimes = isset($data['schedule_start_time']) ? $data['schedule_start_time'] : [];
        $schedEndTimes = isset($data['schedule_end_time']) ? $data['schedule_end_time'] : [];

        // Collect submitted IDs that already exist
        $submittedSchedIds = array_filter($schedRecordIds);

        // Soft-delete records not in submitted list + remove their auditee junctions
        if (!$isNew) {
            $this->db->select('id');
            $this->db->where('program_id', $program_id);
            $this->db->where('status', '1');
            if (!empty($submittedSchedIds)) {
                $this->db->where_not_in('id', $submittedSchedIds);
            }
            $deletedSchedules = $this->db->get('audit_program_schedule')->result();
            foreach ($deletedSchedules as $ds) {
                $this->db->delete('audit_program_schedule_auditee', ['schedule_id' => $ds->id]);
            }
            $this->db->where('program_id', $program_id);
            $this->db->where('status', '1');
            if (!empty($submittedSchedIds)) {
                $this->db->where_not_in('id', $submittedSchedIds);
            }
            $this->db->update('audit_program_schedule', ['status' => '0']);
        }

        // Insert or update schedule entries
        if (!empty($schedProcessIds)) {
            foreach ($schedProcessIds as $k => $processId) {
                $freeText = isset($schedProcessFree[$k]) ? trim($schedProcessFree[$k]) : '';
                // Skip row if both process_id and free text are empty
                if (empty($processId) && empty($freeText)) continue;

                $recordId = isset($schedRecordIds[$k]) ? $schedRecordIds[$k] : '';
                $auditeeFreeText = isset($schedAuditeeFree[$k]) ? trim($schedAuditeeFree[$k]) : '';
                $schedData = [
                    'program_id'        => $program_id,
                    'process_id'        => !empty($processId) ? $processId : null,
                    'process_name_free' => $freeText,
                    'auditor_id'        => isset($schedAuditorIds[$k]) ? $schedAuditorIds[$k] : null,
                    'audit_date'        => isset($schedDates[$k]) ? $schedDates[$k] : null,
                    'start_time'        => isset($schedStartTimes[$k]) ? $schedStartTimes[$k] : null,
                    'end_time'          => isset($schedEndTimes[$k]) ? $schedEndTimes[$k] : null,
                    'auditee_name_free' => $auditeeFreeText,
                    'status'            => '1',
                ];

                if (!empty($recordId)) {
                    // Update existing
                    $schedData['modified_at'] = $now;
                    $schedData['modified_by'] = $userId;
                    $this->db->update('audit_program_schedule', $schedData, ['id' => $recordId]);
                    $scheduleId = $recordId;

                    // Replace auditee for this schedule
                    $this->db->delete('audit_program_schedule_auditee', ['schedule_id' => $scheduleId]);
                } else {
                    // Insert new
                    $schedData['created_at'] = $now;
                    $schedData['created_by'] = $userId;
                    $this->db->insert('audit_program_schedule', $schedData);
                    $scheduleId = $this->db->insert_id();
                }

                // Insert auditee record for this schedule row (single department)
                $deptId = isset($schedAuditeeIds[$k]) ? $schedAuditeeIds[$k] : '';
                if (!empty($deptId)) {
                    $this->db->insert('audit_program_schedule_auditee', [
                        'schedule_id'   => $scheduleId,
                        'department_id' => $deptId,
                    ]);
                }
            }
        }

        // Commit or rollback based on transaction status
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 0, 'msg' => 'Data gagal disimpan. Silakan coba lagi.']);
        } else {
            $this->db->trans_commit();
            echo json_encode(['status' => 1, 'msg' => 'Audit Program berhasil disimpan.', 'id' => $program_id]);
        }
    }

    /**
     * Generate unique ID for audit program following APR{YYMM}-{NNN} pattern
     * Sequence resets to 001 each month
     *
     * @return string Generated ID (e.g., APR2506-001)
     */
    private function _getId()
    {
        $count = 1;
        $ym = date('ym');
        $prefix = "APR" . $ym . "-";
        $result = $this->db->query("SELECT MAX(RIGHT(id, 3)) as max_seq FROM audit_program WHERE SUBSTR(id, 4, 4) = ?", [$ym])->row();

        if ($result && $result->max_seq > 0) {
            $count = $result->max_seq + 1;
        }
        return $prefix . sprintf("%03d", $count);
    }

    /**
     * Send Email - sends audit schedule email to all auditors assigned in schedules
     * Subject: "Jadwal Audit"
     * Content: Program info + schedule table
     */
    public function send_email()
    {
        $id = $this->input->post('id');

        if (!$id) {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid. Please try again.']);
            return;
        }

        $program = $this->audit_program_model->getProgramById($id);

        if (!$program) {
            echo json_encode(['status' => 0, 'msg' => 'Audit Program tidak ditemukan.']);
            return;
        }

        // Get schedules with auditor info
        $schedules = $this->audit_program_model->getSchedules($id);
        foreach ($schedules as &$schedule) {
            $schedule->auditees = $this->audit_program_model->getScheduleAuditees($schedule->id);
        }

        if (empty($schedules)) {
            echo json_encode(['status' => 0, 'msg' => 'Tidak ada jadwal audit untuk dikirim.']);
            return;
        }

        // Collect unique auditor emails from schedules
        $auditor_ids = [];
        foreach ($schedules as $sched) {
            if (!empty($sched->auditor_id)) {
                $auditor_ids[] = $sched->auditor_id;
            }
        }
        $auditor_ids = array_unique($auditor_ids);

        if (empty($auditor_ids)) {
            echo json_encode(['status' => 0, 'msg' => 'Tidak ada auditor yang terdaftar di jadwal.']);
            return;
        }

        // Get auditor emails
        $this->db->where_in('id', $auditor_ids);
        $this->db->where('status', '1');
        $auditors = $this->db->get('audit_auditor_consultant')->result();

        $emails = [];
        foreach ($auditors as $aud) {
            if (!empty($aud->email)) {
                $emails[] = $aud->email;
            }
        }

        if (empty($emails)) {
            echo json_encode(['status' => 0, 'msg' => 'Auditor tidak memiliki alamat email.']);
            return;
        }

        // Build email body HTML
        $body = $this->_buildEmailBody($program, $schedules);

        // Load CI email library and send
        $this->load->library('email');

        $config = get_smtp_config();
        $smtp_user = $config['smtp_user'];

        $this->email->initialize($config);
        $this->email->from($smtp_user, 'Sentral Sistem - Audit');
        $this->email->to($emails);
        $this->email->subject('Jadwal Audit');
        $this->email->message($body);

        if ($this->email->send()) {
            echo json_encode(['status' => 1, 'msg' => 'Email berhasil dikirim ke ' . count($emails) . ' auditor.']);
        } else {
            $error = $this->email->print_debugger(['headers']);
            log_message('error', 'Email send failed: ' . $error);
            echo json_encode(['status' => 0, 'msg' => 'Gagal mengirim email. Silakan cek konfigurasi email.']);
        }
    }

    /**
     * Build HTML email body for audit schedule notification
     *
     * @param object $program Program data
     * @param array $schedules Schedule data with auditees
     * @return string HTML email body
     */
    private function _buildEmailBody($program, $schedules)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">';
        $html .= '<h2 style="color: #2c3e50;">Jadwal Audit</h2>';

        // Program info table
        $html .= '<table style="border-collapse: collapse; width: 100%; margin-bottom: 20px;" border="1" cellpadding="8">';
        $html .= '<tr><th style="text-align:left; background:#f8f9fa; width:200px;">ID Program</th><td>' . htmlspecialchars($program->id) . '</td></tr>';
        $html .= '<tr><th style="text-align:left; background:#f8f9fa;">Perusahaan</th><td>' . htmlspecialchars($program->company) . '</td></tr>';
        $html .= '<tr><th style="text-align:left; background:#f8f9fa;">Lead Auditor</th><td>' . htmlspecialchars($program->auditor_name) . '</td></tr>';
        $html .= '<tr><th style="text-align:left; background:#f8f9fa;">Ruang Lingkup</th><td>' . htmlspecialchars($program->audit_scope) . '</td></tr>';
        $html .= '</table>';

        // Schedule table
        $html .= '<h3 style="color: #2c3e50;"><i class="fa fa-calendar-alt"></i> Jadwal Audit</h3>';
        $html .= '<table style="border-collapse: collapse; width: 100%;" border="1" cellpadding="8">';
        $html .= '<thead><tr style="background: #f8f9fa; text-align: center;">';
        $html .= '<th>No</th><th>Proses</th><th>Auditor</th><th>Department - Company</th><th>Tanggal</th><th>Mulai</th><th>Selesai</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($schedules as $k => $sched) {
            $process = !empty($sched->process_name) ? strip_tags($sched->process_name) : htmlspecialchars($sched->process_name_free);
            $auditor = htmlspecialchars($sched->auditor_name);

            // Department
            $dept = '-';
            if (!empty($sched->auditees)) {
                $auditee_names = [];
                foreach ($sched->auditees as $aud) {
                    $auditee_names[] = $aud->department_name;
                }
                $dept = implode(', ', $auditee_names);
            } elseif (!empty($sched->auditee_name_free)) {
                $dept = htmlspecialchars($sched->auditee_name_free);
            }

            $date = date('d-m-Y', strtotime($sched->audit_date));
            $start = substr($sched->start_time, 0, 5);
            $end = substr($sched->end_time, 0, 5);

            $html .= '<tr>';
            $html .= '<td style="text-align:center;">' . ($k + 1) . '</td>';
            $html .= '<td>' . $process . '</td>';
            $html .= '<td>' . $auditor . '</td>';
            $html .= '<td>' . $dept . '</td>';
            $html .= '<td style="text-align:center;">' . $date . '</td>';
            $html .= '<td style="text-align:center;">' . $start . '</td>';
            $html .= '<td style="text-align:center;">' . $end . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '<br><p style="color: #7f8c8d; font-size: 12px;">Email ini dikirim otomatis dari Sentral Sistem - Audit Module.</p>';
        $html .= '</body></html>';

        return $html;
    }

    /**
     * Delete - soft delete an audit program (set status to '0')
     * Returns JSON response. Prevents deletion if referenced by execution module.
     */
    public function delete()
    {
        $id = $this->input->post('id');

        if (!$id) {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid. Please try again.']);
            return;
        }

        // Check if program is referenced by execution module
        if ($this->audit_program_model->isReferencedByExecution($id)) {
            echo json_encode(['status' => 0, 'msg' => 'Audit Program sedang digunakan dan tidak dapat dihapus.']);
            return;
        }

        // Soft delete: set status to '0'
        $this->db->update('audit_program', ['status' => '0'], ['id' => $id]);

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 1, 'msg' => 'Audit Program berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Gagal menghapus Audit Program.']);
        }
    }

    /**
     * View - read-only display of an audit program and all associated data
     *
     * @param string $id Program ID (e.g., APR2506-001)
     */
    public function view($id = null)
    {
        if (!$id) {
            show_404();
            return;
        }

        $program = $this->audit_program_model->getProgramById($id);

        if (!$program) {
            show_404();
            return;
        }

        // Load all associated child records
        $evaluations = $this->audit_program_model->getEvaluations($id);
        $critical_issues = $this->audit_program_model->getCriticalIssues($id);
        $opportunities = $this->audit_program_model->getOpportunities($id);

        // Load schedules with auditees for each schedule row
        $schedules = $this->audit_program_model->getSchedules($id);
        foreach ($schedules as &$schedule) {
            $schedule->auditees = $this->audit_program_model->getScheduleAuditees($schedule->id);
        }

        $this->template->set([
            'program'         => $program,
            'evaluations'     => $evaluations,
            'critical_issues' => $critical_issues,
            'opportunities'   => $opportunities,
            'schedules'       => $schedules,
        ]);

        $this->template->render('view');
    }

    // =========================================================================
    // AJAX DATA ENDPOINTS
    // =========================================================================

    /**
     * Get temuan details for a selected audit_temuan record (AJAX)
     * Returns HTML partial showing weakness description, category, process, auditee
     *
     * @param string $audit_temuan_id Audit temuan ID
     */
    public function get_temuan_details($audit_temuan_id = null)
    {
        if (!$audit_temuan_id) {
            echo '<div class="alert alert-warning">No details available.</div>';
            return;
        }

        $details = $this->audit_program_model->getTemuanDetails($audit_temuan_id);

        if (empty($details)) {
            echo '<div class="alert alert-info"><i class="fa fa-info-circle mr-1"></i> No details available.</div>';
            return;
        }

        // Category mapping
        $categories = [
            '1' => '<span class="label label-success label-inline">Minor</span>',
            '2' => '<span class="label label-danger label-inline">Major</span>',
            '3' => '<span class="label label-warning label-inline">OFI</span>',
        ];

        $html = '<div class="table-responsive">';
        $html .= '<table class="table table-sm table-bordered table-hover">';
        $html .= '<thead class="table-light"><tr class="text-center">';
        $html .= '<th width="40"><input type="checkbox" id="check-all-temuan" title="Select All"></th>';
        $html .= '<th width="40">No</th>';
        $html .= '<th>Temuan / Weakness</th>';
        $html .= '<th width="100">Kategori</th>';
        $html .= '<th>Proses</th>';
        $html .= '<th>Auditee</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        foreach ($details as $k => $detail) {
            $no = $k + 1;
            $category_label = isset($categories[$detail->category]) ? $categories[$detail->category] : '-';
            $description = isset($detail->description) ? strip_tags($detail->description) : '-';
            $process = isset($detail->process) ? htmlspecialchars($detail->process) : '-';
            $auditee = isset($detail->auditee) ? htmlspecialchars($detail->auditee) : '-';

            $html .= '<tr>';
            $html .= '<td class="text-center"><input type="checkbox" class="check-temuan-item" data-id="' . htmlspecialchars($detail->id) . '" data-desc="' . htmlspecialchars($description) . '"></td>';
            $html .= '<td class="text-center">' . $no . '</td>';
            $html .= '<td><span class="temuan-weakness-text">' . $description . '</span></td>';
            $html .= '<td class="text-center">' . $category_label . '</td>';
            $html .= '<td>' . $process . '</td>';
            $html .= '<td>' . $auditee . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        $html .= '<script>$(document).on("change","#check-all-temuan",function(){$(".check-temuan-item").prop("checked",$(this).prop("checked"));});</script>';

        echo $html;
    }

    /**
     * Get saved evaluations for a program (AJAX)
     * Returns JSON array of evaluation records
     *
     * @param string $program_id Program ID
     */
    public function get_evaluations($program_id = null)
    {
        if (!$program_id) {
            echo json_encode(['status' => 0, 'data' => []]);
            return;
        }

        $evaluations = $this->audit_program_model->getEvaluations($program_id);

        echo json_encode(['status' => 1, 'data' => $evaluations]);
    }

    /**
     * Get saved critical issues for a program (AJAX)
     * Returns JSON array of critical issue records
     *
     * @param string $program_id Program ID
     */
    public function get_critical_issues($program_id = null)
    {
        if (!$program_id) {
            echo json_encode(['status' => 0, 'data' => []]);
            return;
        }

        $critical_issues = $this->audit_program_model->getCriticalIssues($program_id);

        echo json_encode(['status' => 1, 'data' => $critical_issues]);
    }

    /**
     * Get saved opportunities for a program (AJAX)
     * Returns JSON array of opportunity records
     *
     * @param string $program_id Program ID
     */
    public function get_opportunities($program_id = null)
    {
        if (!$program_id) {
            echo json_encode(['status' => 0, 'data' => []]);
            return;
        }

        $opportunities = $this->audit_program_model->getOpportunities($program_id);

        echo json_encode(['status' => 1, 'data' => $opportunities]);
    }

    /**
     * Get saved schedules with auditees for a program (AJAX)
     * Returns JSON array of schedule records including auditee departments
     *
     * @param string $program_id Program ID
     */
    public function get_schedules($program_id = null)
    {
        if (!$program_id) {
            echo json_encode(['status' => 0, 'data' => []]);
            return;
        }

        $schedules = $this->audit_program_model->getSchedules($program_id);

        // Attach auditees to each schedule row
        foreach ($schedules as &$schedule) {
            $schedule->auditees = $this->audit_program_model->getScheduleAuditees($schedule->id);
        }

        echo json_encode(['status' => 1, 'data' => $schedules]);
    }

    /**
     * Server-side validation for audit program data
     *
     * Validates all sections: header fields, critical issues, opportunities,
     * and schedule rows including time/date constraints.
     *
     * @param array $data The POST data array
     * @return array Array of error messages (empty array = valid)
     */
    private function _validate($data)
    {
        $errors = [];

        // 1. Validate Company: not empty, not whitespace-only, max 255 chars
        $company = isset($data['company']) ? $data['company'] : '';
        if (trim($company) === '') {
            $errors[] = 'Company field is required.';
        } elseif (mb_strlen($company) > 255) {
            $errors[] = 'Company field must not exceed 255 characters.';
        }

        // 2. Validate Lead Auditor: not empty/null
        $lead_auditor = isset($data['lead_auditor_id']) ? $data['lead_auditor_id'] : '';
        if (empty($lead_auditor)) {
            $errors[] = 'Lead Auditor must be selected.';
        }

        // 3. Validate Audit Scope: not empty, must be valid option
        $audit_scope = isset($data['audit_scope']) ? $data['audit_scope'] : '';
        $valid_scopes = ['Audit Khusus', 'Audit Regular', 'Audit Product', 'Audit Process'];
        if (empty($audit_scope)) {
            $errors[] = 'Audit Scope must be selected.';
        } elseif (!in_array($audit_scope, $valid_scopes)) {
            $errors[] = 'Audit Scope must be either Audit Khusus or Audit Regular.';
        }

        // 4. Validate Critical Issues (if any): issue_description must not be empty
        if (isset($data['issue_desc']) && is_array($data['issue_desc'])) {
            foreach ($data['issue_desc'] as $index => $desc) {
                if (trim($desc) === '') {
                    $row_num = $index + 1;
                    $errors[] = "Critical Issue row {$row_num}: Issue Description is required.";
                }
            }
        }

        // 5. Validate Opportunities (if any): procedure_id and description must not be empty
        if (isset($data['procedure_id']) && is_array($data['procedure_id'])) {
            foreach ($data['procedure_id'] as $index => $procId) {
                $row_num = $index + 1;
                if (empty($procId)) {
                    $errors[] = "Opportunity row {$row_num}: Procedure must be selected.";
                }
                $desc = isset($data['opportunity_desc'][$index]) ? $data['opportunity_desc'][$index] : '';
                if (trim($desc) === '') {
                    $errors[] = "Opportunity row {$row_num}: Description is required.";
                }
            }
        }

        // 6 & 7 & 8. Validate Schedule rows (if any)
        if (isset($data['schedule_process_id']) && is_array($data['schedule_process_id'])) {
            $today = date('Y-m-d');
            $schedProcessFree = isset($data['schedule_process_name_free']) ? $data['schedule_process_name_free'] : [];

            foreach ($data['schedule_process_id'] as $index => $processId) {
                $row_num = $index + 1;
                $freeText = isset($schedProcessFree[$index]) ? trim($schedProcessFree[$index]) : '';

                // Process: either select or free text must be filled
                if (empty($processId) && empty($freeText)) {
                    $errors[] = "Schedule row {$row_num}: Process must be selected or filled.";
                }
                if (empty($data['schedule_auditor_id'][$index])) {
                    $errors[] = "Schedule row {$row_num}: Auditor must be selected.";
                }
                // Department is now single select or free text
                $auditeeFreeText = isset($data['schedule_auditee_name_free'][$index]) ? trim($data['schedule_auditee_name_free'][$index]) : '';
                if (empty($data['schedule_auditee_id'][$index]) && empty($auditeeFreeText)) {
                    $errors[] = "Schedule row {$row_num}: Department must be selected or filled.";
                }

                $audit_date = isset($data['schedule_date'][$index]) ? $data['schedule_date'][$index] : '';
                $start_time = isset($data['schedule_start_time'][$index]) ? $data['schedule_start_time'][$index] : '';
                $end_time = isset($data['schedule_end_time'][$index]) ? $data['schedule_end_time'][$index] : '';

                if (empty($audit_date)) {
                    $errors[] = "Schedule row {$row_num}: Audit Date is required.";
                }
                if (empty($start_time)) {
                    $errors[] = "Schedule row {$row_num}: Start Time is required.";
                }
                if (empty($end_time)) {
                    $errors[] = "Schedule row {$row_num}: End Time is required.";
                }

                // 7. Validate end_time > start_time
                if (!empty($start_time) && !empty($end_time)) {
                    if ($end_time <= $start_time) {
                        $errors[] = "Schedule row {$row_num}: End Time must be later than Start Time.";
                    }
                }

                // 8. Validate audit_date >= today (only for new programs)
                if (!empty($audit_date) && empty($data['id'])) {
                    if ($audit_date < $today) {
                        $errors[] = "Schedule row {$row_num}: Audit Date must be today or a future date.";
                    }
                }
            }
        }

        return $errors;
    }
}
