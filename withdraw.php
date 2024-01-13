<?php
include("init.php");
$user = f("cek_login")();
f("webview._layout.base")("start");
?>
<h2>Withdraw</h2>
<?php
$data = f("data.load")("wd/".$user['id']);
if($data){
    $biaya_withdraw = f("get_config")("biaya_withdraw")[$data['m']];
    $biaya_withdraw_number_format = number_format($biaya_withdraw,2,',','.');
    $phoneNum = f("get_config")("no_wa_admin");
    $total = $data['a'] + $biaya_withdraw;
    ?>
    <h4>Silakan lanjutkan dengan mengontak administator</h4>
    <div>
        Withdraw: <strong><?=number_format($data['a'],2,',','.')?></strong>
        <br>
        Metode: <strong><?=$data['m']?></strong>
        <br>
        Biaya Admin: <strong><?=$biaya_withdraw_number_format?></strong>
        <br>
        Total: <strong><?=number_format($total,2,',','.')?></strong>
        <br>
        Saldo: <strong><?=number_format($user['saldo'],2,',','.')?></strong>
        <br>
        Sisa Saldo: <strong><?=number_format($user['saldo']-$total,2,',','.')?></strong>
        <br><br>
        <?php
        $whatsaptext = "Halo, admin. Saya ingin mengkonfirmasi withdraw untuk"
        ."\nuser: ".$user['username']
        ."\nsejumlah: ".number_format($data['a'],2,',','.')
        ."\nmetode: ".$data['m']
        ." (biaya: $biaya_withdraw_number_format)"
        ."\ntotal: ".number_format($total,2,',','.')
        ."\nMohon diproses.";
        $whatsaptext_encode = urlencode($whatsaptext);
        $linkwhatsapp = "https://api.whatsapp.com/send/?phone=$phoneNum&text=$whatsaptext_encode&type=phone_number&app_absent=0";
        ?>
        <a target='_blank' href='<?=$linkwhatsapp?>'>Konfirmasi</a><br><br>
        <?php
        $whatsaptext = "Halo, admin. Saya ingin membatalkan withdraw untuk user: ".$user['username'];
        $whatsaptext_encode = urlencode($whatsaptext);
        $linkwhatsapp = "https://api.whatsapp.com/send/?phone=$phoneNum&text=$whatsaptext_encode&type=phone_number&app_absent=0";
        ?>
        <a target='_blank' href='<?=$linkwhatsapp?>'>Batalkan</a><br><br>
        <?php
        $whatsaptext = "Halo, admin. Saya ingin mengubah withdraw untuk"
        ."\nuser: ".$user['username']
        ."\n\nSEMULA:\nnilai: ".number_format($data['a'],2,',','.')
        ."\nmetode: ".$data['m']
        ." (biaya: $biaya_withdraw_number_format)"
        ."\ntotal: ".number_format($total,2,',','.')
        ."\n\nMENJADI:\n................";
        $whatsaptext_encode = urlencode($whatsaptext);
        $linkwhatsapp = "https://api.whatsapp.com/send/?phone=$phoneNum&text=$whatsaptext_encode&type=phone_number&app_absent=0";
        ?>
        <a target='_blank' href='<?=$linkwhatsapp?>'>Ubah</a><br><br>
    </div>
    <?php
    f("webview._layout.base")("exit");
}
elseif(!empty($_POST)){
    if($_POST['nilai'] <= 0){
        ?>
        <script>
            alert("Nilai minimal 1");
            history.back();
        </script>
        <?php
        exit();
    }
    f("data.save")("wd/".$user['id'],[
        'a'=>$_POST['nilai'],
        'm'=>$_POST['metode'],
    ]);
    ?>
    <script>
        window.location.replace("withdraw.php");
    </script>
    <?php
}
else{
    if(!empty($_GET['metode'])){
        $metode_dipilih = $_GET['metode'];
    }
    else{
        $metode_dipilih = array_keys(f("get_config")("biaya_withdraw"))[0];
    }
    $biaya_withdraw = f("get_config")("biaya_withdraw")[$metode_dipilih];
    ?>
    <form method="POST">
        Saldo Anda: <strong><?=number_format($user['saldo'],2,',','.')?></strong><br>
        Withdraw: <input id="nilai" value="<?=$_GET['nilai'] ?? ''?>" name="nilai" type="number" required max="<?=$user['saldo'] - $biaya_withdraw?>" placeholder="Mau ambil berapa?"/><br>
        Metode: <select id="metode" name="metode" onchange="pilihMetode()">
            <?php foreach(array_keys(f("get_config")("biaya_withdraw")) as $item){
                ?>
                <option value="<?=$item?>" <?=($metode_dipilih == $item ? 'selected' : '')?>><?=$item?></option>
                <?php
            }
            ?>
        </select><br>
        Biaya Admin: <strong><?=number_format($biaya_withdraw,2,',','.')?></strong><br>
        <input id="submit" type="submit" />
    </form>
    <script>
        function pilihMetode(){
            var url = "withdraw.php";
            url += "?nilai="+document.getElementById("nilai").value;
            url += "&metode="+document.getElementById("metode").value;
            window.location.replace(url);
        }
    </script>
    <?php
}
f("webview._layout.base")("end");