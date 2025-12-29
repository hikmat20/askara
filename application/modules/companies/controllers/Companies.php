<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Companies extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Company_model', 'Comp');
        $this->template->set([
            'title' => 'Company',
            'icon' => 'fa fa-bulding'
        ]);

        date_default_timezone_set("Asia/Bangkok");
    }

    public function index()
    {
        $data = [];
        if ($this->company == '1') {
            $data = $this->db->get_where('companies')->result();
        } else {
            $data = $this->db->get_where('companies', ['id_perusahaan' => $this->company])->result();
        }

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
        $post       = $this->input->post();
        if($post){
            $return = $this->Comp->saveData();
        }
        echo json_encode($return);
    }

    //Edit Perusahaan
    public function edit($id = null)
    {
        $data = $this->db->get_where('companies', ['id_perusahaan' => $id])->row();
        $branch = $this->db->get_where('company_branch', ['company_id' => $id])->result();
        $this->template->render('edit', ['data' => $data, 'branch' => $branch]);
    }

    public function view($id = null)
    {
        $data = $this->db->get_where('companies', ['id_perusahaan' => $id])->row();
        $branch = $this->db->get_where('company_branch', ['company_id' => $id])->result();
        $this->template->render('view', ['data' => $data, 'branch' => $branch]);
    }

    public function load_data()
    {
        $data = $this->db->get_where('companies')->result();
        $this->template->set('data', $data);
        $this->template->render('load_data');
    }

    function hapus_perusahaan()
    {
        $this->auth->restrict($this->deletePermission);
        $id = $this->uri->segment(3);

        if ($id != '') {

            $result = $this->Perusahaan_model->delete($id);

            $keterangan     = "SUKSES, Delete data Perusahaan " . $id;
            $status         = 1;
            $nm_hak_akses   = $this->addPermission;
            $kode_universal = $id;
            $jumlah         = 1;
            $sql            = $this->db->last_query();
        } else {
            $result = 0;
            $keterangan     = "GAGAL, Delete data Perusahaan " . $id;
            $status         = 0;
            $nm_hak_akses   = $this->addPermission;
            $kode_universal = $id;
            $jumlah         = 1;
            $sql            = $this->db->last_query();
        }

        //Save Log
        simpan_aktifitas($nm_hak_akses, $kode_universal, $keterangan, $jumlah, $sql, $status);

        $param = array(
            'delete' => $result,
            'idx' => $id
        );

        echo json_encode($param);
    }
}
