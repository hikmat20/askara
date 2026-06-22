<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Work_instructions extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('download');
		$this->load->library(array('upload', 'Image_lib'));
		$this->load->model(array(
			'Aktifitas/aktifitas_model'
		));
		$this->load->model('Work_instruction_model', 'WiModel');
		$this->load->model('Users_model', 'UserModel');
		$this->load->model('Positions/Position_model', 'PositionModel');
		$this->load->model('procedures/Procedure_model', 'ProcedureModel');

		$this->template->title('Work Instructions');
		$this->template->page_icon('fas fa-tasks');

		date_default_timezone_set("Asia/Bangkok");
	}

	public function index()
	{
		$dataDraft		= $this->WiModel->getAllByStatus('DFT');
		$dataCorrection = $this->WiModel->getAllByStatus('COR');
		$dataReview		= $this->WiModel->getAllByStatus('REV');
		$dataApproval	= $this->WiModel->getAllByStatus('APV');
		$dataRevision	= $this->WiModel->getAllByStatus('RVI');
		$dataPublished	= $this->WiModel->getAllByStatus('PUB');
		
		$status = [
			'DFT' => '<span class="badge badge-light">Draft</span>',
			'COR' => '<span class="badge badge-warning">Correction</span>',
			'REV' => '<span class="badge badge-info">Review</span>',
			'APV' => '<span class="badge badge-success">Approval</span>',
			'RVI' => '<span class="badge badge-danger">Revision</span>',
			'PUB' => '<span class="badge badge-primary">Published</span>',
		];

		$this->template->render('index', compact(
			'dataDraft',
			'dataCorrection',
			'dataReview',
			'dataApproval',
			'dataRevision',
			'dataPublished',
			'status'
		));
	}

	public function add()
	{
		$departements = $this->db->get_where('departements', ['status' => '1'])->result_array();
		$user         = $this->UserModel->find($this->auth->user_id());
		$positions    = $this->PositionModel->find_all();
		$procedures   = $this->ProcedureModel->as_array()->find_all_by('status !=', 'DEL');

		$this->template->title('Add New Work Instruction');
		$this->template->render('add', compact('procedures', 'departements', 'user', 'positions'));
	}

	public function edit($id = '')
	{
		$this->template->title('Edit Work Instruction');
		$this->template->page_icon('fa fa-edit');

		$dataWi      = $this->WiModel->find_data('view_work_instructions', $id, 'id');

		$departements  = $this->db->get_where('departements', ['status' => '1'])->result_array();
		$user          = $this->UserModel->find($this->auth->user_id());
		$positions     = $this->PositionModel->find_all();
		$procedures    = $this->ProcedureModel->as_array()->find_all_by('status !=', 'DEL');

		$this->template->render('edit', compact('dataWi', 'departements', 'user', 'positions', 'procedures'));
	}

	public function view($id = '')
	{
		// Get work instruction data
		$wi = $this->db->get_where('view_work_instructions', ['id' => $id])->row();
		
		if (!$wi) {
			show_404();
			return;
		}

		// Get status logs with user names
		$this->db->select('wsl.*, u.full_name as action_by_name');
		$this->db->from('work_instruction_status_logs wsl');
		$this->db->join('users u', 'u.id_user = wsl.action_by', 'left');
		$this->db->where('wsl.work_instruction_id', $id);
		$this->db->order_by('wsl.action_at', 'DESC');
		$status_logs = $this->db->get()->result();

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

		// Get version history using getVersionHistory()
		$version_history = $this->WiModel->getVersionHistory($id);

		// Get current version to display (handles under revision scenario)
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

		// Status labels
		$sts = [
			'DFT' => '<span class="badge badge-light">Draft</span>',
			'COR' => '<span class="badge badge-warning">Correction</span>',
			'REV' => '<span class="badge badge-info">Review</span>',
			'APV' => '<span class="badge badge-success">Approval</span>',
			'RVI' => '<span class="badge badge-danger">Revision</span>',
			'PUB' => '<span class="badge badge-primary">Published</span>',
		];

		$this->template->render('view', compact('wi', 'status_logs', 'sts', 'version_history'));
	}

	/**
	 * View work instruction in modal (for Published tab)
	 * Returns HTML partial for modal body
	 * 
	 * @param int $id Work instruction ID
	 * @return void
	 */
	public function view_modal($id = '')
	{
		// Get work instruction data
		$wi = $this->db->get_where('view_work_instructions', ['id' => $id, 'company_id' => $this->company])->row();
		
		if (!$wi) {
			echo json_encode(['status' => 0, 'msg' => 'Work Instruction tidak ditemukan atau Anda tidak memiliki akses.']);
			return;
		}

		// Get status logs with user names
		$this->db->select('wsl.*, u.full_name as action_by_name');
		$this->db->from('work_instruction_status_logs wsl');
		$this->db->join('users u', 'u.id_user = wsl.action_by', 'left');
		$this->db->where('wsl.work_instruction_id', $id);
		$this->db->order_by('wsl.action_at', 'DESC');
		$status_logs = $this->db->get()->result();

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

		// VERSION CONTROL: Get version history
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

		// Status labels
		$sts = [
			'DFT' => '<span class="badge badge-light">Draft</span>',
			'COR' => '<span class="badge badge-warning">Correction</span>',
			'REV' => '<span class="badge badge-info">Review</span>',
			'APV' => '<span class="badge badge-success">Approval</span>',
			'RVI' => '<span class="badge badge-danger">Revision</span>',
			'PUB' => '<span class="badge badge-primary">Published</span>',
		];

		// Get user positions for access control (PM/MR check)
		$ArrPosts = [];
		if (isset($this->ArrPosts)) {
			$ArrPosts = $this->ArrPosts;
		}

		// Load view partial (without template wrapper)
		$this->load->view('view_modal', compact('wi', 'status_logs', 'sts', 'version_history', 'ArrPosts'));
	}

	/**
	 * View specific version of work instruction
	 * 
	 * URL: work_instructions/view_version/{id}/{version}
	 * 
	 * @param int $id Work instruction ID
	 * @param int $version Version number to view
	 * @return void
	 */
	public function view_version($id = null, $version = null)
	{
		// Validate parameters
		if ($id === null || $version === null) {
			show_404();
			return;
		}

		// Check company isolation - verify work instruction belongs to user's company
		if (!$this->_checkCompanyIsolation($id)) {
			// Return 403 Forbidden
			$this->output->set_status_header(403);
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: You do not have permission to access this document.'
			]);
			return;
		}

		// Get version data from work_instruction_versions using getVersionByNumber()
		$version_data = $this->WiModel->getVersionByNumber($id, $version);

		// Check if version exists
		if (!$version_data) {
			// Return 404 Not Found
			$this->output->set_status_header(404);
			echo json_encode([
				'status' => 0,
				'msg' => 'Version not found: The requested version does not exist.'
			]);
			return;
		}

		// Validate file exists on server
		$file_path = FCPATH . $version_data->file_path;
		if (!file_exists($file_path)) {
			// Return 404 Not Found
			$this->output->set_status_header(404);
			log_message('error', 'Version file not found: ' . $version_data->file_path . ' for work_instruction_id: ' . $id . ', version: ' . $version);
			echo json_encode([
				'status' => 0,
				'msg' => 'File not found: The document file may have been deleted or moved.'
			]);
			return;
		}

		// Load view with file preview
		$this->load->view('version_modal', ['version' => $version_data]);
	}

	/**
	 * Download specific version of work instruction
	 * 
	 * URL: work_instructions/download_version/{id}/{version}
	 * 
	 * @param int $id Work instruction ID
	 * @param int $version Version number to download
	 * @return void
	 */
	public function download_version($id = null, $version = null)
	{
		// Validate parameters
		if ($id === null || $version === null) {
			show_404();
			return;
		}

		// Check company isolation - verify work instruction belongs to user's company
		if (!$this->_checkCompanyIsolation($id)) {
			// Return 403 Forbidden
			$this->output->set_status_header(403);
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: You do not have permission to access this document.'
			]);
			return;
		}

		// Get version data from work_instruction_versions using getVersionByNumber()
		$version_data = $this->WiModel->getVersionByNumber($id, $version);

		// Check if version exists
		if (!$version_data) {
			// Return 404 Not Found
			$this->output->set_status_header(404);
			echo json_encode([
				'status' => 0,
				'msg' => 'Version not found: The requested version does not exist.'
			]);
			return;
		}

		// Get full file path on server
		$file_path = FCPATH . $version_data->file_path;

		// Validate file exists on server
		if (!file_exists($file_path)) {
			// Return 404 Not Found
			$this->output->set_status_header(404);
			log_message('error', 'Version file not found: ' . $version_data->file_path . ' for work_instruction_id: ' . $id . ', version: ' . $version);
			echo json_encode([
				'status' => 0,
				'msg' => 'File not found: The document file may have been deleted or moved.'
			]);
			return;
		}

		// Set proper headers for force download
		$this->load->helper('download');
		
		// Read file data
		$file_data = file_get_contents($file_path);
		
		// Force download with proper filename
		force_download($version_data->file_name, $file_data);
	}

	/**
	 * Download active/current version of work instruction
	 * 
	 * URL: work_instructions/download/{id}
	 * 
	 * @param int $id Work instruction ID
	 * @return void
	 */
	public function download($id = null)
	{
		if ($id === null) {
			show_404();
			return;
		}

		// Check company isolation - verify work instruction belongs to user's company
		if (!$this->_checkCompanyIsolation($id)) {
			$this->output->set_status_header(403);
			show_error('Access Denied: You do not have permission to access this document.', 403);
			return;
		}

		$wi = $this->db->get_where('view_work_instructions', ['id' => $id])->row();
		if (!$wi) {
			show_404();
			return;
		}

		// VERSION CONTROL: Get current version to display (handles under revision scenario)
		$current_version = $this->WiModel->getCurrentVersion($id);
		
		$file_name = '';
		$file_path = '';

		if ($current_version && isset($current_version->is_from_history) && $current_version->is_from_history) {
			$file_name = $current_version->file_name;
			$file_path = $current_version->file_path;
		} else {
			$file_name = $wi->file_name;
			$file_path = $wi->file_path;
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

		if (!$this->input->post('id')) {
			if (isset($_FILES['form_file']) && $_FILES['form_file']['name'] == '') {
				$this->form_validation->set_rules('form_file', 'File Upload', 'required|trim');
			}
		}

		$this->form_validation->set_message('required', '{field} tidak boleh kosong');
		if ($this->form_validation->run() === FALSE) {
			return ([
				'status' => 0,
				'errors' => $this->form_validation->error_array()
			]);
		}
		return ([
			'status' => 1
		]);
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

		$result = $this->_validation();
		if ($result['status'] == 0) {
			echo json_encode($result);
			return;
		}

		$Return = $this->WiModel->saveData();
		echo json_encode($Return);
	}

	/**
	 * Verify work instruction belongs to user's company
	 * 
	 * @param int $work_instruction_id Work instruction ID
	 * @return bool True if belongs to user's company, false otherwise
	 */
	private function _checkCompanyIsolation($work_instruction_id)
	{
		$work_instruction = $this->db->get_where('work_instructions', ['id' => $work_instruction_id])->row();
		
		if (!$work_instruction) {
			return false;
		}
		
		// Check if work instruction belongs to user's company
		return ($work_instruction->company_id == $this->company);
	}

	public function process_to_review()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		// Check company isolation
		$id = $this->input->post('id');
		if (!$this->_checkCompanyIsolation($id)) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: Work instruction tidak ditemukan atau tidak dapat diakses.'
			]);
			return;
		}

		$Return = $this->WiModel->reviewProcess();
		echo json_encode($Return);
	}

	public function cancel_review()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		// Check company isolation
		$id = $this->input->post('id');
		if (!$this->_checkCompanyIsolation($id)) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: Work instruction tidak ditemukan atau tidak dapat diakses.'
			]);
			return;
		}

		$Return = $this->WiModel->cancelReview();
		echo json_encode($Return);
	}

	public function correction_to_review()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		// Check company isolation
		$id = $this->input->post('id');
		if (!$this->_checkCompanyIsolation($id)) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: Work instruction tidak ditemukan atau tidak dapat diakses.'
			]);
			return;
		}

		$Return = $this->WiModel->correctionToReview();
		echo json_encode($Return);
	}


	public function form_review($id)
	{
		$this->load->view('form_review', ['id' => $id]);
	}

	public function saveReview()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		// Check company isolation
		$id = $this->input->post('id');
		if (!$this->_checkCompanyIsolation($id)) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: Work instruction tidak ditemukan atau tidak dapat diakses.'
			]);
			return;
		}

		$this->load->library('form_validation');
		$postData = $this->input->post();

		// Validasi field id dan status wajib diisi
		$this->form_validation->set_rules('id', 'ID', 'required|trim');
		$this->form_validation->set_rules('status', 'Status Review', 'required|trim|in_list[APV,COR]', [
			'in_list' => '{field} harus APV atau COR'
		]);

		// Validasi kondisional field note untuk status COR
		if (isset($postData['status']) && $postData['status'] === 'COR') {
			$this->form_validation->set_rules('note', 'Note', 'required|trim', [
				'required' => '{field} wajib diisi jika status adalah COR'
			]);
		}

		$this->form_validation->set_message('required', '{field} tidak boleh kosong');
		if ($this->form_validation->run() === FALSE) {
			echo json_encode([
				'status' => 0,
				'errors' => $this->form_validation->error_array()
			]);
			return;
		}

		$Return = $this->WiModel->saveReview();
		echo json_encode($Return);
	}

	public function form_approval($id)
	{
		$this->load->view('form_approval', ['id' => $id]);
	}

	public function saveApprove()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		$this->load->library('form_validation');
		$postData = $this->input->post();

		// Validasi field id wajib diisi
		$this->form_validation->set_rules('id', 'Work Instruction ID', 'required|trim');

		// Validasi field status wajib diisi
		$this->form_validation->set_rules(
			'status',
			'Action Approval',
			'required|trim|in_list[PUB,COR]',
			[
				'required' => 'Pilih minimal satu Action',
				'in_list' => 'Status harus PUB atau COR'
			]
		);

		// Validasi kondisional: published_date wajib untuk status PUB
		if (isset($postData['status']) && $postData['status'] === 'PUB') {
			$this->form_validation->set_rules('published_date', 'Published Date', 'required|trim');
		}

		// Validasi kondisional: note wajib untuk status COR
		if (isset($postData['status']) && $postData['status'] === 'COR') {
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

		$Return = $this->WiModel->saveApprove();
		echo json_encode($Return);
	}

	/**
	 * Request revision for published work instruction (PUB -> RVI)
	 * 
	 * @return void
	 */
	public function request_revision()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		// Get work instruction ID from POST
		$id = $this->input->post('id');

		// Check company isolation
		if (!$this->_checkCompanyIsolation($id)) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: Work instruction tidak ditemukan atau tidak dapat diakses.'
			]);
			return;
		}

		// Call model method to request revision
		try {
			$result = $this->WiModel->requestRevision($id);
			echo json_encode($result);
		} catch (Exception $e) {
			echo json_encode([
				'status' => 0,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Request deletion for published work instruction (PUB -> HLD with deletion_status)
	 * 
	 * @return void
	 */
	public function request_deletion()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		// Get work instruction ID from POST
		$id = $this->input->post('id');

		// Check company isolation
		if (!$this->_checkCompanyIsolation($id)) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: Work instruction tidak ditemukan atau tidak dapat diakses.'
			]);
			return;
		}

		// Call model method to request deletion
		try {
			$result = $this->WiModel->requestDeletion($id);
			echo json_encode($result);
		} catch (Exception $e) {
			echo json_encode([
				'status' => 0,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Load revision form modal
	 * 
	 * @param int $id Work instruction ID
	 * @return void
	 */
	public function load_wi_revision_form($id = '')
	{
		// Get work instruction data
		$wi = $this->db->get_where('view_work_instructions', ['id' => $id, 'company_id' => $this->company])->row();
		
		if (!$wi) {
			echo json_encode(['status' => 0, 'msg' => 'Work Instruction tidak ditemukan atau Anda tidak memiliki akses.']);
			return;
		}

		// Check if status is PUB
		if ($wi->status !== 'PUB') {
			echo json_encode(['status' => 0, 'msg' => 'Request revision hanya dapat dilakukan untuk dokumen dengan status Published (PUB).']);
			return;
		}

		// Load view partial (without template wrapper)
		$this->load->view('revision_form_modal', compact('wi'));
	}

	/**
	 * Load deletion form modal
	 * 
	 * @param int $id Work instruction ID
	 * @return void
	 */
	public function load_wi_deletion_form($id = '')
	{
		// Get work instruction data
		$wi = $this->db->get_where('view_work_instructions', ['id' => $id, 'company_id' => $this->company])->row();
		
		if (!$wi) {
			echo json_encode(['status' => 0, 'msg' => 'Work Instruction tidak ditemukan atau Anda tidak memiliki akses.']);
			return;
		}

		// Check if status is PUB
		if ($wi->status !== 'PUB') {
			echo json_encode(['status' => 0, 'msg' => 'Request deletion hanya dapat dilakukan untuk dokumen dengan status Published (PUB).']);
			return;
		}

		// Load view partial (without template wrapper)
		$this->load->view('deletion_form_modal', compact('wi'));
	}

	/**
	 * Save revision request with note (PUB -> RVI)
	 * 
	 * @return void
	 */
	public function save_wi_revision()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		// Get work instruction ID and note from POST
		$id = $this->input->post('id');
		$note = $this->input->post('note');

		// Validate note
		if (empty($note) || trim($note) === '') {
			echo json_encode([
				'status' => 0,
				'msg' => 'Alasan revisi wajib diisi.'
			]);
			return;
		}

		// Check company isolation
		if (!$this->_checkCompanyIsolation($id)) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: Work instruction tidak ditemukan atau tidak dapat diakses.'
			]);
			return;
		}

		// Call model method to request revision with note
		try {
			$result = $this->WiModel->requestRevision($id, $note);
			echo json_encode($result);
		} catch (Exception $e) {
			echo json_encode([
				'status' => 0,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Save deletion request with note (PUB -> HLD)
	 * 
	 * @return void
	 */
	public function save_wi_deletion()
	{
		if (!$this->input->is_ajax_request()) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}

		// Get work instruction ID and note from POST
		$id = $this->input->post('id');
		$note = $this->input->post('note');

		// Validate note
		if (empty($note) || trim($note) === '') {
			echo json_encode([
				'status' => 0,
				'msg' => 'Alasan penghapusan wajib diisi.'
			]);
			return;
		}

		// Check company isolation
		if (!$this->_checkCompanyIsolation($id)) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied: Work instruction tidak ditemukan atau tidak dapat diakses.'
			]);
			return;
		}

		// Call model method to request deletion with note
		try {
			$result = $this->WiModel->requestDeletion($id, $note);
			echo json_encode($result);
		} catch (Exception $e) {
			echo json_encode([
				'status' => 0,
				'msg' => $e->getMessage()
			]);
		}
	}
}
