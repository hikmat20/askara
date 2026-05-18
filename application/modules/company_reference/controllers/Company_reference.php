<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Syamsudin
 * @copyright Copyright (c) 2021, Syamsudin
 *
 * This is controller for Folders
 */

class Company_reference extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('download');
		$this->load->library(array('upload', 'Image_lib'));
		$this->load->model('company_reference/Company_reference_model', 'ReferenceModel');

		$this->template->set('title', 'Company Reference');
		$this->template->set('icon', 'fa fa-building');

		date_default_timezone_set("Asia/Bangkok");
	}

	public function index()
	{
		$data		= $this->db->get_where('view_references', ['status' => 'OPN', 'company_id' => $this->company])->result();
		$done		= $this->db->get_where('view_references', ['status' => 'DONE'])->result();
		$lsStd 		= $this->db->get_where('view_ref_standards')->result();
		$lsReg 		= $this->db->get_where('view_ref_regulations')->result();

		$ArrStd = [];
		foreach ($lsStd as $std) {
			$ArrStd[$std->reference_id][] = $std->name;
		}

		$ArrReg = [];
		foreach ($lsReg as $reg) {
			$ArrReg[$reg->reference_id][] = $reg->name;
		}

		$this->template->set([
			'data' 		=> $data,
			'done' 		=> $done,
			'ArrStd' 	=> $ArrStd,
			'ArrReg' 	=> $ArrReg,
		]);

		$this->template->render('index');
	}

	public function add()
	{
		$Companies = $this->db->get_where('companies', ['id_perusahaan' => $this->company])->result();
		$branch    = $this->db->get_where('company_branch', ['company_id' => $this->company])->result();

		$this->template->set([
			'title' 		=> 'Add Company Reference',
			'Companies' 	=> $Companies,
			'branch' 		=> $branch
		]);

		$this->template->render('add');
	}

	public function edit($id = '', $branch = null)
	{
		$Data 			= $this->db->get_where('view_references', ['id' => $id, 'status' => 'OPN'])->row();
		$datStd 		= $this->db->get_where('view_ref_standards', ['reference_id' => $id])->result();
		$companies 		= $this->db->get_where('companies')->result();
		$dataReg 		= $this->db->get_where('view_ref_regulations', ['reference_id' => $id, 'branch_id' => $branch])->result();
		$standards		= $this->db->get_where('requirements', ['status' => '1'])->result();
		$regulations	= $this->db->get_where('view_regulation_subjects', ['status' => 'PUB'])->result();
		$subjects 		= $this->db->get_where('view_compliance_subjects', ['company_id' => $this->company, 'status' => '1', 'branch_id' => $branch])->result();

		if ($regulations) foreach ($regulations as $v) {
			$ArrRegulation[$v->subject_id][$v->regulation_id] = $v->name;
		}

		$ArrReg = [];
		foreach ($dataReg as $reg) {
			$ArrReg[$reg->subject][] = $reg;
		}
print_r($Data);
die();
		$this->template->set([
			'title' 		=> 'Edit Company Reference',
			'Data' 			=> $Data,
			'datStd' 		=> $datStd,
			'dataReg' 		=> $dataReg,
			'Companies' 	=> $companies,
			'standards' 	=> $standards,
			'subjects' 		=> $subjects,
			'ArrReg' 		=> $ArrReg,
			'ArrRegulation' 	=> json_encode($ArrRegulation),
		]);
		$this->template->render('edit');
	}

	public function view($id = null, $branch = null)
	{
		if ($id) {
			$Data 			= $this->db->get_where('view_references', ['id' => $id, 'status' => 'OPN'])->row();
			$datStd 		= $this->db->get_where('view_ref_standards', ['reference_id' => $id])->result();
			$companies 		= $this->db->get_where('companies')->result();
			$subjects 		= $this->db->get_where('view_compliance_subjects', ['company_id' => $this->company, 'status' => '1', 'branch_id' => $branch])->result();
			$dataReg 		= $this->db->get_where('view_ref_regulations', ['reference_id' => $id])->result();
			$standards		= $this->db->get_where('requirements', ['status' => '1'])->result();
			$regulations	= $this->db->get_where('regulations', ['status' => 'PUB'])->result();

			if ($Data) {
				$this->template->set([
					'title' 		=> 'View Company Reference',
					'Data' 			=> $Data,
					'datStd' 		=> $datStd,
					'dataReg' 		=> $dataReg,
					'Companies' 	=> $companies,
					'standards' 	=> $standards,
					'subjects' 		=> $subjects,
					'regulations' 	=> $regulations,
				]);
				$this->template->render('view');
			} else {
				$data = [
					'heading' => 'Error!',
					'message' => 'Data not found..'
				];
				$this->template->render('../views/errors/html/error_404_custome', $data);
			}
		}
	}

	public function save()
	{
		$Data 		= $this->input->post();

		if ($Data) {
			$this->db->trans_begin();
			$saved 	= $this->ReferenceModel->saveData($Data);
			if (isset($saved['id'])) {
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$Return		= array(
						'status'		=> 0,
						'msg'			=> 'Data Chapter failed to save. Please try again.',
					);
				} else {
					$this->db->trans_commit();
					$Return		= array(
						'status'		=> 1,
						'msg'			=> 'Data Chapter successfully saved..',
						'id'			=> $saved['id']
					);
				}
			} else {
				$this->db->trans_rollback();
				$Return		= array(
					'status'		=> 0,
					'msg'			=> 'Data per perusahaan yang dipilih sudah ada atau duplikat.',
				);
			}
		} else {
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Data not valid, please try again!',
			);
		}

		echo json_encode($Return);
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if ($id) {
			$this->db->trans_begin();
			$this->db->delete('references', ['id' => $id]);
			$this->db->delete('ref_standards', ['reference_id' => $id]);
			$this->db->delete('cross_reference_details', ['reference_id' => $id]);
			$this->db->delete('ref_regulations', ['reference_id' => $id]);
			$this->db->delete('compliance_details', ['reference_id' => $id]);
			$this->db->delete('compliance_opports', ['reference_id' => $id]);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'    => 'Failed to delete data.. Please try again.',
				];
			} else {
				$this->db->trans_commit();
				$Return = [
					'status' => 1,
					'msg'    => 'Successfully deleted data..',
				];
			}
		} else {
			$Return = [
				'status' => 0,
				'msg'    => 'Data not valid. Please try again.',
			];
		}

		echo json_encode($Return);
	}

	public function delete_reg()
	{
		$id = $this->input->post('id');
		if ($id) {
			$this->db->trans_begin();
			$this->db->delete('ref_regulations', ['id' => $id]);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$Return = [
					'status' => 0,
					'msg'    => 'Failed to delete data.. Please try again.',
				];
			} else {
				$this->db->trans_commit();
				$Return = [
					'status' => 1,
					'msg'    => 'Successfully deleted data..',
				];
			}
		} else {
			$Return = [
				'status' => 0,
				'msg'    => 'Data not valid. Please try again.',
			];
		}

		echo json_encode($Return);
	}

	/* New Compliance */
	public function select_subjects($reference_id, $branch = null)
	{
		$axist_subjects = $this->db->get_where('compliance_subject', ['company_id' => $this->company, 'reference_id' => $reference_id])->result_array();
		$subjects	= $this->db->get('subjects')->result();
		$this->template->render('select_subjects', ['subjects' => $subjects, 'axist_subjects' => $axist_subjects,'branch' => $branch,
			'reference_id' => $reference_id]);
	}

	public function save_subject()
	{
		try {
			$post 		= $this->input->post();
		
			$data =
				[
					'subject_id' => $post['subject_id'],
					'reference_id' => $post['reference_id'],
					'company_id' => $this->company,
					'created_at' => date('Y-m-d H:i:s'),
					'created_by' => $this->auth->user_id(),
				];

			if ($data) {
				$this->db->trans_begin();
				$this->db->insert('compliance_subject', $data);
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					throw new Exception("Subject Regulation Compliance failed to save. Please try again.", 1);
					
				} else {
					$this->db->trans_commit();
					$Return		= array(
						'status' => 1,
						'msg'	 => 'Subject Regulation Compliance successfully saved..',
					);
				}
			} else {
				throw new Exception("Subject Regulation Compliance failed to save. Please try again.", 1);
			}
		} catch (Exception $e) {
			$this->db->trans_rollback();
			$Return		= array(
				'status' => 0,
				'msg'	 => $e->getMessage(),
			);
		}
		echo json_encode($Return);
	}
}
