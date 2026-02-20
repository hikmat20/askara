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
		$dataDraft		= $this->FormModel->getAll();

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
			if (isset($_FILES['form_file']) && $_FILES['form_file']['name'] == '') {
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
		$validated = $this->_validation();
		if ($validated) {
			echo json_encode([
				'status' => 0,
				'errors' => $validated['errors']
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

		$this->form_validation->set_message('required', '{field} tiidak boleh kosong');
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

		if (isset($postDatat['published_date']) && $postDatat['published_date'] == '') {
			$this->form_validation->set_rules('published_date', 'Published Date', 'required|trim');
		}

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

		$Return = $this->FormModel->saveApprove();
		echo json_encode($Return);
	}
}
