<h1>My Qris (prototype 2)</h1>
<hr>
<?php
include("init.php");
$user = f("cek_login")();
f("webview._layout.base")("start");
echo $user['username']; echo "<hr><br>";
if(!empty($user['banned_at'])){
    ?>
    User anda banned. Silakan hubungi admin.
    <div>
        <a href='logout.php'>Logout</a><br><br>
    </div>
    <?php
    f("webview._layout.base")("exit"); // exit();
}
if(in_array($user['username'],f("get_config")("admins"))){
    ?>
    <div>
        <h3>Admin</h3>
        <div>
            <a target='_blank' href='verifikasi_user.php'>Verifikasi User</a><br><br>
        </div>
        <div>
            <a target='_blank' href='ban_unban.php'>Ban dan Unban</a><br><br>
        </div>
        <div>
            <a target='_blank' href='acc_withdraw.php'>ACC Withdraw</a><br><br>
        </div>
        <div>
            <a target='_blank' href='reset_password.php'>Reset Password</a><br><br>
        </div>
        <div>
            <a target='_blank' href='check_unpaid_all.php'>List All Unpaid Qris</a><br><br>
        </div>
        <hr><br>
    </div>
    <?php
}
if(empty($user['verified_at'])){
    $whatsaptext = "Halo, admin. Saya ingin memverifikasi akun saya: \"".$user['username']."\". \nMohon informasi selanjutnya.";
    $whatsaptext_encode = urlencode($whatsaptext);
    $phoneNum = f("get_config")("no_wa_admin");
    $linkwhatsapp = "https://api.whatsapp.com/send/?phone=$phoneNum&text=$whatsaptext_encode&type=phone_number&app_absent=0";
    ?>
    <div>
        User anda belum diaktivasi, silakan lakukan verifikasi melalui whatsapp berikut:
    </div>
    <div>
        <a target='_blank' href='<?=$linkwhatsapp?>'><?=$linkwhatsapp?></a>
    </div>
    <hr>
    <div>
        <a href='logout.php'>Logout</a><br><br>
    </div>
    <?php
    f("webview._layout.base")("exit"); // exit();
}
?>
<div>
    <a target='_blank' href='generate_qris.php'>Generate Qris</a><br><br>
</div>
<div>
    <a target='_blank' href='check_unpaid.php'>List Unpaid Qris</a><br><br>
</div>
<div>
    <a target='_blank' href='kirim.php'>Kirim ke sesama pengguna</a><br><br>
</div>
<div>
    <a target='_blank' href='withdraw.php'>Withdraw</a><br><br>
</div>
<div>
    Saldo Anda: <strong>Rp<?=number_format($user['saldo'])?></strong><br><br>
</div>
<div>
    Payment Link: <strong>[<?=($user['public'] ? "<a href='my_qris.php?u=".f("myqris.link")($user)."' onclick='linkShow(event);'>LINK</a>" : '❌ DISABLED')?>]</strong><br>
    <script>
        function linkShow(e){
            e.preventDefault();
            alert("You can copy this link address and share it to anyone");
        }
    </script>
    <?php
    if($user['public']){
        ?>
        <form method="GET" action="public_link.php">
            Buat link dengan nominal: 
            <input type="number" name="with_nominal" placeholder="nominal" value="1000" />
            <input type="text" name="keterangan" placeholder="keterangan"  />
            <input type="submit" value="Buat Link" /><br>
            <a href='public_link.php?set=remove'>Hapus link</a><br>
            <a href='public_link.php?set=generate'>Ubah (buat ulang) link</a>
        </form>
        <?php
    }
    else{
        ?>
        <a href='public_link.php?set=generate'>Buat public link</a>
        <?php
    }
    ?>
    <br>
</div>
<div>
    <a target='_blank' href='histori.php'>Histori Transaksi</a><br><br>
</div>
<hr><br>
<div>
    <a target='_blank' href='set_telegram_notif.php'>Set telegram notification</a><br><br>
</div>
<div>
    <?php
    $passwordurl = f("str.dataencrypt")([
        'u'=>$user['id'],
        'p'=>md5($user['password'])
    ], true);
    ?>
    <a target='_blank' href='change_password.php?p=<?=$passwordurl?>'>Change Password</a><br><br>
</div>
<div>
    <a href='logout.php'>Logout</a><br><br>
</div>
<?php
f("webview._layout.base")("exit"); // exit();
