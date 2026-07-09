<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monitoring extends Admin_Controller
{
	public $sts;
	/*
 * @author Yunaz
 * @copyright Copyright (c) 2016, Yunaz
 * 
 * This is controller for Penerimaan
 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('monitoring/monitoring_model', 'Monitoring');
		$this->load->model('forms/form_model', 'FormModel');
		$this->load->model('work_instructions/Work_instruction_model', 'WiModel');
		$this->template->set_theme('dashboard');
		$this->template->page_icon('fa fa-dashboard');

		$this->template->set('allow_download_procedure', $this->_check_download_permission('procedures'));
		$this->template->set('allow_download_form', $this->_check_download_permission('forms'));
		$this->template->set('allow_download_wi', $this->_check_download_permission('work_instructions'));

		$this->sts = [
			'DFT' => '<span class="label label-light-secondary label-pill label-inline mr-2 text-dark-50">Draft</span>',
			'OPN' => '<span class="label label-light-primary label-pill label-inline mr-2">New</span>',
			'REV' => '<span class="label label-light-warning label-pill label-inline mr-2">Waiting Review</span>',
			'COR' => '<span class="label label-light-danger label-pill label-inline mr-2">Need Correction</span>',
			'APV' => '<span class="label label-light-info label-pill label-inline mr-2">Waiting Approval</span>',
			'PUB' => '<span class="label label-light-success label-pill label-inline mr-2">Published</span>',
			'RVI' => '<span class="label label-light-danger label-pill label-inline mr-2">Revision</span>',
			'HLD' => '<span class="label label-light-secondary text-secondary label-pill label-inline mr-2">Hold</span>',
			'DEL' => '<span class="label label-light-danger text-danger label-pill label-inline mr-2">Request Deletion</span>',
			'REJ' => '<span class="label label-light-success text-success label-pill label-inline mr-2">Rejected Deletion</span>',
		];
	}

	public function index()
	{
		/* REVIEW */
		// $dtProcedureRev = $this->db->get_where('procedures', ['company_id' => $this->company, 'status' => 'REV'])->num_rows();
		$dtGuidesRev 	= 0; // $this->db->get_where('dir_guides', ['company_id' => $this->company, 'status' => 'REV'])->num_rows();

		/* APPROVAL */
		// $dtProcedureApv = $this->db->get_where('procedures', ['company_id' => $this->company, 'status' => 'APV'])->num_rows();
		$dtGuidesApv 	= 0; // $this->db->get_where('dir_guides', ['company_id' => $this->company, 'status' => 'APV'])->num_rows();

		/* CORRECTION */
		// $dtProcedureCor = $this->db->get_where('procedures', ['company_id' => $this->company, 'status' => 'COR'])->num_rows();
		$dtGuidesCor 	= 0; // $this->db->get_where('dir_guides', ['company_id' => $this->company, 'status' => 'COR'])->num_rows();

		/* REVISION */
		// $dtProcedureRvi = $this->db->get_where('procedures', ['company_id' => $this->company, 'status' => 'RVI'])->num_rows();
		$dtGuidesRvi 	= 0; // $this->db->get_where('dir_guides', ['company_id' => $this->company, 'status' => 'RVI'])->num_rows();

		/* PUBLISH */
		// $dtProcedurePub = $this->db->get_where('procedures', ['company_id' => $this->company, 'status' => 'PUB'])->num_rows();
		$dtGuidesPub 	=  0; //$this->db->get_where('dir_guides', ['company_id' => $this->company, 'status' => 'PUB'])->num_rows();


		$dtProc = $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();

		$rev 	= $cor = $pub = $apv = $rvi = $hld = $revDel = $apvDel = $rejDel = 0;
		foreach ($dtProc as $value) {
			if ($value->status == 'REV') {
				$rev = $rev + 1;
			}
			if ($value->status == 'COR') {
				$cor = $cor + 1;
			}
			if ($value->status == 'PUB') {
				$pub = $pub + 1;
			}
			if ($value->status == 'APV') {
				$apv = $apv + 1;
			}
			if ($value->status == 'RVI') {
				$rvi = $rvi + 1;
			}
			if (($value->status == 'HLD') && ($value->deletion_status == 'REV')) {
				$hld = $hld + 1;
			}
			if (($value->status == 'HLD') && ($value->deletion_status == 'APV')) {
				$revDel = $revDel + 1;
			}
			if (($value->status == 'HLD') && ($value->deletion_status == 'DEL')) {
				$apvDel = $apvDel + 1;
			}
			if (($value->status == 'HLD') && ($value->deletion_status == 'REJ')) {
				$rejDel = $rejDel + 1;
			}
		}

		/* FORM STATISTICS */
		$dtFormRev = $this->db->get_where('forms', ['company_id' => $this->company, 'status' => 'REV'])->num_rows();
		$dtFormCor = $this->db->get_where('forms', ['company_id' => $this->company, 'status' => 'COR'])->num_rows();
		$dtFormApv = $this->db->get_where('forms', ['company_id' => $this->company, 'status' => 'APV'])->num_rows();
		$dtFormPub = $this->db->get_where('forms', ['company_id' => $this->company, 'status' => 'PUB'])->num_rows();
		$dtFormRvi = $this->db->get_where('forms', ['company_id' => $this->company, 'status' => 'RVI'])->num_rows();

		/* FORM DELETION STATISTICS */
		$dtFormDelOPN = $this->db->get_where('forms', ['company_id' => $this->company, 'status' => 'HLD', 'deletion_status' => 'OPN'])->num_rows();
		$dtFormDelAPV = $this->db->get_where('forms', ['company_id' => $this->company, 'status' => 'HLD', 'deletion_status' => 'APV'])->num_rows();

		/* WORK INSTRUCTION STATISTICS */
		// Check if work_instructions table exists
		if ($this->db->table_exists('work_instructions')) {
			$dtWiRevQ = $this->db->get_where('work_instructions', ['company_id' => $this->company, 'status' => 'REV']);
			$dtWiRev = $dtWiRevQ ? $dtWiRevQ->num_rows() : 0;
			$dtWiCorQ = $this->db->get_where('work_instructions', ['company_id' => $this->company, 'status' => 'COR']);
			$dtWiCor = $dtWiCorQ ? $dtWiCorQ->num_rows() : 0;
			$dtWiApvQ = $this->db->get_where('work_instructions', ['company_id' => $this->company, 'status' => 'APV']);
			$dtWiApv = $dtWiApvQ ? $dtWiApvQ->num_rows() : 0;
			$dtWiRviQ = $this->db->get_where('work_instructions', ['company_id' => $this->company, 'status' => 'RVI']);
			$dtWiRvi = $dtWiRviQ ? $dtWiRviQ->num_rows() : 0;
			$dtWiPubQ = $this->db->get_where('work_instructions', ['company_id' => $this->company, 'status' => 'PUB']);
			$dtWiPub = $dtWiPubQ ? $dtWiPubQ->num_rows() : 0;
			$dtWiDelREVQ = $this->db->get_where('work_instructions', ['company_id' => $this->company, 'status' => 'HLD', 'deletion_status' => 'REV']);
			$dtWiDelREV = $dtWiDelREVQ ? $dtWiDelREVQ->num_rows() : 0;
			$dtWiDelAPVQ = $this->db->get_where('work_instructions', ['company_id' => $this->company, 'status' => 'HLD', 'deletion_status' => 'APV']);
			$dtWiDelAPV = $dtWiDelAPVQ ? $dtWiDelAPVQ->num_rows() : 0;
		} else {
			$dtWiRev = 0;
			$dtWiCor = 0;
			$dtWiApv = 0;
			$dtWiRvi = 0;
			$dtWiPub = 0;
			$dtWiDelREV = 0;
			$dtWiDelAPV = 0;
		}

		$Data = $this->db->order_by('created_at', 'ASC')->get_where('directory', ['parent_id' => '0', 'active' => 'Y', 'status !=' => 'DEL'])->result();
		$RecentFiles = $this->db->order_by('created_at', 'DESC')->get_where('directory', ['parent_id !=' => '0', 'active' => 'Y', 'flag_type' => 'FILE', 'status !=' => 'DEL', 'created_at like' => date('Y-m-d') . "%"])->result();

		$this->template->set(
			[
				'title' 			=> 'Dashboard',
				'Data' 				=> $Data,
				'RecentFiles' 		=> $RecentFiles,
				'dtProcedureRev' 	=> $rev,
				'dtProcedureApv'    => $apv,
				'dtProcedureCor'    => $cor,
				'dtProcedureRvi' 	=> $rvi,
				'dtProcedurePub'	=> $pub,
				'hld'				=> $hld,
				'revDel'			=> $revDel,
				'apvDel'			=> $apvDel,
				'rejDel'			=> $rejDel,
				'dtGuidesApv' 		=> $dtGuidesApv,
				'dtGuidesRev' 		=> $dtGuidesRev,
				'dtGuidesCor' 		=> $dtGuidesCor,
				'dtGuidesPub' 		=> $dtGuidesPub,
				'dtGuidesRvi' 		=> $dtGuidesRvi,
				'dtFormRev'			=> $dtFormRev,
				'dtFormCor'			=> $dtFormCor,
				'dtFormApv'			=> $dtFormApv,
				'dtFormPub'			=> $dtFormPub,
				'dtFormRvi'			=> $dtFormRvi,
				'dtFormDelOPN'		=> $dtFormDelOPN,
				'dtFormDelAPV'		=> $dtFormDelAPV,
				'dtWiRev'			=> $dtWiRev,
				'dtWiCor'			=> $dtWiCor,
				'dtWiApv'			=> $dtWiApv,
				'dtWiRvi'			=> $dtWiRvi,
				'dtWiPub'			=> $dtWiPub,
				'dtWiDelREV'		=> $dtWiDelREV,
				'dtWiDelAPV'		=> $dtWiDelAPV,
			]
		);

		$this->template->render('index');
	}

	public function view($id = null, $type = null)
	{
		$file          = $this->db->get_where('procedures', ['id' => $id])->row();
		$history       = $this->db->order_by('updated_at', 'ASC')->get_where('view_procedure_activity_logs', ['procedure_id' => $id])->result();
		$Data          = $this->db->get_where('view_procedures', ['id' => $id, 'company_id' => $this->company])->row();
		$bilingual     = $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
		$users         = $this->db->get_where('view_users')->result();
		$getForms      = $this->db->get_where('forms', ['status !=' => 'DEL'])->result();
		$getGuides     = $this->db->get_where('work_instructions', ['status !=' => 'DEL'])->result();
		$jabatan       = $this->db->get('positions')->result();
		$ArrUsr        = $ArrJab = $ArrDept =  $ArrForms = $ArrGuides = [];
		$depts         = $this->db->get_where('departements', ['company_id' => $this->company, 'status' => '1'])->result();
		$company       = $this->session->company;
		$revision_logs = $this->db->get_where('procedure_revision_logs', ['company_id' => $this->company, 'procedure_id' => $id, 'status' => '1'])->result();
		$signatures	   = $this->db->get_where('signature_documents', ['document_id' => $id, 'document_type' => 'procedure'])->result();

		$ArrSign = [];
		foreach($signatures as $sign){
			$ArrSign[$sign->sign_type] = $sign->qr_path;
		}

		foreach ($getForms as $frm) {
			$ArrForms[$frm->id] = $frm;
		}
		foreach ($getGuides as $gui) {
			$ArrGuides[$gui->id] = $gui;
		}

		foreach ($users as $usr) {
			$ArrUsr[$usr->id_user] = $usr;
		}

		foreach ($jabatan as $jab) {
			$ArrJab[$jab->id] = $jab;
		}

		foreach ($depts as $dept) {
			$ArrDept[$dept->id] = $dept;
		}

		if ($Data) {
			$Data_detail = $this->db->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
			$this->template->set([
				'sts'           => $this->sts,
				'file'          => $file,
				'type'          => $type,
				'history'       => $history,
				'view_data'     => false,
				'data'          => $Data,
				'bilingual'     => $bilingual,
				'detail'        => $Data_detail,
				'users'         => $users,
				'jabatan'       => $jabatan,
				'ArrUsr'        => $ArrUsr,
				'ArrJab'        => $ArrJab,
				'ArrDept'       => $ArrDept,
				'ArrForms'      => $ArrForms,
				'ArrGuides'     => $ArrGuides,
				'company'       => $company,
				'revision_logs' => $revision_logs,
				'ArrSign'       => $ArrSign,
			]);
			$this->template->render('view');
		}
	}

	public function view_data($id = null, $type = null)
	{
		$file 		= $this->db->get_where('view_procedures', ['id' => $id])->row();
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_procedure_activity_logs', ['procedure_id' => $id])->result();

		$Data          = $this->db->get_where('view_procedures', ['id' => $id, 'company_id' => $this->company])->row();
		$bilingual     = $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
		$users         = $this->db->get_where('view_users')->result();
		$getForms      = $this->db->get_where('forms', ['procedure_id' => $id])->result();
		$getGuides     = $this->db->get_where('work_instructions', ['procedure_id' => $id])->result();
		$jabatan       = $this->db->get('positions')->result();
		$ArrUsr        = $ArrJab = $ArrDept =  $ArrForms = $ArrGuides = [];
		$depts         = $this->db->get_where('departements', ['company_id' => $this->company, 'status' => '1'])->result();
		$company       = $this->session->company;
		$revision_logs = $this->db->get_where('procedure_revision_logs', ['company_id' => $this->company, 'procedure_id' => $id, 'status' => '1'])->result();


		foreach ($getForms as $frm) {
			$ArrForms[$frm->id] = $frm;
		}
		foreach ($getGuides as $gui) {
			$ArrGuides[$gui->id] = $gui;
		}

		foreach ($users as $usr) {
			$ArrUsr[$usr->id_user] = $usr;
		}

		foreach ($jabatan as $jab) {
			$ArrJab[$jab->id] = $jab;
		}

		foreach ($depts as $dept) {
			$ArrDept[$dept->id] = $dept;
		}

		if ($Data) {
			$Data_detail = $this->db->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
			$this->template->set([
				'sts'           => $this->sts,
				'file'          => $file,
				'type'          => $type,
				'history'       => $history,
				'view_data'     => true,
				'data'          => $Data,
				'bilingual'     => $bilingual,
				'detail'        => $Data_detail,
				'users'         => $users,
				'jabatan'       => $jabatan,
				'ArrUsr'        => $ArrUsr,
				'ArrJab'        => $ArrJab,
				'ArrDept'       => $ArrDept,
				'ArrForms'      => $ArrForms,
				'ArrGuides'     => $ArrGuides,
				'company'       => $company,
				'revision_logs' => $revision_logs,
			]);
			$this->template->render('view');
		}
	}


	/* REVIEW PROCESS */

	public function review()
	{
		/* REVIEW */
		$procedures  = $this->db->get_where('view_procedures', ['company_id' => $this->company, 'status' => 'REV'])->result();
		$ArrPosts    = $this->ArrPosts;
		$users       = $this->db->get_where('users')->result();
		$positions   = $this->db->get_where('positions', ['company_id' => $this->company])->result_array();
		$ArrPosition = array_combine(array_column($positions, 'id'), array_column($positions, 'name'));

		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		$this->template->set([
			'title'			=> 'REVIEW PROCEDURES',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosts'		=> $ArrPosts,
			'ArrPosition'		=> $ArrPosition,
		]);

		$this->template->render('list');
	}

	public function load_form_review($id, $type = null)
	{
		$file 		= $this->db->get_where('view_procedures', ['id' => $id])->row();
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_procedure_activity_logs', ['procedure_id' => $id])->result();

		$Data          = $this->db->get_where('view_procedures', ['id' => $id, 'company_id' => $this->company])->row();
		$bilingual     = $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
		$users         = $this->db->get_where('view_users')->result();
		$getForms      = $this->db->get_where('forms', ['procedure_id' => $id])->result();
		$getGuides     = $this->db->get_where('work_instructions', ['procedure_id' => $id])->result();
		$jabatan       = $this->db->get('positions')->result();
		$ArrUsr        = $ArrJab = $ArrDept =  $ArrForms = $ArrGuides = [];
		$depts         = $this->db->get_where('departements', ['company_id' => $this->company, 'status' => '1'])->result();
		$company       = $this->session->company;
		$revision_logs = $this->db->get_where('procedure_revision_logs', ['company_id' => $this->company, 'procedure_id' => $id, 'status' => '1'])->result();


		foreach ($getForms as $frm) {
			$ArrForms[$frm->id] = $frm;
		}
		foreach ($getGuides as $gui) {
			$ArrGuides[$gui->id] = $gui;
		}

		foreach ($users as $usr) {
			$ArrUsr[$usr->id_user] = $usr;
		}

		foreach ($jabatan as $jab) {
			$ArrJab[$jab->id] = $jab;
		}

		foreach ($depts as $dept) {
			$ArrDept[$dept->id] = $dept;
		}

		if ($Data) {
			$Data_detail = $this->db->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
			$this->template->set([
				'sts'           => $this->sts,
				'file'          => $file,
				'type'          => $type,
				'history'       => $history,
				'view_data'     => true,
				'data'          => $Data,
				'bilingual'     => $bilingual,
				'detail'        => $Data_detail,
				'users'         => $users,
				'jabatan'       => $jabatan,
				'ArrUsr'        => $ArrUsr,
				'ArrJab'        => $ArrJab,
				'ArrDept'       => $ArrDept,
				'ArrForms'      => $ArrForms,
				'ArrGuides'     => $ArrGuides,
				'company'       => $company,
				'revision_logs' => $revision_logs,
			]);
			$this->template->render('review/review-form');
		}
	}

	public function save_review()
	{
		$data = $this->input->post();
		$Return = $this->Monitoring->review($data);
		echo json_encode($Return);
	}
	/* END REVIEW PROCESS */


	/* CORRECTION RPOCESS */
	public function correction()
	{
		/* CORRECTION */
		$procedures 	= $this->db->get_where('view_procedures', ['company_id' => $this->company, 'status' => 'COR'])->result();
		$users 			= $this->db->get_where('users')->result();
		$positions 		= $this->db->get_where('positions', ['company_id' => $this->company])->result_array();
		$ArrPosition 	= array_combine(array_column($positions, 'id'), array_column($positions, 'name'));

		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		$this->template->set([
			'title'			=> 'CORRECTION PROCEDURES',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosition'	=> $ArrPosition,
			'ArrPosts'		=> $this->ArrPosts,
		]);
		$this->template->render('list');
	}

	public function load_form_correction($id = null, $type = null)
	{
		$file 		= $this->db->get_where('procedures', ['id' => $id])->row();
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_procedure_activity_logs', ['procedure_id' => $id])->result();
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
		$this->template->set('type', $type);
		$this->template->set('history', $history);
		$this->template->render('correction/correction-form');
	}

	public function save_correction()
	{
		$data = $this->input->post();

		if ($data) {
			$this->db->trans_begin();
			$this->db->update(
				'directory',
				[
					'status' => $data['status'],
					'modified_by' => $this->auth->user_id(),
					'modified_at' => date('Y-m-d H:i:s'),
				],
				['id' => $data['id']]
			);
			$this->_update_history($data);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'	 => 'Failed upload document file. Please try again later.!'
				];
			} else {
				$this->db->trans_commit();
				$Return = [
					'status' => 1,
					'msg'	 => 'Success upload document file...'
				];
			}
		} else {
			$Return = [
				'status' => 0,
				'msg'	 => 'Data not valid.'
			];
		}

		echo json_encode($Return);
	}
	/* END CORRECTION PROCESS */


	/* APPROVAL RPOCESS */
	public function approval()
	{
		/* APPROVAL */
		$procedures  = $this->db->get_where('view_procedures', ['company_id' => $this->company, 'status' => 'APV'])->result();
		$users       = $this->db->get_where('users')->result();
		$positions   = $this->db->get_where('positions', ['company_id' => $this->company])->result_array();
		$ArrPosition = array_combine(array_column($positions, 'id'), array_column($positions, 'name'));
		// $authority = $ArrPosition[$procedures->approval_id];

		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		$this->template->set([
			'title'			=> 'APPROVAL PROCEDURES',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosts'		=> $this->ArrPosts,
			'ArrPosition'	=> $ArrPosition,
		]);
		$this->template->render('list');
	}

	public function load_form_approval($id, $type = null)
	{
		if ($type && $type == 'procedures') {
			$file 		= $this->db->get_where('procedures', ['id' => $id])->row();
			$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_procedure_activity_logs', ['procedure_id' => $id])->result();
		} else {
			$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_directory_log', ['directory_id' => $id])->result();
		}
		$jabatan 	= $this->db->get('positions')->result();

		$this->template->set('jabatan', $jabatan);
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
		$this->template->set('history', $history);
		$this->template->set('type', $type);
		$this->template->render('approval/approval-form');
	}

	public function save_approval()
	{
		$data = $this->input->post();
		$Return = $this->Monitoring->approval($data);
		echo json_encode($Return);
	}


	/* PUBLISHED PROCESS */
	public function publised()
	{
		/* CORRECTION */
		$procedures 	= $this->db->order_by('modified_at', 'DESC')->get_where('view_procedures', ['company_id' => $this->company, 'status' => 'PUB'])->result();
		$users = $this->db->get_where('users')->result();

		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		$this->template->set([
			'title'			=> 'PUBLISHED PROCEDURES',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosts'		=> $this->ArrPosts,
		]);
		$this->template->render('list');
	}

	public function print_document()
	{
		$this->load->library(array('Mpdf'));
		$folder = $_GET['p'];
		$file = $_GET['f'];

		$mpdf = new mPDF('', '', '', '', '', '', '', '', '', '');
		$mpdf->SetImportUse();
		$pagecount = $mpdf->SetSourceFile('directory/' . $folder . '/' . $file);
		$tplId = $mpdf->ImportPage($pagecount);
		$mpdf->UseTemplate($tplId);
		$mpdf->addPage();
		$mpdf->WriteHTML('Hello World');
		$newfile = 'directory/' . $folder . '/' . $file;
		$mpdf->Output(
			$newfile,
			'F'
		);
		$mpdf->Output();
	}

	public function picture()
	{
		$id 		= $this->input->post('id');
		$picture 	= $this->db->get_where('pictures', ['id' => $id])->row();

		$this->template->set('picture', $picture);
		$this->template->render('change-picture');
	}

	public function upload()
	{
		$old_picture 	= $this->input->post('old_picture');
		$id 			= $this->input->post('id');

		$config['upload_path']          = './assets/img/';
		$config['allowed_types']        = 'gif|jpg|png';
		$config['max_size']             = 500;
		$config['max_width']            = 1000;
		$config['max_height']           = 1000;
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('picture')) {
			$error = $this->upload->display_errors();

			$collback = [
				'msg' => $error,
				'status' => 0
			];
			echo json_encode($collback);
			return FALSE;
		} else {
			if ($old_picture) {
				unlink('./assets/img/' . $old_picture);
			}
			$dataPicture = $this->upload->data();
			$picture = $dataPicture['file_name'];
		}

		$Arr_data = [
			'pictures' => $picture,
		];
		$this->db->trans_begin();
		$this->db->update('pictures', $Arr_data, ['id' => $id]);

		if ($this->db->trans_status() == false) {
			$this->db->trans_rollback();
			$collback = [
				'msg' => 'Upload Faild, Please ty again!',
				'status' => 0
			];
		} else {
			$this->db->trans_commit();
		}
		$collback = [
			'msg' => 'Upload Success!',
			'status' => 1,
			'picture' => $picture
		];

		echo json_encode($collback);
	}


	/* PUBLISHED PROCESS */
	public function revision()
	{
		/* CORRECTION */
		$procedures  = $this->db->get_where('view_procedures', ['company_id' => $this->company, 'status' => 'RVI'])->result();
		$users       = $this->db->get_where('users')->result();
		$positions 	 = $this->db->get_where('positions', ['company_id' => $this->company])->result_array();
		$ArrPosition = array_combine(array_column($positions, 'id'), array_column($positions, 'name'));

		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user]['id_user'] = $user->id_user;
			$ArrUsers[$user->id_user]['full_name'] = $user->full_name;
		}

		$this->template->set([
			'title'			=> 'REVISION PROCEDURES',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosition'	=> $ArrPosition,
			'ArrPosts'		=> $this->ArrPosts,
		]);

		$this->template->render('list');
	}

	public function load_form_revision($id, $type = null)
	{
		$file 		= $this->db->get_where('procedures', ['id' => $id])->row();
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_procedure_activity_logs', ['procedure_id' => $id])->result();
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
		$this->template->set('type', $type);
		$this->template->set('history', $history);
		$this->template->render('revision-form');
	}

	public function save_revision()
	{
		$data = $this->input->post();
		$Return = $this->Monitoring->saveRevision($data);
		echo json_encode($Return);
	}

	/* REVIEW DELETION */

	public function load_form_deletion($id, $type = null)
	{
		$file 		= $this->db->get_where('procedures', ['id' => $id])->row();
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_procedure_activity_logs', ['procedure_id' => $id])->result();
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
		$this->template->set('type', $type);
		$this->template->set('history', $history);
		$this->template->render('deletion-form');
	}

	public function save_deletion()
	{
		$data = $this->input->post();
		if ($data) {
			$this->db->trans_begin();
			$data['status'] = 'HLD';
			$this->Monitoring->deletion($data);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'	 => 'Failed processing this file. Please try again later.!'
				];
			} else {
				$this->db->trans_commit();
				$Return = [
					'status' => 1,
					'msg'	 => 'Success deletion document...'
				];
			}
		} else {
			$Return = [
				'status' => 0,
				'msg'	 => 'Data not valid.'
			];
		}

		echo json_encode($Return);
	}

	public function review_deletion()
	{
		/* CORRECTION */
		$procedures 	= $this->db->get_where('view_procedures', [
			'company_id' => $this->company,
			'status' => 'HLD',
			'deletion_status' => 'REV'
		])->result();
		
		$users = $this->db->get_where('users')->result();

		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		$this->template->set([
			'title'			=> 'REVIEW DELETION PROCEDURES',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosts'		=> $this->ArrPosts,
		]);
		$this->template->render('list');
	}

	public function save_rev_deletion()
	{
		$data = $this->input->post();
		if ($data) {
			$data['deletion_status'] = $data['sts'];
			// $data['status'] = 'HLD';
			if ($data['sts'] == 'APV') {
				$data['note'] = 'Reviewed for deleteion';
			} elseif ($data['sts'] == 'REJ') {
				$data['note'] = 'Rejected for deleteion';
			}

			unset($data['sts']);
			$result = $this->Monitoring->rev_deletion($data);
		} else {
			$result = [
				'status' => 0,
				'msg'	 => 'Data not valid.'
			];
		}

		echo json_encode($result);
	}

	/* APPROVAL DELETION */

	public function approval_deletion()
	{
		/* CORRECTION */
		$procedures 	= $this->db->get_where('view_procedures', [
			'company_id' => $this->company,
			'status' => 'HLD',
			'deletion_status' => 'APV'
		])->result();
		$users = $this->db->get_where('users')->result();

		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		$this->template->set([
			'title'			=> 'APPROVAL DELETION PROCEDURES',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosts'		=> $this->ArrPosts,
		]);
		$this->template->render('list');
	}

	public function save_apv_deletion()
	{
		$data = $this->input->post();
		
		if ($data) {
			$data['deletion_status'] = $data['sts'];
			if ($data['sts'] == 'APV') {
				$data['status'] = 'DEL';
				$data['note'] = 'Approved';
			} elseif ($data['sts'] == 'REJ') {
				$data['status'] = 'HLD';
				$data['note'] = 'Rejected';
			}
			unset($data['sts']);
			$result = $this->Monitoring->approval_deletion($data);
		} else {
			$result = [
				'status' => 0,
				'msg'	 => 'Data not valid.'
			];
		}

		echo json_encode($result);
	}

	public function save_delete()
	{
		$data = $this->input->post();
		if ($data) {
			$result = $this->Monitoring->delete_document($data);
		} else {
			$result = [
				'status' => 0,
				'msg'	 => 'Data not valid.'
			];
		}

		echo json_encode($result);
	}

	public function deletion_document()
	{
		/* CORRECTION */
		$procedures 	= $this->db->get_where('view_procedures', [
			'company_id' => $this->company,
			'status' => 'HLD',
			'deletion_status' => 'DEL'
		])->result();
		$users = $this->db->get_where('users')->result();

		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		$this->template->set([
			'title'			=> 'NEED ACTION TO DELETE PROCEDURES',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosts'		=> $this->ArrPosts,
		]);
		$this->template->render('list');
	}

	public function rejected_document()
	{
		/* CORRECTION */
		$procedures 	= $this->db->get_where('view_procedures', [
			'company_id' => $this->company,
			'status' => 'HLD',
			'deletion_status' => 'REJ'
		])->result();
		$users = $this->db->get_where('users')->result();

		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		$this->template->set([
			'title'			=> 'REJECTED DELETION DOCUMENTS',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosts'		=> $this->ArrPosts,
		]);
		$this->template->render('list');
	}


	/* FORMS */
	public function view_form($id)
	{
		$form = $this->db->get_where('view_forms', ['id' => $id])->row();
		$this->template->set([
			'form' => $form,
		]);
		$this->template->render('view_form');
	}

	public function forms_review()
	{
		$forms = $this->db->get_where('view_forms', ['company_id' => $this->company, 'status' => 'REV'])->result();

		// Tandai apakah user saat ini adalah PIC Reviewer untuk setiap form
		$current_user_id = $this->auth->user_id();
		foreach ($forms as $form) {
			$reviewer_position = $this->db->get_where('positions', [
				'id'          => $form->reviewer_position_id,
				'assign_user' => $current_user_id,
			])->row();
			$form->can_action = (bool) $reviewer_position;
		}

		$this->template->set([
			'title'  => 'DAFTAR FORM - REVIEW',
			'forms'  => $forms,
			'sts'    => $this->sts,
		]);

		$this->template->render('forms/list');
	}

	public function forms_correction()
	{
		$forms = $this->db->get_where('view_forms', ['company_id' => $this->company, 'status' => 'COR'])->result();

		// Tandai apakah user saat ini adalah creator/owner form
		$current_user_id = $this->auth->user_id();
		foreach ($forms as $form) {
			$form->can_action = ((int)$form->created_by === (int)$current_user_id);
		}

		$this->template->set([
			'title'  => 'DAFTAR FORM - CORRECTION',
			'forms'  => $forms,
			'sts'    => $this->sts,
		]);

		$this->template->render('forms/list');
	}

	public function forms_approval()
	{
		$forms = $this->db->get_where('view_forms', ['company_id' => $this->company, 'status' => 'APV'])->result();

		// Tandai apakah user saat ini adalah PIC Approver untuk setiap form
		$current_user_id = $this->auth->user_id();
		foreach ($forms as $form) {
			$approver_position = $this->db->get_where('positions', [
				'id'          => $form->approver_position_id,
				'assign_user' => $current_user_id,
			])->row();
			$form->can_action = (bool) $approver_position;
		}

		$this->template->set([
			'title'  => 'DAFTAR FORM - APPROVAL',
			'forms'  => $forms,
			'sts'    => $this->sts,
		]);

		$this->template->render('forms/list');
	}
	public function forms_revision()
	{
		$forms = $this->db->get_where('view_forms', ['company_id' => $this->company, 'status' => 'RVI'])->result();

		// Tandai apakah user saat ini adalah PIC Approver untuk setiap form
		$current_user_id = $this->auth->user_id();
		foreach ($forms as $form) {
			$approver_position = $this->db->get_where('positions', [
				'id'          => $form->approver_position_id,
				'assign_user' => $current_user_id,
			])->row();
			$form->can_action = (bool) $approver_position;
		}

		$this->template->set([
			'title'  => 'DAFTAR FORM - APPROVAL',
			'forms'  => $forms,
			'sts'    => $this->sts,
		]);

		$this->template->render('forms/list');
	}

	public function forms_published()
	{
		$forms = $this->db->get_where('view_forms', ['company_id' => $this->company, 'status' => 'PUB'])->result();

		$this->template->set([
			'title'   => 'DAFTAR FORM - PUBLISHED',
			'forms'   => $forms,
			'sts'     => $this->sts,
			'ArrPosts' => $this->ArrPosts,
		]);

		$this->template->render('forms/list');
	}

	public function load_form_review_form($id)
	{
		// Ambil data Form dari view_forms dengan filter company_id
		$form = $this->db->get_where('view_forms', ['id' => $id, 'company_id' => $this->company])->row();

		// Jika form tidak ditemukan atau company_id tidak cocok → 403
		if (!$form) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Form tidak ditemukan atau bukan milik perusahaan Anda.']);
			return;
		}

		// Verifikasi bahwa user saat ini adalah PIC Reviewer yang sah
		$reviewer_position = $this->db->get_where('positions', [
			'id'          => $form->reviewer_position_id,
			'assign_user' => $this->auth->user_id(),
		])->row();

		if (!$reviewer_position) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Anda bukan PIC Reviewer yang berwenang untuk Form ini.']);
			return;
		}

		// Get procedure name
		if (!empty($form->procedure_id)) {
			$procedure = $this->db->get_where('procedures', ['id' => $form->procedure_id])->row();
			$form->procedure_name = $procedure ? $procedure->name : null;
		}

		// Ambil riwayat status dari form_status_logs
		$history = $this->db->order_by('action_at', 'ASC')
			->get_where('form_status_logs', ['form_id' => $id])
			->result();

		// Ambil data user untuk mapping action_by → nama
		$users = $this->db->get_where('users')->result();
		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		// Render partial view (tanpa layout) untuk dimuat di dalam modal
		$this->load->view('monitoring/forms/review-form-modal', [
			'form'     => $form,
			'history'  => $history,
			'ArrUsers' => $ArrUsers,
			'sts'      => $this->sts,
		]);
	}

	public function save_review_form()
	{
		// Validasi AJAX request
		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			echo json_encode(['status' => 0, 'msg' => 'Invalid request.']);
			return;
		}

		$data = $this->input->post();

		if (empty($data['id'])) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak valid.']);
			return;
		}

		// Verifikasi kepemilikan company
		$form = $this->db->get_where('view_forms', ['id' => $data['id'], 'company_id' => $this->company])->row();

		if (!$form) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Form tidak ditemukan atau bukan milik perusahaan Anda.']);
			return;
		}

		// Verifikasi otorisasi PIC Reviewer
		$reviewer_position = $this->db->get_where('positions', [
			'id'          => $form->reviewer_position_id,
			'assign_user' => $this->auth->user_id(),
		])->row();

		if (!$reviewer_position) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Anda bukan PIC Reviewer yang berwenang untuk Form ini.']);
			return;
		}

		// Panggil saveReview() di FormModel dan return JSON response
		$Return = $this->FormModel->saveReview();
		echo json_encode($Return);
	}

	public function load_form_approval_form($id)
	{
		// Ambil data Form dari view_forms dengan filter company_id
		$form = $this->db->get_where('view_forms', ['id' => $id, 'company_id' => $this->company])->row();

		// Jika form tidak ditemukan atau company_id tidak cocok → 403
		if (!$form) {
			$this->output->set_status_header(403);
			show_error('Akses ditolak. Form tidak ditemukan atau bukan milik perusahaan Anda.', 403);
			return;
		}

		// Verifikasi bahwa user saat ini adalah PIC Approver yang sah
		$approver_position = $this->db->get_where('positions', [
			'id'          => $form->approver_position_id,
			'assign_user' => $this->auth->user_id(),
		])->row();

		if (!$approver_position) {
			$this->output->set_status_header(403);
			show_error('Akses ditolak. Anda bukan PIC Approver yang berwenang untuk Form ini.', 403);
			return;
		}

		// Ambil nama procedure jika ada
		if (!empty($form->procedure_id)) {
			$procedure = $this->db->get_where('procedures', ['id' => $form->procedure_id])->row();
			$form->procedure_name = $procedure ? $procedure->name : '-';
		} else {
			$form->procedure_name = '-';
		}

		// Ambil riwayat status dari form_status_logs
		$history = $this->db->order_by('action_at', 'ASC')
			->get_where('form_status_logs', ['form_id' => $id])
			->result();

		// Ambil data user untuk mapping action_by → nama
		$users = $this->db->get_where('users')->result();
		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		// Render partial view (tanpa layout) untuk dimuat di dalam modal
		$this->load->view('monitoring/forms/approval-form-modal', [
			'form'     => $form,
			'history'  => $history,
			'ArrUsers' => $ArrUsers,
			'sts'      => $this->sts,
		]);
	}

	public function load_form_correction_form($id)
	{
		$form = $this->db->get_where('view_forms', ['id' => $id, 'company_id' => $this->company])->row();

		if (!$form) {
			$this->output->set_status_header(403);
			show_error('Akses ditolak. Form tidak ditemukan atau bukan milik perusahaan Anda.', 403);
			return;
		}

		if ($form->status !== 'COR') {
			$this->output->set_status_header(403);
			show_error('Akses ditolak. Form ini tidak dalam status Correction.', 403);
			return;
		}

		// Verifikasi bahwa user adalah creator/owner form
		if ((int)$form->created_by !== (int)$this->auth->user_id()) {
			$this->output->set_status_header(403);
			show_error('Akses ditolak. Hanya pembuat form yang dapat mengajukan koreksi.', 403);
			return;
		}

		$history = $this->db->order_by('action_at', 'ASC')
			->get_where('form_status_logs', ['form_id' => $id])
			->result();

		$users = $this->db->get_where('users')->result();
		$ArrUsers = [];
		foreach ($users as $user) {
			$ArrUsers[$user->id_user] = $user;
		}

		$this->template->set([
			'form'     => $form,
			'history'  => $history,
			'ArrUsers' => $ArrUsers,
			'sts'      => $this->sts,
		]);

		$this->template->render('forms/correction-form');
	}

	public function save_correction_form()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			echo json_encode(['status' => 0, 'msg' => 'Invalid request.']);
			return;
		}

		$data = $this->input->post();

		if (empty($data['id'])) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak valid.']);
			return;
		}

		$form = $this->db->get_where('view_forms', ['id' => $data['id'], 'company_id' => $this->company])->row();

		if (!$form || $form->status !== 'COR') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau status form tidak valid.']);
			return;
		}

		// Verifikasi bahwa user adalah creator/owner form
		if ((int)$form->created_by !== (int)$this->auth->user_id()) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya pembuat form yang dapat mengajukan koreksi.']);
			return;
		}

		$note = isset($data['note']) ? trim($data['note']) : '';

		$this->db->trans_begin();

		$this->db->where('id', $form->id)->update('forms', [
			'status'      => 'REV',
			'modified_by' => $this->auth->user_id(),
			'modified_at' => date('Y-m-d H:i:s'),
		]);

		$this->db->insert('form_status_logs', [
			'form_id'    => $form->id,
			'old_status' => 'COR',
			'new_status' => 'REV',
			'action_by'  => $this->auth->user_id(),
			'action_at'  => date('Y-m-d H:i:s'),
			'note'       => $note,
		]);

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			echo json_encode(['status' => 0, 'msg' => 'Gagal menyimpan data. Silakan coba lagi.']);
			return;
		}

		$this->db->trans_commit();
		echo json_encode(['status' => 1, 'msg' => 'Form berhasil dikembalikan ke proses Review.']);
	}

	public function save_approval_form()
	{
		// Validasi AJAX request
		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			echo json_encode(['status' => 0, 'msg' => 'Invalid request.']);
			return;
		}

		$data = $this->input->post();

		if (empty($data['id'])) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak valid.']);
			return;
		}

		// Verifikasi kepemilikan company
		$form = $this->db->get_where('view_forms', ['id' => $data['id'], 'company_id' => $this->company])->row();

		if (!$form) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Form tidak ditemukan atau bukan milik perusahaan Anda.']);
			return;
		}

		// Verifikasi otorisasi PIC Approver
		$approver_position = $this->db->get_where('positions', [
			'id'          => $form->approver_position_id,
			'assign_user' => $this->auth->user_id(),
		])->row();

		if (!$approver_position) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Anda bukan PIC Approver yang berwenang untuk Form ini.']);
			return;
		}

		// Panggil saveApprove() di FormModel dan return JSON response
		$Return = $this->FormModel->saveApprove();
		echo json_encode($Return);
	}

	/* FORM REVISION */
	public function load_form_revision_form($id)
	{
		$form = $this->db->get_where('view_forms', ['id' => $id, 'company_id' => $this->company])->row();

		if (!$form || $form->status !== 'PUB') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau form tidak dalam status Published.']);
			return;
		}

		// if (!in_array(1, $this->ArrPosts)) {
		// 	$this->output->set_status_header(403);
		// 	echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya PM/MR yang dapat mengajukan revisi.']);
		// 	return;
		// }

		$this->load->view('monitoring/forms/revision-form-modal', ['form' => $form]);
	}

	public function save_form_revision()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			echo json_encode(['status' => 0, 'msg' => 'Invalid request.']);
			return;
		}

		$data = $this->input->post();

		if (empty($data['id'])) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak valid.']);
			return;
		}

		if (empty(trim(isset($data['note']) ? $data['note'] : ''))) {
			echo json_encode(array('status' => 0, 'msg' => 'Alasan revisi wajib diisi.'));
			return;
		}

		// Verifikasi company & status
		$form = $this->db->get_where('view_forms', ['id' => $data['id'], 'company_id' => $this->company])->row();
		if (!$form || $form->status !== 'PUB') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau form tidak dalam status Published.']);
			return;
		}

		// Verifikasi user menjabat posisi PM/MR (position id = 1)
		// if (!in_array(1, $this->ArrPosts)) {
			// $this->output->set_status_header(403);
			// echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya PM/MR yang dapat mengajukan revisi.']);
			// return;
		// }

		$Return = $this->FormModel->saveFormRevision($data);
		echo json_encode($Return);
	}

	/* FORM DELETION */
	public function load_form_deletion_form($id)
	{
		$form = $this->db->get_where('view_forms', ['id' => $id, 'company_id' => $this->company])->row();

		if (!$form || $form->status !== 'PUB') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau form tidak dalam status Published.']);
			return;
		}

		if (!in_array(1, $this->ArrPosts)) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya PM/MR yang dapat mengajukan penghapusan.']);
			return;
		}

		$this->load->view('monitoring/forms/deletion-form-modal', ['form' => $form]);
	}

	public function load_form_view_modal($id)
	{
		$form = $this->db->get_where('view_forms', ['id' => $id, 'company_id' => $this->company])->row();
		
		if (!$form) {
			$this->output->set_status_header(404);
			echo json_encode(['status' => 0, 'msg' => 'Form tidak ditemukan atau Anda tidak memiliki akses.']);
			return;
		}

		// Get procedure name
		if (!empty($form->procedure_id)) {
			$procedure = $this->db->get_where('procedures', ['id' => $form->procedure_id])->row();
			$form->procedure_name = $procedure ? $procedure->name : null;
		}

		// Get status logs
		$this->db->select('fsl.*, u.full_name as action_by_name');
		$this->db->from('form_status_logs fsl');
		$this->db->join('users u', 'u.id_user = fsl.action_by', 'left');
		$this->db->where('fsl.form_id', $id);
		$this->db->order_by('fsl.action_at', 'DESC');
		$status_logs = $this->db->get()->result();

		// Get reviewed_by and approved_by user names
		if (!empty($form->reviewed_by)) {
			$reviewed_user = $this->db->get_where('users', ['id_user' => $form->reviewed_by])->row();
			$form->reviewed_by_name = $reviewed_user ? $reviewed_user->full_name : null;
		}

		if (!empty($form->approved_by)) {
			$approved_user = $this->db->get_where('users', ['id_user' => $form->approved_by])->row();
			$form->approved_by_name = $approved_user ? $approved_user->full_name : null;
		}

		// VERSION CONTROL: Get version history
		$version_history = $this->FormModel->getVersionHistory($id);

		// VERSION CONTROL: Get current version to display (handles under revision scenario)
		$current_version = $this->FormModel->getCurrentVersion($id);
		
		// If under revision, use file from version history
		if ($current_version && isset($current_version->is_from_history) && $current_version->is_from_history) {
			$form->display_file_name = $current_version->file_name;
			$form->display_file_path = $current_version->file_path;
			$form->display_ext = $current_version->ext;
			$form->display_size = isset($current_version->size) ? $current_version->size : null;
			$form->showing_old_version = true;
		} else {
			$form->display_file_name = isset($form->file_name) ? $form->file_name : null;
			$form->display_file_path = isset($form->file_path) ? $form->file_path : null;
			$form->display_ext = isset($form->ext) ? $form->ext : null;
			$form->display_size = isset($form->size) ? $form->size : null;
			$form->showing_old_version = false;
		}

		$this->load->view('monitoring/forms/view-form-modal', [
			'form' => $form,
			'status_logs' => $status_logs,
			'version_history' => $version_history,
			'sts' => $this->sts,
		]);
	}

	public function save_form_deletion()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			echo json_encode(['status' => 0, 'msg' => 'Invalid request.']);
			return;
		}

		$data = $this->input->post();

		if (empty($data['id'])) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak valid.']);
			return;
		}

		if (empty(trim(isset($data['note']) ? $data['note'] : ''))) {
			echo json_encode(array('status' => 0, 'msg' => 'Alasan penghapusan wajib diisi.'));
			return;
		}

		// Verifikasi company & status
		$form = $this->db->get_where('view_forms', ['id' => $data['id'], 'company_id' => $this->company])->row();
		if (!$form || $form->status !== 'PUB') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau form tidak dalam status Published.']);
			return;
		}

		// Verifikasi user menjabat posisi PM/MR (position id = 1)
		if (!in_array(1, $this->ArrPosts)) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya PM/MR yang dapat mengajukan penghapusan.']);
			return;
		}

		$Return = $this->FormModel->saveFormDeletion($data);
		echo json_encode($Return);
	}

	/* FORMS DELETION REVIEW & APPROVAL */

	public function forms_review_deletion()
	{
		$forms = $this->db->get_where('view_forms', [
			'company_id'      => $this->company,
			'status'          => 'HLD',
			'deletion_status' => 'OPN',
		])->result();

		$this->template->set([
			'title'    => 'DAFTAR FORM - REVIEW DELETION',
			'forms'    => $forms,
			'sts'      => $this->sts,
			'ArrPosts' => $this->ArrPosts,
		]);
		$this->template->render('forms/deletion-list');
	}

	public function save_form_rev_deletion()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			echo json_encode(['status' => 0, 'msg' => 'Invalid request.']);
			return;
		}

		$data = $this->input->post();

		if (empty($data['id']) || empty($data['action'])) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak valid.']);
			return;
		}

		if (!in_array(1, $this->ArrPosts)) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya PM/MR yang dapat mereview deletion.']);
			return;
		}

		$form = $this->db->get_where('view_forms', ['id' => $data['id'], 'company_id' => $this->company])->row();
		if (!$form || $form->status !== 'HLD') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau status form tidak valid.']);
			return;
		}

		$Return = $this->FormModel->saveFormRevDeletion($data);
		echo json_encode($Return);
	}

	public function forms_approval_deletion()
	{
		$forms = $this->db->get_where('view_forms', [
			'company_id'      => $this->company,
			'status'          => 'HLD',
			'deletion_status' => 'APV',
		])->result();

		$this->template->set([
			'title'    => 'DAFTAR FORM - APPROVAL DELETION',
			'forms'    => $forms,
			'sts'      => $this->sts,
			'ArrPosts' => $this->ArrPosts,
		]);
		$this->template->render('forms/deletion-list');
	}

	public function save_form_apv_deletion()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			echo json_encode(['status' => 0, 'msg' => 'Invalid request.']);
			return;
		}

		$data = $this->input->post();

		if (empty($data['id']) || empty($data['action'])) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak valid.']);
			return;
		}

		if (!in_array(1, $this->ArrPosts)) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya PM/MR yang dapat menyetujui deletion.']);
			return;
		}

		$form = $this->db->get_where('view_forms', ['id' => $data['id'], 'company_id' => $this->company])->row();
		if (!$form || $form->status !== 'HLD') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau status form tidak valid.']);
			return;
		}

		$Return = $this->FormModel->saveFormApvDeletion($data);
		echo json_encode($Return);
	}

	/* WORK INSTRUCTIONS MONITORING */

	public function wi_review()
	{
		$work_instructions = $this->db->get_where('view_work_instructions', ['company_id' => $this->company, 'status' => 'REV'])->result();

		// Tandai apakah user saat ini adalah PIC Reviewer untuk setiap WI
		$current_user_id = $this->auth->user_id();
		foreach ($work_instructions as $wi) {
			$reviewer_position = $this->db->get_where('positions', [
				'id'          => $wi->reviewer_position_id,
				'assign_user' => $current_user_id,
			])->row();
			$wi->can_action = (bool) $reviewer_position;
		}

		$this->template->set([
			'title'  => 'DAFTAR WORK INSTRUCTION - REVIEW',
			'work_instructions'  => $work_instructions,
			'sts'    => $this->sts,
		]);

		$this->template->render('work_instructions/list');
	}

	public function wi_correction()
	{
		$work_instructions = $this->db->get_where('view_work_instructions', ['company_id' => $this->company, 'status' => 'COR'])->result();

		// Tandai apakah user saat ini adalah creator/owner WI
		$current_user_id = $this->auth->user_id();
		foreach ($work_instructions as $wi) {
			$wi->can_action = ((int)$wi->created_by === (int)$current_user_id);
		}

		$this->template->set([
			'title'  => 'DAFTAR WORK INSTRUCTION - CORRECTION',
			'work_instructions'  => $work_instructions,
			'sts'    => $this->sts,
		]);

		$this->template->render('work_instructions/list');
	}

	public function wi_approval()
	{
		$work_instructions = $this->db->get_where('view_work_instructions', ['company_id' => $this->company, 'status' => 'APV'])->result();

		// Tandai apakah user saat ini adalah PIC Approver untuk setiap WI
		$current_user_id = $this->auth->user_id();
		foreach ($work_instructions as $wi) {
			$approver_position = $this->db->get_where('positions', [
				'id'          => $wi->approver_position_id,
				'assign_user' => $current_user_id,
			])->row();
			$wi->can_action = (bool) $approver_position;
		}

		$this->template->set([
			'title'  => 'DAFTAR WORK INSTRUCTION - APPROVAL',
			'work_instructions'  => $work_instructions,
			'sts'    => $this->sts,
		]);

		$this->template->render('work_instructions/list');
	}

	public function wi_published()
	{
		$work_instructions = $this->db->get_where('view_work_instructions', ['company_id' => $this->company, 'status' => 'PUB'])->result();

		$this->template->set([
			'title'  => 'DAFTAR WORK INSTRUCTION - PUBLISHED',
			'work_instructions'  => $work_instructions,
			'sts'    => $this->sts,
			'ArrPosts' => $this->ArrPosts,
		]);

		$this->template->render('work_instructions/list');
	}

	public function wi_revision()
	{
		$work_instructions = $this->db->get_where('view_work_instructions', ['company_id' => $this->company, 'status' => 'RVI'])->result();

		$this->template->set([
			'title'  => 'DAFTAR WORK INSTRUCTION - REVISION',
			'work_instructions'  => $work_instructions,
			'sts'    => $this->sts,
		]);

		$this->template->render('work_instructions/list');
	}

	public function view_wi($id)
	{
		$wi = $this->db->get_where('view_work_instructions', ['id' => $id, 'company_id' => $this->company])->row();
		
		if (!$wi) {
			show_404();
			return;
		}

		// Get status logs for audit trail
		$status_logs = $this->db->order_by('action_at', 'DESC')->get_where('work_instruction_status_logs', ['work_instruction_id' => $id])->result();

		// Get reviewed_by and approved_by user names
		if (!empty($wi->reviewed_by)) {
			$reviewed_user = $this->db->get_where('users', ['id_user' => $wi->reviewed_by])->row();
			$wi->reviewed_by_name = $reviewed_user ? $reviewed_user->full_name : null;
		}

		if (!empty($wi->approved_by)) {
			$approved_user = $this->db->get_where('users', ['id_user' => $wi->approved_by])->row();
			$wi->approved_by_name = $approved_user ? $approved_user->full_name : null;
		}

		// Get procedure name
		if (!empty($wi->procedure_id)) {
			$procedure = $this->db->get_where('procedures', ['id' => $wi->procedure_id])->row();
			$wi->procedure_name = $procedure ? $procedure->name : null;
		}

		// Get action_by names for status logs
		foreach ($status_logs as $log) {
			$action_user = $this->db->get_where('users', ['id_user' => $log->action_by])->row();
			$log->action_by_name = $action_user ? $action_user->full_name : null;
		}

		// VERSION CONTROL: Get version history
		$this->load->model('work_instructions/Work_instruction_model', 'WiModel');
		$version_history = $this->WiModel->getVersionHistory($id);

		// VERSION CONTROL: Get current version to display (handles under revision scenario)
		$current_version = $this->WiModel->getCurrentVersion($id);
		
		// If under revision, use file from version history
		if ($current_version && isset($current_version->is_from_history) && $current_version->is_from_history) {
			$wi->display_file_name = $current_version->file_name;
			$wi->display_file_path = $current_version->file_path;
			$wi->display_ext = $current_version->ext;
			$wi->display_size = isset($current_version->size) ? $current_version->size : null;
			$wi->showing_old_version = true;
		} else {
			$wi->display_file_name = isset($wi->file_name) ? $wi->file_name : null;
			$wi->display_file_path = isset($wi->file_path) ? $wi->file_path : null;
			$wi->display_ext = isset($wi->ext) ? $wi->ext : null;
			$wi->display_size = isset($wi->size) ? $wi->size : null;
			$wi->showing_old_version = false;
		}

		$this->template->set([
			'wi' => $wi,
			'status_logs' => $status_logs,
			'version_history' => $version_history,
			'sts' => $this->sts,
		]);
		
		$this->template->render('work_instructions/view');
	}

	/**
	 * Load WI detail with preview for review modal
	 */
	public function load_wi_review_modal($id)
	{
		$wi = $this->db->get_where('view_work_instructions', ['id' => $id, 'company_id' => $this->company])->row();
		
		if (!$wi) {
			$this->output->set_status_header(404);
			echo json_encode(['status' => 0, 'msg' => 'Work Instruction tidak ditemukan.']);
			return;
		}

		// Verifikasi bahwa user saat ini adalah PIC Reviewer yang sah
		$reviewer_position = $this->db->get_where('positions', [
			'id'          => $wi->reviewer_position_id,
			'assign_user' => $this->auth->user_id(),
		])->row();

		if (!$reviewer_position) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Anda bukan PIC Reviewer yang berwenang untuk Work Instruction ini.']);
			return;
		}

		// Get procedure name
		if (!empty($wi->procedure_id)) {
			$procedure = $this->db->get_where('procedures', ['id' => $wi->procedure_id])->row();
			$wi->procedure_name = $procedure ? $procedure->name : null;
		}

		// Load partial view untuk modal
		$this->load->view('work_instructions/review_modal', [
			'wi' => $wi,
			'sts' => $this->sts,
		]);
	}

	/**
	 * Load WI detail with preview for approval modal
	 */
	public function load_wi_approval_modal($id)
	{
		$wi = $this->db->get_where('view_work_instructions', ['id' => $id, 'company_id' => $this->company])->row();
		
		if (!$wi) {
			$this->output->set_status_header(404);
			echo json_encode(['status' => 0, 'msg' => 'Work Instruction tidak ditemukan.']);
			return;
		}

		// Verifikasi bahwa user saat ini adalah PIC Approver yang sah
		$approver_position = $this->db->get_where('positions', [
			'id'          => $wi->approver_position_id,
			'assign_user' => $this->auth->user_id(),
		])->row();

		if (!$approver_position) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Anda bukan PIC Approver yang berwenang untuk Work Instruction ini.']);
			return;
		}

		// Get procedure name
		if (!empty($wi->procedure_id)) {
			$procedure = $this->db->get_where('procedures', ['id' => $wi->procedure_id])->row();
			$wi->procedure_name = $procedure ? $procedure->name : null;
		}

		// Load partial view untuk modal
		$this->load->view('work_instructions/approval_modal', [
			'wi' => $wi,
			'sts' => $this->sts,
		]);
	}

	public function load_wi_revision_modal($id)
	{
		$wi = $this->db->get_where('view_work_instructions', ['id' => $id, 'company_id' => $this->company])->row();

		if (!$wi || $wi->status !== 'PUB') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau work instruction tidak dalam status Published.']);
			return;
		}

		$this->load->view('monitoring/work_instructions/revision_modal', ['wi' => $wi]);
	}

	public function load_wi_deletion_modal($id)
	{
		$wi = $this->db->get_where('view_work_instructions', ['id' => $id, 'company_id' => $this->company])->row();

		if (!$wi || $wi->status !== 'PUB') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau work instruction tidak dalam status Published.']);
			return;
		}

		if (!in_array(1, $this->ArrPosts)) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya PM/MR yang dapat mengajukan penghapusan.']);
			return;
		}

		$this->load->view('monitoring/work_instructions/deletion_modal', ['wi' => $wi]);
	}

	public function wi_review_deletion()
	{
		$work_instructions = $this->db->get_where('view_work_instructions', [
			'company_id'      => $this->company,
			'status'          => 'HLD',
			'deletion_status' => 'REV',
		])->result();

		$this->template->set([
			'title'             => 'DAFTAR WORK INSTRUCTION - REVIEW DELETION',
			'work_instructions' => $work_instructions,
			'sts'               => $this->sts,
			'ArrPosts'          => $this->ArrPosts,
		]);
		$this->template->render('work_instructions/deletion-list');
	}

	public function save_wi_rev_deletion()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			echo json_encode(['status' => 0, 'msg' => 'Invalid request.']);
			return;
		}

		$data = $this->input->post();

		if (empty($data['id']) || empty($data['action'])) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak valid.']);
			return;
		}

		if (!in_array(1, $this->ArrPosts)) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya PM/MR yang dapat mereview deletion.']);
			return;
		}

		$wi = $this->db->get_where('view_work_instructions', ['id' => $data['id'], 'company_id' => $this->company])->row();
		if (!$wi || $wi->status !== 'HLD') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau status work instruction tidak valid.']);
			return;
		}

		$Return = $this->WiModel->saveWiRevDeletion($data);
		echo json_encode($Return);
	}

	public function wi_approval_deletion()
	{
		$work_instructions = $this->db->get_where('view_work_instructions', [
			'company_id'      => $this->company,
			'status'          => 'HLD',
			'deletion_status' => 'APV',
		])->result();

		$this->template->set([
			'title'             => 'DAFTAR WORK INSTRUCTION - APPROVAL DELETION',
			'work_instructions' => $work_instructions,
			'sts'               => $this->sts,
			'ArrPosts'          => $this->ArrPosts,
		]);
		$this->template->render('work_instructions/deletion-list');
	}

	public function save_wi_apv_deletion()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_status_header(400);
			echo json_encode(['status' => 0, 'msg' => 'Invalid request.']);
			return;
		}

		$data = $this->input->post();

		if (empty($data['id']) || empty($data['action'])) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak valid.']);
			return;
		}

		if (!in_array(1, $this->ArrPosts)) {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya PM/MR yang dapat menyetujui deletion.']);
			return;
		}

		$wi = $this->db->get_where('view_work_instructions', ['id' => $data['id'], 'company_id' => $this->company])->row();
		if (!$wi || $wi->status !== 'HLD') {
			$this->output->set_status_header(403);
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak atau status work instruction tidak valid.']);
			return;
		}

		$Return = $this->WiModel->saveWiApvDeletion($data);
		echo json_encode($Return);
	}
}
