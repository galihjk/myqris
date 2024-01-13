<?php
include("init.php");
$user = f("cek_login")();
if(!empty($_GET['set'])){
    if($_GET['set'] == 'remove'){
        f("db.q")("update users set public = null where id = ".f("str.dbq")($user['id']));
    }
    if($_GET['set'] == 'generate'){
        f("db.q")("update users set public = '".date("YmdHis")."' where id = ".f("str.dbq")($user['id']));
    }
    header("Location: index.php");
}
if(!empty($_GET['with_nominal'])){
    $nominal = $_GET['with_nominal'];
    $keterangan = $_GET['keterangan'];
    $addurl = "&n=$nominal";
    if(empty($keterangan)){
        $keterangan = "-";
    }
    else{
        $addurl .= "&k=".urlencode($keterangan);
    }
    $nominal_format = number_format($nominal,2,',','.');
    f("webview._layout.base")("start");
    echo "<h1>Payment Link</h1>";
    echo "Nominal: <strong>$nominal_format</strong><br>";
    echo "Keterangan: <strong>$keterangan</strong><br><br>";
    echo "Link: <strong>[<a href='my_qris.php?u=".f("myqris.link")($user)."$addurl' onclick='linkShow(event);'>LINK</a>]</strong><br>";
    ?>
    <script>
        function linkShow(e){
            e.preventDefault();
            alert("You can copy this link address and share it to anyone");
        }
    </script>
    <?php
    echo "<br><a href='index.php'>Kembali</a>";
    exit();
}
?>
-