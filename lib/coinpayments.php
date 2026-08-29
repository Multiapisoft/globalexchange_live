<?php
$cparr = array(1 => 'BTC', 2 => 'LTC', 3 => 'DOGE', 4 => 'ETH', 5 => 'BCH', 6 => 'Dash', 7 => 'XRP', 8 => 'NEO', 9 => 'PAX', 10 => 'USDT.BEP20');
$cparr = array(12 => 'TRX');
$cparr = array(1 => 'USDT', 2 => 'TRX', 3 => 'EUROO1');
$cparr = array(1 => 'USDT.BEP20');

function coinpayments_api_call($cmd, $req = array()) {
    // Fill these in from your API Keys page
    $public_key = '';
    $private_key = '';

    if (!$private_key) {
        return;
    }

    // Set the API command and required fields
    $req['version'] = 1;
    $req['cmd'] = $cmd;
    $req['key'] = $public_key;
    $req['format'] = 'json'; //supported values are json and xml
    // Generate the query string
    $post_data = http_build_query($req, '', '&');

    // Calculate the HMAC signature on the POST data
    $hmac = hash_hmac('sha512', $post_data, $private_key);

    // Create cURL handle and initialize (if needed)
    static $ch = NULL;
    if ($ch === NULL) {
        $ch = curl_init('https://www.coinpayments.net/api.php');
        curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('HMAC: ' . $hmac));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    // Execute the call and close cURL handle     
    $data = curl_exec($ch);
    // Parse and return data if successful.
    if ($data !== FALSE) {
        if (PHP_INT_SIZE < 8 && version_compare(PHP_VERSION, '5.4.0') >= 0) {
            // We are on 32-bit PHP, so use the bigint as string option. If you are using any API calls with Satoshis it is highly NOT recommended to use 32-bit PHP
            $dec = json_decode($data, TRUE, 512, JSON_BIGINT_AS_STRING);
        } else {
            $dec = json_decode($data, TRUE);
        }
        if ($dec !== NULL && count($dec)) {
            return $dec;
        } else {
            // If you are using PHP 5.5.0 or higher you can use json_last_error_msg() for a better error message
            return array('error' => 'Unable to parse JSON result (' . json_last_error() . ')');
        }
    } else {
        return array('error' => 'cURL error: ' . curl_error($ch));
    }
}

function coinpayments_get_address() {
    global $cparr;
    user();
    $uid = $_SESSION['userid'];
    $user = get_user_details($uid);
    $addressarr = array(1 => 'USDT');
    foreach ($cparr as $type => $value) {
        $_address = (isset($addressarr[$type])) ? $addressarr[$type] : $value;
        $_address = strtolower($_address) . '_address';
        $value = ($value == 'USDT') ? 'USDT.BEP20' : $value;

        if (!$user->$_address) {
            $getNewAddressInfo = coinpayments_api_call('get_callback_address', array('currency' => $value, 'ipn_url' => 'https://' . SITE_URL . '/soft/lib/cp.php', 'label' => $uid));
            if (isset($getNewAddressInfo['error']) && $getNewAddressInfo['error'] == 'ok' && isset($getNewAddressInfo['result']['address'])) {
                my_query("UPDATE user SET $_address = '" . $getNewAddressInfo['result']['address'] . "' WHERE uid='" . $uid . "'");
                $user->$_address = $getNewAddressInfo['result']['address'];
            }
        }
    }
    return $user;
}

function coinpayments_withdrawal($withdraw_arr, $type) {
    if ($withdraw_arr) {
        foreach ($withdraw_arr as $_k => $arr) {
            $_wd = 'wd' . $_k;
            $req['wd[' . $_wd . '][amount]'] = $arr[2];
            $req['wd[' . $_wd . '][address]'] = $arr[1];
            $req['wd[' . $_wd . '][currency]'] = $type;
        }
        $result_mass = coinpayments_api_call('create_mass_withdrawal', $req);
        $_j = 0;
        if ($result_mass['error'] == 'ok') {
            foreach ($result_mass['result'] as $result) {
                $_j++;
                $_recid = $withdraw_arr[$_j][0];
                $ac_bc = $withdraw_arr[$_j][1];
                if ($result['error'] == 'ok') {
                    my_query("UPDATE withdrawal_block SET withdrawal_address = '" . $ac_bc . "', withdrawal_id = '" . $result['id'] . "', withdrawal_status = '" . $result['status'] . "', approved_datetime='" . date('c') . "', status = 1, remark = 'AUTO W', type = '" . $type . "' WHERE recid = '" . $_recid . "'");
                } else {
                    my_query("UPDATE withdrawal_block SET withdrawal_address = '" . $ac_bc . "', error = '" . $result['error'] . "', type = '" . $type . "' WHERE recid='" . $_recid . "'");
                }
            }
        }
    }
}

