<?php
include("init.php");
$user = f("cek_login")();
if(!empty($_POST)){
    if($_POST['nilai'] < 1){
        ?>
        <script>
            alert("Nilai minimal 1");
            history.back();
        </script>
        <?php
        exit();
    }
    if($_POST['nilai'] > $user['saldo']){
        ?>
        <script>
            alert("Nilai (<?=$_POST['nilai']?>) tidak boleh melebihi saldo (<?=$user['saldo']?>).");
            history.back();
        </script>
        <?php
        exit();
    }
    if($_POST['username'] == $user['username']){
        ?>
        <script>
            alert(`Tidak boleh mengirim kepada '<?=$_POST['username']?>' (diri sendiri).`);
            history.back();
        </script>
        <?php
        exit();
    }
    $tujuan_transfer = f("db.select_one")("select * from users where username=".f("str.dbq")($_POST['username']));
    if(empty($tujuan_transfer)){
        ?>
        <script>
            alert(`Pengguna '<?=$_POST['username']?>' tidak ditemukan.`);
            history.back();
        </script>
        <?php
        exit();
    }
    if(empty($tujuan_transfer['verified_at'])){
        ?>
        <script>
            alert(`Pengguna '<?=$_POST['username']?>' belum diverifikasi.`);
            history.back();
        </script>
        <?php
        exit();
    }
    if(!empty($tujuan_transfer['banned_at'])){
        ?>
        <script>
            alert(`Status pengguna '<?=$_POST['username']?>' adalah banned.`);
            history.back();
        </script>
        <?php
        exit();
    }
    if(empty($_POST['yakin'])){
        f("webview._layout.base")("start");
        ?>
            <h1>Apakah anda yakin?</h1>
            <form method="POST">
                <input type="hidden" name="yakin" value="1"/>
                Kirim Ke: <strong><?=$_POST['username']?></strong><br>
                Nilai: <strong><?=number_format($_POST['nilai'],2,',','.')?></strong><br>
                Keterangan: <textarea disabled><?=str_replace('"','&quot;',$_POST['keterangan'])?></textarea>
                <br>
                <?php
                foreach($_POST as $k=>$v){
                    $v = str_replace('"','&quot;',$v);
                    echo "<input type='hidden' name='$k' value=\"$v\" />";
                }
                ?>
                <br>
                <input type="submit" value="Ya" />
                <br>
                <br>
                <button type="button" onclick="history.back()">Tidak</button>
            </form>
        <?php
        f("webview._layout.base")("exit");
    }
    $jumlah_transfer = $_POST['nilai'];
    $keterangan = $_POST['keterangan'];
    $saldo_baru_pengirim = $user['saldo'] - $jumlah_transfer;
    $saldo_baru_penerima = $tujuan_transfer['saldo'] + $jumlah_transfer;
    $formatNumberTransfer = number_format($jumlah_transfer,2,',','.');
    $formatNumberSaldoPengirim = number_format($saldo_baru_pengirim,2,',','.');
    $formatNumberSaldoPenerima = number_format($saldo_baru_penerima,2,',','.');
    f("db.q")("update users set saldo=$saldo_baru_pengirim where id=".f("str.dbq")($user['id']));
    f("db.q")("update users set saldo=$saldo_baru_penerima where id=".f("str.dbq")($tujuan_transfer['id']));
    $randomcode = md5(date("YmdHis").rand(0,9999));
    //notif telegram admin
    $text = "#KIRIM \nDari #u_".$user['username']."\nKe: #u_".$tujuan_transfer['username']."\n";
    $text.= "Nilai: $jumlah_transfer\n";
    $text.= "Saldo Akhir:\n";
    $text.= "Pengirim: $saldo_baru_pengirim\n";
    $text.= "Penerima: $saldo_baru_penerima\n";
    $text.= "Kode: $randomcode";
    $telegram = f("bot.execute")("sendMessage",[
        'chat_id'=>f("get_config")("trx_log_chat_id"),
        'text'=>$text,
    ]);
    //notif telegram pengirim
    if(!empty($user['telegram_id'])){
        $text = $user['username']." #KIRIM Berhasil!\nTujuan: ".$tujuan_transfer['username']."\n";
        $text.= "Nilai: $formatNumberTransfer\n";
        $text.= "Saldo: $formatNumberSaldoPengirim\n";
        $text.= "Kode: $randomcode";
        $telegram = f("bot.execute")("sendMessage",[
            'chat_id'=>$user['telegram_id'],
            'text'=>$text,
        ]);
    }
    //notif telegram penerima
    if(!empty($tujuan_transfer['telegram_id'])){
        $text = $tujuan_transfer['username']." #TERIMA \nDari: ".$user['username']."\n";
        $text.= "Nilai: $formatNumberTransfer\n";
        $text.= "Saldo: $formatNumberSaldoPenerima\n";
        $text.= "Kode: $randomcode";
        $telegram = f("bot.execute")("sendMessage",[
            'chat_id'=>$tujuan_transfer['telegram_id'],
            'text'=>$text,
        ]);
    }
    $values = f("str.dbq")([
        'kode'=>$randomcode,
        'tanggal'=>date("Y-m-d H:i:s"),
        'jenis'=>"transfer",
        'id_pengirim'=>$user['id'],
        'id_penerima'=>$tujuan_transfer['id'],
        'saldo_awal_pengirim'=>$user['saldo'],
        'saldo_awal_penerima'=>$tujuan_transfer['saldo'],
        'nilai_transaksi'=>$jumlah_transfer,
        'keterangan'=>$keterangan,
    ]);
    $insert_history_q = "insert into histori 
    (".implode(",",array_keys($values)).")
    values
    (".implode(",",$values).")";
    f("db.q")($insert_history_q);
    $url = "bukti.php?code=$randomcode";
    header("Location: $url");
    exit();
}
f("webview._layout.base")("start");
?>
<h2>Kirim ke sesama pengguna</h2>
<form method="POST">
    Saldo Anda: <strong><?=$user['saldo']?></strong><br>
    Kirim Ke: <input name="username" type="text" required placeholder="username"/><br>
    Nilai: <input style="width:170px;" max="<?=$user['saldo']?>" name="nilai" type="number" required min="0" placeholder="Mau kirim berapa?"/><br>
    Keterangan: <textarea name="keterangan"></textarea>
    <br>
    <input type="submit"/>
</form>
<?php
f("webview._layout.base")("end");