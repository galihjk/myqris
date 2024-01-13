<?php
function qr__png($isi){
    f("qr.init")();
    $code = date("Ymd").md5($isi);
    $png = "temp/$code.png";
    QRcode::png($isi, $png);
    return $png;
}
    