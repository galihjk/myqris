<?php 
include("init.php");
if(!empty($_POST)){
    $fail = false;
    if($_POST['password'] != $_POST['repassword']){
        $err = "Password konfirmasi tidak sama.";
        $fail = true;
    }
    if(!$fail){
        $strlen = strlen($_POST['username']??'');
        if($strlen < 4){
            $err = "Jumlah karakter username tidak boleh kurang dari 4.";
            $fail = true;
        }
        elseif($strlen > 15){
            $err = "Jumlah karakter username tidak boleh lebih dari 15.";
            $fail = true;
        }
    }
    if(!$fail){
        $strlen = strlen($_POST['password']??'');
        if($strlen < 12){
            $err = "Jumlah karakter password tidak boleh kurang dari 12.";
            $fail = true;
        }
    }
    if(!$fail){
        $_POST['username'] = strtolower($_POST['username']);
        if(!preg_match('/^[a-z0-9_]+$/',$_POST['username'])){
            $err = "Untuk username, simbol yang dibolehkan hanya underscore (_). Tidak boleh juga mengandung spasi.";
            $fail = true;
        }
    }
    if(!$fail){
        $exists = f("db.select_one")("select * from users where username = ".f("str.dbq")($_POST['username']));
        if(!empty($exists)){
            $err = "Username ini sudah ada, gunakan yang lain";
            $fail = true;
        }
    }
    if($fail){
        f("webview._layout.base")("start");
        ?>
        <h2>Perhatian</h2>
        <?=$err?>
        <hr>
        <a href='#' onclick='history.back()'>OK</a>
        <?php
        f("webview._layout.base")("exit");
    }
    f("db.q")("insert into users (username, password) values (".f("str.dbq")($_POST['username']).",".f("str.dbq")(password_hash($_POST['password'],PASSWORD_DEFAULT)).")");
    $whatsaptext = "Halo, admin. Saya ingin memverifikasi akun saya: \"".$_POST['username']."\". \nMohon informasi selanjutnya.";
    $whatsaptext_encode = urlencode($whatsaptext);
    $phoneNum = f("get_config")("no_wa_admin");
    $linkwhatsapp = "https://api.whatsapp.com/send/?phone=$phoneNum&text=$whatsaptext_encode&type=phone_number&app_absent=0";
    f("webview._layout.base")("start");
    ?>
    <h3>Lakukan Verifikasi</h3>
    <div>
        Pendaftaran berhasil, silakan lakukan verifikasi melalui whatsapp berikut:
    </div>
    <div>
        <a target='_blank' href='<?=$linkwhatsapp?>'><?=$linkwhatsapp?></a>
    </div>
    <div>
        <hr>
        <a href='login.php'>Kembali ke halaman login.</a>
    </div>
    <?php
    f("webview._layout.base")("exit");
}
f("webview._layout.base")("start");
?>
<h2>Registrasi</h2>
<form method="post">
    <div>
        username 
        <input style="text-transform: lowercase;" type="text" placeholder="username" name="username" required /> simbol yang boleh digunakan hanya underscore (_), minimal 4 karakter, maksimal 15 karakter. Tidak mengandung spasi.
    </div>
    <div>
        password <input type="password" placeholder="password" name="password" required /> minimal 12 karakter
    </div>
    <div>
        konfirmasi password <input type="password" placeholder="konfirmasi password" name="repassword" required />
    </div>
    <div>
        data diri lainnya... (underconstruction)
        <input type="text" />
    </div>
    <div>
        CAPTCHA <input type="text" placeholder="CAPTCHA" name="CAPTCHA "/> (underconst, bebas aja)
    </div>
    <input type="submit" />
</form>
<?php
f("webview._layout.base")("end");