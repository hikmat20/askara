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
        $form_id = !empty($Data['id']) ? $Data['id'] : null;
        $version_number = null;
        
        if ($form_id) {
          $existing_form = $this->db->get_where('forms', array('id' => $form_id))->row();
          if ($existing_form && $existing_form->status === 'RVI') {
            $this->db->select_max('version_number');
            $this->db->where('form_id', $form_id);
            $max_version = $this->db->get('form_versions')->row();
            
            $version_number = ($max_version && $max_version->version_number) ? $max_version->version_number + 1 : 2; // Default to 2 if first revision
          }
        }

        $uploadFile = $this->_uploadFile($form_id, $version_number);

        if ($uploadFile['status'] == 1) {
          $Data['file_name'] = $uploadFile['data']['file_name'];
          $Data['file_path'] = isset($uploadFile['data']['file_path']) ? $uploadFile['data']['file_path'] : null;
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
        $Data['current_version'] = 1;
        $Data['is_under_revision'] = 0;
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

  private function _uploadFile($form_id = null, $version_number = null)
  {
    $company_id = $this->session->company->id_perusahaan;
    $Data = $this->input->post();

    if (empty($_FILES['form_file']['name'])) {
      return array(
        'status' => 0,
        'error'  => 'File gagal diupload. Kemungkinan ukuran file melebihi batas server.'
      );
    }

    if ($form_id && $version_number) {
      $path = FCPATH . "directory/FORMS/{$company_id}/{$form_id}/v{$version_number}/";
    } else {
      $path = FCPATH . "directory/FORMS/{$company_id}/";
    }

    if (!is_dir($path)) {
      mkdir($path, 0755, TRUE);
    }

    $revision_num = isset($Data['revision_number']) ? $Data['revision_number'] : 0;
    $filename_base = slugify($Data['number'] . '-' . $Data['name']) . '-Rev' . $revision_num;

    $config['upload_path']   = $path;
    $config['allowed_types'] = 'pdf|xlsx|xls|docx';
    $config['encrypt_name']  = false;
    $config['overwrite']     = true;
    $config['max_size']      = 5120;
    $config['remove_spaces'] = true;
    $config['file_name']     = $filename_base;
    $this->upload->initialize($config);

    if (!$this->upload->do_upload('form_file')) {
      $error = $this->upload->display_errors();
      return array(
        'status' => 0,
        'error'  => $error
      );
    } else {
      $file              = $this->upload->data();
      $relative_path     = str_replace(FCPATH, '', $path);
      
      $data['file_name'] = $file['file_name'];
      $data['file_path'] = $relative_path . $file['file_name'];
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

      if (empty($form->reviewer_position_id) || empty($form->approver_position_id)) {
        throw new Exception('Harap mengisi data PIC Reviewer dan PIC Approver terlebih dahulu sebelum mengajukan review.');
      }

      $old_status = $form->status;

      $this->db->trans_begin();
      $this->update($id, array('status' => 'REV'));
      $this->_insertStatusLog($id, $old_status, 'REV');

      // Trigger email notification
      $this->_send_email_notification($id, 'REV');

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

      if (empty($form->reviewer_position_id) || empty($form->approver_position_id)) {
        throw new Exception('Harap mengisi data PIC Reviewer dan PIC Approver terlebih dahulu sebelum mengajukan review.');
      }
      
      if ($form->status !== 'COR') {
        throw new Exception('Form tidak dalam status Correction.');
      }

      $note_post = $this->input->post('note');
      $note = isset($note_post) ? trim($note_post) : null;

      $this->db->trans_begin();
      $this->update($id, array(
        'status'      => 'REV',
        'modified_by' => $this->auth->user_id(),
        'modified_at' => date('Y-m-d H:i:s'),
      ));

      $this->_insertStatusLog($id, 'COR', 'REV', $note);

      // Trigger email notification
      $this->_send_email_notification($id, 'REV');

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

      // Trigger email notification
      $this->_send_email_notification($data['id'], $data['status'], $note);

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
        
        // VERSION CONTROL: Save version snapshot
        $revision_description = isset($data['revision_description']) ? trim($data['revision_description']) : null;
        $new_version = $this->saveVersionSnapshot($data['id'], $revision_description);
        
        if ($new_version === false) {
          throw new Exception('Gagal menyimpan version snapshot.');
        }
        
        // Update version tracking fields
        $dataUpdate['current_version'] = $new_version;
        $dataUpdate['is_under_revision'] = 0;
      }

      if ($data['status'] == 'COR') {
        $dataUpdate['note'] = $note;
      }

      $this->db->trans_begin();
      $this->update($data['id'], $dataUpdate);
      $this->_insertStatusLog($data['id'], $old_status, $data['status'], !empty($note) ? $note : null);

      // Trigger email notification
      $this->_send_email_notification($data['id'], $data['status'], $note);

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
        'status'            => 'RVI',
        'is_under_revision' => 1,
        'note'              => $data['note'],
        'modified_by'       => $this->auth->user_id(),
        'modified_at'       => date('Y-m-d H:i:s'),
      ));
      $this->_insertStatusLog($form->id, 'PUB', 'RVI', $data['note']);

      // Trigger email notification
      $this->_send_email_notification($form->id, 'RVI', $data['note']);

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

  // ============================================================================
  // VERSION CONTROL METHODS FOR FORMS
  // ============================================================================

  /**
   * Save version snapshot for forms
   */
  public function saveVersionSnapshot($form_id, $description = null)
  {
    try {
      // Step 1: Get form data from master table
      $form = $this->db->get_where('forms', array('id' => $form_id))->row();
      
      if (!$form) {
        throw new Exception('Form tidak ditemukan.');
      }

      // Step 2: Calculate next version_number (MAX + 1)
      $this->db->select_max('version_number');
      $this->db->where('form_id', $form_id);
      $max_version = $this->db->get('form_versions')->row();
      
      $next_version = ($max_version && $max_version->version_number) ? $max_version->version_number + 1 : 1;

      // Step 3: Begin database transaction
      $this->db->trans_begin();

      // Step 4: Mark old version as superseded (is_current = 0)
      $this->db->where('form_id', $form_id);
      $this->db->where('is_current', 1);
      $this->db->update('form_versions', array(
        'is_current'    => 0,
        'superseded_at' => date('Y-m-d H:i:s'),
        'superseded_by' => $next_version
      ));

      // Step 5: Insert new version record
      $version_data = array(
        'form_id'        => $form_id,
        'version_number' => $next_version,
        
        // File snapshot
        'file_name'      => $form->file_name,
        'file_path'      => isset($form->file_path) ? $form->file_path : null,
        'ext'            => $form->ext,
        'size'           => isset($form->size) ? $form->size : null,
        
        // Metadata snapshot
        'number'          => $form->number,
        'name'            => $form->name,
        'revision_number' => isset($form->revision_number) ? $form->revision_number : 0,
        'published_date'  => isset($form->published_date) ? $form->published_date : date('Y-m-d'),
        'effective_date'  => isset($form->effective_date) ? $form->effective_date : null,
        
        // Audit trail
        'published_by'    => $this->auth->user_id(),
        'published_at'    => date('Y-m-d H:i:s'),
        
        // Status
        'is_current'      => 1,
        
        // Description
        'description'     => $description
      );

      $inserted = $this->db->insert('form_versions', $version_data);
      
      if (!$inserted) {
        throw new Exception('Gagal menyimpan version snapshot.');
      }

      // Step 6: Commit transaction
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Transaction failed saat menyimpan version snapshot.');
      }

      $this->db->trans_commit();

      // Step 7: Return new version_number
      return $next_version;

    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'saveVersionSnapshot (forms) failed: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Get current version to display (handles under revision scenario)
   */
  public function getCurrentVersion($form_id)
  {
    $form = $this->db->get_where('forms', array('id' => $form_id))->row();
    
    if (!$form) {
      return null;
    }

    if (isset($form->is_under_revision) && $form->is_under_revision == 1) {
      $this->db->select('fv.*, u.full_name as publisher_name');
      $this->db->from('form_versions fv');
      $this->db->join('users u', 'fv.published_by = u.id_user', 'left');
      $this->db->where('fv.form_id', $form_id);
      $this->db->where('fv.is_current', 1);
      
      $version = $this->db->get()->row();
      
      if ($version) {
        $version->is_from_history = true;
        $version->under_revision = true;
        return $version;
      }
    }

    $form->is_from_history = false;
    $form->under_revision = false;
    return $form;
  }

  /**
   * Get version history for a form
   */
  public function getVersionHistory($form_id)
  {
    $this->db->select('fv.*, u.full_name as publisher_name, u.username as publisher_username');
    $this->db->from('form_versions fv');
    $this->db->join('users u', 'fv.published_by = u.id_user', 'left');
    $this->db->where('fv.form_id', $form_id);
    $this->db->order_by('fv.version_number', 'DESC');
    
    return $this->db->get()->result();
  }

  /**
   * Get specific version by version number
   */
  public function getVersionByNumber($form_id, $version_number)
  {
    $this->db->select('fv.*, u.full_name as publisher_name, u.username as publisher_username');
    $this->db->from('form_versions fv');
    $this->db->join('users u', 'fv.published_by = u.id_user', 'left');
    $this->db->where('fv.form_id', $form_id);
    $this->db->where('fv.version_number', $version_number);
    
    return $this->db->get()->row();
  }

  /**
   * Mengirim notifikasi email sesuai perubahan status (Workflow)
   */
  private function _send_email_notification($form_id, $new_status, $note = '')
  {
      $form = $this->db->get_where('forms', ['id' => $form_id])->row();
      if (!$form) return;

      // Tentukan target *position_id* dan *user_id* berdasarkan status baru
      $target_position_ids = [];
      $target_user_ids = [];
      
      $subject_prefix = "[ISO-Platform] ";
      $message = "<h3>Notifikasi Dokumen Kontrol</h3>";
      $message .= "<p>Dokumen form <strong>" . $form->name . "</strong> mengalami pembaruan status.</p>";
      
      switch ($new_status) {
          case 'REV':
              $target_position_ids[] = $form->reviewer_position_id;
              $subject = $subject_prefix . "Membutuhkan Review Anda";
              $message .= "<p>Dokumen telah diajukan kepada Anda untuk proses <strong>Review</strong>. Harap segera diperiksa.</p>";
              break;
          case 'COR':
              $target_user_ids[] = $form->created_by;
              if ($form->reviewer_position_id) $target_position_ids[] = $form->reviewer_position_id;
              $subject = $subject_prefix . "Dokumen Membutuhkan Koreksi";
              $message .= "<p>Dokumen Anda dikembalikan karena membutuhkan <strong>Koreksi</strong>.</p>";
              break;
          case 'APV':
              $target_position_ids[] = $form->approver_position_id;
              $subject = $subject_prefix . "Membutuhkan Approval Anda";
              $message .= "<p>Dokumen telah lolos review dan kini menunggu tahapan akhir <strong>Approval</strong> dari Anda.</p>";
              break;
          case 'PUB':
              $target_user_ids[] = $form->created_by;
              $target_position_ids[] = $form->reviewer_position_id;
              $target_position_ids[] = $form->approver_position_id;
              $subject = $subject_prefix . "Dokumen Telah Rilis (Published)";
              $message .= "<p>Dokumen telah disetujui secara keseluruhan dan resmi berstatus <strong>Published / Rilis</strong>.</p>";
              break;
          case 'RVI':
              $target_user_ids[] = $form->created_by;
              if ($form->reviewer_position_id) $target_position_ids[] = $form->reviewer_position_id;
              $subject = $subject_prefix . "Pengajuan Revisi Dokumen";
              $message .= "<p>Terdapat pengajuan <strong>Revisi</strong> pada dokumen ini.</p>";
              break;
          default:
              return;
      }

      if (!empty($note) && $note !== '~') {
          $message .= "<br><p><strong>Catatan Tambahan:</strong><br><i>\"" . $note . "\"</i></p>";
      }
      $message .= "<br><p>Silakan login ke aplikasi untuk melihat detail dokumen ini.</p>";

      // Konversi Position_ID menjadi User_ID jika ada target berupa position
      $target_position_ids = array_unique(array_filter($target_position_ids));
      if (!empty($target_position_ids)) {
          $this->db->where_in('id', $target_position_ids);
          $positions = $this->db->get('positions')->result();
          foreach ($positions as $pos) {
              if ($pos->assign_user) {
                  $target_user_ids[] = $pos->assign_user; // Kumpulkan assign_user (user_id asli)
              }
          }
      }

      $target_user_ids = array_unique(array_filter($target_user_ids)); 
      if (empty($target_user_ids)) return;

      // Ambil email user
      $this->db->where_in('id_user', $target_user_ids);
      $users = $this->db->get('users')->result();

      $emails = [];
      foreach ($users as $u) {
          if (!empty($u->email)) {
              $emails[] = $u->email;
          }
      }

      // Masukkan antrean (ke Cron / email_queues)
      if (!empty($emails)) {
          $this->load->library('email_runner');

          // Tentukan link spesifik berdasarkan status
          $action = 'view';
          if ($new_status == 'REV') $action = 'review';
          if ($new_status == 'APV') $action = 'approval';
          
          $action_url = base_url('monitoring/' . $action);
          
          $this->email_runner->queue($emails, $subject, $message, null, $action_url);
      }
  }
}
