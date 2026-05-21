<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Syamsudin
 * @copyright Copyright (c) 2021, Syamsudin
 *
 * This is controller for Perusahaan
 */

class Groups extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->template->set('title', 'Groups');
        $this->template->page_icon('fa fa-table');
        $this->load->library('Menu_generator');
        date_default_timezone_set("Asia/Bangkok");
    }

    public function index()
    {
        $list1 = [];
        if ($this->auth->user_id() == '1') {
            $list1 = $this->db->get_where('groups', ['active' => 'Y', 'company_id' => null])->result();
        } else {
            $list1 = $this->db->get_where('groups', ['active' => 'Y', 'company_id' => null, 'id_group !=' => '1'])->result();
        }

        $data = $this->db->get_where('groups', ['active' => 'Y', 'company_id' => $this->company])->result();
        $this->template->set('results', array_merge($list1, $data));
        $this->template->render('index');
    }

    //Create New Customer
    public function create()
    {
        $this->template->render('add');
    }

    public function save()
    {
        $data = $this->input->post();
        $this->db->trans_begin();

        if (isset($data)) {
            $data['nm_group'] = $data['name'];
            unset($data['name']);
            if (isset($data['id_group'])) {
                $this->db->update('groups', $data, ['id_group' => $data['id_group']]);
            } else {
                $data['company_id'] = (isset($this->company) && $this->company) ? $this->company : null;
                $this->db->insert('groups', $data);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $Arr_Return        = array(
                    'status'        => 0,
                    'msg'         => 'Save Process Failed. Please Try Again...'
                );
            } else {
                $this->db->trans_commit();
                $Arr_Return        = array(
                    'status'       => 1,
                    'msg'        => 'Save Process Success... '
                );
            }
        } else {
            $Arr_Return         = array(
                'status'        => 0,
                'msg'           => 'Data not valid...'
            );
        }

        echo json_encode($Arr_Return);
    }

    public function save_permission()
    {
        $data = $this->input->post();

        $this->db->trans_begin();
        if (isset($data['menus'])) {
            $company_id = ($data['company_id']) ?: $this->company;
            $group_id = $data['group_id'];

            foreach ($data['menus'] as $menus) {
                $menu_id =  $menus['id'];
                unset($menus['id']);
                $check = $this->db->get_where('group_menus', ['menu_id' =>  $menu_id, 'company_id' => $company_id, 'group_id' => $group_id])->num_rows();
                $menus['menu_id']       = $menu_id;
                $menus['read']          = isset($menus['read']) ? '1' : '0';
                $menus['create']        = isset($menus['create']) ? '1' : '0';
                $menus['update']        = isset($menus['update']) ? '1' : '0';
                $menus['delete']        = isset($menus['delete']) ? '1' : '0';

                if ($check > 0) {
                    $this->db->update('group_menus', $menus, ['menu_id' =>  $menu_id, 'company_id' => $company_id, 'group_id' => $group_id]);
                } else {
                    $menus['company_id']    = $company_id;
                    $menus['group_id']      = $group_id;
                    $this->db->insert('group_menus', $menus);
                }
            }

            foreach ($data['submenus'] as $submenus) {
                $submenus_id = $submenus['id'];
                unset($submenus['id']);
                $check = $this->db->get_where('group_menus', ['menu_id' => $submenus_id, 'company_id' => $company_id, 'group_id' => $group_id])->num_rows();

                $submenus['menu_id'] = $submenus_id;
                $submenus['read'] = isset($submenus['read']) ? '1' : '0';
                $submenus['create'] = isset($submenus['create']) ? '1' : '0';
                $submenus['update'] = isset($submenus['update']) ? '1' : '0';
                $submenus['delete'] = isset($submenus['delete']) ? '1' : '0';
                if ($check > 0) {
                    $this->db->update('group_menus', $submenus, ['menu_id' => $submenus_id, 'company_id' => $company_id, 'group_id' => $group_id]);
                } else {
                    $submenus['company_id']    = $company_id;
                    $submenus['group_id']      = $group_id;
                    $this->db->insert('group_menus', $submenus);
                }
            }

            if ($this->db->trans_status() === FALSE) {
                // if ($this->db->affected_rows() <= 0) {
                $this->db->trans_rollback();
                $Arr_Return        = array(
                    'status'        => 0,
                    'msg'         => 'Save Process Failed. Please Try Again...'
                );
            } else {
                $this->db->trans_commit();

                $Arr_Return        = array(
                    'status'       => 1,
                    'msg'        => 'Save Process Success... '
                );
            }
        } else {
            $Arr_Return         = array(
                'status'        => 0,
                'msg'           => 'Data not valid...'
            );
        }

        echo json_encode($Arr_Return);
    }

    public function update($data)
    {
        if ($data) {
            $this->db->trans_begin();
            $id = $data['id'];
            $data['id_group']  = $data['id'];
            $data['nm_group']  = $data['name'];
            $data['company_id']  = $this->company;
            unset($data['name']);
            unset($data['id']);
            $this->db->update("groups", $data, ['id_group' => $id]);
        }
        if ($this->db->trans_status() === FALSE) {
            $error = $this->db->error()['message'];
            $this->db->trans_rollback();
            $Arr_Return        = array(
                'status'        => 0,
                'msg'          => $error
            );
        } else {
            $this->db->trans_commit();
            $Arr_Return        = array(
                'status'       => 1,
                'msg'        => 'Save Process Success... '
            );
        }
        echo json_encode($Arr_Return);
    }

    //Edit Cabang
    public function edit($id)
    {
        if ($id) {
            $data = $this->db->get_where('groups', ['id_group' => $id])->row();
            $this->template->set('data', $data);
            $this->template->render('edit');
        } else {
            redirect('groups');
        }
    }


    public function permissions($id)
    {

        $group   = $this->db->get_where('groups', ['id_group' => $id])->row();
        $this->template->set('group', $group);
        $this->template->render('permission');
    }

    public function view($id)
    {
        $group   = $this->db->get_where('groups', ['id_group' => $id])->row();
        // $menus = $this->db->order_by('menu_id', 'ASC')->get_where('view_group_menus', ['parent_id' => '0'])->result();
        // $submenus = $this->db->order_by('parent_id', 'ASC')->get_where('view_group_menus', ['parent_id !=' => '0'])->result();
        // $ArrSubmenu = [];
        // foreach ($submenus as $key => $sub) {
        //     $ArrSubmenu[$sub->parent_id][$key] = $sub;
        // }

        $this->template->set('group', $group);
        // $this->template->set('menus', $menus);
        // $this->template->set('submenus', $ArrSubmenu);
        $this->template->render('view');
    }

    function delete()
    {
        $id = $this->input->post('id');
        if ($id) {
            $this->db->trans_begin();
            $this->db->update("groups", ['active' => 'N'], ['id_group' => $id]);
        }
        if ($this->db->trans_status() === FALSE) {
            $error = $this->db->error()['message'];
            $this->db->trans_rollback();
            $Arr_Return     = array(
                'status'    => 0,
                'msg'       => $error
            );
        } else {
            $this->db->trans_commit();
            $Arr_Return        = array(
                'status'       => 1,
                'msg'        => 'Deleting Successfull... '
            );
        }
        echo json_encode($Arr_Return);
    }
}
