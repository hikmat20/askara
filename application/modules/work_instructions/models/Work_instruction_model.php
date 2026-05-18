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

  public function getAll()
  {
    return $this->db->get_where('view_work_instructions', ['status !=' => 'DEL'])->result();
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

        // Validate PIC Reviewer and Approver if provided
        $reviewer_position_id = isset($Data['reviewer_position_id']) && $Data['reviewer_position_id'] !== '' ? $Data['reviewer_position_id'] : null;
        $approver_position_id = isset($Data['approver_position_id']) && $Data['approver_position_id'] !== '' ? $Data['approver_position_id'] : null;

        if ($reviewer_position_id !== null || $approver_position_id !== null) {
          $validation_error = $this->_validatePic($reviewer_position_id, $approver_position_id, $Data['company_id']);
          if ($validation_error !== null) {
            throw new Exception($validation_error);
          }
        }

        // Handle file upload
        if (isset($_FILES['file']) && $_FILES['file']['name'] != '') {
          
          // Determine if this is an update with versioning
          $wi_id = isset($Data['id']) ? $Data['id'] : null;
          $version_number = null;
          
          if ($wi_id) {
            // Get existing work instruction to check status
            $existing_wi = $this->db->get_where('work_instructions', array('id' => $wi_id))->row();
            
            if ($existing_wi && $existing_wi->status === 'RVI') {
              // Under revision - calculate next version number
              $this->db->select_max('version_number');
              $this->db->where('work_instruction_id', $wi_id);
              $max_version = $this->db->get('work_instruction_versions')->row();
              
              $version_number = ($max_version && $max_version->version_number) ? $max_version->version_number + 1 : 1;
            }
          }
          
          // Upload file with version support
          $uploadFile = $this->_uploadFile($wi_id, $version_number);
          
          $fileData = $uploadFile;
          $Data['file_name'] = $fileData['file_name'];
          $Data['file_path'] = $fileData['file_path'];
          $Data['size']      = $fileData['size'];
          $Data['ext']       = $fileData['ext'];
        }

        if (isset($Data['id']) && $Data['id']) {
          // Update existing record
          $Data['modified_by'] = $this->auth->user_id();
          $Data['modified_at'] = date('Y-m-d H:i:s');
          $this->update($Data['id'], $Data);
        } else {
          // Insert new record
          $Data['created_by'] = $this->auth->user_id();
          $Data['created_at'] = date('Y-m-d H:i:s');
          $Data['current_version'] = 1;
          $Data['is_under_revision'] = 0;
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

  /**
   * Upload file with version support
   * 
   * @param int|null $work_instruction_id Work instruction ID (for versioning)
   * @param int|null $version_number Version number (for folder structure)
   * @return array File data (file_name, file_path, size, ext)
   * @throws Exception if upload fails
   */
  private function _uploadFile($work_instruction_id = null, $version_number = null)
  {
    $Data = $this->input->post();

    // Determine version folder structure
    if ($work_instruction_id && $version_number) {
      // Version-based folder: directory/WI/1/{wi_id}/v{version}/
      $path = FCPATH . "directory/WI/1/{$work_instruction_id}/v{$version_number}/";
    } else {
      // Legacy folder for new documents: directory/WI/1/
      $path = FCPATH . "directory/WI/1/";
    }

    // Create folder if not exists
    if (!is_dir($path)) {
      mkdir($path, 0755, TRUE);
    }

    // Generate filename with revision number
    $revision_num = isset($Data['revision_number']) ? $Data['revision_number'] : 0;
    $filename_base = slugify($Data['number'] . '-' . $Data['name']) . '-Rev' . $revision_num;

    $config['upload_path']   = $path;
    $config['allowed_types'] = 'xlsx|xls|pdf|docx|doc';
    $config['encrypt_name']  = false;
    $config['max_size']      = 10148; // 10MB
    $config['remove_spaces'] = true;
    $config['file_name']     = $filename_base;

    $this->upload->initialize($config);
    
    if ($this->upload->do_upload('file')) {
      $file = $this->upload->data();
      
      // Calculate relative path from FCPATH
      $relative_path = str_replace(FCPATH, '', $path);
      
      $data['file_name'] = $file['file_name'];
      $data['file_path'] = $relative_path . $file['file_name'];
      $data['size']      = $file['file_size'];
      $data['ext']       = $file['file_ext'];
      
      return $data;
    } else {
      $error = $this->upload->display_errors();
      throw new Exception($error);
    }
  }

  /**
   * Validate PIC (Person In Charge) positions for reviewer and approver
   * 
   * @param int|null $reviewer_position_id Reviewer position ID
   * @param int|null $approver_position_id Approver position ID
   * @param int $company_id Company ID
   * @return string|null Returns null if validation passes, error message if validation fails
   */
  private function _validatePic($reviewer_position_id = null, $approver_position_id = null, $company_id)
  {
    // Step 1: Validate reviewer position if provided
    if ($reviewer_position_id !== null) {
      $reviewer = $this->db->get_where('positions', array(
        'id'         => $reviewer_position_id,
        'company_id' => $company_id
      ))->row();

      if (!$reviewer) {
        return 'PIC Reviewer tidak valid atau tidak ditemukan untuk perusahaan ini.';
      }
    }

    // Step 2: Validate approver position if provided
    if ($approver_position_id !== null) {
      $approver = $this->db->get_where('positions', array(
        'id'         => $approver_position_id,
        'company_id' => $company_id
      ))->row();

      if (!$approver) {
        return 'PIC Approver tidak valid atau tidak ditemukan untuk perusahaan ini.';
      }
    }

    // Step 3: Check if reviewer and approver are different
    if ($reviewer_position_id !== null && $approver_position_id !== null) {
      if ($reviewer_position_id === $approver_position_id) {
        return 'PIC Reviewer dan PIC Approver tidak boleh jabatan yang sama.';
      }
    }

    // All validations passed
    return null;
  }

  /**
   * Insert status log entry for audit trail
   * 
   * @param int $work_instruction_id Work instruction ID
   * @param string $old_status Status before change
   * @param string $new_status Status after change
   * @param string|null $note Optional note (required for COR status)
   * @throws Exception if insert fails
   * @return void
   */
  private function _insertStatusLog($work_instruction_id, $old_status, $new_status, $note = null)
  {
    $log = array(
      'work_instruction_id' => $work_instruction_id,
      'old_status'          => $old_status,
      'new_status'          => $new_status,
      'action_by'           => $this->auth->user_id(),
      'action_at'           => date('Y-m-d H:i:s'),
      'note'                => $note,
      'created_at'          => date('Y-m-d H:i:s'),
    );

    $inserted = $this->db->insert('work_instruction_status_logs', $log);
    if (!$inserted) {
      throw new Exception('Gagal menyimpan log status work instruction.');
    }
  }

  /**
   * Process work instruction to review status (DFT -> REV)
   * 
   * @return array Response with status and message
   */
  public function reviewProcess()
  {
    try {
      // Step 1: Validate POST data contains id field
      $id = $this->input->post('id');
      if (empty($id)) {
        throw new Exception('ID work instruction tidak valid.');
      }

      // Step 2: Get work instruction from database
      $work_instruction = $this->db->get_where('work_instructions', array('id' => $id))->row();
      if (!$work_instruction) {
        throw new Exception('Work instruction tidak ditemukan.');
      }
      $old_status = $work_instruction->status;

      // Step 3: Begin database transaction
      $this->db->trans_begin();

      // Step 4: Update status from DFT to REV
      $this->update($id, array('status' => 'REV'));

      // Step 5: Insert status log with transition DFT→REV
      $this->_insertStatusLog($id, $old_status, 'REV');

      // Step 6: Commit transaction or rollback if failed
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed to process data work instruction. Please try again.');
      }

      $this->db->trans_commit();

      // Step 7: Return success response
      return array(
        'status' => 1,
        'msg'    => 'Data work instruction successfully updated to review.'
      );
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array(
        'status' => 0,
        'msg'    => $e->getMessage()
      );
    }
  }

  /**
   * Cancel review and revert work instruction to draft status (REV -> DFT)
   * 
   * @return array Response with status and message
   */
  public function cancelReview()
  {
    try {
      // Step 1: Validate POST data contains id field
      $id = $this->input->post('id');
      if (empty($id)) {
        throw new Exception('ID work instruction tidak valid.');
      }

      // Step 2: Get work instruction and verify status is REV
      $work_instruction = $this->db->get_where('work_instructions', array('id' => $id))->row();
      if (!$work_instruction) {
        throw new Exception('Work instruction tidak ditemukan.');
      }

      if ($work_instruction->status !== 'REV') {
        throw new Exception('Work instruction tidak dalam status review. Pembatalan review hanya dapat dilakukan untuk dokumen dengan status REV.');
      }

      $old_status = $work_instruction->status;

      // Step 3: Begin database transaction
      $this->db->trans_begin();

      // Step 4: Update status from REV to DFT
      // Step 5: Update modified_by and modified_at fields
      $this->update($id, array(
        'status'      => 'DFT',
        'modified_by' => $this->auth->user_id(),
        'modified_at' => date('Y-m-d H:i:s')
      ));

      // Step 6: Insert status log with transition REV→DFT
      $this->_insertStatusLog($id, $old_status, 'DFT');

      // Step 7: Commit transaction or rollback if failed
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed to cancel review work instruction. Please try again.');
      }

      $this->db->trans_commit();

      // Step 8: Return success response
      return array(
        'status' => 1,
        'msg'    => 'Review work instruction successfully cancelled and reverted to draft.'
      );
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array(
        'status' => 0,
        'msg'    => $e->getMessage()
      );
    }
  }

  /**
   * Resubmit work instruction from correction to review status (COR -> REV)
   * 
   * @return array Response with status and message
   */
  public function correctionToReview()
  {
    try {
      // Step 1: Validate POST data contains id field
      $id = $this->input->post('id');
      if (empty($id)) {
        throw new Exception('ID work instruction tidak valid.');
      }

      // Step 2: Get work instruction and verify status is COR
      $work_instruction = $this->db->get_where('work_instructions', array('id' => $id))->row();
      if (!$work_instruction) {
        throw new Exception('Work instruction tidak ditemukan.');
      }

      if ($work_instruction->status !== 'COR') {
        throw new Exception('Work instruction tidak dalam status koreksi. Resubmit hanya dapat dilakukan untuk dokumen dengan status COR.');
      }

      $old_status = $work_instruction->status;

      // Step 3: Begin database transaction
      $this->db->trans_begin();

      // Step 4: Update status from COR to REV
      // Step 5: Update modified_by and modified_at fields
      $this->update($id, array(
        'status'      => 'REV',
        'modified_by' => $this->auth->user_id(),
        'modified_at' => date('Y-m-d H:i:s')
      ));

      // Step 6: Insert status log with transition COR→REV
      $this->_insertStatusLog($id, $old_status, 'REV');

      // Step 7: Commit transaction or rollback if failed
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed to resubmit work instruction. Please try again.');
      }

      $this->db->trans_commit();

      // Step 8: Return success response
      return array(
        'status' => 1,
        'msg'    => 'Work instruction successfully resubmitted for review.'
      );
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array(
        'status' => 0,
        'msg'    => $e->getMessage()
      );
    }
  }

  /**
   * Save review decision (approve or request correction)
   * 
   * @return array Response with status and message
   */
  public function saveReview()
  {
    try {
      // Step 1: Validate POST data contains id and status fields
      $data = $this->input->post();
      
      if (empty($data['id'])) {
        throw new Exception('ID work instruction tidak valid.');
      }

      // Step 2: Validate status is APV or COR
      $allowed_statuses = array('APV', 'COR');
      if (empty($data['status']) || !in_array($data['status'], $allowed_statuses)) {
        throw new Exception('Status review tidak valid. Pilih APV atau COR.');
      }

      // Step 3: If status is COR, validate note field is not empty
      $note = isset($data['note']) ? trim($data['note']) : '';
      if ($data['status'] === 'COR' && empty($note)) {
        throw new Exception('Catatan wajib diisi jika aksi adalah Kembalikan (COR).');
      }

      // Step 4: Get work instruction from database
      $work_instruction = $this->db->get_where('work_instructions', array('id' => $data['id']))->row();
      if (!$work_instruction) {
        throw new Exception('Work instruction tidak ditemukan.');
      }

      $old_status = $work_instruction->status;

      // Step 5: Begin database transaction
      $this->db->trans_begin();

      // Step 6: Prepare update data
      $updateData = array(
        'status'      => $data['status'],
        'reviewed_by' => $this->auth->user_id(),
        'reviewed_at' => date('Y-m-d H:i:s')
      );

      // Step 7: If status is COR, update note field
      if ($data['status'] === 'COR' && !empty($note)) {
        $updateData['note'] = $note;
      }

      // Update work instruction
      $this->update($data['id'], $updateData);

      // Step 8: Call _insertStatusLog() with appropriate transition
      $this->_insertStatusLog($data['id'], $old_status, $data['status'], $note);

      // Step 9: Commit transaction or rollback if failed
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed process review document. Please try again later.');
      }

      $this->db->trans_commit();

      // Step 10: Return success response
      return array(
        'status' => 1,
        'msg'    => 'Success process review document.'
      );
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array(
        'status' => 0,
        'msg'    => $e->getMessage()
      );
    }
  }

  /**
   * Process approval action (APV -> PUB or APV -> COR)
   * Includes version control integration for publishing
   * 
   * @return array Response with status and message
   */
  public function saveApprove()
  {
    $data = $this->input->post();

    try {
      // Step 1: Validate POST data contains id field
      if (empty($data['id'])) {
        throw new Exception('ID work instruction tidak valid.');
      }

      // Step 2: Validate POST data contains status field
      if (empty($data['status'])) {
        throw new Exception('Status approval tidak valid.');
      }

      // Step 3: Validate status is PUB or COR
      $allowed_statuses = array('PUB', 'COR');
      if (!in_array($data['status'], $allowed_statuses)) {
        throw new Exception('Status approval tidak valid. Pilih PUB atau COR.');
      }

      // Step 4: Validate published_date if status is PUB
      if ($data['status'] === 'PUB') {
        if (empty($data['published_date']) || trim($data['published_date']) === '') {
          throw new Exception('Tanggal publikasi wajib diisi jika aksi adalah Setujui & Publikasikan (PUB).');
        }
      }

      // Step 5: Validate note if status is COR
      if ($data['status'] === 'COR') {
        if (empty($data['note']) || trim($data['note']) === '') {
          throw new Exception('Catatan wajib diisi jika aksi adalah Kembalikan (COR).');
        }
      }

      // Step 6: Get work instruction from database
      $work_instruction = $this->db->get_where('work_instructions', array('id' => $data['id']))->row();
      if (!$work_instruction) {
        throw new Exception('Work instruction tidak ditemukan.');
      }

      $old_status = $work_instruction->status;

      // Step 7: Begin database transaction
      $this->db->trans_begin();

      // Step 8: Prepare update data
      $dataUpdate = array();
      $dataUpdate['status'] = $data['status'];

      // Step 9: If status is PUB, handle publishing with version control
      if ($data['status'] === 'PUB') {
        $dataUpdate['published_date'] = $data['published_date'];
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

      // Step 10: If status is COR, update note field
      if ($data['status'] === 'COR') {
        $dataUpdate['note'] = trim($data['note']);
      }

      // Step 11: Update work instruction
      $this->update($data['id'], $dataUpdate);

      // Step 12: Insert status log with appropriate transition
      $note = ($data['status'] === 'COR') ? trim($data['note']) : null;
      $this->_insertStatusLog($data['id'], $old_status, $data['status'], $note);

      // Step 13: Commit transaction or rollback if failed
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed process approve document. Please try again later.');
      }

      $this->db->trans_commit();

      // Step 14: Return success response
      $success_msg = ($data['status'] === 'PUB') 
        ? 'Success process approve document. Version ' . $new_version . ' has been published.'
        : 'Success process approve document.';
        
      return array(
        'status' => 1,
        'msg'    => $success_msg
      );
    } catch (\Throwable $th) {
      $this->db->trans_rollback();
      return array(
        'status' => 0,
        'msg'    => $th->getMessage()
      );
    }
  }

  // ============================================================================
  // VERSION CONTROL METHODS
  // ============================================================================

  /**
   * Request revision for published document (PUB -> RVI)
   * Sets is_under_revision flag to maintain access to current version
   * 
   * @param int $id Work instruction ID
   * @param string|null $note Optional note explaining reason for revision
   * @return array Response with status and message
   */
  public function requestRevision($id = null, $note = null)
  {
    try {
      // Step 1: Validate ID
      if (empty($id)) {
        throw new Exception('ID work instruction tidak valid.');
      }

      // Step 2: Get work instruction from database
      $work_instruction = $this->db->get_where('work_instructions', array('id' => $id))->row();
      if (!$work_instruction) {
        throw new Exception('Work instruction tidak ditemukan.');
      }

      // Step 3: Validate current status is PUB
      if ($work_instruction->status !== 'PUB') {
        throw new Exception('Request revision hanya dapat dilakukan untuk dokumen dengan status Published (PUB).');
      }

      $old_status = $work_instruction->status;

      // Step 4: Begin database transaction
      $this->db->trans_begin();

      // Step 5: Update status to RVI and set is_under_revision flag
      $this->update($id, array(
        'status'            => 'RVI',
        'is_under_revision' => 1,
        'modified_by'       => $this->auth->user_id(),
        'modified_at'       => date('Y-m-d H:i:s')
      ));

      // Step 6: Insert status log with note
      $log_note = $note ? $note : 'Request revision';
      $this->_insertStatusLog($id, $old_status, 'RVI', $log_note);

      // Step 7: Commit transaction or rollback if failed
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed to request revision. Please try again.');
      }

      $this->db->trans_commit();

      // Step 8: Return success response
      return array(
        'status' => 1,
        'msg'    => 'Revision request submitted successfully. Document is now under revision.'
      );
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array(
        'status' => 0,
        'msg'    => $e->getMessage()
      );
    }
  }

  /**
   * Save version snapshot when document is published
   * 
   * This method creates a snapshot of the current work instruction state
   * and saves it to the work_instruction_versions table for version history.
   * 
   * @param int $work_instruction_id Work instruction ID
   * @param string|null $description Optional description of changes in this version
   * @return int|false Returns the new version_number if successful, false if failed
   * @throws Exception if snapshot creation fails
   */
  public function saveVersionSnapshot($work_instruction_id, $description = null)
  {
    try {
      // Step 1: Get work instruction data from master table
      $wi = $this->db->get_where('work_instructions', array('id' => $work_instruction_id))->row();
      
      if (!$wi) {
        throw new Exception('Work instruction tidak ditemukan.');
      }

      // Step 2: Calculate next version_number (MAX + 1)
      $this->db->select_max('version_number');
      $this->db->where('work_instruction_id', $work_instruction_id);
      $max_version = $this->db->get('work_instruction_versions')->row();
      
      $next_version = ($max_version && $max_version->version_number) ? $max_version->version_number + 1 : 1;

      // Step 3: Begin database transaction
      $this->db->trans_begin();

      // Step 4: Mark old version as superseded (is_current = 0)
      $this->db->where('work_instruction_id', $work_instruction_id);
      $this->db->where('is_current', 1);
      $this->db->update('work_instruction_versions', array(
        'is_current'    => 0,
        'superseded_at' => date('Y-m-d H:i:s'),
        'superseded_by' => $next_version
      ));

      // Step 5: Insert new version record
      $version_data = array(
        'work_instruction_id' => $work_instruction_id,
        'version_number'      => $next_version,
        
        // File snapshot
        'file_name'           => $wi->file_name,
        'file_path'           => isset($wi->file_path) ? $wi->file_path : null,
        'ext'                 => $wi->ext,
        'size'                => isset($wi->size) ? $wi->size : null,
        
        // Metadata snapshot
        'number'              => $wi->number,
        'name'                => $wi->name,
        'revision_number'     => isset($wi->revision_number) ? $wi->revision_number : 0,
        'published_date'      => isset($wi->published_date) ? $wi->published_date : date('Y-m-d'),
        'effective_date'      => isset($wi->effective_date) ? $wi->effective_date : null,
        
        // Audit trail
        'published_by'        => $this->auth->user_id(),
        'published_at'        => date('Y-m-d H:i:s'),
        
        // Status
        'is_current'          => 1,
        
        // Description
        'description'         => $description
      );

      $inserted = $this->db->insert('work_instruction_versions', $version_data);
      
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
      log_message('error', 'saveVersionSnapshot failed: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Get current version to display
   * 
   * Returns the appropriate file information based on revision status:
   * - If under revision: returns the last published version from version history
   * - If not under revision: returns current data from master table
   * 
   * @param int $work_instruction_id Work instruction ID
   * @return object|null Version object with file info, or null if not found
   */
  public function getCurrentVersion($work_instruction_id)
  {
    // Step 1: Get work instruction from master table
    $wi = $this->db->get_where('work_instructions', array('id' => $work_instruction_id))->row();
    
    if (!$wi) {
      return null;
    }

    // Step 2: Check if under revision
    if (isset($wi->is_under_revision) && $wi->is_under_revision == 1) {
      // Step 3a: Under revision - get version with is_current = 1
      $this->db->select('wiv.*, u.full_name as publisher_name');
      $this->db->from('work_instruction_versions wiv');
      $this->db->join('users u', 'wiv.published_by = u.id_user', 'left');
      $this->db->where('wiv.work_instruction_id', $work_instruction_id);
      $this->db->where('wiv.is_current', 1);
      
      $version = $this->db->get()->row();
      
      if ($version) {
        $version->is_from_history = true;
        $version->under_revision = true;
        return $version;
      }
    }

    // Step 3b: Not under revision - return data from master table
    $wi->is_from_history = false;
    $wi->under_revision = false;
    return $wi;
  }

  /**
   * Get version history for a work instruction
   * 
   * Returns all versions ordered by version_number descending (newest first)
   * 
   * @param int $work_instruction_id Work instruction ID
   * @return array Array of version objects with publisher info
   */
  public function getVersionHistory($work_instruction_id)
  {
    // Query work_instruction_versions with JOIN to users
    $this->db->select('wiv.*, u.full_name as publisher_name, u.username as publisher_username');
    $this->db->from('work_instruction_versions wiv');
    $this->db->join('users u', 'wiv.published_by = u.id_user', 'left');
    $this->db->where('wiv.work_instruction_id', $work_instruction_id);
    $this->db->order_by('wiv.version_number', 'DESC');
    
    return $this->db->get()->result();
  }

  /**
   * Get specific version by version number
   * 
   * @param int $work_instruction_id Work instruction ID
   * @param int $version_number Version number to retrieve
   * @return object|null Version object or null if not found
   */
  public function getVersionByNumber($work_instruction_id, $version_number)
  {
    $this->db->select('wiv.*, u.full_name as publisher_name, u.username as publisher_username');
    $this->db->from('work_instruction_versions wiv');
    $this->db->join('users u', 'wiv.published_by = u.id_user', 'left');
    $this->db->where('wiv.work_instruction_id', $work_instruction_id);
    $this->db->where('wiv.version_number', $version_number);
    
    return $this->db->get()->row();
  }

  /**
   * Request deletion for published document (PUB -> HLD)
   * Sets deletion_status to track deletion workflow
   * 
   * @param int $id Work instruction ID
   * @param string|null $note Optional note explaining reason for deletion
   * @return array Response with status and message
   */
  public function requestDeletion($id, $note = null)
  {
    try {
      // Step 1: Validate ID
      if (empty($id)) {
        throw new Exception('ID work instruction tidak valid.');
      }

      // Step 2: Get work instruction from database
      $work_instruction = $this->db->get_where('work_instructions', array('id' => $id))->row();
      if (!$work_instruction) {
        throw new Exception('Work instruction tidak ditemukan.');
      }

      // Step 3: Validate current status is PUB
      if ($work_instruction->status !== 'PUB') {
        throw new Exception('Request deletion hanya dapat dilakukan untuk dokumen dengan status Published (PUB).');
      }

      $old_status = $work_instruction->status;

      // Step 4: Begin database transaction
      $this->db->trans_begin();

      // Step 5: Update status to HLD and set deletion_status to REV (Review Deletion)
      $this->update($id, array(
        'status'          => 'HLD',
        'deletion_status' => 'REV',
        'modified_by'     => $this->auth->user_id(),
        'modified_at'     => date('Y-m-d H:i:s')
      ));

      // Step 6: Insert status log with note
      $log_note = $note ? $note : 'Request deletion';
      $this->_insertStatusLog($id, $old_status, 'HLD', $log_note);

      // Step 7: Commit transaction or rollback if failed
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        throw new Exception('Failed to request deletion. Please try again.');
      }

      $this->db->trans_commit();

      // Step 8: Return success response
      return array(
        'status' => 1,
        'msg'    => 'Deletion request submitted successfully. Document is now on hold pending approval.'
      );
    } catch (Exception $e) {
      $this->db->trans_rollback();
      return array(
        'status' => 0,
        'msg'    => $e->getMessage()
      );
    }
  }
}
