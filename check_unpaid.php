<?php
include("init.php");
$user = f("cek_login")();
$unpaid_list = f("data.list")("trx");
foreach($unpaid_list as $k=>$v){
    if(!f("str.is_diawali")($v,$user['id']."-")) unset($unpaid_list[$k]);
}
f("webview._layout.base")("start");
?>
<h2>List Unpaid Qris</h2>
<?php
if(empty($unpaid_list)){
    dd("Tidak ada transaksi yang belum dibayar.");
}
?>
<table class="table">
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nilai</th>
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
            <td>
                <?php
                // expired 30 menit
                if((time()-$data['t'] >= 30*60)){
                    ?>
                        (Expired) <a href='cancel.php?trx=<?=$data['cliTrxNumber']?>'>Hapus</a>
                    <?php
                }
                else{
                    ?>
                        <a href='check.php?id=<?=$data['cliTrxNumber']?>'>Cek Pembayaran</a>
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