<?php
include("init.php");
$user = f("cek_login")();
f("webview._layout.base")("start",['body_class'=>'bg-warning']);
?>
<div class="bg-dark text-warning p-2 rounded mb-4">
    <div class="bg-white rounded p-2 float-start">
        <img src="assets/img/logo1.jpg" style="width: 140px;">
    </div>
    <div class="text-end m-1">
        <?=$user['username']?>
        <?=($user['id'] == "sys"?" (System Administrator)":"")?>
        <br>
        <a class="btn btn-sm btn-secondary" href='logout.php'>
            <i class="fa fa-power-off"></i>
            Logout
        </a>
    </div>
</div>
<?php
if(!empty($user['banned_at'])){
    ?>
    User anda banned. Silakan hubungi admin.
    <?php
    f("webview._layout.base")("exit"); // exit();
}
if(in_array($user['username'],f("get_config")("admins"))){
    ?>
    <div>
        <h3 class="mb-3">Admin</h3>
        <div>
            <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='verifikasi_user.php'>Verifikasi User</a>
        </div>
        <div>
            <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='ban_unban.php'>Ban dan Unban</a>
        </div>
        <div>
            <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='acc_withdraw.php'>ACC Withdraw</a>
        </div>
        <div>
            <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='reset_password.php'>Reset Password</a>
        </div>
        <div>
            <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='check_unpaid_all.php'>List All Unpaid Qris</a>
        </div>
        <hr>
    </div>
    <?php
}
if(empty($user['verified_at'])){
    if($user['id'] != 'sys'){
        $whatsaptext = "Halo, admin. Saya ingin memverifikasi akun saya: \"".$user['username']."\". \nMohon informasi selanjutnya.";
        $whatsaptext_encode = urlencode($whatsaptext);
        $phoneNum = f("get_config")("no_wa_admin");
        $linkwhatsapp = "https://api.whatsapp.com/send/?phone=$phoneNum&text=$whatsaptext_encode&type=phone_number&app_absent=0";
        ?>
        <div>
            User anda belum diaktivasi, silakan <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='<?=$linkwhatsapp?>'>lakukan verifikasi</a>
        </div>
        <hr>
        <?php
    }
    ?>
    <?php
    f("webview._layout.base")("exit"); // exit();
}
?>
<div>
    <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='generate_qris.php'>Generate Qris</a>
</div>
<div>
    <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='check_unpaid.php'>List Unpaid Qris</a>
</div>
<div>
    <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='kirim.php'>Kirim ke sesama pengguna</a>
</div>
<div>
    <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='withdraw.php'>Withdraw</a>
</div>
<div class="bg-success p-1 mb-1 rounded text-white">
    Saldo Anda: <strong>Rp<?=number_format($user['saldo'])?></strong>
</div>
<div class="bg-light rounded px-2 py-3">
    Payment Link: <strong>[<?=($user['public'] ? "<a href='my_qris.php?u=".f("myqris.link")($user)."' onclick='linkShow(event);'>LINK</a>" : '❌ DISABLED')?>]</strong><br>
    <script>
        function linkShow(e){
            e.preventDefault();
            alert("Silakan salin alamat dari link ini (Copy link address).");
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
        <a class="btn btn-dark border-secondary mb-2 w-100" href='public_link.php?set=generate'>Buat public link</a>
        <?php
    }
    ?>
</div>
<div class="mt-2">
    <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='histori.php'>Histori Transaksi</a>
</div>
<div>
    <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='set_telegram_notif.php'>Set telegram notification</a>
</div>
<div>
    <?php
    $passwordurl = f("str.dataencrypt")([
        'u'=>$user['id'],
        'p'=>md5($user['password'])
    ], true);
    ?>
    <a target='_blank' class="btn btn-dark border-secondary mb-2 w-100" href='change_password.php?p=<?=$passwordurl?>'>Change Password</a>
</div>
<?php
f("webview._layout.base")("exit"); // exit();
