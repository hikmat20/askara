<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('generate_qr_token')) {
    function generate_qr_token()
    {
        return bin2hex(random_bytes(32));
    }
}
