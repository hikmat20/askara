<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{
	/*
 * @author Yunaz
 * @copyright Copyright (c) 2016, Yunaz
 * 
 * This is controller for Penerimaan
 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('dashboard/dashboard_model');
		$this->load->model('dashboard/dashboard_cards_model');
		$this->template->set_theme('dashboard');
		$this->template->page_icon('fa fa-dashboard');

		// $this->cbg = $this->session->app_session['id_cabang'];
	}

	public function index()
	{
		$cards = $this->dashboard_cards_model->get_active_cards();

		$this->template->set(
			[
				'title' => 'Dashboard',
				'cards' => $cards,
				'is_admin' => $this->auth->is_admin(),
			]
		);

		$this->template->render('index');
	}

	public function cards()
	{
		$this->require_administrator();
		$data = $this->dashboard_cards_model->get_all_cards();
		$this->template->set([
			'title' => 'Kelola Card Dashboard',
			'icon' => 'fa fa-th-large',
			'data' => $data,
		]);
		$this->template->render('cards');
	}

	public function add_card()
	{
		$this->require_administrator();
		$this->_render_card_form();
	}

	public function edit_card($id = null)
	{
		$this->require_administrator();
		$data = $this->db->get_where('dashboard_cards', ['id' => $id])->row();
		if (!$data) {
			show_404();
		}
		$this->_render_card_form($data);
	}

	public function save_card()
	{
		if (!$this->auth->is_admin()) {
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya administrator.']);
			return;
		}

		$post = $this->input->post();
		$link = $this->_resolve_card_link($post);
		if (empty($post['name']) || empty($link)) {
			echo json_encode(['status' => 0, 'msg' => 'Nama dan link menu wajib diisi.']);
			return;
		}

		$picture = isset($post['old_picture']) ? $post['old_picture'] : '';
		if (!empty($_FILES['picture']['name'])) {
			$uploaded = $this->_upload_card_image();
			if ($uploaded['status'] == 0) {
				echo json_encode($uploaded);
				return;
			}
			if (!empty($post['old_picture']) && $post['old_picture'] !== $uploaded['picture']) {
				$oldPath = FCPATH . 'assets/images/dashboard/' . $post['old_picture'];
				if (is_file($oldPath)) {
					@unlink($oldPath);
				}
			}
			$picture = $uploaded['picture'];
		}

		if (empty($picture)) {
			echo json_encode(['status' => 0, 'msg' => 'Gambar card wajib diupload.']);
			return;
		}

		$row = [
			'name' => trim($post['name']),
			'link' => $link,
			'picture' => $picture,
			'sort_order' => (int) (isset($post['sort_order']) ? $post['sort_order'] : 0),
			'is_active' => (isset($post['is_active']) && $post['is_active'] === 'Y') ? 'Y' : 'N',
			'modified_at' => date('Y-m-d H:i:s'),
			'modified_by' => $this->auth->user_id(),
		];

		$this->db->trans_begin();
		if (!empty($post['id'])) {
			$this->db->update('dashboard_cards', $row, ['id' => $post['id']]);
		} else {
			$row['created_at'] = date('Y-m-d H:i:s');
			$row['created_by'] = $this->auth->user_id();
			$this->db->insert('dashboard_cards', $row);
		}

		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			echo json_encode(['status' => 0, 'msg' => 'Gagal menyimpan data card.']);
			return;
		}

		$this->db->trans_commit();
		echo json_encode(['status' => 1, 'msg' => 'Card dashboard berhasil disimpan.']);
	}

	public function delete_card()
	{
		if (!$this->auth->is_admin()) {
			echo json_encode(['status' => 0, 'msg' => 'Akses ditolak. Hanya administrator.']);
			return;
		}

		$id = $this->input->post('id');
		if (!$id) {
			echo json_encode(['status' => 0, 'msg' => 'ID tidak valid.']);
			return;
		}

		$row = $this->db->get_where('dashboard_cards', ['id' => $id])->row();
		if (!$row) {
			echo json_encode(['status' => 0, 'msg' => 'Data tidak ditemukan.']);
			return;
		}

		$this->db->delete('dashboard_cards', ['id' => $id]);
		if ($row->picture) {
			$path = FCPATH . 'assets/images/dashboard/' . $row->picture;
			if (is_file($path)) {
				@unlink($path);
			}
		}

		echo json_encode(['status' => 1, 'msg' => 'Card berhasil dihapus.']);
	}

	protected function require_administrator()
	{
		if (!$this->auth->is_admin()) {
			$this->template->set_message('Akses ditolak. Hanya administrator yang dapat mengelola card dashboard.', 'error');
			redirect('dashboard');
		}
	}

	protected function _render_card_form($data = null)
	{
		$menuData = $this->dashboard_cards_model->get_menu_link_options();
		$storedLink = $data ? $this->_normalize_card_link($data->link) : '';
		$linkInMenu = $storedLink && in_array($storedLink, $menuData['links'], true);

		$this->template->set([
			'data' => $data,
			'menu_options' => $menuData['options'],
			'link_in_menu' => $linkInMenu,
			'stored_link' => $storedLink,
		]);
		$this->template->render('card_form');
	}

	protected function _resolve_card_link($post)
	{
		$menuLink = isset($post['link_menu']) ? trim($post['link_menu']) : '';
		if ($menuLink === '__custom__') {
			return $this->_normalize_card_link(isset($post['link_custom']) ? $post['link_custom'] : '');
		}
		if ($menuLink !== '') {
			return $this->_normalize_card_link($menuLink);
		}

		return $this->_normalize_card_link(isset($post['link']) ? $post['link'] : '');
	}

	protected function _normalize_card_link($link)
	{
		return trim($link, " \t\n\r\0\x0B/");
	}

	protected function _upload_card_image()
	{
		$config = [
			'upload_path' => FCPATH . 'assets/images/dashboard/',
			'allowed_types' => 'gif|jpg|jpeg|png|webp',
			'max_size' => 2048,
			'max_width' => 2000,
			'max_height' => 2000,
			'encrypt_name' => true,
		];

		if (!is_dir($config['upload_path'])) {
			@mkdir($config['upload_path'], 0755, true);
		}

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('picture')) {
			return ['status' => 0, 'msg' => strip_tags($this->upload->display_errors())];
		}

		$file = $this->upload->data();
		return ['status' => 1, 'picture' => $file['file_name']];
	}

	public function create_documents()
	{
		$this->template->set('title', 'Create Document');
		$id_jabatan = $this->session->app_session['id_jabatan'];
		$id_user 	= $this->session->app_session['id_user'];
		$doc1 = $this->db->get_where('gambar', ['nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();
		$doc2 = $this->db->get_where('gambar1', ['nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();
		$doc3 = $this->db->get_where('gambar2', ['nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();
		$doc = $doc1 + $doc2 + $doc3;

		// koreksi
		$cor1 = $this->db->get_where('gambar', ['status_approve' => 0, 'prepared_by' => $id_user, 'nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();
		$cor2 = $this->db->get_where('gambar1', ['status_approve' => 0, 'prepared_by' => $id_user, 'nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();
		$cor3 = $this->db->get_where('gambar2', ['status_approve' => 0, 'prepared_by' => $id_user, 'nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();

		// revisi
		$rev1 = $this->db->get_where('gambar', ['status_approve' => 1, 'id_review' => $id_jabatan, 'nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();
		$rev2 = $this->db->get_where('gambar1', ['status_approve' => 1, 'id_review' => $id_jabatan, 'nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();
		$rev3 = $this->db->get_where('gambar2', ['status_approve' => 1, 'id_review' => $id_jabatan, 'nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();

		// approve
		$apv1 = $this->db->get_where('gambar', ['status_approve' => 2, 'id_approval' => $id_jabatan, 'nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();
		$apv2 = $this->db->get_where('gambar1', ['status_approve' => 2, 'id_approval' => $id_jabatan, 'nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();
		$apv3 = $this->db->get_where('gambar2', ['status_approve' => 2, 'id_approval' => $id_jabatan, 'nama_file !=' => null, 'id_perusahaan' => $this->prsh, 'id_cabang' => $this->cbg])->num_rows();

		$allCorr 	= $cor1 + $cor2 + $cor3;
		$allRev 	= $rev1 + $rev2 + $rev3;
		$allApv 	= $apv1 + $apv2 + $apv3;

		$pictures = $this->db->get('pictures')->result();
		$this->template->set('pictures', $pictures);
		$this->template->set('doc', $doc);
		$this->template->set('docCor', $allCorr);
		$this->template->set('docApv', $allApv);
		$this->template->set('docRev', $allRev);
		$this->template->render('create-document');
	}

	public function picture()
	{
		$id 		= $this->input->post('id');
		$picture 	= $this->db->get_where('pictures', ['id' => $id])->row();

		$this->template->set('picture', $picture);
		$this->template->render('change-picture');
	}

	public function upload()
	{
		$old_picture 	= $this->input->post('old_picture');
		$id 			= $this->input->post('id');

		$config['upload_path']          = './assets/img/';
		$config['allowed_types']        = 'gif|jpg|png';
		$config['max_size']             = 500;
		$config['max_width']            = 1000;
		$config['max_height']           = 1000;
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('picture')) {
			$error = $this->upload->display_errors();

			$collback = [
				'msg' => $error,
				'status' => 0
			];
			echo json_encode($collback);
			return FALSE;
		} else {
			if ($old_picture) {
				unlink('./assets/img/' . $old_picture);
			}
			$dataPicture = $this->upload->data();
			$picture = $dataPicture['file_name'];
		}

		$Arr_data = [
			'pictures' => $picture,
		];
		$this->db->trans_begin();
		$this->db->update('pictures', $Arr_data, ['id' => $id]);

		if ($this->db->trans_status() == false) {
			$this->db->trans_rollback();
			$collback = [
				'msg' => 'Upload Faild, Please ty again!',
				'status' => 0
			];
		} else {
			$this->db->trans_commit();
		}
		$collback = [
			'msg' => 'Upload Success!',
			'status' => 1,
			'picture' => $picture
		];

		echo json_encode($collback);
	}
}
