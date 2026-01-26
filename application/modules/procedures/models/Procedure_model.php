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
          $Return    = array(
            'status'    => 1,
            'msg'      => 'Data Procedure successfully processed for review.',
          );
        }
      }
    } catch (\Throwable $th) {
      $this->db->trans_rollback();
      $Return    = array(
        'status'    => 0,
        'msg'      => $th->getMessage(),
      );
    }

    return $Return;
  }

  public function saveProcedure()
  {
    $Data = $this->input->post();
    $this->db->trans_begin();

    try {
      // echo '<pre>';
      // print_r($Data);
      // echo '</pre>';
      // exit;
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
}
