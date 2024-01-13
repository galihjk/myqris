<h1>My Qris (prototype 2)</h1>
<hr>
<?php
include("init.php");
$user = f("cek_login")();
f("webview._layout.base")("start");
$page = ($_GET['page'] ?? 1);
$rowperpage = f("get_config")("history_per_page",2);
$last_id = (empty($_GET['last_id']) ? false : $_GET['last_id']);
if($last_id){
    $q_lastid = "and id < ".f("str.dbq")($last_id,false);
}
else{
    $q_lastid = "";
}
$q = "select * from histori 
where (id_penerima=".$user['id']." or id_pengirim=".$user['id'].")
$q_lastid
order by tanggal desc, id desc
limit $rowperpage";
// dump($q);
$data = f("db.q")($q);
if($data){
    $last_id = $data[count($data)-1]['id'];
}
?>
<h2>Histori Transaksi</h2>
<?php
if($page > 1){
    ?>
    <a href="javascript:void(0)" onclick="history.back()">Sebelumnya</a>
    <?php
}
else{
    echo "Sebelumnya";
}
echo " [Halaman: $page] ";
if(count($data) == $rowperpage){
    ?>
    <a href="?last_id=<?=$last_id?>&page=<?=$page+1?>">Selanjutnya</a>
    <?php
}
else{
    echo "Selanjutnya";
}
?>
<br><br>
<table border=1>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th>Dari / Ke</th>
            <th>Saldo Awal</th>
            <th>Nominal</th>
            <th>Saldo Akhir</th>
            <th>Link Bukti</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = ($page-1)*$rowperpage+1;
        foreach($data as $item){
            $jenis = "?";
            $dari_ke = "";
            $nilai = 0;
            $saldo_awal = "";
            if($item['jenis'] == "transfer"){
                if($item['id_pengirim'] == $user['id']){
                    $jenis = "kirim ke";
                    $dari_ke = f("user.get_username")($item['id_penerima']);
                    $nilai = -$item['nilai_transaksi'];
                    $saldo_awal = $item['saldo_awal_pengirim'];
                }
                else{
                    $jenis = "terima dari";
                    $dari_ke = f("user.get_username")($item['id_pengirim']);
                    $nilai = $item['nilai_transaksi'];
                    $saldo_awal = $item['saldo_awal_penerima'];
                }
            }
            if($item['jenis'] == "topup"){
                $jenis = "top up";
                $json_decode = json_decode($item['topup_info'],true);
                $dari_ke = $json_decode['qris_payment_customername'] ?? "-";
                $dari_ke .= " (" . ($json_decode['qris_payment_methodby'] ?? "-") . ")";
                $nilai = $item['nilai_transaksi'];
                $saldo_awal = $item['saldo_awal_penerima'];
            }
            if($item['jenis'] == "withdraw"){
                $jenis = "withdrawal";
                $json_decode = json_decode($item['topup_info'],true);
                $dari_ke = "(Anda)";
                $nilai = -$item['nilai_transaksi'];
                $saldo_awal = $item['saldo_awal_pengirim'];
            }
            if($item['jenis'] == "biaya_admin"){
                $jenis = "biaya admin";
                $json_decode = json_decode($item['topup_info'],true);
                $dari_ke = "(Admin)";
                $nilai = -$item['nilai_transaksi'];
                $saldo_awal = $item['saldo_awal_pengirim'];
            }
            ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$item['tanggal']?></td>
                <td><?=$jenis?></td>
                <td><?=$dari_ke?></td>
                <td style="text-align: right"><?=number_format($saldo_awal,0,',','.')?></td>
                <td style="text-align: right"><?=number_format($nilai,0,',','.')?></td>
                <td style="text-align: right"><?=number_format(intval($saldo_awal)+$nilai,0,',','.')?></td>
                <td><a href="bukti.php?code=<?=$item['kode']?>">Lihat</a></td>
                <td><?=nl2br($item['keterangan'])?></td>
            </tr>
            <?php
            $no++;
        }
        ?>
    </tbody>
</table>
<br>
<div style="text-align: right;">
<?php
if($page > 1){
    ?>
    <a href="javascript:void(0)" onclick="history.back()">Sebelumnya</a>
    <?php
}
else{
    echo "Sebelumnya";
}
echo " [Halaman: $page] ";
if(count($data) == $rowperpage){
    ?>
    <a href="?last_id=<?=$last_id?>&page=<?=$page+1?>">Selanjutnya</a>
    <?php
}
else{
    echo "Selanjutnya";
}
?>
</div>
<?php
f("webview._layout.base")("exit"); // exit();
