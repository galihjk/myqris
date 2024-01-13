<?php
function webview__errorpost($data){
    ?>
    <h1 style='text-align:center;margin-top: 60px;'><?=$data?></h1>
    <div style='text-align:center'>
        <button onclick="history.back()" style="font-size: x-large;">Kembali</button>
    </div>
    <?php
    exit();
}
    