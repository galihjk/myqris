<?php
function str__dbq($str, $wrap = true, $mode = "free"){
    if(is_array($str)){
        foreach($str as $k=>$v){
            if(is_string($v)){
                $str[$k] = str_replace("'", "''", $v);
            }
            if($mode == "alphanumeric") $str[$k] = preg_replace("/[^a-zA-Z0-9]+/", "", $str[$k]);
            if($wrap) $str[$k] = "'".$str[$k]."'";
        }
        return $str;
    }
    if(is_string($str)) $str = str_replace("'", "''", $str);
    if($mode == "alphanumeric") $str = preg_replace("/[^a-zA-Z0-9]+/", "", $str);
    if($wrap) $str = "'".$str."'";
    return $str;
}