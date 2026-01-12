<?php


class Print_to_pdf extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('app');
        $this->load->library('PdfService');
		$this->load->model('companies/Company_model', 'Comp');
        date_default_timezone_set("Asia/Bangkok");
    }

    public function printfile($id = null)
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
     
        $mpdf = $this->pdfservice->load();

        // $mpdf = new Mpdf([
        // 	'margin_top' => 50,
        // 	'margin_bottom' => 15,
        // ]);
        $mpdf->showImageErrors = true;
        $mpdf->curlAllowUnsafeSslRequests = true;
        $procedure           = $this->db->get_where('view_procedures', ['id' => $id])->row();
        $flowDetail          = $this->db->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
        $getForms            = $this->db->get_where('dir_forms', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
        $getGuides           = $this->db->get_where('dir_guides', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
        $users               = $this->db->get_where('view_users', ['company_id' => $procedure->company_id])->result();
        $jabatan             = $this->db->get('positions')->result();
        $ArrUsr              = $ArrJab = $ArrDept = $ArrForms = $ArrGuides = [];
        $procedure_bilingual = $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
        $company             = $this->Comp->find($procedure->company_id);
        $depts               = $this->db->get_where('departements', ['company_id' => $procedure->company_id, 'status' => '1'])->result();
        $revision_logs = $this->db->get_where('procedure_revision_logs', ['company_id' => $procedure->company_id, 'procedure_id' => $id, 'status' => '1'])->result();

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

        $this->db->select('*')->from('view_cross_reference_details');
        $this->db->where("find_in_set($id, procedure_id)");
        $this->db->where("company_id", $procedure->company_id);
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

        // $allProcedure 		= $this->db->get_where('view_procedures', ['company_id' => $procedure->company_id, 'status !=' => 'DEL'])->result();

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
            // 'allProcedure'        => $allProcedure,
            'procedure_bilingual' => $procedure_bilingual,
            'revision_logs'       => $revision_logs,
        ];

        $header = $this->getHeader($getData);
        $mpdf->SetHTMLHeader($header);
        $mpdf->AddPage('P', '',  '', '', '', 7, 7, 50, 7, 7, '', '', '');

        // $this->template->set($getData);
        $page = $this->load->view('printout', $getData, true);

        // 🔥 WAJIB SETELAH HTML LENGKAP
        $page = convert_ol_to_table($page);
        // 8️⃣ PASTIKAN BUFFER BERSIH

        $mpdf->WriteHTML($page);
        $mpdf->Output('procedure.pdf', 'I');
        exit; // ⬅️ WAJIB & BENAR
    }

    public function getHeader($allData)
    {
        return '<div>
		<table class="table-data" cellpadding="2" cellspacing="0" style="font-size: 10pt;">
				<tr>
				<td rowspan="5" width="100" class="text-right" style="vertical-align: middle;border-right:0px">
					<img width="80" src="' . base_url($allData['company']->path_logo . $allData['company']->id_perusahaan . '/' . $allData['company']->logo) . '" alt="">
				</td>
				<td rowspan="5" width="250"  class="text-center" style="vertical-align: middle;border-left:0px">
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

    public function printOutHtml($id = null)
    {
       
        $procedure           = $this->db->get_where('view_procedures', ['id' => $id])->row();
        $flowDetail          = $this->db->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
        $getForms            = $this->db->get_where('dir_forms', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
        $getGuides           = $this->db->get_where('dir_guides', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
        $users               = $this->db->get_where('view_users', ['company_id' => $procedure->company_id])->result();
        $jabatan             = $this->db->get('positions')->result();
        $ArrUsr              = $ArrJab = $ArrDept = $ArrForms = $ArrGuides = [];
        $procedure_bilingual = $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
        $company             = $this->Comp->find($procedure->company_id);
        $depts               = $this->db->get_where('departements', ['company_id' => $procedure->company_id, 'status' => '1'])->result();
        $revision_logs = $this->db->get_where('procedure_revision_logs', ['company_id' => $procedure->company_id, 'procedure_id' => $id, 'status' => '1'])->result();

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

        $this->db->select('*')->from('view_cross_reference_details');
        $this->db->where("find_in_set($id, procedure_id)");
        $this->db->where("company_id", $procedure->company_id);
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
            // 'allProcedure'        => $allProcedure,
            'procedure_bilingual' => $procedure_bilingual,
            'revision_logs'       => $revision_logs,
        ];

        // $this->template->set($getData);
       $this->load->view('printout', $getData, true);
    }
    
}
