<?php
function data__load($name, $empty = []){

	$data = $empty;
    if(empty($GLOBALS['data'][$name])){

        $filename="data/$name.json";
		if(file_exists($filename)){
			$filedata = file_get_contents($filename);
			$data = json_decode($filedata,true);
			if($data === false){
				$data = $empty;
			}
		}
		else{
			$data = $empty;
		}

        if(empty($GLOBALS['data'])){
            $GLOBALS['data'] = [];
        }

        $GLOBALS['data'][$name] = $data;
    }
    else{
        $data = $GLOBALS['data'][$name];
    }

    return $data;
}