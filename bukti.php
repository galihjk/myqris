<?php
include("init.php");
if(empty($_GET['code'])){
    die("no code");
}
$q = "select * from histori where kode=".f("str.dbq")($_GET['code'],true,"alphanumeric");
$data = f("db.select_one")($q);
if(empty($data)){
    dd("invalid code: ".$_GET['code']);
}
f("webview._layout.base")("start");
// dump($data);
if($data['jenis'] == "topup"){
    $username_pengirim = json_decode($data['topup_info'],true);
    $user_pengirim = [
        'username'=>$username_pengirim['qris_payment_customername'] . " (" . $username_pengirim['qris_payment_methodby'] . ")"
    ];
}
else{
    $user_pengirim = f("db.select_one")("select username from users where id=".$data['id_pengirim']);
}
if($data['jenis'] == "withdraw"){
    $user_penerima = [
        'username'=>"<span style='color: red;'>Withdrawal</span>",
    ];
}
elseif($data['jenis'] == "biaya_admin"){
    $user_penerima = [
        'username'=>"<span style='color: red;'>Biaya Admin</span>",
    ];
}
else{
    $user_penerima = f("db.select_one")("select username from users where id=".$data['id_penerima']);
}
?>
<h2>Transaksi Berhasil</h2>
<div>
    <table>
        <tr>
            <td><strong>Dari</strong></td>
            <td> &nbsp; : &nbsp; </td>
            <td><?=$user_pengirim['username']?></td>
        </tr>
        <tr>
            <td><strong>Kepada</strong></td>
            <td> &nbsp; : &nbsp; </td>
            <td><?=$user_penerima['username']?></td>
        </tr>
        <tr>
            <td><strong>Sejumlah</strong></td>
            <td> &nbsp; : &nbsp; </td>
            <td><?=number_format($data['nilai_transaksi'],2,',','.')?></td>
        </tr>
        <tr>
            <td><strong>Pada</strong></td>
            <td> &nbsp; : &nbsp; </td>
            <td><?=$data['tanggal']?></td>
        </tr>
        <tr>
            <td><strong>Kode</strong></td>
            <td> &nbsp; : &nbsp; </td>
            <td><input type="text" readonly value="<?=$data['kode']?>" onClick="this.select();"/></td>
        </tr>
        <?php
        if(!empty($data['keterangan'])){
            ?>
            <tr>
                <td><strong>Keterangan</strong></td>
                <td> &nbsp; : &nbsp; </td>
                <td>
                    <textarea disabled><?=str_replace('"','&quot;',$data['keterangan'])?></textarea>
                </td>
            </tr>
            <?php
        }
        if($data['jenis'] == "withdraw"){
            ?>
            <tr>
                <td><strong>Biaya Admin</strong></td>
                <td> &nbsp; : &nbsp; </td>
                <td>
                    <a href="bukti.php?code=A<?=substr($_GET['code'],1)?>">Lihat</a>
                </td>
            </tr>
            <?php
        }
        if($data['jenis'] == "biaya_admin"){
            ?>
            <tr>
                <td><strong>Withdrawal</strong></td>
                <td> &nbsp; : &nbsp; </td>
                <td>
                    <a href="bukti.php?code=W<?=substr($_GET['code'],1)?>">Lihat</a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>
<br>
Link Bukti:<br>
<textarea style="width:99%" readonly id="trxurl" onClick="this.select();"></textarea>
<script>
    document.getElementById("trxurl").innerHTML = window.location.href;
</script>
<?php
if(!empty($_SESSION['user']['id'])){
    $username = $_SESSION['user']['username'];
    $usersaldo = f("db.select_one")("select saldo from users where id=".f("str.dbq")($_SESSION['user']['id']));
    if(!empty($usersaldo)){
        ?>
        <br><br><hr><br>
        Saldo <?=$username?>: <?=number_format($usersaldo['saldo'],2,',','.')?>
        <?php
    }
}
?>
<?php
f("webview._layout.base")("end");