<?php
function data__delete($name){
    $filename="data/$name.json";
    if(file_exists($filename)){
        return unlink($filename);
    }
	return false;
}