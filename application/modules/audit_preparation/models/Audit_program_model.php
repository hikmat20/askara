<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Audit Program Model
 *
 * Model class for managing audit programs and related data.
 * Handles CRUD operations for audit_program table and queries
 * for child records and master data.
 *
 * @author Kiro
 * @copyright Copyright (c) 2025
 */
class Audit_program_model extends BF_Model
{
    /**
     * @var string Table name
     */
    protected $table_name = 'audit_program';

    /**
     * @var string Primary key
     */
    protected $key = 'id';

    /**
     * @var bool Auto-fill created timestamp
     */
    protected $set_created = true;

    /**
     * @var bool Auto-fill modified timestamp
     */
    protected $set_modified = true;

    /**
     * @var bool Soft deletes disabled (uses status field instead)
     */
    protected $soft_deletes = false;

    /**
     * @var string Date format for created/modified fields
     */
    protected $date_format = 'datetime';

    /**
     * @var bool Log user ID on create/modify
     */
    protected $log_user = true;

    /**
     * @var string Created timestamp field name
     */
    protected $created_field = 'created_at';

    /**
     * @var string Modified timestamp field name
     */
    protected $modified_field = 'modified_at';

    /**
     * @var string Created by field name
     */
    protected $created_by_field = 'created_by';

    /**
     * @var string Modified by field name
     */
    protected $modified_by_field = 'modified_by';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    // =========================================================================
    // PROGRAM QUERIES
    // =========================================================================

