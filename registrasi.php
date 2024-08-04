<?php 
include("init.php");
if(!empty($_POST)){
    $fail = false;
    if (strtoupper($_POST['captcha_input']) != $_SESSION['captcha_text']) {
        $err = "Captcha tidak valid. Silakan coba lagi. Pastikan Anda memasukkan teks yang Anda lihat di gambar dengan benar.".$_SESSION['captcha_text'];
        $fail = true;
    }
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
        if($strlen < 8){
            $err = "Jumlah karakter password tidak boleh kurang dari 8.";
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
        //sys admin user
        if($_POST['username'] == strtolower(f("get_config")("sysadmin_user",""))){
            $err = "Username ini sudah ada, gunakan yang lain";
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
        Pendaftaran berhasil, silakan <a target='_blank' href='<?=$linkwhatsapp?>'>lakukan verifikasi</a>
    </div>
    <div>
        <hr>
        <a href='login.php'>Kembali ke halaman login.</a>
    </div>
    <?php
    f("webview._layout.base")("exit");
}

f("webview._layout.base")("start",['title'=>'Registrasi']);
?>
<script>
    function validateForm() {
        var password = document.getElementById("password").value;
        var repassword = document.getElementById("repassword").value;
        if (password !== repassword) {
            alert("Password dan Konfirmasi Password tidak sama.");
            return false;
        }
        return true;
    }
</script>
<h2>Registrasi</h2>
<div class="container mt-5 text-start">
    <form method="post" onsubmit="return validateForm()">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="username" style="text-transform: lowercase;" required 
                    pattern="^[a-z0-9_]{4,15}$" title="Simbol yang boleh digunakan hanya underscore (_), minimal 4 karakter, maksimal 15 karakter. Tidak mengandung spasi.">
            <div class="form-text">Simbol yang boleh digunakan hanya underscore (_), minimal 4 karakter, maksimal 15 karakter. Tidak mengandung spasi.</div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="password" required 
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, dan angka.">
            <div class="form-text">Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, dan angka.</div>
        </div>
        <div class="mb-3">
            <label for="repassword" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="repassword" name="repassword" placeholder="konfirmasi password" required>
        </div>
        <div class="mb-3">
            <img src="captcha.php" alt="CAPTCHA" class="mb-2">
            <label for="captcha_input" class="form-label">Masukkan teks yang Anda lihat di gambar</label>
            <input type="text" class="form-control" id="captcha_input" name="captcha_input" style="text-transform: uppercase;" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<?php
f("webview._layout.base")("end");