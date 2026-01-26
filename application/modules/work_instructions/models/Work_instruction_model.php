<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Work_instruction_model extends BF_Model
{

  /**
   * @var string  User Table Name
   */
  protected $table_name = 'work_instructions';
  protected $key        = 'id';

  /**
   * @var string Field name to use for the created time column in the DB table
   * if $set_created is enabled.
   */
  protected $created_field = 'created_at';

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

  public function getAllByStatus($status)
  {
    return $this->db->get_where('view_work_instructions', ['status' => $status])->result();
  }

  public function saveData()
  {
    try {
      $Data = $this->input->post();

      $this->db->trans_begin();
      if ($Data) {
        $Data['company_id'] = 1;

        if (isset($_FILES['file']) && $_FILES['file']['name'] != '') {
          $uploadFile        = $this->_uploadFile();
          $Data['file_name'] = $uploadFile['file_name'];
          $Data['size']      = $uploadFile['size'];
          $Data['ext']       = $uploadFile['ext'];
        }

        if (isset($Data['id']) && $Data['id']) {
          $Data['modified_by'] = $this->auth->user_id();
          $Data['modified_at'] = date('Y-m-d H:i:s');
          $this->update($Data['id'], $Data);
        } else {
          $Data['created_by'] = $this->auth->user_id();
          $Data['created_at'] = date('Y-m-d H:i:s');
          $this->insert($Data);
        }
      }

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed to save data work instruction . Please try again.');
      } else {
        $this->db->trans_commit();
        $Return    = array(
          'status'    => 1,
          'msg'      => 'Data work instruction successfully saved..',
        );
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

  private function _uploadFile()
  {
    $path = FCPATH . "directory/WI/1/";
    $Data = $this->input->post();

    if (!is_dir($path)) {
      mkdir($path, 0755, TRUE);
    }

    $config['upload_path']   = $path; //path folder
    $config['allowed_types'] = 'pdf'; //type yang dapat diakses bisa anda sesuaikan
    $config['encrypt_name']  = false; //Enkripsi nama yang terupload
    $config['max_size']      = 2048;
    $config['remove_spaces'] = true;
    $config['file_name']     = slugify($Data['number'] . '-' . $Data['name']) . '-' . date('Ymd') . '-' . bin2hex(random_bytes(6));

    $this->upload->initialize($config);
    if ($this->upload->do_upload('file')) :
      $file              = $this->upload->data();
      $data['file_name'] = $file['file_name'];
      $data['size']      = $file['file_size'];
      $data['ext']       = $file['file_ext'];
      return $data;
    else :
      $error = $this->upload->display_errors();
      $this->db->trans_rollback();
      $Return = [
        'status' => 0,
        'msg'   => $error
      ];
      echo json_encode($Return);
      return;
    endif;
  }

  public function reviewProcess()
  {
    try {
      $id = $this->input->post('id');
      $this->db->trans_begin();
      $this->update($id, ['status' => 'REV']);

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed to process data work instruction . Please try again.');
      } else {
        $this->db->trans_commit();
        $Return    = array(
          'status'    => 1,
          'msg'      => 'Data work instruction successfully updated to review..',
        );
      }
    } catch (\Throwable $th) {
      $Return    = [
        'status'    => 0,
        'msg'      => $th->getMessage(),
      ];
    }
    return $Return;
  }

  public function saveReview()
  {
    $data = $this->input->post();
    try {
      $this->db->trans_begin();
      $this->update(
        $data['id'],
        [
          'status'      => $data['status'],
          'reviewed_by' => $this->auth->user_id(),
          'reviewed_at' => date('Y-m-d H:i:s'),
        ]
      );
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed process review document. Please try again later.');
      } else {
        $this->db->trans_commit();
        $Return = [
          'status' => 1,
          'msg'     => 'Success process review document...'
        ];
      }
    } catch (\Throwable $th) {
      $this->db->trans_rollback();
      $Return = [
        'status' => 0,
        'msg'     => $th->getMessage()
      ];
    }

    return $Return;
  }

  public function saveApprove()
  {
    $data = $this->input->post();

    try {
      $dataUpdate['status'] = $data['status'];
      
      if ($data['status'] == 'APV') {
        $dataUpdate['published_date'] = $data['published_date'];
        $dataUpdate['approved_by']    = $this->auth->user_id();
        $dataUpdate['approved_at']    = date('Y-m-d H:i:s');
      }

      if ($data['status'] == 'COR') {
        $dataUpdate['note']           = $data['note'];
      }

      $this->db->trans_begin();
      $this->update(
        $data['id'],
        $dataUpdate
      );

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed process approve document. Please try again later.');
      } else {
        $this->db->trans_commit();
        $Return = [
          'status' => 1,
          'msg'     => 'Success process approve document...'
        ];
      }
    } catch (\Throwable $th) {
      $this->db->trans_rollback();
      $Return = [
        'status' => 0,
        'msg'     => $th->getMessage()
      ];
    }
    return $Return;
  }
}
