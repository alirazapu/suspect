<?php

if (!function_exists('pid_encrypt')) {
    function pid_encrypt(string|int $value): string
    {
        $encryptMethod = 'AES-256-CBC';
        $secretKey     = 'Irfan love CTD';
        $secretIv      = 'SEStoPakistan';
        $key = hash('sha256', $secretKey);
        $iv  = substr(hash('sha256', $secretIv), 0, 16);
        return base64_encode(openssl_encrypt((string) $value, $encryptMethod, $key, 0, $iv));
    }
}

if (!function_exists('pid_decrypt')) {
    function pid_decrypt(string $value): string|false
    {
        $encryptMethod = 'AES-256-CBC';
        $secretKey     = 'Irfan love CTD';
        $secretIv      = 'SEStoPakistan';
        $key = hash('sha256', $secretKey);
        $iv  = substr(hash('sha256', $secretIv), 0, 16);
        return openssl_decrypt(base64_decode($value), $encryptMethod, $key, 0, $iv);
    }
}
