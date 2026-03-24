<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

$setting = $CI->db->get('settings')->result();
foreach($setting as $set ){
    $recaptcha[$set->setting_name] = $set->value;
}

$config['recaptcha'] = array(
    'site_key'   => $recaptcha['recaptcha_site_key'],    // Ganti dengan Site Key Anda
    'secret_key' => $recaptcha['recaptcha_secret_key'],  // Ganti dengan Secret Key Anda
    'threshold'  => $recaptcha['recaptcha_threshold']      // Ambang batas skor(0.0 - 1.0)
);