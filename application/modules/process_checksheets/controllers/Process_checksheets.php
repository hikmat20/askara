<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Mpdf\Mpdf;

class Process_checksheets extends Admin_Controller
{
	/*
	 * @author Hikmat A.R
	 * @copyright Copyright (c) 2023, Hikmat A.R
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->library('upload');
		$this->template->set('title', 'MASTER CHECKSHEET');

		date_default_timezone_set('Asia/Jakarta');
	}
	private function getNumber()
	{
		// $last_number 	= $this->db->select('MAX(number) as number_max')->from('checksheet_process_data')->like('number', 'CK'.date('y').'-', 'both	')->get()->row();
		$last_number = $this->db->query('SELECT MAX(number) AS number_max FROM checksheet_process_data WHERE `number` LIKE "%CK' . date('y') . '-%"')->row();

		$angkaUrut2 = $last_number->number_max;
		$urutan2 = (int) substr($angkaUrut2, 5, 5);
		$urutan2++;
		$new_number = "CK" . date('y') . "-" . sprintf("%05d", $urutan2);
		return $new_number;
	}

	public function index()
	{

		if (isset($_GET['sheet'])) {
			if (!$_GET['sheet'] || !isset($_GET['create'])) {
				echo "Data not found!";
				return false;
			}

			if (!isset($_GET['dir']) || !($_GET['dir'])) {
				echo "Data not valid!";
				return false;
			}

			$this->_create($_GET['sheet'], $_GET['dir']);
			return false;
		}

		if ((isset($_GET['p']) && !$_GET['p'])) {
			echo "Directory not valid";
			return false;
		} else if ((isset($_GET['sub']) && !$_GET['sub'])) {
			echo "Sub Directory not valid";
			return false;
		} else if ((isset($_GET['checksheet']) && !$_GET['checksheet'])) {
			echo "Checksheet Directory not valid";
			return false;
		} else if ((isset($_GET['p']) && $_GET['p']) && (isset($_GET['sub']) && $_GET['sub']) && !isset($_GET['checksheet'])) {
			$parent = $this->db->get_where('checksheet_process', ['id' => $_GET['p'], 'company_id' => $this->company, 'status' => '1'])->row();
			$sub = $this->db->get_where('checksheet_process_sub', ['id' => $_GET['sub'], 'company_id' => $this->company, 'status' => '1'])->row();
			$data = $this->db->get_where('checksheet_process_dir', ['process_id' => $_GET['p'], 'sub_id' => $_GET['sub'], 'company_id' => $this->company, 'status' => '1'])->result();
			$this->template->set(
				[
					'data' => $data,
					'sub' => $sub,
					'parent' => $parent,
				]
			);

			$this->template->render('index-dir');
			return false;
		} else if ((isset($_GET['checksheet']) && $_GET['checksheet'] && $_GET['sub'])) {

			$parent = $this->db->get_where('checksheet_process', ['id' => $_GET['p'], 'company_id' => $this->company])->row();
			$sub = $this->db->get_where('checksheet_process_sub', ['id' => $_GET['sub'], 'company_id' => $this->company])->row();
			$dir = $this->db->get_where('checksheet_process_dir', ['id' => $_GET['checksheet'], 'company_id' => $this->company])->row();
			$data = $this->db->get_where('checksheet_process_data', ['dir_id' => $_GET['checksheet'], 'company_id' => $this->company])->result();

			$fExecution = [
				'1' => 'Once Time',
				'2' => 'Weekly~Daily',
				'3' => 'Monthly~Daily',
				'4' => 'Weekly~Monthly',
				'5' => 'Yearly~Monthly',
			];
			$fChecking = [
				'1' => 'Daily',
				'2' => 'Weekly',
				'3' => 'Monthly',
			];

			$this->template->set(
				[
					'data' => $data,
					'dir' => $dir,
					'sub' => $sub,
					'parent' => $parent,
					'fExecution' => $fExecution,
					'fChecking' => $fChecking,
				]
			);

			$this->template->render('index-check');
			return false;
		}

		if ((isset($_GET['p']) && $_GET['p'])) {
			$parent = $this->db->get_where('checksheet_process', ['id' => $_GET['p'], 'status' => '1'])->row();
			$subFolder = $this->db->get_where('checksheet_process_sub', ['process_id' => $_GET['p'], 'status' => '1'])->result();

			$this->template->set(
				[
					'subFolder' => $subFolder,
					'parent' => $parent,
				]
			);
			$this->template->render('index-sub');
			return false;
		}

		$parents = $this->db->get_where('checksheet_process', ['status' => '1', 'company_id' => $this->company])->result();
		$this->template->set([
			'parents' => $parents,
		]);

		$this->template->render('index');
	}

	public function create_checksheet($id = null, $dir = null)
	{
		$sheet = $this->db->get_where('view_checksheet_detail_data', ['id' => $id])->row();
		$items = $this->db->get_where('checksheet_data_items', ['checksheet_data_number' => $sheet->number])->result();
		$fExecution = [
			'1' => 'Once Time',
			'2' => 'Weekly~Daily',
			'3' => 'Monthly~Daily',
			'4' => 'Weekly~Monthly',
			'5' => 'Yearly~Monthly',
		];

		// $width = $count = $name_col = '';
		// if ($sheet->frequency_execution == 1) {
		// 	$width 		= '100%';
		// 	$col_width 	= '20%';
		// 	$count 		= 1;
		// 	$name_col 	= 'Day';
		// } elseif ($sheet->frequency_execution == 2) {
		// 	$width 		= '150%';
		// 	$col_width 	= '20%';
		// 	$count 		= 7;
		// 	$name_col 	= 'Day';
		// } elseif ($sheet->frequency_execution == 3) {
		// 	$width 		= '500%';
		// 	$count 		= 31;
		// 	$name_col 	= 'Day';
		// } elseif ($sheet->frequency_execution == 4) {
		// 	$width 		= '120%';
		// 	$count 		= 5;
		// 	$name_col 	= 'Week';
		// } elseif ($sheet->frequency_execution == 5) {
		// 	$width 		= '220%';
		// 	$count 		= 12;
		// 	$name_col 	= 'Month';
		// }

		$this->template->set(
			[
				'data' => $sheet,
				'items' => $items,
				'fExecution' => $fExecution,
				// 'width' 		=> $width,
				// 'count' 		=> $count,
				// 'name_col' 		=> $name_col,
				'dir' => $dir,
			]
		);

		$this->template->render('sheet');
		return false;
	}

	public function edit_checkhseet($id = null, $dir = null)
	{
		if ($id) {
			$sheet = $this->db->get_where('checksheet_process_data', ['id' => $id])->row();
			$dataDir = $this->db->get_where('view_checksheet_process_dir', ['id' => $sheet->dir_id])->row();
			// $items 		= $this->db->get_where('checksheet_process_details', ['checksheet_process_data_number' => $sheet->number])->result();
			$items = $this->db->get_where('checksheet_data_items', ['checksheet_data_number' => $sheet->checksheet_data_number])->result();
			// $items 		= $this->db->get_where('checksheet_process_details', ['checksheet_process_data_number' => $sheet->number])->result();
			$items = $this->db->get_where('checksheet_data_items', ['checksheet_data_number' => $sheet->checksheet_data_number])->result();

			$fExecution = [
				'1' => 'Once Time',
				'2' => 'Weekly~Daily',
				'3' => 'Monthly~Daily',
				'4' => 'Weekly~Monthly',
				'5' => 'Yearly~Monthly',
			];
			$fChecking = [
				'1' => 'Daily',
				'2' => 'Weekly',
				'3' => 'Monthly',
			];

			$this->template->set(
				[
					'data' => $sheet,
					'items' => $items,
					'fExecution' => $fExecution,
					'fChecking' => $fChecking,
					'dataDir' => $dataDir,
				]
			);

			$this->template->render('edit');
		} else {
			echo "Data not found..";
		}
	}

	private function _create($id = null, $dir = null)
	{
		$sheet = $this->db->get_where('checksheet_detail_data', ['id' => $id])->row();
		if ($sheet) {
			$items = $this->db->get_where('checksheet_data_items', ['checksheet_data_number' => $sheet->number])->result();
			$fExecution = [
				'1' => 'Once Time',
				'2' => 'Weekly~Daily',
				'3' => 'Monthly~Daily',
				'4' => 'Weekly~Monthly',
				'5' => 'Yearly~Monthly',
			];
			$fChecking = [
				'1' => 'Daily',
				'2' => 'Weekly',
				'3' => 'Monthly',
			];
			$dataDir = $this->db->get_where('view_checksheet_process_dir', ['id' => $dir])->row();

			$this->template->set(
				[
					'data' => $sheet,
					'items' => $items,
					'fExecution' => $fExecution,
					'fChecking' => $fChecking,
					'dataDir' => $dataDir,
				]
			);

			$this->template->render('create');
		} else {
			echo "Data not found";
		}
	}

	public function load_sheet($id, $dir = null)
	{
		$checksheets = $this->db->get_where('checksheets', ['company_id' => $this->company, 'status' => '1'])->result();
		$fExecution = [
			'1' => 'Once Time',
			'2' => 'Weekly~Daily',
			'3' => 'Monthly~Daily',
			'4' => 'Weekly~Monthly',
			'5' => 'Yearly~Monthly',
		];
		$this->template->set([
			'checksheets' => $checksheets,
			'fExecution' => $fExecution,
			'dir' => $dir,
		]);
		$this->template->render('load_sheet');
	}

	public function load_details($checksheet_id)
	{
		$details = $this->db->get_where('checksheet_details', ['checksheet_id' => $checksheet_id, 'company_id' => $this->company, 'status' => '1'])->result();
		echo "<option value=''></option>";
		foreach ($details as $csDtl) {
			echo "<option value='$csDtl->id'>$csDtl->name</option>";
		}
	}

	public function load_detail_data($checksheet_detail_id = null, $dir)
	{
		$data = $this->db->get_where('checksheet_detail_data', ['checksheet_detail_id' => $checksheet_detail_id, 'company_id' => $this->company, 'status' => '1'])->result();
		$periode = [
			'1' => 'Once Time',
			'2' => 'Weekly~Daily',
			'3' => 'Monthly~Daily',
			'4' => 'Weekly~Monthly',
			'5' => 'Yearly~Monthly',
		];
		$html = '';
		$n = 0;
		foreach ($data as $dt) {
			$n++;
			$per = $periode[$dt->frequency_execution];
			$html .= "<tr>
						<td class='p-2'>$n</td>
						<td class='p-2'>$dt->name</td>
						<td class='p-2'>$per</td>
						<td class='p-2'>
							<a href='" . base_url($this->uri->segment(1) . "/?sheet=" . $dt->id . "&dir=$dir&create") . "' data-id='$dt->id' class='btn btn-info btn-icon btn-sm process' title='Process' data-toggle='tooltip'><i class='fas fa-arrow-right'></i></a>
						</td>
					  </tr>";
		}
		echo $html;
	}

	public function save_checksheet()
	{
		$data = $this->input->post();
		$details = isset($data['details']) ? $data['details'] : [];
		$notes = isset($data['notes']) ? $data['notes'] : [];

		unset($data['notes']);
		unset($data['details']);

		$this->db->trans_begin();
		// if (isset($data['id']) && $data['id']) {
		// 	$data['updated_at'] = date('Y-m-d H:i:s');
		// 	$data['update_by'] = $this->auth->user_id();
		// 	$this->db->update('checksheet_process_data', $data, ['id' => $data['id']]);
		// } else {
		// }

		$dataDir = $this->db->get_where('checksheet_process_dir', ['id' => $data['dir']])->row();
		$data['number'] = (isset($data['number']) && $data['number']) ? $data['number'] : $this->getNumber();
		$data['sub_id'] = $dataDir->sub_id;
		$data['process_id'] = $dataDir->process_id;
		$data['dir_id'] = $dataDir->id;
		$data['company_id'] = $this->company;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $this->auth->user_id();
		unset($data['dir']);

		if (isset($data['id'])) {
			$this->db->update('checksheet_process_data', $data, ['id' => $data['id']]);
		} else {
			$this->db->insert('checksheet_process_data', $data);
		}

		$i = 0;
		if ($details) {
			foreach ($details as $result) {
				$i++;
				$result['checksheet_process_data_number'] = $data['number'];
				$this->db->insert('checksheet_process_details', $result);

				// if (isset($result['id']) && $result['id']) {
				// 	$this->db->update('checksheet_process_details', $result, ['id' => $result['id']]);
				// $note = isset($result["note$i"]) ? ($result["note$i"] ?: null) : null;
				// unset($result["note$i"]);
				// if ($note) {
				// 	$exist = $this->db->get_where('checksheet_notes', ['item_id' => $result['id']])->row();
				// 	if (!$exist) {
				// 		$this->db->insert('checksheet_notes', [
				// 			"item_id" => $result['id'],
				// 			"note$i" => $note
				// 		]);
				// 	}
				// 	$this->db->update('checksheet_notes', ["note$i" => $note], ['item_id' => $result['id']]);
				// } else {
				// 	$this->db->update('checksheet_notes', ["note$i" => $note], ['item_id' => $result['id']]);
				// }

				// $exist_checker = $this->db->get_where('checksheet_execution', ['item_id' => $result['id']])->row();
				// if (!$exist_checker) {
				// 	$this->db->insert('checksheet_execution', [
				// 		"item_id" 		=> $result['id'],
				// 		"checked_by$i" 	=> $this->auth->user_id()
				// 	]);
				// } else {
				// 	$this->db->update('checksheet_execution', ["checked_by$i" => $this->auth->user_id()], ['item_id' => $result['id']]);
				// }

				// $execution_date = $this->db->get_where('checksheet_execution_date', ['item_id' => $result['id']])->row();
				// if (!$execution_date) {
				// 	$this->db->insert('checksheet_execution_date', [
				// 		"item_id" 		=> $result['id'],
				// 		"checking_date$i" 	=> date('Y-m-d H:i:s')
				// 	]);
				// } else {
				// 	$this->db->update('checksheet_execution_date', ["checking_date$i" => date('Y-m-d H:i:s')], ['item_id' => $result['id']]);
				// }
				// } else {

				// }
			}
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Save checksheet failed. Error!'
			);
		} else {
			if ($this->db->affected_rows() > 0) {
				$this->db->trans_commit();
				$Return = array(
					'status' => 1,
					'msg' => 'Save checksheet successfull'
				);
			} else {
				$this->db->trans_commit();
				$Return = array(
					'status' => 1,
					'msg' => "The data is up to date. Thanks"
				);
			}
		}
		echo json_encode($Return);
	}

	private function _makeArray($data, $key, $field = null)
	{
		$ArrData = [];

		foreach ($data as $dt) {
			$ArrData[$dt->$key] = isset($field) ? $dt->$field : $dt;
		}

		return $ArrData;
	}

	public function view_sheet($id = null)
	{
		if ($id) {
			$data = $this->db->get_where('checksheet_process_data', ['id' => $id])->row();
			$details = $this->db->get_where('checksheet_process_details', ['checksheet_process_data_number' => $data->number])->result();

			$notes = $this->db->get_where('checksheet_notes', ['data_id' => $data->id])->result();
			$execution = $this->db->get_where('checksheet_execution', ['data_id' => $data->id])->result();
			$execution_date = $this->db->get_where('checksheet_execution_date', ['data_id' => $data->id])->result();
			$users = $this->db->get_where('users')->result();

			$checkers = $this->db->get_where('checksheet_checkers', ['data_id' => $data->id])->result();
			$checking_date = $this->db->get_where('checksheet_checking_date', ['data_id' => $data->id])->result();
			$checking_note = $this->db->get_where('checksheet_checker_note', ['data_id' => $data->id])->result();
			$checking_value = $this->db->get_where('checksheet_checker_values', ['data_id' => $data->id])->result();

			$ArrNotes = $this->_makeArray($notes, 'item_id');
			$ArrUsers = $this->_makeArray($users, 'id_user', 'full_name');
			$ArrExe = $this->_makeArray($execution, 'data_id');
			$ArrExeDate = $this->_makeArray($execution_date, 'data_id');

			$ArrCheck = $this->_makeArray($checkers, 'data_id');
			$ArrCheckDate = $this->_makeArray($checking_date, 'data_id');
			$ArrCheckNote = $this->_makeArray($checking_note, 'data_id');
			$ArrCheckValue = $this->_makeArray($checking_value, 'data_id');

			$fExecution = [
				'1' => 'Once Time',
				'2' => 'Weekly~Daily',
				'3' => 'Monthly~Daily',
				'4' => 'Weekly~Monthly',
				'5' => 'Yearly~Monthly',
			];

			$fChecking = [
				'1' => 'Daily',
				'2' => 'Weekly',
				'3' => 'Monthly',
			];

			$width = $count = $name_col = $col_width = '';
			if ($data->frequency_execution == 1) {
				$width = '100%';
				$col_width = '20%';
				$count = 1;
				$name_col = 'Day';
			} elseif ($data->frequency_execution == 2) {
				$width = '100%';
				$col_width = '20%';
				$count = 7;
				$name_col = 'Day';
			} elseif ($data->frequency_execution == 3) {
				$width = '170%';
				$count = 31;
				$name_col = 'Day';
				$col_width = '30%';
			} elseif ($data->frequency_execution == 4) {
				$width = '150%';
				$count = 5;
				$name_col = 'Week';
				$col_width = '50%';
			} elseif ($data->frequency_execution == 5) {
				$width = '220%';
				$count = 12;
				$name_col = 'Month';
				$col_width = '20%';
			}

			$this->template->set([
				'data' => $data,
				'width' => $width,
				'count' => $count,
				'col_width' => $col_width,
				'name_col' => $name_col,
				'fExecution' => $fExecution,
				'fChecking' => $fChecking,
				'details' => $details,
				'ArrUsers' => $ArrUsers,
				'ArrNotes' => $ArrNotes,
				'ArrExe' => $ArrExe,
				'ArrExeDate' => $ArrExeDate,
				'ArrCheck' => $ArrCheck,
				'ArrCheckDate' => $ArrCheckDate,
				'ArrCheckNote' => $ArrCheckNote,
				'ArrCheckValue' => $ArrCheckValue,
			]);

			$this->template->render('view-sheet');
		}
	}

	public function checking_sheet($id = null)
	{
		if ($id) {
			$data = $this->db->get_where('checksheet_process_data', ['id' => $id])->row();
			$details = $this->db->get_where('checksheet_process_details', ['checksheet_process_data_number' => $data->number])->result();

			$notes = $this->db->get_where('checksheet_notes', ['data_id' => $data->id])->result();
			$execution = $this->db->get_where('checksheet_execution', ['data_id' => $data->id])->result();
			$execution_date = $this->db->get_where('checksheet_execution_date', ['data_id' => $data->id])->result();
			$users = $this->db->get_where('users')->result();

			$checkers = $this->db->get_where('checksheet_checkers', ['data_id' => $data->id])->result();
			$checking_date = $this->db->get_where('checksheet_checking_date', ['data_id' => $data->id])->result();
			$checking_note = $this->db->get_where('checksheet_checker_note', ['data_id' => $data->id])->result();
			$checking_value = $this->db->get_where('checksheet_checker_values', ['data_id' => $data->id])->result();

			$ArrNotes = $this->_makeArray($notes, 'item_id');
			$ArrUsers = $this->_makeArray($users, 'id_user', 'full_name');
			$ArrExe = $this->_makeArray($execution, 'data_id');
			$ArrExeDate = $this->_makeArray($execution_date, 'data_id');

			$ArrCheck = $this->_makeArray($checkers, 'data_id');
			$ArrCheckDate = $this->_makeArray($checking_date, 'data_id');
			$ArrCheckNote = $this->_makeArray($checking_note, 'data_id');
			$ArrCheckValue = $this->_makeArray($checking_value, 'data_id');

			$ArrUsers = [];
			foreach ($users as $usr) {
				$ArrUsers[$usr->id_user] = $usr->full_name;
			}

			$fExecution = [
				'1' => 'Once Time',
				'2' => 'Weekly~Daily',
				'3' => 'Monthly~Daily',
				'4' => 'Weekly~Monthly',
				'5' => 'Yearly~Monthly',
			];

			$fChecking = [
				'1' => 'Daily',
				'2' => 'Weekly',
				'3' => 'Monthly',
			];

			$width = $count = $name_col = $col_width = $weekOfMonth = '';
			if ($data->frequency_execution == 1) {
				$width = '100%';
				$col_width = '20%';
				$count = 1;
				$name_col = 'Day';
			} elseif ($data->frequency_execution == 2) {
				$width = '100%';
				$col_width = '20%';
				$count = 7;
				$name_col = 'Day';
			} elseif ($data->frequency_execution == 3) {
				$width = '170%';
				$count = 31;
				$name_col = 'Day';
				$col_width = '30%';
			} elseif ($data->frequency_execution == 4) {
				$width = '120%';
				$count = 5;
				$name_col = 'Week';
				$col_width = '50%';
				$weekOfMonth = $this->_weekOfMonth(strtotime(date('Y-m-d')));
			} elseif ($data->frequency_execution == 5) {
				$width = '150%';
				$count = 12;
				$name_col = 'Month';
				$col_width = '20%';
			}

			$current = '';
			if ($data->frequency_execution == 1) {
				$current = 1;
			} elseif ($data->frequency_execution == 2) {
				$current = date('N');
			} elseif ($data->frequency_execution == 3) {
				$current = date('j');
			} elseif ($data->frequency_execution == 4) {
				$current = $weekOfMonth;
			} elseif ($data->frequency_execution == 5) {
				$current = date('n');
			}

			$this->template->set([
				'data' => $data,
				'width' => $width,
				'count' => $count,
				'col_width' => $col_width,
				'name_col' => $name_col,
				'fExecution' => $fExecution,
				'fChecking' => $fChecking,
				'details' => $details,
				'ArrNotes' => $ArrNotes,
				'ArrExe' => $ArrExe,
				'ArrExeDate' => $ArrExeDate,
				'ArrCheck' => $ArrCheck,
				'ArrCheckDate' => $ArrCheckDate,
				'ArrUsers' => $ArrUsers,
				'weekOfMonth' => $weekOfMonth,
				'current' => $current,
				'ArrCheckNote' => $ArrCheckNote,
				'ArrCheckValue' => $ArrCheckValue,
			]);

			$this->template->render('checking-sheet');
		}
	}


	private function _weekOfMonth($date)
	{
		//Get the first day of the month.
		$firstOfMonth = strtotime(date("Y-m-01", $date));
		//Apply above formula.
		return $this->_weekOfYear($date) - $this->_weekOfYear($firstOfMonth) + 1;
	}

	private function _weekOfYear($date)
	{
		$weekOfYear = intval(date("W", $date));
		if (date('n', $date) == "1" && $weekOfYear > 51) {
			// It's the last week of the previos year.
			return 0;
		} else if (date('n', $date) == "12" && $weekOfYear == 1) {
			// It's the first week of the next year.
			return 53;
		} else {
			// It's a "normal" week.
			return $weekOfYear;
		}
	}

	/* FOLDER */

