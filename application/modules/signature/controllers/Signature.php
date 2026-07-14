<?php defined('BASEPATH') or exit('No direct script access allowed');

class Signature extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('ciqrcode');
        $this->load->helper('signature/signature');
        $this->load->model('Signature_model', 'Signature');
    }

    /**
     * TEST GENERATE QR
     */
    public function generate_qr()
    {
        $token = generate_qr_token();

        $verifyUrl = site_url('signature/verify?token=' . $token);

        $filePath = FCPATH . 'directory/SIGNATURE/' . $token . '.png';

        $params = [
            'data'     => $verifyUrl,
            'level'    => 'H',
            'size'     => 10,
            'savename' => $filePath
        ];

        $this->ciqrcode->generate($params);

        $data['qr_image'] = base_url('directory/SIGNATURE/' . $token . '.png');
        $data['token']    = $token;
        $data['url']      = $verifyUrl;

        $this->load->view('signature/qr_test', $data);
    }

    /**
     * Dummy verify (nanti kita isi)
     */
    public function verify()
    {
        $token = $this->input->get('token');

        if (!$token) {
            show_404();
        }
        $result = $this->Signature->getByToken($token);
        
        if (!$result) {
            return $this->load->view('invalid');
        }

        $document = $this->Signature->getDocument($result->document_id, $result->document_type);

        if (!$document) {
            return $this->load->view('invalid');
        }

        $currentHash = null;
        if (file_exists($document->file_path)) {
            $currentHash = hash_file('sha256', $document->file_path);
        }

        $status = 'VALID';

        if ($result->status === 'REVOKE') {
            $status = 'REVOKED';
        }

        // if ($currentHash !== $document->file_path) {
        //     $status = 'TAMPERED';
        // }

        $data = [
            'signature'  => $result,
            'document'   => $document,
            'status'     => $status,
        ];

        $this->load->view('verify_result', $data);
    }

    public function view_document()
    {
        $this->load->library('users/auth');
        if (!$this->auth->is_login()) {
            $this->session->set_flashdata('warning', 'Silakan login terlebih dahulu untuk melihat file asli.');
            redirect('login');
        }

        $token = $this->input->get('token');
        if (!$token) {
            show_404();
        }

        $result = $this->Signature->getByToken($token);
        if (!$result || $result->status === 'REVOKE') {
            show_404();
        }

        $document = $this->Signature->getDocument($result->document_id, $result->document_type);
        if (!$document || empty($document->file_path)) {
            show_404();
        }

        $clean_path = ltrim($document->file_path, './');
        $full_path = FCPATH . $clean_path;

        if (!file_exists($full_path)) {
            show_404();
        }

        $mime = mime_content_type($full_path);
        if (!$mime) {
            $mime = 'application/pdf';
        }

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($full_path) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        readfile($full_path);
        exit;
    }
}
