<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function recaptcha_script($action = 'submit')
{
    $CI = &get_instance();
    $CI->config->load('recaptcha');
    $site_key = $CI->config->item('recaptcha')['site_key'];

    return '<script src="https://www.google.com/recaptcha/api.js?render=' . $site_key . '"></script>
            <script>
                function getToken(action) {
                    grecaptcha.ready(function() {
                        grecaptcha.execute("' . $site_key . '", {action: action}).then(function(token) {
                            var recaptchaResponse = document.getElementById("g-recaptcha-response");
                            recaptchaResponse.value = token;
                        });
                    });
                }
            </script>';
}

function recaptcha_div($action = 'submit')
{
    return '<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">';
}
