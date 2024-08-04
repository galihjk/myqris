<?php
include("init.php");
if(empty($_GET['u'])){
    $user = f("cek_login")();
    $data_u = ['u'=>$user['id']];
    $_GET['u'] = "";
}
else{
    $data_u = f("str.datadecrypt")($_GET['u']);
    if(empty($data_u['u'])){
        die("invalid");
    }
    if(empty($data_u['t'])){
        die("invalid.");
    }
    $q = "select * from users where 
    id=".f("str.dbq")($data_u['u']);
    $user = f("db.select_one")($q);
    if(empty($user)){
        die("invalid..");
    }
    if($data_u['t'] !== $user['public']){
        die("invalid...");
    }
}

// print_r($data);
if(!empty($_POST)){
    $last_check_status = f("data.load")("last_check_status",0);
    $current_time = time();
    if($current_time < $last_check_status+6){
        $sisa_detik = 1+$last_check_status+6 - $current_time;
        dump("Tunggu $sisa_detik detik lalu submit kembali.");
        ?>
        <form method="POST" action="?u=<?=urlencode($_GET['u'])?>">
            User:<br>
            <strong><?=$user['username']?></strong><br>
            <br>
            Nilai:<br>
            <input name="nilai" type="number" required min="1" value="<?=$_POST['nilai']?>"/><br>
            <br>
            Keterangan:<br>
            <textarea name="keterangan"><?=$_POST['keterangan']?></textarea><br>
            <br>
            <input type="submit" />
        </form>
        <?php
        exit();
    }
    else{
        f("data.save")("last_check_status",time());
        $qrismake = f("qris.make")($_POST['nilai'],$data_u['u'],$_POST['keterangan']);
        header("Location: ?u=".urlencode($_GET['u'])."&trx=".$qrismake['cliTrxNumber']);
        exit();
    }
}
if(!empty($_GET['trx'])){
    f("webview._layout.base")("start");
    $cliTrxNumber = $_GET['trx'];
    $data = f("data.load")("trx/".$cliTrxNumber);
    if(!$data){
        dd("ERROR: Transaksi '".$cliTrxNumber."' tidak valid.");
    }
    $qrcodeimg = f("qr.png")($data['qris_content']);
    ?>
    <div style="height: 9vh; text-align:center">
            <img style="height: 100%;" src='img/qrislogo.png' />
        </div>
        <div style="height: 60vh; text-align:center">
            <a style="height: 100%;" href="<?= $qrcodeimg ?>" download >
                <img style="height: 100%;" src="<?= $qrcodeimg ?>" />
            </a>
        </div>
        <div style="text-align:center">
            <div style="text-align:center; color:lightgray; font-style: italic;">
                <small>*Klik QR Code di atas untuk mengunduhnya.</small><br>
            </div>
            <iframe src="job_check.php?u=<?=urlencode($_GET['u'])?>&id=<?=$data['cliTrxNumber']?>" style="width: 0px; height: 0px"></iframe>
            <a style='font-size: x-large;' href='check.php?u=<?=urlencode($_GET['u'])?>&id=<?=$data['cliTrxNumber']?>'>
                [CEK PEMBAYARAN]
            </a>
            <br><br>
            NMID: <?=$data['qris_nmid']?><br>
            <?=f("get_config")("NamaMerchant")?><br>
            User: <?=$user['username']?><br>
            Nominal: <?=number_format($data['cliTrxAmount'],2,',','.')?><br>
            <?php if(!empty($data['k'])){
                ?>
                Keterangan:<br><pre><?=$data['k']?></pre><br>
                <?php
            } ?>
            <?php
            if(empty($_SESSION['user'])){
                ?>
                <br><br>
                <a href='index.php' target='_blank'>Generate My Own Qris</a>
                <?php
            }
            $whatsaptext = "Halo, admin. Saya butuh bantuan. ".$data['cliTrxNumber'];
            $whatsaptext_encode = urlencode($whatsaptext);
            $phoneNum = f("get_config")("no_wa_admin");
            $linkwhatsapp = "https://api.whatsapp.com/send/?phone=$phoneNum&text=$whatsaptext_encode&type=phone_number&app_absent=0";
            ?>
            <br><br>
            <a href='<?=$linkwhatsapp?>' target='_blank'>Kontak WhatsApp Admin</a>
        </div>
    <?php
    f("webview._layout.base")("exit");
}
f("webview._layout.base")("start");
?>
<div class="container mt-4">
    <h1 class="text-center">
        <img src="assets/img/logo1.jpg" class="img-fluid" style="max-width: 121px;" alt="Logo">
    </h1>
    <hr>
    <div class="text-center">
        <form id="myForm" method="POST" action="?u=<?= urlencode($_GET['u']) ?>">
            <div class="mb-3">
                <label for="username" class="form-label">User:</label>
                <strong id="username"><?=$user['username']?></strong>
            </div>
            <div class="mb-3">
                <label for="nilai" class="form-label">Nilai:</label>
                <input id="nilai" name="nilai" type="number" class="form-control" required min="1"
                    <?=(!empty($_GET['n']) ? 'value="'.htmlspecialchars($_GET['n']).'"' : '')?>>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan:</label>
                <textarea id="keterangan" name="keterangan" class="form-control"><?=(!empty($_GET['k']) ? htmlspecialchars($_GET['k']) : '')?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Buat QRIS</button>
            <div class="text-danger mt-3">
                <i class="fa fa-exclamation-triangle"></i>
                Setelah transaksi QRIS berhasil, klik <strong>[CEK PEMBAYARAN]</strong>
            </div>
        </form>
    </div>
</div>
<?php
if(!empty($_GET['n'])){
    ?>
    <script>
        document.getElementById("myForm").submit();
    </script>
    <?php
}
f("webview._layout.base")("exit");
?>