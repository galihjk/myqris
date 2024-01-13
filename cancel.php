<?php
include("init.php");
if(!empty($_GET['trx'])){
    $data = f("data.load")("trx/".$_GET['trx']);
    if($data){
        if((time()-$data['t'] < 30*60)){
            $text = "Transaksi dibatalkan sebelum expired\n";
            $text.= substr(print_r($data,1),6);
            $telegram = f("bot.execute")("sendMessage",[
                'chat_id'=>f("get_config")("trx_log_chat_id"),
                'text'=>$text,
            ]);
        }
        f("data.delete")("trx/".$_GET['trx']);
        if(!empty($_GET['admin'])){
            header("Location: check_unpaid_all.php");
            exit();
        }
    }
    f("webview._layout.base")("start");
    ?>
    <h3>Transaksi dibatalkan</h3>
    <br>
    <a href='check_unpaid.php'>List Unpaid Qris</a>
    <?php
    f("webview._layout.base")("exit");
}
header("Location: index.php");