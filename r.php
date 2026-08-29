<?php 
function register($data, $session = 0) {
    $_status = 0;
    $_msg = "Transaction failed.";
    $plan_id = 1;
    $uid = createId();
    $refer_id = isset($data['refer_id']) ? tres($data['refer_id']) : 100;
    $refer = my_fetch_object(my_query("SELECT * FROM user WHERE uid='" . $refer_id . "'"));
    if (!$refer) {
        $refer_id = 100;
    }
    $address = $login_id = isset($data['login_id']) ? tres($data['login_id']) : (isset($data['address']) ? tres($data['address']) : $uid);
    $country = isset($data['country']) ? tres($data['country']) : 'IN';
    $_address = get_address_field($data);
    $iRow = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . $plan_id . "'"));
    $amount = $iRow->amount_from;

    $checktx = register_deposit($uid, $amount, $data);
    
    $position = isset($data['position']) && $data['position'] ? $data['position'] : '';
    //$child_ids = get_single_dimensional(get_child_levels($refer_id, 'yes'));
    
    //$check_position = my_query( "SELECT uid, position FROM user WHERE placement_id='$placement_id'");
    //$position_row = mysqli_fetch_object($check_position)->position;

    if ($refer_id == 0) {
        $_msg = "Invalid sponsor id.";
    } elseif (checkLoginId($login_id) == 0) {
        $_msg = "Invalid user id.";
    } elseif($checktx) {
        $placement_id = $refer_id;
        //$placement_id = get_terminal_id($refer_id, $position);
        
        $sql = "INSERT INTO `user` (`uid`, `login_id`, `refer_id`, `placement_id`, `position`, `country`, $_address, `datetime`) VALUES ('" . $uid . "', '" . $login_id . "', '" . $refer_id . "', '" . $placement_id . "', '" . $position . "', '" . $country . "', '" . $address . "', '" . date('c') . "')";
        my_query($sql);
        
        $last_insert_id = my_insert_id();
        
        if($last_insert_id){
            /*************************/
            $amount = $iRow->amount_from;
            $token = 0;
            
            if($session){
                $_SESSION['loginid'] = $login_id;
                $_SESSION['userid'] = $uid;
                $_SESSION['type'] = 0;
                
                /* login detail */
                my_query( "INSERT INTO user_login_detail (`uid`, `datetime`, `ip`) VALUES ('".$uid."', '".date('c')."', '".USER_IP."')");
            }
            
            $_status = 1;
            $_msg = "Success - thank you for register with us.";
        }
        
        $new_amount = 100;
        $account = $address;
        $ct = SITE_CURRENCY_TKN;
        
        my_query("UPDATE user SET wallet_token= wallet_token+'$new_amount' WHERE uid='".$uid."'");
        my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `type`) VALUES ('".$uid ."', '".$new_amount."', '".date('c')."', '2')");
        //my_query("INSERT INTO withdrawal_block (uid, amount, fee, net_amount, amount_coin, datetime, status, withdrawal_address, type, type2) VALUES ('" . $uid . "', '" . $new_amount . "', '0', '" . $new_amount . "', '" . $new_amount . "', '" . date('c') . "', 0, '" . $account . "', '" . $ct . "', '" . $ct . "')");
        
        $checkrefer = my_num_rows(my_query( "SELECT uid FROM user WHERE refer_id='" . $refer_id . "'"));
        if($checkrefer == 10 || $checkrefer == 25 || $checkrefer == 50){
            $new_amount = ($checkrefer >= 50) ? 1000 : (($checkrefer >= 25) ? 400 : 100);
            //my_query("UPDATE user SET wallet_token= wallet_token+'$new_amount' WHERE uid='".$refer_id."'");
            
            my_query("UPDATE user SET wallet_topup= wallet_topup+'".($new_amount/TKN_RATE_USD)."' WHERE uid='".$refer_id."'");
            my_query( "INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, tamt, `datetime`, `type`, `remark`) VALUES ('".$refer_id."', '".$refer_id."', '".($new_amount/TKN_RATE_USD)."','".$new_amount."', '".date('c')."', '4', 'Airdrop to topup R')");
            
            my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type) VALUES ('" .$refer_id ."','".$uid."','".$new_amount."','".date('c')."','0', 2)");
            //my_query("INSERT INTO withdrawal_block (uid, amount, fee, net_amount, amount_coin, datetime, status, withdrawal_address, type, type2) VALUES ('" . $uid . "', '" . $new_amount . "', '0', '" . $new_amount . "', '" . $new_amount . "', '" . date('c') . "', 0, '" . $account . "', '" . $ct . "', '" . $ct . "')");
        }
        
        /*************************/
        $top = get_top_level_uids2($uid, 12);

        $level_amount = array(0.06, 0.05, 0.04, 0.03, 0.02, 0.01, 0.005, 0.005, 0.005, 0.005, 0.005, 0.005);

        $i = 0;
        $level = count($top);
        if($level>20){$level=20;}
        if($level>0){
            while($i<$level){
                $value = $top[$i];
                if($i<6){$j=$i;}else{$j=6;}
                $percentage = $level_amount[$j];
                //$new_amount = $percentage * $amount;
                $new_amount = 5;
                $user2 = get_user_details($value);
                if($user2->topup > 0 || 1){
                    $checkrefer = mysqli_num_rows(my_query( "SELECT uid FROM user WHERE uid='" . $value . "' AND topup >= 0"));
                    if($checkrefer >= 10){
                        my_query("UPDATE user SET wallet_topup= wallet_topup+'".($new_amount/TKN_RATE_USD)."' WHERE uid='".$value."'");
                        my_query( "INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, tamt, `datetime`, `type`, `remark`) VALUES ('".$value."', '".$value."', '".($new_amount/TKN_RATE_USD)."', '".$new_amount."', '".date('c')."', '4', 'Airdrop to topup L')");
                    }
                    else{
                        my_query("UPDATE user SET wallet_token= wallet_token+'$new_amount' WHERE uid='".$value."'");
                    }

                    /*if($i==0){
                        my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, ipid, iamount) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."', '".$recid."', '".$iamount."')");
                    }
                    else{*/
                        $account = $user2->bnb_address;
                        my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."','".($i+1)."', 1)");
                        //my_query("INSERT INTO withdrawal_block (uid, amount, fee, net_amount, amount_coin, datetime, status, withdrawal_address, type, type2) VALUES ('" . $value . "', '" . $new_amount . "', '0', '" . $new_amount . "', '" . $new_amount . "', '" . date('c') . "', 0, '" . $account . "', '" . $ct . "', '" . $ct . "')");
                    //}
                }
                $i++;
            }
        }
        
        $user2 = get_user_details($refer_id);
        $checkrefer = mysqli_num_rows(my_query( "SELECT uid FROM user WHERE refer_id='" . $refer_id . "'"));
        if($checkrefer >= 10 && $user2->wallet_token >= 5){
            my_query("UPDATE user SET wallet_topup= wallet_topup+'".($user2->wallet_token/TKN_RATE_USD)."', wallet_token=0 WHERE uid='".$refer_id."'");
            my_query( "INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, tamt, `datetime`, `type`, `remark`) VALUES ('".$refer_id."', '".$refer_id."', '".($user2->wallet_token/TKN_RATE_USD)."', '".$user2->wallet_token."', '".date('c')."', '4', 'Airdrop to topup C')");
        }
        /*************************/
    }
    return array($_status, $_msg);
}

