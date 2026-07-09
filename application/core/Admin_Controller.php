<?php defined('BASEPATH') || exit('No direct script access allowed');

class Admin_Controller extends Base_Controller
{
    protected $pager;
    protected $limit;
    protected $user_data;
    protected $ArrPosts;
    protected $company;
    protected $group_id;

    public function __construct()
    {
        $this->autoload['helpers'][]   = 'form';
        $this->autoload['helpers'][]   = 'app';
        $this->autoload['libraries'][] = 'Template';
        $this->autoload['libraries'][] = 'users/auth';

        parent::__construct();

        $this->load->model('identitas_model');

        /*Check If user has logged in*/
        if (!$this->auth->is_login()) {
            redirect('login');
        }

        $idt                = $this->identitas_model->find(1);
        $this->user_data    = $this->auth->userdata();
        $this->company      = $this->session->company->id_perusahaan;
        $this->group_id     = $this->session->group->id_group;
        $companies          = $this->db->get_where('companies')->result();
        
        $this->form_validation->set_error_delimiters('', '');
        $positions = $this->db->get_where('positions', ['assign_user' => $this->auth->user_id(), 'company_id' => $this->company])->result();
        
        $user_positions = $this->db->select('position_id as id')
            ->from('user_positions')
            ->join('positions', 'positions.id = user_positions.position_id')
            ->where('user_positions.user_id', $this->auth->user_id())
            ->where('positions.company_id', $this->company)
            ->get()->result();

        $ArrPos = [];
        foreach ($positions as $pos) {
            $ArrPos[] = $pos->id;
        }
        foreach ($user_positions as $pos) {
            if (!in_array($pos->id, $ArrPos)) {
                $ArrPos[] = $pos->id;
            }
        }

        $this->ArrPosts         = $ArrPos;
        // Pagination config
        $this->pager = array(
            'full_tag_open'     => '<ul class="pagination pull-right" style="margin: 0 0 0;">',
            'full_tag_close'    => '</ul>',
            'next_link'         => '&rarr;',
            'prev_link'         => '&larr;',
            'next_tag_open'     => '<li>',
            'next_tag_close'    => '</li>',
            'prev_tag_open'     => '<li>',
            'prev_tag_close'    => '</li>',
            'first_tag_open'    => '<li>',
            'first_tag_close'   => '</li>',
            'last_tag_open'     => '<li>',
            'last_tag_close'    => '</li>',
            'cur_tag_open'      => '<li class="active"><a href="#">',
            'cur_tag_close'     => '</a></li>',
            'num_tag_open'      => '<li>',
            'num_tag_close'     => '</li>',
        );

        // Basic setup
        $this->template->set('userData', $this->user_data);
        $this->template->set('idt', $idt);
        $this->template->set('companies', $companies);
        $this->template->set('company_name', $this->session->company->nm_perusahaan);

        // $this->template->set_theme('admin');
        $this->template->set_theme('dashboard');
        $this->template->set_layout('index');

        // $this->branch 		= $this->session->app_session['company_id'];

        //Overwrite if the request is ajax
        if ($this->input->is_ajax_request()) {
            $this->template->set_layout('ajax');
        }

        $this->form_validation->set_error_delimiters('', '');
    }
    protected function _check_download_permission($menu_link)
    {
        if ($this->auth->is_admin()) {
            return true;
        }

        $permission = $this->db->select('group_menus.*')
            ->from('group_menus')
            ->join('menus', 'group_menus.menu_id = menus.id')
            ->where('group_menus.group_id', $this->group_id)
            ->where('group_menus.company_id', $this->company)
            ->where('menus.link', $menu_link)
            ->get()
            ->row();

        return ($permission && $permission->download == '1');
    }
}
/* End of file Admin_Controller.php */
