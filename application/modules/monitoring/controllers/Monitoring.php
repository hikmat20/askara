<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monitoring extends Admin_Controller
{
	/*
 * @author Yunaz
 * @copyright Copyright (c) 2016, Yunaz
 * 
 * This is controller for Penerimaan
 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('monitoring/monitoring_model', 'Monitor_model');
		$this->template->set_theme('dashboard');
		$this->template->page_icon('fa fa-dashboard');

		$this->sts = [
			'DFT' => '<span class="label label-light-secondary label-pill label-inline mr-2 text-dark-50">Draft</span>',
			'OPN' => '<span class="label label-light-primary label-pill label-inline mr-2">New</span>',
			'REV' => '<span class="label label-light-warning label-pill label-inline mr-2">Waiting Review</span>',
			'COR' => '<span class="label label-light-danger label-pill label-inline mr-2">Need Correction</span>',
			'APV' => '<span class="label label-light-info label-pill label-inline mr-2">Waiting Approval</span>',
			'PUB' => '<span class="label label-light-success label-pill label-inline mr-2">Published</span>',
			'RVI' => '<span class="label label-light-danger label-pill label-inline mr-2">Revision</span>',
			'HLD' => '<span class="label label-light-secondary text-secondary label-pill label-inline mr-2">Hold</span>',
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
			if (($value->status == 'HLD') && ($value->deletion_status == 'OPN')) {
				$hld = $hld + 1;
			}
			if (($value->status == 'HLD') && ($value->deletion_status == 'REV')) {
				$revDel = $revDel + 1;
			}
			if (($value->status == 'HLD') && ($value->deletion_status == 'APV')) {
				$apvDel = $apvDel + 1;
			}
			if (($value->status == 'HLD') && ($value->deletion_status == 'REJ')) {
				$rejDel = $rejDel + 1;
			}
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
			]
		);

		$this->template->render('index');
	}

	public function view($id = null, $type = null)
	{
		$file 		= $this->db->get_where('procedures', ['id' => $id])->row();
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_directory_log', ['directory_id' => $id])->result();
		$Data          = $this->db->get_where('view_procedures', ['id' => $id, 'company_id' => $this->company])->row();
		$bilingual     = $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
		$users         = $this->db->get_where('view_users')->result();
		$getForms      = $this->db->get_where('dir_forms', ['procedure_id' => $id])->result();
		$getGuides     = $this->db->get_where('dir_guides', ['procedure_id' => $id])->result();
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
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_directory_log', ['directory_id' => $id])->result();

		$Data          = $this->db->get_where('view_procedures', ['id' => $id, 'company_id' => $this->company])->row();
		$bilingual     = $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
		$users         = $this->db->get_where('view_users')->result();
		$getForms      = $this->db->get_where('dir_forms', ['procedure_id' => $id])->result();
		$getGuides     = $this->db->get_where('dir_guides', ['procedure_id' => $id])->result();
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
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_directory_log', ['directory_id' => $id])->result();

		$Data          = $this->db->get_where('view_procedures', ['id' => $id, 'company_id' => $this->company])->row();
		$bilingual     = $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
		$users         = $this->db->get_where('view_users')->result();
		$getForms      = $this->db->get_where('dir_forms', ['procedure_id' => $id])->result();
		$getGuides     = $this->db->get_where('dir_guides', ['procedure_id' => $id])->result();
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
		$Return = $this->Monitor_model->review($data);
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
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
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
		$procedures 	= $this->db->get_where('view_procedures', ['company_id' => $this->company, 'status' => 'APV'])->result();
		$users = $this->db->get_where('users')->result();
		$positions = $this->db->get_where('positions', ['company_id' => $this->company])->result_array();
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
		}

		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_directory_log', ['directory_id' => $id])->result();
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
		$Return = $this->Monitor_model->approval();
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
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
		$this->template->set('type', $type);
		$this->template->set('history', $history);
		$this->template->render('revision-form');
	}

	public function save_revision()
	{
		$data = $this->input->post();
		$Return = $this->Monitor_model->saveRevision($data);
		echo json_encode($Return);
	}

	/* REVIEW DELETION */

	public function load_form_deletion($id, $type = null)
	{
		$file 		= $this->db->get_where('procedures', ['id' => $id])->row();
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
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
			$this->Monitor_model->deletion($data);
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
			'deletion_status' => 'OPN'
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
			$this->db->trans_begin();
			$data['deletion_status'] = $data['sts'];
			$data['status'] = 'HLD';
			if ($data['sts'] == 'REV') {
				$data['note'] = 'Reviewed';
			} elseif ($data['sts'] == 'REJ') {
				$data['note'] = 'Rejected';
			}
			unset($data['sts']);
			$this->Monitor_model->rev_deletion($data);
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

	/* APPROVAL DELETION */

	public function approval_deletion()
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
			$this->db->trans_begin();
			$data['deletion_status'] = $data['sts'];
			if ($data['sts'] == 'APV') {
				$data['status'] = 'DEL';
				$data['note'] = 'Approved';
			} elseif ($data['sts'] == 'REJ') {
				$data['status'] = 'HLD';
				$data['note'] = 'Rejected';
			}
			unset($data['sts']);
			$this->Monitor_model->rev_deletion($data);
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

	public function deletion_document()
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
			'title'			=> 'NEED ACTION TO DELETE PROCEDURES',
			'procedures' 	=> $procedures,
			'sts'			=> $this->sts,
			'ArrUsers'		=> $ArrUsers,
			'ArrPosts'		=> $this->ArrPosts,
		]);
		$this->template->render('list');
	}
}
