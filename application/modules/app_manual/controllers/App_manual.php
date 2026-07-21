<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class App_manual extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template->set([
            'title' => 'App Manual Book',
            'icon' => 'fa fa-book'
        ]);
        $this->load->model('App_manual_model');
    }

    public function index()
    {
        $data = $this->db->order_by('created_on', 'DESC')->get('app_manuals')->result();
        $this->template->set('data', $data);
        $this->template->render('index');
    }

    public function add()
    {
        $this->template->render('form');
    }

    public function edit($id)
    {
        $manual = $this->db->get_where('app_manuals', ['id' => $id])->row();
        $this->template->set('manual', $manual);
        $this->template->render('form');
    }

    public function save()
    {
        $id = $this->input->post('id');
        $old_file = $this->input->post('old_file');
        
        file_put_contents('/tmp/post.log', "POST: " . print_r($_POST, true) . "\nFILES: " . print_r($_FILES, true));
        
        $this->db->trans_begin();
        $config['upload_path']   = './assets/files/';
        $config['allowed_types'] = 'pdf';
        $config['encrypt_name']  = true;
        
        $this->load->library('upload', $config);
        
        if (!empty($_FILES['document']['name'])) {
            if ($this->upload->do_upload('document')) {
                $file = $this->upload->data();
                $data = [
                    'file_name' => $file['file_name'],
                    'original_name' => $file['client_name'],
                    'description' => $this->input->post('description'),
                    'status' => 'N',
                    'created_by' => $this->auth->user_id(),
                    'created_on' => date('Y-m-d H:i:s')
                ];
                
                if ($id) {
                    // if update, modify created_on to modified_on
                    unset($data['status'], $data['created_by'], $data['created_on']);
                    $data['modified_by'] = $this->auth->user_id();
                    $data['modified_on'] = date('Y-m-d H:i:s');
                    $this->db->update('app_manuals', $data, ['id' => $id]);
                    
                    // delete old file if new file is uploaded
                    if ($old_file && file_exists('./assets/files/' . $old_file)) {
                        @unlink('./assets/files/' . $old_file);
                    }
                } else {
                    $this->db->insert('app_manuals', $data);
                }
                
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $return = ['status' => 0, 'msg' => 'Failed to save data.'];
                    @unlink('./assets/files/' . $file['file_name']);
                } else {
                    $this->db->trans_commit();
                    $return = ['status' => 1, 'msg' => 'Data saved successfully.'];
                }
            } else {
                $return = ['status' => 0, 'msg' => $this->upload->display_errors('','')];
                echo json_encode($return);
                return;
            }
        } else {
            // Update description only
            if ($id) {
                $data = [
                    'description' => $this->input->post('description'),
                    'modified_by' => $this->auth->user_id(),
                    'modified_on' => date('Y-m-d H:i:s')
                ];
                $this->db->update('app_manuals', $data, ['id' => $id]);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $return = ['status' => 0, 'msg' => 'Failed to update data.'];
                } else {
                    $this->db->trans_commit();
                    $return = ['status' => 1, 'msg' => 'Description updated successfully.'];
                }
            } else {
                $return = ['status' => 0, 'msg' => 'File is required.'];
            }
        }
        
        echo json_encode($return);
    }

    public function set_active()
    {
        $id = $this->input->post('id');
        $this->db->trans_begin();
        
        // Deactivate all
        $this->db->update('app_manuals', ['status' => 'N']);
        
        // Activate selected
        $this->db->update('app_manuals', ['status' => 'Y', 'modified_by' => $this->auth->user_id(), 'modified_on' => date('Y-m-d H:i:s')], ['id' => $id]);
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $return = ['status' => 0, 'msg' => 'Failed to activate.'];
        } else {
            $this->db->trans_commit();
            $return = ['status' => 1, 'msg' => 'Manual Book activated successfully.'];
        }
        echo json_encode($return);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        $data = $this->db->get_where('app_manuals', ['id' => $id])->row();
        
        if ($data) {
            $this->db->trans_begin();
            $this->db->delete('app_manuals', ['id' => $id]);
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $return = ['status' => 0, 'msg' => 'Failed to delete data.'];
            } else {
                @unlink('./assets/files/' . $data->file_name);
                $this->db->trans_commit();
                $return = ['status' => 1, 'msg' => 'Data deleted successfully.'];
            }
        } else {
            $return = ['status' => 0, 'msg' => 'Data not found.'];
        }
        echo json_encode($return);
    }
}
