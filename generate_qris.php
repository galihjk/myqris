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
<div class="container mt-5">
    <h2 class="mb-4">Generate Qris</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nilai" class="form-label">Nilai:</label>
            <input id="nilai" name="nilai" type="number" class="form-control" required min="1" placeholder="Masukkan nilai nominal">
        </div>
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan:</label>
            <textarea id="keterangan" name="keterangan" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <div class="text-danger mt-3">
            <i class="fa fa-exclamation-triangle"></i>
            Setelah transaksi QRIS berhasil, klik <strong>[CEK PEMBAYARAN]</strong>
        </div>
    </form>
</div>
<?php
f("webview._layout.base")("end");