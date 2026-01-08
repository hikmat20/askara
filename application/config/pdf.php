<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['pdf'] = [
    'enabled'      => (ENVIRONMENT === 'development'),
    'min_php'      => '7.1.0',
    'paper'        => 'A4',
    'orientation'  => 'portrait',
];
