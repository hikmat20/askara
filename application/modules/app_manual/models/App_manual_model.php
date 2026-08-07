<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class App_manual_model extends BF_Model
{
    protected $table_name = 'app_manuals';
    protected $key        = 'id';
    protected $created_field = 'created_on';
    protected $modified_field = 'modified_on';
    protected $set_created = true;
    protected $set_modified = true;
    protected $soft_deletes = false;
    protected $date_format = 'datetime';
    protected $log_user = true;

    public function __construct()
    {
        parent::__construct();
    }
}
