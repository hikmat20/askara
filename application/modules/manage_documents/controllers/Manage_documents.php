<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manage_documents extends Admin_Controller
{
	/*
 * @author Hikmat A.R
 * @copyright Copyright (c) 2022, Hikmat A.R
 */

	public function __construct()
	{
		parent::__construct();
		$this->load->model('manage_documents/manage_documents_model', 'DOCS');
		$this->load->library('upload');
		$this->template->page_icon('fa fa-file-alt');
		$this->MainData 	= $this->db->get_where('view_directories', ['parent_id' => '0'])->result();
		$this->sts = [
			'' => '<span class="label label-light-secondary label-pill label-inline mr-2 text-dark-50">!Null!</span>',
			'OPN' => '<span class="label label-light-primary label-pill label-inline mr-2">New</span>',
			'REV' => '<span class="label label-light-warning label-pill label-inline mr-2">Waiting Review</span>',
			'COR' => '<span class="label label-light-danger label-pill label-inline mr-2">Need Correction</span>',
			'APV' => '<span class="label label-light-info label-pill label-inline mr-2">Waiting Approval</span>',
			'PUB' => '<span class="label label-light-success label-pill label-inline mr-2">Published</span>',
		];
	}

	public function index()
	{
		//$this->template->set('sum_penacc', $sum_penacc);
		$this->template->render('index');
	}

	public function create()
	{
		$permission = $this->db->get_where('group_menus', ['group_id' => $this->group_id, 'company_id' => $this->company])->row();
		if (!$permission) {
			redirect('/');
			return FALSE;
		}
		$mainFolder = $this->db->get_where('view_directories', ['flag_type' => 'FOLDER', 'active' => 'Y', 'status !=' => 'DEL', 'parent_id' => '0', 'company_id' => $this->company])->result();
		$Data 		= $this->db->get_where('view_directories', ['flag_type' => 'FOLDER', 'active' => 'Y', 'status !=' => 'DEL'])->result();
		$jabatan 	= $this->db->get('positions')->result();
		$ArrFolder = [];
		foreach ($Data as $dir) {
			$ArrFolder[$dir->parent_id][] = $dir;
		}

		$loadFolder = $this->menus($ArrFolder);
		$this->template->set('loadFolder', $loadFolder);
		$this->template->set('jabatan', $jabatan);
		$this->template->set('mainFolder', $mainFolder);
		$this->template->render('create');
	}

	// NOTE: edit() dihapus karena tidak memuat data berdasarkan $id.
	// Jika dibutuhkan, implementasi ulang dengan load data dari DB.

	function menus($ArrFolder, $parent = '0')
	{
		if (!isset($ArrFolder[$parent])) {
			return '';
		}
		$cek_company = '';
		$html = "<ul class='h6 text-dark'>";
		foreach ($ArrFolder[$parent] as $val) {
			// exit;
			$main = ($val->parent_id == '0') ? true : false;
			if ($main == false) {
				$cek_company = ($val->company_id && $val->company_id != $this->company) ? 'd-none' : '';
			}

			if (isset($ArrFolder[$val->id])) {

				$html .= "<li class='h6 py-1 " . $cek_company . "'><a href='" . $val->link . "' data-id='" . $val->id . "' data-parent_id='" . $val->parent_id . "' class='tree-folder'>" . $val->name . "</a>";
				$html .= $this->menus($ArrFolder, $val->id);
				$html .= "</li>";
			} else {
				$html .= "<li class='h6 py-1 " . $cek_company . "'><a href='" . $val->link . "' data-id='" . $val->id . "' data-parent_id='" . $val->parent_id . "' data-custome='" . $val->custome . "' class='tree-folder'>" . $val->name . "</a></li>";
			}
		}
		// $html .= "<li class='h6 py-1'><a href='javascript:void(0)' data-custome='Y' class='procedures'>PROSEDUR, FORM, IK DAN RECORD</a></li>";
		$html .= "</ul>";
		return $html;
	}

	public function load_file($id = null, $folder = '')
	{
		if ($id != '0') {
			$data_file 	= $this->db->get_where('view_directories', ['parent_id' => $id, 'flag_type !=' => 'LINK', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
			$dir_row    = $this->db->get_where('view_directories', ['id' => $id])->row();
			$prev 		= $dir_row ? $dir_row->parent_id : '0';

			$this->template->set('prev', $prev);
			$this->template->set('parent_id', $id);
			$this->template->set('sts', $this->sts);
			$this->template->set('list_file', $data_file);
			$this->template->set('main', $folder);
			$this->template->render('list-file');
		}
	}

	public function previous()
	{
		$id = $this->input->post('id');
		if ($id) {
			$data = $this->db->get_where('view_directories', ['id' => $id, 'flag_type !=' => 'LINK', 'status !=' => 'DEL'])->row();
			echo json_encode($data);
		}
	}

	// NOTE: check_folder_name() dihapus karena berisi output debug (echo/exit)
	// dan tidak pernah dipanggil dari manapun. Logic duplikasi nama sudah
	// ditangani di method save().

	public function new_folder($parent_id, $folder)
	{
		$this->template->set('parent_id', $parent_id);
		$this->template->set('folder', $folder);
		$this->template->render('form');
	}

	public function rename($id)
	{
		$data = $this->db->get_where('view_directories', ['id' => $id])->row();

		$this->template->set('data', $data);
		$this->template->set('parent_id', $data->parent_id);
		$this->template->set('rename', true);
		$this->template->set('title', 'Rename');
		$this->template->render('form');
	}

	public function delete_folder()
	{
		$id 		= $this->input->post('id');

		// Validasi kepemilikan folder terhadap company aktif
		$folder = $this->db->get_where('directory', ['id' => $id, 'company_id' => $this->company])->row();
		if (!$folder) {
			echo json_encode(['status' => 0, 'msg' => 'Folder tidak ditemukan atau akses ditolak.']);
			return;
		}

		$this->db->trans_begin();
		$this->db->update('directory', ['status' => 'DEL'], ['id' => $id]);

		// Lakukan rekursif delete menggunakan model
		$user_id = $this->auth->user_id();
		$date    = date('Y-m-d H:i:s');
		$this->DOCS->delete_recursive_folder($id, $user_id, $date);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Folder failed deleted'
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Folder success deleted'
			);
		}
		// if ($check_child == '0') {
		// } else {
		// 	$Return		= array(
		// 		'status'		=> 0,
		// 		'msg'			=> "Can't delete this folder, The folder has another folder or file that belongs to this folder"
		// 	);
		// }

		echo json_encode($Return);
	}

	public function process_review()
	{
		$id 		= $this->input->post('id');
		if ($id) {
			$this->db->trans_begin();
			$this->db->update('directory', ['status' => 'REV', 'modified_by' => $this->auth->user_id(), 'modified_at' => date('Y-m-d H:i:s')], ['id' => $id]);

			$file = $this->db->get_where('view_directories', ['id' => $id])->row_array();
			$file['note'] = 'Processed to Review';
			$file['status'] = 'REV';
			$this->_update_history($file);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return		= array(
					'status'		=> 0,
					'msg'			=> 'Failed to Review Process'
				);
			} else {
				$this->db->trans_commit();
				$Return		= array(
					'status'		=> 1,
					'msg'			=> 'Success to Review Process'
				);
			}
		} else {
			$Return		= array(
				'status'		=> 0,
				'msg'			=> "File not valid"
			);
		}

		echo json_encode($Return);
	}

	public function procedures()
	{
		$group_process = $this->db->get_where('group_procedure')->result();
		$procedures = $this->db->get_where('procedures', ['status' => '1'])->result();

		$this->template->set([
			'procedures' => $procedures,
			'group_process' => $group_process
		]);

		$this->template->render('procedures/index');
	}

	public function list_procedures($id = null)
	{
		$procedures = $this->db->get_where('procedures', ['group_procedure' => $id, 'status' => '1', 'deleted_by' => null])->result();

		$this->template->set([
			'procedures' => $procedures
		]);
		$this->template->render('procedures/list-procedures');
	}

	public function procedure_details($id = null)
	{
		$this->template->set('id', $id);
		$this->template->render('procedures/list-docs');
	}

	public function getProcedure($id = null)
	{
		$procedure 	= $this->db->get_where('procedures', ['id' => $id])->result();

		$this->template->set([
			'data' 	=> $procedure,
			'type' 	=> 'view-procedure',
		]);
		$this->template->render('procedures/list-data');
	}

	public function getForms($id = null)
	{
		$forms 	= $this->db->order_by('name', 'ASC')->get_where('dir_forms', ['status !=' => 'DEL', 'procedure_id' => $id])->result();
		$this->template->set([
			'data' 	=> $forms,
			'type' 		=> 'view-form',
		]);
		$this->template->render('procedures/list-data');
	}

	public function getGuides($id = null)
	{
		$guides 		= $this->db->order_by('name', 'ASC')->get_where('dir_guides', ['status !=' => 'DEL', 'procedure_id' => $id])->result();
		// // $records 		= $this->db->get_where('dir_records', ['procedure_id' => $id])->result();

		$this->template->set([
			'data' 	=> $guides,
			'type' 		=> 'view-guide',
		]);
		$this->template->render('procedures/list-data');
	}
	public function getRecords($id = null)
	{
		$records 		= $this->db->order_by('name', 'ASC')->get_where('dir_records', ['status !=' => 'DEL', 'procedure_id' => $id])->result();

		$this->template->set([
			'data' 	=> $records,
			'type' 		=> 'view-records',
		]);
		$this->template->render('procedures/list-data');
	}

	public function delete_file()
	{
		$id 		= $this->input->post('id');

		// Validasi kepemilikan file terhadap company aktif
		$file = $this->db->get_where('directory', ['id' => $id, 'company_id' => $this->company])->row();
		if (!$file) {
			echo json_encode(['status' => 0, 'msg' => 'File tidak ditemukan atau akses ditolak.']);
			return;
		}

		$this->db->trans_begin();
		$this->db->update('directory', ['status' => 'DEL', 'modified_by' => $this->auth->user_id(), 'modified_at' => date('Y-m-d H:i:s')], ['id' => $id]);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Folder failed deleted'
			);
		} else {
			$this->db->trans_commit();
			$Return		= array(
				'status'		=> 1,
				'msg'			=> 'Folder success deleted'
			);
		}
		echo json_encode($Return);
	}

	public function save()
	{
		$data 				= $this->input->post();
		$check_folder_name 	= $this->db->get_where('view_directories', ['name' => $data['folder_name'], 'parent_id' => $data['parent_id']])->num_rows();
		$folder_name 		= ($check_folder_name > 0) ? $data['folder_name'] . "(" . $check_folder_name . ")" : $data['folder_name'];

		$ArrFolder = [
			'parent_id' => $data['parent_id'],
			'name' => $folder_name,
			'company_id' => $this->company,
		];

		if ($data) {
			$this->db->trans_begin();
			if (isset($data['id']) && ($data['id'] != null)) :
				$ArrFolder['modified_by'] = $this->auth->user_id();
				$ArrFolder['modified_at'] = date('Y-m-d H:i:s');
				$old_name = $this->db->get_where('view_directories', ['id' => $data['id']])->row()->name;
				if (is_dir("./directory/" . $old_name)) {
					rename("./directory/" . $old_name, "./directory/" . $data['folder_name']);
				}
				$this->db->update('directory', $ArrFolder, ['id' => $data['id']]);
			else :
				$ArrFolder['id'] = uniqid();
				$ArrFolder['created_by'] = $this->auth->user_id();
				$ArrFolder['created_at'] = date('Y-m-d H:i:s');
				$this->db->insert('directory', $ArrFolder);
				if (!is_dir('./directory/' . $data['folder_name'])) {
					mkdir('./directory/' . $data['folder_name'], 0755, TRUE);
					chmod("./directory/" . $data['folder_name'], 0755);  // octal; correct value of mode
					chown("./directory/" . $data['folder_name'], 'www-data');
				}
			endif;

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				if (is_dir('./directory/' . $data['folder_name'])) {
					rmdir('./directory/' . $data['folder_name']);
				}
				$Return		= array(
					'status'		=> 0,
					'msg'			=> 'Folder failed created'
				);
			} else {
				$this->db->trans_commit();
				$Return		= array(
					'status'		=> 1,
					'msg'			=> 'Folder success created'
				);
			}
		} else {
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Data not valid'
			);
		}

		echo json_encode($Return);
	}

	public function upload($parent, $folder)
	{
		$users 		= $this->db->get_where('users', ['status' => 'ACT', 'id_user !=' => '1'])->result();
		$jabatan 	= $this->db->get('positions')->result();
		// exit;
		$this->template->set([
			'jabatan' 		=> $jabatan,
			'parent_id' 	=> $parent,
			'users' 		=> $users,
			'folder' 		=> $folder,
		]);
		$this->template->render('upload_file');
	}

	public function edit_file($id)
	{
		$file 		= $this->db->get_where('view_directories', ['id' => $id])->row();

		$users 		= $this->db->get_where('users', ['st_aktif' => '1', 'id_perusahaan' => $this->company, 'id_user !=' => '1'])->result();
		$jabatan 	= $this->db->get('positions')->result();

		$this->template->set([
			'file' 			=> $file,
			'jabatan' 		=> $jabatan,
			'parent_id' 	=> $file->parent_id,
			'users' 		=> $users,
		]);

		$this->template->render('upload_file');
	}

	function fixForUri($string)
	{
		$slug = trim($string); // trim the string
		$slug = preg_replace('/[^a-zA-Z0-9 -]/', '', $slug); // only take alphanumerical characters, but keep the spaces and dashes too...
		$slug = str_replace(' ', '-', $slug); // replace spaces by dashes
		$slug = strtolower($slug);  // make it lowercase
		return $slug;
	}

	public function save_upload()
	{
		$data = $this->input->post();
		$mainFolder = $data['folder'];
		try {
			$parent_name = $this->db->get_where('view_directories', ['id' => $data['parent_id']])->row()->name;
			if ($_FILES['image']['name']) {
				if (!is_dir("./directory/$mainFolder/$this->company/" . $parent_name)) {
					mkdir("./directory/$mainFolder/$this->company/" . $parent_name, 0755, TRUE);
					chmod("./directory/$mainFolder/$this->company/" . $parent_name, 0755);  // octal; correct value of mode
					chown("./directory/$mainFolder/$this->company/" . $parent_name, 'www-data');
				}
				// $new_name 					= $this->fixForUri($data['description']);
				$config['upload_path'] 		= "./directory/$mainFolder/$this->company/$parent_name"; //path folder
				$config['allowed_types'] 	= 'pdf|xlsx|docx'; //type yang dapat diakses bisa anda sesuaikan
				$config['encrypt_name'] 	= true; //Enkripsi nama yang terupload
				$id 						= (!$data['id']) ? uniqid(date('m')) : $data['id'];


				$this->upload->initialize($config);
				if ($this->upload->do_upload('image')) :
					$file = $this->upload->data();
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
					$dist 					= isset($data['distribute_id']) ? implode(",", $data['distribute_id']) : null;
					$data['distribute_id']	= $dist;
					$old_file 				= isset($data['old_file']) ? $data['old_file'] : null;
					unset($data['old_file']);

					if ($old_file != null) {
						if (file_exists("./directory/$mainFolder/$this->company/$parent_name/" . $old_file)) {
							unlink("./directory/$mainFolder/$this->company/$parent_name/" . $old_file);
						}
					}

					$this->db->trans_begin();
					$check = $this->db->get_where('view_directories', ['id' => $id])->num_rows();

					if (intval($check) == '0') {
						$data['created_by']		= $this->auth->user_id();
						$data['created_at']		= date('Y-m-d H:i:s');
						$data['note']			= 'First Upload File';
						$data['status']			= isset($data['status']) ? $data['status'] : ((isset($data['flag_record']) && $data['flag_record'] == 'Y') ? 'PUB' : 'OPN');
						$this->_update_history($data);
						unset($data['note']);
						unset($data['folder']);
						$this->db->insert('directory', $data);
					} else {
						$data['modified_by']	= $this->auth->user_id();
						$data['modified_at']	= date('Y-m-d H:i:s');
						$data['note']			= 'Re-upload File';
						$this->_update_history($data);
						unset($data['folder']);
						unset($data['note']);
						$this->db->update('directory', $data, ['id' => $id]);
					}

					if (isset($data['distribute_id'])) {
						foreach ($this->input->post('distribute_id') as $key => $value) {
							$cek = $this->db->where(['id_jabatan' => $value, 'id_file' => $this->input->post('id')])->get('distribusi')->row();
							$arr_dist = [
								'id_file' => $this->input->post('id'),
								'id_jabatan' => $value
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
			}
		} catch (Exception $e) {
			$this->db->trans_rollback();
			$Return = [
				'status' => 0,
				'msg'	 => $e->getMessage()
			];
			echo json_encode($Return);
			return;
		}

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
		echo json_encode($Return);
	}

	private function _update_history($data)
	{
		$dataLog = [
			'directory_id'  => $data['id'],
			'new_status' 	 	=> $data['status'],
			'note' 		    => $data['note'],
			'updated_by'    => $this->auth->user_id(),
			'updated_at'    => date('Y-m-d H:i:s'),
		];

		$this->db->insert('directory_log', $dataLog);
	}

	public function view_file($id, $folder = '')
	{
		$file 			= $this->db->get_where('view_directories', ['id' => $id])->row();
		if (!$file) {
			show_404();
			return;
		}
		$parent_row  = $this->db->get_where('view_directories', ['id' => $file->parent_id])->row();
		$parent_name = $parent_row ? $parent_row->name : '';
		$this->template->set(
			[
				'file' => $file,
				'parent_name' => $parent_name,
				'company_id' => $this->company,
				'folderMain' => $folder,
			]
		);
		$this->template->render('view-file');
	}

	public function viewfile($parent_name, $id)
	{
		// Sanitasi parameter untuk mencegah path traversal
		$parent_name = basename(str_replace(['..', '\\'], '', $parent_name));
		$id          = basename(str_replace(['..', '\\'], '', $id));

		$path = "./directory/" . $parent_name . "/" . $id;

		if (!file_exists($path)) {
			show_404();
			return;
		}

		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="' . $id . '"');
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		@readfile($path);
	}


	/* REVIEW PROCESS */

	public function review()
	{
		$files = $this->db->get_where('view_directories', ['status' => 'REV'])->result();
		$this->template->set('files', $files);
		$this->template->set('sts', $this->sts);
		$this->template->render('review/review-list');
	}

	public function load_form_review($id)
	{
		$file 		= $this->db->get_where('view_directories', ['id' => $id])->row();
		$dir_name 	= $this->db->get_where('view_directories', ['id' => $file->parent_id])->row()->name;
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
		$this->template->set('dir_name', $dir_name);
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
		$this->template->set('history', $history);
		$this->template->render('review/review-form');
	}

	public function save_review()
	{
		$data = $this->input->post();

		if ($data) {
			$this->db->trans_begin();
			$this->db->update(
				'directory',
				[
					'status' 		=> $data['status'],
					'modified_by' 	=> $this->auth->user_id(),
					'modified_at' 	=> date('Y-m-d H:i:s'),
					'reviewed_by' 	=> $this->auth->user_id(),
					'reviewed_at' 	=> date('Y-m-d H:i:s'),
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

	/* END REVIEW PROCESS */



	/* CORRECTION RPOCESS */
	public function correction()
	{
		$files = $this->db->get_where('view_directories', ['status' => 'COR'])->result();
		$this->template->set('files', $files);
		$this->template->set('sts', $this->sts);
		$this->template->render('correction/correction-list');
	}

	public function load_form_correction($id)
	{
		$file 		= $this->db->get_where('view_directories', ['id' => $id])->row();
		$dir_name 	= $this->db->get_where('view_directories', ['id' => $file->parent_id])->row()->name;
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
		$jabatan 	= $this->db->get('positions')->result();
		$this->template->set('dir_name', $dir_name);
		$this->template->set('jabatan', $jabatan);
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
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
		$files = $this->db->get_where('view_directories', ['status' => 'APV'])->result();
		$this->template->set('files', $files);
		$this->template->set('sts', $this->sts);
		$this->template->render('approval/approval-list');
	}

	public function load_form_approval($id)
	{
		$file 		= $this->db->get_where('view_directories', ['id' => $id])->row();
		$dir_name 	= $this->db->get_where('view_directories', ['id' => $file->parent_id])->row()->name;
		$history	= $this->db->order_by('updated_at', 'ASC')->get_where('directory_log', ['directory_id' => $id])->result();
		$jabatan 	= $this->db->get('positions')->result();
		$this->template->set('dir_name', $dir_name);
		$this->template->set('jabatan', $jabatan);
		$this->template->set('sts', $this->sts);
		$this->template->set('file', $file);
		$this->template->set('history', $history);
		$this->template->render('approval/approval-form');
	}

	public function save_approval()
	{
		$data = $this->input->post();
		$this->load->library(array('Mpdf'));

		if ($data) {
			$this->db->trans_begin();
			$this->db->update(
				'directory',
				[
					'approved_at' => date('Y-m-d H:i:s'),
					'approved_by' => $this->auth->user_id(),
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

				$folder = $this->db->get_where('view_directories', ['id' => $data['parent_id']])->row();
				$file = $this->db->get_where('view_directories', ['id' => $data['id']])->row();
				$users = $this->db->get('users')->result();

				$ArrUsers = [];
				foreach ($users as $user) {
					$ArrUsers[$user->id_user] = $user;
				}

				$html = " 
					<div>
					<table border='1' width='100%' style='font-family:Arial,Tahoma,sans-serif;border-collapse: collapse;'>
						<thead>
							<tr>
								<th>Prepered By</th>
								<th>Reviewed By</th>
								<th>Aproved By</th>
							</tr>
							<tr>
								<td align='center'>" . (isset($ArrUsers[$file->prepared_by]) ? $ArrUsers[$file->prepared_by]->full_name : 'N/A') . "</td>
								<td align='center'>" . (isset($ArrUsers[$file->reviewed_by]) ? $ArrUsers[$file->reviewed_by]->full_name : 'N/A') . "</td>
								<td align='center'>" . (isset($ArrUsers[$file->approved_by]) ? $ArrUsers[$file->approved_by]->full_name : 'N/A') . "</td>
							</tr>
							<tr>
								<td align='center'>$file->created_at</td>
								<td align='center'>$file->reviewed_at</td>
								<td align='center'>$file->approved_at</td>
							</tr>
						</thead>
					</table>
					</div>
					";
				$mpdf = new mPDF(
					'P',
					'',
					'',
					'',
					'20', //kiri
					'20', //kanan
					'30', //atas
					'10', //bawah
					'',
					''
				);

				$mpdf->SetImportUse();
				$pagecount = $mpdf->SetSourceFile('./directory/' . $folder->name . '/' . $file->file_name);

				for ($i = 1; $i <= $pagecount; $i++) {
					$mpdf->AddPage(); // Add page to output document
					$tplId = $mpdf->ImportPage($i); // Import page as a template
					$mpdf->UseTemplate($tplId); // insert the template in the added page
				}

				// $tplId = $mpdf->ImportPage($pagecount);
				// $mpdf->UseTemplate($tplId);
				$mpdf->addPage();
				$mpdf->WriteHTML($html);
				$newfile = './directory/' .  $folder->name . '/' . $file->file_name;
				$mpdf->Output($newfile, 'F');
			}
		} else {
			$Return = [
				'status' => 0,
				'msg'	 => 'Data not valid.'
			];
		}

		echo json_encode($Return);
	}

	public function print_document()
	{
		$this->load->library(array('Mpdf'));

		// Sanitasi input untuk mencegah path traversal
		$folder = basename(str_replace(['..', '\\'], '', $this->input->get('p')));
		$file   = basename(str_replace(['..', '\\'], '', $this->input->get('f')));

		if (empty($folder) || empty($file)) {
			show_error('Parameter tidak valid.', 400);
			return;
		}

		$filePath = 'directory/' . $folder . '/' . $file;
		if (!file_exists($filePath)) {
			show_404();
			return;
		}

		$mpdf = new mPDF('', '', '', '', '', '', '', '', '', '');
		$mpdf->SetImportUse();
		$pagecount = $mpdf->SetSourceFile($filePath);
		$tplId = $mpdf->ImportPage($pagecount);
		$mpdf->UseTemplate($tplId);
		$mpdf->addPage();
		$mpdf->WriteHTML('Hello World');
		$mpdf->Output($filePath, 'F');
		$mpdf->Output();
	}
}
