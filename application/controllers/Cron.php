<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Antigravity
 *
 * This controller acts as a background worker (Cron) to send queued emails.
 * Uses the email_template view to wrap the message body with branded HTML layout.
 */

class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Dipanggil oleh Email_runner->_trigger_background_worker()
     */
    public function process_email_queue()
    {
        // Abaikan koneksi user yang terputus (cURL timeout 1 detik dari runner) agar proses terus berjalan.
        ignore_user_abort(true);
        set_time_limit(0); 

        // 1. Ambil batasan limit email (batch per 5 email)
        $queues = $this->db->get_where('email_queues', ['status' => 'PND'], 5)->result();

        if (empty($queues)) {
            echo "[" . date('Y-m-d H:i:s') . "] No pending queues.\n";
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
        $body_db = $this->db->get_where('settings', ['setting_name' => 'email_template_body'])->row();
        $css_db = $this->db->get_where('settings', ['setting_name' => 'email_template_css'])->row();

        // Ambil Overrides Variabel Email (Global)
        $vars_keys = ['email_vars_company_name', 'email_vars_company_address', 'email_vars_company_logo'];
        $vars_db = $this->db->where_in('setting_name', $vars_keys)->get('settings')->result();
        $email_overrides = [];
        foreach ($vars_db as $v) {
            $email_overrides[$v->setting_name] = $v->value;
        }

        foreach ($queues as $q) {
            $this->email->clear();
            
            $this->email->from($settings['smtp_user'], 'Askara Notification System');
            $this->email->to($q->to_email);
            $this->email->subject($q->subject);

            // Ambil data perusahaan untuk placeholder dinamis (sebagai fallback)
            $company = $this->db->get_where('companies', ['id_perusahaan' => $q->company_id])->row();

            if ($body_db) {
                // Gunakan template terpisah (Body & CSS)
                $htmlBody = $body_db->value;
                $htmlCss = ($css_db) ? $css_db->value : '';
                $htmlMessage = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>' . $htmlCss . '</style></head><body class="email-template">' . $htmlBody . '</body></html>';
            } else {
                // Fallback 1: Template Full HTML dari database (Lama)
                $old_db = $this->db->get_where('settings', ['setting_name' => 'email_template_html'])->row();
                if ($old_db) {
                    $htmlMessage = $old_db->value;
                } else {
                    // Fallback 2: Bungkus pesan dengan template HTML fisik (Lama)
                    $htmlMessage = $this->load->view('setting/email_template', ['message' => $q->message], true);
                }
            }

            // Ganti Placeholder Dasar
            $htmlMessage = str_replace('{{content}}', $q->message, $htmlMessage);
            $htmlMessage = str_replace('{{subject}}', $q->subject, $htmlMessage);

            // Tentukan Nilai untuk Placeholder (Prioritas: Overrides > Master Perusahaan)
            $final_name    = (!empty($email_overrides['email_vars_company_name'])) ? $email_overrides['email_vars_company_name'] : ($company ? $company->nm_perusahaan : '');
            $final_address = (!empty($email_overrides['email_vars_company_address'])) ? $email_overrides['email_vars_company_address'] : ($company ? $company->alamat : '');
            $final_logo    = '';

            if (!empty($email_overrides['email_vars_company_logo'])) {
                $final_logo = $email_overrides['email_vars_company_logo'];
            } elseif ($company && !empty($company->logo)) {
                $final_logo = base_url($company->path_logo . $company->id_perusahaan . '/' . $company->logo);
            }

            $htmlMessage = str_replace('{{company_name}}', $final_name, $htmlMessage);
            $htmlMessage = str_replace('{{company_address}}', $final_address, $htmlMessage);
            $htmlMessage = str_replace('{{company_logo}}', $final_logo, $htmlMessage);

            // Ganti Action URL (Jika kosong, arahkan ke Home)
            $final_url = (!empty($q->action_url)) ? $q->action_url : base_url();
            $htmlMessage = str_replace('{{action_url}}', $final_url, $htmlMessage);
            
            $this->email->message($htmlMessage);

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
                $clean_error = strip_tags($error_msg);
                
                // Ekstrak pesan error asli dari output debugger CI3
                if (strpos($clean_error, 'The following SMTP error was encountered:') !== false) {
                    $parts = explode('The following SMTP error was encountered:', $clean_error);
                    $clean_error = 'SMTP Error: ' . trim(end($parts));
                }
                
                // Jika masih terlalu panjang, potong
                if (strlen($clean_error) > 1000) {
                    $clean_error = substr($clean_error, 0, 1000);
                }

                $new_status = ($q->attempts >= 3) ? 'FAI' : 'PND'; // 3x gagal = FAI
                
                $this->db->update('email_queues', [
                    'status'    => $new_status,
                    'error_msg' => $clean_error,
                    'attempts'  => $q->attempts + 1
                ], ['id' => $q->id]);
            }
        }
        
        echo "[" . date('Y-m-d H:i:s') . "] Batch processed " . count($queues) . " emails.\n";
    }
}
