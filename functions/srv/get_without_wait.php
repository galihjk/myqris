<?php
    function srv__get_without_wait($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        // echo "\nurl=$url\n";
        return $response;
    }
    