function coinpayments_new_withdrawal($withdraw_arr, $type) {
    $type = ($type == 'USDT') ? 'USDT.BEP20' : $type;
    if ($withdraw_arr) {
        foreach ($withdraw_arr as $_k => $arr) {
            $_wd = 'wd' . $_k;
            $req['wd[' . $_wd . '][amount]'] = $arr[2];
            $req['wd[' . $_wd . '][address]'] = $arr[1];
            $req['wd[' . $_wd . '][currency]'] = $type;
        }
        $result_mass = coinpayments_api_call('create_mass_withdrawal', $req);
        $_j = 0;
        if ($result_mass['error'] == 'ok') {
            foreach ($result_mass['result'] as $result) {
                $_j++;
                $_recid = $withdraw_arr[$_j][0];
                $ac_bc = $withdraw_arr[$_j][1];
                if ($result['error'] == 'ok') {
                    my_query("UPDATE withdrawal_block SET withdrawal_address = '" . $ac_bc . "', withdrawal_id = '" . $result['id'] . "', withdrawal_status = '" . $result['status'] . "', approved_datetime='" . date('c') . "', status = 1, remark = 'AUTO W' WHERE recid = '" . $_recid . "'");
                } else {
                    my_query("UPDATE withdrawal_block SET withdrawal_address = '" . $ac_bc . "', error = '" . $result['error'] . "' WHERE recid='" . $_recid . "'");
                }
            }
        }
    }
}

function coinpayments_deposit($data) {
    global $cparr;
    $addressarr = array('USDT.BEP20' => 'USDT', 'USDT.BEP20' => 'USDT');
    $address = isset($data['address']) ? $data['address'] : '';
    $uid = isset($data['label']) ? $data['label'] : '';
    $status = isset($data['status']) ? $data['status'] : '';
    $currency = isset($data['currency']) ? $data['currency'] : '';
    $txid = isset($data['txn_id']) ? $data['txn_id'] : '';
    $currency2 = $_address = (isset($addressarr[$currency])) ? $addressarr[$currency] : $currency;
    $_address = strtolower($_address) . '_address';
    $amount_coin = isset($data['amount']) ? $data['amount'] : 0;
    $data_json = @json_encode($data);

    $row = my_fetch_object(my_query("SELECT * FROM user WHERE uid = '" . $uid . "' AND $_address = '" . $address . "'"));
    $check = my_num_rows(my_query("SELECT uid FROM deposit_block WHERE txid = '" . $txid . "'"));
    if ($row && $check == 0 && $status >= 10) {
        foreach ($cparr as $value) {
            if ($value == $currency) {
                $amount = ($value == 'TRX') ? $amount_coin*_ETH_USD_ : $amount_coin;
                //$fee = $amount*0.01;
                $fee = 0;
                $net_amount = $amount - $fee;
                my_query("INSERT INTO deposit_block (uid, datetime, status, amount, fee, net_amount, amount_coin, txid, data, type) VALUES ('" . $uid . "', '" . date('c') . "', 1, '" . $amount . "', '" . $fee . "', '" . $net_amount . "', '" . $amount_coin . "', '" . $txid . "', '" . $data_json . "', '" . $value . "')");
                my_query("UPDATE user SET wallet_topup = wallet_topup + '" . $net_amount . "' WHERE uid='" . $uid . "'");
            }
        }
    }
}

//Get current coin exchange rates
//print_r(coinpayments_api_call('create_transaction', array('amount' => 100, 'currency1' => 'USD', 'currency2' => 'BTC')));
//print_r(coinpayments_api_call('get_tx_info', array('txid' => 'CPCI2YKIIFYBSNPHAGWGJBEEED')));die;
//print_r(coinpayments_api_call('get_tx_ids', array()));
?>