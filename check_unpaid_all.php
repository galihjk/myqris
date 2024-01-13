<?php
include("init.php");
$user = f("cek_login")();
if(!in_array($user['username'],f("get_config")("admins"))) dd("menu ini hanya untuk admin");
f("webview._layout.base")("start");
$unpaid_list = f("data.list")("trx");
if(empty($unpaid_list)){
    dd("Tidak ada transaksi yang belum dibayar.");
}
?>
<table border="1">
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nilai</th>
        <th>Cek Pembayaran</th>
        <th></th>
    </tr>
    <?php
    $no = 1;
    foreach($unpaid_list as $item){
        $data = f("data.load")("trx/$item");
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=$data['qris_request_date']?></td>
            <td><?=$data['cliTrxAmount']?></td>
            <td><a href='check.php?id=<?=$data['cliTrxNumber']?>'><?=$data['cliTrxNumber']?></a></td>
            <td>
                <?php
                // expired 30 menit
                if((time()-$data['t'] >= 30*60)){
                    ?>
                        (Expired) <a href='cancel.php?trx=<?=$data['cliTrxNumber']?>&admin=1'>Hapus</a>
                    <?php
                }
                ?>
            </td>
        </tr>
        <?php
        $no++;
    }
    ?>
    <tr></tr>
</table>
<?php
f("webview._layout.base")("end");