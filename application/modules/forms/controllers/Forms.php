<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Forms extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('download');
		$this->load->library(array('upload', 'Image_lib'));
		$this->load->model(array(
			'Aktifitas/aktifitas_model'
		));
		$this->load->model('Form_model', 'FormModel');
		$this->load->model('Users_model', 'UserModel');
		$this->load->model('Positions/Position_model', 'PositionModel');
		$this->load->model('procedures/Procedure_model', 'ProcedureModel');

		$this->template->title('Forms');
		$this->template->page_icon('fab fa-wpforms');

		date_default_timezone_set("Asia/Bangkok");
	}

	public function index()
	{
		$dataDraft		= $this->FormModel->getAllByStatus('DFT');
		$dataCorrection		= $this->FormModel->getAllByStatus('COR');
		$dataReview		= $this->FormModel->getAllByStatus('REV');
		$dataApproval		= $this->FormModel->getAllByStatus('APV');
		$dataRevision		= $this->FormModel->getAllByStatus('RVI');
		$dataPublished		= $this->FormModel->getAllByStatus('PUB');

		$status = [
			'DFT' => '<span class="badge badge-light">Draft</span>',
			'COR' => '<span class="badge badge-warning">Correction</span>',
			'REV' => '<span class="badge badge-info">Review</span>',
			'APV' => '<span class="badge badge-success">Approval</span>',
			'RVI' => '<span class="badge badge-danger">Revision</span>',
			'PUB' => '<span class="badge badge-primary">Published</span>',
		];

		$this->template->set('allow_download', $this->_check_download_permission('forms'));
		$this->template->render('index', compact(
			'dataDraft',
			'dataReview',
			'dataCorrection',
			'dataApproval',
			'dataRevision',
			'dataPublished',
			'status'
		));
	}

	public function add()
	{
		$departements = $this->db->get_where('departements', ['status' => '1'])->result_array();
		$users         = $this->UserModel->find_all_by('status', 'ACT');
		$positions    = $this->PositionModel->find_all();
		$procedures   = $this->ProcedureModel->as_array()->find_all_by('status !=', 'DEL');

		$user_id = $this->auth->user_id();
		$user_positions = $this->db->get_where('user_positions', ['user_id' => $user_id])->result();
		$default_prepared_id = '';
		if (count($user_positions) == 1) {
			$default_prepared_id = $user_positions[0]->position_id;
		}

		$this->template->title('Add New Form');
		$this->template->render('add', compact('departements', 'users', 'positions', 'procedures', 'default_prepared_id'));
	}

	public function edit($id = '')
	{
		$this->template->title('Edit Form');
		$this->template->page_icon('fa fa-edit');

		$dataForm      = $this->FormModel->find_data('view_forms', $id, 'id');

		// BUG-02: Cek otorisasi — pastikan record milik company yang login
		if (!$dataForm || $dataForm->company_id != $this->company) {
			show_error('Anda tidak memiliki akses ke data ini.', 403);
			return;
		}

		$departements  = $this->db->get_where('departements', ['status' => '1'])->result_array();
		$users         = $this->UserModel->find_all_by('status', 'ACT');
		// $user          = $this->UserModel->find($this->auth->user_id());
		$positions     = $this->PositionModel->find_all();
		$procedures    = $this->ProcedureModel->as_array()->find_all_by('status !=', 'DEL');

		$this->template->render('edit', compact('dataForm', 'departements', 'users', 'positions', 'procedures'));
	}

	public function view($id = '')
	{
		$dataForm = $this->FormModel->find_data('view_forms', $id, 'id');

		if (!$dataForm || $dataForm->company_id != $this->company) {
			show_error('Anda tidak memiliki akses ke data ini.', 403);
			return;
		}

		// VERSION CONTROL: Get dynamic current version to display
		$display_form = $this->FormModel->getCurrentVersion($id);
		
		// Get version history
		$version_history = $this->FormModel->getVersionHistory($id);

		// Get status logs
		$this->db->select('fsl.*, u.full_name as action_by_name');
		$this->db->from('form_status_logs fsl');
		$this->db->join('users u', 'fsl.action_by = u.id_user', 'left');
		$this->db->where('fsl.form_id', $id);
		$this->db->order_by('fsl.action_at', 'ASC');
		$status_logs = $this->db->get()->result();

		// Fetch names for reviewed_by and approved_by
		$reviewed_by_name = '-';
		if ($dataForm->reviewed_by) {
			$rev_user = $this->db->get_where('users', ['id_user' => $dataForm->reviewed_by])->row();
			if ($rev_user) $reviewed_by_name = $rev_user->full_name;
		}
		
		$approved_by_name = '-';
		if ($dataForm->approved_by) {
			$apv_user = $this->db->get_where('users', ['id_user' => $dataForm->approved_by])->row();
			if ($apv_user) $approved_by_name = $apv_user->full_name;
		}

		$sts = [
			'DFT' => '<span class="badge badge-light">Draft</span>',
			'COR' => '<span class="badge badge-warning">Correction</span>',
			'REV' => '<span class="badge badge-info">Review</span>',
			'APV' => '<span class="badge badge-success">Approval</span>',
			'RVI' => '<span class="badge badge-danger">Revision</span>',
			'PUB' => '<span class="badge badge-primary">Published</span>',
			'HLD' => '<span class="badge badge-warning">On Hold</span>',
			'DEL' => '<span class="badge badge-secondary">Deleted</span>',
		];

		$this->template->set('allow_download', $this->_check_download_permission('forms'));
		$this->template->render('view', compact(
			'dataForm',
			'display_form',
			'version_history',
			'status_logs',
			'reviewed_by_name',
			'approved_by_name',
			'sts'
		));
	}

	private function _validation()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Document Name', 'required|trim');
		$this->form_validation->set_rules('departement_id', 'Departement', 'required|trim');
		$this->form_validation->set_rules('number', 'Number', 'required|trim');
		$this->form_validation->set_rules('procedure_id', 'Procedure', 'required|trim');
		$this->form_validation->set_rules('is_active', 'Status Active', 'required|trim');
		$this->form_validation->set_rules('issue_date', 'Issue Date', 'required|trim');
		$this->form_validation->set_rules('effective_date', 'Effective Date', 'required|trim');
		$this->form_validation->set_rules('revision_number', 'Revision Number', 'required|trim');
		$this->form_validation->set_rules('reviewer_position_id', 'PIC Reviewer', 'required|trim|integer');
		$this->form_validation->set_rules('approver_position_id', 'PIC Approver', 'required|trim|integer');

		if (!$this->input->post('id')) {
			if (!$this->input->post('form_type')) {
				$this->form_validation->set_rules('form_type', 'Form Type', 'required|trim');
			}
			if (!isset($_FILES['form_file']) || $_FILES['form_file']['name'] == '') {
				$this->form_validation->set_rules('form_file', 'File Upload', 'required|trim');
			}
			if ($this->input->post('link_form')) {
				$this->form_validation->set_rules('link_form', 'Link online Form', 'required|trim');
			}
		}


		$this->form_validation->set_message('required', '{field} tidak boleh kosong');
		if ($this->form_validation->run() === FALSE) {
			return ['errors' => $this->form_validation->error_array()];
		}

		return false; // validasi sukses, tidak ada error
	}

	public function save()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		// Handler khusus jika file melebihi upload_max_filesize di php.ini
		if (isset($_FILES['form_file']['error']) && $_FILES['form_file']['error'] == UPLOAD_ERR_INI_SIZE) {
			$maxSize = ini_get('upload_max_filesize');
			echo json_encode([
				'status' => 0,
				'msg' => "Ukuran file form terlalu besar, melebihi konfigurasi server (Maksimal: {$maxSize})."
			]);
			return;
		}

		// Handler khusus jika request POST melebihi kapasitas post_max_size di php.ini
		if (empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
			$maxSize = ini_get('post_max_size');
			echo json_encode([
				'status' => 0,
				'msg' => "Total beban payload/file terlalu besar melebihi kapasitas memori request (Maksimal: {$maxSize})."
			]);
			return;
		}

		$validated = $this->_validation();
		if ($validated) {
			echo json_encode([
				'status' => 0,
				'errors' => $validated['errors']
			]);
			return;
		}

		// BUG-02: Cek otorisasi untuk update
		if ($this->input->post('id')) {
			$existingRecord = $this->FormModel->find_data('view_forms', $this->input->post('id'), 'id');
			if (!$existingRecord || $existingRecord->company_id != $this->company) {
				echo json_encode(['status' => 0, 'msg' => 'Akses ditolak.']);
				return;
			}
		}

		// Validasi silang: reviewer dan approver tidak boleh sama
		$reviewer_id = (int) $this->input->post('reviewer_position_id');
		$approver_id = (int) $this->input->post('approver_position_id');
		if ($reviewer_id && $approver_id && $reviewer_id === $approver_id) {
			echo json_encode([
				'status' => 0,
				'errors' => [
					'approver_position_id' => 'PIC Approver tidak boleh sama dengan PIC Reviewer.',
				],
			]);
			return;
		}

		$Return = $this->FormModel->saveData();
		
		echo json_encode($Return);
	}

	public function form_review($id)
	{
		$this->load->view('form_review', ['id' => $id]);
	}

	public function process_to_review()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode(['status' => 0, 'msg' => 'Access Denied']);
			return;
		}
		$Return = $this->FormModel->reviewProcess();
		echo json_encode($Return);
	}

	public function cancel_review()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode(['status' => 0, 'msg' => 'Access Denied']);
			return;
		}
		$Return = $this->FormModel->cancelReview();
		echo json_encode($Return);
	}

	public function correction_to_review()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode(['status' => 0, 'msg' => 'Access Denied']);
			return;
		}
		$Return = $this->FormModel->correctionToReview();
		echo json_encode($Return);
	}

	public function saveReview()
	{

		$this->load->library('form_validation');
		$postDatat = $this->input->post();

		$this->form_validation->set_rules('status', 'Status Review', 'required|trim');
		if (isset($postDatat['note']) && $postDatat['note'] == '') {
			$this->form_validation->set_rules('note', 'Note', 'required|trim');
		}

		$this->form_validation->set_message('required', '{field} tidak boleh kosong');
		if ($this->form_validation->run() === FALSE) {
			echo json_encode([
				'status' => 0,
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}

		$Return = $this->FormModel->saveReview();
		echo json_encode($Return);
	}

	public function form_approval($id)
	{
		$this->load->view('form_approval', ['id' => $id]);
	}

	public function saveApprove()
	{
		$this->load->library('form_validation');
		$postDatat = $this->input->post();

		$this->form_validation->set_rules(
			'status',
			'Action Approval',
			'required',
			[
				'required' => 'Pilih minimal satu Action'
			]
		);

		if ($postDatat['status'] === 'PUB') {
			$this->form_validation->set_rules('published_date', 'Published Date', 'required|trim');
		}

		if ($postDatat['status'] === 'COR') {
			$this->form_validation->set_rules('note', 'Note', 'required|trim');
		}

		$this->form_validation->set_message('required', '{field} tidak boleh kosong');
		if ($this->form_validation->run() === FALSE) {
			echo json_encode([
				'status' => 0,
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}

		$Return = $this->FormModel->saveApprove();
		echo json_encode($Return);
	}

	public function delete()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode(['status' => 0, 'msg' => 'Access Denied']);
			return;
		}
		$id = $this->input->post('id');
		$Return = $this->FormModel->deleteData($id);
		echo json_encode($Return);
	}

	/**
	 * View specific version of form
	 * 
	 * URL: forms/view_version/{id}/{version}
	 */
	public function view_version($id = null, $version = null)
	{
		if ($id === null || $version === null) {
			show_404();
			return;
		}

		if (!$this->_checkCompanyIsolation($id)) {
			$this->output->set_status_header(403);
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: You do not have permission to access this document.'
			]);
			return;
		}

		$version_data = $this->FormModel->getVersionByNumber($id, $version);

		if (!$version_data) {
			$this->output->set_status_header(404);
			echo json_encode([
				'status' => 0,
				'msg' => 'Version not found: The requested version does not exist.'
			]);
			return;
		}

		$file_path = FCPATH . $version_data->file_path;
		if (!file_exists($file_path)) {
			$this->output->set_status_header(404);
			log_message('error', 'Version file not found: ' . $version_data->file_path . ' for form_id: ' . $id . ', version: ' . $version);
			echo json_encode([
				'status' => 0,
				'msg' => 'File not found: The document file may have been deleted or moved.'
			]);
			return;
		}

		$this->load->view('version_modal', ['version' => $version_data]);
	}

	/**
	 * Download specific version of form
	 * 
	 * URL: forms/download_version/{id}/{version}
	 */
	public function download_version($id = null, $version = null)
	{
		if ($id === null || $version === null) {
			show_404();
			return;
		}

		if (!$this->_check_download_permission('forms')) {
			$this->output->set_status_header(403);
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: You do not have permission to download this document.'
			]);
			return;
		}

		if (!$this->_checkCompanyIsolation($id)) {
			$this->output->set_status_header(403);
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: You do not have permission to access this document.'
			]);
			return;
		}

		$version_data = $this->FormModel->getVersionByNumber($id, $version);

		if (!$version_data) {
			$this->output->set_status_header(404);
			echo json_encode([
				'status' => 0,
				'msg' => 'Version not found: The requested version does not exist.'
			]);
			return;
		}

		$file_path = FCPATH . $version_data->file_path;

		if (!file_exists($file_path)) {
			$this->output->set_status_header(404);
			log_message('error', 'Version file not found: ' . $version_data->file_path . ' for form_id: ' . $id . ', version: ' . $version);
			echo json_encode([
				'status' => 0,
				'msg' => 'File not found: The document file may have been deleted or moved.'
			]);
			return;
		}

		$this->load->helper('download');
		$file_data = file_get_contents($file_path);
		force_download($version_data->file_name, $file_data);
	}

	/**
	 * Download active/current version of form
	 * 
	 * URL: forms/download/{id}
	 * 
	 * @param int $id Form ID
	 * @return void
	 */
	public function download($id = null)
	{
		if ($id === null) {
			show_404();
			return;
		}

		if (!$this->_check_download_permission('forms')) {
			$this->output->set_status_header(403);
			show_error('Access Denied: You do not have permission to download this document.', 403);
			return;
		}

		if (!$this->_checkCompanyIsolation($id)) {
			$this->output->set_status_header(403);
			show_error('Access Denied: You do not have permission to access this document.', 403);
			return;
		}

		$form = $this->FormModel->find_data('view_forms', $id, 'id');
		if (!$form) {
			show_404();
			return;
		}

		// VERSION CONTROL: Get dynamic current version to display
		$display_form = $this->FormModel->getCurrentVersion($id);
		if (!$display_form) {
			show_404();
			return;
		}

		if ($display_form->form_type !== 'upload_file') {
			// If form is link based, redirect to it
			if (filter_var($display_form->link_form, FILTER_VALIDATE_URL)) {
				redirect($display_form->link_form);
				return;
			}
		}

		$file_name = '';
		$file_path = '';

		if (isset($display_form->is_from_history) && $display_form->is_from_history) {
			$file_name = $display_form->file_name;
			$file_path = $display_form->file_path;
		} else {
			$file_name = $display_form->file_name;
			$file_path = 'directory/FORMS/' . $form->company_id . '/' . $display_form->file_name;
		}

		if (!empty($file_name)) {
			// Get full file path on server
			$clean_path = ltrim($file_path, './');
			$full_path = FCPATH . $clean_path;

			// Validate file exists on server
			if (file_exists($full_path)) {
				$this->load->helper('download');
				$file_data = file_get_contents($full_path);
				force_download($file_name, $file_data);
				exit;
			}
		}

		show_error('File not found: The document file may have been deleted or moved.', 404);
	}

	private function _checkCompanyIsolation($form_id)
	{
		$form = $this->db->get_where('forms', ['id' => $form_id])->row();
		if (!$form) {
			return false;
		}
		return ($form->company_id == $this->company);
	}
}
