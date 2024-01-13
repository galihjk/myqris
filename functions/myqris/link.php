<?php
function myqris__link($user){
    return f("str.dataencrypt")([
        'u'=>$user['id'],
        't'=>$user['public'],
    ],true);
}