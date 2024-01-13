
<?php
include("init.php");
$user = f("cek_login")();
$telegramid = $user['telegram_id'];
f("webview._layout.base")("start");
$result = "";
if(!empty($_POST['id'])){
    $setTelegramId = $_POST['id'];
    $text = "Notifikasi berhasil diatur untuk user '".$user['username']."'";
    $telegram = f("bot.execute")("sendMessage",[
        'chat_id'=>$setTelegramId,
        'text'=>$text,
    ]);
    if(!empty($telegram['ok'])){
        //berhasil
        f("db.q")("update users set telegram_id='$setTelegramId' where id=".f("str.dbq")($user['id']));
        $telegramid = $setTelegramId;
        $result = "BERHASIL!";
    }
    else{
        $result = "GAGAL! Bot tidak bisa mengirim pesan kepada chat id $setTelegramId";
    }
}
?>
<h2>Set Telegram Notification</h2>
<form method="POST">
Telegram Chat ID: 
<input type='text' name='id' value='<?=$telegramid?>'/>
<input type='submit'/>
</form>
<div style='font-size: x-large; color:red; font-weight: bold;'><?=$result?></div>
<hr>
<div>
    <ul>
        <li>
            Cara mengatur notif untuk akun <strong>telegram pribadi</strong>:
            <ul>
                <li>blablabla @<?=f("get_config")("bot_username")?></li>
                <li>blablabla bla balb la bala @<?=f("get_config")("bot_username")?></li>
                <li><a target="_blank" href='https://t.me/getmyid_bot'>@getmyid_bot</a></li>
            </ul>
        </li>
        <li>
            Cara mengatur notif untuk <strong>group telegram</strong>:
            <ul>
                <li>blablabla @<?=f("get_config")("bot_username")?></li>
                <li>blablabla bla balb la bala @<?=f("get_config")("bot_username")?></li>
            </ul>
        </li>
        <li>
            Cara mengatur notif untuk <strong>channel telegram</strong>:
            <ul>
                <li>blablabla @<?=f("get_config")("bot_username")?></li>
                <li>blablabla bla balb la bala @<?=f("get_config")("bot_username")?></li>
            </ul>
        </li>
    </ul>
</div>
<?php
f("webview._layout.base")("end");