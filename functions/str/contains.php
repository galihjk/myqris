<?php
function str__contains($haystack, $needle){
    if((string)$needle === ""){
        return true;
    }
    return (strpos($haystack, $needle) !== false);
}