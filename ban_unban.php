<?php
include("init.php");
$user = f("cek_login")();
if(!in_array($user['username'],f("get_config")("admins"))) dd("menu ini hanya untuk admin");
if(!empty($_GET['unban'])){
    if(!empty($_GET['ya']) and $_GET['ya'] == 'yakin'){
        f("db.q")("update users set banned_at = null 
        where banned_at is not null and id=".f("str.dbq")($_GET['unban']));
        header("Location: ban_unban.php");
        exit();
    }
    f("webview._layout.base")("start");
    ?>
    <h2>Yakin?</h2>
    <h3>Anda akan meng-unban akun "<?=$_GET['username']?>"</h3>
    <br>
    <br>
    [<a href="?unban=<?=$_GET['unban']?>&ya=yakin">Ya, yakin!</a>]
    <br><br>
    [<a href="ban_unban.php">Batalkan</a>]
    <?php
    f("webview._layout.base")("exit");
}
if(!empty($_GET['ban_username'])){
    f("db.q")("update users set banned_at = '".date("Y-m-d H:i:s")."'
    where banned_at is null and username=".f("str.dbq")($_GET['ban_username']));
    header("Location: ban_unban.php");
    exit();
}
$banned_users = f("db.q")("select * from users where banned_at is not null order by banned_at");
f("webview._layout.base")("start");
?>
<h1><img src="assets/img/logo1.jpg" style="width: 121px;"></h1>
<h2>Ban / Unban User Account</h2>
<hr>
<form>Ban User <input type="text" placeholder="ban user_name" required name="ban_username"/> <input type="submit" value="BAN USER!"/></form>
<hr>
Banned Users:
<table class="table">
    <thead>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>User Name</th>
            <th>Banned At</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach($banned_users as $item){
            ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$item['id']?></td>
                <td><?=$item['username']?></td>
                <td><?=$item['banned_at']?></td>
                <td>[<a href="?unban=<?=$item['id']?>&username=<?=$item['username']?>">UNBAN</a>]</td>
            </tr>
            <?php
            $no++;
        }
        ?>
    </tbody>
</table>
<?php
f("webview._layout.base")("end");