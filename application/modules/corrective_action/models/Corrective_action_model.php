<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Corrective Action Model
 *
 * Model class for table "corrective_action"
 * Handles CA number generation, status retrieval, and base CRUD operations.
 */

class Corrective_action_model extends BF_Model
{

    /**
     * @var string Table Name
     */
    protected $table_name = 'corrective_action';

    /**
     * @var string Primary Key
     */
    protected $key = 'id';

    /**
     * @var bool Disable auto-setting created timestamp (handled manually)
     */
    protected $set_created = false;

    /**
     * @var bool Disable auto-setting modified timestamp (handled manually)
     */
    protected $set_modified = false;

    /**
     * @var bool Enable soft deletes
     */
    protected $soft_deletes = true;

    /**
     * @var string Date format type
     */
    protected $date_format = 'datetime';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate CA Number with format CA{YY}{MM}-{NNN}
     * Sequential per month, resets each month.
     *
     * @return string Generated CA number (e.g., CA2506-001)
     */
    public function generateCANumber()
    {
        $yearMonth = date('ym');
        $result = $this->db->select("MAX(CAST(RIGHT(id, 3) AS UNSIGNED)) as max_seq")
            ->from('corrective_action')
            ->where("SUBSTR(id, 3, 4) = '{$yearMonth}'", null, false)
            ->get()
            ->row();

        $nextSeq = ($result && $result->max_seq > 0) ? $result->max_seq + 1 : 1;
        return "CA" . $yearMonth . "-" . sprintf("%03d", $nextSeq);
    }

    /**
     * Get the current status of a corrective action by ID
     *
     * @param string $ca_id The corrective action ID
     * @return string|null The status_ca value, or null if not found
     */
    public function getStatus($ca_id)
    {
        $row = $this->db->select('status_ca')
            ->from('corrective_action')
            ->where('id', $ca_id)
            ->where('deleted', '0')
            ->get()
            ->row();

        return $row ? $row->status_ca : null;
    }

    // =========================================================================
    // DATA RETRIEVAL
    // =========================================================================