	public function save_folder()
	{
		$post = $this->input->post();
		$name = $post['name'];
		$is_exist = $this->db->get_where('checksheet_process', ['name' => $name, 'status' => '1', 'company_id' => $this->company])->num_rows();

		if ($is_exist > 0) {
			$return = [
				'status' => 0,
				'msg' => 'Folder Name is already exist',
			];
			echo json_encode($return);
			return false;
		}

		if (!isset($post['id'])) {
			$this->db->trans_begin();
			$data = [
				'name' => strtoupper($name),
				'company_id' => $this->company,
				'created_by' => $this->auth->user_id(),
				'created_at' => date('Y-m-d H:i:s'),
			];
			$this->db->insert('checksheet_process', $data);
		} else {
			$name = $post['name'];
			$data = [
				'name' => strtoupper($name),
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('checksheet_process', $data, ['id' => $post['id']]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Folder failed created. Query Error'
			);
		} else {
			if ($this->db->affected_rows() > 0) {
				$this->db->trans_commit();
				$Return = array(
					'status' => 1,
					'msg' => 'Folder successfull created'
				);
			} else {
				$this->db->trans_commit();
				$Return = array(
					'status' => 1,
					'msg' => 'Folder is up to date'
				);
			}
		}
		echo json_encode($Return);
	}

	public function edit_folder($id)
	{
		if ($id) {
			$data = $this->db->get_where('checksheet_process', ['id' => $id])->row();
			echo json_encode(['data' => $data]);
		}
	}

	public function delete_folder()
	{
		$id = $this->input->post('id');

		$this->db->trans_begin();
		$this->db->update('checksheet_process', ['status' => '0'], ['id' => $id, 'company_id' => $this->company]);

		$check_child = $this->db->get_where('checksheet_process_sub', ['process_id' => $id, 'company_id' => $this->company])->num_rows();
		$check_dir = $this->db->get_where('checksheet_process_dir', ['process_id' => $id, 'company_id' => $this->company])->num_rows();

		if ($check_child > 0) {
			$this->db->update('checksheet_process_sub', ['status' => '0'], ['process_id' => $id, 'company_id' => $this->company]);
		}
		if ($check_dir > 0) {
			$this->db->update('checksheet_process_sub', ['status' => '0'], ['process_id' => $id, 'company_id' => $this->company]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Delete Folder failed. Error!'
			);
		} else {
			$this->db->trans_commit();
			$Return = array(
				'status' => 1,
				'msg' => 'Delete Folder successfull'
			);
		}
		echo json_encode($Return);
	}


	/* SUB FOLDER */


	public function save_sub_folder()
	{
		$post = $this->input->post();

		if (!isset($post['id'])) {
			$name = $post['name'];
			$is_exist = $this->db->get_where('checksheet_process_sub', ['name' => $name, 'status' => '1', 'process_id' => $post['process_id'], 'company_id' => $this->company])->num_rows();

			if ($is_exist > 0) {
				$return = [
					'status' => 0,
					'msg' => 'Folder Name is already exist',
				];
				echo json_encode($return);
				return false;
			}

			$this->db->trans_begin();
			$data = [
				'name' => strtoupper($name),
				'process_id' => $post['process_id'],
				'company_id' => $this->company,
				'created_by' => $this->auth->user_id(),
				'created_at' => date('Y-m-d H:i:s'),
			];

			$this->db->insert('checksheet_process_sub', $data);
		} else {
			$name = $post['name'];
			$data = [
				'name' => strtoupper($name),
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('checksheet_process_sub', $data, ['id' => $post['id']]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Directory failed created. Query Error'
			);
		} else {
			$this->db->trans_commit();
			$Return = array(
				'status' => 1,
				'msg' => 'Directory successfull created'
			);
		}
		echo json_encode($Return);
	}

	public function edit_sub_folder($id)
	{
		if ($id) {
			$data = $this->db->get_where('checksheet_process_sub', ['id' => $id])->row();
			echo json_encode(['data' => $data]);
		}
	}

	public function delete_sub_folder()
	{
		$id = $this->input->post('id');

		$this->db->trans_begin();
		$this->db->update('checksheet_process_sub', ['status' => '0'], ['id' => $id, 'company_id' => $this->company]);
		$check_dir = $this->db->get_where('checksheet_process_dir', ['sub_id' => $id, 'company_id' => $this->company])->num_rows();
		if ($check_dir > 0) {
			$this->db->update('checksheet_process_dir', ['status' => '0'], ['sub_id' => $id, 'company_id' => $this->company]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Delete Folder failed. Error!'
			);
		} else {
			$this->db->trans_commit();
			$Return = array(
				'status' => 1,
				'msg' => 'Delete Folder successfull'
			);
		}
		echo json_encode($Return);
	}

	/* DIRECTORY PROCESS */

	public function save_process_dir()
	{
		$post = $this->input->post();

		if (!isset($post['id'])) {
			$name = $post['name'];
			$is_exist = $this->db->get_where('checksheet_process_dir', ['name' => $name, 'status' => '1', 'sub_id' => $post['sub_id'], 'company_id' => $this->company])->num_rows();

			if ($is_exist > 0) {
				$return = [
					'status' => 0,
					'msg' => 'Folder Name is already exist',
				];
				echo json_encode($return);
				return false;
			}

			$this->db->trans_begin();
			$data = [
				'name' => strtoupper($name),
				'process_id' => $post['process_id'],
				'sub_id' => $post['sub_id'],
				'company_id' => $this->company,
				'created_by' => $this->auth->user_id(),
				'created_at' => date('Y-m-d H:i:s'),
			];

			$this->db->insert('checksheet_process_dir', $data);
		} else {
			$name = $post['name'];
			$data = [
				'name' => strtoupper($name),
				'modified_by' => $this->auth->user_id(),
				'modified_at' => date('Y-m-d H:i:s'),
			];
			$this->db->update('checksheet_process_dir', $data, ['id' => $post['id']]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Folder failed created. Query Error'
			);
		} else {
			$this->db->trans_commit();
			$Return = array(
				'status' => 1,
				'msg' => 'Folder successfull created'
			);
		}
		echo json_encode($Return);
	}

	public function edit_process_dir($id)
	{
		if ($id) {
			$data = $this->db->get_where('checksheet_process_dir', ['id' => $id])->row();
			echo json_encode(['data' => $data]);
		}
	}

	public function delete_process_dir()
	{
		$id = $this->input->post('id');

		$this->db->trans_begin();
		$this->db->update('checksheet_process_dir', ['status' => '0'], ['id' => $id, 'company_id' => $this->company]);
		// $this->db->update('checksheet_process_sub', ['status' => '0'], ['id' => $id]);

		// $check_dir 	 = $this->db->get_where('checksheet_process_dir', ['sub_id' => $id, 'company_id' => $this->company])->num_rows();

		// if ($check_dir > 0) {
		// }

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Delete Folder failed. Error!'
			);
		} else {
			$this->db->trans_commit();
			$Return = array(
				'status' => 1,
				'msg' => 'Delete Folder successfull'
			);
		}
		echo json_encode($Return);
	}


