<?php
function qris__make($cliTrxAmount, $userid = "", $keterangan = ""){
    $cliTrxNumber = $userid."-".md5($userid.date("YmdHis").rand(0,99));
    $api_param = [
        'do'=>'create-invoice',
        'apikey'=>f("get_config")("APIKEY"),
        'mID'=>f("get_config")("mID"),
        'cliTrxNumber'=>$cliTrxNumber,
        'cliTrxAmount'=>$cliTrxAmount,
    ];
    $api_url = "https://qris.online/restapi/qris/show_qris.php";
    $api_q_param = http_build_query($api_param);
    // dd("$api_url?$api_q_param");
    $data_api_json = file_get_contents("$api_url?$api_q_param");
    // dd($data_api_json);
    $data_api = json_decode($data_api_json,true);
    if(empty($data_api['data']['qris_content'])) {
        $errordate = date("Y-m-d-H-i-s-").rand(100,999);
        f("data.save")("ERR $errordate",[
            "$api_url?$api_q_param",
            $data_api_json,
            $data_api,
        ]);
        die("Mohon maaf, telah terjadi kesalahan. Kode: $errordate");
    }
    if(empty($userid)) $userid = $_SESSION['user']['id'];
    $data = [
        'qris_content'=>$data_api['data']['qris_content'],
        'qris_request_date'=>$data_api['data']['qris_request_date'],
        'qris_invoiceid'=>$data_api['data']['qris_invoiceid'],
        'qris_nmid'=>$data_api['data']['qris_nmid'],
        'cliTrxNumber'=>$cliTrxNumber,
        'cliTrxAmount'=>$cliTrxAmount,
        'u'=>$userid,
        't'=>time(),
        'k'=>$keterangan,
    ];
    f("data.save")("trx/$cliTrxNumber",$data);
    return $data;
}
    