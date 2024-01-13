<?php
function cleartemp(){
    $scandir = scandir("temp");
    foreach($scandir as $file){
        if(in_array($file, ['..', '.', 'info.txt'])) continue;
        if(!f("str.is_diawali")($file,date("Ymd"))){
            unlink("temp/$file");
        }
    }
    return true;
}
    