<?php
include("init.php");
f("webview._layout.base")("start");
if(empty($_GET['id'])){
    dd("ERROR: empty id");
}
$last_check_status = f("data.load")("last_check_status",0);
$current_time = time();
if($current_time < $last_check_status+6){
    $sisa_detik = 1+$last_check_status+6 - $current_time;
    dd("Tunggu $sisa_detik detik lalu refresh.");
}
$data = f("data.load")("trx/".$_GET['id']);
if(!$data){
    dd("ERROR: Transaksi '".$_GET['id']."' tidak valid.");
}
f("data.save")("last_check_status",time());
$api_param = [
    'do'=>'checkStatus',
    'apikey'=>f("get_config")("APIKEY"),
    'mID'=>f("get_config")("mID"),
    'invid'=>$data['qris_invoiceid'],
    'trxvalue'=>$data['cliTrxAmount'],
    'trxdate'=>date("Y-m-d"),
];
$api_url = "https://qris.online/restapi/qris/checkpaid_qris.php";
$api_q_param = http_build_query($api_param);
// dump("$api_url?$api_q_param");
$data_api = json_decode(file_get_contents("$api_url?$api_q_param"),true);
// dump($data_api);
if(!empty($data_api['status']) and $data_api['status'] == "success"){
    $data_api_data = $data_api['data'];
    $trxuser = f("db.select_one")("select * from users where id=".f("str.dbq")($data['u']));
    if(empty($trxuser['id'])){
        dump([$data, $data_api]);
        die("ERROR: empty trx user");
    }
    // dump($trxuser);
    $saldo_awal = $trxuser['saldo'];
    $saldo = $saldo_awal;
    $cliTrxAmount = $data['cliTrxAmount'];
    $saldo += $cliTrxAmount;
    f("db.q")("update users set saldo = $saldo where id=".f("str.dbq")($data['u']));
    f("data.delete")("trx/".$_GET['id']);
    $randomcode = "Q".md5(date("YmdHis").rand(0,9999));
    $data_api_data['invoiceid'] = $data['qris_invoiceid'];
    $values = f("str.dbq")([
        'kode'=>$randomcode,
        'tanggal'=>date("Y-m-d H:i:s"),
        'jenis'=>"topup",
        'id_penerima'=>$trxuser['id'],
        'saldo_awal_penerima'=>$saldo_awal,
        'nilai_transaksi'=>$cliTrxAmount,
        'keterangan'=>$data['k'],
        'topup_info'=>json_encode($data_api_data),
    ]);
    f("db.q")("insert into histori (".implode(",",array_keys($values)).") values (".implode(",",$values).")");
    //======================================
    // notif ke admin
    $text = "#TOPUP #u_".$trxuser['username']."\nNilai: $cliTrxAmount \nSaldo: $saldo\n";
    $text.= "Pengirim: " . $data_api_data['qris_payment_customername'] . " (" . $data_api_data['qris_payment_methodby'] . ")\n";
    $text.= "Status: " . $data_api_data['qris_status'] . " " . $data_api_data['qris_paid_date'] . "\n";
    $text.= "Kode: $randomcode";
    $telegram = f("bot.execute")("sendMessage",[
        'chat_id'=>f("get_config")("trx_log_chat_id"),
        'text'=>$text,
    ]);
    //=====================================
    //notif ke user
    $formatNumberNilai = number_format($cliTrxAmount,2,',','.');
    $formatNumberSaldo = number_format($saldo,2,',','.');
    if(!empty($trxuser['telegram_id'])){
        $text = $trxuser['username']." #TOPUP Berhasil! \nNilai: $formatNumberNilai \nSaldo: $formatNumberSaldo\n";
        $text.= "Pengirim: " . $data_api_data['qris_payment_customername'] . " (" . $data_api_data['qris_payment_methodby'] . ")\n";
        $text.= "Status: " . $data_api_data['qris_status'] . " " . $data_api_data['qris_paid_date'] . "\n";
        $text.= "Kode: $randomcode";
        $telegram = f("bot.execute")("sendMessage",[
            'chat_id'=>$trxuser['telegram_id'],
            'text'=>$text,
        ]);
    }
    //=====================================
    ?>
    <h2>Pembayaran Berhasil!</h2>
    <a href="bukti.php?code=<?=$randomcode?>">OK</a>
    <script>
        window.location.replace("bukti.php?code=<?=$randomcode?>");
    </script>
    <?php
    f("webview._layout.base")("exit");
}
else{
    ?>
    <h2>Perhatian</h2>
    Transaksi gagal atau belum dibayar.<br><br>
    <?php
}

?>
<br><br>
<a href='cancel.php?trx=<?=$_GET['id']?>'>Batalkan Transaksi</a><br><br>
<?php 
// <a href='dev_tes_anggap_sukses.php?trx=<?=$_GET['id']?
//>'>Anggap Sukses (Dev test underconstruction)</a><br><br>
?>
<a href='my_qris.php?u=<?=urlencode($_GET['u']??'')?>&trx=<?=$_GET['id']?>'>Coba Lagi</a><br><br>
<a href='check_unpaid.php'>List Unpaid Qris</a><br><br>
<a href='index.php'>Beranda</a><br><br>
<?php
f("webview._layout.base")("end");
// "status": "success",
// "data": {
//   "qris_status": "paid",
//   "qris_payment_customername": "Bpk GALIH JAYA KUSUMAH",
//   "qris_payment_methodby": "BNI",
//   "qris_paid_date": "2023-11-11 07:47:10"
// },
// "qris_api_version_code": "2311101447"
?>