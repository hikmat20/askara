<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* 
 * @author Yunas Handra
 * @copyright Copyright (c) 2016, Yunas Handra
 * 
 * This is model class for table "log_5masterbarang"
 */

class Manage_documents_model extends BF_Model
{

    /**
     * @var string  User Table Name
     */
    protected $table_name = 'directory';
    protected $key        = 'id';

    /**
     * @var string Field name to use for the created time column in the DB table
     * if $set_created is enabled.
     */
    protected $created_field = 'created_on';

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
    protected $soft_deletes = true;

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

    public function delete_recursive_folder($parent_id, $user_id, $date)
    {
        $children = $this->db->get_where('directory', ['parent_id' => $parent_id])->result();
        foreach ($children as $child) {
            $this->db->update('directory', ['status' => 'DEL', 'modified_by' => $user_id, 'modified_at' => $date], ['id' => $child->id]);
            // Rekursif untuk mencari anak dari child ini
            $this->delete_recursive_folder($child->id, $user_id, $date);
        }
    }

}
