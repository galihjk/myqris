<?php
function str__datadecrypt($encryptstr){
    $decrypt_json = f("str.decrypt")($encryptstr);
    $decrypt = json_decode($decrypt_json,true);
    if(empty($decrypt[1])){
        dd("invalid encryption");
        return false;
    }
    $data = $decrypt[0];
    $hash = $decrypt[1];
    if(crc32(print_r($data,1)) != $hash){
        dd("invalid encryption.");
        return false;
    }
    return $data;
}