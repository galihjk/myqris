<?php
function db__disconnect($db = null){
    if(!$db and !empty($GLOBALS['global_db_connect'])){
        $GLOBALS['global_db_connect']->close();
        return true;
    }
    if($db){
        $db->close();
        return true;
    }
    return false;
}