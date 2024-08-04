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
        if($strlen < 8){
            $err = "Jumlah karakter password tidak boleh kurang dari 8.";
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
f("webview._layout.base")("start",['title'=>'Ubah Password']);
?>
<div class="container mt-5">
    <h2 class="mb-4">Ubah Password</h2>
    <form method="post" action="?p=<?=urlencode($_GET['p'])?>" onsubmit="return validatePassword()">
        <div class="mb-3">
            <label class="form-label">User</label>
            <p><strong><?=$user['username']?></strong></p>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" placeholder="password" name="password" required />
            <small class="form-text text-muted">Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, dan angka</small>
        </div>
        <div class="mb-3">
            <label for="repassword" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="repassword" placeholder="konfirmasi password" name="repassword" required />
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<script>
    function validatePassword() {
        const password = document.getElementById('password').value;
        const repassword = document.getElementById('repassword').value;
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;

        if (!passwordPattern.test(password)) {
            alert('Password harus minimal 8 karakter dan mengandung huruf besar, huruf kecil, dan angka.');
            return false;
        }

        if (password !== repassword) {
            alert('Konfirmasi password harus sama dengan password.');
            return false;
        }

        return true;
    }
</script>
<?php
f("webview._layout.base")("end");