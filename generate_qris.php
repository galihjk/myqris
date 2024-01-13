<?php
include("init.php");
$user = f("cek_login")();
if(!empty($_POST)){
    $last_check_status = f("data.load")("last_check_status",0);
    $current_time = time();
    if($current_time < $last_check_status+6){
        $sisa_detik = 1+$last_check_status+6 - $current_time;
        dump("Tunggu $sisa_detik detik lalu submit kembali.");
    }
    else{
        f("data.save")("last_check_status",time());
        $qrismake = f("qris.make")($_POST['nilai'],$user['id'],$_POST['keterangan']);
        header("Location: my_qris.php?trx=".$qrismake['cliTrxNumber']);
        exit();
    }
}
f("webview._layout.base")("start");
?>
<h2>Generate Qris</h2>
<form method="POST">
Nilai:<br>
<input name="nilai" type="number" required min="1" placeholder="Masukan nilai nominal"/><br>
<br>
Keterangan:<br>
<textarea name="keterangan"></textarea><br>
<br>
<input type="submit" />
</form>
<?php
f("webview._layout.base")("end");