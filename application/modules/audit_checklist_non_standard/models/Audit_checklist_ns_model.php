<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Audit Checklist Audit Berdasarkan Kinerja Model
 *
 * Handles data retrieval from audit_program_schedule and audit_program_opportunity,
 * and CRUD operations for non-standard checklist items.
 */
class Audit_checklist_ns_model extends BF_Model
{
    protected $table_name  = 'audit_checklist_non_standard';
    protected $key         = 'id';
    protected $set_created = false;
    protected $set_modified = false;
    protected $soft_deletes = false;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all active schedules from all active audit programs
     * Joins with procedures, auditor, department, and program
     *
     * @return array
     */
    public function getAllSchedules()
    {
        return $this->db->select('
                audit_program_schedule.id as schedule_id,
                audit_program_schedule.program_id,
                audit_program_schedule.process_id,
                audit_program_schedule.requirement_id,
                audit_program_schedule.audit_date,
                audit_program_schedule.start_time,
                audit_program_schedule.end_time,
                audit_program_schedule.process_name_free,
                procedures.name as process_name,
                requirements.name as requirement_name,
                audit_auditor_consultant.name as auditor_name,
                CONCAT_WS(\', \',
                    (SELECT GROUP_CONCAT(d.name SEPARATOR \', \') 
                     FROM audit_program_schedule_auditee apsa 
                     JOIN departements d ON d.id = apsa.department_id 
                     WHERE apsa.schedule_id = audit_program_schedule.id), 
                    NULLIF(audit_program_schedule.auditee_name_free, \'\')
                ) as department_name,
                audit_program.company,
                audit_program.id as program_code
            ')
            ->from('audit_program_schedule')
            ->join('audit_program', 'audit_program.id = audit_program_schedule.program_id', 'left')
            ->join('procedures', 'procedures.id = audit_program_schedule.process_id', 'left')
            ->join('requirements', 'requirements.id = audit_program_schedule.requirement_id', 'left')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program_schedule.auditor_id', 'left')
            ->where('audit_program_schedule.status', '1')
            ->where('audit_program.status', '1')
            ->order_by('audit_program_schedule.audit_date', 'DESC')
            ->order_by('audit_program_schedule.start_time', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Get a single schedule row by its ID with joined info
     *
     * @param int $schedule_id
     * @return object|null
     */
    public function getScheduleById($schedule_id)
    {
        return $this->db->select('
                audit_program_schedule.id as schedule_id,
                audit_program_schedule.program_id,
                audit_program_schedule.process_id,
                audit_program_schedule.requirement_id,
                audit_program_schedule.audit_date,
                audit_program_schedule.start_time,
                audit_program_schedule.end_time,
                audit_program_schedule.process_name_free,
                procedures.name as process_name,
                requirements.name as requirement_name,
                audit_auditor_consultant.name as auditor_name,
                CONCAT_WS(\', \',
                    (SELECT GROUP_CONCAT(d.name SEPARATOR \', \') 
                     FROM audit_program_schedule_auditee apsa 
                     JOIN departements d ON d.id = apsa.department_id 
                     WHERE apsa.schedule_id = audit_program_schedule.id), 
                    NULLIF(audit_program_schedule.auditee_name_free, \'\')
                ) as department_name,
                audit_program.company,
                audit_program.id as program_code
            ')
            ->from('audit_program_schedule')
            ->join('audit_program', 'audit_program.id = audit_program_schedule.program_id', 'left')
            ->join('procedures', 'procedures.id = audit_program_schedule.process_id', 'left')
            ->join('requirements', 'requirements.id = audit_program_schedule.requirement_id', 'left')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program_schedule.auditor_id', 'left')
            ->where('audit_program_schedule.id', $schedule_id)
            ->where('audit_program_schedule.status', '1')
            ->get()
            ->row();
    }

    /**
     * Get issues from Isu Proses (audit_program_opportunity) that match a given process
     * Matches by procedure_id = process_id from the schedule
     *
     * @param string $program_id The audit program ID
     * @param string $process_id The process/procedure ID from the schedule
     * @return array
     */
    public function getIssuesByProcess($program_id, $process_id)
    {
        if (empty($process_id)) {
            return [];
        }

        return $this->db->select('audit_program_opportunity.*, procedures.name as procedure_name')
            ->from('audit_program_opportunity')
            ->join('procedures', 'procedures.id = audit_program_opportunity.procedure_id', 'left')
            ->where('audit_program_opportunity.program_id', $program_id)
            ->where('audit_program_opportunity.procedure_id', $process_id)
            ->where('audit_program_opportunity.status', '1')
            ->get()
            ->result();
    }

    /**
     * Count active checklist items for a given schedule
     *
     * @param int $schedule_id
     * @return int
     */
    public function countChecklistByScheduleId($schedule_id)
    {
        return $this->db->where(['schedule_id' => $schedule_id, 'status' => '1'])
            ->count_all_results('audit_checklist_non_standard');
    }

    /**
     * Get existing non-standard checklist items for a given schedule
     *
     * @param int $schedule_id
     * @return array
     */
    public function getChecklistByScheduleId($schedule_id)
    {
        return $this->db->get_where('audit_checklist_non_standard', [
            'schedule_id' => $schedule_id,
            'status'      => '1'
        ])->result();
    }

    /**
     * Save checklist items for a schedule
     * Deletes removed items (soft delete) and inserts/updates
     *
     * @param int   $schedule_id
     * @param array $items Array of checklist text items
     * @param int   $userId
     * @return bool
     */
    public function saveChecklist($schedule_id, $items, $userId)
    {
        $this->db->trans_begin();
        $now = date('Y-m-d H:i:s');

        // Soft-delete all existing items for this schedule
        $this->db->update('audit_checklist_non_standard', ['status' => '0'], ['schedule_id' => $schedule_id]);

        // Insert new items
        if (!empty($items)) {
            foreach ($items as $item) {
                $text = isset($item['text']) ? trim($item['text']) : '';
                if ($text === '') continue;

                $insertData = [
                    'schedule_id' => $schedule_id,
                    'checklist_text' => $text,
                    'status'      => '1',
                    'created_at'  => $now,
                    'created_by'  => $userId,
                ];
                $this->db->insert('audit_checklist_non_standard', $insertData);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

    /**
     * Delete a single checklist item (soft delete)
     *
     * @param int $id
     * @return bool
     */
    public function deleteChecklistItem($id)
    {
        $this->db->update('audit_checklist_non_standard', ['status' => '0'], ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }
}
