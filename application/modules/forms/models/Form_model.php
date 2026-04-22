<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Form_model extends BF_Model
{

  protected $table_name = 'forms';
  protected $key        = 'id';
  protected $created_field  = 'created_at';
  protected $modified_field = 'modified_at';
  protected $set_created    = true;
  protected $set_modified   = true;
  protected $soft_deletes   = false;
  protected $date_format    = 'datetime';
  protected $log_user       = true;

  public function __construct()
  {
    parent::__construct();
  }

  public function getAll()
  {
    return $this->db->get_where('view_forms', array('status !=' => 'DEL'))->result();
  }

  public function getDraft()
  {
    return $this->db->get_where('view_forms', array('status' => 'DFT'))->result();
  }

  public function getAllByStatus($status)
  {
    return $this->db->get_where('view_forms', array('status' => $status))->result();
  }

  public function saveData()
  {
    try {
      $Data = $this->input->post();

      if (empty($Data)) {
        throw new Exception('No data provided to save.');
      }

      $this->db->trans_begin();
      $Data['company_id'] = $this->session->company->id_perusahaan;

      // Validasi PIC jika salah satu diisi
      $reviewer_pos_id = isset($Data['reviewer_position_id']) ? $Data['reviewer_position_id'] : null;
      $approver_pos_id = isset($Data['approver_position_id']) ? $Data['approver_position_id'] : null;

      if (!empty($reviewer_pos_id) || !empty($approver_pos_id)) {
        $picError = $this->_validatePic($reviewer_pos_id, $approver_pos_id, $Data['company_id']);
        if ($picError !== null) {
          throw new Exception($picError);
        }
      }

      if (!empty($_FILES['form_file']['name'])) {
        $uploadFile = $this->_uploadFile();

        if ($uploadFile['status'] == 1) {
          $Data['file_name'] = $uploadFile['data']['file_name'];
          $Data['size']      = $uploadFile['data']['size'];
          $Data['ext']       = $uploadFile['data']['ext'];
        } else {
          throw new Exception($uploadFile['error']);
        }
      }

      $id = !empty($Data['id']) ? $Data['id'] : null;

      if ($id) {
        $Data['modified_by'] = $this->auth->user_id();
        $Data['modified_at'] = date('Y-m-d H:i:s');
        $save = $this->update($id, $Data);
      } else {
        $Data['created_by'] = $this->auth->user_id();
        $Data['created_at'] = date('Y-m-d H:i:s');
        $save = $this->insert($Data);
      }

      if (!$save || $this->db->trans_status() === FALSE) {
        throw new Exception('Failed to save data form. Please try again.');
      }

      $this->db->trans_commit();

      return array(
        'status' => 1,
        'msg'    => 'Data Form successfully saved.',
      );
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array(
        'status' => 0,
        'msg'    => $e->getMessage(),
      );
    }
  }

  private function _uploadFile()
  {
    $company_id = $this->session->company->id_perusahaan;
    $path = FCPATH . 'directory/FORMS/' . $company_id . '/';
    $Data = $this->input->post();

    if (empty($_FILES['form_file']['name'])) {
      return array(
        'status' => 0,
        'error'  => 'File gagal diupload. Kemungkinan ukuran file melebihi batas server.'
      );
    }

    if (!is_dir($path)) {
      mkdir($path, 0755, TRUE);
    }

    // PHP 5.6 compatible random string (openssl_random_pseudo_bytes sebagai fallback random_bytes)
    $randomHex = bin2hex(openssl_random_pseudo_bytes(6));

    $config['upload_path']   = $path;
    $config['allowed_types'] = 'pdf|xlsx|xls|docx';
    $config['encrypt_name']  = false;
    $config['max_size']      = 5120;
    $config['remove_spaces'] = true;
    $config['file_name']     = slugify($Data['number'] . '-' . $Data['name']) . '-' . date('Ymd') . '-' . $randomHex;
    $this->upload->initialize($config);

    if (!$this->upload->do_upload('form_file')) {
      $error = $this->upload->display_errors();
      return array(
        'status' => 0,
        'error'  => $error
      );
    } else {
      $file              = $this->upload->data();
      $data['file_name'] = $file['file_name'];
      $data['size']      = $file['file_size'];
      $data['ext']       = $file['file_ext'];

      return array(
        'status' => 1,
        'data'   => $data
      );
    }
  }

  private function _validatePic($reviewer_position_id, $approver_position_id, $company_id)
  {
    if (!empty($reviewer_position_id)) {
      $reviewer = $this->db->get_where('positions', array(
        'id'         => $reviewer_position_id,
        'company_id' => $company_id,
      ))->row();
      if (!$reviewer) {
        return 'PIC Reviewer tidak valid atau tidak ditemukan untuk perusahaan ini.';
      }
    }

    if (!empty($approver_position_id)) {
      $approver = $this->db->get_where('positions', array(
        'id'         => $approver_position_id,
        'company_id' => $company_id,
      ))->row();
      if (!$approver) {
        return 'PIC Approver tidak valid atau tidak ditemukan untuk perusahaan ini.';
      }
    }

    if (!empty($reviewer_position_id) && !empty($approver_position_id)
      && (int)$reviewer_position_id === (int)$approver_position_id
    ) {
      return 'PIC Reviewer dan PIC Approver tidak boleh jabatan yang sama.';
    }

    return null;
  }

  private function _insertStatusLog($form_id, $old_status, $new_status, $note = null)
  {
    $log = array(
      'form_id'    => $form_id,
      'old_status' => $old_status,
      'new_status' => $new_status,
      'action_by'  => $this->auth->user_id(),
      'action_at'  => date('Y-m-d H:i:s'),
      'note'       => $note,
      'created_at' => date('Y-m-d H:i:s'),
    );

    $inserted = $this->db->insert('form_status_logs', $log);
    if (!$inserted) {
      throw new Exception('Gagal menyimpan log status form.');
    }
  }

  public function reviewProcess()
  {
    try {
      $id = $this->input->post('id');
      if (empty($id)) {
        throw new Exception('ID form tidak valid.');
      }

      $form = $this->db->get_where('forms', array('id' => $id))->row();
      if (!$form) {
        throw new Exception('Form tidak ditemukan.');
      }
      $old_status = $form->status;

      $this->db->trans_begin();
      $this->update($id, array('status' => 'REV'));
      $this->_insertStatusLog($id, $old_status, 'REV');

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed to process data form. Please try again.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => 'Data Form successfully updated to review.');
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }

  public function cancelReview()
  {
    try {
      $id = $this->input->post('id');
      if (empty($id)) {
        throw new Exception('ID form tidak valid.');
      }

      $form = $this->db->get_where('forms', array('id' => $id))->row();
      if (!$form) {
        throw new Exception('Form tidak ditemukan.');
      }
      if ($form->status !== 'REV') {
        throw new Exception('Form tidak dalam status Review. Tidak dapat dibatalkan.');
      }

      $this->db->trans_begin();
      $this->update($id, array(
        'status'      => 'DFT',
        'modified_by' => $this->auth->user_id(),
        'modified_at' => date('Y-m-d H:i:s'),
      ));
      $this->_insertStatusLog($id, 'REV', 'DFT');

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Gagal membatalkan review. Silakan coba lagi.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => 'Review dibatalkan. Form dikembalikan ke Draft.');
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }

  public function correctionToReview()
  {
    try {
      $id = $this->input->post('id');
      if (empty($id)) {
        throw new Exception('ID form tidak valid.');
      }

      $form = $this->db->get_where('forms', array('id' => $id))->row();
      if (!$form) {
        throw new Exception('Form tidak ditemukan.');
      }
      if ($form->status !== 'COR') {
        throw new Exception('Form tidak dalam status Correction.');
      }

      $this->db->trans_begin();
      $this->update($id, array(
        'status'      => 'REV',
        'modified_by' => $this->auth->user_id(),
        'modified_at' => date('Y-m-d H:i:s'),
      ));
      $this->_insertStatusLog($id, 'COR', 'REV');

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Gagal memproses ke Review. Silakan coba lagi.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => 'Form berhasil dikembalikan ke proses Review.');
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }

  public function saveReview()
  {
    $data = $this->input->post();
    try {
      $allowed_statuses = array('APV', 'COR');
      if (empty($data['status']) || !in_array($data['status'], $allowed_statuses, true)) {
        throw new Exception('Status review tidak valid. Pilih APV atau COR.');
      }

      $note = isset($data['note']) ? trim($data['note']) : '';
      if ($data['status'] === 'COR' && empty($note)) {
        throw new Exception('Catatan wajib diisi jika aksi adalah Kembalikan (COR).');
      }

      $form = $this->db->get_where('forms', array('id' => $data['id']))->row();
      if (!$form) {
        throw new Exception('Form tidak ditemukan.');
      }
      $old_status = $form->status;

      $this->db->trans_begin();

      $updateData = array(
        'status'      => $data['status'],
        'reviewed_by' => $this->auth->user_id(),
        'reviewed_at' => date('Y-m-d H:i:s'),
      );
      if ($data['status'] === 'COR' && !empty($note)) {
        $updateData['note'] = $note;
      }

      $this->update($data['id'], $updateData);
      $this->_insertStatusLog($data['id'], $old_status, $data['status'], !empty($note) ? $note : null);

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('Failed process review document. Please try again later.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => 'Success process review document.');
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }

  public function saveApprove()
  {
    $data = $this->input->post();

    try {
      $allowed_statuses = array('PUB', 'COR');
      if (empty($data['status']) || !in_array($data['status'], $allowed_statuses, true)) {
        throw new Exception('Status approval tidak valid. Pilih PUB atau COR.');
      }

      $published_date = isset($data['published_date']) ? trim($data['published_date']) : '';
      $note           = isset($data['note']) ? trim($data['note']) : '';

      if ($data['status'] === 'PUB' && empty($published_date)) {
        throw new Exception('Tanggal terbit wajib diisi jika aksi adalah Setujui & Publish.');
      }

      if ($data['status'] === 'COR' && empty($note)) {
        throw new Exception('Catatan wajib diisi jika aksi adalah Kembalikan (COR).');
      }

      $form = $this->db->get_where('forms', array('id' => $data['id']))->row();
      if (!$form) {
        throw new Exception('Form tidak ditemukan.');
      }
      $old_status = $form->status;

      $dataUpdate = array('status' => $data['status']);

      if ($data['status'] == 'PUB') {
        $dataUpdate['published_date'] = $published_date;
        $dataUpdate['approved_by']    = $this->auth->user_id();
        $dataUpdate['approved_at']    = date('Y-m-d H:i:s');
      }

      if ($data['status'] == 'COR') {
        $dataUpdate['note'] = $note;
      }

      $this->db->trans_begin();
      $this->update($data['id'], $dataUpdate);
      $this->_insertStatusLog($data['id'], $old_status, $data['status'], !empty($note) ? $note : null);

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('Failed process approve document. Please try again later.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => 'Success process approve document.');
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }

  public function deleteData($id)
  {
    try {
      $form = $this->db->get_where('forms', array('id' => $id))->row();
      if (!$form) {
        throw new Exception('Form tidak ditemukan.');
      }

      $old_status = $form->status;

      $this->db->trans_begin();
      $this->update($id, ['status' => 'DEL', 'deleted_at' => date('Y-m-d H:i:s')]);
      $this->_insertStatusLog($id, $old_status, 'DEL', 'Delete Document');

      if ($this->db->trans_status() === FALSE) {
        throw new Exception('Failed process delete document. Please try again later.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => 'Success process delete document.');
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }

  public function saveFormRevision($data)
  {
    try {
      if (empty($data['id'])) {
        throw new Exception('ID form tidak valid.');
      }

      $form = $this->db->get_where('forms', array('id' => $data['id']))->row();
      if (!$form) {
        throw new Exception('Form tidak ditemukan.');
      }
      if ($form->status !== 'PUB') {
        throw new Exception('Form tidak dalam status Published. Tidak dapat diajukan revisi.');
      }

      $this->db->trans_begin();
      $this->db->where('id', $form->id)->update('forms', array(
        'status'      => 'RVI',
        'note'        => $data['note'],
        'modified_by' => $this->auth->user_id(),
        'modified_at' => date('Y-m-d H:i:s'),
      ));
      $this->_insertStatusLog($form->id, 'PUB', 'RVI', $data['note']);

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Gagal mengajukan revisi. Silakan coba lagi.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => 'Pengajuan revisi berhasil. Form dikembalikan ke status Revision.');
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }

  public function saveFormDeletion($data)
  {
    try {
      if (empty($data['id'])) {
        throw new Exception('ID form tidak valid.');
      }

      $form = $this->db->get_where('forms', array('id' => $data['id']))->row();
      if (!$form) {
        throw new Exception('Form tidak ditemukan.');
      }
      if ($form->status !== 'PUB') {
        throw new Exception('Form tidak dalam status Published. Tidak dapat diajukan penghapusan.');
      }

      $this->db->trans_begin();
      $this->db->where('id', $form->id)->update('forms', array(
        'status'          => 'HLD',
        'deletion_status' => 'OPN',
        'note'            => $data['note'],
        'modified_by'     => $this->auth->user_id(),
        'modified_at'     => date('Y-m-d H:i:s'),
      ));
      $this->_insertStatusLog($form->id, 'PUB', 'HLD', $data['note']);

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Gagal mengajukan penghapusan. Silakan coba lagi.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => 'Pengajuan penghapusan berhasil. Form masuk ke proses Deletion.');
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }

  public function saveFormRevDeletion($data)
  {
    try {
      $form = $this->db->get_where('forms', array('id' => $data['id']))->row();
      if (!$form || $form->status !== 'HLD') {
        throw new Exception('Form tidak ditemukan atau status tidak valid.');
      }

      $action = $data['action'];

      $this->db->trans_begin();

      if ($action === 'APV') {
        $this->db->where('id', $form->id)->update('forms', array(
          'deletion_status' => 'APV',
          'modified_by'     => $this->auth->user_id(),
          'modified_at'     => date('Y-m-d H:i:s'),
        ));
        $this->_insertStatusLog($form->id, 'HLD', 'HLD', 'Review deletion disetujui — lanjut ke Approval Deletion.');
        $msg = 'Pengajuan deletion disetujui. Lanjut ke proses Approval Deletion.';
      } else {
        $this->db->where('id', $form->id)->update('forms', array(
          'status'          => 'PUB',
          'deletion_status' => null,
          'note'            => null,
          'modified_by'     => $this->auth->user_id(),
          'modified_at'     => date('Y-m-d H:i:s'),
        ));
        $this->_insertStatusLog($form->id, 'HLD', 'PUB', 'Pengajuan deletion ditolak pada tahap Review.');
        $msg = 'Pengajuan deletion ditolak. Form dikembalikan ke status Published.';
      }

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Gagal memproses review deletion. Silakan coba lagi.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => $msg);
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }

  public function saveFormApvDeletion($data)
  {
    try {
      $form = $this->db->get_where('forms', array('id' => $data['id']))->row();
      if (!$form || $form->status !== 'HLD') {
        throw new Exception('Form tidak ditemukan atau status tidak valid.');
      }

      $action = $data['action'];

      $this->db->trans_begin();

      if ($action === 'DEL') {
        $this->db->where('id', $form->id)->update('forms', array(
          'status'          => 'DEL',
          'deletion_status' => 'DEL',
          'modified_by'     => $this->auth->user_id(),
          'modified_at'     => date('Y-m-d H:i:s'),
        ));
        $this->_insertStatusLog($form->id, 'HLD', 'DEL', 'Approval deletion disetujui — form dihapus.');
        $msg = 'Form berhasil dihapus.';
      } else {
        $this->db->where('id', $form->id)->update('forms', array(
          'status'          => 'PUB',
          'deletion_status' => null,
          'note'            => null,
          'modified_by'     => $this->auth->user_id(),
          'modified_at'     => date('Y-m-d H:i:s'),
        ));
        $this->_insertStatusLog($form->id, 'HLD', 'PUB', 'Pengajuan deletion ditolak pada tahap Approval.');
        $msg = 'Pengajuan deletion ditolak. Form dikembalikan ke status Published.';
      }

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Gagal memproses approval deletion. Silakan coba lagi.');
      }

      $this->db->trans_commit();
      return array('status' => 1, 'msg' => $msg);
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array('status' => 0, 'msg' => $e->getMessage());
    }
  }
}
