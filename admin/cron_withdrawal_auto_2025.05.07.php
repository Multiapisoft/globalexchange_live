<?php include_once '../lib/config.php';
define("SITE_URL_NODE", "login.multiapisoft.com/v1/w");

$c = 'USDT';
echo '<br />/*******'.$c.'*******/<br />';
$result = mysqli_query($link, "SELECT * FROM withdrawal_block WHERE status = 0 AND amount_coin>0 AND type='".$c."' ORDER BY amount_coin ASC");
while ($row = mysqli_fetch_object($result)) {
    $account = $row->withdrawal_address;
    $amount = $row->amount_coin;
    $response = call_node($account, $amount, $c);
    $arr = @json_decode($response, true);
    if(isset($arr['result']['hash']) && $arr['result']['hash']){
        echo $hash = $arr['result']['hash'];
        $recid = $row->recid;
        my_query("UPDATE withdrawal_block SET approved_datetime='".date('c')."', status=1, withdrawal_id = '".$hash."' WHERE recid='".$recid."'");
        
        // Store fee in walfare_fund
        my_query("INSERT INTO walfare_fund (uid, amount, datetime) VALUES ('" . $row->uid . "', '" . $row->fee . "', '" . date('c') . "')");
    }else{
        echo $response;
    }
    echo '<br>========================<br>';
}

echo "<br/> Closing complete. Please close this browser.";

function call_node($address, $amount, $c = "BNB") {
    $data = array(
        "auth" => "ToTiLaL",
        "address" => $address,
        "amount" => $amount,
        "site" => SITE_URL,
        "c" => $c
    );
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://".SITE_URL_NODE."/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json"
        ),
    ));
    echo $response = curl_exec($curl);
    echo 1;
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        return $response;
    }
}

function call_node_bal($c = 'BNB') {
    $data = array(
        "auth" => "ToTiLaLbal",
        "site" => SITE_URL,
        "c" => $c
    );
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://".SITE_URL_NODE."/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        return $response;
    }
}
?>