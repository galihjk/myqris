<?php
function webview___component__js__second_to_time(){
    if(!empty($GLOBALS['f_second_to_time'])){
        return false;
    }
    $GLOBALS['f_second_to_time'] = true;
    ?>
    <script>
        function pad(num, size) {
            num = num.toString();
            while (num.length < size) num = "0" + num;
            return num;
        }
        function second_to_time(my_second){
            var d = Math.floor(my_second / (86400));
            var h = Math.floor(my_second % (86400) / 3600);
            var m = Math.floor(my_second % 3600 / 60);
            var s = Math.floor(my_second % 60);
            return (d > 0 ? d + "h " : "")+pad(h)+":"+pad(m,2)+":"+pad(s,2);
        }
    </script>
    <?php
}
    