function register_deposit($uid, $amount_coin, $data) {
    return 1;
    $data_json = @json_encode($data);
    $status = isset($data['status']) ? $data['status'] : 1;
    $currency = isset($data['currency']) ? $data['currency'] : SITE_CURRENCY_;
    $txid = isset($data['transactionHash']) ? $data['transactionHash'] : (isset($data['txid']) ? tres($data['txid']) : '');

    //my_query("DELETE FROM `deposit_block_temp` WHERE txid = '" . $txid . "'");
    $checktx = 0;
    $check = my_num_rows(my_query("SELECT uid FROM deposit_block WHERE txid = '" . $txid . "'"));
    if ($check == 0 && $status) {
        $checktx = 1;
        $amount = $amount_coin;
        $fee = 0;
        $net_amount = $amount - $fee;
        //my_query("INSERT INTO deposit_block (uid, datetime, status, amount, fee, net_amount, amount_coin, txid, data, type) VALUES ('" . $uid . "', '" . date('c') . "', 1, '" . $amount . "', '" . $fee . "', '" . $net_amount . "', '" . $amount_coin . "', '" . $txid . "', '" . $data_json . "', '" . $currency . "')");
    }
    return $checktx;
}

function get_address_field($data) {
    $addressarr = array(SITE_CURRENCY_ => SITE_CURRENCY_);
    $currency = isset($data['currency']) ? $data['currency'] : SITE_CURRENCY_;
    $_address = (isset($addressarr[$currency])) ? $addressarr[$currency] : $currency;
    $_address = strtolower($_address) . '_address';
    return $_address;
}
?>