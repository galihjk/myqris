<?php
function user__get($userid = "current"){
    if($userid == "current"){
        $userid = $_SESSION['user']['id'];
    }
    return f("db.select_one")("select * from users where id = ".f("str.dbq")($userid,true));
}