<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Mpdf\Mpdf;

class Procedures extends Admin_Controller
{
	protected $status;
	protected $sts;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('download');
		$this->load->library(array('upload', 'Image_lib'));
		$this->load->model('companies/Company_model', 'Comp');


		$this->template->set('title', 'List Procedures');
		$this->template->set('icon', 'fa fa-cog');

		date_default_timezone_set("Asia/Bangkok");
		$this->status = [
			'0' => '<span class="badge badge-danger">Invalid</span>',
			'1' => '<span class="badge badge-primary">Publish</span>',
			'DFT' => '<span class="badge badge-secondary">Draft</span>'
		];

		$this->sts = [
			'DFT' => '<span class="label label-secondary label-pill label-inline mr-2">Draft</span>',
			'REV' => '<span class="label label-warning label-pill label-inline mr-2">Waiting Review</span>',
			'COR' => '<span class="label label-danger label-pill label-inline mr-2">Need Correction</span>',
			'APV' => '<span class="label label-info label-pill label-inline mr-2">Waiting Approval</span>',
			'PUB' => '<span class="label label-success label-pill label-inline mr-2">Published</span>',
			'RVI' => '<span class="label label-success label-pill label-inline mr-2">Revision</span>',
			'HLD' => '<span class="label label-light-danger label-pill label-inline mr-2">Hold For Deletion</span>',
		];
	}

	public function index()
	{
		// $data			= $this->db->get_where('procedures', ['company_id' => $this->company, 'deleted_at' => null, 'status' => '1'])->result();
		$dataDraft		= $this->db->get_where('procedures', ['company_id' => $this->company, 'deleted_at' => null, 'status' => 'DFT'])->result();
		$dataRev		= $this->db->get_where('procedures', ['company_id' => $this->company, 'deleted_at' => null, 'status' => 'REV'])->result();
		$dataCor		= $this->db->get_where('procedures', ['company_id' => $this->company, 'deleted_at' => null, 'status' => 'COR'])->result();
		$dataApv		= $this->db->get_where('procedures', ['company_id' => $this->company, 'deleted_at' => null, 'status' => 'APV'])->result();
		$dataPub		= $this->db->get_where('procedures', ['company_id' => $this->company, 'deleted_at' => null, 'status' => 'PUB'])->result();
		$dataDel		= $this->db->get_where('procedures', ['company_id' => $this->company, 'deleted_at' => null, 'status' => 'HLD', 'deletion_status' => 'APV'])->result();
		$dataRvi		= $this->db->get_where('procedures', ['company_id' => $this->company, 'deleted_at' => null, 'status' => 'RVI'])->result();
		$noteRevision	= $this->db->order_by('id', 'DESC')->select('*')->get_where('directory_log', ['doc_type' => 'Procedure', 'new_status' => 'RVI'])->result();

		$ArrReason = [];
		foreach (array_reverse($noteRevision) as $rvi) {
			$ArrReason[$rvi->directory_id] = $rvi;
		};


		$this->template->set('title', 'List of Procedures');
		$this->template->set([
			'dataDraft' => $dataDraft,
			'dataRev' 	=> $dataRev,
			'dataCor' 	=> $dataCor,
			'dataApv' 	=> $dataApv,
			'dataPub' 	=> $dataPub,
			'dataRvi' 	=> $dataRvi,
			'dataDel' 	=> $dataDel,
			'ArrReason' => $ArrReason,
		]);
		$this->template->set('status', $this->sts);
		$this->template->render('index');
	}

	public function add()
	{
		$grProcess	= $this->db->get_where('group_procedure', ['status' => 'ACT'])->result();
		$users 		= $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
		$jabatan 	= $this->db->get_where('positions', ['company_id' => $this->company])->result();
		$depts 	= $this->db->get_where('departements', ['company_id' => $this->company, 'status' => '1'])->result();

		$this->template->set([
			'grProcess' 	=> $grProcess,
			'users' 		=> $users,
			'jabatan' 		=> $jabatan,
			'depts' 		=> $depts,
		]);

		$this->template->set('title', 'Add Procedures');
		$this->template->render('add');
	}

	public function edit($id = '')
	{
		$Data 			= $this->db->get_where('procedures', ['company_id' => $this->company, 'id' => $id])->row();
		$languange 		= ['english'];

		if ($Data) {
			$Data_detail = $this->db->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
			$grProcess   = $this->db->get_where('group_procedure', ['status' => 'ACT'])->result();
			$getForms    = $this->db->get_where('dir_forms', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
			$getGuides   = $this->db->get_where('dir_guides', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
			$getRecords  = $this->db->get_where('dir_records', ['procedure_id' => $id, 'status !=' => 'DEL', 'flag_type' => 'FOLDER', 'parent_id' => null])->result();
			$users       = $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
			$jabatan     = $this->db->get_where('positions', ['company_id' => $this->company])->result();
			$depts       = $this->db->get_where('departements', ['company_id' => $this->company, 'status' => '1'])->result();
			$bilingual   = $this->db->get_where('procedure_bilingual', ['procedure_id' => $Data->id])->result();

			$bilingualArr = [];
			foreach ($bilingual as $bilingualItem) {
				$bilingualArr[$bilingualItem->language] = $bilingualItem;
			}

			$ArrForms = [];
			foreach ($getForms as $frm) {
				$ArrForms[$frm->id] = $frm;
			}
			$ArrGuides = [];
			foreach ($getGuides as $gui) {
				$ArrGuides[$gui->id] = $gui;
			}

			$this->template->set([
				'title'      => 'Edit Procedures',
				'data'       => $Data,
				'users'      => $users,
				'grProcess'  => $grProcess,
				'detail'     => $Data_detail,
				'getForms'   => $getForms,
				'getGuides'  => $getGuides,
				'getRecords' => $getRecords,
				'jabatan'    => $jabatan,
				'ArrForms'   => $ArrForms,
				'ArrGuides'  => $ArrGuides,
				'depts'      => $depts,
				'languange'  => $languange,
				'bilingualArr'  => $bilingualArr,
				'sts'        => $this->sts,
			]);

			$this->template->render('edit');
		} else {
			$data = [
				'heading' => 'Error!',
				'message' => 'Data not found..'
			];
			$this->template->render('../views/errors/html/error_404_custome', $data);
		}
	}

	public function view($id = '', $status = '')
	{
		$Data 				= $this->db->get_where('procedures', ['id' => $id, 'company_id' => $this->company])->row();
		$bilingual 			= $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
		$users 				= $this->db->get_where('view_users')->result();
		$getForms			= $this->db->get_where('dir_forms', ['procedure_id' => $id])->result();
		$getGuides			= $this->db->get_where('dir_guides', ['procedure_id' => $id])->result();
		$jabatan 			= $this->db->get('positions')->result();
		$ArrUsr 			= $ArrJab = $ArrForms = $ArrGuides = [];

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

		if ($Data) {
			$Data_detail 		= $this->db->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
			$this->template->set([
				'title'	    => 'Procedures',
				'data'      => $Data,
				'bilingual' => $bilingual,
				'detail'    => $Data_detail,
				'users'	    => $users,
				'jabatan'   => $jabatan,
				'ArrUsr'    => $ArrUsr,
				'ArrJab'    => $ArrJab,
				'ArrForms'  => $ArrForms,
				'ArrGuides' => $ArrGuides,
			]);
			$this->template->render('view');
		} else {
			$data = [
				'heading' 	=> 'Error!',
				'message' 	=> 'Data not found..'
			];
			$this->template->render('../views/errors/html/error_404_custome', $data);
		}
	}

	public function save()
	{
		$Data      = $this->input->post();
		$Data_flow = $this->input->post('flow');
		$bilingual = $Data['bilingual'];

		unset($Data['bilingual']);
		unset($Data['DataTables_Table_0_length']);
		unset($Data['DataTables_Table_1_length']);
		unset($Data['DataTables_Table_2_length']);
		if ($Data) {
			if (isset($_FILES)) {
				$images = $this->upload_images();

				if ((isset($images['error']) && $images['error']) == '1') {
					$this->db->trans_rollback();
					$Return = [
						'status' => 0,
						'msg'	 => $images['error_msg'] . ' File gambar gagal diupload, silahkan coba lagi.'
					];
					echo json_encode($Return);
					return false;
				}

				($images['image1']) ? $Data['image_flow_1'] = $images['image1'] : null;
				($images['image2']) ? $Data['image_flow_2'] = $images['image2'] : null;
				($images['image3']) ? $Data['image_flow_3'] = $images['image3'] : null;
				($images['flow_file']) ? $Data['flow_file'] = $images['flow_file'] : null;
			}

			$Data['company_id'] 	= $this->company;
			$dist 					= isset($Data['distribute_id']) ? implode(",", $Data['distribute_id']) : null;
			$Data['distribute_id']	= $dist;

			unset($Data['flow']);
			$this->db->trans_begin();
			if (isset($Data['id'])) {
				$Data['modified_by'] = $this->auth->user_id();
				$Data['modified_at'] = date('Y-m-d H:i:s');
				$pro_id = $Data['id'];
				$this->db->update('procedures', $Data, ['id' => $Data['id']]);
			} else {
				$Data['created_by'] = $this->auth->user_id();
				$Data['created_at'] = date('Y-m-d H:i:s');
				$this->db->insert('procedures', $Data);
				$pro_id = $this->db->order_by('id', 'DESC')->get_where('procedures')->row()->id;
				$thisData = $this->db->get_where('procedures', ['company_id' => $this->company, 'name' => $Data['name']])->row();
				$dataLog = [
					'directory_id' 	=> $thisData->id,
					'new_status' 	=> $thisData->status,
					'doc_type' 		=> 'Procedure',
					'note' 			=> 'New input data procedure',
				];
				$this->_update_history($dataLog);
			}


			// if bilingual
			if ($bilingual) {
				if (count($bilingual) > 0) {
					foreach ($bilingual as $lang => $dt_lang) {
						$dt_lang['procedure_id'] = ($this->db->insert_id() ? $this->db->insert_id() : $pro_id);
						$dt_lang['language'] = $lang;

						// update if exists
						$check_bilingual = $this->db->get_where('procedure_bilingual', ['procedure_id' => $pro_id, 'language' => $lang])->num_rows();

						if ($check_bilingual) {
							$this->db->update('procedure_bilingual', $dt_lang, ['procedure_id' => $pro_id, 'language' => $lang]);
						} else {
							$this->db->insert('procedure_bilingual', $dt_lang);
						}
					}
				}
			}
		}

		if ($Data_flow) {
			$Data_flow['procedure_id'] = $pro_id;
			if (isset($Data_flow['id']) && $Data_flow['id']) {
				$Data_flow['relate_doc'] 		= isset($Data_flow['relate_doc']) ? json_encode($Data_flow['relate_doc']) : '-';
				$Data_flow['relate_ik_doc'] 	= isset($Data_flow['relate_ik_doc']) ? json_encode($Data_flow['relate_ik_doc']) : '-';
				$Data_flow['modified_by'] 		= $this->auth->user_id();
				$Data_flow['modified_at'] 		= date('Y-m-d H:i:s');
				$this->db->update('procedure_details', $Data_flow, ['id' => $Data_flow['id']]);
			} else {
				$Data_flow['relate_doc'] 		= isset($Data_flow['relate_doc']) ? json_encode($Data_flow['relate_doc']) : '-';
				$Data_flow['relate_ik_doc'] 	= isset($Data_flow['relate_ik_doc']) ? json_encode($Data_flow['relate_ik_doc']) : '-';
				$Data_flow['created_by'] 		= $this->auth->user_id();
				$Data_flow['created_at'] 		= date('Y-m-d H:i:s');
				$this->db->insert('procedure_details', $Data_flow);
			}
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Data Procedure failed to save. Please try again.',
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Data Procedure successfully saved..',
				'id'			=> $pro_id,
			);
		}
		echo json_encode($Return);
	}

	public function saveFlowDetail()
	{
		$Data 			= $this->input->post('flow');
		$pro_id 		= $this->input->post('procedure_id');

		if ($Data) {
			$Data['procedure_id'] = $pro_id;
			if (isset($Data['id']) && $Data['id']) {
				$Data['relate_doc'] 		= isset($Data['relate_doc']) ? json_encode($Data['relate_doc']) : '-';
				$Data['relate_ik_doc'] 		= isset($Data['relate_ik_doc']) ? json_encode($Data['relate_ik_doc']) : '-';
				$Data['modified_by'] 		= $this->auth->user_id();
				$Data['modified_at'] 		= date('Y-m-d H:i:s');
				$this->db->update('procedure_details', $Data, ['id' => $Data['id']]);
			} else {
				$Data['relate_doc'] 		= isset($Data['relate_doc']) ? json_encode($Data['relate_doc']) : '-';
				$Data['relate_ik_doc'] 		= isset($Data['relate_ik_doc']) ? json_encode($Data['relate_ik_doc']) : '-';
				$Data['created_by'] 		= $this->auth->user_id();
				$Data['created_at'] 		= date('Y-m-d H:i:s');
				$this->db->insert('procedure_details', $Data);
			}
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status' => 0,
				'msg'	 => 'Data Flow Detail failed to save. Please try again.',
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status' => 1,
				'msg'	 => 'Data Flow Detail successfully saved..',
				'id'	 => $pro_id,
			);
		}
		echo json_encode($Return);
	}

	private function _save_upload()
	{
		$data = $this->input->post('forms');
		$dir = '';

		if (isset($data['type']) && $data['type'] == 'form') {
			$table = 'dir_forms';
			$dir = 'FORMS';
		} else if (isset($data['type']) && $data['type'] == 'guide') {
			$table = 'dir_guides';
			$dir = 'GUIDES';
		} else if (isset($data['type']) && $data['type'] == 'record') {
			$table = 'dir_records';
			$dir = 'RECORDS';
		}

		unset($data['type']);
		unset($data['flag_type']);
		if ($_FILES['forms_image']) {
			if (!is_dir("./directory/$dir")) {
				mkdir("./directory/$dir", 0755, TRUE);
				chmod("./directory/$dir", 0755);  // octal; correct value of mode
				chown("./directory/$dir", 'www-data');
			}
			// $new_name 					= $this->fixForUri($data['description']);
			$config['upload_path'] 		= "./directory/$dir"; //path folder
			$config['allowed_types'] 	= 'pdf|xlsx|docx'; //type yang dapat diakses bisa anda sesuaikan
			$config['encrypt_name'] 	= true; //Enkripsi nama yang terupload
			// $config['file_name'] 		= $new_name;
			$id 						= (!$data['id']) ? uniqid(date('m')) : $data['id'];


			$this->upload->initialize($config);

			if ($this->upload->do_upload('forms_image')) :
				$file 		= $this->upload->data();
				$file_name  = $file['file_name'];
				$size  		= $file['file_size'];
				$ext     	= $file['file_ext'];

				$data['id']	    		= $id;
				$data['name']	    	= $data['description'];
				$data['file_name']		= $file_name;
				$data['size']			= $size;
				$data['ext']			= $ext;
				$data['company_id']		= $this->company;
				// $data['flag_type']		= 'FILE';
				$dist 					= isset($data['distribute_id']) ? implode(",", $data['distribute_id']) : null;
				$data['distribute_id']	= $dist;
				$old_file 				= isset($data['old_file']) ? $data['old_file'] : null;
				unset($data['old_file']);

				if ($old_file != null) {
					if (file_exists("./directory/$dir/" . $old_file)) {
						unlink("./directory/$dir/" . $old_file);
					}
				}

				$check = $this->db->get_where($table, ['id' => $id])->num_rows();
				$note = isset($data['note']) ? $data['note'] : null;
				$data['status']			= isset($data['status']) ? $data['status'] : 'OPN';
				unset($data['note']);
				if (intval($check) == '0') {
					$data['created_by']		= $this->auth->user_id();
					$data['created_at']		= date('Y-m-d H:i:s');
					$data['note']			= 'First Upload File';
					$this->db->insert($table, $data);
				} else {
					$data['modified_by']	= $this->auth->user_id();
					$data['modified_at']	= date('Y-m-d H:i:s');
					$data['note']			= 'Re-upload File';
					$this->db->update($table, $data, ['id' => $id]);
				}

				$data['note'] = $note;
				$this->_update_history($data);

				if (isset($data['distribute_id'])) {
					foreach ($this->input->post('forms')['distribute_id'] as $key => $value) {
						$cek = $this->db->where(['id_jabatan' => $value, 'id_file' => $data['id']])->get('distribusi')->row();
						$arr_dist = [
							'id_file' => $this->input->post('id'),
							'id_jabatan' => $value,
							'created_by' => $this->auth->user_id(),
							'created_at' => date('Y-m-d H:i:s'),
						];

						if ($cek) {
							$this->db->update('distribusi', $arr_dist, ['id' => $cek->id]);
						} else if (!$cek) {
							$this->db->insert('distribusi', $arr_dist);
						} else {
							$this->db->delete('distribusi', ['id' => $cek->id]);
						}
					}
				};

			else :
				$error_msg = $this->upload->display_errors();
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'	 => $error_msg
				];
				echo json_encode($Return);
				return false;
			endif;
		} else {
			$Return = [
				'status' => 0,
				'msg'	 => 'No file or data to upload document..'
			];
			echo json_encode($Return);
			return false;
		}
	}

	public function add_flow($id = null)
	{
		$flow 	   = '';
		$forms     = $this->db->get_where('dir_forms', ['procedure_id' => $id, 'company_id' => $this->company, 'active' => 'Y', 'status !=' => 'DEL'])->result();
		$guides    = $this->db->get_where('dir_guides', ['procedure_id' => $id, 'company_id' => $this->company, 'active' => 'Y', 'status !=' => 'DEL'])->result();
		$language = ['english'];

		$this->template->set([
			'procedure_id' => $id,
			'flow'         => $flow,
			'forms'        => $forms,
			'guides'       => $guides,
			'language'    => $language,
		]);

		$this->template->render('form-flow');
	}

	public function edit_flow($proc_id = null, $id = null)
	{
		$language = ['english'];
		if ($proc_id && $id) {
			$flow 		= $this->db->get_where('procedure_details', ['id' => $id])->row();
			$forms 		= $this->db->get_where('dir_forms', ['procedure_id' => $proc_id, 'company_id' => $this->company, 'active' => 'Y', 'status !=' => 'DEL'])->result();
			$guides 	= $this->db->get_where('dir_guides', ['procedure_id' => $proc_id, 'company_id' => $this->company, 'active' => 'Y', 'status !=' => 'DEL'])->result();
		}

		$this->template->set([
			'procedure_id' => $proc_id,
			'flow' 		=> $flow,
			'forms' 	=> $forms,
			'guides' 	=> $guides,
			'language' 	=> $language,
		]);
		$this->template->render('form-flow');
	}

	public function loadFlow($id)
	{
		$Data_detail = '';
		if ($id) {
			$Data_detail 	= $this->db->order_by('number asc')->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
			$getForms	= $this->db->get_where('dir_forms', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
			$getguides	= $this->db->get_where('dir_guides', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
			$ArrForms = [];
			foreach ($getForms as $frm) {
				$ArrForms[$frm->id] = $frm;
			}
			$ArrGuides = [];
			foreach ($getguides as $gid) {
				$ArrGuides[$gid->id] = $gid;
			}
		}

		$this->template->set([
			'detail' 		=> $Data_detail,
			'ArrForms' 		=> $ArrForms,
			'ArrGuides' 	=> $ArrGuides,
		]);
		$this->template->render('data-flow');
	}

	public function load_file_flow($id)
	{
		$data = [];
		if ($id) {
			$data = $this->db->get_where('procedures', ['id' => $id])->row();

			$return = [
				'status' => 1,
				'data' => $data
			];
		} else {
			$return = [
				'status' => 0,
				'data' => $data
			];
		}

		echo json_encode($return);
	}


	public function saveFileRecord()
	{
		$data = $this->input->post('forms');

		if ($_FILES['forms_image']) {
			if (!is_dir("./directory/RECORDS/$this->company/")) {
				mkdir("./directory/RECORDS/$this->company/", 0755, TRUE);
				chmod("./directory/RECORDS/$this->company/", 0755);  // octal; correct value of mode
				chown("./directory/RECORDS/$this->company/", 'www-data');
			}
			$config['upload_path'] 		= "./directory/RECORDS/$this->company/"; //path folder
			$config['allowed_types'] 	= 'pdf'; //type yang dapat diakses bisa anda sesuaikan
			$config['encrypt_name'] 	= true; //Enkripsi nama yang terupload
			// $config['file_name'] 		= $new_name;
			$id 						= (!$data['id']) ? uniqid(date('m')) : $data['id'];

			$this->upload->initialize($config);
			if ($this->upload->do_upload('forms_image')) :
				$file 		= $this->upload->data();
				$file_name  = $file['file_name'];
				$size  		= $file['file_size'];
				$ext     	= $file['file_ext'];

				$data['id']	    		= $id;
				$data['name']	    	= $data['description'];
				$data['file_name']		= $file_name;
				$data['size']			= $size;
				$data['ext']			= $ext;
				$data['company_id']		= $this->company;
				$data['flag_type']		= 'FILE';

				$old_file 				= isset($data['old_file']) ? $data['old_file'] : null;
				unset($data['old_file']);

				if ($old_file != null) {
					if (file_exists("./directory/RECORDS/$this->company/" . $old_file)) {
						unlink("./directory/RECORDS/$this->company/" . $old_file);
					}
				}

				unset($data['type']);
				$this->db->trans_begin();
				$check = $this->db->get_where('dir_records', ['id' => $id])->num_rows();
				$note = isset($data['note']) ? $data['note'] : null;
				$data['status']			= isset($data['status']) ? $data['status'] : 'OPN';
				unset($data['note']);

				if (intval($check) == 0) {
					$data['created_by']		= $this->auth->user_id();
					$data['created_at']		= date('Y-m-d H:i:s');
					$data['note']			= 'First Upload File';
					$this->db->insert('dir_records', $data);
				} else {
					$data['modified_by']	= $this->auth->user_id();
					$data['modified_at']	= date('Y-m-d H:i:s');
					$data['note']			= 'Re-upload File';
					$this->db->update('dir_records', $data, ['id' => $id]);
				}

				$dataLog = [
					'directory_id' 	=> $id,
					'new_status' 	=> $data['status'],
					'doc_type' 		=> 'Record',
					'note'			=> 'Upload file'
				];
				$this->_update_history($dataLog);

			else :
				$error_msg = $this->upload->display_errors();
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'	 => $error_msg
				];
				echo json_encode($Return);
				return false;
			endif;


			if ($this->db->trans_status() === 'FALSE') {
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'	 => 'Data gagal diupload, silahkan coba beberapa saat lagi.'
				];
				echo json_encode($Return);
				return false;
			} else {
				$this->db->trans_commit();
				$Return = [
					'status' => 1,
					'msg'	 => 'Data Record berhasil di upload. Terima kasih'
				];
				echo json_encode($Return);
			}
		} else {
			$Return = [
				'status' => 0,
				'msg'	 => 'No file or data to upload document..'
			];
			echo json_encode($Return);
		}
	}

	private function _update_history($data)
	{
		$data['updated_by']    = $this->auth->user_id();
		$data['updated_at']    = date('Y-m-d H:i:s');
		$this->db->insert('directory_log', $data);
	}

	function delete_procedure($id)
	{
		$this->db->trans_begin();
		if (($id)) {
			$data['deleted_by'] = $this->auth->user_id();
			$data['deleted_at'] = date('Y-m-d H:i:s');
			$data['status'] = 'DEL';
			$this->db->update('procedures', $data, ['company_id' => $this->company, 'id' => $id]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Data Procedure failed to delete. Please try again.',
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Data Procedure successfully delete..',
			);
		}
		echo json_encode($Return);
	}

	function review($id)
	{
		$this->db->trans_begin();
		if (($id)) {
			$thisData = $this->db->get_where('procedures', ['id' => $id])->row();
			if ($thisData->reviewer_id == '' || $thisData->reviewer_id == null || $thisData->approval_id == '' || $thisData->approval_id == null) {
				$Return		= array(
					'status'		=> 0,
					'msg'			=> 'Please select Reviewer User And Approval User first to go to the next process.',
				);
				echo json_encode($Return);
				return false;
			}

			$data['modified_by'] = $this->auth->user_id();
			$data['modified_at'] = date('Y-m-d H:i:s');
			$data['status'] = 'REV';

			if ($thisData->status == 'RVI') {
				$data['revision'] = $thisData->revision + 1;
				$data['revision_date'] = date('Y-m-d H:i:s');
			}

			$this->db->update('procedures', $data, ['company_id' => $this->company, 'id' => $id]);
			$dataLog = [
				'directory_id' 	=> $id,
				'old_status'	=> $thisData->status,
				'new_status' 	=> $data['status'],
				'note' 			=> 'Update data procedure',
				'doc_type' 		=> 'Procedure',
			];

			$this->_update_history($dataLog);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Can\'t process this data. Please try again.',
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Data Procedure successfully processed for review..',
			);
		}
		echo json_encode($Return);
	}

	function cancel_review($id)
	{
		$this->db->trans_begin();
		if (($id)) {
			$thisData = $this->db->get_where('procedures', ['id' => $id])->row();
			$data['modified_by'] = $this->auth->user_id();
			$data['modified_at'] = date('Y-m-d H:i:s');
			$data['status'] = 'DFT';
			$this->db->update('procedures', $data, ['company_id' => $this->company, 'id' => $id]);
			$dataLog = [
				'directory_id' 			=> $id,
				'old_status' 	=> $thisData->status,
				'new_status' 	=> $data['status'],
				'doc_type' 		=> 'Procedure',
				'note'			=> 'Cancel review data procdedure'
			];

			$this->_update_history($dataLog);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Can\'t cancle this data. Please try again.',
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Data Procedure successfully canceled for review..',
			);
		}
		echo json_encode($Return);
	}

	function delete_flow($id)
	{
		$this->db->trans_begin();
		if (($id)) {
			$data['deleted_by'] = $this->auth->user_id();
			$data['deleted_at'] = date('Y-m-d H:i:s');
			$data['status'] = '0';
			$this->db->update('procedure_details', $data, ['id' => $id]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Data Procedure failed to save. Please try again.',
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Data Procedure successfully saved..',
			);
		}
		echo json_encode($Return);
	}

	function delete_img($id, $dataImg)
	{
		$this->db->trans_begin();
		if (($id)) {
			$data['modified_by'] = $this->auth->user_id();
			$data['modified_at'] = date('Y-m-d H:i:s');
			$data["$dataImg"] = null;
			$this->db->update('procedures', $data, ['id' => $id]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Failed to delete image.. Please try again.',
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Successfully deleted image..',
			);
		}

		echo json_encode($Return);
	}

	public function upload_images()
	{
		$this->load->library('upload');
		$dataInfo = array();
		$files = $_FILES;

		if (!is_dir('./directory/FLOW_IMG/' . $this->company . '/')) {
			mkdir('./directory/FLOW_IMG/' . $this->company . '/', 0755, TRUE);
			chmod('./directory/FLOW_IMG/' . $this->company . '/', 0755);  // octal; correct value of mode
			chown('./directory/FLOW_IMG/' . $this->company . '/', 'www-data');
		}

		$cpt = count($_FILES['img_flow']['name']);

		for ($i = 0; $i < $cpt; $i++) {
			$_FILES['img_flow']['name'] 	= $files['img_flow']['name'][$i];
			$_FILES['img_flow']['type'] 	= $files['img_flow']['type'][$i];
			$_FILES['img_flow']['tmp_name'] = $files['img_flow']['tmp_name'][$i];
			$_FILES['img_flow']['error'] 	= $files['img_flow']['error'][$i];
			$_FILES['img_flow']['size'] 	= $files['img_flow']['size'][$i];

			if ($files['img_flow']['name'][$i]) {
				$this->upload->initialize($this->set_upload_options());
				$this->upload->do_upload('img_flow');
				$dataInfo[] = $this->upload->data();
				if ($this->upload->display_errors()) {
					return [
						'error' => 1,
						'error_msg' => $this->upload->display_errors(),
					];
					return false;
				}
			} else {
				$dataInfo[$i]['file_name'] = $files['img_flow']['name'][$i];
			}
		}

		if ($_FILES['flow_file']['name']) {
			$this->upload->initialize($this->set_upload_file_options());
			$this->upload->do_upload('flow_file');
			$fileInfo = $this->upload->data();
			if ($this->upload->display_errors()) {
				return [
					'error' => 1,
					'error_msg' => $this->upload->display_errors(),
				];
				return false;
			}
		} else {
			$fileInfo['file_name'] = $_FILES['flow_file']['name'];
		}

		return array(
			'image1' 		=> $dataInfo[0]['file_name'],
			'image2' 		=> $dataInfo[1]['file_name'],
			'image3' 		=> $dataInfo[2]['file_name'],
			'flow_file' 	=> $fileInfo['file_name'],
		);
	}

	private function set_upload_options()
	{
		//upload an image options
		$config = array();
		$config['upload_path'] 	 = './directory/FLOW_IMG/' . $this->company . '/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['max_size']      = 5120; // 5mb;
		$config['overwrite']     = TRUE;
		$config['encrypt_name']  = TRUE;

		return $config;
	}

	private function set_upload_file_options()
	{
		if (!is_dir('./directory/FLOW_FILE/' . $this->company . '/')) {
			mkdir('./directory/FLOW_FILE/' . $this->company . '/', 0755, TRUE);
			chmod('./directory/FLOW_FILE/' . $this->company . '/', 0755);  // octal; correct value of mode
			chown('./directory/FLOW_FILE/' . $this->company . '/', 'www-data');
		}

		//upload an image options
		$config = array();
		$config['upload_path'] 	 = './directory/FLOW_FILE/' . $this->company . '/';
		$config['allowed_types'] = 'pdf';
		$config['max_size']      = 5120; // 5mb;
		$config['overwrite']     = TRUE;
		$config['encrypt_name']  = TRUE;

		return $config;
	}

	public function view_form($id = null)
	{
		if ($id) {
			$file 		= $this->db->get_where('dir_forms', ['id' => $id])->row();
			// $dir_name 	= $this->db->get_where('dir_form', ['id' => $file->parent_id])->row()->name;
			$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_directory_log', ['directory_id' => $id])->result();
			// $this->template->set('dir_name', $dir_name);
			$this->template->set('sts', $this->sts);
			$this->template->set('file', $file);
			$this->template->set('type', 'form');
			$this->template->set('history', $history);
			$this->template->render('show');
		} else {
			echo "~ Not data available ~";
		}
	}

	public function upload_form($id = null)
	{
		$users 		= $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
		$jabatan 	= $this->db->get('positions')->result();

		$this->template->set([
			'jabatan' 		=> $jabatan,
			'procedure_id' 	=> $id,
			'users' 		=> $users,
			'type' 			=> "form",
		]);
		$this->template->render('upload_file_form');
	}

	public function edit_form($id = null)
	{
		$users 		= $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
		$jabatan 	= $this->db->get('positions')->result();
		$data = $this->db->get_where('dir_forms', ['id' => $id])->row();


		$this->template->set([
			'data' 			=> $data,
			'jabatan' 		=> $jabatan,
			'procedure_id' 	=> $data->procedure_id,
			'users' 		=> $users,
			'type' 			=> "form",
		]);
		$this->template->render('upload_file_form');
	}

	public function delete_form($id = null)
	{
		if ($id) {
			$this->db->trans_begin();
			$data = [
				'status' => 'DEL',
				'deleted_by' => $this->auth->user_id(),
				'deleted_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('dir_forms', $data, ['id' => $id]);
			$file_name = $this->db->get_where('dir_forms', ['id' => $id])->row()->file_name;
			$this->_delete_file('FORMS', $file_name);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return = [
					'status' => '0',
					'msg' => 'Data failed to delete, pelase try again.'
				];
			} else {
				$this->db->trans_commit();
				$Return = [
					'status' => '1',
					'msg' => 'Data successfull deleted.'
				];
			}
		} else {
			$Return = [
				'status' => '0',
				'msg' => 'Data not valid'
			];
		}

		echo json_encode($Return);
	}

	public function saveForm()
	{
		$data = $this->input->post('forms');
		if ($data) {
			$id 					= (!$data['id']) ? uniqid(date('m')) : $data['id'];
			$data['id']	    		= $id;
			$data['name']	    	= $data['description'];
			$data['company_id']		= $this->company;
			$check 					= $this->db->get_where('dir_forms', ['id' => $id])->num_rows();
			$note 					= isset($data['note']) ? $data['note'] : null;
			$data['status']			= isset($data['status']) ? $data['status'] : 'OPN';
			unset($data['note']);
			unset($data['type']);

			if (isset($_FILES['forms_image'])) {
				if (!is_dir("./directory/FORMS/$this->company/")) {
					mkdir("./directory/FORMS/$this->company/", 0755, TRUE);
					chmod("./directory/FORMS/$this->company/", 0755);  // octal; correct value of mode
					chown("./directory/FORMS/$this->company/", 'www-data');
				}
				$config['upload_path'] 		= "./directory/FORMS/$this->company"; //path folder
				$config['allowed_types'] 	= 'pdf'; //type yang dapat diakses bisa anda sesuaikan
				$config['encrypt_name'] 	= true; //Enkripsi nama yang terupload
				// $config['file_name'] 		= $new_name;

				$this->upload->initialize($config);
				if ($this->upload->do_upload('forms_image')) :
					$file 					= $this->upload->data();
					$data['size']  			= $file['file_size'];
					$data['ext']     		= $file['file_ext'];
					$data['file_name']  	= $file['file_name'];
					// $data['flag_type']		= 'FILE';
					// $dist 					= isset($data['distribute_id']) ? implode(",", $data['distribute_id']) : null;
					// $data['distribute_id']	= $dist;
					$old_file 				= isset($data['old_file']) ? $data['old_file'] : null;
					unset($data['old_file']);

					if ($old_file != null) {
						if (file_exists("./directory/FORMS/$this->company/" . $old_file)) {
							unlink("./directory/FORMS/$this->company/" . $old_file);
						}
					};

				else :
					$error_msg = $this->upload->display_errors();
					$this->db->trans_rollback();
					$Return = [
						'status' => 0,
						'msg'	 => $error_msg . ' File Form gagal diupload, silahkan coba lagi.'
					];
					echo json_encode($Return);
					return false;
				endif;
			}

			if (intval($check) == '0') {
				$data['created_by']		= $this->auth->user_id();
				$data['created_at']		= date('Y-m-d H:i:s');
				$data['note']			= 'First Upload File';
				$this->db->insert('dir_forms', $data);
			} else {
				$data['modified_by']	= $this->auth->user_id();
				$data['modified_at']	= date('Y-m-d H:i:s');
				$data['note']			= 'Re-upload File';
				$this->db->update('dir_forms', $data, ['id' => $id]);
			}

			$dataLog = [
				'directory_id' 	=> $id,
				'new_status' 	=> $data['status'],
				'doc_type' 		=> 'Form',
				'note'			=> 'Upload file'
			];

			$this->_update_history($dataLog);
		}

		if ($this->db->trans_status() === 'FALSE') {
			$this->db->trans_rollback();
			$Return = [
				'status' => 0,
				'msg'	 => 'File Form gagal diupload, silahkan coba lagi.'
			];
		} else {
			$this->db->trans_commit();
			$Return = [
				'status' => 1,
				'msg'	 => 'File Form berhasil di upload. Terima kasih'
			];
		}

		echo json_encode($Return);
	}

	public function loadDataForm($procedure_id = null)
	{
		$getForms	= $this->db->get_where('dir_forms', ['procedure_id' => $procedure_id, 'status !=' => 'DEL'])->result();
		$this->template->set('getForms', $getForms);
		$this->template->render('data-forms');
	}

	private function _delete_file($dir = null, $file_name = null)
	{
		if ($dir && $file_name) {
			if (file_exists("./directory/$dir/$this->company/" . $file_name)) {
				unlink("./directory/$dir/$this->company/" . $file_name);
			}
		}
	}

	/* upload ik */

	public function view_guide($id = null)
	{
		if ($id) {
			$file 		= $this->db->get_where('dir_guides', ['id' => $id])->row();
			// $dir_name 	= $this->db->get_where('dir_form', ['id' => $file->parent_id])->row()->name;
			$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_directory_log', ['directory_id' => $id])->result();
			// $this->template->set('dir_name', $dir_name);
			$this->template->set('sts', $this->sts);
			$this->template->set('file', $file);
			$this->template->set('type', 'guide');
			$this->template->set('history', $history);
			$this->template->render('show');
		} else {
			echo "~ Not data available ~";
		}
	}

	public function upload_guide($id = null)
	{
		$users 		= $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
		$jabatan 	= $this->db->get('positions')->result();

		$this->template->set([
			'jabatan' 		=> $jabatan,
			'procedure_id' 	=> $id,
			'users' 		=> $users,
			'type' 			=> "guide",
		]);
		$this->template->render('upload_file_guide');
	}

	public function edit_guide($id = null)
	{

		$users 		= $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
		$jabatan 	= $this->db->get('positions')->result();
		$data = $this->db->get_where('dir_guides', ['id' => $id])->row();


		$this->template->set([
			'data' 			=> $data,
			'jabatan' 		=> $jabatan,
			'procedure_id' 	=> $data->procedure_id,
			'users' 		=> $users,
			'type' 			=> "guide",
		]);
		$this->template->render('upload_file_guide');
	}

	public function delete_guide($id = null)
	{
		if ($id) {
			$this->db->trans_begin();
			$data = [
				'status' => 'DEL',
				'deleted_by' => $this->auth->user_id(),
				'deleted_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('dir_guides', $data, ['id' => $id]);
			$file_name = $this->db->get_where('dir_guides', ['id' => $id])->row()->file_name;
			$this->_delete_file('GUIDES', $file_name);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return = [
					'status' => '0',
					'msg' => 'Data failed to delete, pelase try again.'
				];
			} else {
				$this->db->trans_commit();
				$Return = [
					'status' => '1',
					'msg' => 'Data successfull deleted.'
				];
			}
		} else {
			$Return = [
				'status' => '0',
				'msg' => 'Data not valid'
			];
		}

		echo json_encode($Return);
	}

	public function loadDataGuide($procedure_id = null)
	{
		$getGuides	= $this->db->get_where('dir_guides', ['procedure_id' => $procedure_id, 'status !=' => 'DEL'])->result();

		$this->template->set('getGuides', $getGuides);
		$this->template->render('data-guides');
	}

	public function saveGuide()
	{
		$data = $this->input->post('forms');

		if ($_FILES['forms_image']) {
			if (!is_dir("./directory/GUIDES/$this->company")) {
				mkdir("./directory/GUIDES/$this->company", 0755, TRUE);
				chmod("./directory/GUIDES/$this->company", 0755);  // octal; correct value of mode
				chown("./directory/GUIDES/$this->company", 'www-data');
			}
			$config['upload_path'] 		= "./directory/GUIDES/$this->company"; //path folder
			$config['allowed_types'] 	= 'pdf'; //type yang dapat diakses bisa anda sesuaikan
			$config['encrypt_name'] 	= true; //Enkripsi nama yang terupload
			// $config['file_name'] 		= $new_name;
			$id 						= (!$data['id']) ? uniqid(date('m')) : $data['id'];


			$this->upload->initialize($config);
			if ($this->upload->do_upload('forms_image')) :
				$file 		= $this->upload->data();
				$file_name  = $file['file_name'];
				$size  		= $file['file_size'];
				$ext     	= $file['file_ext'];

				$data['id']	    		= $id;
				$data['name']	    	= $data['description'];
				$data['file_name']		= $file_name;
				$data['size']			= $size;
				$data['ext']			= $ext;
				$data['company_id']		= $this->company;
				// $data['flag_type']		= 'FILE';
				$dist 					= isset($data['distribute_id']) ? implode(",", $data['distribute_id']) : null;
				$data['distribute_id']	= $dist;
				$old_file 				= isset($data['old_file']) ? $data['old_file'] : null;
				unset($data['old_file']);

				if ($old_file != null) {
					if (file_exists("./directory/GUIDES/$this->company/" . $old_file)) {
						unlink("./directory/GUIDES/$this->company/" . $old_file);
					}
				}

				$check = $this->db->get_where('dir_guides', ['id' => $id])->num_rows();
				$note = isset($data['note']) ? $data['note'] : null;
				$data['status']			= isset($data['status']) ? $data['status'] : 'OPN';
				unset($data['note']);
				unset($data['type']);
				if (intval($check) == '0') {
					$data['created_by']		= $this->auth->user_id();
					$data['created_at']		= date('Y-m-d H:i:s');
					$data['note']			= 'First Upload File';
					$this->db->insert('dir_guides', $data);
				} else {
					$data['modified_by']	= $this->auth->user_id();
					$data['modified_at']	= date('Y-m-d H:i:s');
					$data['note']			= 'Re-upload File';
					$this->db->update('dir_guides', $data, ['id' => $id]);
				}

				$dataLog = [
					'directory_id' 	=> $id,
					'new_status' 	=> $data['status'],
					'doc_type' 		=> 'IK',
					'note'			=> 'Upload file'
				];
				$this->_update_history($dataLog);

				if (isset($data['distribute_id'])) {
					foreach ($this->input->post('forms')['distribute_id'] as $key => $value) {
						$cek = $this->db->where(['id_jabatan' => $value, 'id_file' => $data['id']])->get('distribusi')->row();
						$arr_dist = [
							'id_file' => $this->input->post('id'),
							'id_jabatan' => $value,
							'created_by' => $this->auth->user_id(),
							'created_at' => date('Y-m-d H:i:s'),
						];

						if ($cek) {
							$this->db->update('distribusi', $arr_dist, ['id' => $cek->id]);
						} else if (!$cek) {
							$this->db->insert('distribusi', $arr_dist);
						} else {
							$this->db->delete('distribusi', ['id' => $cek->id]);
						}
					}
				};

			else :
				$error_msg = $this->upload->display_errors();
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'	 => $error_msg
				];
				echo json_encode($Return);
				return false;
			endif;
			if ($this->db->trans_status() === 'FALSE') {
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'	 => 'File IK gagal diupload, silahkan coba lagi.'
				];
				echo json_encode($Return);
				return false;
			} else {
				$this->db->trans_commit();
				$Return = [
					'status' => 1,
					'msg'	 => 'File IK berhasil di upload. Terima kasih'
				];
				echo json_encode($Return);
			}
		} else {
			$Return = [
				'status' => 0,
				'msg'	 => 'No file or data to upload document..'
			];
			echo json_encode($Return);
		}
	}

	/* UPLOAD RECORDS */

	public function view_record($id = null)
	{

		if ($id) {
			$file 		= $this->db->get_where('dir_records', ['id' => $id])->row();
			// $dir_name 	= $this->db->get_where('dir_form', ['id' => $file->parent_id])->row()->name;
			$history	= $this->db->order_by('updated_at', 'ASC')->get_where('view_directory_log', ['directory_id' => $id])->result();
			// $this->template->set('dir_name', $dir_name);

			$this->template->set('sts', $this->sts);
			$this->template->set('file', $file);
			$this->template->set('type', 'record');
			$this->template->set('history', $history);
			$this->template->render('show');
		} else {
			echo "~ Not data available ~";
		}
	}

	public function upload_record($id = null, $parent_id = null)
	{
		$users 		= $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
		$jabatan 	= $this->db->get('positions')->result();

		$this->template->set([
			'jabatan' 		=> $jabatan,
			'procedure_id' 	=> $id,
			'parent_id' 	=> $parent_id,
			'users' 		=> $users,
			'type' 			=> "record",
		]);
		$this->template->render('upload_file_record');
	}

	public function edit_record($id = null)
	{

		$users 		= $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
		$jabatan 	= $this->db->get('positions')->result();
		$data = $this->db->get_where('dir_records', ['id' => $id])->row();


		$this->template->set([
			'data' 			=> $data,
			'jabatan' 		=> $jabatan,
			'procedure_id' 	=> $data->procedure_id,
			'users' 		=> $users,
			'type' 			=> "record",
		]);
		$this->template->render('upload_file_record');
	}

	public function delete_record($id = null)
	{
		if ($id) {
			$this->db->trans_begin();
			$data = [
				'status' => 'DEL',
				'deleted_by' => $this->auth->user_id(),
				'deleted_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('dir_records', $data, ['id' => $id]);
			$file_name = $this->db->get_where('dir_records', ['id' => $id])->row()->file_name;
			$this->_delete_file('RECORDS', $file_name);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return = [
					'status' => '0',
					'msg' => 'Data failed to delete, pelase try again.'
				];
			} else {
				$this->db->trans_commit();
				$Return = [
					'status' => '1',
					'msg' => 'Data successfull deleted.'
				];
			}
		} else {
			$Return = [
				'status' => '0',
				'msg' => 'Data not valid'
			];
		}

		echo json_encode($Return);
	}

	/* FOLDER RECORD */

	public function saveFolder()
	{
		$Data 				= $this->input->post();
		$Data['id']			= $Data['folder_id'];
		$Data['name']		= $Data['folder_name'];
		$Data['parent_id']	= ($Data['parent_id']) ?: null;
		$Data['company_id'] = $this->company;
		$pro_id 			= $Data['procedure_id'];

		unset($Data['folder_id']);
		unset($Data['folder_name']);

		$this->db->trans_begin();
		if ($Data) {
			if (isset($Data['id']) && $Data['id']) {
				$Data['modified_by'] = $this->auth->user_id();
				$Data['modified_at'] = date('Y-m-d H:i:s');
				$this->db->update('dir_records', $Data, ['id' => $Data['id']]);
			} else {
				$Data['id'] 		= uniqid(date('m'));
				$Data['created_by'] = $this->auth->user_id();
				$Data['created_at'] = date('Y-m-d H:i:s');
				$Data['status'] 	= 'PUB';
				$this->db->insert('dir_records', $Data);
			}
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Data Procedure failed to save. Please try again.',
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Data Procedure successfully saved..',
				'id'			=> $pro_id,
			);
		}

		echo json_encode($Return);
	}

	public function records_folder($folder = null, $procedure_id = null)
	{
		if ($folder != null) {
			$getRecords = $this->db->get_where('dir_records', ['procedure_id' => $procedure_id, 'parent_id' => $folder, 'status !=' => 'DEL'])->result();
			// Handle for null id folder!!
		}

		$this->template->set([
			'getRecords' => $getRecords,
			'parent_id' 	=> $folder,
			'EOF' 		 => false,
		]);

		$this->template->render('data-records');
	}

	public function up_folder($id = null, $procedure_id = null)
	{;
		$parent_id = "";
		$EOF = true;
		if ($id != 'null') {
			$parent_id = $this->db->get_where('dir_records', ['id' => $id])->row()->parent_id;
			if ($parent_id) {
				$getRecords = $this->db->get_where('dir_records', ['procedure_id' => $procedure_id, 'parent_id' => $parent_id, 'status !=' => 'DEL'])->result();
				$EOF = false;
			} else {
				$getRecords = $this->db->get_where('dir_records', ['procedure_id' => $procedure_id, 'status !=' => 'DEL', 'flag_type' => 'FOLDER', 'parent_id' => null])->result();
				$EOF = true;
			}
		} else {
			$getRecords = $this->db->get_where('dir_records', ['procedure_id' => $procedure_id, 'status !=' => 'DEL', 'flag_type' => 'FOLDER'])->result();
			$EOF = true;
		}

		$this->template->set([
			'getRecords' => $getRecords,
			'parent_id' => $parent_id,
			'EOF'		=> $EOF,
		]);

		$this->template->render('data-records');
		// Handle for null id folder!!
	}

	public function refresh($id = '', $procedure_id = null)
	{
		$EOF = true;
		if ($id != 'null') {
			$getRecords = $this->db->get_where('dir_records', ['procedure_id' => $procedure_id, 'parent_id' => $id, 'status !=' => 'DEL'])->result();
			$EOF = false;
		} else {
			$getRecords = $this->db->get_where('dir_records', ['procedure_id' => $procedure_id, 'status !=' => 'DEL', 'flag_type' => 'FOLDER', 'parent_id' => null])->result();
			$EOF = true;
		}

		$this->template->set([
			'getRecords' 	=> $getRecords,
			'parent_id' 	=> ($id == 'null') ? '' : $id,
			'EOF'			=> $EOF,
		]);

		$this->template->render('data-records');
	}

	/* PRINTOUT */
	public function printOut($id = null)
	{
		$mpdf = new Mpdf([
			'margin_top' => 55,
			'margin_bottom' => 15,
		]);
		$mpdf->showImageErrors = true;
		$mpdf->curlAllowUnsafeSslRequests = true;
		$procedure           = $this->db->get_where('view_procedures', ['id' => $id])->row();
		$flowDetail          = $this->db->get_where('procedure_details', ['procedure_id' => $id, 'status' => '1'])->result();
		$getForms            = $this->db->get_where('dir_forms', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
		$getGuides           = $this->db->get_where('dir_guides', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
		$users               = $this->db->get_where('view_users', ['company_id' => $this->company])->result();
		$jabatan             = $this->db->get('positions')->result();
		$ArrUsr              = $ArrJab = $ArrForms = $ArrGuides = [];
		$procedure_bilingual = $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
		$company = $this->Comp->find($this->company);

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
		$this->db->where("company_id", $this->company);
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

		$allProcedure 		= $this->db->get_where('view_procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();

		$Data = [
			'procedure'           => $procedure,
			'company'             => $company,
			'detail'              => $flowDetail,
			'ArrUsr'              => $ArrUsr,
			'ArrJab'              => $ArrJab,
			'ArrForms'            => $ArrForms,
			'ArrGuides'           => $ArrGuides,
			'Data'                => $Data,
			'ArrData'             => $ArrData,
			'ArrStd'              => $ArrStd,
			'allProcedure'        => $allProcedure,
			'procedure_bilingual' => $procedure_bilingual,
		];
		$header = $this->getheader($Data);
		$mpdf->SetHTMLHeader($header);

		$mpdf->AddPage('P', '', '', '', '', 7, 7, 55, 7, 7, '', '', '');
		// $mpdf->AddPage();
		$this->template->set($Data);
		$data = $this->template->load_view('printout');
		$mpdf->WriteHTML($data);
		$mpdf->Output();
	}

	public function getHeader($Data)
	{
		return '<div>
		<table class="table-data" cellpadding="2" cellspacing="0" style="font-size: 10pt;">
				<tr>
				<td rowspan="5" width="100" class="text-right" style="vertical-align: middle;border-right:0px">
					<img width="80" src="' . base_url($Data['company']->path_logo . $Data['company']->id_perusahaan . '/' . $Data['company']->logo) . '" alt="">
				</td>
				<td rowspan="5" width="250"  class="text-center" style="vertical-align: middle;border-left:0px">
					<h2>' . $Data['company']->nm_perusahaan . '</h2>
				</td>
				<td width="150">Dept</td>
				<td width="">' . (isset($Data['procedure']->departement_id) ? $Data['procedure']->departement_name : '') . '</td>
				</tr>
				<tr>
				<td>No. Dok</td>
				<td>' . (($Data['procedure']->nomor) ?: '~') . '</td>
				</tr>
				<tr>
				<td>Revisi</td>
				<td>' . (($Data['procedure']->revision) ?: '~') . '</td>
				</tr>
				<tr>
				<td>Tgl. Terbit</td>
				<td>' . (($Data['procedure']->published_at) ?: '~') . '</td>
				</tr>
				<tr>
				<td>Halaman</td>
				<td>{PAGENO} dari {nbpg}</td>
				</tr>
				<tr>
				<td colspan="4" class="text-center" style="vertical-align: middle;">
					<h2>' . strtoupper($Data['procedure']->name) . '</h2>
					<h3 style="color: #0088ffff;">(' . (isset($Data['procedure_bilingual']->name) ? strtoupper($Data['procedure_bilingual']->name) : '') . ')</h3>
				</td>
				</tr>
			</table></div>';
	}
}
