<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('pid_encrypt')) {
    function pid_encrypt($value) {
        $encrypt_method = 'AES-256-CBC';
        $secret_key     = 'Irfan love CTD';
        $secret_iv      = 'SEStoPakistan';
        $key = hash('sha256', $secret_key);
        $iv  = substr(hash('sha256', $secret_iv), 0, 16);
        return base64_encode(openssl_encrypt($value, $encrypt_method, $key, 0, $iv));
    }
}

if (!function_exists('pid_decrypt')) {
    function pid_decrypt($value) {
        $encrypt_method = 'AES-256-CBC';
        $secret_key     = 'Irfan love CTD';
        $secret_iv      = 'SEStoPakistan';
        $key = hash('sha256', $secret_key);
        $iv  = substr(hash('sha256', $secret_iv), 0, 16);
        return openssl_decrypt(base64_decode($value), $encrypt_method, $key, 0, $iv);
    }
}
