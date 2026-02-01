<?php defined('BASEPATH') or exit('No direct script access allowed');

class Signature extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('ciqrcode');
        $this->load->helper('signature/signature');
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
        echo 'QR SCANNED - TOKEN OK';
    }
}