	/* CHECKING PROCESS */


	public function checking()
	{
		if (!isset($_GET['sheet']) || !$_GET['sheet']) {
			echo "Data not valid";
			return false;
		}

		$fExecution = [
			'1' => 'Once Time',
			'2' => 'Weekly~Daily',
			'3' => 'Monthly~Daily',
			'4' => 'Weekly~Monthly',
			'5' => 'Yearly~Monthly',
		];

		$fChecking = [
			'1' => 'Daily',
			'2' => 'Weekly',
			'3' => 'Monthly',
		];
		$sheet = $this->db->get_where('checksheet_process_data', ['id' => $_GET['sheet']])->row();
		// $details	= $this->db->get_where('checksheet_data_items', ['checksheet_data_number' => $sheet->checksheet_data_number])->result();
		$details = $this->db->get_where('checksheet_process_details', ['checksheet_process_data_number' => $sheet->number])->result();

		// $ArrProcess = [];
		// if ($detailsProcess) {
		// 	foreach ($detailsProcess as $dtl) {
		// 		$ArrProcess[$dtl->checksheet_item_id] = $dtl;
		// 	}
		// }
		$weekOfMonth = '';
		$width = $count = $name_col = '';
		if ($sheet->frequency_execution == 1) {
			$width = '100%';
			$col_width = '20%';
			$count = 1;
			$name_col = 'Day';
		} elseif ($sheet->frequency_execution == 2) {
			$width = '150%';
			$col_width = '20%';
			$count = 7;
			$name_col = 'Day';
		} elseif ($sheet->frequency_execution == 3) {
			$width = '450%';
			$col_width = '30%';
			$count = 31;
			$name_col = 'Day';
		} elseif ($sheet->frequency_execution == 4) {
			$width = '120%';
			$col_width = '20%';
			$count = 5;
			$name_col = 'Week';
			$weekOfMonth = $this->_weekOfMonth(strtotime(date('Y-m-d')));
		} elseif ($sheet->frequency_execution == 5) {
			$width = '220%';
			$col_width = '20%';
			$count = 12;
			$name_col = [
				'1' => 'Jan',
				'2' => 'Feb',
				'3' => 'Mar',
				'4' => 'Apr',
				'5' => 'Mei',
				'6' => 'Jun',
				'7' => 'Jul',
				'8' => 'Agu',
				'9' => 'Sep',
				'10' => 'Okt',
				'11' => 'Nov',
				'12' => 'Des',
			];

			$monthOfYear = $this->_weekOfMonth(strtotime(date('Y-m-d')));
		}

		$current = '';
		if ($sheet->frequency_execution == 1) {
			$current = 1;
		} elseif ($sheet->frequency_execution == 2) {
			$current = date('N');
		} elseif ($sheet->frequency_execution == 3) {
			$current = date('j');
		} elseif ($sheet->frequency_execution == 4) {
			$current = $weekOfMonth;
		} elseif ($sheet->frequency_execution == 5) {
			$current = date('n');
		}

		$notes = $this->db->get_where('checksheet_notes', ['data_id' => $sheet->id])->result();
		$execution = $this->db->get_where('checksheet_execution', ['item_id' => $sheet->id])->row();
		$execution_date = $this->db->get_where('checksheet_execution_date', ['item_id' => $sheet->id])->row();

		$ArrNote = [];
		foreach ($notes as $note) {
			$ArrNote[$note->item_id] = $note;
		}
		$this->template->set([
			'data' => $sheet,
			'width' => $width,
			'count' => $count,
			'col_width' => $col_width,
			'name_col' => $name_col,
			'fExecution' => $fExecution,
			'fChecking' => $fChecking,
			'details' => $details,
			'ArrNote' => $ArrNote,
			// 'ArrProcess' 	=> $ArrProcess,
			'execution' => ($execution) ?: [],
			'current' => $current,
			'weekOfMonth' => $weekOfMonth,
			'checking_date' => ($execution_date) ?: [],
		]);

		$this->template->render('checking');
	}

