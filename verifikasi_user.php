<?php
include("init.php");
$user = f("cek_login")();
if(!in_array($user['username'],f("get_config")("admins"))) dd("menu ini hanya untuk admin");
if(!empty($_GET['reject'])){
    if(!empty($_GET['ya']) and $_GET['ya'] == 'yakin'){
        f("db.q")("delete from users where banned_at is null and verified_at is null and id=".f("str.dbq")($_GET['reject']));
        header("Location: verifikasi_user.php");
        exit();
    }
    f("webview._layout.base")("start");
    ?>
    <h2>Yakin?</h2>
    <h3>Anda akan menghapus akun "<?=$_GET['username']?>"</h3>
    <div>*setelah dihapus tidak dapat di-undo</div>
    <br>
    <br>
    [<a href="?reject=<?=$_GET['reject']?>&ya=yakin">Ya, yakin!</a>]
    <br><br>
    [<a href="verifikasi_user.php">Batalkan</a>]
    <?php
    f("webview._layout.base")("exit");
}
if(!empty($_GET['acc'])){
    if(!empty($_GET['ya']) and $_GET['ya'] == 'yakin'){
        f("db.q")("update users set verified_at = '".date("Y-m-d H:i:s")."'
        where banned_at is null and verified_at is null and id=".f("str.dbq")($_GET['acc']));
        header("Location: verifikasi_user.php");
        exit();
    }
    f("webview._layout.base")("start");
    ?>
    <h2>Yakin?</h2>
    <h3>Anda akan meng-ACC akun "<?=$_GET['username']?>"</h3>
    <br>
    <br>
    [<a href="?acc=<?=$_GET['acc']?>&ya=yakin">Ya, yakin!</a>]
    <br><br>
    [<a href="verifikasi_user.php">Batalkan</a>]
    <?php
    f("webview._layout.base")("exit");
}
$unverified_users = f("db.q")("select * from users where banned_at is null and verified_at is null order by id");
f("webview._layout.base")("start");
?>
<h1>My Qris (prototype 2)</h1>
<h2>Verifikasi Akun</h2>
<hr>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>User Name</th>
            <th>Verifikasi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach($unverified_users as $item){
            ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$item['id']?></td>
                <td><?=$item['username']?></td>
                <td>[<a href="?acc=<?=$item['id']?>&username=<?=$item['username']?>">ACC</a>] [<a href="?reject=<?=$item['id']?>&username=<?=$item['username']?>">TOLAK</a>]</td>
            </tr>
            <?php
            $no++;
        }
        ?>
    </tbody>
</table>
<?php
f("webview._layout.base")("end");