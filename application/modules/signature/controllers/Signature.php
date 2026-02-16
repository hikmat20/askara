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
}