	public function save_process_checksheet()
	{
		$config['upload_path'] = './assets/images/directory/checksheet/'; //path folder
		$config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|pdf|webp'; //type yang dapat diakses bisa anda sesuaikan
		$config['max_size'] = 100000000; // Maximum file size in kilobytes (2MB).
		$config['encrypt_name'] = TRUE; // Encrypt the uploaded file's name.
		$config['remove_spaces'] = TRUE; // Remove spaces from the file name.

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		$post = $this->input->post();
		$this->db->trans_begin();
		if ($post['id']) {
			if (isset($post['detail'])) {
				$nn = 1;
				foreach ($post['detail'] as $dt) {
					$field = $dt['field'];
					$fieldNote = "note" . $field;
					$fieldDate = "date" . $field;
					$fieldChecker = "day" . $field;
					$fieldBukti = "bukti_" . $field;

					/* UPDATE DETAIL */
					if (isset($dt["n" . $field])) {
						$this->db->update('checksheet_process_details', [
							"n" . $field => $dt["n" . $field]
						], ['id' => $dt['id']]);

						/* NOTES */
						if ($dt["n" . $field] == 'no') {
							$checkNote = $this->db->get_where('checksheet_notes', ['data_id' => $post['id'], 'item_id' => $dt['id']])->row();

							$upload_bukti = '';
							if (!empty($checkNote)) {
								if (isset($_FILES['bukti' . $nn . $field]) && $_FILES['bukti' . $nn . $field]['name'] !== '') {
									$files = $_FILES['bukti' . $nn . $field];
									$file_count = count($files['name']);

									$_FILES['bukti' . $nn . $field]['name'] = $files['name'];
									$_FILES['bukti' . $nn . $field]['type'] = $files['type'];
									$_FILES['bukti' . $nn . $field]['tmp_name'] = $files['tmp_name'];
									$_FILES['bukti' . $nn . $field]['error'] = $files['error'];
									$_FILES['bukti' . $nn . $field]['size'] = $files['size'];
									$this->upload->do_upload('bukti' . $nn . $field);
									$data = $this->upload->data();
									$upload_bukti = 'assets/images/directory/checksheet/' . $data['file_name'];
								}
							} else {
								if (isset($_FILES['bukti' . $nn . $field]) && $_FILES['bukti' . $nn . $field]['name'] !== '') {
									$files = $_FILES['bukti' . $nn . $field];
									$file_count = count($files['name']);

									$_FILES['bukti' . $nn . $field]['name'] = $files['name'];
									$_FILES['bukti' . $nn . $field]['type'] = $files['type'];
									$_FILES['bukti' . $nn . $field]['tmp_name'] = $files['tmp_name'];
									$_FILES['bukti' . $nn . $field]['error'] = $files['error'];
									$_FILES['bukti' . $nn . $field]['size'] = $files['size'];
									$this->upload->do_upload('bukti' . $nn . $field);
									$data = $this->upload->data();
									$upload_bukti = 'assets/images/directory/checksheet/' . $data['file_name'];
								}
							}

							$dataNote = [
								'data_id' => $post['id'],
								'item_id' => $dt['id'],
								$fieldNote => (isset($dt[$fieldNote]) ? $dt[$fieldNote] : null),
								$fieldBukti => $upload_bukti
							];

							if (!$checkNote) {
								$this->db->insert('checksheet_notes', $dataNote);
							} else {
								$this->db->update('checksheet_notes', $dataNote, ['data_id' => $post['id'], 'item_id' => $dt['id']]);
							}
						} else if ($dt["n" . $field] == 'yes') {

							$checkNote = $this->db->get_where('checksheet_notes', ['data_id' => $post['id'], 'item_id' => $dt['id']])->row_array();

							$upload_bukti = '';

							if (!empty($checkNote)) {
								$upload_bukti = $checkNote['bukti_' . $field];

								if (isset($_FILES['bukti' . $nn . $field]) && $_FILES['bukti' . $nn . $field]['name'] !== '') {
									$files = $_FILES['bukti' . $nn . $field];
									$file_count = count($files['name']);

									$_FILES['bukti' . $nn . $field]['name'] = $files['name'];
									$_FILES['bukti' . $nn . $field]['type'] = $files['type'];
									$_FILES['bukti' . $nn . $field]['tmp_name'] = $files['tmp_name'];
									$_FILES['bukti' . $nn . $field]['error'] = $files['error'];
									$_FILES['bukti' . $nn . $field]['size'] = $files['size'];
									$this->upload->do_upload('bukti' . $nn . $field);
									$data = $this->upload->data();
									$upload_bukti = 'assets/images/directory/checksheet/' . $data['file_name'];
								}
							} else {
								if (isset($_FILES['bukti' . $nn . $field]) && $_FILES['bukti' . $nn . $field]['name'] !== '') {
									$files = $_FILES['bukti' . $nn . $field];
									$file_count = count($files['name']);

									$_FILES['bukti' . $nn . $field]['name'] = $files['name'];
									$_FILES['bukti' . $nn . $field]['type'] = $files['type'];
									$_FILES['bukti' . $nn . $field]['tmp_name'] = $files['tmp_name'];
									$_FILES['bukti' . $nn . $field]['error'] = $files['error'];
									$_FILES['bukti' . $nn . $field]['size'] = $files['size'];
									$this->upload->do_upload('bukti' . $nn . $field);
									$data = $this->upload->data();
									$upload_bukti = 'assets/images/directory/checksheet/' . $data['file_name'];
								}
							}
							// } else {
							// }

							if (empty($checkNote)) {
								$insert_notes = $this->db->insert('checksheet_notes', ['data_id' => $post['id'], 'item_id' => $dt['id'], $fieldNote => null, 'bukti_' . $field => $upload_bukti]);
								if (!$insert_notes) {
									print_r($this->db->error($insert_notes));
									exit;
								}
							} else {
								$update_notes = $this->db->update('checksheet_notes', [$fieldNote => null, 'bukti_' . $field => $upload_bukti], ['data_id' => $post['id'], 'item_id' => $dt['id']]);
								if (!$update_notes) {
									print_r($this->db->error($update_notes));
									exit;
								}
							}
						}
					}

					/* CHECKING DATE */
					$checkDate = $this->db->get_where('checksheet_execution_date', ['data_id' => $post['id']])->row();
					$dataDate = [
						'data_id' => $post['id'],
						$fieldDate => date('Y-m-d H:i:s')
					];

					if (!$checkDate) {
						$this->db->insert('checksheet_execution_date', $dataDate);
					} else {
						$this->db->update('checksheet_execution_date', $dataDate, ['data_id' => $post['id']]);
					}

					/* CHECKER */
					$checkBy = $this->db->get_where('checksheet_execution', ['data_id' => $post['id']])->row();
					$dataChecker = [
						'data_id' => $post['id'],
						$fieldChecker => $this->auth->user_id()
					];

					if (!$checkBy) {
						$this->db->insert('checksheet_execution', $dataChecker);
					} else {
						$this->db->update('checksheet_execution', $dataChecker, ['data_id' => $post['id'], 'item_id' => $dt['id']]);
					}

					$nn++;
				}
			}
			$this->db->update('checksheet_process_data', [
				'updated_at' => date('Y-m-d H:i:s'),
				'update_by' => $this->auth->user_id(),
			], ['id' => $post['id']]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Save data checkheet failed. Error!'
			);
		} else {
			$this->db->trans_commit();
			$Return = array(
				'status' => 1,
				'msg' => 'Save data checkheet successfull'
			);
		}

		echo json_encode($Return);
	}