    /**
     * Get index data - all pelaksanaan_audit with active temuan, with CA status
     *
     * @param int $company_id
     * @return array
     */
    public function getIndexData($company_id)
    {
        return $this->db->select('
                pa.id as pelaksanaan_id,
                aps.audit_date,
                COALESCE(p.name, aps.process_name_free) as process_name,
                COALESCE(ad.name, aps.auditee_name_free) as department_name,
                aac.name as auditor_name,
                COUNT(pat.id) as temuan_count,
                GROUP_CONCAT(DISTINCT pat.kategori SEPARATOR ", ") as kategori,
                ca.id as ca_id,
                ca.status_ca
            ')
            ->from('pelaksanaan_audit pa')
            ->join('audit_program_schedule aps', 'aps.id = pa.schedule_id', 'left')
            ->join('procedures p', 'p.id = aps.process_id', 'left')
            ->join('audit_auditor_consultant aac', 'aac.id = aps.auditor_id', 'left')
            ->join('audit_program_schedule_auditee apsa', 'apsa.schedule_id = aps.id', 'left')
            ->join('departements ad', 'ad.id = apsa.department_id', 'left')
            ->join('pelaksanaan_audit_temuan pat', 'pat.audit_id = pa.id AND pat.status = "1"', 'inner')
            ->join('corrective_action ca', 'ca.pelaksanaan_id = pa.id AND ca.deleted = "0"', 'left')
            ->where('pa.status', '1')
            ->group_by('pa.id, aps.audit_date, p.name, aps.process_name_free, ad.department_name, aps.auditee_name_free, aac.name, ca.id, ca.status_ca')
            ->order_by('aps.audit_date', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Get approval index data - only Waiting Approval and Approved CAs
     *
     * @return array
     */
    public function getApprovalIndexData()
    {
        return $this->db->select('
                ca.id as ca_id,
                aps.audit_date,
                COALESCE(p.name, aps.process_name_free) as process_name,
                COALESCE(ad.name, aps.auditee_name_free) as department_name,
                aac.name as auditor_name,
                COUNT(pat.id) as temuan_count,
                GROUP_CONCAT(DISTINCT pat.kategori SEPARATOR ", ") as kategori,
                ca.status_ca
            ')
            ->from('corrective_action ca')
            ->join('pelaksanaan_audit pa', 'pa.id = ca.pelaksanaan_id', 'left')
            ->join('audit_program_schedule aps', 'aps.id = pa.schedule_id', 'left')
            ->join('procedures p', 'p.id = aps.process_id', 'left')
            ->join('audit_auditor_consultant aac', 'aac.id = aps.auditor_id', 'left')
            ->join('audit_program_schedule_auditee apsa', 'apsa.schedule_id = aps.id', 'left')
            ->join('departements ad', 'ad.id = apsa.department_id', 'left')
            ->join('pelaksanaan_audit_temuan pat', 'pat.audit_id = pa.id AND pat.status = "1"', 'left')
            ->where_in('ca.status_ca', ['waiting_approval', 'approved'])
            ->where('ca.deleted', '0')
            ->group_by('ca.id, aps.audit_date, p.name, aps.process_name_free, ad.department_name, aps.auditee_name_free, aac.name, ca.status_ca')
            ->order_by('aps.audit_date', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Get record index data - only Approved CAs
     *
     * @return array
     */
    public function getRecordIndexData()
    {
        return $this->db->select('
                ca.id as ca_id,
                aps.audit_date,
                COALESCE(p.name, aps.process_name_free) as process_name,
                COALESCE(ad.name, aps.auditee_name_free) as department_name,
                aac.name as auditor_name,
                COUNT(pat.id) as temuan_count,
                GROUP_CONCAT(DISTINCT pat.kategori SEPARATOR ", ") as kategori,
                ca.status_ca
            ')
            ->from('corrective_action ca')
            ->join('pelaksanaan_audit pa', 'pa.id = ca.pelaksanaan_id', 'left')
            ->join('audit_program_schedule aps', 'aps.id = pa.schedule_id', 'left')
            ->join('procedures p', 'p.id = aps.process_id', 'left')
            ->join('audit_auditor_consultant aac', 'aac.id = aps.auditor_id', 'left')
            ->join('audit_program_schedule_auditee apsa', 'apsa.schedule_id = aps.id', 'left')
            ->join('departements ad', 'ad.id = apsa.department_id', 'left')
            ->join('pelaksanaan_audit_temuan pat', 'pat.audit_id = pa.id AND pat.status = "1"', 'left')
            ->where('ca.status_ca', 'approved')
            ->where('ca.deleted', '0')
            ->group_by('ca.id, aps.audit_date, p.name, aps.process_name_free, ad.department_name, aps.auditee_name_free, aac.name, ca.status_ca')
            ->order_by('aps.audit_date', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Get audit header data for a pelaksanaan audit
     *
     * @param int $pelaksanaan_id
     * @return object|null
     */
    public function getAuditHeader($pelaksanaan_id)
    {
        return $this->db->select('
                COALESCE(p.name, aps.process_name_free) as process_name,
                aps.audit_date,
                COALESCE(ad.name, aps.auditee_name_free) as department_name,
                aac.name as auditor_name
            ')
            ->from('pelaksanaan_audit pa')
            ->join('audit_program_schedule aps', 'aps.id = pa.schedule_id', 'left')
            ->join('procedures p', 'p.id = aps.process_id', 'left')
            ->join('audit_auditor_consultant aac', 'aac.id = aps.auditor_id', 'left')
            ->join('audit_program_schedule_auditee apsa', 'apsa.schedule_id = aps.id', 'left')
            ->join('departements ad', 'ad.id = apsa.department_id', 'left')
            ->where('pa.id', $pelaksanaan_id)
            ->get()
            ->row();
    }

    /**
     * Get active temuan (findings) for a pelaksanaan audit
     *
     * @param int $pelaksanaan_id
     * @return array
     */
    public function getTemuanByAudit($pelaksanaan_id)
    {
        return $this->db->select('pat.*')
            ->from('pelaksanaan_audit_temuan pat')
            ->where('pat.audit_id', $pelaksanaan_id)
            ->where('pat.status', '1')
            ->order_by('pat.id', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Get existing corrective action record for a pelaksanaan audit
     *
     * @param int $pelaksanaan_id
     * @return object|null
     */
    public function getCorrectionByPelaksanaan($pelaksanaan_id)
    {
        return $this->db->select('*')
            ->from('corrective_action')
            ->where('pelaksanaan_id', $pelaksanaan_id)
            ->where('deleted', '0')
            ->get()
            ->row();
    }

    /**
     * Get all corrective action detail rows for a CA
     *
     * @param string $ca_id
     * @return array
     */
    public function getCADetails($ca_id)
    {
        return $this->db->select('*')
            ->from('corrective_action_detail')
            ->where('ca_id', $ca_id)
            ->where('deleted', '0')
            ->get()
            ->result();
    }

    /**
     * Get all file records for a CA (joined via ca_detail_id)
     *
     * @param string $ca_id
     * @return array
     */
    public function getCAFiles($ca_id)
    {
        return $this->db->select('caf.*, cad.temuan_id, cad.ca_id')
            ->from('corrective_action_file caf')
            ->join('corrective_action_detail cad', 'cad.id = caf.ca_detail_id', 'inner')
            ->where('cad.ca_id', $ca_id)
            ->where('caf.deleted', '0')
            ->where('cad.deleted', '0')
            ->get()
            ->result();
    }

    /**
     * Get single file record by ID
     *
     * @param int $file_id
     * @return object|null
     */
    public function getFileById($file_id)
    {
        return $this->db->select('caf.*, cad.ca_id')
            ->from('corrective_action_file caf')
            ->join('corrective_action_detail cad', 'cad.id = caf.ca_detail_id', 'inner')
            ->where('caf.id', $file_id)
            ->where('caf.deleted', '0')
            ->get()
            ->row();
    }

    /**
     * Get rejection history for a CA, ordered by most recent first
     *
     * @param string $ca_id
     * @return array
     */
    public function getRejectionHistory($ca_id)
    {
        return $this->db->select('*')
            ->from('corrective_action_rejection')
            ->where('ca_id', $ca_id)
            ->order_by('rejected_at', 'DESC')
            ->get()
            ->result();
    }

    // =========================================================================
    // WRITE OPERATIONS
    // =========================================================================

    /**
     * Save corrective action (create or update as Draft)
     *
     * Creates a new CA record or updates an existing Draft CA.
     * Inserts/updates detail rows per temuan within a transaction.
     *
     * @param array $data POST data with pelaksanaan_id, company_id, detail[temuan_id][fakta|penyebab|correction|corrective_action]
     * @param int $userId Current user ID
     * @return array ['status' => 0|1, 'msg' => '...', 'ca_id' => '...']
     */
    public function saveCorrective($data, $userId)
    {
        $this->db->trans_begin();
        $now = date('Y-m-d H:i:s');
        $pelaksanaan_id = $data['pelaksanaan_id'];

        // Check if CA already exists for this pelaksanaan
        $existing = $this->getCorrectionByPelaksanaan($pelaksanaan_id);

        if ($existing) {
            // Validate status is Draft
            if ($existing->status_ca !== 'draft') {
                $this->db->trans_rollback();
                return ['status' => 0, 'msg' => 'Corrective Action tidak dapat diedit pada status saat ini.'];
            }
            $ca_id = $existing->id;
            $this->db->update('corrective_action', [
                'modified_at' => $now,
                'modified_by' => $userId,
            ], ['id' => $ca_id]);
        } else {
            // Generate new CA number and insert
            $ca_id = $this->generateCANumber();
            $this->db->insert('corrective_action', [
                'id'              => $ca_id,
                'pelaksanaan_id'  => $pelaksanaan_id,
                'company_id'      => isset($data['company_id']) ? $data['company_id'] : null,
                'status_ca'       => 'draft',
                'deleted'         => '0',
                'created_at'      => $now,
                'created_by'      => $userId,
            ]);
        }

        // Save details per temuan
        $details = isset($data['detail']) ? $data['detail'] : [];
        foreach ($details as $temuan_id => $fields) {
            $detailData = [
                'ca_id'              => $ca_id,
                'temuan_id'          => $temuan_id,
                'fakta'              => isset($fields['fakta']) ? substr($fields['fakta'], 0, 2000) : '',
                'kesimpulan_penyebab'=> isset($fields['penyebab']) ? substr($fields['penyebab'], 0, 2000) : '',
                'correction'         => isset($fields['correction']) ? substr($fields['correction'], 0, 2000) : '',
                'corrective_action'  => isset($fields['corrective_action']) ? substr($fields['corrective_action'], 0, 2000) : '',
                'modified_at'        => $now,
                'modified_by'        => $userId,
            ];

            // Check if detail already exists for this temuan
            $existingDetail = $this->db->get_where('corrective_action_detail', [
                'ca_id'     => $ca_id,
                'temuan_id' => $temuan_id,
                'deleted'   => '0'
            ])->row();

            if ($existingDetail) {
                $this->db->update('corrective_action_detail', $detailData, ['id' => $existingDetail->id]);
            } else {
                $detailData['created_at'] = $now;
                $detailData['created_by'] = $userId;
                $detailData['deleted'] = '0';
                $this->db->insert('corrective_action_detail', $detailData);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['status' => 0, 'msg' => 'Gagal menyimpan data. Silakan coba lagi.'];
        }

        $this->db->trans_commit();
        return ['status' => 1, 'msg' => 'Data berhasil disimpan.', 'ca_id' => $ca_id];
    }

    /**
     * Submit corrective action (Draft â†’ Waiting Approval)
     *
     * Validates current status is Draft, validates all required fields
     * are filled for every temuan, then updates status.
     *
     * @param string $ca_id The corrective action ID
     * @param int $userId Current user ID
     * @return array ['status' => 0|1, 'msg' => '...']
     */
    public function submitCorrective($ca_id, $userId)
    {
        // Validate current status
        $ca = $this->db->get_where('corrective_action', ['id' => $ca_id, 'deleted' => '0'])->row();
        if (!$ca || $ca->status_ca !== 'draft') {
            return ['status' => 0, 'msg' => 'Aksi tidak diizinkan pada status saat ini.'];
        }

        // Validate all details are filled
        $details = $this->getCADetails($ca_id);
        foreach ($details as $detail) {
            if (trim($detail->fakta) === '' || trim($detail->kesimpulan_penyebab) === '' ||
                trim($detail->correction) === '' || trim($detail->corrective_action) === '') {
                return ['status' => 0, 'msg' => 'Semua field wajib diisi untuk setiap temuan sebelum diajukan.'];
            }
        }

        // Validate detail count matches active temuan count
        $temuan = $this->getTemuanByAudit($ca->pelaksanaan_id);
        if (count($details) < count($temuan)) {
            return ['status' => 0, 'msg' => 'Lengkapi corrective action untuk semua temuan.'];
        }

        $this->db->trans_begin();
        $now = date('Y-m-d H:i:s');

        $this->db->update('corrective_action', [
            'status_ca'    => 'waiting_approval',
            'submitted_at' => $now,
            'submitted_by' => $userId,
            'modified_at'  => $now,
            'modified_by'  => $userId,
        ], ['id' => $ca_id]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['status' => 0, 'msg' => 'Gagal mengajukan. Silakan coba lagi.'];
        }

        $this->db->trans_commit();
        return ['status' => 1, 'msg' => 'Corrective Action berhasil diajukan untuk approval.'];
    }

    /**
     * Approve corrective action (Waiting Approval â†’ Approved)
     *
     * Validates current status is Waiting Approval, then updates status to Approved.
     *
     * @param string $ca_id The corrective action ID
     * @param int $userId Current user ID
     * @return array ['status' => 0|1, 'msg' => '...']
     */
    public function approveCorrective($ca_id, $userId)
    {
        $ca = $this->db->get_where('corrective_action', ['id' => $ca_id, 'deleted' => '0'])->row();
        if (!$ca || $ca->status_ca !== 'waiting_approval') {
            return ['status' => 0, 'msg' => 'Aksi tidak diizinkan pada status saat ini.'];
        }

        $this->db->trans_begin();
        $now = date('Y-m-d H:i:s');

        $this->db->update('corrective_action', [
            'status_ca'   => 'approved',
            'approved_at' => $now,
            'approved_by' => $userId,
            'modified_at' => $now,
            'modified_by' => $userId,
        ], ['id' => $ca_id]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['status' => 0, 'msg' => 'Gagal menyetujui. Silakan coba lagi.'];
        }

        $this->db->trans_commit();
        return ['status' => 1, 'msg' => 'Corrective Action berhasil disetujui.'];
    }

    /**
     * Reject corrective action (Waiting Approval â†’ Draft)
     *
     * Validates current status is Waiting Approval, updates status back to Draft,
     * and stores the rejection reason in the rejection history table.
     *
     * @param string $ca_id The corrective action ID
     * @param string $reason Rejection reason text
     * @param int $userId Current user ID
     * @return array ['status' => 0|1, 'msg' => '...']
     */
    public function rejectCorrective($ca_id, $reason, $userId)
    {
        $ca = $this->db->get_where('corrective_action', ['id' => $ca_id, 'deleted' => '0'])->row();
        if (!$ca || $ca->status_ca !== 'waiting_approval') {
            return ['status' => 0, 'msg' => 'Aksi tidak diizinkan pada status saat ini.'];
        }

        $this->db->trans_begin();
        $now = date('Y-m-d H:i:s');

        // Update status back to draft
        $this->db->update('corrective_action', [
            'status_ca'   => 'draft',
            'rejected_at' => $now,
            'rejected_by' => $userId,
            'modified_at' => $now,
            'modified_by' => $userId,
        ], ['id' => $ca_id]);

        // Store rejection history
        $this->db->insert('corrective_action_rejection', [
            'ca_id'       => $ca_id,
            'reason'      => $reason,
            'rejected_by' => $userId,
            'rejected_at' => $now,
        ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['status' => 0, 'msg' => 'Gagal menolak. Silakan coba lagi.'];
        }

        $this->db->trans_commit();
        return ['status' => 1, 'msg' => 'Corrective Action ditolak dan dikembalikan untuk revisi.'];
    }

    // =========================================================================
    // File Operations
    // =========================================================================

    /**
     * Save a file record to the corrective_action_file table
     *
     * @param int $ca_detail_id The corrective action detail ID
     * @param array $fileData Array with keys: file_name, file_name_original, file_type, file_size, user_id
     * @return int|false Insert ID on success, false on failure
     */
    public function saveFile($ca_detail_id, $fileData)
    {
        $insertData = [
            'ca_detail_id'      => $ca_detail_id,
            'file_name'         => $fileData['file_name'],
            'file_name_original'=> $fileData['file_name_original'],
            'file_type'         => $fileData['file_type'],
            'file_size'         => $fileData['file_size'],
            'deleted'           => '0',
            'created_at'        => date('Y-m-d H:i:s'),
            'created_by'        => $fileData['user_id'],
        ];

        $result = $this->db->insert('corrective_action_file', $insertData);

        if ($result) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Soft-delete a file record by setting deleted='1'
     *
     * @param int $file_id The file record ID
     * @return bool True on success, false on failure
     */
    public function deleteFile($file_id)
    {
        $this->db->where('id', $file_id);
        $this->db->where('deleted', '0');

        return $this->db->update('corrective_action_file', ['deleted' => '1']);
    }
}
