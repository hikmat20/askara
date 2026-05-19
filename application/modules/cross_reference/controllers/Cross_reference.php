<?php

use Mpdf\Tag\Details;
use Mpdf\Mpdf;

if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Syamsudin
 * @copyright Copyright (c) 2021, Syamsudin
 *
 * This is controller for Folders
 */

class Cross_reference extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('download');
		$this->load->library(array('upload', 'Image_lib'));
		$this->load->model(array(
			'Aktifitas/aktifitas_model'
		));

		$this->template->set('title', 'Cross References');
		$this->template->set('icon', 'fa fa-cog');

		date_default_timezone_set("Asia/Bangkok");
		$this->status = [
			'0' => '<span class="badge badge-danger">Invalid</span>',
			'1' => '<span class="badge badge-primary">Valid</span>'
		];
	}

	public function index()
	{
		$data		= $this->db->get_where('view_cross_references', ['company_id' => $this->company])->result();
		$this->template->set('data', $data);
		$this->template->set('company_id', $this->company);
		$this->template->set('status', $this->status);
		$this->template->render('index');
	}

	public function pasal_to_process()
	{
		$data		= $this->db->get_where('requirements', ['company_id' => $this->company, 'deleted_at' => null])->result();

		$this->template->set('title', 'Cross Reference (Pasal to Proses)');
		$this->template->set('data', $data);
		$this->template->set('status', $this->status);
		$this->template->render('pasal_to_proses');
	}

	public function process_to_pasal()
	{
		$data		= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL', 'deleted_at' => null])->result();

		$this->template->set('title', 'Cross Reference (Pasal to Proses)');
		$this->template->set('data', $data);
		$this->template->set('status', $this->status);
		$this->template->render('proses_to_pasal');
	}

	public function select_procedure($id = '')
	{
		// $Data 			= $this->db->get_where('view_cross_reference_details', ['procedure_id like' => "%$id%"])->result();

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

		$procedure 		= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();

		$this->template->set([
			'Data' 			=> $Data,
			'ArrData' 		=> $ArrData,
			'ArrStd' 		=> $ArrStd,
			'procedure' 	=> $procedure,
		]);

		$this->template->render('load_proses');
	}

	public function select_standard($id = '')
	{
		$Data 			= $this->db->get_where('requirements', ['company_id' => $this->company, 'id' => $id])->row();
		$Data_detail 	= $this->db->get_where('requirement_details', ['requirement_id' => $id])->result();
		$procedure 		= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL', 'deleted_by' => null])->result();

		$this->template->set('Data', $Data);
		$this->template->set('Data_detail', $Data_detail);
		$this->template->set('procedure', $procedure);
		$this->template->render('load_pasal');
	}

	public function cross_pasal($id = '')
	{
		$Detail 		= $ArrPro = [];
		$other_docs 	= '';

		$DataStd 		= $this->db->get_where('requirements', ['id' => $id])->row();
		$DetailStd 		= $this->db->get_where('requirement_details', ['requirement_id' => $id])->result_array();
		$crossData 			= $this->db->get_where('view_cross_references', ['company_id' => $this->company, 'standard_id' => $id])->row();

		if ($crossData) {
			$Detail 		= $this->db->get_where('view_cross_reference_details', ['requirement_id' => $crossData->standard_id, 'reference_id' => $crossData->id])->result_array();
			$combine = array_combine(array_column($Detail, 'chapter_id'), array_column($Detail, 'procedure_id'));
			foreach ($combine as $k => $com) {
				$ArrPro[$k] = explode(",", $com);
			}

			$other_docs = array_combine(array_column($Detail, 'chapter_id'), array_column($Detail, 'other_docs'));
		}

		$procedures 	= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result_array();

		$list_procedure = [];
		foreach ($procedures as $pro) {
			$list_procedure[$pro['id']] = "<span class='badge badge-success m-1'>" . $pro['name'] . "</span>";
		}
		$this->template->set([
			'DataStd' 			=> $DataStd,
			'Data' 				=> $crossData,
			'Detail' 			=> $Detail,
			'list_procedure' 	=> $list_procedure,
			'procedures' 		=> $procedures,
			'DetailStd' 		=> $DetailStd,
			'ArrPro' 			=> $ArrPro,
			'other_docs' 		=> $other_docs,
		]);
		$this->template->render('edit');
	}

	public function cross()
	{
		$Data 			= $this->db->get_where('requirements', ['status' => '1'])->result();

		$this->template->set([
			'Data' 				=> $Data,
		]);

		$this->template->render('cross');
	}

	public function view($id = '')
	{
		$Data 			= $this->db->get_where('view_cross_references', ['company_id' => $this->company, 'id' => $id])->row();
		$Detail 		= $this->db->get_where('requirement_details', ['requirement_id' => $Data->standard_id])->result();
		$DetailCross	= $this->db->get_where('view_cross_reference_details', ['reference_id' => $id])->result_array();
		$Procedure		= array_combine(array_column($DetailCross, 'chapter_id'), array_column($DetailCross, 'procedure_id'));
		$other_docs 	= array_combine(array_column($DetailCross, 'chapter_id'), array_column($DetailCross, 'other_docs'));

		$procedures 	= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();
		$status = [
			'PUB' => 'badge-success',
			'DFT' => 'badge-secondary',
			'APV' => 'badge-info',
			'REV' => 'badge-warning',
			'COR' => 'badge-danger',
			'DEL' => 'badge-light',
			'RVI' => 'badge-info',
			'HLD' => 'badge-default',
		];

		$list_procedure = [];
		$ArrProcedures = [];
		foreach ($procedures as $pro) {
			$list_procedure[$pro->id] = "<span style='cursor:pointer' title='View Process' class='badge view-procedure "  . $status[$pro->status] . " m-1' data-id='$pro->id'> " . $pro->name . "<sup> (". ($pro->status).")</sup></span>";
			$ArrProcedures[$pro->id] = $pro->status;
		}

		$this->template->set([
			'Data' 				=> $Data,
			'Detail' 			=> $Detail,
			'list_procedure' 	=> $list_procedure,
			'other_docs' 		=> $other_docs,
			'Procedure' 		=> $Procedure,
			'ArrProcedures' 	=> $ArrProcedures,
		]);

		$this->template->render('view_pasal_to_process');
	}

	public function view_pasal($id = '')
	{
		$Data 		= $this->db->get_where('requirement_details', ['id' => $id])->row();
		echo json_encode($Data);
	}

	public function save()
	{
		$Data 			= $this->input->post();
		$Detail 		= $this->input->post('detail');
		$id 			= ($Data['id']) ?: '';
		$requirement_id = ($Data['requirement_id']) ?: '';

		$this->db->trans_begin();
		unset($Data['id']);
		unset($Data['detail']);
		if ($Data && $Detail) {
			if ($Data) {
				$Data['company_id'] 	 = $this->company;
				if (($id)) {
					$Data['modified_by'] = $this->auth->user_id();
					$Data['modified_at'] = date('Y-m-d H:i:s');
					$this->db->update('cross_references', $Data, ['id' => $id]);
				} else {
					$Data['created_by'] = $this->auth->user_id();
					$Data['created_at'] = date('Y-m-d H:i:s');
					$this->db->insert('cross_references', $Data);
				}
			}

			if ($Detail) {
				$reference_id = $this->db->order_by('id', 'DESC')->get_where('cross_references')->row();
				foreach ($Detail as $dtl) :
					$dtlId 					= $dtl['id'];
					$dtl['reference_id'] 	= ($id) ?: $reference_id->id;
					$dtl['requirement_id'] 	= $requirement_id;
					$dtl['other_docs'] 		= (isset($dtl['other_docs'])) ? $dtl['other_docs'] : null;

					$procedure = '';
					if (isset($dtl['procedure_id'])) {
						$procedure 			= implode(',', $dtl['procedure_id']);
						$dtl['procedure_id'] = $procedure;
					} else {
						$dtl['procedure_id'] = $procedure;
					}

					if ((isset($dtl['id']) && $dtl['id'])) {
						$dtl['modified_by'] = $this->auth->user_id();
						$dtl['modified_at'] = date('Y-m-d H:i:s');
						$this->db->update('cross_reference_details', $dtl, ['id' => $dtl['id']]);
					} else if (($dtl['procedure_id']) || ($dtl['other_docs'])) {
						unset($dtl['id']);
						$dtl['company_id'] = $this->company;
						$dtl['created_by'] = $this->auth->user_id();
						$dtl['created_at'] = date('Y-m-d H:i:s');
						$this->db->insert('cross_reference_details', $dtl);
					}
				endforeach;
			}
		} else {
			$Return		= array(
				'status'		=> 0,
				'msg'			=> 'Data not valid. Please try again.',
			);
			echo json_encode($Return);
			return false;
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

	public function print_cross($id = null)
	{
		$mpdf = new \Mpdf\Mpdf([
			'margin_left' => 5,
			'margin_right' => 5,
			'margin_top' => 5,
			'margin_bottom' => 5
		]);

		$crossStd  		= $this->db->get_where('view_cross_references', ['id' => $id])->row();
		$dtlCross 		= $this->db->get_where('view_cross_reference_details', ['reference_id' => $id])->result();

		$procedures 	= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();
		$lsProcedure 	= [];
		$ArrDtlCross 	= [];
		$DataStd 		= [];

		foreach ($dtlCross as $dtl) {
			$ArrDtlCross[] = explode(",", $dtl->procedure_id);
		}


		foreach ($ArrDtlCross as $arr) {
			foreach ($arr as $value) {
				$lsProcedure[$value] = $value;
				if ($value) {
					$this->db->select('*')->from('view_cross_reference_details');
					$this->db->where("find_in_set($value, procedure_id)");
					$this->db->where("reference_id", $id);
					$this->db->where("company_id", $this->company);
					$DataStd[$value] = $this->db->get()->result();
				}
			}
		}
		$Data = [
			'crossStd' 		=> $crossStd,
			'DataStd' 		=> $DataStd,
			'procedures' 	=> $procedures,
			'lsProcedure' 	=> $lsProcedure,
		];

		$data = $this->load->view('printout', $Data, TRUE);
		$mpdf->WriteHTML($data);
		$mpdf->Output();
	}

	public function print_cross_pasal_to_process($id = null)
	{
		$mpdf = new \Mpdf\Mpdf([
			'margin_left' => 10,
			'margin_right' => 10,
			'margin_top' => 10,
			'margin_bottom' => 10
		]);

		$Data 			= $this->db->get_where('view_cross_references', ['company_id' => $this->company, 'id' => $id])->row();
		$Detail 		= $this->db->get_where('requirement_details', ['requirement_id' => $Data->standard_id])->result();
		$DetailCross	= $this->db->get_where('view_cross_reference_details', ['reference_id' => $id])->result_array();
		$Procedure		= array_combine(array_column($DetailCross, 'chapter_id'), array_column($DetailCross, 'procedure_id'));
		$other_docs 	= array_combine(array_column($DetailCross, 'chapter_id'), array_column($DetailCross, 'other_docs'));

		$procedures 	= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();
		$list_procedure = [];
		foreach ($procedures as $pro) {
			$list_procedure[$pro->id] = "<span class='badge badge-success m-1'>" . $pro->name . "</span>";
		}

		$this->template->set([
			'Data' 				=> $Data,
			'Detail' 			=> $Detail,
			'list_procedure' 	=> $list_procedure,
			'other_docs' 		=> $other_docs,
			'Procedure' 		=> $Procedure,
		]);

		$data = $this->template->load_view('printout', $Data, TRUE);
		$mpdf->WriteHTML($data);
		$mpdf->Output();
	}

	public function print_process_to_pasal($id = null)
	{
		$mpdf = new \Mpdf\Mpdf([
			'margin_left' => 10,
			'margin_right' => 10,
			'margin_top' => 10,
			'margin_bottom' => 10
		]);

		$crossStd  		= $this->db->get_where('view_cross_references', ['company_id' => $this->company])->row();
		$dtlCross 		= $this->db->get_where('view_cross_reference_details', ['reference_id' => $crossStd->reference_id])->result();

		$procedures 	= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();
		$lsProcedure 	= [];
		$ArrDtlCross 	= [];
		$DataStd 		= [];

		foreach ($dtlCross as $dtl) {
			$ArrDtlCross[] = explode(",", $dtl->procedure_id);
		}


		foreach ($ArrDtlCross as $arr) {
			foreach ($arr as $value) {
				$lsProcedure[$value] = $value;
				if ($value) {
					$this->db->select('*')->from('view_cross_reference_details');
					$this->db->where("find_in_set($value, procedure_id)");
					$this->db->where("reference_id", $crossStd->reference_id);
					$this->db->where("company_id", $this->company);
					$DataStd[$value] = $this->db->get()->result();
				}
			}
		}
		$Data = [
			'crossStd' 		=> $crossStd,
			'DataStd' 		=> $DataStd,
			'procedures' 	=> $procedures,
			'lsProcedure' 	=> $lsProcedure,
		];

		$data = $this->template->load_view('printout_bk', $Data, TRUE);
		$mpdf->WriteHTML($data);
		$mpdf->Output();
	}

	/* PRINTOUT */
	public function download($id = null)
	{
		$mpdf 				= new Mpdf();
		$procedure 			= $this->db->get_where('procedures', ['id' => $id])->row();
		$flowDetail 		= $this->db->get_where('procedure_details', ['procedure_id' => $id])->result();
		$getForms			= $this->db->get_where('dir_forms', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
		$getGuides			= $this->db->get_where('dir_guides', ['procedure_id' => $id, 'status !=' => 'DEL'])->result();
		$users 				= $this->db->get_where('view_users', ['status' => 'ACT', 'id_user !=' => '1', 'company_id' => $this->company])->result();
		$jabatan 			= $this->db->get('positions')->result();
		$ArrUsr 			= $ArrJab = $ArrForms = $ArrGuides = [];

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

		$allProcedure 		= $this->db->get_where('procedures', ['company_id' => $this->company, 'status !=' => 'DEL'])->result();

		$Data = [
			'procedure' => $procedure,
			'detail' 	=> $flowDetail,
			'ArrUsr' 	=> $ArrUsr,
			'ArrJab' 	=> $ArrJab,
			'ArrForms' 	=> $ArrForms,
			'ArrGuides' 	=> $ArrGuides,

			'Data' 			=> $Data,
			'ArrData' 		=> $ArrData,
			'ArrStd' 		=> $ArrStd,
			'allProcedure' 	=> $allProcedure,
		];

		$data = $this->load->view('printout_procedure', $Data, TRUE);
		$mpdf->WriteHTML($data);
		// $mpdf->Output();
		$mpdf->Output(trim($procedure->name) . ".pdf", 'D');
	}
}
