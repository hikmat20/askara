<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Antigravity
 *
 * This controller acts as a background worker (Cron) to send queued emails.
 */

class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Hanya izinkan akses dari internal / CLI cURL agar tidak ada sembarang orang yg hit
        if (!$this->input->is_ajax_request() && !is_cli() && strpos($_SERVER['REMOTE_ADDR'], '127.0.0.1') === false) {
            // Bisa di-protect dengan auth token khusus jika di production server terpisah
        }
    }

    /**
     * Dipanggil oleh Email_runner->_trigger_background_worker()
     */
    public function process_email_queue()
    {
        // Abaikan koneksi user yang terputus (cURL timeout 1 detik dari runner) agar proses terus berjalan.
        ignore_user_abort(true);
        set_time_limit(0); 

        // 1. Ambil batasan limit email (contoh batch per 5 email)
        $queues = $this->db->get_where('email_queues', ['status' => 'PND'], 5)->result();

        if (empty($queues)) {
            echo "No pending queues.";
            return;
        }

        // 2. Ambil konfigurasi SMTP dari database settings
        $this->load->library('encryption');
        $this->db->like('setting_name', 'smtp_');
        $smtp_data = $this->db->get('settings')->result();

        $settings = [];
        foreach ($smtp_data as $dt) {
            if ($dt->setting_name == 'smtp_pass' && !empty($dt->value)) {
                $decrypted = $this->encryption->decrypt($dt->value);
                $settings[$dt->setting_name] = ($decrypted !== FALSE && $decrypted !== '') ? $decrypted : $dt->value;
            } else {
                $settings[$dt->setting_name] = $dt->value;
            }
        }

        if (empty($settings['smtp_host']) || empty($settings['smtp_user']) || empty($settings['smtp_pass'])) {
            echo "SMTP Configuration is missing.";
            return;
        }

        // Bersihkan host dari awalan ssl:// atau tls:// karena di-handle oleh smtp_crypto di CI3
        $clean_host = str_replace(['ssl://', 'tls://'], '', $settings['smtp_host']);

        // 3. Set Config CI3 Email Library
        $config = [
            'protocol'    => 'smtp',
            'smtp_host'   => $clean_host,
            'smtp_port'   => $settings['smtp_port'],
            'smtp_user'   => $settings['smtp_user'],
            'smtp_pass'   => $settings['smtp_pass'],
            'smtp_crypto' => isset($settings['smtp_crypto']) ? $settings['smtp_crypto'] : 'ssl',
            'mailtype'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n",
            'crlf'        => "\r\n",
            'wordwrap'    => TRUE
        ];

        $this->load->library('email');
        $this->email->initialize($config);

        // 4. Looping Pengiriman
        foreach ($queues as $q) {
            $this->email->clear();
            
            $this->email->from($settings['smtp_user'], 'Askara Notification System');
            $this->email->to($q->to_email);
            $this->email->subject($q->subject);
            $this->email->message($q->message);

            if ($this->email->send()) {
                // Success
                $this->db->update('email_queues', [
                    'status'   => 'SND',
                    'sent_at'  => date('Y-m-d H:i:s'),
                    'attempts' => $q->attempts + 1
                ], ['id' => $q->id]);
            } else {
                // Failed
                $error_msg = $this->email->print_debugger(['headers']);
                $new_status = ($q->attempts >= 3) ? 'FAI' : 'PND'; // 3x gagal = FAI
                
                $this->db->update('email_queues', [
                    'status'    => $new_status,
                    'error_msg' => substr($error_msg, 0, 1000), // Simpan limit string agar tidak pecah DB
                    'attempts'  => $q->attempts + 1
                ], ['id' => $q->id]);
            }
        }
        
        echo "Batch processed.";
    }
}
