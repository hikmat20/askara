<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Documents_list extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('documents_list/Documents_list_model', 'List');
		$this->load->model('Procedures/Procedures_model', 'ProcedureModel');
		$this->template->page_icon('fa fa-dashboard');
		$this->MainData 	= $this->db->get_where('directory', ['parent_id' => '0'])->result();
		$this->sts = [
			'OPN' => '<span class="label label-light-primary label-pill label-inline mr-2">New Upload</span>',
			'REV' => '<span class="label label-light-warning label-pill label-inline mr-2">Waiting Review</span>',
			'COR' => '<span class="label label-light-danger label-pill label-inline mr-2">Need Correction</span>',
			'APV' => '<span class="label label-light-info label-pill label-inline mr-2">Waiting Approval</span>',
			'PUB' => '<span class="label label-light-success label-pill label-inline mr-2">Published</span>',
		];
	}

	public function index()
	{
		//$this->template->set('sum_penacc', $sum_penacc);
		// $this->template->render('index');
		redirect('dashboard');
	}

	public function find($id)
	{
		$thisData 		= $this->db->get_where('directory', ['id' => $id])->row();
		$Data 			= $this->db->get_where('directory', ['parent_id' => $id, 'flag_type' => 'FOLDER', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$DataFile 			= $this->db->get_where('directory', ['parent_id' => $id, 'flag_type' => 'FILE', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$listDataFolder = $this->db->get_where('directory', ['flag_type' => 'FOLDER', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$listDataFile 	= $this->db->get_where('directory', ['flag_type' => 'FILE', 'status' => 'PUB', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$listDataLink 	= $this->db->get_where('directory', ['flag_type' => 'LINK', 'status !=' => 'DEL', 'company_id' => $this->company])->result();

		$ArrDataFolder = [];
		foreach ($listDataFolder as $listFolder) {
			$ArrDataFolder[$listFolder->parent_id][] = $listFolder;
		}
		$ArrDataFile = [];
		foreach ($listDataFile as $listFile) {
			$ArrDataFile[$listFile->parent_id][] = $listFile;
		}
		$ArrDataLink = [];
		foreach ($listDataLink as $listLink) {
			$ArrDataLink[$listLink->parent_id][] = $listLink;
		}

		$dt 		= $this->db->get_where('directory', ['id' => $id])->row_array();
		$buildBreadcumb = $this->buildBreadcumb($dt);

		$this->template->set('MainData', $this->MainData);
		$this->template->set('company', $this->company);
		$this->template->set('Breadcumb', $buildBreadcumb);
		$this->template->set('thisData', $thisData);
		$this->template->set('Data', $Data);
		$this->template->set('DataFile', $DataFile);
		$this->template->set('ArrDataFolder', $ArrDataFolder);
		$this->template->set('ArrDataFile', $ArrDataFile);
		$this->template->set('ArrDataLink', $ArrDataLink);

		$this->template->render('index');
	}

	function buildBreadcumb($data)
	{
		// $Breadcumb = [];
		$data = $this->db->get_where('directory', ['id' => $data['parent_id']])->row();

		if ($data) {
			if ($data->parent_id != '0') {
				$Breadcumb[] =  $data;
			}
			return isset($Breadcumb) ? $Breadcumb : '';
			$this->buildBreadcumb($data);
		}
	}

	public function show($id)
	{
		$file 		= $this->db->get_where('directory', ['id' => $id])->row();
		// pre
		$dir_name 	= $this->db->get_where('directory', ['id' => $file->parent_id])->row()->name;
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
		$type 		= 'STANDARDS';

		$this->template->set('type', $type);
		$this->template->set('company', $this->company);
		$this->template->set('dir_name', $dir_name);
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
		$this->template->set('history', $history);
		$this->template->render('show');
	}

	public function procedures($id = null)
	{


		if (isset($id)) {
			$procedures = $this->ProcedureModel->find_all_by(['status' => 'PUB']);
			$forms        = $this->db->order_by('name', 'ASC')->get_where('forms', ['procedure_id' => $id, 'is_active' => 'ACT', 'status !=' => 'DEL'])->result();
			$ik          = $this->db->order_by('name', 'ASC')->get_where('work_instructions', ['procedure_id' => $id, 'is_active' => 'ACT', 'status !=' => 'DEL'])->result();
			$records      = $this->db->order_by('name', 'ASC')->get_where('dir_records', ['procedure_id' => $id, 'status' => 'PUB', 'flag_type' => 'FOLDER', 'company_id' => $this->company, 'parent_id' => null])->result();
			$countRecords = $this->db->get_where('dir_records', ['procedure_id' => $id, 'status' => 'PUB', 'flag_type' => 'FILE', 'company_id' => $this->company])->num_rows();

			$this->template->set([
				'procedures'   => $procedures,
				'forms'        => $forms,
				'ik'          => $ik,
				'records'      => $records,
				'MainData'     => $this->MainData,
				'countRecords' => $countRecords
			]);
			$this->template->render('procedures/list-docs');
		} else {
			$groups        = $this->db->get_where('group_procedure', ['status' => 'ACT'])->result();
			$procedures    = $this->ProcedureModel->as_array()->find_all_by(['company_id' => $this->company, 'status' => 'PUB', 'deleted_by' => null]);

			$ArrPro = [];
			if ($procedures) {
				foreach ($procedures as $pro) {
					$ArrPro[$pro['group_procedure']][] = $pro;
				}
			}

			$this->template->set([
				'groups' 		=> $groups,
				'ArrPro' 		=> $ArrPro,
			]);
			$this->template->render('procedures/index');
		}
	}

	public function view_procedure($id)
	{
		$docs 			= $this->db->get_where('view_procedures', ['id' => $id])->row();
		$detail 		= $this->db->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
		$forms 			= $this->db->get_where('dir_forms', ['procedure_id' => $id, 'active' => 'Y'])->result();
		$guides 		= $this->db->get_where('dir_guides', ['procedure_id' => $id, 'active' => 'Y'])->result();
		$users 			= $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
		$jabatan 		= $this->db->get('positions')->result();
		$ArrUsr 		= $ArrJab = $ArrForms = $ArrGuides = [];

		foreach ($users as $usr) {
			$ArrUsr[$usr->id_user] = $usr;
		}

		foreach ($jabatan as $jab) {
			$ArrJab[$jab->id] = $jab;
		}
		foreach ($forms as $form) {
			$ArrForms[$form->id] = $form;
		}

		foreach ($guides as $gui) {
			$ArrGuides[$gui->id] = $gui;
		}


		$this->template->set([
			'docs' 			=> $docs,
			'detail' 		=> $detail,
			'MainData' 		=> $this->MainData,
			'ArrUsr' 		=> $ArrUsr,
			'ArrJab' 		=> $ArrJab,
			'ArrForms' 		=> $ArrForms,
			'ArrGuides' 	=> $ArrGuides,
		]);

		$this->template->render('procedures/view-docs');
	}

	public function view_record($id)
	{
		$record 			= $this->db->get_where('dir_records', ['id' => $id])->row();
		$history			= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
		$users = $this->db->get_where('users', ['status' => 'ACT'])->result();
		foreach ($users as $user) {
			$ArrUsr[$user->id_user] = $user;
		}
		$this->template->set([
			'record' 			=> $record,
			'history' 			=> $history,
			'sts'				=> $this->sts,
			'ArrUsr'			=> $ArrUsr
		]);

		$this->template->render('procedures/view-record');
	}

	public function view_form($id)
	{
		$form = $this->db->get_where('forms', ['id' => $id])->row();
		// $history = $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
		$users = $this->db->get_where('users', ['status' => 'ACT'])->result();
		foreach ($users as $user) {
			$ArrUsr[$user->id_user] = $user;
		}
		$this->template->set([
			'form' 			=> $form,
			// 'history' 			=> $history,
			'sts'				=> $this->sts,
			'ArrUsr'			=> $ArrUsr
		]);

		$this->template->render('procedures/view-form');
	}

	public function view_guide($id)
	{
		$ik 			= $this->db->get_where('work_instructions', ['id' => $id])->row();
		// $history			= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
		$users = $this->db->get_where('users', ['status' => 'ACT'])->result();
		foreach ($users as $user) {
			$ArrUsr[$user->id_user] = $user;
		}
		$this->template->set([
			'ik' 		=> $ik,
			// 'history' 		=> $history,
			'sts'			=> $this->sts,
			'ArrUsr'		=> $ArrUsr
		]);

		// redirect("directory/GUIDES/$guide->company_id/" . $guide->file_name);
		$this->template->render('procedures/view-guide');
	}

	/* PROCEDURES */
	/* ========== */

	public function getRecords($methode = null,  $procedure_id = null, $id = null)
	{
		if ($methode == 'home') {
			$records = $this->db->get_where('dir_records', ['company_id' => $this->company, 'procedure_id' => $procedure_id, 'parent_id' => null, 'status' => 'PUB', 'flag_type' => 'FOLDER'])->result();
			$EOF = true;
		} elseif ($methode == 'back') {
			$parent_id = $this->db->get_where('dir_records', ['id' => $id])->row()->parent_id;
			if ($parent_id > 0) {
				$records = $this->db->get_where('dir_records', ['company_id' => $this->company, 'procedure_id' => $procedure_id, 'parent_id' => $parent_id, 'status' => 'PUB', 'flag_type' => 'FOLDER'])->result();
				$EOF = false;
				$id = $parent_id;
			} else {
				$records = $this->db->get_where('dir_records', ['company_id' => $this->company, 'procedure_id' => $procedure_id, 'parent_id' => null, 'status' => 'PUB', 'flag_type' => 'FOLDER'])->result();
				$EOF = true;
				$id = '';
			}
		} elseif ($methode == 'refresh') {
			if ($id) {
				$records = $this->db->get_where('dir_records', ['company_id' => $this->company, 'procedure_id' => $procedure_id, 'parent_id' => $id, 'status' => 'PUB'])->result();
				$EOF = false;
			} else {
				$records = $this->db->get_where('dir_records', ['company_id' => $this->company, 'procedure_id' => $procedure_id, 'parent_id' => null, 'status' => 'PUB', 'flag_type' => 'FOLDER'])->result();
				$EOF = true;
			}
		} elseif ($methode == 'find') {
			$records = $this->db->get_where('dir_records', ['company_id' => $this->company, 'procedure_id' => $procedure_id, 'parent_id' => $id])->result();
			$EOF = false;
		}

		$this->template->set([
			'id' 			=> $id,
			'EOF' 			=> $EOF,
			'procedure_id' 	=> $procedure_id,
			'records' 		=> $records,
		]);

		$this->template->render('procedures/records');
	}



	/* PEMENUHAN */

	public function compliances()
	{
		$reference = $this->db->get_where('view_references')->result();
		$ArrUsers = [];
		$users       = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();

		foreach ($users as $usr) {
			$ArrUsers[$usr->id_user] = $usr->full_name;
		}

		$this->template->set([
			// 'regulations'   => $regulations,
			'reference'     => $reference,
			'ArrUsers'       => $ArrUsers,
			// 'reviews'       => $reviews,
			// 'summary'       => $summary,
		]);
		$this->template->render('compliances/index');
	}

	public function view_compliance($id = null)
	{
		$review = $this->db->order_by('last_review', 'DESC')->get_where('compilation_reviews', ['reference_id' => $id])->row();
		$this->template->set([
			'review' 	=> $review,
		]);
		$this->template->render('compliances/view');
	}



	/* MATERI TRAINING */

	public function materi()
	{
		if ($this->input->get('f') && $this->input->get('sub')) {
			$f 				= $this->input->get('f');
			$sub 			= $this->input->get('sub');
			$materi 		= $this->db->get_where('materi_details', ['id' => $f])->result();

			$dtlData 	= $this->db->get_where('materi_detail_data', ['materi_detail_id' => $sub, 'status' => '1'])->result();

			$category 		= [
				'MAT' => 'Materi Training',
				'PRE' => 'Pre Test & Post Test',
				'STU' => 'Studi Kasus, Quiz & Workshop',
				'SIL' => 'Silabus',
				'VID' => 'Video',
				'REF' => 'Reference',
			];
			$ArrDtlData = [];
			foreach ($dtlData as $dtl) {
				$ArrDtlData[$dtl->category][] = $dtl;
			}

			$this->template->set([
				'materi' 		=> $materi,
				'category' 		=> $category,
				'ArrDtlData' 	=> $ArrDtlData,
			]);

			$this->template->render('materi/list');
			return false;
		};


		$materi 		= $this->db->get_where('materi', ['status' => '1', 'company_id' => $this->company])->result();
		$detail 		= $this->db->get_where('materi_details', ['status' => '1', 'company_id' => $this->company])->result();
		$ArrDetail 		= [];

		foreach ($detail as $dtl) {
			$ArrDetail[$dtl->materi_id][] = $dtl;
		}

		$this->template->set([
			'materi' 		=> $materi,
			'ArrDetail' 	=> $ArrDetail,
		]);

		$this->template->render('materi/index');
	}

	public function show_materi($id = null)
	{


		$file 			= $this->db->get_where('materi_detail_data', ['id' => $id])->row();
		$array 			= explode('.', $file->document);
		$extension 		= end($array);

		$this->template->set([
			'file' 		=> $file,
			'ext' 		=> $extension,
			// 'dir' 		=> $parent,
			'company' 		=> $this->company,
		]);
		$this->template->render('materi/show');
	}


	/* MASTER IK */

	public function guides()
	{
		if ($this->input->get('f')) {
			$f 				= $this->input->get('f');
			// $sub 			= $this->input->get('sub');
			$guide_details 		= $this->db->get_where('guide_details', ['id' => $f, 'company_id' => $this->company])->result();
			$methode 		= ['INS' => 'Insitu', 'LAB' => 'Inlab'];
			$dtlData 	= $this->db->get_where('view_guides_detail_data', ['guide_detail_id' => $f, 'company_id' => $this->company])->result();

			$ArrDtlData = [];
			foreach ($dtlData as $dtl) {
				$ArrDtlData[$dtl->guide_detail_id][] = $dtl;
			}

			$this->template->set([
				'guide_details' => $guide_details,
				'ArrDtlData' 	=> $ArrDtlData,
				'methode' 	=> $methode,
			]);

			$this->template->render('guides/list');
			return false;
		};


		$guides 		= $this->db->get_where('guides', ['status' => '1', 'company_id' => $this->company])->result();
		$details 		= $this->db->get_where('guide_details', ['status' => '1', 'company_id' => $this->company])->result();
		$ArrDetail 		= [];

		foreach ($details as $dtl) {
			$ArrDetail[$dtl->guide_id][] = $dtl;
		}
		$this->template->set([
			'guides' 		=> $guides,
			'ArrDetail' 	=> $ArrDetail,
		]);

		$this->template->render('guides/index');
	}

	public function view_guides($id = null)
	{
		$data 			= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'status' => '1'])->result();
		$ArrDoc = [];
		foreach ($data as $d) {
			$ArrDoc[$d->file_type][] = $d;
		}
		$file 			= './directory/MASTER_GUIDES/' . $this->company . '/';
		$methode 		= ['INS' => 'Insitu', 'LAB' => 'Inlab'];
		$this->template->set([
			'data' 		=> $data,
			'ArrDoc'	=> $ArrDoc,
			'file'		=> $file,
			'methode'	=> $methode
		]);

		$this->template->render('guides/view_guides');
	}

	public function view_video($id = null)
	{
		$data 			= $this->db->get_where('view_guides_detail_data', ['id' => $id])->row();
		$video 			= 'directory/MASTER_GUIDES/video/' . $data->company_id . '/' . $data->video;

		$this->template->set([
			'video'		=> $video,
		]);

		$this->template->render('guides/view_video');
	}

	public function show_file_guides($id = null)
	{
		$file 			= $this->db->get_where('guide_detail_data', ['id' => $id])->row();
		$array 			= explode('.', $file->document);
		$extension 		= end($array);

		$this->template->set([
			'file' 		=> $file,
			'ext' 		=> $extension
		]);

		$this->template->render('guides/show');
	}

	public function view_file_guides($id = null)
	{
		$file 			= $this->db->get_where('guide_documents', ['id' => $id])->row();
		$array 			= explode('.', $file->document);
		$extension 		= end($array);

		$this->template->set([
			'file' 		=> $file,
			'ext' 		=> $extension
		]);

		$this->template->render('guides/show');
	}


	/* MANUAL */

	public function manual()
	{
		$thisData 		= $this->db->get_where('directory', ['description' => 'MANUAL', 'status !=' => 'DEL', 'company_id' => $this->company])->row();
		$Data 			= $this->db->get_where('directory', ['parent_id' => $thisData->id, 'flag_type' => 'FOLDER', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$DataFile 			= $this->db->get_where('directory', ['parent_id' => $thisData->id, 'flag_type' => 'FILE', 'status !=' => 'DEL', 'company_id' => $this->company])->result();

		$listDataFolder = $this->db->get_where('directory', ['flag_type' => 'FOLDER', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$listDataFile 	= $this->db->get_where('directory', ['flag_type' => 'FILE', 'status' => 'PUB', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$listDataLink 	= $this->db->get_where('directory', ['flag_type' => 'LINK', 'status !=' => 'DEL', 'company_id' => $this->company])->result();

		$ArrDataFolder = [];
		foreach ($listDataFolder as $listFolder) {
			$ArrDataFolder[$listFolder->parent_id][] = $listFolder;
		}
		$ArrDataFile = [];
		foreach ($listDataFile as $listFile) {
			$ArrDataFile[$listFile->parent_id][] = $listFile;
		}
		$ArrDataLink = [];
		foreach ($listDataLink as $listLink) {
			$ArrDataLink[$listLink->parent_id][] = $listLink;
		}

		$dt 		= $this->db->get_where('directory', ['id' => '00062c7fd13bd121'])->row_array();
		$buildBreadcumb = $this->buildBreadcumb($dt);

		$this->template->set('MainData', $this->MainData);
		$this->template->set('company', $this->company);
		$this->template->set('Breadcumb', $buildBreadcumb);
		$this->template->set('thisData', $thisData);
		$this->template->set('Data', $Data);
		$this->template->set('DataFile', $DataFile);
		$this->template->set('ArrDataFolder', $ArrDataFolder);
		$this->template->set('ArrDataFile', $ArrDataFile);
		$this->template->set('ArrDataLink', $ArrDataLink);

		$this->template->render('manual/index');
	}

	public function show_manual($id = null)
	{
		$file 		= $this->db->get_where('directory', ['id' => $id])->row();
		// pre
		$dir_name 	= $this->db->get_where('directory', ['id' => $file->parent_id])->row()->name;
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
		$type 		= 'MANUAL';

		$this->template->set('type', $type);
		$this->template->set('company', $this->company);
		$this->template->set('dir_name', $dir_name);
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
		$this->template->set('history', $history);
		$this->template->render('show');
	}

	public function find_manual($id)
	{
		$thisData 		= $this->db->get_where('directory', ['id' => $id])->row();
		$Data 			= $this->db->get_where('directory', ['parent_id' => $id, 'flag_type' => 'FOLDER', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$DataFile 			= $this->db->get_where('directory', ['parent_id' => $id, 'flag_type' => 'FILE', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$listDataFolder = $this->db->get_where('directory', ['flag_type' => 'FOLDER', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$listDataFile 	= $this->db->get_where('directory', ['flag_type' => 'FILE', 'status' => 'PUB', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
		$listDataLink 	= $this->db->get_where('directory', ['flag_type' => 'LINK', 'status !=' => 'DEL', 'company_id' => $this->company])->result();

		$ArrDataFolder = [];
		foreach ($listDataFolder as $listFolder) {
			$ArrDataFolder[$listFolder->parent_id][] = $listFolder;
		}
		$ArrDataFile = [];
		foreach ($listDataFile as $listFile) {
			$ArrDataFile[$listFile->parent_id][] = $listFile;
		}
		$ArrDataLink = [];
		foreach ($listDataLink as $listLink) {
			$ArrDataLink[$listLink->parent_id][] = $listLink;
		}

		$dt 		= $this->db->get_where('directory', ['id' => $id])->row_array();
		$buildBreadcumb = $this->buildBreadcumb($dt);

		$this->template->set('MainData', $this->MainData);
		$this->template->set('company', $this->company);
		$this->template->set('Breadcumb', $buildBreadcumb);
		$this->template->set('thisData', $thisData);
		$this->template->set('Data', $Data);
		$this->template->set('DataFile', $DataFile);
		$this->template->set('ArrDataFolder', $ArrDataFolder);
		$this->template->set('ArrDataFile', $ArrDataFile);
		$this->template->set('ArrDataLink', $ArrDataLink);

		$this->template->render('manual/index');
	}


	/* CROSS REFERENCE */

	public function cross()
	{
		$data		= $this->db->get_where('view_cross_references', ['company_id' => $this->company])->result();
		$this->template->set('data', $data);
		$this->template->set('company_id', $this->company);
		$this->template->render('cross/index');
	}

	public function cross_pasal_proses($id = '')
	{
		$Data 			= $this->db->get_where('view_cross_references', ['company_id' => $this->company, 'id' => $id])->row();
		$Detail 		= $this->db->get_where('requirement_details', ['requirement_id' => $Data->standard_id])->result();
		$DetailCross	= $this->db->get_where('view_cross_reference_details', ['reference_id' => $id])->result_array();
		$Procedure		= array_combine(array_column($DetailCross, 'chapter_id'), array_column($DetailCross, 'procedure_id'));
		$other_docs 	= array_combine(array_column($DetailCross, 'chapter_id'), array_column($DetailCross, 'other_docs'));

		$procedures 	= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();
		$list_procedure = [];
		foreach ($procedures as $pro) {
			$list_procedure[$pro->id] = $pro->name;
		}

		$this->template->set([
			'Data' 				=> $Data,
			'Detail' 			=> $Detail,
			'list_procedure' 	=> $list_procedure,
			'other_docs' 		=> $other_docs,
			'Procedure' 		=> $Procedure,
		]);


		$this->template->render('cross/view');
	}

	public function cross_process_pasal($id = '')
	{
		$requirement = $this->db->get_where('requirements', ['company_id' => $this->company, 'id' => $id])->row();
		$procedures 		= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();

		$Data = [];
		foreach ($procedures as $pr) {
			$this->db->select('chapter,procedure_id,requirement_id')->from('view_cross_reference_details');
			$this->db->where("find_in_set($pr->id, procedure_id)");
			$this->db->where("company_id", $this->company);
			$this->db->where("requirement_id", $id);
			$Data[$pr->id] = $this->db->get()->result();
		}


		// $ArrData = [];
		// foreach ($Data as $dt) {
		// 	$ArrData['id'][$dt->requirement_id] = $dt->requirement_id;
		// 	$ArrData['standards'][$dt->requirement_id][] = $dt;
		// }

		// $ArrStd = [];
		// foreach ($Data as $dtstd) {
		// 	$ArrStd[$dtstd->requirement_id] = $dtstd;
		// }

		$this->template->set([
			'Data' 			=> $Data,
			'requirement' 	=> $requirement,
			// 'ArrStd' 		=> $ArrStd,
			'procedures' 	=> $procedures,
		]);

		$this->template->render('cross/process_to_pasal');
	}


	public function all_cross()
	{
		$requirement  = $this->db->get_where('view_cross_references', ['company_id' => $this->company])->result();
		$procedures   = $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();

		$Data = [];
		foreach ($requirement as $req) {
			foreach ($procedures as $pr) {
				$this->db->select('chapter,procedure_id,requirement_id')->from('view_cross_reference_details');
				$this->db->where("find_in_set($pr->id, procedure_id)");
				$this->db->where("company_id", $this->company);
				$this->db->where("requirement_id", $req->standard_id);
				$Data[$req->standard_id][$pr->id] = $this->db->get()->result();
			}
		}


		// $ArrData = [];
		// foreach ($Data as $dt) {
		// 	$ArrData['id'][$dt->requirement_id] = $dt->requirement_id;
		// 	$ArrData['standards'][$dt->requirement_id][] = $dt;
		// }

		$ArrPro = [];
		foreach ($procedures as $p) {
			$ArrPro[$p->id] = $p->name;
		}

		$this->template->set([
			'Data' 			=> $Data,
			'requirement' 	=> $requirement,
			'ArrPro' 		=> $ArrPro,
			'procedures' 	=> $procedures,
		]);

		$this->template->render('cross/all-cross');
	}
}
