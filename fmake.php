<?php

if(!empty($argv[1])){
    $f = $argv[1];
    $filename = "functions/".str_replace(".","/",$f).".php";
    $explode = explode("/",$filename);
    unset($explode[count($explode) - 1]);
    $dir = implode("/",$explode);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $function_name = str_replace(".","__",$f);
    $data = "<?php
function $function_name(\$param){
    return \$param;
}
    ";
    file_put_contents($filename, $data);
    echo "Fungsi $f berhasil dibuat";
}
else{
    echo "FAILED!";
}