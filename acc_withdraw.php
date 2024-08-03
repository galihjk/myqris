<?php
include("init.php");
$user = f("cek_login")();
if(!in_array($user['username'],f("get_config")("admins"))) dd("menu ini hanya untuk admin");
f("webview._layout.base")("start");
?>
<h2>ACC Withdraw</h2>
<?php
if(!empty($_GET['edit'])){
    if(!empty($_POST)){
        if($_POST['nilai'] <= 0){
            ?>
            <script>
                alert("Nilai minimal 1");
                history.back();
            </script>
            <?php
            f("webview._layout.base")("exit");
        }
        f("data.save")("wd/".$_GET['edit'],[
            'a'=>$_POST['nilai'],
            'm'=>$_POST['metode'],
        ]);
        ?>
        <script>
            window.location.replace("acc_withdraw.php?user=<?=$_GET['edit']?>&username=<?=$_GET['username']?>");
        </script>
        <?php
        f("webview._layout.base")("exit");
    }
    $data = f("data.load")("wd/".$_GET['edit']);
    if(empty($data)){
        ?>
        <script>
            alert("data withdraw not found");
            window.location.replace("acc_withdraw.php");
        </script>
        <?php
        f("webview._layout.base")("exit");
    }
    $withdraw_user = f("db.select_one")("select * from users where id=".f("str.dbq")($_GET['edit']));
    if(!empty($_GET['metode'])){
        $metode_dipilih = $_GET['metode'];
    }
    else{
        $metode_dipilih = $data['m'];
    }
    $biaya_withdraw = f("get_config")("biaya_withdraw")[$metode_dipilih];
    ?>
    <form method="POST" >
        User: <strong><?=$_GET['username']?> [ID:<?=$_GET['edit']?>]</strong><br>
        Saldo: <strong><?=number_format($withdraw_user['saldo'],2,',','.')?></strong><br>
        Withdraw: <input id="nilai" value="<?=$_GET['nilai'] ?? $data['a']?>" name="nilai" type="number" required max="<?=$withdraw_user['saldo'] - $biaya_withdraw?>" placeholder="Mau ambil berapa?"/><br>
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
    <br>
    <a href='acc_withdraw.php?user=<?=$_GET['edit']?>'>Kembali</a>
    <script>
        function pilihMetode(){
            var url = "acc_withdraw.php?edit=<?=$_GET['edit']?>&username=<?=$_GET['username']?>";
            url += "&nilai="+document.getElementById("nilai").value;
            url += "&metode="+document.getElementById("metode").value;
            window.location.replace(url);
        }
    </script>
    <?php
    f("webview._layout.base")("exit");
}
if(!empty($_GET['user'])){
    $data = f("data.load")("wd/".$_GET['user']);
    if(!empty($data)){
        $withdraw_user = f("db.select_one")("select * from users where id=".f("str.dbq")($_GET['user']));
        $biaya_withdraw = f("get_config")("biaya_withdraw")[$data['m']];
        $biaya_withdraw_number_format = number_format($biaya_withdraw,2,',','.');
        $phoneNum = f("get_config")("no_wa_admin");
        $total = $data['a'] + $biaya_withdraw;
        
        if(!empty($_GET['act'])){
            if($_GET['act'] == 'acc'){
                $saldo_awal = $withdraw_user['saldo'];

                f("data.delete")("wd/".$_GET['user']);
                f("db.q")("update users set saldo=".($withdraw_user['saldo']-$total)." where id=".f("str.dbq")($_GET['user']));
                
                $randomcode = md5(date("YmdHis").rand(0,9999));
                //histori withdraw
                $values = f("str.dbq")([
                    'kode'=>"W".$randomcode,
                    'tanggal'=>date("Y-m-d H:i:s"),
                    'jenis'=>"withdraw",
                    'id_pengirim'=>$withdraw_user['id'],
                    'saldo_awal_pengirim'=>$saldo_awal,
                    'nilai_transaksi'=>$data['a'],
                    'keterangan'=>$_GET['keterangan'],
                ]);
                f("db.q")("insert into histori (".implode(",",array_keys($values)).") values (".implode(",",$values).")");
                //histori biaya admin
                $values = f("str.dbq")([
                    'kode'=>"A".$randomcode,
                    'tanggal'=>date("Y-m-d H:i:s"),
                    'jenis'=>"biaya_admin",
                    'id_pengirim'=>$withdraw_user['id'],
                    'saldo_awal_pengirim'=>$saldo_awal-$data['a'],
                    'nilai_transaksi'=>$biaya_withdraw,
                    'keterangan'=>$data['m'],
                ]);
                f("db.q")("insert into histori (".implode(",",array_keys($values)).") values (".implode(",",$values).")");

                $text = "#WITHDRAW #u_".$withdraw_user['username']."\n"
                ."Withdraw: ".$data['a']."\n"
                ."Metode: ".$data['m']."\n"
                ."Biaya Admin: $biaya_withdraw\n"
                ."Total: $total\n"
                ."Sisa Saldo: ".($withdraw_user['saldo']-$total)."\n";
                $telegram = f("bot.execute")("sendMessage",[
                    'chat_id'=>f("get_config")("trx_log_chat_id"),
                    'text'=>$text,
                ]);
                if(!empty($withdraw_user['telegram_id'])){
                    $text = $withdraw_user['username']." #WITHDRAW Berhasil!\n"
                    ."Nilai Withdraw: ".number_format($data['a'],2,',','.')."\n"
                    ."Biaya Admin: $biaya_withdraw_number_format\n"
                    ."Total: ".number_format($total,2,',','.')."\n"
                    ."Sisa Saldo: ".number_format($withdraw_user['saldo']-$total,2,',','.')."\n";
                    $telegram = f("bot.execute")("sendMessage",[
                        'chat_id'=>$withdraw_user['telegram_id'],
                        'text'=>$text,
                    ]);
                }
                ?>
                <script>
                    window.location.replace("acc_withdraw.php?success=W<?=$randomcode?>");
                </script>
                <?php
                f("webview._layout.base")("exit");
            }
            elseif($_GET['act'] == 'reject'){
                if(!empty($withdraw_user['telegram_id'])){
                    $text = "Withdrawal $biaya_withdraw_number_format telah dibatalkan\n";
                    $telegram = f("bot.execute")("sendMessage",[
                        'chat_id'=>$withdraw_user['telegram_id'],
                        'text'=>$text,
                    ]);
                }
                f("data.delete")("wd/".$_GET['user']);
                ?>
                <script>
                    window.location.replace("acc_withdraw.php");
                </script>
                <?php
                f("webview._layout.base")("exit");
            }
            else{
                dd("underconst act");
            }
        }
        ?>
        <form>
        User: <strong><?=$_GET['username']?> [ID:<?=$_GET['user']?>]</strong>
        <br>
        Withdraw: <strong><?=number_format($data['a'],2,',','.')?></strong>
        <br>
        Metode: <strong><?=$data['m']?></strong>
        <br>
        Biaya Admin: <strong><?=$biaya_withdraw_number_format?></strong>
        <br>
        Total: <strong><?=number_format($total,2,',','.')?></strong>
        <br>
        Saldo: <strong><?=number_format($withdraw_user['saldo'],2,',','.')?></strong>
        <br>
        Sisa Saldo: <strong><?=number_format($withdraw_user['saldo']-$total,2,',','.')?></strong>
        <br><br>
        <hr>
        <strong>ACC</strong><br>
        Keterangan: <textarea name="keterangan"></textarea>
        <br><br>
        <input type="hidden" name="user" value="<?=$_GET['user']?>" />
        <button type="submit" name="act" value="acc" style="font-size: larger;">✔️ ACC</button>
        <hr>
        <br>
        <a href='?edit=<?=$_GET['user']?>&username=<?=$_GET['username']?>'>✏️ EDIT</a>
        <br><br>
        <a href='?user=<?=$_GET['user']?>&act=reject'>❌ BATALKAN</a>
        <br>
        </form>
        <br>
        <hr>
        <br>
        <?php
    }
}
if(!empty($_GET['success'])){
    ?>
    <div>
        ACC Withdraw berhasil. Link Bukti Transfer: <a href="bukti.php?code=<?=$_GET['success']?>">LIHAT</a>
    </div>
    <?php
}
?>
<table class="table">
    <tr>
        <td>
            <strong>No</strong>
        </td>
        <td colspan="2">
            <strong>User</strong>
        </td>
    </tr>
    <?php
    $no = 1;
    $datalist_wd = f("data.list")("wd");
    $wd_users = [];
    if(!empty($datalist_wd)){
        $wd_users_q = "select id, username from users where id in ('".implode("', '",$datalist_wd)."')";
        $wd_users = f("db.q")("select id, username from users where id in ('".implode("', '",$datalist_wd)."')");
    }
    foreach($wd_users as $item){
        ?>
        <tr>
            <td style="text-align: right"><?=$no?></td>
            <td><?=$item['username']?></td>
            <td>
                <?php
                if(!empty($_GET['user']) and $_GET['user'] == $item['id']){
                    echo "DIPILIH";
                }
                else{
                    echo "<a href='?user=".$item['id']."&username=".$item['username']."'>➡️PILIH</a>";
                }
                ?>
            </td>
        </tr>
        <?php 
        $no++;       
    }
    ?>
</table>
<?php
f("webview._layout.base")("end");