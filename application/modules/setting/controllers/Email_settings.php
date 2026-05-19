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

    /**
     * Tampilan daftar antrean email (Queue List)
     */
    public function queue()
    {
        $this->template->set([
            'title' => 'Email Queue List',
        ]);
        $this->template->render('email_queue');
    }

    /**
     * Data JSON untuk Datatables Antrean Email
     */
    public function get_queue_data()
    {
        $draw   = intval($this->input->get('draw'));
        $start  = intval($this->input->get('start'));
        $length = intval($this->input->get('length'));
        $search = $this->input->get('search')['value'];
        $status = $this->input->get('status');

        $this->db->from('email_queues');
        
        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('to_email', $search);
            $this->db->or_like('subject', $search);
            $this->db->group_end();
        }
        $totalFiltered = $this->db->count_all_results('', false);

        $this->db->order_by('id', 'DESC');
        $this->db->limit($length, $start);
        $rows = $this->db->get()->result();

        $totalRecords = $this->db->count_all('email_queues');

        $data = [];
        $no   = $start + 1;
        foreach ($rows as $r) {
            $statusBadge = '';
            if ($r->status == 'SND') {
                $statusBadge = '<span class="label label-light-success label-inline font-weight-bold">Sent</span>';
            } elseif ($r->status == 'FAI') {
                $statusBadge = '<span class="label label-light-danger label-inline font-weight-bold">Failed</span>';
            } else {
                $statusBadge = '<span class="label label-light-warning label-inline font-weight-bold">Pending</span>';
            }

            $actionBtn = '<button type="button" class="btn btn-xs btn-light-info btn-icon btn-preview mr-1" data-id="'.$r->id.'" title="Preview Email"><i class="fa fa-eye"></i></button>';
            if ($r->status == 'FAI') {
                $actionBtn .= '<button type="button" class="btn btn-xs btn-primary btn-resend" data-id="'.$r->id.'" title="Kirim Ulang"><i class="fa fa-redo"></i> Resend</button>';
            } elseif ($r->status == 'PND') {
                $actionBtn .= '<button type="button" class="btn btn-xs btn-secondary" disabled><i class="fa fa-clock"></i> Pending</button>';
            }

            $errorHtml = '';
            if (!empty($r->error_msg)) {
                $errorHtml = '<br><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> '.htmlspecialchars(substr($r->error_msg, 0, 80)).'...</small>';
            }

            $data[] = [
                $no++,
                date('d M Y H:i', strtotime($r->created_at)),
                htmlspecialchars($r->to_email),
                htmlspecialchars($r->subject) . $errorHtml,
                $statusBadge,
                $r->attempts,
                $r->sent_at ? date('d M Y H:i', strtotime($r->sent_at)) : '-',
                $actionBtn
            ];
        }

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data
        ]);
    }

    /**
     * Memasukkan kembali email yang gagal ke antrean (Resend)
     */
    public function resend_queue()
    {
        $id = $this->input->post('id');
        if (!$id) {
            echo json_encode(['status' => 0, 'msg' => 'ID tidak valid.']);
            return;
        }

        $this->db->where('id', $id);
        $this->db->where('status', 'FAI');
        $this->db->update('email_queues', [
            'status'    => 'PND',
            'attempts'  => 0,
            'error_msg' => NULL
        ]);

        if ($this->db->affected_rows() > 0) {
            // Trigger background worker
            $this->load->library('email_runner');
            $this->email_runner->trigger_worker();
            echo json_encode(['status' => 1, 'msg' => 'Email berhasil dimasukkan kembali ke antrean.']);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Email tidak ditemukan atau bukan status Failed.']);
        }
    }

    /**
     * Hapus semua email yang sudah terkirim (status SND) dari queue
     */
    public function clear_sent()
    {
        $this->db->where('status', 'SND');
        $this->db->delete('email_queues');

        echo json_encode(['status' => 1, 'msg' => 'Riwayat email terkirim berhasil dibersihkan.']);
    }

    /**
     * Get summary counts for email queue dashboard
     */
    public function get_queue_counts()
    {
        $pending = $this->db->where('status', 'PND')->count_all_results('email_queues');
        $sent    = $this->db->where('status', 'SND')->count_all_results('email_queues');
        $failed  = $this->db->where('status', 'FAI')->count_all_results('email_queues');
        $total   = $this->db->count_all('email_queues');

        echo json_encode([
            'status'  => 1,
            'pending' => $pending,
            'sent'    => $sent,
            'failed'  => $failed,
            'total'   => $total
        ]);
    }

    /**
     * Editor Template Email
     */
    public function template()
    {
        $template_body = '';
        $template_css = '';

        // Cek pengaturan baru
        $body_db = $this->db->get_where('settings', ['setting_name' => 'email_template_body'])->row();
        $css_db = $this->db->get_where('settings', ['setting_name' => 'email_template_css'])->row();
        
        // Ambil Overrides Variabel Email
        $email_vars = [];
        $vars_keys = ['email_vars_company_name', 'email_vars_company_address', 'email_vars_company_logo'];
        $vars_db = $this->db->where_in('setting_name', $vars_keys)->get('settings')->result();
        foreach ($vars_db as $v) {
            $email_vars[$v->setting_name] = $v->value;
        }

        if ($body_db) {
            $template_body = $body_db->value;
            $template_css = ($css_db) ? $css_db->value : '';
        } else {
            // Migrasi dari email_template_html (Lama) atau File
            $old_db = $this->db->get_where('settings', ['setting_name' => 'email_template_html'])->row();
            $full_html = ($old_db) ? $old_db->value : file_get_contents(APPPATH . 'modules/setting/views/email_template.php');

            // Ekstrak CSS
            preg_match('/<style>(.*?)<\/style>/s', $full_html, $css_match);
            $template_css = isset($css_match[1]) ? trim($css_match[1]) : '';

            // Ekstrak Body Content
            preg_match('/<body.*?>(.*?)<\/body>/s', $full_html, $body_match);
            $template_body = isset($body_match[1]) ? trim($body_match[1]) : $full_html;
        }

        $this->template->set([
            'title'         => 'Edit Template Email',
            'icon'          => 'fa fa-edit',
            'template_body' => $template_body,
            'template_css'  => $template_css,
            'email_vars'    => $email_vars
        ]);
        $this->template->render('email_template_editor');
    }

    public function save_template()
    {
        $template_body = $this->input->post('template_body');
        $template_css  = $this->input->post('template_css');
        
        // Simpan Variabel Overrides
        $email_vars = $this->input->post('email_vars');
        if (is_array($email_vars)) {
            foreach ($email_vars as $key => $val) {
                $this->_upsert_setting($key, $val);
            }
        }

        if ($template_body) {
            // Simpan Body
            $this->_upsert_setting('email_template_body', $template_body);
            // Simpan CSS
            $this->_upsert_setting('email_template_css', $template_css);

            $return = ['status' => 1, 'msg' => 'Template email dan variabel berhasil disimpan.'];
        } else {
            $return = ['status' => 0, 'msg' => 'Konten template tidak boleh kosong.'];
        }
        
        echo json_encode($return);
    }

    private function _upsert_setting($name, $value)
    {
        $exist = $this->db->get_where('settings', ['setting_name' => $name])->num_rows();
        if ($exist > 0) {
            $this->db->update('settings', ['value' => $value], ['setting_name' => $name]);
        } else {
            $this->db->insert('settings', ['setting_name' => $name, 'value' => $value]);
        }
    }

    /**
     * Preview queued email HTML fully compiled
     */
    public function preview($id)
    {
        $q = $this->db->get_where('email_queues', ['id' => $id])->row();
        if ($q) {
            // Get email template body and CSS
            $body_db = $this->db->get_where('settings', ['setting_name' => 'email_template_body'])->row();
            $css_db  = $this->db->get_where('settings', ['setting_name' => 'email_template_css'])->row();

            // Get Email Variable Overrides
            $email_vars = [];
            $vars_keys  = ['email_vars_company_name', 'email_vars_company_address', 'email_vars_company_logo'];
            $vars_db    = $this->db->where_in('setting_name', $vars_keys)->get('settings')->result();
            foreach ($vars_db as $v) {
                $email_vars[$v->setting_name] = $v->value;
            }

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
            $final_name    = (!empty($email_vars['email_vars_company_name'])) ? $email_vars['email_vars_company_name'] : ($company ? $company->nm_perusahaan : '');
            $final_address = (!empty($email_vars['email_vars_company_address'])) ? $email_vars['email_vars_company_address'] : ($company ? $company->alamat : '');
            $final_logo    = '';

            if (!empty($email_vars['email_vars_company_logo'])) {
                $final_logo = base_url('directory/COMPANY/' . $email_vars['email_vars_company_logo']);
            } elseif ($company && !empty($company->logo)) {
                $final_logo = base_url($company->path_logo . $company->id_perusahaan . '/' . $company->logo);
            }

            $htmlMessage = str_replace('{{company_name}}', $final_name, $htmlMessage);
            $htmlMessage = str_replace('{{company_address}}', $final_address, $htmlMessage);
            $htmlMessage = str_replace('{{company_logo}}', $final_logo, $htmlMessage);

            // Ganti Action URL (Jika kosong, arahkan ke Home)
            $final_url = (!empty($q->action_url)) ? $q->action_url : base_url();
            $htmlMessage = str_replace('{{action_url}}', $final_url, $htmlMessage);

            echo $htmlMessage;
        } else {
            echo "Email tidak ditemukan.";
        }
    }
}
