<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Yunas Handra
 * @copyright Copyright (c) 2018, Yunas Handra
 *
 * This is model class for table "Customer"
 */

class Company_reference_model extends BF_Model
{

  /**
   * @var string  User Table Name
   */
  protected $table_name = 'company_reference';
  protected $key        = 'id';

  /**
   * @var string Field name to use for the created time column in the DB table
   * if $set_created is enabled.
   */
  protected $created_field = 'create_on';

  /**
   * @var string Field name to use for the modified time column in the DB
   * table if $set_modified is enabled.
   */
  protected $modified_field = 'modified_on';

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

  public function saveData($Data = null)
  {
    $DataStd   = (isset($Data['standards'])) ? $Data['standards'] : '';
    $DataReg   = (isset($Data['regulations'])) ? $Data['regulations'] : '';

    unset($Data['standards']);
    unset($Data['regulations']);

    if (isset($Data['id'])) {
      $Id                 = $Data['id'];
      $Data['modified_by'] = $this->auth->user_id();
      $Data['modified_at'] = date('Y-m-d H:i:s');
      $this->db->update('references', $Data, ['id' => $Data['id']]);
    } else {

      /* Check Exists Data */
      $checkData = $this->db->get_where('references', ['company_id' => $Data['company_id'], 'branch_id' => $Data['branch_id']])->num_rows();
      if ($checkData > 0) {
        return ['exists' => 1];
      }

      // $Data['company_id'] = $Data['company_id'];
      $Data['created_by'] = $this->auth->user_id();
      $Data['created_at'] = date('Y-m-d H:i:s');

      $this->db->insert('references', $Data);
      // $Id = $this->db->order_by('id', 'DESC')->get_where('references')->row()->id;
      $Id = $this->db->insert_id('references');
    }

    /* List Standard */
    if ($DataStd) {
      foreach ($DataStd as $std) {
        if (isset($std['id'])) {
          // $std['reference_id'] = $Id;
          $std['modified_by'] = $this->auth->user_id();
          $std['modified_at'] = date('Y-m-d H:i:s');
          $this->db->update('ref_standards', $std, ['id' => $std['id']]);
        } else {
          $std['reference_id'] = $Id;
          $std['created_by'] = $this->auth->user_id();
          $std['created_at'] = date('Y-m-d H:i:s');
          $this->db->insert('ref_standards', $std);
        }
      }
    }

    /* List Regulation */
    if ($DataReg) {
      foreach ($DataReg as $reg) {
        if (isset($reg['id'])) {
          $reg['modified_by'] = $this->auth->user_id();
          $reg['modified_at'] = date('Y-m-d H:i:s');
          $this->db->update('ref_regulations', $reg, ['id' => $reg['id']]);
        } else {
          $reg['reference_id'] = $Id;
          $reg['created_by']  = $this->auth->user_id();
          $reg['created_at']  = date('Y-m-d H:i:s');
          $this->db->insert('ref_regulations', $reg);
        }
      }
    }

    return ['id' => $Id];
  }
}
