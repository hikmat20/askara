<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Pelaksanaan Audit Model
 */
class Pelaksanaan_audit_model extends BF_Model
{
    protected $table_name  = 'pelaksanaan_audit';
    protected $key         = 'id';
    protected $set_created = false;
    protected $set_modified = false;
    protected $soft_deletes = false;

    public function __construct()
    {
        parent::__construct();
    }

    // =========================================================================
    // SCHEDULE DATA (from audit_program_schedule)
    // =========================================================================

    public function getAllSchedules()
    {
        // Ambil schedule yang punya Checklist Non Standard ATAU punya Checklist Standard (via procedure)
        return $this->db->select('
                audit_program_schedule.id as schedule_id,
                audit_program_schedule.program_id,
                audit_program_schedule.process_id,
                audit_program_schedule.audit_date,
                audit_program_schedule.start_time,
                audit_program_schedule.end_time,
                audit_program_schedule.process_name_free,
                procedures.name as process_name,
                audit_auditor_consultant.name as auditor_name,
                audit_department.department_name,
                audit_program.company,
                audit_program.id as program_code
            ')
            ->from('audit_program_schedule')
            ->join('audit_program', 'audit_program.id = audit_program_schedule.program_id', 'left')
            ->join('procedures', 'procedures.id = audit_program_schedule.process_id', 'left')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program_schedule.auditor_id', 'left')
            ->join('audit_program_schedule_auditee', 'audit_program_schedule_auditee.schedule_id = audit_program_schedule.id', 'left')
            ->join('audit_department', 'audit_department.id = audit_program_schedule_auditee.department_id', 'left')
            ->where('audit_program_schedule.status', '1')
            ->where('audit_program.status', '1')
            ->where('(audit_program_schedule.id IN (SELECT DISTINCT schedule_id FROM audit_checklist_non_standard WHERE status = "1") OR audit_program_schedule.process_id IN (SELECT DISTINCT procedure_id FROM audit_checklist WHERE status = "1"))', null, false)
            ->order_by('audit_program_schedule.audit_date', 'DESC')
            ->order_by('audit_program_schedule.start_time', 'ASC')
            ->get()
            ->result();
    }

    public function getScheduleById($schedule_id)
    {
        return $this->db->select('
                audit_program_schedule.id as schedule_id,
                audit_program_schedule.program_id,
                audit_program_schedule.process_id,
                audit_program_schedule.audit_date,
                audit_program_schedule.start_time,
                audit_program_schedule.end_time,
                audit_program_schedule.process_name_free,
                procedures.name as process_name,
                audit_auditor_consultant.name as auditor_name,
                audit_department.department_name,
                audit_program.company,
                audit_program.id as program_code
            ')
            ->from('audit_program_schedule')
            ->join('audit_program', 'audit_program.id = audit_program_schedule.program_id', 'left')
            ->join('procedures', 'procedures.id = audit_program_schedule.process_id', 'left')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program_schedule.auditor_id', 'left')
            ->join('audit_program_schedule_auditee', 'audit_program_schedule_auditee.schedule_id = audit_program_schedule.id', 'left')
            ->join('audit_department', 'audit_department.id = audit_program_schedule_auditee.department_id', 'left')
            ->where('audit_program_schedule.id', $schedule_id)
            ->where('audit_program_schedule.status', '1')
            ->get()
            ->row();
    }

    // =========================================================================
    // ISU PROSES
    // =========================================================================

    public function getIssuesByProcess($program_id, $process_id)
    {
        if (empty($process_id)) return [];

        return $this->db->select('audit_program_opportunity.*, procedures.name as procedure_name')
            ->from('audit_program_opportunity')
            ->join('procedures', 'procedures.id = audit_program_opportunity.procedure_id', 'left')
            ->where('audit_program_opportunity.program_id', $program_id)
            ->where('audit_program_opportunity.procedure_id', $process_id)
            ->where('audit_program_opportunity.status', '1')
            ->get()
            ->result();
    }

    // =========================================================================
    // CHECKLIST DATA
    // =========================================================================

    /**
     * Get non-standard checklist items for a schedule
     */
    public function getNonStandardChecklist($schedule_id)
    {
        return $this->db->get_where('audit_checklist_non_standard', [
            'schedule_id' => $schedule_id,
            'status'      => '1'
        ])->result();
    }

    /**
     * Get standard checklist items from audit_checklist module
     * Based on procedure_id matching
     */
    public function getStandardChecklist($process_id, $company_id)
    {
        if (empty($process_id)) return [];

        $checklist = $this->db->get_where('audit_checklist', [
            'procedure_id' => $process_id,
            'status'       => '1'
        ])->row();

        if (!$checklist) return [];

        return $this->db->get_where('audit_checklist_details', [
            'checklist_id' => $checklist->id,
            'status'       => '1'
        ])->result();
    }

    // =========================================================================
    // REQUIREMENTS (ISO Standards & Pasal)
    // =========================================================================

    public function getRequirements()
    {
        return $this->db->get_where('requirements', [
            'deleted_at' => null,
            'status'     => '1'
        ])->result();
    }

    public function getPasalByRequirement($requirement_id)
    {
        return $this->db->get_where('requirement_details', [
            'requirement_id' => $requirement_id
        ])->result();
    }

    // =========================================================================
    // PELAKSANAAN AUDIT CRUD
    // =========================================================================

    public function countAuditByScheduleId($schedule_id)
    {
        return $this->db->where(['schedule_id' => $schedule_id, 'status' => '1'])
            ->count_all_results('pelaksanaan_audit');
    }

    public function getAuditByScheduleId($schedule_id)
    {
        return $this->db->get_where('pelaksanaan_audit', [
            'schedule_id' => $schedule_id,
            'status'      => '1'
        ])->row();
    }

    public function getAuditNsDetails($audit_id)
    {
        return $this->db->get_where('pelaksanaan_audit_ns_details', [
            'audit_id' => $audit_id,
            'status'   => '1'
        ])->result();
    }

    public function getAuditStdDetails($audit_id)
    {
        return $this->db->get_where('pelaksanaan_audit_std_details', [
            'audit_id' => $audit_id,
            'status'   => '1'
        ])->result();
    }

    public function getAuditConformity($audit_id)
    {
        return $this->db->get_where('pelaksanaan_audit_conformity', [
            'audit_id' => $audit_id,
            'status'   => '1'
        ])->result();
    }

    public function getAuditTemuan($audit_id)
    {
        return $this->db->get_where('pelaksanaan_audit_temuan', [
            'audit_id' => $audit_id,
            'status'   => '1'
        ])->result();
    }

    // =========================================================================
    // SAVE AUDIT
    // =========================================================================

    public function saveAudit($data, $userId)
    {
        $this->db->trans_begin();
        $now = date('Y-m-d H:i:s');

        $schedule_id = $data['schedule_id'];
        $ns_details = isset($data['ns_detail']) ? $data['ns_detail'] : [];
        $std_details = isset($data['std_detail']) ? $data['std_detail'] : [];
        $conformity = isset($data['conformity']) ? $data['conformity'] : [];
        $temuan = isset($data['temuan']) ? $data['temuan'] : [];

        // Check if audit already exists for this schedule
        $existing = $this->getAuditByScheduleId($schedule_id);

        if ($existing) {
            $audit_id = $existing->id;
            $this->db->update('pelaksanaan_audit', [
                'modified_at' => $now,
                'modified_by' => $userId,
            ], ['id' => $audit_id]);
        } else {
            $audit_id = $this->generateAuditId();
            $this->db->insert('pelaksanaan_audit', [
                'id'          => $audit_id,
                'schedule_id' => $schedule_id,
                'status'      => '1',
                'created_at'  => $now,
                'created_by'  => $userId,
            ]);
        }

        // Save NS Details (non-standard checklist audit details)
        $this->saveNsDetails($audit_id, $ns_details, $userId, $now);

        // Save Standard Details
        $this->saveStdDetails($audit_id, $std_details, $userId, $now);

        // Save Conformity/Strong Point
        $this->saveConformity($audit_id, $conformity, $userId, $now);

        // Save Temuan
        $this->saveTemuan($audit_id, $temuan, $userId, $now);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

    private function generateAuditId()
    {
        $count = 1;
        $result = $this->db->select('MAX(RIGHT(id,3)) as id')
            ->from('pelaksanaan_audit')
            ->where(['SUBSTR(id,3,4)' => date('ym')])
            ->get()->row();

        if ($result && $result->id > 0) {
            $count = $result->id + 1;
        }
        return "PA" . date('ym-') . sprintf("%03d", $count);
    }

    private function saveNsDetails($audit_id, $items, $userId, $now)
    {
        // Soft delete existing
        $this->db->update('pelaksanaan_audit_ns_details', ['status' => '0'], ['audit_id' => $audit_id]);

        if (!empty($items)) {
            foreach ($items as $item) {
                $checklist_id = isset($item['checklist_id']) ? $item['checklist_id'] : '';
                if (empty($checklist_id)) continue;

                $insertData = [
                    'audit_id'      => $audit_id,
                    'checklist_id'  => $checklist_id,
                    'catatan'       => isset($item['catatan']) ? $item['catatan'] : '',
                    'kategori'      => isset($item['kategori']) ? $item['kategori'] : '',
                    'iso_id'        => isset($item['iso_id']) ? $item['iso_id'] : null,
                    'pasal_id'      => isset($item['pasal_id']) ? $item['pasal_id'] : null,
                    'status'        => '1',
                    'created_at'    => $now,
                    'created_by'    => $userId,
                ];

                // Keep existing file if re-saving
                if (isset($item['id']) && $item['id']) {
                    $old = $this->db->get_where('pelaksanaan_audit_ns_details', ['id' => $item['id']])->row();
                    if ($old) {
                        $insertData['file_name'] = $old->file_name;
                        $insertData['file_type'] = $old->file_type;
                        $insertData['file_size'] = $old->file_size;
                    }
                    $insertData['id'] = $item['id'];
                    $this->db->update('pelaksanaan_audit_ns_details', $insertData, ['id' => $item['id']]);
                } else {
                    $this->db->insert('pelaksanaan_audit_ns_details', $insertData);
                }
            }
        }
    }

    private function saveStdDetails($audit_id, $items, $userId, $now)
    {
        // Soft delete existing
        $this->db->update('pelaksanaan_audit_std_details', ['status' => '0'], ['audit_id' => $audit_id]);

        if (!empty($items)) {
            foreach ($items as $item) {
                $checklist_detail_id = isset($item['checklist_detail_id']) ? $item['checklist_detail_id'] : '';
                if (empty($checklist_detail_id)) continue;

                $insertData = [
                    'audit_id'            => $audit_id,
                    'checklist_detail_id' => $checklist_detail_id,
                    'catatan'             => isset($item['catatan']) ? $item['catatan'] : '',
                    'kategori'            => isset($item['kategori']) ? $item['kategori'] : '',
                    'iso_id'              => isset($item['iso_id']) ? $item['iso_id'] : null,
                    'pasal_id'            => isset($item['pasal_id']) ? $item['pasal_id'] : null,
                    'status'              => '1',
                    'created_at'          => $now,
                    'created_by'          => $userId,
                ];

                if (isset($item['id']) && $item['id']) {
                    $old = $this->db->get_where('pelaksanaan_audit_std_details', ['id' => $item['id']])->row();
                    if ($old) {
                        $insertData['file_name'] = $old->file_name;
                        $insertData['file_type'] = $old->file_type;
                        $insertData['file_size'] = $old->file_size;
                    }
                    $insertData['id'] = $item['id'];
                    $this->db->update('pelaksanaan_audit_std_details', $insertData, ['id' => $item['id']]);
                } else {
                    $this->db->insert('pelaksanaan_audit_std_details', $insertData);
                }
            }
        }
    }

    private function saveConformity($audit_id, $items, $userId, $now)
    {
        // Soft delete existing
        $this->db->update('pelaksanaan_audit_conformity', ['status' => '0'], ['audit_id' => $audit_id]);

        if (!empty($items)) {
            foreach ($items as $item) {
                $description = isset($item['description']) ? trim($item['description']) : '';
                if ($description === '') continue;

                $insertData = [
                    'audit_id'    => $audit_id,
                    'description' => $description,
                    'kategori'    => isset($item['kategori']) ? $item['kategori'] : '',
                    'iso_id'      => isset($item['iso_id']) ? $item['iso_id'] : null,
                    'pasal_id'    => isset($item['pasal_id']) ? $item['pasal_id'] : null,
                    'status'      => '1',
                    'created_at'  => $now,
                    'created_by'  => $userId,
                ];

                if (isset($item['id']) && $item['id']) {
                    $old = $this->db->get_where('pelaksanaan_audit_conformity', ['id' => $item['id']])->row();
                    if ($old) {
                        $insertData['file_name'] = $old->file_name;
                        $insertData['file_type'] = $old->file_type;
                        $insertData['file_size'] = $old->file_size;
                    }
                    $insertData['id'] = $item['id'];
                    $this->db->update('pelaksanaan_audit_conformity', $insertData, ['id' => $item['id']]);
                } else {
                    $this->db->insert('pelaksanaan_audit_conformity', $insertData);
                }
            }
        }
    }

    private function saveTemuan($audit_id, $items, $userId, $now)
    {
        // Soft delete existing
        $this->db->update('pelaksanaan_audit_temuan', ['status' => '0'], ['audit_id' => $audit_id]);

        if (!empty($items)) {
            foreach ($items as $item) {
                $description = isset($item['description']) ? trim($item['description']) : '';
                if ($description === '') continue;

                $insertData = [
                    'audit_id'    => $audit_id,
                    'description' => $description,
                    'kategori'    => isset($item['kategori']) ? $item['kategori'] : '',
                    'iso_id'      => isset($item['iso_id']) ? $item['iso_id'] : null,
                    'pasal_id'    => isset($item['pasal_id']) ? $item['pasal_id'] : null,
                    'status'      => '1',
                    'created_at'  => $now,
                    'created_by'  => $userId,
                ];

                if (isset($item['id']) && $item['id']) {
                    $old = $this->db->get_where('pelaksanaan_audit_temuan', ['id' => $item['id']])->row();
                    if ($old) {
                        $insertData['file_name'] = $old->file_name;
                        $insertData['file_type'] = $old->file_type;
                        $insertData['file_size'] = $old->file_size;
                    }
                    $insertData['id'] = $item['id'];
                    $this->db->update('pelaksanaan_audit_temuan', $insertData, ['id' => $item['id']]);
                } else {
                    $this->db->insert('pelaksanaan_audit_temuan', $insertData);
                }
            }
        }
    }

    // =========================================================================
    // EVIDENCE & DELETE
    // =========================================================================

    public function updateEvidence($type, $detail_id, $fileInfo)
    {
        $table_map = [
            'ns_detail'  => 'pelaksanaan_audit_ns_details',
            'std_detail' => 'pelaksanaan_audit_std_details',
            'conformity' => 'pelaksanaan_audit_conformity',
            'temuan'     => 'pelaksanaan_audit_temuan',
        ];

        if (!isset($table_map[$type])) return false;

        $this->db->update($table_map[$type], $fileInfo, ['id' => $detail_id]);
        return $this->db->affected_rows() > 0;
    }

    public function deleteConformity($id)
    {
        return $this->db->update('pelaksanaan_audit_conformity', ['status' => '0'], ['id' => $id]);
    }

    public function deleteTemuan($id)
    {
        return $this->db->update('pelaksanaan_audit_temuan', ['status' => '0'], ['id' => $id]);
    }
}
