<?php
function data__save($name, $data){
    $GLOBALS['data'][$name] = $data;
    $filename="data/$name.json";
    $dirname = dirname($filename);
    if (!is_dir($dirname)) {
        mkdir($dirname, 0777, true);
    }
	return file_put_contents($filename, json_encode($data)); 
}