    /**
     * Get all active audit programs with lead auditor name, ordered by created_at DESC
     *
     * @return array
     */
    public function getActivePrograms()
    {
        return $this->db->select('audit_program.*, audit_auditor_consultant.name as auditor_name')
            ->from('audit_program')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program.lead_auditor_id', 'left')
            ->where('audit_program.status', '1')
            ->order_by('audit_program.created_at', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Get a single audit program by ID with all joined data
     *
     * @param string $id Program ID
     * @return object|null
     */
    public function getProgramById($id)
    {
        return $this->db->select('audit_program.*, audit_auditor_consultant.name as auditor_name, users.full_name as created_by_name')
            ->from('audit_program')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program.lead_auditor_id', 'left')
            ->join('users', 'users.id_user = audit_program.created_by', 'left')
            ->where('audit_program.id', $id)
            ->get()
            ->row();
    }

    // =========================================================================
    // MASTER DATA QUERIES
    // =========================================================================

    /**
     * Get active auditors (position contains '1' = Auditor, status = '1')
     *
     * @return array
     */
    public function getActiveAuditors()
    {
        return $this->db->like('position', '1')
            ->get_where('audit_auditor_consultant', ['status' => '1'])
            ->result();
    }

    /**
     * Get active audit processes (status = '1')
     *
     * @return array
     */
    public function getActiveProcesses()
    {
        return $this->db->get_where('audit_process', ['status' => '1'])
            ->result();
    }

    /**
     * Get active/published procedures (status = 'PUB')
     *
     * @return array
     */
    public function getActiveProcedures($company_id = null)
    {
        $this->db->where('status', 'PUB');
        if ($company_id) {
            $this->db->where('company_id', $company_id);
        }
        return $this->db->get('procedures')->result();
    }

    /**
     * Get active audit temuan records (status = '1')
     * Returns temuan with company and badan sertifikasi info
     *
     * @return array
     */
    public function getActiveTemuan()
    {
        return $this->db->select('audit_temuan.*, audit_company.company_name as company_name, audit_badan_sertifikasi.name as badan_sertifikasi_name')
            ->from('audit_temuan')
            ->join('audit_company', 'audit_company.id = audit_temuan.company_id', 'left')
            ->join('audit_badan_sertifikasi', 'audit_badan_sertifikasi.id = audit_temuan.badan_sert_id', 'left')
            ->order_by('audit_temuan.date', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Get temuan details for a specific audit_temuan record
     *
     * @param string $temuan_id Audit temuan ID
     * @return array
     */
    public function getTemuanDetails($temuan_id)
    {
        return $this->db->select('audit_temuan_details.*')
            ->from('audit_temuan_details')
            ->where('audit_temuan_details.audit_id', $temuan_id)
            ->where('audit_temuan_details.status', '1')
            ->get()
            ->result();
    }

    /**
     * Get all departments/companies for auditee selection
     *
     * @return array
     */
    public function getDepartments($company_id = null)
    {
        return $this->db->select('id, department_name as name')
            ->where('status', '1')
            ->order_by('department_name', 'ASC')
            ->get('audit_department')
            ->result();
    }

    // =========================================================================
    // CHILD RECORD QUERIES
    // =========================================================================

    /**
     * Get evaluations for a specific program
     *
     * @param string $program_id Program ID
     * @return array
     */
    public function getEvaluations($program_id)
    {
        return $this->db->select('audit_program_evaluation.*, audit_temuan.id as temuan_ref_id')
            ->from('audit_program_evaluation')
            ->join('audit_temuan', 'audit_temuan.id = audit_program_evaluation.audit_temuan_id', 'left')
            ->where('audit_program_evaluation.program_id', $program_id)
            ->where('audit_program_evaluation.status', '1')
            ->get()
            ->result();
    }

    /**
     * Get critical issues for a specific program
     *
     * @param string $program_id Program ID
     * @return array
     */
    public function getCriticalIssues($program_id)
    {
        return $this->db->get_where('audit_program_critical_issue', [
            'program_id' => $program_id,
            'status' => '1'
        ])->result();
    }

    /**
     * Get opportunities/problems for a specific program
     *
     * @param string $program_id Program ID
     * @return array
     */
    public function getOpportunities($program_id)
    {
        return $this->db->select('audit_program_opportunity.*, audit_program_opportunity.description as issue_text, procedures.name as procedure_name')
            ->from('audit_program_opportunity')
            ->join('procedures', 'procedures.id = audit_program_opportunity.procedure_id', 'left')
            ->where('audit_program_opportunity.program_id', $program_id)
            ->where('audit_program_opportunity.status', '1')
            ->get()
            ->result();
    }

    /**
     * Get schedules for a specific program
     *
     * @param string $program_id Program ID
     * @return array
     */
    public function getSchedules($program_id)
    {
        return $this->db->select('audit_program_schedule.*, procedures.name as process_name, audit_auditor_consultant.name as auditor_name, requirements.name as requirement_name')
            ->from('audit_program_schedule')
            ->join('procedures', 'procedures.id = audit_program_schedule.process_id', 'left')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program_schedule.auditor_id', 'left')
            ->join('requirements', 'requirements.id = audit_program_schedule.requirement_id', 'left')
            ->where('audit_program_schedule.program_id', $program_id)
            ->where('audit_program_schedule.status', '1')
            ->get()
            ->result();
    }

    /**
     * Get published requirements (Index of Standard) for audit persyaratan
     *
     * @return array
     */
    public function getPublishedRequirements()
    {
        return $this->db->select('id, name as nama')
            ->where('status', '1')
            ->where('deleted_at', null)
            ->order_by('name', 'ASC')
            ->get('requirements')
            ->result();
    }

    /**
     * Get auditees (departments) for a specific schedule row
     *
     * @param int $schedule_id Schedule row ID
     * @return array
     */
    public function getScheduleAuditees($schedule_id)
    {
        return $this->db->select('audit_program_schedule_auditee.*, audit_department.department_name as department_name')
            ->from('audit_program_schedule_auditee')
            ->join('audit_department', 'audit_department.id = audit_program_schedule_auditee.department_id', 'left')
            ->where('audit_program_schedule_auditee.schedule_id', $schedule_id)
            ->get()
            ->result();
    }

    // =========================================================================
    // ID GENERATION
    // =========================================================================

    /**
     * Generate a unique ID following the APR{YYMM}-{NNN} pattern
     * Sequence resets to 001 each month
     *
     * @return string Generated ID (e.g., APR2506-001)
     */
    public function generateId()
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

    // =========================================================================
    // VALIDATION HELPERS
    // =========================================================================

    /**
     * Check if an audit program is referenced by the audit execution module
     * Prevents deletion of programs in use
     *
     * @param string $program_id Program ID
     * @return bool True if referenced, false otherwise
     */
    public function isReferencedByExecution($program_id)
    {
        // Check if audit_execution table exists first
        if (!$this->db->table_exists('audit_execution')) {
            return false;
        }

        $count = $this->db->where('program_id', $program_id)
            ->count_all_results('audit_execution');

        return $count > 0;
    }

    /**
     * Detect scheduling conflicts - overlapping auditor assignments
     * A conflict exists when the same auditor is assigned to multiple schedules
     * on the same date with overlapping time ranges.
     *
     * @param array $schedules Array of schedule data, each containing:
     *   - auditor_id: string
     *   - audit_date: string (Y-m-d)
     *   - start_time: string (H:i or H:i:s)
     *   - end_time: string (H:i or H:i:s)
     * @return array Array of conflict descriptions
     */
    public function detectScheduleConflicts($schedules)
    {
        $conflicts = [];

        for ($i = 0; $i < count($schedules); $i++) {
            for ($j = $i + 1; $j < count($schedules); $j++) {
                $a = $schedules[$i];
                $b = $schedules[$j];

                // Same auditor and same date
                if ($a['auditor_id'] == $b['auditor_id'] && $a['audit_date'] == $b['audit_date']) {
                    // Check time overlap: A.start < B.end AND B.start < A.end
                    if ($a['start_time'] < $b['end_time'] && $b['start_time'] < $a['end_time']) {
                        $conflicts[] = [
                            'row1' => $i,
                            'row2' => $j,
                            'auditor_id' => $a['auditor_id'],
                            'date' => $a['audit_date'],
                            'message' => "Auditor conflict on row " . ($i + 1) . " and row " . ($j + 1) . " (same auditor, same date, overlapping time)"
                        ];
                    }
                }
            }
        }

        return $conflicts;
    }
}
