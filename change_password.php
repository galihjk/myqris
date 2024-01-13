<?php
if(empty($_GET['p'])) exit();
include("init.php");
$data_p = f("str.datadecrypt")($_GET['p']);
if(empty($data_p['u'])){
    die("invalid");
}
$user = f("db.select_one")("select * from users where id = ".f("str.dbq")($data_p['u']));
if(empty($user['password'])){
    die("invalid.");
}
if(empty($data_p['p'])){
    die("invalid..");
}
if(md5($user['password']) != $data_p['p']){
    die("invalid...");
};
if(!empty($_POST)){
    $fail = false;
    if($_POST['password'] != $_POST['repassword']){
        $err = "Password konfirmasi tidak sama.";
        $fail = true;
    }
    if(!$fail){
        $strlen = strlen($_POST['password']??'');
        if($strlen < 12){
            $err = "Jumlah karakter password tidak boleh kurang dari 12.";
            $fail = true;
        }
    }
    if($fail){
        f("webview._layout.base")("start");
        ?>
        <h2>Gagal</h2>
        <?=$err?>
        <hr>
        <a href='#' onclick='history.back()'>OK</a>
        <?php
        f("webview._layout.base")("exit");
    }
    f("db.q")("update users set 
    password = ".f("str.dbq")(password_hash($_POST['password'],PASSWORD_DEFAULT))
    ." where id=".f("str.dbq")($data_p['u']));
    f("webview._layout.base")("start");
    ?>
    <h2>Ubah Password</h2>
    <h3>Berhasil</h3>
    <div>
        <a href='index.php'>OK</a>
    </div>
    <?php
    f("webview._layout.base")("exit");
}
f("webview._layout.base")("start");
?>
<h2>Ubah Password</h2>
<form method="post" action="?p=<?=urlencode($_GET['p'])?>">
    <div>
        user <strong><?=$user['username']?></strong>
    </div>
    <div>
        password <input type="password" placeholder="password" name="password" required /> minimal 12 karakter
    </div>
    <div>
        konfirmasi password <input type="password" placeholder="konfirmasi password" name="repassword" required />
    </div>
    <input type="submit" />
</form>
<?php
f("webview._layout.base")("end");