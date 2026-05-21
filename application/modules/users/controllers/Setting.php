<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author CokesHome
 * @copyright Copyright (c) 2015, CokesHome
 *
 * This is controller for Users Management
 */

class Setting extends Admin_Controller
{

    /**
     * Load the models, library, etc
     *
     *
     */

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('users');
        $this->load->model(array(
            'users/users_model',
            'users/groups_model',
            'users/user_groups_model'
        ));

        $this->template->page_icon('fa fa-users');
    }
    /* 
    public function index()
    {
        if (isset($_POST['delete'])) {
            $checked = $this->input->post('checked');

            if (is_array($checked) && count($checked)) {
                $result = FALSE;
                $sukses = 0;
                foreach ($checked as $pid) {
                    $result      = $this->users_model->delete($pid);

                    if ($result) {
                        $keterangan = "SUKSES, hapus data user dengan ID : " . $pid;
                        $status     = 1;

                        $sukses++;
                    } else {
                        $keterangan = "GAGAL, hapus data user dengan ID : " . $pid;
                        $status     = 0;
                    }

                    $nm_hak_akses   = $this->deletePermission;
                    $kode_universal = $pid;
                    $jumlah         = 1;
                    $sql            = $this->db->last_query();

                    simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);
                }

                if ($result) {
                    $this->template->set_message(count($sukses) . ' ' . lang('users_del_success') . '.', 'success');
                } else {
                    $this->template->set_message(lang('users_del_fail') . $this->users_model->error, 'error');
                }
            } else {
                $this->template->set_message(lang('users_del_error'), 'error');
            }

            unset($_POST['delete']);
        }

        // Pagination
        $this->load->library('pagination');

        if (isset($_POST['table_search'])) {
            $search = isset($_POST['table_search']) ? $this->input->post('table_search') : '';
        } else {
            $search = isset($_GET['search']) ? $this->input->get('search') : '';
        }

        $filter = "";
        if ($search != "") {
            $filter = "?search=" . $search;
        }

        $search2 = $this->db->escape_str($search);

        $where = "users.deleted = 0
                    AND (`username` LIKE '%$search2%' ESCAPE '!'
                    OR `nm_lengkap` LIKE '%$search2%' ESCAPE '!'
                    OR `users`.`alamat` LIKE '%$search2%' ESCAPE '!'
                    OR `users`.`kota` LIKE '%$search2%' ESCAPE '!'
                    OR `users`.`hp` LIKE '%$search2%' ESCAPE '!'
                   )";

        $total = $this->users_model
            ->where($where)
            ->count_all();

        $offset = $this->input->get('per_page');

        $limit = $this->config->item('list_limit');

        $this->pager['base_url']            = current_url() . $filter;
        $this->pager['total_rows']          = $total;
        $this->pager['per_page']            = $limit;
        $this->pager['page_query_string']   = TRUE;

        $this->pagination->initialize($this->pager);

        $data = $this->users_model->select("users.*")
            ->where($where)
            ->order_by('nm_lengkap', 'ASC')
            ->limit($limit, $offset)->find_all();

        $this->template->set('results', $data);
        $this->template->set('search', $search);

        $this->template->title(lang('users_manage_title'));
        $this->template->set("numb", $offset + 1);
        $this->template->render('list');
    }
    */

    public function index()
    {
        $userActive = $this->get_company_users('ACT');
        $userNonActive = $this->get_company_users('NAC');
        $this->template->set([
            'userActive' => $userActive,
            'userNonActive' => $userNonActive,
        ]);
        $this->template->render('list');
    }

    /**
     * Ambil daftar user per company. Tetap tampil meski group sudah nonaktif
     * karena relasi user_groups tidak dihapus saat group di-delete.
     */
    protected function get_company_users($status)
    {
        return $this->db
            ->select('users.id_user, users.username, users.full_name, users.email, users.phone, users.status, users.photo, user_groups.company_id, user_groups.id_group, groups.nm_group')
            ->from('users')
            ->join('user_groups', 'users.id_user = user_groups.user_id', 'inner')
            ->join('groups', 'user_groups.id_group = groups.id_group', 'left')
            ->where('user_groups.company_id', $this->company)
            ->where('users.status', $status)
            ->order_by('users.full_name', 'ASC')
            ->get()
            ->result();
    }

    public function delete()
    {
        $id = $this->input->post('id');

        if (!$id || $id == 1) {
            echo json_encode([
                'status' => 0,
                'msg'    => 'User tidak valid atau user admin tidak dapat dihapus.',
            ]);
            return;
        }

        $user = $this->db->get_where('users', ['id_user' => $id])->row();

        if (!$user) {
            echo json_encode([
                'status' => 0,
                'msg'    => 'User tidak ditemukan.',
            ]);
            return;
        }

        if ($user->status == 'DEL') {
            echo json_encode([
                'status' => 0,
                'msg'    => 'User sudah dihapus sebelumnya.',
            ]);
            return;
        }

        $this->db->trans_begin();
        $this->db->update('users', ['status' => 'DEL'], ['id_user' => $id]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode([
                'status' => 0,
                'msg'    => 'Gagal menghapus user. Silakan coba lagi.',
            ]);
            return;
        }

        $this->db->trans_commit();
        echo json_encode([
            'status' => 1,
            'msg'    => 'User berhasil dihapus.',
        ]);
    }
    public function create()
    {
        $levels = [];
        if ($this->auth->user_id() == '1') {
            $levels = $this->db->get_where('groups', ['active' => 'Y', 'company_id' => null])->result();
        }

        $levelsComp = $this->db->get_where('groups', ['active' => 'Y', 'company_id' => $this->company])->result();
        // $cabang = $this->Cabang_model->find_all();

        $this->template->set('levels', array_merge($levels, $levelsComp));
        if ($this->company == '1') {
            $companies = $this->db->get_where('companies')->result();
        } else {
            $companies = $this->db->get_where('companies', ['id_perusahaan' => $this->company])->result();
        }

        $this->template->set('companies', $companies);
        $this->template->title(lang('users_new_title'));
        $this->template->render('users_form');
    }

    public function save()
    {
        $this->save_user();
    }

    public function edit($id = 0)
    {
        $data           = $this->db->get_where('view_users', ['id_user' => $id])->row();
        $user_group     = $this->db->get_where('user_groups', ['user_id' => $id])->row();
        $companies      = $this->db->get_where('companies')->result();
        $levels = [];
        if ($this->auth->user_id() == '1') {
            $levels = $this->db->get_where('groups', ['active' => 'Y', 'company_id' => null])->result();
        } else {
            $levels = $this->db->get_where('groups', ['active' => 'Y', 'company_id' => null, 'id_group !=' => '1'])->result();
        }
        $levelsComp = $this->db->get_where('groups', ['active' => 'Y', 'company_id' => $this->company])->result();
        $this->template->set('levels', array_merge($levels, $levelsComp));
        $this->template->set('companies', $companies);
        $this->template->set('data', $data);
        $this->template->set('user_group', $user_group);
        $this->template->title(lang('users_edit_title'));
        $this->template->page_icon('fa fa-user');
        $this->template->render('users_form');
    }

    public function permission($id = 0)
    {
        $this->auth->restrict($this->managePermission);
        if ($id == 0 || is_numeric($id) == FALSE || $id == 1) {
            $this->template->set_message(lang('users_invalid_id'), 'error');
            redirect('users/setting');
        }

        if (isset($_POST['save'])) {
            if ($this->save_permission($id)) {
                $this->template->set_message(lang('users_permission_edit_success'), 'success');
            }
        }

        //User data
        $data = $this->users_model->find($id);

        if ($data) {
            if ($data->deleted == 1 || $data->status == 'DEL') {
                $this->template->set_message(lang('users_already_deleted'), 'error');
                redirect('users/setting');
            }
        }

        //All Permission
        $permissions = $this->permissions_model
            ->order_by("nm_permission", "ASC")
            ->find_all();

        $auth_permissions = $this->get_auth_permission($id);

        $rows   = array();
        $header = array();
        $tmp    = array();
        if ($permissions) {
            //table Header
            foreach ($permissions as $key => $pr) {
                $x = explode(".", $pr->nm_permission);
                if (!in_array($x[1], $header)) {
                    $header[] = $x[1];
                    $tmp[$x[1]] = 0;
                }
            }
            //Temporary value
            foreach ($permissions as $key2 => $pr) {
                $x = explode(".", $pr->nm_permission);
                $rows[$x[0]] = $tmp;
            }
            //Actual value
            foreach ($permissions as $key3 => $pr) {
                $x = explode(".", $pr->nm_permission);
                //Rows
                $rows[$x[0]][$x[1]] = array('nm' => $pr->nm_menu, 'perm_id' => $pr->id_permission, 'action_name' => $x[1], 'is_role_permission' => (isset($auth_permissions[$pr->id_permission]->is_role_permission) && $auth_permissions[$pr->id_permission]->is_role_permission == 1) ? 1 : '', 'value' => (isset($auth_permissions[$pr->id_permission]) ? 1 : 0));
            }
        }

        $this->template->set('data', $data);
        $this->template->set('header', $header);
        $this->template->set('permissions', $rows);

        $this->template->title(lang('users_edit_perm_title'));
        $this->template->page_icon('fa fa-shield');
        $this->template->render('user_permissions');
    }

    protected function save_permission($id_user = 0)
    {
        if ($id_user == 0 || $id_user == "") {
            $this->template->set_message(lang('users_invalid_id'), 'error');
            return FALSE;
        }

        $id_permissions = $this->input->post('id_permissions');

        $insert_data = array();
        if ($id_permissions) {
            foreach ($id_permissions as $key => $idp) {
                $insert_data[] = array(
                    'id_user' => $id_user,
                    'id_permission' => $idp
                );
            }
        }

        //Delete Fisrt All Previous user permission
        $result = $this->user_permissions_model->delete_where(array('id_user' => $id_user));

        //Insert New one
        if ($insert_data) {
            $result = $this->user_permissions_model->insert_batch($insert_data);
        }

        if ($result === FALSE) {
            $this->template->set_message(lang('users_permission_edit_fail'), 'error');
            return FALSE;
        }

        unset($_POST['save']);

        return $result;
    }

    protected function get_auth_permission($id = 0)
    {
        $role_permissions = $this->users_model->select("permissions.*")
            ->join("user_groups", "users.id_user = user_groups.id_user")
            ->join("group_permissions", "user_groups.id_group = group_permissions.id_group")
            ->join("permissions", "group_permissions.id_permission = permissions.id_permission")
            ->where("users.id_user", $id)
            ->find_all();

        $user_permissions = $this->users_model->select("permissions.*")
            ->join("user_permissions", "users.id_user = user_permissions.id_user")
            ->join("permissions", "user_permissions.id_permission = permissions.id_permission")
            ->where("users.id_user", $id)
            ->find_all();

        $merge = array();
        if ($role_permissions) {
            foreach ($role_permissions as $key => $rp) {
                if (!isset($merge[$rp->id_permission])) {
                    $rp->is_role_permission = 1;
                    $merge[$rp->id_permission] = $rp;
                }
            }
        }

        if ($user_permissions) {
            foreach ($user_permissions as $key => $up) {
                if (!isset($merge[$up->id_permission])) {
                    $up->is_role_permission = 0;
                    $merge[$up->id_permission] = $up;
                }
            }
        }

        return $merge;
    }

    protected function save_user()
    {
        $data           = $this->input->post();
        $data['ip']     = $this->input->ip_address();
        $company_id     = $data['company_id'];
        unset($data['company_id']);
    
        /**
         * This code will benchmark your server to determine how high of a cost you can
         * afford. You want to set the highest cost that you can without slowing down
         * you server too much. 8-10 is a good baseline, and more is good if your servers
         * are fast enough. The code below aims for ≤ 50 milliseconds stretching time,
         * which is a good baseline for systems handling interactive logins.
         */
        $timeTarget = 0.05; // 50 milliseconds

        $cost = 8;
        do {
            $cost++;
            $start = microtime(true);
            password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);


        // $options = [
        //     'cost' => $cost,
        //     'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)
        // ];
       
        if ($data['password']) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }

        // $data['photo'] = null;
        if ($_FILES['profile_avatar']['name']) {
            $config['upload_path']          = './assets/img/avatar/';
            $config['allowed_types']        = 'gif|jpg|png';
            $config['max_size']             = 2048;
            $config['max_width']            = 1024;
            $config['max_height']           = 1024;
            $config['file_name']            = $data['username'];
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('profile_avatar')) {
                $error = $this->upload->display_errors();
                $this->template->set_message($error, 'error');
                return FALSE;
            } else {
                if ($data['old_photo']) {
                    if (file_exists('assets/img/avatar/' . $data['old_photo'])) {
                        unlink('assets/img/avatar/' . $data['old_photo']);
                    }
                }
                $dataPhoto = $this->upload->data();
                $data['photo'] = $dataPhoto['file_name'];
            }
        }

        $group_id = $data['group_id'];
        unset($data['group_id']);
        unset($data['re-password']);
        unset($data['old_photo']);

        $this->db->trans_begin();
        if (isset($data['id_user']) && $data['id_user']) {
            $data['modified_at'] = date('Y-m-d H:i:s');
            $data['modified_by'] = $this->auth->user_id();
            $data['status'] = (isset($data['status'])) ? $data['status'] : 'NAC';
            $this->db->update('users', $data, ['id_user' => $data['id_user']]);
        } else {
            $check_check_username   =  $this->check_username($data['username']);
            $check_check_email      =  $this->check_email($data['email']);
            if ($check_check_username > 0) {
                $return = [
                    'msg' => 'the Username is already registered, please use a different username',
                    'status' => 0
                ];
                echo json_encode($return);
                return false;
            }

            if ($check_check_email > 0) {
                $return = [
                    'msg' => 'the Email is already registered, please use a different Email',
                    'status' => 0
                ];
                echo json_encode($return);
                return false;
            }
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->auth->user_id();
            $data['status']     = ($data['status']) ?: 'NAC';
            $this->db->insert('users', $data);
        }

        $error = $this->db->error()['message'];

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $return = [
                'msg' => 'User data saved successfully',
                'status' => 1
            ];
            $this->assign_company($data['username'], $group_id, $company_id);
        } else {
            $this->db->trans_rollback();
            $return = [
                'msg' => $error . ' User data failed to save, Please try again...',
                'status' => 0
            ];
        }
        echo json_encode($return);
    }

    public function default_select($val)
    {
        return $val == "" ? FALSE : TRUE;
    }

    public function check_username($username)
    {
        $check = $this->db
            ->where('username', $username)
            ->where('status !=', 'DEL')
            ->get('users')
            ->num_rows();
        return $check;
    }

    public function check_email($email)
    {
        $check = $this->db
            ->where('email', $email)
            ->where('status !=', 'DEL')
            ->get('users')
            ->num_rows();
        return $check;
    }

    public function assign_company($username, $group_id, $company_id)
    {
        $user =  $this->db->get_where('users', ['username' => $username])->row();
        $check = $this->db->get_where('user_groups', ['user_id' => $user->id_user, 'company_id' => $company_id])->num_rows();
        if ($check > 0) {
            $this->db->update(
                'user_groups',
                [
                    'company_id' => $company_id,
                    'id_group' => $group_id,
                ],
                [
                    'user_id' => $user->id_user,
                    'company_id' => $company_id,
                ]
            );
        } else {
            $this->db->insert('user_groups', [
                'user_id' => $user->id_user,
                'company_id' => $company_id,
                'id_group' => $group_id,
                'active' => 'Y',
                'default' => 'Y',
            ]);
        }
    }
}
