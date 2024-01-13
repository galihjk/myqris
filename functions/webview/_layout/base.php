<?php
function webview___layout__base($section, $data = []){
    if($section == "start"){
        if(empty($data['body_class'])) $data['body_class'] = "sb-nav-fixed";
        if(empty($data['title'])){
            $data['title'] = "Problematic";
        }
        else{
            $data['title'] .= " - Problematic";
        }
        $GLOBALS['page_script'] = "";
        ?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="utf-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
                <meta name="description" content="" />
                <meta name="author" content="" />
                <title><?=$data['title']?></title>
                <link href="css/styles.css" rel="stylesheet" />
                <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
            </head>
            <body class="<?=$data['body_class']?>">
        <?php
    }
    elseif($section == "end"){
        ?>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
            <script src="js/jquery-3.7.1.min.js"></script>
            <script src="js/scripts.js"></script>
            <?= $GLOBALS['page_script'] ?>
            </body>
        </html>
        <?php
    }
    elseif($section == "exit"){
        f("webview._layout.base")("end");
        exit();
    }
    return "";
}
    