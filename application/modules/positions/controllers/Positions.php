<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Syamsudin
 * @copyright Copyright (c) 2021, Syamsudin
 *
 * This is controller for Perusahaan
 */

class Positions extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->template->set([
            'title' => 'Positions',
            'icon' => 'fa fa-user-tie'
        ]);

        date_default_timezone_set("Asia/Bangkok");
    }

    public function index()
    {
        $data = $this->db->get_where('view_positions', ['company_id' => $this->company])->result();
        $this->template->set('data', $data);
        $this->template->render('index');
    }

    //Create New Customer
    public function add()
    {
        $this->template->render('add');
    }

    public function save()
    {
        $data       = $this->input->post();
        
        $this->db->trans_begin();
        if ($data) {
            if (isset($data['id']) && $data['id']) {
                $data['modified_at'] = date('Y-m-d H:i:s');
                $data['modified_by'] = $this->auth->user_id();
                $this->db->update('positions', $data, ['id' => $data['id']]);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = $this->auth->user_id();
                $data['company_id'] = $this->company;
                $this->db->insert('positions', $data);
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
        } else {
            $this->db->trans_commit();
            $return        = array(
                'status'        => 0,
                'msg'            => 'Data not valid. Please Try Again!'
            );
        }
        echo json_encode($return);
    }

    //Edit Perusahaan
    public function edit($id = null)
    {
        $data = $this->db->get_where('positions', ['id' => $id])->row();
        $this->template->set('data', $data);
        $this->template->render('edit');
    }

    public function view($id = null)
    {
        $data = $this->db->get_where('positions', ['id' => $id])->row();
        $this->template->set('data', $data);
        $this->template->render('view');
    }

    public function assign($id = null)
    {
        $pos    = $this->db->get_where('positions', ['id' => $id])->row();
        $users  = $this->db->get_where('view_user_groups', ['company_id' => $this->company, 'active' => 'Y'])->result();
        
        $assigned_users = $this->db->get_where('user_positions', ['position_id' => $id])->result_array();
        $assigned_user_ids = array_column($assigned_users, 'user_id');

        $this->template->set([
            'pos' => $pos,
            'users' => $users,
            'assigned_user_ids' => $assigned_user_ids
        ]);
        $this->template->render('assign');
    }

    function save_assign()
    {
        $id = $this->input->post('position');
        $user_id = $this->input->post('user_id');
        $this->db->trans_begin();
        if ($id != '' && $user_id != '') {
            $check = $this->db->get_where('user_positions', ['position_id' => $id, 'user_id' => $user_id])->num_rows();
            if ($check == 0) {
                $this->db->insert('user_positions', ['position_id' => $id, 'user_id' => $user_id]);
                $this->db->update('positions', ['assign_user' => $user_id], ['id' => $id]);
            }
        } else {
            $this->db->trans_rollback();
            $Return = [
                'msg'       => "Data not valid",
                'status'    => 0
            ];
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $Return = [
                'msg'       => "Successfull Assign user to this position.",
                'status'    => 0
            ];
        } else {
            $this->db->trans_commit();
            $Return = [
                'msg'       => "Failed Assign  user to this position, please try again.",
                'status'    => 1
            ];
        }

        echo json_encode($Return);
    }

    function remove_assign()
    {
        $id         = $this->input->post('position');
        $user_id    = $this->input->post('user_id');

        $this->db->trans_begin();
        if ($id != '' && $user_id != '') {
            $this->db->delete('user_positions', ['position_id' => $id, 'user_id' => $user_id]);
            $pos = $this->db->get_where('positions', ['id' => $id])->row();
            if ($pos && $pos->assign_user == $user_id) {
                $this->db->update('positions', ['assign_user' => null], ['id' => $id]);
            }
        } else {
            $this->db->trans_rollback();
            $Return = [
                'msg'       => "Data not valid",
                'status'    => 0
            ];
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $Return = [
                'msg'       => "Successfull Remove Assign user to this position.",
                'status'    => 0
            ];
        } else {
            $this->db->trans_commit();
            $Return = [
                'msg'       => "Failed Remove Assign user to this position, please try again.",
                'status'    => 1
            ];
        }

        echo json_encode($Return);
    }

    function delete()
    {
        $id = $this->input->post('id');
        $this->db->trans_begin();
        if ($id != '') {
            $this->db->delete('positions', ['id' => $id]);
        } else {
            $this->db->trans_rollback();
            $Return = [
                'msg'       => "Data not valid",
                'status'    => 0
            ];
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $Return = [
                'msg'       => "Successfull save data position.",
                'status'    => 0
            ];
        } else {
            $this->db->trans_commit();
            $Return = [
                'msg'       => "Failed save data position, please try again.",
                'status'    => 1
            ];
        }

        echo json_encode($Return);
    }
}
