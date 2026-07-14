<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Preferences extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template->set([
            'title' => 'Preferences',
            'icon' => 'fa fa-cogs'
        ]);

        $this->load->model('Preferences_model', 'PrefModel');
    }

    public function index()
    {
        $settings = $this->PrefModel->get_settings($this->company);
        $positions = $this->db->get_where('positions', ['company_id' => $this->company])->result();
        
        $this->template->set([
            'settings' => $settings,
            'positions' => $positions
        ]);
        $this->template->render('index');
    }

    public function save()
    {
        $data = $this->input->post();
        if ($data) {
            $save = $this->PrefModel->save_settings($this->company, $data);
            if ($save) {
                $return = [
                    'status' => 1,
                    'msg'    => 'Preferences successfully saved.'
                ];
            } else {
                $return = [
                    'status' => 0,
                    'msg'    => 'Failed to save preferences. Please try again.'
                ];
            }
        } else {
            $return = [
                'status' => 0,
                'msg'    => 'No data received.'
            ];
        }
        
        echo json_encode($return);
    }
}
