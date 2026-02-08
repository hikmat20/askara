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
		$dataDraft		= $this->WiModel->getAll();
		$dataCorrection = $this->WiModel->getAllByStatus('COR');
		$dataReview		= $this->WiModel->getAllByStatus('REV');
		$dataApproval	= $this->WiModel->getAllByStatus('APV');
		$dataRevision	= $this->WiModel->getAllByStatus('RVI');
		$dataPublished	= $this->WiModel->getAllByStatus('PUB');

		$this->template->render('index', compact(
			'dataDraft',
			'dataCorrection',
			'dataReview',
			'dataApproval',
			'dataRevision',
			'dataPublished'
		));
	}

	public function add()
	{
		$departements = $this->db->get_where('departements', ['status' => '1'])->result_array();
		$user         = $this->UserModel->find($this->auth->user_id());
		$positions    = $this->PositionModel->find_all();
		$procedures   = $this->ProcedureModel->as_array()->find_all_by('status !=', 'DEL');

		$this->template->title('Add New Work Instruction');
		$this->template->render('add', compact('procedures','departements', 'user', 'positions'));
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
		$result = $this->WiModel->find_data('view_work_instructions', $id, 'id');
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
			if (isset($_FILES['form_file']) && $_FILES['form_file']['name'] == '') {
				$this->form_validation->set_rules('form_file', 'File Upload', 'required|trim');
			}
		}
		
		$this->form_validation->set_message('required', '{field} tiidak boleh kosong');
		if ($this->form_validation->run() === FALSE) {
			echo json_encode([
				'status' => 0,
				'errors' => $this->form_validation->error_array()
			]);
			return;
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
		$this->_validation();
		$Return = $this->WiModel->saveData();
		echo json_encode($Return);
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
		$Return = $this->WiModel->reviewProcess();
		echo json_encode($Return);
	}


	public function form_review($id)
	{
		$this->load->view('form_review', ['id' => $id]);
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

		$Return = $this->WiModel->saveReview();
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

		$Return = $this->WiModel->saveApprove();
		echo json_encode($Return);
	}
}
