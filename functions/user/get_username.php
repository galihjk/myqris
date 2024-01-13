<?php
$GLOBALS['usernames'] = [];
function user__get_username($userid){
    if(!empty($GLOBALS['usernames'][$userid])){
        return $GLOBALS['usernames'][$userid];
    }
    else{
        $data = f("db.select_one")("select username from users where id=$userid");
        if($data){
            $GLOBALS['usernames'][$userid] = $data['username'];
            return $data['username'];
        }
    }
    return "";
}
    