<?php
function str__dataencrypt($data,$urlencode){
    $dataencrypt = [
        $data,
        crc32(print_r($data,1))
    ];
    $json = json_encode($dataencrypt);
    $encrypt = f("str.encrypt")($json,$urlencode);
    return $encrypt;
}