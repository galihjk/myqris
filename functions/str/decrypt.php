<?php
function str__decrypt($str, $urldecode = false){
    if($urldecode) $str = urldecode($str);
    $decryption_iv = '1234567891101121';
    $ciphering = "AES-128-CTR";
    $decryption_key = "my_qris_galihjk";
    $options = 0;
    $decryption=openssl_decrypt (
        $str, $ciphering, 
        $decryption_key, $options, $decryption_iv
    );
    return $decryption;
}