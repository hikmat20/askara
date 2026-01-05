<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Syamsudin
 * @copyright Copyright (c) 2021, Syamsudin
 *
 * This is model class for table "Perusahaan"
 */

class Company_model extends BF_Model
{

    /**
     * @var string  User Table Name
     */
    protected $table_name = 'companies';
    protected $key        = 'id_perusahaan';

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

    public function getDataList() {}

    public function saveData()
    {
        $post = $this->input->post();
        try {
            $data   = $post;
            $branch = isset($data['branch']) ? $data['branch'] : '';
            unset($data['branch']);

            if (isset($_FILES['logo']['name'])) {
                $logoData = $this->uploadLogo($data['id_perusahaan']);
                $data['logo'] = $logoData['logo'];
                $data['path_logo'] = $logoData['path_logo'];
            }

            $this->db->trans_begin();
            if ($data) {
                if (isset($data['id_perusahaan']) && $data['id_perusahaan']) {
                    $data['modified_at'] = date('Y-m-d H:i:s');
                    $data['modified_by'] = $this->auth->user_id();
                    $this->db->update('companies', $data, ['id_perusahaan' => $data['id_perusahaan']]);
                } else {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $data['created_by'] = $this->auth->user_id();
                    $this->db->insert('companies', $data);
                }

                $this->saveDataBranch($data, $branch);
            } else {
                $this->db->trans_rollback();
                $return        = array(
                    'status'        => 0,
                    'msg'            => 'Data not valid. Please Try Again!'
                );
                echo json_encode($return);
                return false;
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $return        = array(
                    'status'        => 0,
                    'msg'            => 'Data company Failed save. Please Try Again!'
                );
            } else {
                $this->db->trans_commit();
                $return        = array(
                    'status'        => 1,
                    'msg'            => 'Data company successfull saved. Thanks you.'
                );
            }
        } catch (\Throwable $th) {
            $this->db->trans_rollback();
            $return = array(
                'status'        => 0,
                'msg'            => $th->getMessage(),
            );
        }

        return $return;
    }

    public function saveDataBranch($data, $branch)
    {
        $dataBranch = [];
        /* Branch */
        if ($branch) foreach ($branch as $k => $val) {
            $dataBranch = [
                'branch_name' => $val['branch_name'],
                'branch_address' => $val['address'],
                'branch_city' => $val['city'],
            ];

            if (isset($val['id'])) {
                $dataBranch['company_id'] = $data['id_perusahaan'];
                $dataBranch['modified_at'] = date('Y-m-d H:i:s');
                $dataBranch['modified_by'] = $this->auth->user_id();
                $this->db->update('company_branch', $dataBranch, ['id' => $val['id']]);
            } else {
                $dataBranch['company_id'] = ($data['id_perusahaan']) ?: $this->db->insert_id('companies');
                $dataBranch['created_at'] = date('Y-m-d H:i:s');
                $dataBranch['created_by'] = $this->auth->user_id();
                $this->db->insert('company_branch', $dataBranch);
            }
        }
    }

    public function uploadLogo($company)
    {
        $this->load->library('upload');
        $config = array();
        $config['upload_path']      = './assets/logo/' . $company . '/';
        $config['allowed_types'] = '|jpg|jpeg|png';
        $config['max_size']      = 5120; // 5mb;
        $config['overwrite']     = TRUE;
        // $config['encrypt_name']  = TRUE;


        if (!is_dir('./assets/logo/' . $company . '/')) {
            chown('./assets/', 'www-data');
            mkdir('./assets/logo/' . $company . '/', 0755, TRUE);
            chmod('./assets/logo/' . $company . '/', 0755);  // octal; correct value of mode
        }

        if ($_FILES['logo']['name']) {
            $this->upload->initialize($config);
            $this->upload->do_upload('logo');
            $dataLogo = $this->upload->data();
            if ($this->upload->display_errors()) {
                $return        = array(
                    'status' => 0,
                    'msg'    =>  $this->upload->display_errors()
                );
                echo json_encode($return);
                return false;
            }
            return [
                'path_logo' => '/assets/logo/',
                'logo' => $dataLogo['file_name'],
            ];
        }
    }
}
