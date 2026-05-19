<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Yunas Handra
 * @copyright Copyright (c) 2018, Yunas Handra
 *
 * This is model class for table "Customer"
 */

class Procedure_model extends BF_Model
{

  /**
   * @var string  User Table Name
   */
  protected $table_name = 'procedures';
  protected $key        = 'id';

  /**
   * @var string Field name to use for the created time column in the DB table
   * if $set_created is enabled.
   */
  protected $created_field = 'create_at';

  /**
   * @var string Field name to use for the modified time column in the DB
   * table if $set_modified is enabled.
   */
  protected $modified_field = 'modified_at';

  /**
   * @var bool Set the created time automatically on a new record (if true)
   */
  protected $set_created = true;

  /**
   * @var bool Set the modified time automatically on editing a record (if true)
   */
  protected $set_modified = true;
  /**
   * @var string The type of date/time field used for $created_field and $modified_field.
   * Valid values are 'int', 'datetime', 'date'.
   */
  /**
   * @var bool Enable/Disable soft deletes.
   * If false, the delete() method will perform a delete of that row.
   * If true, the value in $deleted_field will be set to 1.
   */
  protected $soft_deletes = false;

  protected $date_format = 'datetime';

  /**
   * @var bool If true, will log user id in $created_by_field, $modified_by_field,
   * and $deleted_by_field.
   */
  protected $log_user = true;

