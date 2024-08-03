<?php
include("init.php");
$user = f("cek_login")();
if(!in_array($user['username'],f("get_config")("admins"))) dd("menu ini hanya untuk admin");
f("webview._layout.base")("start");
?>
<h1><img src="assets/img/logo1.jpg" style="width: 121px;"></h1>
<h2>Reset Password</h2>
<hr>
<form>Reset Password User <input type="text" placeholder="reset user_name" value="<?=$_GET['reset'] ?? ''?>" required name="reset"/> <input type="submit" value="GET RESET LINK!"/></form>
<hr>
<?php
if(!empty($_GET['reset'])){
    $user_reset = f("db.select_one")("select * from users where username=".f("str.dbq")($_GET['reset']));
    if(empty($user_reset['id'])){
        die("user '".$_GET['reset']."' tidak ditemukan");
    }
    $passwordurl = f("str.dataencrypt")(['u'=>$user_reset['id'],'p'=>md5($user_reset['password'])],true);
    $resetlink = "change_password.php?p=$passwordurl";
    ?>
    berikut link reset untuk <?=$_GET['reset']?>:<br>
    <a href='<?=$resetlink?>'>Reset Password</a>
    <hr>
    <?php
}
f("webview._layout.base")("exit");