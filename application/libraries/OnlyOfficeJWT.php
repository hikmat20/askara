<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'libraries/PHPJWT/JWT.php';
require_once APPPATH . 'libraries/PHPJWT/Key.php';
require_once APPPATH . 'libraries/PHPJWT/BeforeValidException.php';
require_once APPPATH . 'libraries/PHPJWT/ExpiredException.php';
require_once APPPATH . 'libraries/PHPJWT/SignatureInvalidException.php';

use Firebase\JWT\JWT;

class OnlyOfficeJWT
{
    protected $secret;

    public function __construct()
    {
        // isi secret sesuai di container OnlyOffice
        $this->secret = 'DXD7bKDfDEiA3Jptfq0jARWcoTKd088g';
    }

    public function generate($payload)
    {
        return JWT::encode($payload, $this->secret);
    }
}
