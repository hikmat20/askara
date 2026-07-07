<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Documents extends Admin_Controller
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
		$this->template->page_icon('fa fa-file-alt');
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
		$this->template->render('file-manager');
	}

	public function index_()
	{
		// if (!isset($_GET['d']) || $_GET['d'] == '') {
		// 	redirect('documents/?d=0');
		// }

		// Root path for file manager
		// use absolute path of directory i.e: '/var/www/folder' or $_SERVER['DOCUMENT_ROOT'].'/folder'
		$root_path = $_SERVER['DOCUMENT_ROOT'] . '/data';

		// Root url for links in file manager.Relative to $http_host. Variants: '', 'path/to/subfolder'
		// Will not working if $root_path will be outside of server document root
		$root_url = $root_path;

		// Server hostname. Can set manually if wrong
		// $_SERVER['HTTP_HOST'].'/folder'
		$http_host = $_SERVER['HTTP_HOST'];

		// input encoding for iconv
		$iconv_input_encoding = 'UTF-8';

		// date() format for file modification date
		// Doc - https://www.php.net/manual/en/function.date.php
		$datetime_format = 'm/d/Yd g:i A';

		// Enable ace.js (https://ace.c9.io/) on view's page
		$edit_files = true;

		// Allowed file extensions for create and rename files
		// e.g. 'txt,html,css,js'
		$allowed_file_extensions = '';

		// Allowed file extensions for upload files
		// e.g. 'gif,png,jpg,html,txt'
		$allowed_upload_extensions = '';

		// Favicon path. This can be either a full url to an .PNG image, or a path based on the document root.
		// full path, e.g http://example.com/favicon.png
		// local path, e.g images/icons/favicon.png
		$favicon_path = '';

		// Files and folders to excluded from listing
		// e.g. array('myfile.html', 'personal-folder', '*.php', ...)
		$exclude_items = array();

		$global_readonly = false;
		// Sticky Nav bar
		// true => enable sticky header
		// false => disable sticky header
		$sticky_navbar = true;

		// Maximum file upload size
		// Increase the following values in php.ini to work properly
		// memory_limit, upload_max_filesize, post_max_size
		$max_upload_size_bytes = 5000000000; // size 5,000,000,000 bytes (~5GB)

		// Possible rules are 'OFF', 'AND' or 'OR'
		// OFF => Don't check connection IP, defaults to OFF
		// AND => Connection must be on the whitelist, and not on the blacklist
		// OR => Connection must be on the whitelist, or not on the blacklist
		$ip_ruleset = 'OFF';

		// Should users be notified of their block?
		$ip_silent = true;

		// IP-addresses, both ipv4 and ipv6
		$ip_whitelist = array(
			'127.0.0.1',    // local ipv4
			'::1'           // local ipv6
		);

		// IP-addresses, both ipv4 and ipv6
		$ip_blacklist = array(
			'0.0.0.0',      // non-routable meta ipv4
			'::'            // non-routable meta ipv6
		);

		// Show or hide files and folders that starts with a dot
		$show_hidden_files = false;

		$is_https = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';

		// clean $root_url
		$root_url = $this->fm_clean_path($root_url);

		// echo '<pre>';
		// print_r($_SERVER['PHP_SELF']);
		// echo '</pre>';
		// exit;
		// abs path for site
		defined('FM_ROOT_URL') || define('FM_ROOT_URL', ($is_https ? 'https' : 'http') . '://' . $http_host . (!empty($root_url) ? '/' . $root_url : ''));
		defined('FM_SELF_URL') || define('FM_SELF_URL', ($is_https ? 'https' : 'http') . '://' . $http_host . '/documents');

		// clean and check $root_path
		$root_path = rtrim($root_path, '\\/');
		$root_path = str_replace('\\', '/', $root_path);
		if (!@is_dir($root_path)) {
			echo "<h1>" . ('Root path') . " \"{$root_path}\" " . ('not found!') . " </h1>";
			exit;
		}

		defined('FM_SHOW_HIDDEN') || define('FM_SHOW_HIDDEN', $show_hidden_files);
		defined('FM_ROOT_PATH') || define('FM_ROOT_PATH', $root_path);
		// defined('FM_LANG') || define('FM_LANG', $lang);
		defined('FM_FILE_EXTENSION') || define('FM_FILE_EXTENSION', $allowed_file_extensions);
		defined('FM_UPLOAD_EXTENSION') || define('FM_UPLOAD_EXTENSION', $allowed_upload_extensions);
		defined('FM_EXCLUDE_ITEMS') || define('FM_EXCLUDE_ITEMS', (version_compare(PHP_VERSION, '7.0.0', '<') ? serialize($exclude_items) : $exclude_items));
		// defined('FM_DOC_VIEWER') || define('FM_DOC_VIEWER', $online_viewer);
		// define('FM_READONLY', $global_readonly || ($use_auth && !empty($readonly_users) && isset($_SESSION[FM_SESSION_ID]['logged']) && in_array($_SESSION[FM_SESSION_ID]['logged'], $readonly_users)));
		define('FM_READONLY', $global_readonly);
		define('FM_IS_WIN', DIRECTORY_SEPARATOR == '\\');

		// always use ?d=
		if (!isset($_GET['d']) && empty($_FILES)) {
			$this->fm_redirect(FM_SELF_URL . '?d=');
		}

		// get path
		$p = isset($_GET['p']) ? $_GET['p'] : (isset($_POST['p']) ? $_POST['p'] : '');

		// clean path
		$p = $this->fm_clean_path($p);

		// for ajax request - save
		$input = file_get_contents('php://input');
		$_POST = (strpos($input, 'ajax') != FALSE && strpos($input, 'save') != FALSE) ? json_decode($input, true) : $_POST;

		// instead globals vars
		define('FM_PATH', $p);
		// define('FM_USE_AUTH', $use_auth);
		define('FM_EDIT_FILE', $edit_files);
		defined('FM_ICONV_INPUT_ENC') || define('FM_ICONV_INPUT_ENC', $iconv_input_encoding);
		// defined('FM_USE_HIGHLIGHTJS') || define('FM_USE_HIGHLIGHTJS', $use_highlightjs);
		// defined('FM_HIGHLIGHTJS_STYLE') || define('FM_HIGHLIGHTJS_STYLE', $highlightjs_style);
		defined('FM_DATETIME_FORMAT') || define('FM_DATETIME_FORMAT', $datetime_format);

		unset($p, $use_auth, $iconv_input_encoding, $use_highlightjs, $highlightjs_style);


		// $ArrDetail 				= '';
		// $selected 				= $details = $details_data = $breadcumb = $sub = '';
		// $dirs 	  				= $this->db->get_where('materi', ['status' => '1', 'company_id' => $this->company])->result();

		// if (isset($_GET['d']) && ($_GET['d'])) {
		// 	$selected 			= $_GET['d'];
		// 	$details 	  		= $this->db->get_where('view_materi_details', ['materi_id' => $selected, 'status' => '1', 'company_id' => $this->company])->result();
		// 	$materi 			= $this->db->get_where('materi', ['id' => $selected, 'company_id' => $this->company])->row();
		// 	$breadcumb 			= [($materi) ? $materi->name : ''];
		// }

		// if ((isset($_GET['d']) && ($_GET['d'])) && (isset($_GET['sub']) && ($_GET['sub']))) {
		// 	$selected 			= $_GET['d'];
		// 	$sub 				= $_GET['sub'];
		// 	$details 	  		= $this->db->get_where('view_materi_details', ['id' => $sub, 'status' => '1', 'company_id' => $this->company])->row();
		// 	$details_data 	  	= $this->db->get_where('view_materi_detail_data', ['materi_detail_id' => $sub, 'status' => '1', 'company_id' => $this->company])->result();
		// 	$breadcumb 			= ($details) ? [$details->materi_name, $details->materi_detail_name] : '';

		// 	$ArrDetail 			= [0];
		// 	foreach ($details_data as $dtl) {
		// 		$ArrDetail[$dtl->category][] = $dtl;
		// 	}
		// }




		// get current path
		$path = FM_ROOT_PATH;

		if (FM_PATH != '') {
			$path .= '/' . FM_PATH;
		}
		// check path
		if (!is_dir($path)) {
			$this->fm_redirect(FM_SELF_URL . '?d=');
		}

		// get parent folder
		$parent = $this->fm_get_parent_path(FM_PATH);

		$objects = is_readable($path) ? scandir($path) : array();
		$folders = array();
		$files = array();
		$current_path = array_slice(explode("/", $path), -1)[0];

		if (is_array($objects) && $this->fm_is_exclude_items($current_path)) {
			foreach ($objects as $file) {
				if ($file == '.' || $file == '..') {
					continue;
				}
				if (!FM_SHOW_HIDDEN && substr($file, 0, 1) === '.') {
					continue;
				}
				$new_path = $path . '/' . $file;
				if (@is_file($new_path)) {
					$files[] = $file;
				} elseif (@is_dir($new_path) && $file != '.' && $file != '..') {
					$folders[] = $file;
				}
			}
		}


		if (!empty($files)) {
			natcasesort($files);
		}
		if (!empty($folders)) {
			natcasesort($folders);
		}

		$num_files = count($files);
		$num_folders = count($folders);
		$all_files_size = 0;
		$this->template->set([
			'parent' 			=> $parent,
			'files' 			=> $files,
			'folders'  			=> $folders,
			'FM_READONLY'  		=> FM_READONLY,
			'FM_ROOT_URL'  		=> FM_ROOT_URL,
			'FM_IS_WIN'  		=> FM_IS_WIN,
			'FM_PATH'  			=> FM_PATH,
			'path'  			=> $path,
			'num_files'  		=> $num_files,
			'num_folders'  		=> $num_folders,
			'all_files_size'  	=> $all_files_size,
		]);

		$this->template->render('index');
	}


	/**
	 * Get parent path
	 * @param string $path
	 * @return bool|string
	 */
	function fm_get_parent_path($path)
	{
		$path = $this->fm_clean_path($path);
		if ($path != '') {
			$array = explode('/', $path);
			if (count($array) > 1) {
				$array = array_slice($array, 0, -1);
				return implode('/', $array);
			}
			return '';
		}
		return false;
	}


	function fm_clean_path($path, $trim = true)
	{
		$path = $trim ? trim($path) : $path;
		$path = trim($path, '\\/');
		$path = str_replace(
			array('../', '..\\'),
			'',
			$path
		);
		$path = $this->get_absolute_path($path);
		if ($path == '..') {
			$path = '';
		}
		return str_replace('\\', '/', $path);
	}

	/**
	 * Path traversal prevention and clean the url
	 * It replaces (consecutive) occurrences of / and \\ with whatever is in DIRECTORY_SEPARATOR, and processes /. and /.. fine.
	 * @param $path
	 * @return string
	 */
	function get_absolute_path($path)
	{
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		$absolutes = array();
		foreach ($parts as $part) {
			if ('.' == $part) continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		return implode(DIRECTORY_SEPARATOR, $absolutes);
	}

	/**
	 * HTTP Redirect
	 * @param string $url
	 * @param int $code
	 */
	function fm_redirect($url, $code = 302)
	{
		header('Location: ' . $url, true, $code);
		exit;
	}

	/**
	 * Check file is in exclude list
	 * @param string $file
	 * @return bool
	 */
	function fm_is_exclude_items($file)
	{
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		if (isset($exclude_items) and sizeof($exclude_items)) {
			unset($exclude_items);
		}

		$exclude_items = FM_EXCLUDE_ITEMS;
		if (version_compare(PHP_VERSION, '7.0.0', '<')) {
			$exclude_items = unserialize($exclude_items);
		}
		if (!in_array($file, $exclude_items) && !in_array("*.$ext", $exclude_items)) {
			return true;
		}
		return false;
	}

	/* ==================================================================================== */

	public function save_dir()
	{
		$post 			= $this->input->post();

		if (!isset($post['id'])) {
			$name 			= $post['name'];
			$is_exist 		= $this->db->get_where('materi', ['name' => $name])->num_rows();

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

			$this->db->insert('materi', $data);
		} else {
			$name 			= $post['name'];
			$data = [
				'name' => strtoupper($name),
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('materi', $data, ['id' => $post['id']]);
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
			$data = $this->db->get_where('materi', ['id' => $id])->row();
			echo json_encode(['data' => $data]);
		}
	}

	public function delete_dir()
	{
		$id 		 = $this->input->post('id');
		$check_child = $this->db->get_where('materi_details', ['materi_id' => $id])->num_rows();

		$this->db->trans_begin();

		if ($check_child > 0) {
			$data_detail = $this->db->get_where('materi_details', ['materi_id' => $id,], ['materi_id' => $id])->result();

			$this->db->update('materi_details', ['status' => '0'], ['materi_id' => $id]);
			foreach ($data_detail as $dtl) {
				$detail_data = $this->db->get_where('materi_detail_data', ['materi_detail_id' => $dtl->id])->result();

				if (count($detail_data) > 0) {
					$this->db->update('materi_detail_data', ['status' => '0'], ['materi_detail_id' => $dtl->id]);
				}
			}
		}

		$this->db->update('materi', ['status' => '0'], ['id' => $id]);

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
			$data = $this->db->get_where('materi_details', ['id' => $id])->row();
			echo json_encode(['data' => $data]);
		}
	}

	public function save_sub_dir()
	{
		$post 			= $this->input->post();

		if (!isset($post['id'])) {
			$name 			= $post['name'];
			$materi_id 		= $post['materi_id'];
			$is_exist 		= $this->db->get_where('materi_details', ['name' => $name])->num_rows();

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
				'company_id' 	=> $this->company,
				'materi_id' 	=> $materi_id,
				'created_by' 	=> $this->auth->user_id(),
				'created_at' 	=> date('Y-m-d H:i:s'),
			];

			$this->db->insert('materi_details', $data);
		} else {
			$name 			= $post['name'];
			$data = [
				'name' => strtoupper($name),
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('materi_details', $data, ['id' => $post['id']]);
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
		$check_child = $this->db->get_where('materi_detail_data', ['materi_detail_id' => $id])->result();

		$this->db->trans_begin();

		if (count($check_child) > 0) {
			$this->db->update('materi_detail_data', ['status' => '0'], ['materi_detail_id' => $id]);
		}

		$this->db->update('materi_details', ['status' => '0'], ['id' => $id]);

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
	public function save_document()
	{
		$data 					= $this->input->post();
		$id						= isset($data['id']) ? $data['id'] : '';
		$data['company_id']		= $this->company;
		$nama_file 				= $data['name-file'];
		$old_file 				= isset($data['old_file']) ? $data['old_file'] : null;

		unset($data['old_file']);
		unset($data['name-file']);

		try {
			$DIR_COMP = $this->company;
			if (($_FILES) && $_FILES['documents']['name']) {
				if (!is_dir('./directory/MATERI/' . $DIR_COMP)) {
					mkdir('./directory/MATERI/' . $DIR_COMP, 0755, TRUE);
					chmod("./directory/MATERI/" . $DIR_COMP, 0755);  // octal; correct value of mode
					chown("./directory/MATERI/" . $DIR_COMP, 'www-data');
				}
				// $new_name 					= $this->fixForUri($data['description']);
				$config['upload_path'] 		= "./directory/MATERI/$DIR_COMP"; //path folder
				$config['allowed_types'] 	= 'pdf|xlsx|docx'; //type yang dapat diakses bisa anda sesuaikan
				$config['encrypt_name'] 	= true; //Enkripsi nama yang terupload
				// $config['file_name'] 		= $new_name;

				$this->upload->initialize($config);
				if ($this->upload->do_upload('documents')) {
					$file = $this->upload->data();
					$data['document']		= $file['file_name'];

					if ($old_file != null) {
						if (file_exists("./directory/MATERI/" . $DIR_COMP . $old_file)) {
							unlink("./directory/MATERI/" . $DIR_COMP . $old_file);
						}
					}
				} else {
					$error_msg = $this->upload->display_errors();
					$Return = [
						'status' => 0,
						'msg'	 => $error_msg
					];
					echo json_encode($Return);
					return false;
				};
			}

			if ($nama_file) {
				$this->db->trans_begin();
				$check = $this->db->get_where('materi_detail_data', ['id' => $id])->num_rows();

				if (intval($check) == '0') {
					$data['created_by']			= $this->auth->user_id();
					$data['created_at']			= date('Y-m-d H:i:s');
					$data['name']				= $nama_file;
					// $data['link_website_name'] 	= $data['link_website_name'];
					// $data['url_link'] 			= $data['url_link'];
					// $data['video_name'] 		= $data['video_name'];
					// $data['video_link'] 		= $data['video_link'];
					$this->db->insert('materi_detail_data', $data);
				} else {
					$data['modified_by']		= $this->auth->user_id();
					$data['modified_at']		= date('Y-m-d H:i:s');
					$data['name']				= $nama_file;
					// $data['link_website_name'] 	= $data['link_website_name'];
					// $data['url_link'] 			= $data['url_link'];
					// $data['video_name'] 		= $data['video_name'];
					// $data['video_link'] 		= $data['video_link'];

					if (isset($data['remove-document']) && $data['remove-document'] == 'x' && !$_FILES['documents']['name']) {
						if (file_exists("./directory/MATERI/" . $DIR_COMP . $old_file)) {
							unlink("./directory/MATERI/" . $DIR_COMP . $old_file);
						}
						$data['document'] = null;
					}
					unset($data['remove-document']);
					$this->db->update('materi_detail_data', $data, ['id' => $id]);
				}
			}
		} catch (Exception $e) {
			$this->db->trans_rollback();
			$Return = [
				'status' => 0,
				'msg'	 => $e->getMessage()
			];

			echo json_encode($Return);
			return false;
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

	public function edit_file($id)
	{
		$data = [];
		if ($id) {
			$data = $this->db->get_where('materi_detail_data', ['id' => $id])->row();

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
		$data 			= $this->db->get_where('materi_detail_data', ['id' => $id])->row();
		$this->template->set(
			[
				'data' => $data,
			]
		);

		$this->template->render('view-file');
	}

	public function delete_file()
	{
		$id = $this->input->post('id');
		if ($id) {
			$this->db->update('materi_detail_data', ['status' => '0'], ['id' => $id]);

			$return = [
				'status' => 1
			];
		} else {
			$return = [
				'status' => 0
			];
		}

		echo json_encode($return);
	}





	/* ============================= */
	public function create()
	{
		if (!$this->auth->is_admin()) {
			$permission = $this->db->get_where('group_menus', ['group_id' => $this->group_id, 'company_id' => $this->company])->row();
			if (!$permission) {
				redirect('/');
				return FALSE;
			}
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
		$this->template->render('create');
	}

	public function edit($id)
	{
		$this->template->render('create');
	}

	function menus($ArrFolder, $parent = '0')
	{
		// $result = ("SELECT a.id, a.label, a.link, Deriv1.Count FROM `menu` a  LEFT OUTER JOIN (SELECT parent, COUNT(*) AS Count FROM `menu` GROUP BY parent) Deriv1 ON a.id = Deriv1.parent WHERE a.parent=" . $parent);
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

	public function load_file($id = null)
	{

		if ($id != '0') {

			$data_file = $this->db->get_where('view_directories', ['parent_id' => $id, 'flag_type !=' => 'LINK', 'status !=' => 'DEL', 'company_id' => $this->company])->result();
			$prev = $this->db->get_where('view_directories', ['id' => $id])->row()->parent_id;
			// echo '<pre>';
			// print_r($data_file);
			// echo '<pre>';
			// exit;
			$this->template->set('prev', $prev);
			$this->template->set('parent_id', $id);
			$this->template->set('sts', $this->sts);
			$this->template->set('list_file', $data_file);
			$this->template->render('list-file');
		}
		// else {
		// 	$data_file = $this->db->get_where('view_directories', ['parent_id' => '0', 'flag_type !=' => 'LINK', 'status !=' => 'DEL'])->result();
		// 	$this->template->set('list_file', $data_file);
		// 	$this->template->render('list-file');
		// }
	}

	public function previous()
	{
		$id = $this->input->post('id');
		if ($id) {
			$data = $this->db->get_where('view_directories', ['id' => $id, 'flag_type !=' => 'LINK', 'status !=' => 'DEL'])->row();
			echo json_encode($data);
		}
	}

	public function check_folder_name($name, $parent_id)
	{
		$check 	= $this->db->get_where('view_directories', ['name' => $name, 'parent_id' => $parent_id])->num_rows();
		if ($check > 0) {
			for ($i = 0; $i < $check; $i++) {
				$newName = $name . "(" . $i . ")";
				echo '<pre>';
				print_r($newName);
				echo '<pre>';
			}
			exit;
		}
	}

	public function new_folder($parent_id)
	{
		$this->template->set('parent_id', $parent_id);
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
		$check_child = $this->db->get_where('view_directories', ['parent_id' => $id])->num_rows();

		$this->db->trans_begin();
		$this->db->update('directory', ['status' => 'DEL'], ['id' => $id]);

		if ($check_child > 0) {
			$this->db->update('directory', ['status' => 'DEL', 'modified_by' => $this->auth->user_id(), 'modified_at' => date('Y-m-d H:i:s')], ['parent_id' => $id]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return		= array(
				'status'		=> 0,
				'pesan'			=> 'Folder failed deleted'
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
					'pesan'			=> 'Failed to Review Process'
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

	public function save()
	{
		$data 				= $this->input->post();
		$check_folder_name 	= $this->db->get_where('view_directories', ['name' => $data['folder_name'], 'parent_id' => $data['parent_id']])->num_rows();
		$folder_name 		= ($check_folder_name > 0) ? $data['folder_name'] . "(" . $check_folder_name . ")" : $data['folder_name'];

		$ArrFolder = [
			'parent_id' => $data['parent_id'],
			'name' => $data['folder_name'],
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
					'pesan'			=> 'Folder faild created'
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
				'pesan'			=> 'Data not valid'
			);
		}

		echo json_encode($Return);
	}

	public function upload($parent_id)
	{
		$users 		= $this->db->get_where('users', ['status' => 'ACT', 'id_user !=' => '1'])->result();
		$jabatan 	= $this->db->get('positions')->result();

		$this->template->set([
			'jabatan' 		=> $jabatan,
			'parent_id' 	=> $parent_id,
			'users' 		=> $users,
		]);
		$this->template->render('upload_file');
	}

	public function edit_filse($id)
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

	// public function save_document()
	// {
	// 	$data = $this->input->post();

	// 	try {
	// 		$parent_name = $this->db->get_where('view_directories', ['id' => $data['parent_id']])->row()->name;
	// 		if ($_FILES['image']['name']) {
	// 			if (!is_dir('./directory/' . $parent_name)) {
	// 				mkdir('./directory/' . $parent_name, 0755, TRUE);
	// 				chmod("./directory/" . $parent_name, 0755);  // octal; correct value of mode
	// 				chown("./directory/" . $parent_name, 'www-data');
	// 			}
	// 			// $new_name 					= $this->fixForUri($data['description']);
	// 			$config['upload_path'] 		= "./directory/$parent_name"; //path folder
	// 			$config['allowed_types'] 	= 'pdf|xlsx|docx'; //type yang dapat diakses bisa anda sesuaikan
	// 			$config['encrypt_name'] 	= true; //Enkripsi nama yang terupload
	// 			// $config['file_name'] 		= $new_name;
	// 			$id 						= (!$data['id']) ? uniqid(date('m')) : $data['id'];


	// 			$this->upload->initialize($config);
	// 			if ($this->upload->do_upload('image')) :
	// 				$file = $this->upload->data();
	// 				$file_name  = $file['file_name'];
	// 				$size  		= $file['file_size'];
	// 				$ext     	= $file['file_ext'];


	// 				$data['id']	    		= $id;
	// 				$data['name']	    	= $data['description'];
	// 				$data['file_name']		= $file_name;
	// 				$data['size']			= $size;
	// 				$data['ext']			= $ext;
	// 				$data['company_id']		= $this->company;
	// 				$data['flag_type']		= 'FILE';
	// 				$dist 					= isset($data['distribute_id']) ? implode(",", $data['distribute_id']) : null;
	// 				$data['distribute_id']	= $dist;
	// 				$old_file 				= isset($data['old_file']) ? $data['old_file'] : null;
	// 				unset($data['old_file']);

	// 				if ($old_file != null) {
	// 					if (file_exists('./assets/files/' . $old_file)) {
	// 						unlink('./assets/files/' . $old_file);
	// 					}
	// 				}

	// 				$this->db->trans_begin();
	// 				$check = $this->db->get_where('view_directories', ['id' => $id])->num_rows();

	// 				if (intval($check) == '0') {
	// 					$data['created_by']		= $this->auth->user_id();
	// 					$data['created_at']		= date('Y-m-d H:i:s');
	// 					$data['note']			= 'First Upload File';
	// 					$data['status']			= isset($data['status']) ? $data['status'] : (($data['flag_record'] == 'Y') ? 'PUB' : 'OPN');
	// 					$this->_update_history($data);
	// 					unset($data['note']);
	// 					$this->db->insert('directory', $data);
	// 				} else {
	// 					$data['modified_by']	= $this->auth->user_id();
	// 					$data['modified_at']	= date('Y-m-d H:i:s');
	// 					$data['note']			= 'Re-upload File';
	// 					$this->_update_history($data);
	// 					unset($data['note']);
	// 					$this->db->update('directory', $data, ['id' => $id]);
	// 				}

	// 				if (isset($data['distribute_id'])) {
	// 					foreach ($this->input->post('distribute_id') as $key => $value) {
	// 						$cek = $this->db->where(['id_jabatan' => $value, 'id_file' => $this->input->post('id')])->get('distribusi')->row();
	// 						$arr_dist = [
	// 							'id_file' => $this->input->post('id'),
	// 							'id_jabatan' => $value
	// 						];

	// 						if ($cek) {
	// 							$this->db->update('distribusi', $arr_dist, ['id' => $cek->id]);
	// 						} else if (!$cek) {
	// 							$this->db->insert('distribusi', $arr_dist);
	// 						} else {
	// 							$this->db->delete('distribusi', ['id' => $cek->id]);
	// 						}
	// 					}
	// 				};

	// 			else :
	// 				$error_msg = $this->upload->display_errors();
	// 				$Return = [
	// 					'status' => 0,
	// 					'msg'	 => $error_msg
	// 				];
	// 				echo json_encode($Return);
	// 				return false;
	// 			endif;
	// 		} else {
	// 			$Return = [
	// 				'status' => 0,
	// 				'msg'	 => 'No file or data to upload document..'
	// 			];
	// 		}
	// 	} catch (Exception $e) {
	// 		$this->db->trans_rollback();
	// 		$Return = [
	// 			'status' => 1,
	// 			'msg'	 => $e->getMessage()
	// 		];

	// 		return $Return;
	// 	}

	// 	if ($this->db->trans_status() === FALSE) {
	// 		$this->db->trans_rollback();
	// 		$Return = [
	// 			'status' => 0,
	// 			'msg'	 => 'Failed upload document file. Please try again later.!'
	// 		];
	// 	} else {
	// 		$this->db->trans_commit();
	// 		$Return = [
	// 			'status' => 1,
	// 			'msg'	 => 'Success upload document file...'
	// 		];
	// 	}
	// 	echo json_encode($Return);
	// }

	private function _update_history($data)
	{
		$dataLog = [
			'directory_id'  => $data['id'],
			'status' 	 	=> $data['status'],
			'note' 		    => $data['note'],
			'updated_by'    => $this->auth->user_id(),
			'updated_at'    => date('Y-m-d H:i:s'),
		];

		$this->db->insert('directory_log', $dataLog);
	}


	public function viewfile($parent_name, $id)
	{
		// $file 			= $this->db->get_where('view_directories', ['id' => $id])->row();
		// $parent_name 	= $this->db->get_where('view_directories', ['id' => $file->parent_id])->row()->name;
		// Store the file name into variable
		// $file = 'filename.pdf';
		// $filename = 'filename.pdf';
		$path = "./directory/" . $parent_name . "/" . $id;
		// Header content type
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="' . $id . '"');
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		// Read the file
		@readfile(base_url($path));
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
				$users = $this->db->get_where('users')->result();

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
								<td align='center'>" . $ArrUsers[$file->prepared_by]->full_name . "</td>
								<td align='center'>" . $ArrUsers[$file->reviewed_by]->full_name . "</td>
								<td align='center'>" . $ArrUsers[$file->approved_by]->full_name . "</td>
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
