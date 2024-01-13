<?php
include("init.php");
f("webview._layout.base")("start");
$whatsaptext = "Halo, admin. Saya lupa password saya, mohon bantuannya.";
$whatsaptext_encode = urlencode($whatsaptext);
$phoneNum = f("get_config")("no_wa_admin");
$linkwhatsapp = "https://api.whatsapp.com/send/?phone=$phoneNum&text=$whatsaptext_encode&type=phone_number&app_absent=0";
?>
Silakan <a target='_blank' href='<?=$linkwhatsapp?>'>hubungi admin.</a><br><br>
<a href='index.php'>OK</a>
<?php
f("webview._layout.base")("end");