<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Guides extends Admin_Controller
{
	/*
 * @author Hikmat A.R
 * @copyright Copyright (c) 2023, Hikmat A.R
 */

	public function __construct()
	{
		parent::__construct();
		// $this->load->model('manage_documents/manage_documents_model', 'DOCS');
		$this->load->library('upload');
		$this->template->set('title', 'MASTER IK');
	}

	public function index()
	{

		if (!isset($_GET['d']) || $_GET['d'] == '') {
			redirect('guides/?d=0');
		}

		$ArrDetail 				= '';
		$selected 				= $details = $breadcumb = $sub = $methode = '';
		$details_data 			= 0;
		$dirs 	  				= $this->db->get_where('guides', ['status' => '1', 'company_id' => $this->company])->result();
		$references 	 		= $this->db->get_where('view_standards', ['status' => 'PUB'])->result();

		if ((isset($_GET['d']) && ($_GET['d'])) && (isset($_GET['sub']) && ($_GET['sub'])) && (isset($_GET['edit']) && ($_GET['edit']))) {
			$id = $_GET['edit'];
			redirect("guides/edit_file/$id");
		}

		if (isset($_GET['d']) && ($_GET['d'])) {
			$selected 			= $_GET['d'];
			$details 	  		= $this->db->get_where('view_guides_details', ['guide_id' => $selected, 'status' => '1'])->result();
			$guides				= $this->db->get_where('guides', ['id' => $selected, 'company_id' => $this->company, 'status' => '1'])->row();
			$breadcumb 			= [($guides) ? $guides->name : ''];
		}



		if ((isset($_GET['d']) && ($_GET['d'])) && (isset($_GET['sub']) && ($_GET['sub']))) {
			$selected 			= $_GET['d'];
			$sub 				= $_GET['sub'];
			$details 	  		= $this->db->get_where('view_guides_details', ['id' => $sub, 'status' => '1'])->row();
			$details_data 	  	= $this->db->get_where('view_guides_detail_data', ['guide_detail_id' => $sub, 'status' => '1'])->result();
			$breadcumb 			= ($details) ? ["<a href='" . base_url($this->uri->segment(1) . '/?d=' . $selected) . "'>$details->guide_name</a>", $details->guide_detail_name] : '';
			$methode 			= ['INS' => 'Insitu', 'LAB' => 'Inlab'];
		}



		$ArrRef = [];
		foreach ($references as $ref) {
			$ArrRef[$ref->id] = $ref->alias;
		}

		// Check if current user has same position as prepare_by users (for edit/delete/process permissions)
		$current_user_position = $this->db->get_where('positions', ['assign_user' => $this->auth->user_id()])->row();
		$current_position_id = $current_user_position ? $current_user_position->id : null;

		$this->template->set([
			'data' 				=> $dirs,
			'details' 			=> $details,
			'selected'  		=> $selected,
			'details_data'  	=> $details_data,
			'breadcumb'  		=> $breadcumb,
			'sub'  				=> $sub,
			'methode'  			=> $methode,
			'ArrRef'  			=> $ArrRef,
			'current_user_id'	=> $this->auth->user_id(),
			'current_position_id' => $current_position_id,
		]);

		$this->template->render('index');
	}

	public function save_dir()
	{
		$post 			= $this->input->post();

		if (!isset($post['id'])) {
			$name 			= $post['name'];
			$is_exist 		= $this->db->get_where('guides', ['name' => $name])->num_rows();

			if ($is_exist > 0) {
				$return = [
					'status' => 0,
					'msg' => 'Directory Name is already exist',
				];
				echo json_encode($return);
				return false;
			}

			$this->db->trans_begin();
			$data = [
				'name' => strtoupper($name),
				'company_id' => $this->company,
				'created_by' => $this->auth->user_id(),
				'created_at' => date('Y-m-d H:i:s'),
			];

			$this->db->insert('guides', $data);
		} else {
			$name 			= $post['name'];
			$data = [
				'name' => strtoupper($name),
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('guides', $data, ['id' => $post['id']]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Directory failed created. Query Error'
			);
		} else if ($this->db->affected_rows() > 0) {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Directory successfull created'
			);
		} else {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Directory failed created. Data not be inserted'
			);
		}
		echo json_encode($Return);
	}

	public function edit_dir($id)
	{
		if ($id) {
			$data = $this->db->get_where('guides', ['id' => $id])->row();
			echo json_encode(['data' => $data]);
		}
	}

	public function delete_dir()
	{
		$id 		 = $this->input->post('id');
		$check_child = $this->db->get_where('guide_details', ['guide_id' => $id])->num_rows();

		$this->db->trans_begin();

		if ($check_child > 0) {
			$data_detail = $this->db->get_where('guide_details', ['guide_id' => $id,], ['guide_id' => $id])->result();

			$this->db->update('guide_details', ['status' => '0'], ['guide_id' => $id]);
			foreach ($data_detail as $dtl) {
				$detail_data = $this->db->get_where('guide_detail_data', ['guide_detail_id' => $dtl->id])->result();

				if (count($detail_data) > 0) {
					$this->db->update('guide_detail_data', ['status' => '0'], ['guide_detail_id' => $dtl->id]);
				}
			}
		}

		$this->db->update('guides', ['status' => '0'], ['id' => $id]);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Delete directory failed. Error!'
			);
		} else if ($this->db->affected_rows() > 0) {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Delete directory successfull'
			);
		} else {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> "Can't Deleted this directory. Data not valid"
			);
		}
		echo json_encode($Return);
	}


	/* SUB DIRECTORY */

	public function edit_sub_dir($id)
	{
		if ($id) {
			$data = $this->db->get_where('guide_details', ['id' => $id])->row();
			echo json_encode(['data' => $data]);
		}
	}

	public function save_sub_dir()
	{
		$post 			= $this->input->post();

		if (!isset($post['id'])) {
			$name 			= $post['name'];
			$guide_id 		= $post['guide_id'];
			$is_exist 		= $this->db->get_where('guide_details', ['name' => $name])->num_rows();

			if ($is_exist > 0) {
				$return = [
					'status' => 0,
					'msg' => 'Directory Name is already exist',
				];
				echo json_encode($return);
				return false;
			}

			$this->db->trans_begin();
			$data = [
				'name' 			=> ucfirst($name),
				'company_id' 	=> $this->company,
				'guide_id' 		=> $guide_id,
				'created_by' 	=> $this->auth->user_id(),
				'created_at' 	=> date('Y-m-d H:i:s'),
			];

			$this->db->insert('guide_details', $data);
		} else {
			$name	= $post['name'];
			$data 	= [
				'name' => strtoupper($name),
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('guide_details', $data, ['id' => $post['id']]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Directory failed created. Query Error'
			);
		} else if ($this->db->affected_rows() > 0) {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Directory successfull created'
			);
		} else {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Directory failed created. Data not be inserted'
			);
		}
		echo json_encode($Return);
	}


	public function delete_sub_dir()
	{
		$id 		 = $this->input->post('id');
		$check_child = $this->db->get_where('guide_detail_data', ['guide_detail_id' => $id])->result();

		$this->db->trans_begin();

		if (count($check_child) > 0) {
			$this->db->update('guide_detail_data', ['status' => '0'], ['guide_detail_id' => $id]);
		}

		$this->db->update('guide_details', ['status' => '0'], ['id' => $id]);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Delete directory failed. Error!'
			);
		} else if ($this->db->affected_rows() > 0) {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Delete directory successfull'
			);
		} else {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> "Can't Deleted this directory. Data not valid"
			);
		}
		echo json_encode($Return);
	}


	/* UPLOAD FILE */

	public function upload()
	{
		$this->template->render('form-upload');
	}

	public function new_file($guide_detail_id)
	{
		if ($guide_detail_id) {
			// $guide_detail_id = $this->input->post('dtl');
			$group_tools 	 = $this->db->get_where('tool_scopes')->result();
			$references 	 = $this->db->get_where('view_standards', ['status' => 'PUB'])->result();
			$detail 		 = $this->db->get_where('guide_details', ['id' => $guide_detail_id])->row();
			$users 			 = $this->db->get_where('users', ['status' => 'ACT'])->result();
			$this->template->set([
				'guide_detail_id' 	=> $guide_detail_id,
				'group_tools' 		=> $group_tools,
				'references' 		=> $references,
				'detail' 			=> $detail,
				'users' 			=> $users,
				'current_user_id'	=> $this->auth->user_id()
			]);

			$this->template->render('form');
		} else {
			echo "<h1>404 | Not Found!</h1>";
		}
	}

	private function getNumber($group_id)
	{
		$comp_code 		= 'STM';
		$group_tool 	= $this->db->get_where('tool_scopes', ['id' => $group_id])->row();
		$code_tool 		= $group_tool->code;

		$last_number 	= $this->db->select('MAX(RIGHT(number,3)) as number')->from('guide_detail_data')->where(['group_id' => $group_id])->get()->row();
		$counter 		= 1;
		if ($last_number->number) {
			$counter = $last_number->number + 1;
		}
		$new_number = $comp_code . "/IK-" . $code_tool . "/" . sprintf("%03d", $counter);
		return $new_number;
	}

	public function upload_document()
	{
		$post 					= $this->input->post();

		if ($post) {
			try {
				// Only take valid fields for guide_detail_data table
				$data = [
					'guide_detail_id'		=> isset($post['guide_detail_id']) ? $post['guide_detail_id'] : null,
					'doc_number'			=> isset($post['doc_number']) ? $post['doc_number'] : null,
					'doc_name'				=> isset($post['doc_name']) ? $post['doc_name'] : null,
					'issue_date'			=> (isset($post['issue_date']) && $post['issue_date']) ? date_format(date_create(str_replace("/", "-", $post['issue_date'])), 'Y-m-d') : null,
					'effective_date'		=> (isset($post['effective_date']) && $post['effective_date']) ? date_format(date_create(str_replace("/", "-", $post['effective_date'])), 'Y-m-d') : null,
					'doc_revision_number'	=> isset($post['doc_revision_number']) ? $post['doc_revision_number'] : null,
					'prepare_by'			=> isset($post['prepare_by']) ? $post['prepare_by'] : null,
					'pic_reviewer'			=> isset($post['pic_reviewer']) ? $post['pic_reviewer'] : null,
					'pic_approval'			=> isset($post['pic_approval']) ? $post['pic_approval'] : null,
					'company_id'			=> $this->company,
				];

				$id = isset($post['id']) ? $post['id'] : '';

				$this->db->trans_begin();
				$check = $this->db->get_where('guide_detail_data', ['id' => $id])->num_rows();

				if (intval($check) == '0') {
					$data['created_by']		= $this->auth->user_id();
					$data['created_at']		= date('Y-m-d H:i:s');
					$this->db->insert('guide_detail_data', $data);
					$newid = $this->db->order_by('id', 'desc')->get_where('guide_detail_data')->row()->id;
				} else {
					$data['modified_by']	= $this->auth->user_id();
					$data['modified_at']	= date('Y-m-d H:i:s');
					$newid = $id;
					$this->db->update('guide_detail_data', $data, ['id' => $id]);
				}

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$Return = [
						'status' => 0,
						'msg'	 => 'Failed upload document file. Please try again later.!'
					];
				} else {
					// Upload document file
					if (isset($_FILES['doc_file']['name']) && $_FILES['doc_file']['name']) {
						$DIR_COMP = $this->company;
						$DIR = 'IK';

						if (!is_dir("./directory/MASTER_GUIDES/$DIR_COMP/$DIR")) {
							mkdir("./directory/MASTER_GUIDES/$DIR_COMP/$DIR", 0755, TRUE);
						}

						$config['upload_path'] 		= "./directory/MASTER_GUIDES/$DIR_COMP/$DIR";
						$config['allowed_types'] 	= 'pdf|jpg|jpeg|png|doc|docx|xls|xlsx';
						$config['encrypt_name'] 	= true;
						$config['max_size'] 		= 10240; // 10MB
						$this->load->library('upload', $config);
						$this->upload->initialize($config);

						if ($this->upload->do_upload('doc_file')) {
							$file = $this->upload->data();

							// Check if existing document exists (update mode)
							$existing_doc = $this->db->get_where('guide_documents', [
								'guide_detail_data_id' => $newid,
								'file_type' => 1,
								'status' => '1'
							])->row();

							if ($existing_doc) {
								// Delete old file
								$old_file_path = "./directory/MASTER_GUIDES/$DIR_COMP/$DIR/" . $existing_doc->file;
								if (file_exists($old_file_path)) {
									unlink($old_file_path);
								}
								// Update existing record
								$this->db->update('guide_documents', [
									'name'			=> $data['doc_name'],
									'ext'			=> $file['file_ext'],
									'file'			=> $file['file_name'],
									'modified_by'	=> $this->auth->user_id(),
									'modified_at'	=> date('Y-m-d H:i:s')
								], ['id' => $existing_doc->id]);
							} else {
								// Insert new record
								$doc_data = [
									'id'					=> $this->_getIDDoc($this->company, 1),
									'guide_detail_data_id'	=> $newid,
									'name'					=> $data['doc_name'],
									'ext'					=> $file['file_ext'],
									'file_type'				=> 1,
									'file'					=> $file['file_name'],
									'created_by'			=> $this->auth->user_id(),
									'created_at'			=> date('Y-m-d H:i:s')
								];
								$this->db->insert('guide_documents', $doc_data);
							}
						} else {
							$this->db->trans_rollback();
							echo json_encode(['status' => 0, 'msg' => $this->upload->display_errors('', '')]);
							return;
						}
					}

					$this->db->trans_commit();
					$Return = [
						'status' => 1,
						'msg'	 => 'Success upload document file...',
						'id'	 => $newid
					];
				}
				echo json_encode($Return);
			} catch (Exception $e) {
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'	 => $e->getMessage()
				];

				echo json_encode($Return);
				return false;
			}
		} else {
			$Return = [
				'status' => 0,
				'msg'	 => 'Data not valid!'
			];
			echo json_encode($Return);
		}
	}

	private function _getIDDoc($company, $doc)
	{
		$company 	= sprintf('%02d', $company);
		$count 		= 1;
		$y 			= date('y');
		$m 			= date('m');
		$max 		= $this->db->select('MAX(RIGHT(id,4)) as id')->get_where('guide_documents', ['SUBSTR(id,4,2)' => $company, 'SUBSTR(id,7,1)' => $doc, 'SUBSTR(id,8,2)' => $y, 'SUBSTR(id,10,2)' => $m])->row();

		if ($max->id > 0) {
			$count = $max->id + 1;
		}
		$counter 		= sprintf('%04d', $count);
		return "DOC$company-$doc$y$m-$counter";
	}

	private function _uploadFiles($id)
	{
		$this->_DocsUploads('ik_file', 'ik_name', 'IK', 1, $id);
		$this->_DocsUploads('cmc_file', 'cmc_name', 'CMC', 2, $id);
		$this->_DocsUploads('temp_file', 'temp_name', 'TEMPLATE', 3, $id);
		$this->_DocsUploads('ublk_file', 'ublk_name', 'UBLK', 4, $id);
		$this->_DocsUploads('sert_file', 'sert_name', 'FORMAT_SERTIFIKAT', 5, $id);
		$this->_DocsUploads('drift_file', 'drift_name', 'ANALISA_DRIFT', 6, $id);
		$this->_DocsUploads('sertcal_file', 'sertcal_name', 'SERTIFIKAT_KALIBRATOR', 7, $id);
		$this->_DocsUploads('cek_file', 'cek_name', 'CEK_ANTARA', 8, $id);
		$this->_DocsUploads('video_file', 'video_name', 'VIDEO', 9, $id);
	}

	private function _DocsUploads($FILE, $NAME, $DIR, $CODE, $id)
	{
		$post 	= $this->input->post();
			if (isset($_FILES[$FILE]['name']) && $_FILES[$FILE]['name']) {
				$data['id']				= $this->_getIDDoc($this->company, $CODE); // DOC01-12305-0001;
				$data['guide_detail_data_id'] = $id;
				$data['name'] 			= $post['documents'][$NAME];
				// $data['ext'] 			= end((explode(".", $_FILES[$FILE]['name'])));
				$data['ext'] 			= pathinfo($_FILES[$FILE]['name'], PATHINFO_EXTENSION);
				$data['file_type'] 		= $CODE;
				$data['created_by'] 	= $this->auth->user_id();
				$data['created_at'] 	= date('Y-m-d H:i:s');
				$DIR_COMP 				= $this->company;

				if (!is_dir("./directory/MASTER_GUIDES/$DIR_COMP/$DIR")) {
					mkdir("./directory/MASTER_GUIDES/$DIR_COMP/$DIR", 0755, TRUE);
					chmod("./directory/MASTER_GUIDES/$DIR_COMP/$DIR", 0755);  // octal; correct value of mode
					chown("./directory/MASTER_GUIDES/$DIR_COMP/$DIR", 'www-data');
				}

				$config['upload_path'] 		= "./directory/MASTER_GUIDES/$DIR_COMP/$DIR"; //path folder
				$config['allowed_types'] 	= '*'; //type yang dapat diakses bisa anda sesuaikan
				$config['encrypt_name'] 	= true; //Enkripsi nama yang terupload
				$config['max_size'] 		= 504990; //Enkripsi nama yang terupload
				$this->upload->initialize($config);
				$this->upload->do_upload($FILE);
				
				$file 						= $this->upload->data();
				$data['file']				= $file['file_name'];

				if ($this->upload->display_errors()) {
					$error_msg = $this->upload->display_errors();
					$this->db->trans_rollback();
					$Return = [
						'status' => 0,
						'msg'	 => $error_msg
					];
					echo json_encode($Return);
					return false;
				} else {
					$this->db->insert('guide_documents', $data);
					$error = $this->db->error();
					if ($this->db->trans_status() === FALSE) {
						$this->db->trans_rollback();
						$Return = [
							'status' => 0,
							'msg'	 => 'Failed upload document file. Please try again later.!' . $error['message']
						];

						if (file_exists("./directory/MASTER_GUIDES/$DIR_COMP/$DIR/" . $data['file'])) {
							unlink("./directory/MASTER_GUIDES/$DIR_COMP/$DIR/" . $data['file']);
						}
						echo json_encode($Return);
						return false;
					}
				}
			}
	}

	public function edit_file($id)
	{
		$data = [];
		if ($id) {
			$data 			= $this->db->get_where('guide_detail_data', ['id' => $id])->row();
			$detail 		= $this->db->get_where('guide_details', ['id' => $data->guide_detail_id])->row();
			$users 			= $this->db->get_where('users', ['status' => 'ACT'])->result();
			$DocIK 			= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '1', 'status' => '1'])->result();

			$this->template->set([
				'data' 				=> $data,
				'detail' 			=> $detail,
				'users' 			=> $users,
				'DocIK' 			=> $DocIK,
				'current_user_id'	=> $this->auth->user_id()
			]);

			$this->template->render('edit');
		} else {
			echo "Data not found";
		}
	}

	public function load_file($id)
	{
		$data = [];
		if ($id) {
			$data = $this->db->get_where('view_guides_detail_data', ['id' => $id])->row();

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

	public function view_file($id)
	{
		$data 			= $this->db->get_where('view_guides_detail_data', ['id' => $id])->row();
		$file 			= './directory/MASTER_GUIDES/' . $data->company_id . '/' . $data->document;
		$methode 		= ['INS' => 'Insitu', 'LAB' => 'Inlab'];
		$standards 		= $this->db->get_where('view_standards', ['status' => 'PUB'])->result();
		$ArrStd 		= [];
		foreach ($standards as $std) {
			$ArrStd[$std->id] = $std->alias;
		}
		$ArrRange = $data->range_measure ? json_decode($data->range_measure) : [];
		$ArrUncert = $data->uncertainty ? json_decode($data->uncertainty) : [];
		$ArrCombine = ($ArrRange && $ArrUncert) ? array_combine($ArrRange, $ArrUncert) : [];
		$ArrSubTools = $data->sub_tools ? json_decode($data->sub_tools) : [];

		$DocIK 			= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '1', 'status' => '1'])->result();
			$DocCMC 		= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '2', 'status' => '1'])->result();
			$DocTEMP 		= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '3', 'status' => '1'])->result();
			$DocUBLK 		= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '4', 'status' => '1'])->result();
			$DocSERT 		= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '5', 'status' => '1'])->result();
			$DocDRIFT 		= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '6', 'status' => '1'])->result();
			$DocSERTCAL 	= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '7', 'status' => '1'])->result();
			$DocCEK 		= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '8', 'status' => '1'])->result();
			$DocVID 		= $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '9', 'status' => '1'])->result();
			

		// Get user names for display
		$prepare_by_name = '-';
		$reviewer_name = '-';
		$approval_name = '-';
		if ($data->prepare_by) {
			$prep_user = $this->db->get_where('users', ['id_user' => $data->prepare_by])->row();
			if ($prep_user) $prepare_by_name = $prep_user->full_name;
		}
		if ($data->pic_reviewer) {
			$rev_user = $this->db->get_where('users', ['id_user' => $data->pic_reviewer])->row();
			if ($rev_user) $reviewer_name = $rev_user->full_name;
		}
		if ($data->pic_approval) {
			$apv_user = $this->db->get_where('users', ['id_user' => $data->pic_approval])->row();
			if ($apv_user) $approval_name = $apv_user->full_name;
		}

		$this->template->set([
			'data' 				=> $data,
			'DocIK' 			=> $DocIK,
			'prepare_by_name'	=> $prepare_by_name,
			'reviewer_name'		=> $reviewer_name,
			'approval_name'		=> $approval_name,
		]);

		$this->template->render('view-file');
	}

	public function view_file_page($id)
	{
		$data = $this->db->get_where('view_guides_detail_data', ['id' => $id])->row();
		if (!$data) {
			echo "Data not found";
			return;
		}

		$DocIK = $this->db->get_where('guide_documents', ['guide_detail_data_id' => $id, 'file_type' => '1', 'status' => '1'])->result();

		$prepare_by_name = '-';
		$reviewer_name = '-';
		$approval_name = '-';
		if ($data->prepare_by) {
			$prep_user = $this->db->get_where('users', ['id_user' => $data->prepare_by])->row();
			if ($prep_user) $prepare_by_name = $prep_user->full_name;
		}
		if ($data->pic_reviewer) {
			$rev_user = $this->db->get_where('users', ['id_user' => $data->pic_reviewer])->row();
			if ($rev_user) $reviewer_name = $rev_user->full_name;
		}
		if ($data->pic_approval) {
			$apv_user = $this->db->get_where('users', ['id_user' => $data->pic_approval])->row();
			if ($apv_user) $approval_name = $apv_user->full_name;
		}

		$this->template->set([
			'data' 				=> $data,
			'DocIK' 			=> $DocIK,
			'prepare_by_name'	=> $prepare_by_name,
			'reviewer_name'		=> $reviewer_name,
			'approval_name'		=> $approval_name,
		]);

		$this->template->render('view-file-page');
	}

	public function view_video($id)
	{
		if ($id) {
			$data = $this->db->get_where('guide_documents', ['id' => $id])->row();
			if ($data) {
				echo '
				<video controls controlsList="nodownload" class="w-100" oncontextmenu="return false" style="max-width:100%">
					<source src="' . base_url('/directory/MASTER_GUIDES/' . $this->company . '/VIDEO/' . $data->file) . '#t=5' . '" type="video/mp4" />
				</video>
				';
			}
		}
	}

	public function delete_document()
	{
		$id 		 = $this->input->post('id');
		if ($id) {
			$this->db->trans_begin();
			$this->db->update('guide_detail_data', ['status' => '0'], ['id' => $id]);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return		= array(
					'status'		=> 0,
					'msg'			=> 'Delete document failed. Error!'
				);
			} else if ($this->db->affected_rows() > 0) {
				$this->db->trans_commit();
				$Return		= array(
					'status'		=> 1,
					'msg'			=> 'Delete document successfull'
				);
			} else {
				$this->db->trans_rollback();
				$Return		= array(
					'status'		=> 0,
					'msg'			=> "Can't Deleted this directory. Data not valid"
				);
			}
			echo json_encode($Return);
		} else {
			$Return		= array(
				'status'		=> 0,
				'msg'			=> "Data not valid"
			);
			echo json_encode($Return);
		}
	}

	public function process_to_review()
	{
		$id = $this->input->post('id');
		if ($id) {
			$data = $this->db->get_where('guide_detail_data', ['id' => $id])->row();
			if (!$data) {
				echo json_encode(['status' => 0, 'msg' => 'Data not found.']);
				return;
			}
			if (!in_array($data->doc_status, ['DFT', 'RVI', 'COR'])) {
				echo json_encode(['status' => 0, 'msg' => 'Document cannot be processed to Review from current status.']);
				return;
			}
			$this->db->update('guide_detail_data', [
				'doc_status'  => 'REV',
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s')
			], ['id' => $id]);

			if ($this->db->affected_rows() > 0) {
				echo json_encode(['status' => 1, 'msg' => 'Document berhasil diproses ke Review.']);
			} else {
				echo json_encode(['status' => 0, 'msg' => 'Failed to process document.']);
			}
		} else {
			echo json_encode(['status' => 0, 'msg' => 'Data not valid.']);
		}
	}

	public function submit_review()
	{
		$id = $this->input->post('id');
		$action = $this->input->post('action');
		$reason = $this->input->post('reason');

		if (!$id || !$action) {
			echo json_encode(['status' => 0, 'msg' => 'Data not valid.']);
			return;
		}

		$data = $this->db->get_where('guide_detail_data', ['id' => $id])->row();
		if (!$data) {
			echo json_encode(['status' => 0, 'msg' => 'Data not found.']);
			return;
		}

		if ($data->doc_status != 'REV') {
			echo json_encode(['status' => 0, 'msg' => 'Document is not in Review status.']);
			return;
		}

		if ($action == 'approve') {
			$new_status = 'APV';
			$msg = 'Document berhasil diproses ke Approval.';
		} elseif ($action == 'correction') {
			$new_status = 'COR';
			$msg = 'Document dikembalikan untuk Correction.';
		} else {
			echo json_encode(['status' => 0, 'msg' => 'Invalid action.']);
			return;
		}

		$update_data = [
			'doc_status'  => $new_status,
			'modified_by' => $this->auth->user_id(),
			'modified_at' => date('Y-m-d H:i:s')
		];

		$this->db->update('guide_detail_data', $update_data, ['id' => $id]);

		if ($this->db->affected_rows() > 0) {
			echo json_encode(['status' => 1, 'msg' => $msg]);
		} else {
			echo json_encode(['status' => 0, 'msg' => 'Failed to process.']);
		}
	}

	public function request_revision()
	{
		$id = $this->input->post('id');
		$reason = $this->input->post('reason');

		if (!$id || !$reason) {
			echo json_encode(['status' => 0, 'msg' => 'Data not valid.']);
			return;
		}

		$data = $this->db->get_where('guide_detail_data', ['id' => $id])->row();
		if (!$data) {
			echo json_encode(['status' => 0, 'msg' => 'Data not found.']);
			return;
		}

		if ($data->doc_status != 'PUB') {
			echo json_encode(['status' => 0, 'msg' => 'Document is not in Published status.']);
			return;
		}

		$this->db->update('guide_detail_data', [
			'doc_status'  => 'RVI',
			'modified_by' => $this->auth->user_id(),
			'modified_at' => date('Y-m-d H:i:s')
		], ['id' => $id]);

		if ($this->db->affected_rows() > 0) {
			echo json_encode(['status' => 1, 'msg' => 'Document berhasil di-request untuk Revision.']);
		} else {
			echo json_encode(['status' => 0, 'msg' => 'Failed to process.']);
		}
	}

	public function submit_approval()
	{
		$id = $this->input->post('id');
		$action = $this->input->post('action');
		$note = $this->input->post('note');
		$published_date = $this->input->post('published_date');

		if (!$id || !$action) {
			echo json_encode(['status' => 0, 'msg' => 'Data not valid.']);
			return;
		}

		$data = $this->db->get_where('guide_detail_data', ['id' => $id])->row();
		if (!$data) {
			echo json_encode(['status' => 0, 'msg' => 'Data not found.']);
			return;
		}

		if ($data->doc_status != 'APV') {
			echo json_encode(['status' => 0, 'msg' => 'Document is not in Approval status.']);
			return;
		}

		if ($action == 'publish') {
			$new_status = 'PUB';
			$msg = 'Document berhasil dipublikasikan.';
			$update_data = [
				'doc_status'  => $new_status,
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s')
			];
			if ($published_date) {
				$update_data['effective_date'] = date_format(date_create(str_replace("/", "-", $published_date)), 'Y-m-d');
			}
		} elseif ($action == 'correction') {
			$new_status = 'COR';
			$msg = 'Document dikembalikan untuk Correction.';
			$update_data = [
				'doc_status'  => $new_status,
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s')
			];
		} else {
			echo json_encode(['status' => 0, 'msg' => 'Invalid action.']);
			return;
		}

		$this->db->update('guide_detail_data', $update_data, ['id' => $id]);

		if ($this->db->affected_rows() > 0) {
			echo json_encode(['status' => 1, 'msg' => $msg]);
		} else {
			echo json_encode(['status' => 0, 'msg' => 'Failed to process.']);
		}
	}

	public function delete_sub_document()
	{
		$id 		 = $this->input->post('id');
		if ($id) {
			$this->db->trans_begin();
			$this->db->update('guide_documents', ['status' => '0'], ['id' => $id]);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return		= array(
					'status'		=> 0,
					'msg'			=> 'Delete document failed. Error!'
				);
			} else if ($this->db->affected_rows() > 0) {
				$this->db->trans_commit();
				$Return		= array(
					'status'		=> 1,
					'msg'			=> 'Delete document successfull'
				);
			} else {
				$this->db->trans_rollback();
				$Return		= array(
					'status'		=> 0,
					'msg'			=> "Can't Delete this document. Data not valid"
				);
			}
			echo json_encode($Return);
		} else {
			$Return		= array(
				'status'		=> 0,
				'msg'			=> "Data not valid"
			);
			echo json_encode($Return);
		}
	}

	/* ===================== */
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
		$mpdf->Output($newfile, 'F');
		$mpdf->Output();
	}
}
