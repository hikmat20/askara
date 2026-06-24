<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Master List Controller
 * Displays Document Master Lists for SOP, IK, and Form
 */
class Master_list extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template->set([
            'title' => 'Master List',
            'icon'  => 'fa fa-list-alt'
        ]);
    }

    /**
     * Index - display master list with filter tabs (SOP / IK / Form)
     */
    public function index()
    {
        $filter = $this->input->get('filter') ?: '';
        $status = $this->input->get('status') ?: 'all';

        // Get group_procedure for department mapping
        $groups = $this->db->get_where('group_procedure', ['status' => 'ACT'])->result();
        $grp_map = [];
        foreach ($groups as $g) { $grp_map[$g->id] = $g->name; }

        $data = [];

        if ($filter == 'sop') {
            $data = $this->_getSOP($status);
        } elseif ($filter == 'ik') {
            $data = $this->_getIK($status);
        } elseif ($filter == 'form') {
            $data = $this->_getForm($status);
        }

        // Count per status for badges
        $count_sop = $this->_countByStatus('procedures');
        $count_ik = $this->_countByStatus('dir_guides');
        $count_form = $this->_countByStatus('dir_forms');

        $this->template->set([
            'filter'     => $filter,
            'status'     => $status,
            'data'       => $data,
            'grp_map'    => $grp_map,
            'count_sop'  => $count_sop,
            'count_ik'   => $count_ik,
            'count_form' => $count_form,
        ]);
        $this->template->render('index');
    }

    /**
     * Get SOP (procedures) data
     */
    private function _getSOP($status)
    {
        $this->db->select('procedures.*, group_procedure.name as department')
            ->from('procedures')
            ->join('group_procedure', 'group_procedure.id = procedures.group_procedure', 'left')
            ->where('procedures.company_id', $this->company)
            ->where_not_in('procedures.status', ['DEL', 'HLD'])
            ->where('procedures.deleted_at IS NULL');

        if ($status == 'publish') {
            $this->db->where('procedures.status', 'PUB');
        } elseif ($status == 'draft') {
            $this->db->where('procedures.status', 'DFT');
        } elseif ($status == 'waiting') {
            $this->db->where_in('procedures.status', ['REV', 'APV']);
        }

        $results = $this->db->order_by('procedures.revision_date', 'DESC')->order_by('procedures.created_at', 'DESC')->get()->result();

        // Attach cross reference pasal ISO for each procedure
        $cross_refs = $this->db->where('company_id', $this->company)->where('status', '1')->get('view_cross_reference_details')->result();
        $ref_map = []; // procedure_id => [iso names]
        foreach ($cross_refs as $cr) {
            $proc_ids = explode(',', $cr->procedure_id);
            foreach ($proc_ids as $pid) {
                $pid = trim($pid);
                if ($pid) {
                    if (!isset($ref_map[$pid])) $ref_map[$pid] = [];
                    $label = $cr->name . ' ' . $cr->year;
                    if (!in_array($label, $ref_map[$pid])) {
                        $ref_map[$pid][] = $label;
                    }
                }
            }
        }

        foreach ($results as &$row) {
            if (isset($ref_map[$row->id])) {
                $row->cross_reference = '- ' . implode('<br>- ', $ref_map[$row->id]);
            } else {
                $row->cross_reference = '-';
            }
        }

        return $results;
    }

    /**
     * Get IK (dir_guides) data
     */
    private function _getIK($status)
    {
        $this->db->select('view_work_instructions.*, procedures.nomor as procedure_nomor')
            ->from('view_work_instructions')
            ->join('procedures', 'procedures.id = view_work_instructions.procedure_id', 'left')
            ->where('view_work_instructions.company_id', $this->company)
            ->where('view_work_instructions.status !=', 'DEL');

        if ($status == 'publish') {
            $this->db->where('view_work_instructions.status', 'PUB');
        } elseif ($status == 'draft') {
            $this->db->where_in('view_work_instructions.status', ['DFT', 'OPN', 'COR', 'RVI']);
        } elseif ($status == 'waiting') {
            $this->db->where_in('view_work_instructions.status', ['REV', 'APV']);
        }

        return $this->db->order_by('view_work_instructions.effective_date', 'DESC')->order_by('view_work_instructions.created_at', 'DESC')->get()->result();
    }

    /**
     * Get Form (dir_forms) data
     */
    private function _getForm($status)
    {
        $this->db->select('view_forms.*, procedures.nomor as procedure_nomor')
            ->from('view_forms')
            ->join('procedures', 'procedures.id = view_forms.procedure_id', 'left')
            ->where('view_forms.company_id', $this->company)
            ->where('view_forms.status !=', 'DEL');

        if ($status == 'publish') {
            $this->db->where('view_forms.status', 'PUB');
        } elseif ($status == 'draft') {
            $this->db->where_in('view_forms.status', ['DFT', 'OPN', 'COR', 'RVI']);
        } elseif ($status == 'waiting') {
            $this->db->where_in('view_forms.status', ['REV', 'APV']);
        }

        return $this->db->order_by('view_forms.effective_date', 'DESC')->order_by('view_forms.created_at', 'DESC')->get()->result();
    }

    /**
     * Count documents by status for a table
     */
    private function _countByStatus($table)
    {
        if ($table == 'procedures') {
            $all = $this->db->where('company_id', $this->company)->where_not_in('status', ['DEL', 'HLD'])->where('deleted_at IS NULL')->count_all_results($table);

            $this->db->reset_query();
            $pub = $this->db->where('company_id', $this->company)->where('deleted_at IS NULL')->where('status', 'PUB')->count_all_results($table);

            $this->db->reset_query();
            $draft = $this->db->where('company_id', $this->company)->where('deleted_at IS NULL')->where_in('status', ['DFT', 'RVI'])->count_all_results($table);

            $this->db->reset_query();
            $waiting = $this->db->where('company_id', $this->company)->where('deleted_at IS NULL')->where_in('status', ['REV', 'APV'])->count_all_results($table);
        } else {
            // IK & Form: count based on work_instructions or forms status
            $target_table = ($table == 'dir_guides') ? 'work_instructions' : 'forms';

            $all = $this->db->where('company_id', $this->company)
                ->where_not_in('status', ['DEL', 'HLD'])
                ->count_all_results($target_table);

            $this->db->reset_query();
            $pub = $this->db->where('company_id', $this->company)
                ->where('status', 'PUB')
                ->count_all_results($target_table);

            $this->db->reset_query();
            $draft = $this->db->where('company_id', $this->company)
                ->where_in('status', ['DFT', 'OPN', 'COR', 'RVI'])
                ->count_all_results($target_table);

            $this->db->reset_query();
            $waiting = $this->db->where('company_id', $this->company)
                ->where_in('status', ['REV', 'APV'])
                ->count_all_results($target_table);
        }

        return ['all' => $all, 'publish' => $pub, 'draft' => $draft, 'waiting' => $waiting];
    }

    /**
     * Export Excel
     */
    public function export_excel()
    {
        $filter = $this->input->get('filter') ?: 'sop';
        $data = [];

        if ($filter == 'sop') { $data = $this->_getSOP('all'); }
        elseif ($filter == 'ik') { $data = $this->_getIK('all'); }
        elseif ($filter == 'form') { $data = $this->_getForm('all'); }

        require_once(APPPATH . 'libraries/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        $titles = [
            'sop'  => 'DAFTAR INDUK SOP',
            'ik'   => 'DAFTAR INDUK IK',
            'form' => 'DAFTAR INDUK FORM',
        ];
        $sheet->setTitle(isset($titles[$filter]) ? $titles[$filter] : 'Master List');

        $sts_labels = ['DFT'=>'Draft','REV'=>'Review','APV'=>'Approval','PUB'=>'Published','RVI'=>'Revision','COR'=>'Correction','HLD'=>'Hold','OPN'=>'Draft'];

        if ($filter == 'sop') {
            $headers = ['No', 'Department', 'Document Number', 'Document Name', 'Effective Date Rev. 0', 'Latest Revision', 'Effective Date Latest Rev.', 'Status', 'Cross Reference to Pasal ISO'];
            $col = 'A';
            foreach ($headers as $h) { $sheet->setCellValue($col . '1', $h); $col++; }

            $row = 2;
            foreach ($data as $k => $v) {
                $cross_ref = isset($v->cross_reference) ? str_replace(['<br>', '- '], ["\n", '- '], strip_tags($v->cross_reference, '')) : '-';
                $sheet->setCellValue('A' . $row, $k + 1);
                $sheet->setCellValue('B' . $row, isset($v->department) ? $v->department : '-');
                $sheet->setCellValue('C' . $row, $v->nomor);
                $sheet->setCellValue('D' . $row, strip_tags($v->name));
                $sheet->setCellValue('E' . $row, $v->created_at ? date('d-m-Y', strtotime($v->created_at)) : '-');
                $sheet->setCellValue('F' . $row, $v->revision ? 'Rev. ' . $v->revision : '-');
                $sheet->setCellValue('G' . $row, $v->revision_date ? date('d-m-Y', strtotime($v->revision_date)) : '-');
                $sheet->setCellValue('H' . $row, isset($sts_labels[$v->status]) ? $sts_labels[$v->status] : $v->status);
                $sheet->setCellValue('I' . $row, $cross_ref);
                $row++;
            }
        } else {
            $headers = ['No', 'Department', 'Document Number', 'Prosedur Induk', 'Document Name', 'Effective Date Rev. 0', 'Latest Revision', 'Effective Date Latest Rev.', 'Status'];
            $col = 'A';
            foreach ($headers as $h) { $sheet->setCellValue($col . '1', $h); $col++; }

            $row = 2;
            foreach ($data as $k => $v) {
                $sheet->setCellValue('A' . $row, $k + 1);
                $sheet->setCellValue('B' . $row, isset($v->departement_name) ? $v->departement_name : '-');
                $sheet->setCellValue('C' . $row, isset($v->procedure_nomor) ? $v->procedure_nomor : '-');
                $sheet->setCellValue('D' . $row, isset($v->procedure_name) ? strip_tags($v->procedure_name) : '-');
                $sheet->setCellValue('E' . $row, strip_tags($v->name));
                $sheet->setCellValue('F' . $row, isset($v->issue_date) && $v->issue_date ? date('d-m-Y', strtotime($v->issue_date)) : '-');
                $sheet->setCellValue('G' . $row, isset($v->revision_number) && $v->revision_number !== null ? 'Rev. ' . $v->revision_number : '-');
                $sheet->setCellValue('H' . $row, isset($v->effective_date) && $v->effective_date ? date('d-m-Y', strtotime($v->effective_date)) : '-');
                $sts = isset($v->status) ? $v->status : '';
                $sheet->setCellValue('I' . $row, isset($sts_labels[$sts]) ? $sts_labels[$sts] : $sts);
                $row++;
            }
        }

        $filename = 'Master_List_' . strtoupper($filter) . '_' . date('Ymd') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Print PDF
     */
    public function print_pdf()
    {
        $filter = $this->input->get('filter') ?: 'sop';
        $data = [];

        if ($filter == 'sop') { $data = $this->_getSOP('all'); }
        elseif ($filter == 'ik') { $data = $this->_getIK('all'); }
        elseif ($filter == 'form') { $data = $this->_getForm('all'); }

        $html_data = [
            'filter' => $filter,
            'data'   => $data,
        ];

        $html = $this->load->view('master_list/pdf', $html_data, true);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'tempDir' => APPPATH . 'cache/mpdf'
        ]);
        $mpdf->SetTitle('Master List ' . strtoupper($filter));
        $mpdf->WriteHTML($html);
        if (ob_get_contents()) ob_clean();
        $mpdf->Output('Master_List_' . strtoupper($filter) . '_' . date('Ymd') . '.pdf', 'I');
    }
}
