<?php
function bot__execute($method, $data){
    $token = f("get_config")("bot_token");
    $url = "https://api.telegram.org/bot$token/$method";
    $url .= "?".http_build_query($data);
    return json_decode(@file_get_contents($url),true);
}
    