  /**
   * Function construct used to load some library, do some actions, etc.
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function getDataProcedureById($id)
  {
    /* join with departement and group procedure*/
    return $this->db->get_where('view_procedures', ['id' => $id])->row();
  }

  public function getBilingualProcedure($id)
  {
    return $this->db->get_where('procedure_bilingual', ['procedure_id' => $id])->row();
  }

  public function getRevisionLogProcedure($id)
  {
    return $this->db->get_where('procedure_revision_logs', ['procedure_id' => $id])->result();
  }

  public function getArrayDepartements()
  {
    $data = $this->db->get_where('departements', ['status' => '1'])->result_array();
    return array_column($data, 'name', 'id');
  }

  public function getArrayPosition()
  {
    $data = $this->db->get_where('positions')->result_array();
    
    return array_column($data, 'name', 'id');
  }

  public function getArrayUser()
  {
    $data = $this->db->get_where('users', ['status' => 'ACT'])->result_array();
    return array_column($data, 'full_name', 'id_user');
  }

  public function getArrayForm($id)
  {
    $data = $this->db->get_where('view_forms', ['status !=' => 'DEL'])->result_array();
    return array_column($data, 'name', 'id');
  }

  public function getArrayWorkInstruction($id)
  {
    $data = $this->db->get_where('work_instructions', ['status !=' => 'DEL'])->result_array();
    return array_column($data, 'name', 'id');
  }

  public function getDetailProcedureById($id)
  {
    $data = $this->db->get_where('procedure_details', ['procedure_id' => $id])->result();
    return $data;
  }

  public function getActivityProcedure($id)
  {
    $data = $this->db->order_by('last_update', 'desc')->get_where('procedure_activity_logs', ['procedure_id' => $id])->result();
    return $data;
  }

  // getLogsProcedure
  public function getLogsProcedure($id)
  {
    return $this->db->get_where('view_directory_log', ['directory_id' => $id])->result();
  }

  public function viewDataProcedure($id)
  {
    $data['procedure']         = $this->getDataProcedureById($id);
    $data['bilingual']         = $this->getBilingualProcedure($id);
    $data['revision_logs']     = $this->getRevisionLogProcedure($id);
    $data['depts']             = $this->getArrayDepartements();
    $data['positions']         = $this->getArrayPosition();
    $data['users']             = $this->getArrayUser();
    $data['detail']            = $this->getDetailProcedureById($id);
    $data['activity']          = $this->getActivityProcedure($id);
    $data['forms']             = $this->getArrayForm($id);
    $data['work_instructions'] = $this->getArrayWorkInstruction($id);
    $data['company']           = $this->session->userdata['company'];
    $data['logs']              = $this->getLogsProcedure($id);

    return $data;
  }

  private function _update_history($data, $procedure)
  {
    $dataLog = [
      'directory_id' => $procedure->id,
      'old_status'   => $procedure->status,
      'new_status'   => $data['status'],
      'doc_type'     => 'Procedure',
      'updated_by'     => $this->auth->user_id(),
      'updated_at'     => date('Y-m-d H:i:s'),
    ];

    if ($procedure->status == 'DFT') {
      $dataLog['note'] = 'Procesed to review procedure';
    }

    $this->db->insert('directory_log', $dataLog);
  }

  public function processReview($id)
  {
    $Return = array(
      'status' => 0,
      'msg'    => 'Invalid procedure ID.',
    );

    try {
      if ($id) {
        $this->db->trans_begin();
        $procedure = $this->db->get_where('procedures', ['id' => $id])->row();
        if ($procedure->reviewer_id == '' || $procedure->reviewer_id == null || $procedure->approval_id == '' || $procedure->approval_id == null) {
          throw new Exception("Please select Reviewer User And Approval User first to go to the next process.");
        }

        $data['modified_by'] = $this->auth->user_id();
        $data['modified_at'] = date('Y-m-d H:i:s');
        $data['status']      = 'REV';

        $this->update(['company_id' => 1, 'id' => $id], $data);
        $this->_update_history($data, $procedure);

        if ($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          throw new Exception("Failed to process data. Please try again.");
        } else {
          $this->db->trans_commit();
          
          // Trigger email notification
          $this->_send_email_notification($id, 'REV');

          $Return    = array(
            'status'    => 1,
            'msg'      => 'Data Procedure successfully processed for review.',
          );
        }
      }
    } catch (\Throwable $e) {
      $this->db->trans_rollback();
      $Return    = array(
        'status'    => 0,
        'msg'      => $e->getMessage(),
      );
    }
    return $Return;
  }

  public function saveProcedure()
  {
    $Data = $this->input->post();
    $this->db->trans_begin();

    try {
      $this->db->insert('procedures', $Data);
      $pro_id = $this->db->insert_id();

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception("Failed to save procedure. Please try again.");
      } else {
        $this->db->trans_commit();
        $Return = array(
          'status' => 1,
          'msg' => 'Procedure successfully saved.',
          'id' => $pro_id
        );
      }
    } catch (\Throwable $th) {
      $this->db->trans_rollback();
      $Return = array(
        'status' => 0,
        'msg' => $th->getMessage(),
      );
    }

    return $Return;
  }

  /**
   * Mengirim notifikasi email sesuai perubahan status (Workflow)
   */
  private function _send_email_notification($procedure_id, $new_status, $note = '')
  {
      $procedure = $this->db->get_where('procedures', ['id' => $procedure_id])->row();
      if (!$procedure) return;

      // Tentukan target *position_id* dan *user_id* berdasarkan status baru
      $target_position_ids = [];
      $target_user_ids = [];
      
      $subject_prefix = "[ISO-Platform] ";
      $message = "<h3>Notifikasi Dokumen Kontrol</h3>";
      $message .= "<p>Dokumen prosedur <strong>" . $procedure->name . "  (" . $procedure->nomor . ")</strong> mengalami pembaruan status.</p>";
      
      switch ($new_status) {
          case 'REV':
              $target_position_ids[] = $procedure->reviewer_id;
              $subject = $subject_prefix . "Membutuhkan Review Anda";
              $message .= "<p>Dokumen telah diajukan kepada Anda untuk proses <strong>Review</strong>. Harap segera diperiksa.</p>";
              break;
          case 'COR':
              $target_user_ids[] = $procedure->created_by;
              if ($procedure->prepared_id) $target_position_ids[] = $procedure->prepared_id;
              $subject = $subject_prefix . "Dokumen Membutuhkan Koreksi";
              $message .= "<p>Dokumen Anda dikembalikan karena membutuhkan <strong>Koreksi</strong>.</p>";
              break;
          case 'APV':
              $target_position_ids[] = $procedure->approval_id;
              $subject = $subject_prefix . "Membutuhkan Approval Anda";
              $message .= "<p>Dokumen telah lolos review dan kini menunggu tahapan akhir <strong>Approval</strong> dari Anda.</p>";
              break;
          case 'PUB':
              $target_user_ids[] = $procedure->created_by;
              $target_position_ids[] = $procedure->reviewer_id;
              $target_position_ids[] = $procedure->approval_id;
              $subject = $subject_prefix . "Dokumen Telah Rilis (Published)";
              $message .= "<p>Dokumen telah disetujui secara keseluruhan dan resmi berstatus <strong>Published / Rilis</strong>.</p>";
              break;
          case 'RVI':
              $target_user_ids[] = $procedure->created_by;
              if ($procedure->reviewer_id) $target_position_ids[] = $procedure->reviewer_id;
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
