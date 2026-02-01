<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Monitoring_model extends BF_Model
{

    /**
     * @var string  User Table Name
     */
    protected $table_name = 'monitoring';
    protected $key        = 'kodebarang';

    /**
     * @var string Field name to use for the created time column in the DB table
     * if $set_created is enabled.
     */
    protected $created_field = 'created_on';

    /**
     * @var string Field name to use for the modified time column in the DB
     * table if $set_modified is enabled.
     */
    protected $modified_field = 'modified_on';

    /**
     * @var bool Set the created time automatically on a new record (if true)
     */
    protected $set_created = true;

    /**
     * @var bool Set the modified time automatically on editing a record (if true)
     */
    protected $set_modified = true;
    /**
     * @var string The type of date/time field used for $created_field and $modified_field.
     * Valid values are 'int', 'datetime', 'date'.
     */
    /**
     * @var bool Enable/Disable soft deletes.
     * If false, the delete() method will perform a delete of that row.
     * If true, the value in $deleted_field will be set to 1.
     */
    protected $soft_deletes = true;

    protected $date_format = 'datetime';

    /**
     * @var bool If true, will log user id in $created_by_field, $modified_by_field,
     * and $deleted_by_field.
     */
    protected $log_user = true;

    /**
     * Function construct used to load some library, do some actions, etc.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('signature/signature');
        date_default_timezone_set('Asia/Jakarta');
    }

    private function _update_history($data, $note = null)
    {
        $thisData = $this->db->get_where('procedures', ['id' => $data['id']])->row();

        $logData['directory_id'] = $data['id'];
        $logData['new_status']   = $data['status'];
        $logData['old_status']   = $thisData->status;
        $logData['doc_type']     = 'Procedure';
        $logData['note']         = ($note) ?: '~';
        $logData['updated_by']   = $this->auth->user_id();
        $logData['updated_at']   = date('Y-m-d H:i:s');

        $this->db->insert('directory_log', $logData);

        if ($this->db->affected_rows() === 0) {
            throw new Exception('Failed update Logs activity.');
        }
    }

    private function _logsProcedure($data, $note = null)
    {
        $procedure = $this->db->get_where('procedures', ['id' => $data['id']])->row();

        if ($procedure->revision > 0):
            $logRevision = [
                'company_id'      => 1,
                'procedure_id'    => $data['id'],
                'revision_number' => intVal($procedure->revision) + 1,
                'revision_date'   => date('Y-m-d'),
                'description'     => $note,
            ];
        else:
            $logRevision = [
                'company_id'      => 1,
                'procedure_id'    => $data['id'],
                'revision_number' => 0,
                'revision_date'   => date('Y-m-d'),
                'description'     => 'Rilis Baru',
            ];
        endif;
        $this->db->insert('procedure_revision_logs', $logRevision);
    }

    public function review($data = null)
    {
        try {
            if ($data) {
                $this->db->trans_begin();

                $this->db->update(
                    'procedures',
                    [
                        'status'         => $data['status'],
                        'modified_by'     => $this->auth->user_id(),
                        'modified_at'     => date('Y-m-d H:i:s'),
                        'reviewed_by'     => $this->auth->user_id(),
                        'reviewed_at'     => date('Y-m-d H:i:s'),
                    ],
                    ['id' => $data['id']]
                );

                $this->_signature($data, 'review');
                $this->_update_history($data);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    throw new Exception('Failed process review document. Please try again later.');
                } else {
                    $this->db->trans_commit();
                    $Return = [
                        'status' => 1,
                        'msg'     => 'Success process review document...'
                    ];
                }
            } else {
                throw new Exception('Data not valid. Please try again later.');
            }
        } catch (\Throwable $th) {
            $this->db->trans_rollback();
            $Return = [
                'status' => 0,
                'msg'     => $th->getMessage()
            ];
        }

        return $Return;
    }

    public function approval($data = null)
    {
        $data = $this->input->post();
        try {
            if ($data) {
                $this->db->trans_begin();
                $this->_signature($data, 'approve');
                $this->_update_history($data);
                $this->_logsProcedure($data);
                $this->generatePdfFile($data['id']);
                $this->db->update(
                    'procedures',
                    [
                        'status'      => $data['status'],
                        'modified_by' => $this->auth->user_id(),
                        'modified_at' => date('Y-m-d H:i:s'),
                        'approved_by' => $this->auth->user_id(),
                        'approved_at' => date('Y-m-d H:i:s'),
                        'published_at' => date('Y-m-d H:i:s'),
                    ],
                    ['id' => $data['id']]
                );

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    throw new Exception("Error Processing Request", 1);
                } else {
                    $this->db->trans_commit();
                    $Return = [
                        'status' => 1,
                        'msg'     => 'Success Approve document.'
                    ];
                }
            } else {
                throw new ErrorException('Data not found.');
            }
        } catch (\Throwable $th) {
            $this->db->trans_rollback();
            $Return = [
                'status' => 0,
                'msg'     => $th->getMessage() . ' Please try again later!'
            ];
        }
        return $Return;
    }

    public function saveRevision($data = null)
    {
        try {
            if ($data) {
                $this->db->trans_begin();
                $data['status'] = 'RVI';
                $procedur = $this->db->get_where('procedures', ['id' => $data['id']])->row();
                $this->db->update(
                    'procedures',
                    [
                        'status'          => $data['status'],
                        'revision'        => $procedur->revision + 1,
                        'modified_by'     => $this->auth->user_id(),
                        'modified_at'     => date('Y-m-d H:i:s'),
                        'revision_req_by' => $this->auth->user_id(),
                        'revision_req_at' => date('Y-m-d H:i:s'),
                    ],
                    ['id' => $data['id']]
                );

                $this->_update_history($data, $data['note']);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    throw new ErrorException('Failed to submit revision document.');
                } else {
                    $this->db->trans_commit();
                    $Return = [
                        'status' => 1,
                        'msg'    => 'Success submit revision document file...'
                    ];
                }
            } else {
                throw new ErrorException('Data not found.');
            }
        } catch (\Throwable $th) {
            $this->db->trans_rollback();
            $Return = [
                'status' => 0,
                'msg'    => $th->getMessage()
            ];
        }
        return $Return;
    }

    public function deletion($data = null)
    {
        if ($data) {
            $this->db->update(
                'procedures',
                [
                    'status'          => $data['status'],
                    'deletion_status' => 'OPN',
                    'modified_by'     => $this->auth->user_id(),
                    'modified_at'     => date('Y-m-d H:i:s'),
                ],
                ['id' => $data['id']]
            );

            $thisData = $this->db->get_where('procedures', ['id' => $data['id']])->row();
            $data['directory_id']     = $data['id'];
            $data['new_status']       = $data['status'];
            $data['old_status']       = $thisData->status;
            $data['doc_type']         = 'Procedure';
            unset($data['id']);
            unset($data['status']);
            $this->_update_history($data);
        } else {
            return false;
        }
    }

    public function rev_deletion($data = null)
    {
        if ($data) {
            $this->db->update(
                'procedures',
                [
                    'deletion_status'     => $data['deletion_status'],
                    'modified_by'     => $this->auth->user_id(),
                    'modified_at'     => date('Y-m-d H:i:s'),
                ],
                ['id' => $data['id']]
            );

            $thisData = $this->db->get_where('procedures', ['id' => $data['id']])->row();
            $data['directory_id']     = $data['id'];
            $data['new_status']       = $data['status'];
            $data['old_status']       = $thisData->status;
            $data['doc_type']         = 'Procedure';
            unset($data['id']);
            unset($data['status']);
            unset($data['deletion_status']);
            $this->_update_history($data);
        } else {
            return false;
        }
    }

    public function _signature($data, $sign_type)
    {
        $position = $this->db
            ->where('assign_user', $this->auth->user_id())
            ->get('positions')
            ->row();

        if (!$position) {
            throw new Exception('User position not found.');
        }

        $token = generate_qr_token();

        $signData = [
            'document_id'   => $data['id'],
            'document_type' => 'procedure',
            'position_id'   => $position->id,
            'token'         => $token,
            'sign_type'     => $sign_type,
            'sign_by'       => $this->auth->user_id(),
            'sign_at'       => date('Y-m-d H:i:s'),
            'created_by'    => $this->auth->user_id(),
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $this->db->insert('signature_documents', $signData);

        if ($this->db->affected_rows() === 0) {
            throw new Exception('Failed insert signature document.');
        }

        $this->load->library('ciqrcode');

        $dir = FCPATH . 'directory/SIGNATURE/';
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new Exception('Failed create signature directory.');
            }
        }

        $qrPath = $dir . $token . '.png';

        $params = [
            'data'     => site_url('signature/verify?token=' . $token),
            'level'    => 'H',
            'size'     => 10,
            'savename' => $qrPath
        ];

        if (!$this->ciqrcode->generate($params)) {
            throw new Exception('Failed generate QR Code.');
        }

        if (!file_exists($qrPath)) {
            throw new Exception('QR file not created.');
        }

        $this->db->where('token', $token)->update(
            'signature_documents',
            [
                'qr_path' => 'directory/SIGNATURE/' . $token . '.png'
            ]
        );

        if ($this->db->affected_rows() === 0) {
            throw new Exception('Failed update QR path.');
        }
    }

    /* Generate File PDF */

    private function generatePdfFile($procedure_id)
    {
        $document = $this->db->get_where('view_procedures', ['id' => $procedure_id])->row();
        if ($document->status === 'PUB') {
            return; // sudah final
        }

        $hash = hash_hmac(
            'sha256',
            $document->id,
            config_item('encryption_key')
        );

        $filePath = FCPATH . 'directory/PROCEDURES/' . date('Y') . '/' . date('m') . '/';
        if (!is_dir($filePath)) mkdir($filePath, 0755, true);
        $filename = $document->nomor . '-FINAL.pdf';

        $this->load->helper('app');
        $this->load->library('PdfService');
        $this->load->model('companies/Company_model', 'Comp');

        $mpdf = $this->pdfservice->load();
        $mpdf->SetProtection(['print']); // ❗ no edit
        $mpdf->SetWatermarkText('FINAL DOCUMENT');
        // $mpdf->showWatermarkText = true;
        $mpdf->showImageErrors = true;
        $mpdf->curlAllowUnsafeSslRequests = true;
        $mpdf->SetHtmlFooter('<div class="text-center" style="color:#595959"><i>- Hardcopy Uncontrol -</i></div>');

        $company_id = $this->session->company->id_perusahaan;

        // watermark
        $procedure           = $this->db->get_where('view_procedures', ['id' => $procedure_id])->row();
        $flowDetail          = $this->db->get_where('procedure_details', ['procedure_id' => $procedure_id, 'status' => '1'])->result();
        $getForms            = $this->db->get_where('dir_forms', ['procedure_id' => $procedure_id, 'status !=' => 'DEL'])->result();
        $getGuides           = $this->db->get_where('dir_guides', ['procedure_id' => $procedure_id, 'status !=' => 'DEL'])->result();
        $users               = $this->db->get_where('view_users', ['company_id' => $company_id])->result();
        $jabatan             = $this->db->get('positions')->result();
        $ArrUsr              = $ArrJab = $ArrDept = $ArrForms = $ArrGuides = [];
        $procedure_bilingual = $this->db->get_where('procedure_bilingual', ['procedure_id' => $procedure_id])->row();
        $company             = $this->Comp->find($company_id);
        $depts               = $this->db->get_where('departements', ['company_id' => $company_id, 'status' => '1'])->result();
        $revision_logs       = $this->db->get_where('procedure_revision_logs', ['company_id' => $company_id, 'procedure_id' => $procedure_id, 'status' => '1'])->result();
        $revision_markers    = $this->db->get_where('procedure_revision_markers', ['company_id' => $company_id, 'procedure_id' => $procedure_id, 'is_active' => '1', 'marker_counter' => $procedure->revision])->result();

        foreach ($depts as $dept) {
            $ArrDept[$dept->id] = $dept;
        }

        foreach ($getForms as $frm) {
            $ArrForms[$frm->id] = $frm;
        }
        foreach ($users as $usr) {
            $ArrUsr[$usr->id_user] = $usr;
        }

        foreach ($jabatan as $jab) {
            $ArrJab[$jab->id] = $jab;
        }

        foreach ($getGuides as $gui) {
            $ArrGuides[$gui->id] = $gui;
        }

        $markers = [];
        if ($revision_markers) {
            foreach ($revision_markers as $rm) {
                $markers[$rm->marker_position] = $rm;
            }
        }

        $this->db->select('*')->from('view_cross_reference_details');
        $this->db->where("find_in_set($procedure_id, procedure_id)");
        $this->db->where("company_id", $company_id);
        $Data = $this->db->get()->result();

        $ArrData = [];
        foreach ($Data as $dt) {
            $ArrData['id'][$dt->requirement_id] = $dt->requirement_id;
            $ArrData['standards'][$dt->requirement_id][] = $dt;
        }
        $ArrStd = [];
        foreach ($Data as $dtstd) {
            $ArrStd[$dtstd->requirement_id] = $dtstd;
        }

        $signatures       = $this->db->get_where('signature_documents', ['document_id' => $procedure_id, 'document_type' => 'procedure'])->result();

        $ArrSign = [];
        foreach ($signatures as $sign) {
            $ArrSign[$sign->sign_type] = $sign->qr_path;
        }

        $getData = [
            'procedure'           => $procedure,
            'company'             => $company,
            'detail'              => $flowDetail,
            'ArrUsr'              => $ArrUsr,
            'ArrJab'              => $ArrJab,
            'ArrForms'            => $ArrForms,
            'ArrGuides'           => $ArrGuides,
            'Data'                => $Data,
            'ArrDept'             => $ArrDept,
            'ArrData'             => $ArrData,
            'ArrStd'              => $ArrStd,
            'procedure_bilingual' => $procedure_bilingual,
            'revision_logs'       => $revision_logs,
            'markers'             => $markers,
            'hash'                => $hash,
            'ArrSign'             => $ArrSign
        ];


        $mpdf->SetWatermarkImage(FCPATH . 'assets/logo/1/' . $company->logo, 0.2, [100, 80], [60, 100]);
        $mpdf->showWatermarkImage = true;

        $header = $this->getHeader($getData);
        $mpdf->SetHTMLHeader($header);
        $mpdf->AddPage('P', '',  '', '', '', 7, 7, 50, 7, 7, '', '', '');

        $page = $this->load->view('printout', $getData, true);

        // 🔥 WAJIB SETELAH HTML LENGKAP
        $page = convert_ol_to_table($page);

        $mpdf->WriteHTML($page);
        $mpdf->Output($filePath . $filename, 'F');
    }

    public function getHeader($allData)
    {
        return '<div>
		<table class="table-data" cellpadding="1" cellspacing="0">
				<tr>
				<td rowspan="5" width="100" class="text-right" style="vertical-align: middle;border-right:0px">
					<img width="80" src="' . base_url($allData['company']->path_logo . $allData['company']->id_perusahaan . '/' . $allData['company']->logo) . '" alt="">
				</td>
				<td rowspan="5" width="280"  class="text-center" style="vertical-align: middle;border-left:0px">
					<h2>' . $allData['company']->nm_perusahaan . '</h2>
				</td>
				<td width="150">Dept</td>
				<td width="">' . (isset($allData['procedure']->departement_id) ? $allData['procedure']->departement_name : '') . '</td>
				</tr>
				<tr>
				<td>No. Dok</td>
				<td>' . (($allData['procedure']->nomor) ?: '~') . '</td>
				</tr>
				<tr>
				<td>Revisi</td>
				<td>' . (($allData['procedure']->revision) ?: '~') . '</td>
				</tr>
				<tr>
				<td>Tgl. Terbit</td>
				<td>' . (($allData['procedure']->published_at) ?: '~') . '</td>
				</tr>
				<tr>
				<td>Halaman</td>
				<td>{PAGENO} dari {nbpg}</td>
				</tr>
				<tr>
				<td colspan="4" class="text-center" style="vertical-align: middle;">
					<h2>' . strtoupper($allData['procedure']->name) . '</h2>
					<h3 style="color: #0088ffff;">(' . (isset($allData['procedure_bilingual']->name) ? strtoupper($allData['procedure_bilingual']->name) : '') . ')</h3>
				</td>
				</tr>
			</table></div>';
    }


    /* Signature */
}
