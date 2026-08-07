<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Antigravity
 *
 * This controller acts as a background worker (Cron) to send queued emails.
 * Uses active configuration from email_configurations master table (or settings fallback).
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

        // Otomatis buat/perbarui skema tabel email_queues saat pertama kali dijalankan di server live
        $this->_check_table_and_migrate();

        // 0. Mutex / Process Locking menggunakan flock untuk mencegah multiple worker running bersamaan
        $lock_file = sys_get_temp_dir() . '/askara_email_cron.lock';
        $fp = @fopen($lock_file, 'c+');
        if ($fp) {
            if (!@flock($fp, LOCK_EX | LOCK_NB)) {
                // Worker lain sedang berjalan, keluar dengan aman
                @fclose($fp);
                return;
            }
        }

        // 0b. Reset antrean status 'PRG' (Processing) yang gantung > 5 menit kembali ke 'PND'
        $five_mins_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $this->db->where('status', 'PRG');
        $this->db->group_start();
        $this->db->where('locked_at <', $five_mins_ago);
        $this->db->or_where('locked_at IS NULL'); // Handle lama
        $this->db->group_end();
        $this->db->update('email_queues', ['status' => 'PND']);

        // 1. Ambil kandidat antrean pending (batch per 5 email)
        $candidates = $this->db->get_where('email_queues', ['status' => 'PND'], 5)->result();

        if (empty($candidates)) {
            if ($fp) {
                @flock($fp, LOCK_UN);
                @fclose($fp);
            }
            echo "[" . date('Y-m-d H:i:s') . "] No pending queues.\n";
            return;
        }

        // 1b. Atomic Status Claim: Klaim kandidat antrean dari status 'PND' ke 'PRG' (Processing)
        $candidate_ids = array_column($candidates, 'id');
        $this->db->where_in('id', $candidate_ids);
        $this->db->where('status', 'PND');
        $this->db->update('email_queues', ['status' => 'PRG', 'locked_at' => date('Y-m-d H:i:s')]);

        if ($this->db->affected_rows() == 0) {
            if ($fp) {
                @flock($fp, LOCK_UN);
                @fclose($fp);
            }
            return;
        }

        // Ambil hanya antrean yang berhasil diklaim secara atomik oleh worker ini
        $queues = $this->db->where_in('id', $candidate_ids)
                           ->where('status', 'PRG')
                           ->get('email_queues')
                           ->result();

        if (empty($queues)) {
            if ($fp) {
                @flock($fp, LOCK_UN);
                @fclose($fp);
            }
            return;
        }

        // 2. Ambil konfigurasi SMTP dari Master Email Configurations (atau Fallback Settings)
        $this->load->library('encryption');
        
        $active_config_id = null;
        $smtp_host        = '';
        $smtp_port        = 465;
        $smtp_user        = '';
        $smtp_pass        = '';
        $smtp_crypto      = 'ssl';
        $sender_name      = 'Askara Notification System';
        $sender_email     = '';
        $reply_to_name    = null;
        $reply_to_email   = null;

        if ($this->db->table_exists('email_configurations')) {
            $this->load->model('setting/Email_configuration_model', 'email_cfg');
            $cfg = $this->email_cfg->get_active();
            if ($cfg) {
                $active_config_id = $cfg->id;
                $smtp_host        = $cfg->smtp_host;
                $smtp_port        = $cfg->smtp_port;
                $smtp_user        = $cfg->smtp_user;
                $decrypted        = $this->encryption->decrypt($cfg->smtp_pass);
                $smtp_pass        = ($decrypted !== FALSE && $decrypted !== '') ? $decrypted : $cfg->smtp_pass;
                $smtp_crypto      = $cfg->smtp_crypto;
                $sender_name      = !empty($cfg->sender_name) ? $cfg->sender_name : 'Askara Notification System';
                $sender_email     = !empty($cfg->sender_email) ? $cfg->sender_email : $cfg->smtp_user;
                $reply_to_name    = $cfg->reply_to_name;
                $reply_to_email   = $cfg->reply_to_email;
            }
        }

        // Fallback ke tabel settings jika belum ada di master email_configurations
        if (empty($smtp_host) || empty($smtp_user)) {
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

            if (!empty($settings['smtp_host']) && !empty($settings['smtp_user'])) {
                $smtp_host    = $settings['smtp_host'];
                $smtp_port    = isset($settings['smtp_port']) ? $settings['smtp_port'] : 465;
                $smtp_user    = $settings['smtp_user'];
                $smtp_pass    = isset($settings['smtp_pass']) ? $settings['smtp_pass'] : '';
                $smtp_crypto  = isset($settings['smtp_crypto']) ? $settings['smtp_crypto'] : 'ssl';
                $sender_email = $smtp_user;
            }
        }

        if (empty($smtp_host) || empty($smtp_user) || empty($smtp_pass)) {
            // Kembalikan status ke PND agar bisa dicoba kembali setelah konfigurasi diselesaikan
            $this->db->where_in('id', array_column($queues, 'id'));
            $this->db->update('email_queues', ['status' => 'PND']);

            if ($fp) {
                @flock($fp, LOCK_UN);
                @fclose($fp);
            }
            echo "SMTP Configuration is missing.";
            return;
        }

        // Bersihkan host dari awalan ssl:// atau tls:// karena di-handle oleh smtp_crypto di CI3
        $clean_host = str_replace(['ssl://', 'tls://'], '', $smtp_host);

        // 3. Set Config CI3 Email Library
        $config = [
            'protocol'    => 'smtp',
            'smtp_host'   => $clean_host,
            'smtp_port'   => $smtp_port,
            'smtp_user'   => $smtp_user,
            'smtp_pass'   => $smtp_pass,
            'smtp_crypto' => !empty($smtp_crypto) ? $smtp_crypto : 'ssl',
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
        $css_db  = $this->db->get_where('settings', ['setting_name' => 'email_template_css'])->row();

        // Ambil Overrides Variabel Email (Global)
        $vars_keys = ['email_vars_company_name', 'email_vars_company_address', 'email_vars_company_logo'];
        $vars_db   = $this->db->where_in('setting_name', $vars_keys)->get('settings')->result();
        $email_overrides = [];
        foreach ($vars_db as $v) {
            $email_overrides[$v->setting_name] = $v->value;
        }

        $success_count = 0;
        $last_error_msg = null;

        foreach ($queues as $q) {
            $this->email->clear();
            
            $this->email->from($sender_email, $sender_name);
            if (!empty($reply_to_email)) {
                $this->email->reply_to($reply_to_email, !empty($reply_to_name) ? $reply_to_name : $sender_name);
            }
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
                $final_logo = base_url('directory/COMPANY/' . $email_overrides['email_vars_company_logo']);
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
                $success_count++;
                $this->db->update('email_queues', [
                    'status'   => 'SND',
                    'sent_at'  => date('Y-m-d H:i:s'),
                    'attempts' => $q->attempts + 1
                ], ['id' => $q->id]);
            } else {
                // Failed
                $error_msg = $this->email->print_debugger(['headers']);
                $clean_error = strip_tags($error_msg);
                
                if (strpos($clean_error, 'The following SMTP error was encountered:') !== false) {
                    $parts = explode('The following SMTP error was encountered:', $clean_error);
                    $clean_error = 'SMTP Error: ' . trim(end($parts));
                }
                
                if (strlen($clean_error) > 1000) {
                    $clean_error = substr($clean_error, 0, 1000);
                }

                $last_error_msg = $clean_error;

                $new_status = ($q->attempts >= 3) ? 'FAI' : 'PND'; // 3x gagal = FAI
                
                $this->db->update('email_queues', [
                    'status'    => $new_status,
                    'error_msg' => $clean_error,
                    'attempts'  => $q->attempts + 1
                ], ['id' => $q->id]);
            }
        }

        // 5. Update Status SMTP Log pada Master Konfigurasi
        if ($active_config_id) {
            $now = date('Y-m-d H:i:s');
            if ($success_count > 0) {
                $this->db->update('email_configurations', [
                    'last_success_at' => $now
                ], ['id' => $active_config_id]);
            }
            if (!empty($last_error_msg)) {
                $this->db->update('email_configurations', [
                    'last_error_at'  => $now,
                    'last_error_msg' => $last_error_msg
                ], ['id' => $active_config_id]);
            }
        }

        // Lepas file lock jika ada
        if ($fp) {
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
        
        echo "[" . date('Y-m-d H:i:s') . "] Batch processed " . count($queues) . " emails.\n";
    }

    /**
     * Otomatis membuat tabel email_queues jika belum ada
     * dan memastikan kolom status mendukung 'PRG' (Processing) saat di-deploy ke server live.
     */
    private function _check_table_and_migrate()
    {
        if (!$this->db->table_exists('email_queues')) {
            $sql = "CREATE TABLE IF NOT EXISTS `email_queues` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `company_id` int(11) DEFAULT 1,
              `to_email` text NOT NULL,
              `subject` varchar(255) NOT NULL,
              `message` longtext NOT NULL,
              `action_url` text DEFAULT NULL,
              `status` varchar(10) NOT NULL DEFAULT 'PND' COMMENT 'PND=Pending, PRG=Processing, SND=Sent, FAI=Failed',
              `attempts` int(11) NOT NULL DEFAULT 0,
              `error_msg` text DEFAULT NULL,
              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `locked_at` datetime DEFAULT NULL,
              `sent_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql);
        } else {
            // Pastikan kolom status adalah VARCHAR(10) agar mendukung status 'PRG'
            $fields = $this->db->field_data('email_queues');
            $has_locked_at = false;
            foreach ($fields as $field) {
                if ($field->name == 'status') {
                    if (strpos(strtolower($field->type), 'enum') !== false || $field->max_length < 10) {
                        $this->db->query("ALTER TABLE `email_queues` MODIFY COLUMN `status` VARCHAR(10) NOT NULL DEFAULT 'PND' COMMENT 'PND=Pending, PRG=Processing, SND=Sent, FAI=Failed'");
                    }
                }
                if ($field->name == 'locked_at') {
                    $has_locked_at = true;
                }
            }
            if (!$has_locked_at) {
                $this->db->query("ALTER TABLE `email_queues` ADD COLUMN `locked_at` DATETIME NULL DEFAULT NULL AFTER `created_at`");
            }
        }
    }
}