	public function save_done()
	{
		$post = $this->input->post();

		if (!isset($post['id']) || !$post['id']) {
			echo json_encode(['status' => 0, 'msg' => 'Invalid ID']);
			return;
		}

		// Security check: ensure the checksheet belongs to the company
		$checkData = $this->db->get_where('checksheet_process_data', ['id' => $post['id'], 'company_id' => $this->company])->row();
		if (!$checkData) {
			echo json_encode(['status' => 0, 'msg' => 'Data not found or access denied']);
			return;
		}

		$this->db->trans_begin();

		$dataDate = [];
		$dataChecker = [];
		$dataValue = [];
		$dataNote = [];

		if (isset($post['checker'])) {
			foreach ($post['checker'] as $key => $val) {
				$i = substr($key, 1); // Extract index from 'c1', 'c2', etc.
				
				$dataDate["date" . $i] = date('Y-m-d H:i:s');
				$dataChecker["day" . $i] = $this->auth->user_id();
				$dataValue["check" . $i] = $val;
				
				if (isset($post['checker_note']['note' . $i])) {
					$dataNote["day" . $i] = $post['checker_note']['note' . $i];
				}
			}
		}

		if (!empty($dataDate)) {
			/* CHECKING DATE */
			$checkDate = $this->db->get_where('checksheet_checking_date', ['data_id' => $post['id']])->row();
			if (!$checkDate) {
				$dataDate['data_id'] = $post['id'];
				$this->db->insert('checksheet_checking_date', $dataDate);
			} else {
				$this->db->update('checksheet_checking_date', $dataDate, ['data_id' => $post['id']]);
			}

			/* CHECKER USER */
			$checkBy = $this->db->get_where('checksheet_checkers', ['data_id' => $post['id']])->row();
			if (!$checkBy) {
				$dataChecker['data_id'] = $post['id'];
				$this->db->insert('checksheet_checkers', $dataChecker);
			} else {
				$this->db->update('checksheet_checkers', $dataChecker, ['data_id' => $post['id']]);
			}

			/* CHECKER VALUE (Yes/No) */
			$checkValueRow = $this->db->get_where('checksheet_checker_values', ['data_id' => $post['id']])->row();
			if (!$checkValueRow) {
				$dataValue['data_id'] = $post['id'];
				$this->db->insert('checksheet_checker_values', $dataValue);
			} else {
				$this->db->update('checksheet_checker_values', $dataValue, ['data_id' => $post['id']]);
			}

			/* CHECKER NOTE (Reason) */
			$checkNoteRow = $this->db->get_where('checksheet_checker_note', ['data_id' => $post['id']])->row();
			if (!$checkNoteRow) {
				$dataNote['data_id'] = $post['id'];
				$this->db->insert('checksheet_checker_note', $dataNote);
			} else {
				$this->db->update('checksheet_checker_note', $dataNote, ['data_id' => $post['id']]);
			}
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Save data checksheet failed. Error!'
			);
		} else {
			$this->db->trans_commit();
			$Return = array(
				'status' => 1,
				'msg' => 'Save data checksheet successful'
			);
		}

