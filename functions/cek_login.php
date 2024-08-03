<?php
function cek_login(){
    if(empty($_SESSION['user'])){
        $location = "login.php";
        if(!empty($_GET)){
            $location .= "?".http_build_query($_GET);
        }
        header("Location: $location");
        exit();
    }
    if($_SESSION['user']['id'] == 'sys'){
        $userdata = $_SESSION['user'];
    }
    else{
        $q = "select * from users where 
        id=".f("str.dbq")($_SESSION['user']['id']);
        $userdata = f("db.select_one")($q);        
    }
    return $userdata;
}
    