<?php
function db__q($q){
    $mysqli = f("db.connect")();
    $data = [];
    // echo "<pre>";
    // print_r($mysqli);
    // print_r($q);
    // print_r($mysqli -> query($q));
    // echo "</pre>";
    if ($result = $mysqli -> query($q)) {
        if(is_object($result) and method_exists($result,"fetch_array")){
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $data[] = $row;
            }
            $result -> free_result();            
        }
        else{
            $data = $result;
        }
    }
    else{
        file_put_contents("log/ERROR DBQ ".date("YmdHis").".txt","$q\n\n".$mysqli->error);
    }
    return $data;
}