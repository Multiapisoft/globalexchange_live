<?php //include '../lib/config.php';
define("SITE_URL_NODE", "login.multiapisoft.com/v1/w");

/*$result = mysqli_query($link, "SELECT * FROM user WHERE status = 0 AND bitcoin!='' AND wallet >= 3");
while ($row = mysqli_fetch_object($result)) {
    $uid = $row->uid;
    $wallet_field = 'wallet';
    $amount = $row->$wallet_field;
    $account = $row->bitcoin;
    $type = 9;
    $type2 = 'USDT';
    $fee = $amount*0;
    $net_amount = $amount - $fee;
    $remark = 'AUTO W';
    
    if($amount >= 3){
        my_query("UPDATE user SET $wallet_field=$wallet_field-'$amount' WHERE uid='" . $uid . "'");
        //$_price = round($net_amount*SITE_COIN_RATE, 4);
        $_price = round($net_amount, 2);
        my_query("INSERT INTO withdrawal_block (uid, amount, fee, net_amount, amount_coin, datetime, status, withdrawal_address, type, remark) VALUES ('" . $uid . "', '" . $amount . "', '" . $fee . "', '" . $net_amount . "', '" . $_price . "', '" . date('c') . "', 0, '" . $account . "', '" . $type2 . "', '".$remark."')");
    }
}*/

//wusdt();

function wusdt($recid = ''){
    $where = ($recid) ? " AND recid = '".$recid."' " : "";
    $c = 'USDT';
    $status = 0;
    //echo '<br />/*******'.$c.'*******/<br />';
    $result = my_query("SELECT * FROM withdrawal_block WHERE status = 0 AND amount_coin>0 AND type='".$c."' $where ORDER BY amount_coin ASC");
    while ($row = mysqli_fetch_object($result)) {
        $account = $row->withdrawal_address;
        $amount = $row->amount_coin;
        // echo $amount;
        // die;
        $response = call_node($account, $amount, $c);
        $arr = @json_decode($response, true);
        if(isset($arr['result']['hash']) && $arr['result']['hash']){
            $hash = $arr['result']['hash'];
            $status = 1;
            $recid = $row->recid;
            my_query("UPDATE withdrawal_block SET approved_datetime='".date('c')."', status=1, withdrawal_id = '".$hash."' WHERE recid='".$recid."'");
            
            // Store fee in walfare_fund
            my_query("INSERT INTO walfare_fund (uid, amount, datetime) VALUES ('" . $row->uid . "', '" . $row->fee . "', '" . date('c') . "')");
        }else{
            //echo $response;
        }
        //echo '<br>========================<br>';
    }
    return $status;
}

//echo "<br/> Closing complete. Please close this browser.";

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
    // die;
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        // echo $response;
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