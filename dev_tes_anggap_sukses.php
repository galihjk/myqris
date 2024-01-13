<?php
include("init.php");
$user = f("cek_login")();
if(!in_array($user['username'],f("get_config")("admins"))) dd("menu ini hanya untuk admin");
if(empty($_GET['trx'])){
    die("empty trx");
}
$data = f("data.load")("trx/".$_GET['trx']);
if(empty($data['u'])){
    die("empty data trx");
}
dump($data);
$trxuser = f("db.select_one")("select * from users where id=".f("str.dbq")($data['u']));
if(empty($trxuser['id'])){
    die("empty trx user");
}
dump($trxuser);
$saldo = $trxuser['saldo'];
$cliTrxAmount = $data['cliTrxAmount'];
$saldo += $cliTrxAmount;
f("db.q")("update users set saldo = $saldo where id=".f("str.dbq")($data['u']));
$text = "TOPUP #u_".$trxuser['id']." sebesar $cliTrxAmount. saldo: $saldo.";
$telegram = f("bot.execute")("sendMessage",[
    'chat_id'=>f("get_config")("trx_log_chat_id"),
    'text'=>$text,
]);
// dump($telegram);
f("data.delete")("trx/".$_GET['trx']);