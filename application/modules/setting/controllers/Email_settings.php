<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Antigravity
 *
 * This is controller for Email Settings
 */

class Email_settings extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template->set([
            'title' => 'Pengaturan Email',
            'icon'  => 'fa fa-envelope'
        ]);
    }

    public function index()
    {
        $this->load->library('encryption');
        // Load existing email settings from db
        $this->db->like('setting_name', 'smtp_');
        $data = $this->db->get('settings')->result();
        
        $setData = [];
        foreach($data as $dt) {
            if ($dt->setting_name == 'smtp_pass' && !empty($dt->value)) {
                $decrypted = $this->encryption->decrypt($dt->value);
                // Jika data lama belum ter-enkripsi, gunakan data asli
                $setData[$dt->setting_name] = ($decrypted !== FALSE && $decrypted !== '') ? $decrypted : $dt->value;
            } else {
                $setData[$dt->setting_name] = $dt->value;
            }
        }
        
        $this->template->set('data', $setData);
        $this->template->render('email_settings');
    }

    public function save()
    {
        $this->load->library('encryption');
        $post = $this->input->post();
        if ($post) {
            $this->db->trans_begin();
            
            foreach ($post as $key => $value) {
                // Encrypt password before saving
                if ($key == 'smtp_pass' && !empty($value)) {
                    $value = $this->encryption->encrypt($value);
                }

                // Check if setting exists
                $exist = $this->db->get_where('settings', ['setting_name' => $key])->num_rows();
                if ($exist > 0) {
                    $this->db->update('settings', ['value' => $value], ['setting_name' => $key]);
                } else {
                    $this->db->insert('settings', ['setting_name' => $key, 'value' => $value]);
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $return = ['status' => 0, 'msg' => 'Gagal menyimpan pengaturan email.'];
            } else {
                $this->db->trans_commit();
                $return = ['status' => 1, 'msg' => 'Pengaturan email berhasil disimpan.'];
            }
        } else {
            $return = ['status' => 0, 'msg' => 'Data tidak valid.'];
        }
        
        echo json_encode($return);
    }

    public function test_email()
    {
        $this->load->library('email_runner');
        
        // Ambil email dari post input UI saat ini sebagai tujuan testing
        $target_email = $this->input->post('smtp_user'); 
        
        if (empty($target_email)) {
            echo json_encode(['status' => 0, 'msg' => 'Email SMTP User harus diisi untuk pengiriman test.']);
            return;
        }

        $subject = "Askara: Test Configuration " . date('H:i');
        $message = "<h3>Berhasil!</h3><p>Jika Anda menerima email ini, berarti pengaturan SMTP (Google Mail) di aplikasi Askara sudah bekerja dengan baik lewat Queue Background.</p>";
        
        // Push to queue
        $this->email_runner->queue($target_email, $subject, $message);

        echo json_encode(['status' => 1, 'msg' => 'Test email berhasil dimasukkan ke antrean! Mohon cek kotak masuk email Anda ('.$target_email.') dalam 1 menit ke depan.']);
    }
}
