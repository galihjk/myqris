<?php
function str__dbtime($time = "now", $wrap = true){
    $time = date("Y-m-d H:i:s", strtotime($time));
    if($wrap) $time = "'".$time."'";
    return $time;
}