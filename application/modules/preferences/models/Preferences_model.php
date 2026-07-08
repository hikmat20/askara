<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Preferences_model extends BF_Model
{
    protected $table_name = 'settings';
    protected $key        = 'setting_name';
    protected $set_created = false;
    protected $set_modified = false;
    protected $soft_deletes = false;
    protected $log_user = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_settings($company_id = null)
    {
        $data = $this->db->get($this->table_name)->result();
        $settings = [];
        foreach ($data as $row) {
            $settings[$row->setting_name] = $row->value;
        }
        return $settings;
    }

    public function save_settings($company_id, $settings_data)
    {
        $this->db->trans_begin();
        
        foreach ($settings_data as $key => $value) {
            $existing = $this->db->get_where($this->table_name, ['setting_name' => $key])->row();
            
            if ($existing) {
                $this->db->update($this->table_name, [
                    'value' => $value
                ], ['setting_name' => $key]);
            } else {
                $this->db->insert($this->table_name, [
                    'setting_name' => $key,
                    'value' => $value
                ]);
            }
        }
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
}