		echo json_encode($Return);
	}

	public function print_sheet()
	{
		if (!isset($_GET['sheet']) || !$_GET['sheet']) {
			echo "Data tidak valid!";
			return false;
		}
		$id = $_GET['sheet'];

		$sheet = $this->db->get_where('checksheet_process_data', ['id' => $id])->row();
		$details = $this->db->get_where('checksheet_process_details', ['checksheet_process_data_number' => $sheet->number])->result();

		$notes = $this->db->get_where('checksheet_notes', ['data_id' => $sheet->id])->result();
		$execution = $this->db->get_where('checksheet_execution', ['data_id' => $sheet->id])->result();
		$execution_date = $this->db->get_where('checksheet_execution_date', ['data_id' => $sheet->id])->result();
		$users = $this->db->get_where('users')->result();

		$checkers = $this->db->get_where('checksheet_checkers', ['data_id' => $sheet->id])->result();
		$checking_date = $this->db->get_where('checksheet_checking_date', ['data_id' => $sheet->id])->result();

		$ArrNotes = $this->_makeArray($notes, 'item_id');
		$ArrUsers = $this->_makeArray($users, 'id_user', 'full_name');
		$ArrExe = $this->_makeArray($execution, 'data_id');
		$ArrExeDate = $this->_makeArray($execution_date, 'data_id');

		$ArrCheck = $this->_makeArray($checkers, 'data_id');
		$ArrCheckDate = $this->_makeArray($checking_date, 'data_id');


		$ArrUsers = [];
		foreach ($users as $usr) {
			$ArrUsers[$usr->id_user] = $usr->full_name;
		}

		$fExecution = [
			'1' => 'Once Time',
			'2' => 'Weekly~Daily',
			'3' => 'Monthly~Daily',
			'4' => 'Weekly~Monthly',
			'5' => 'Yearly~Monthly',
		];

		$fChecking = [
			'1' => 'Daily',
			'2' => 'Weekly',
			'3' => 'Monthly',
		];

		$width = $count = $name_col = $col_width = $weekOfMonth = '';
		if ($sheet->frequency_execution == 1) {
			$width = '100%';
			$col_width = '20%';
			$count = 1;
			$name_col = 'Day';
		} elseif ($sheet->frequency_execution == 2) {
			$width = '100%';
			$col_width = '20%';
			$count = 7;
			$name_col = 'Day';
		} elseif ($sheet->frequency_execution == 3) {
			$width = '170%';
			$count = 31;
			$name_col = 'Day';
			$col_width = '30%';
		} elseif ($sheet->frequency_execution == 4) {
			$width = '120%';
			$count = 5;
			$name_col = 'Week';
			$col_width = '50%';
			$weekOfMonth = $this->_weekOfMonth(strtotime(date('Y-m-d')));
		} elseif ($sheet->frequency_execution == 5) {
			$width = '150%';
			$count = 12;
			$name_col = 'Month';
			$col_width = '20%';
		}

		$data = [
			'data' => $sheet,
			'width' => $width,
			'count' => $count,
			'col_width' => $col_width,
			'name_col' => $name_col,
			'fExecution' => $fExecution,
			'fChecking' => $fChecking,
			'details' => $details,
			'ArrNotes' => $ArrNotes,
			'ArrExe' => $ArrExe,
			'ArrExeDate' => $ArrExeDate,
			'ArrCheck' => $ArrCheck,
			'ArrCheckDate' => $ArrCheckDate,
			'ArrUsers' => $ArrUsers,
			'weekOfMonth' => $weekOfMonth,
		];

		$this->load->view('print-sheet', $data);
	}

	/* ================================================ */
	/* ADD ITEMS */

	public function delete($id = null)
	{
		if ($id) {
			$this->db->trans_begin();

			/* CHECKING DATE */
			$data = $this->db->get_where('checksheet_process_data', ['id' => $id, 'company_id' => $this->company])->row();
			if (!$data) {
				echo json_encode(['status' => 0, 'msg' => 'Data not found or access denied']);
				return;
			}
			$this->db->delete('checksheet_process_data', ['id' => $id]);
			$this->db->delete('checksheet_process_details', ['checksheet_process_data_number' => $data->number]);
			$this->db->delete('checksheet_checker_note', ['data_id' => $id]);
			$this->db->delete('checksheet_checker_values', ['data_id' => $id]);
			$this->db->delete('checksheet_checkers', ['data_id' => $id]);
			$this->db->delete('checksheet_checking_date', ['data_id' => $id]);
			$this->db->delete('checksheet_execution', ['data_id' => $id]);
			$this->db->delete('checksheet_execution_date', ['data_id' => $id]);
			$this->db->delete('checksheet_notes', ['data_id' => $id]);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Return = array(
				'status' => 0,
				'msg' => 'Deleted data checkheet failed. Error!'
			);
		} else {
			$this->db->trans_commit();
			$Return = array(
				'status' => 1,
				'msg' => 'Deleted data checkheet successfull'
			);
		}

		echo json_encode($Return);
	}

	/* ===================== */
	public function print_document()
	{
		$this->load->library(array('Mpdf'));
		$folder = basename($_GET['p']);
		$file = basename($_GET['f']);

		if (!file_exists('directory/' . $folder . '/' . $file)) {
			echo "File not found";
			return;
		}

		$mpdf = new Mpdf();
		$mpdf->SetImportUse();
		$pagecount = $mpdf->SetSourceFile('directory/' . $folder . '/' . $file);
		$tplId = $mpdf->ImportPage($pagecount);
		$mpdf->UseTemplate($tplId);
		$mpdf->addPage();
		$mpdf->WriteHTML('Hello World');
		$newfile = 'directory/' . $folder . '/' . $file;
		// $mpdf->Output($newfile, 'F'); // Overwriting is dangerous if user inputs are malicious, but basename mitigates it.
		$mpdf->Output();
	}
}
