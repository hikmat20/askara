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

		$this->template->title('Add New Form');
		$this->template->render('add', compact('departements', 'users', 'positions', 'procedures'));
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
		// $this->load->library('OnlyOfficeJWT');

		$result = $this->FormModel->find_data('view_forms', $id, 'id');
		// $file_path = 'http://192.168.2.127:8080/askara/directory/FORMS/1/' . $result->file_name;

		// $payload = [
		// 	"document" => [
		// 		"fileType" => "xlsx",
		// 		"key" => $result->file_name,
		// 		"title" => $result->file_name,
		// 		"url" => $file_path
		// 	],
		// 	"documentType" => "cell",
		// 	"editorConfig" => [
		// 		"mode" => "view"
		// 	]
		// ];

		// $token           = $this->onlyofficejwt->generate($payload);
		// $data['payload'] = $payload;
		// // $data['token']   = $token;
		$this->template->render('view', $result);
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
			echo json_encode([
				'status' => 0,
				'msg' => 'Access Denied'
			]);
			return;
		}
		$Return = $this->FormModel->reviewProcess();
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
}
