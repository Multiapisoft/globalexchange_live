<?php include_once '../lib/config.php';
user();
$uid = $_SESSION['userid'];
$wallet_field_arr = get_wallet_field();
$recid = isset($_POST['recid']) ? (int) tres($_POST['recid']) : 0;
$iRow = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='".$recid."'"));
if(isset($_POST) && $iRow){
    $user = get_user_details($uid);
    $uid2 = isset($_POST['uid']) ? (int) tres($_POST['uid']) : $uid;
    $user_2 = get_user_details($uid2);
    
    $type = (int) tres($_POST['type']);
    //$wallet_field = $wallet_field_arr[0];
    $wallet_field = 'wallet_topup';
    $wallet = $user->$wallet_field;
    
    $min = $iRow->amount_from;
    $max = $iRow->amount_to;
    $mul = $min;
    $recid =$iRow->recid;
    $amount = tres($_POST['amount']);
    
    if(checkDecimal($amount)==0){
        setMessage('Invalid amount.', 'error');
    }
    elseif(!in_array($type, array(0,1,2,3,4,5,6,7,8,9,10))){
        setMessage('Invalid amount.', 'error');
    }
    elseif($amount < 0){
        setMessage('Invalid amount.', 'error');
    }
    /*elseif(!in_array($type, array(0)) && $user_2->topup == 0){
        setMessage('Invalid amount.', 'error');
    }
    elseif(in_array($type, array(0)) && $user_2->topup > 0){
        setMessage('Invalid amount.', 'error');
    }
    elseif($iRow->recid <= $user_2->package && $iRow->recid <= 6){
        setMessage('Invalid amount.', 'error');
    }
    elseif($iRow->recid != ($user_2->package+1) && $iRow->recid <= 6){
        setMessage('Invalid amount.', 'error');
    }
    elseif(!$user_2->package && $iRow->recid == 7){
        setMessage('Invalid amount.', 'error');
    }*/
    /*elseif($amount < $user_2->topup){
        setMessage('Invalid amount.', 'error');
    }*/
    /*elseif(date('Y-m-d', strtotime($user_2->ex_date)) < date('Y-m-d')){
        setMessage('Please activate your ID.', 'error');
    }*/
    elseif($user_2->package == 2 && $iRow->recid == 2){
        setMessage('Invalid amount.', 'error');
    }
    /*elseif($user_2->package == 0 && $iRow->recid == 2){
        setMessage('Invalid amount.', 'error');
    }*/
    elseif($amount<$min || $amount>$max){
        setMessage('Invest min '.SITE_CURRENCY.''.$min.' and max '.SITE_CURRENCY.''.$max.' multiple of '.$mul.'.', 'error');
    }
    elseif($amount%$mul){
        setMessage('Invest min '.SITE_CURRENCY.''.$min.' and max '.SITE_CURRENCY.''.$max.' multiple of '.$mul.'.', 'error');
    }
    elseif($wallet<$amount){
        setMessage('Insufficient funds to cover this investment.', 'error');
    }
    else{
        my_query("UPDATE user SET $wallet_field=$wallet_field-'".($amount)."' WHERE uid='".$uid."'");
        
        if($recid == 2){
            $amount = $amount + 10;
        }
        
        $_uid = $uid;
        $uid = isset($_POST['uid']) ? (int) tres($_POST['uid']) : $uid;
        
        $camt = round($amount/B_RATE_, 2);
        $bamt = $amount;
        
        $iamount = $amount;
        $_package = ($recid < $user2->package) ? $user2->package : $recid;
        
        /*if($recid <= 3){
            $day = ($recid == 3) ? 36 : (($recid == 2) ? 12 : 6);
            if($user2->ex_date != '0000-00-00'){
                $ex_date = date('Y-m-d', strtotime('+'+$day+' months', strtotime($user_2->ex_date)));
            }
            else{
                $ex_date = date('Y-m-d', strtotime('+'+$day+' months', time()));
            }
            my_query("UPDATE user SET ex_date='".$ex_date."' WHERE uid='$uid'");
        }*/
        
        if($user_2->topup > 0){
            if($recid == 1){
                my_query("UPDATE user SET package='".$_package."', topup=topup+'$amount' WHERE uid='$uid'");
            }
            else{
                my_query("UPDATE user SET package='".$_package."' WHERE uid='$uid'");
            }
        }
        else{
            $topupa = ($recid == 1) ? $amount : 0;
            $tkna = ($recid == 1) ? 10 : 0;
            my_query("UPDATE user SET package='".$_package."', topup=topup+'$topupa', wallet_token = $tkna, topup_datetime='".date('c')."' WHERE uid='$uid'");
        }
        
        my_query("INSERT INTO investments (uid, amount, amount2, ipid, datetime, type, uid2, amount_coin, bonus) VALUES ('$uid', '$amount', '$amount', '$recid', '".date('c')."', '$type', '$_uid', '".$camt."', '".$bamt."')");
        
        /******************************************/
        /*$account = $user_2->bnb_address;
        $ct = SITE_CURRENCY_TKN;
        $new_amount = $bamt;
        my_query("INSERT INTO withdrawal_block (uid, amount, fee, net_amount, amount_coin, datetime, status, withdrawal_address, type, type2) VALUES ('" . $uid . "', '" . $new_amount . "', '0', '" . $new_amount . "', '" . $new_amount . "', '" . date('c') . "', 0, '" . $account . "', '" . $ct . "', '" . $ct . "')");
        */
        /**************************/
        
        $ruser = get_user_details($uid);
        $pool = $iRow->recid - 1;
        $pool2 = $pool+1;
        
        if($pool > 0){
            $pfld = "placement_id".$pool2;
            $ptfld = "pt".$pool2;
            $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '$uid'"));
            
            if($ruser->$pfld == 0 && ($pool <= 1 || $check >= 2)){
                $itype = ($pool <= 1) ? 3 : 4;
                //$placement = get_placement_id_by_pool(100, $pool2);
                $placement = get_placement_id_by_pool($ruser->refer_id, $pool2);
                my_query("UPDATE user SET $pfld = '".$placement."', $ptfld='".date('c')."' WHERE uid='$uid'");
                
                if($pool <= 1){
                    $top = get_top_level_uids_by_pool($uid, 5, $pool2);
                    
                    $level_amount = array(10, 3, 3, 4, 5, 0.05, 0.05, 0.05, 0.05, 0.02, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01);
            
                    $i = 0;
                    $level = count($top);
                    if($level>20){$level=20;}
                    if($level>0){
                        while($i<$level){
                            $value = $top[$i];
                            if($i<6){$j=$i;}else{$j=6;}
                            $percentage = $level_amount[$j];
                            /*$new_amount = ($pool == 3) ? 4.5 : (($pool == 2) ? 1.25 : 0.5);
                            
                            if($pool > 3){
                                $new_amount = $amount*0.1;
                            }*/
                            $new_amount = $percentage;
                            
                            $tgapi = get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=3");
                            
                            $umat = ($tgapi >= 200) ? $new_amount : 0;
                            $wamt = $new_amount - $umat;
                            
                            $user2 = get_user_details($value);
                            $wamt = check_3x($value, $wamt);
                            if($user2->topup > 0 && $new_amount > 0){
                                my_query("UPDATE user SET wallet= wallet+'$wamt' WHERE uid='".$value."'");
            
                                my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, ipid, iamount, pool, type, uamt, wamt) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."','".($i+1)."', '".$recid."', '".$iamount."', '".$pool."', '".$itype."', '".$uamt."', '".$wamt."')");
                            }
                            
                            if($tgapi == 280){
                                global_matrix($uid, $pool);
                            }
                            $i++;
                        }
                    }
                }
                else{
                    //global_matrix($uid, $pool-3);
                }
            }
        }
        
        /*************************/
        if($recid <= 1){
            $top = get_top_level_uids2($uid, 1);
    
            $level_amount = array(0.05, 0.05, 0.05, 0.05, 0.05, 0.05, 0.05, 0.05, 0.05, 0.02, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01);
    
            $i = 0;
            $level = count($top);
            if($level>20){$level=20;}
            if($level>0){
                while($i<$level){
                    $value = $top[$i];
                    if($i<6){$j=$i;}else{$j=6;}
                    $percentage = $level_amount[$j];
                    $new_amount = $percentage * $amount;
                    $user2 = get_user_details($value);
                    $new_amount = check_3x($value, $new_amount);
                    if($user2->topup > 0 && $new_amount > 0){
                        my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$value."'");
    
                        if($i==0){
                            my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, ipid, iamount) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."', '".$recid."', '".$iamount."')");
                        }
                        else{
                            my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, ipid, iamount) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."','".($i)."', '".$recid."', '".$iamount."')");
                        }
                    }
                    $i++;
                }
            }
        }
        /*************************/
        
        setMessage('Success - thank you for invest.', 'success');
    }
}

/* get top level uids */
function get_top_level_uids2($uid, $level=0, $arr=array()){
    global $link;
    $result = my_query("SELECT refer_id FROM user WHERE uid = '$uid'");
    if(count($arr)==$level && $level!=0){
        return $arr;
    }elseif($uid==100){
        return $arr;
    }
    if(my_num_rows($result)>0){
        $data = my_fetch_array($result);
        $arr[count($arr)] = $data[0];
        return get_top_level_uids2($data[0], $level, $arr);
    }else {
        return $arr;
    }
}

function global_matrix($uid, $pool, $level = 1){
    if($uid == 100){
        return;
    }
    $ruser = get_user_details($uid);
    $arr = array(
        array(30, 20, 20, 30, 40, 50, 60, 70, 80, 100, 0),
        array(5, 5, 20, 16, 128, 448, 14336, 917504, 117440512, 30064771072, 0),
        array(2, 2, 4, 16, 128, 448, 14336, 917504, 117440512, 30064771072, 0),
    );
    $amount = $arr[$pool-1][$level-1];
    $num = 2;
    if($level == 10){
        $num = 1024;
    }
    elseif($level == 9){
        $num = 512;
    }
    elseif($level == 8){
        $num = 256;
    }
    elseif($level == 7){
        $num = 128;
    }
    elseif($level == 6){
        $num = 64;
    }
    elseif($level == 5){
        $num = 32;
    }
    elseif($level == 4){
        $num = 16;
    }
    elseif($level == 3){
        $num = 8;
    }
    elseif($level == 2){
        $num = 4;
    }
    
    $nextamount = $arr[$pool-1][$level];
    $pool2 = (($pool-1)*10)+$level;
    
    $placement = get_placement_id_re(100, $num, $pool2);
    //$placement = get_placement_id_re($ruser->refer_id, $num, $pool2);
    $touid = $placement->uid;
    
    my_query("INSERT INTO `userre` (`uid`, `placement_id`, `placement_uid`, `datetime`, pool) VALUES ('" .$uid ."','".$placement->recid."','".$touid."','".date('c')."','".$pool2."')");
    $last_insert_id = my_insert_id();
    
    $uamt = ($level >= 10) ? 0 : round($nextamount/$num, 2);
    $uamt = ($uamt > 0) ? $uamt : 0;
    $camt = 0;
    $camt = ($camt > 0) ? $camt : 0;
    $wamt = $amount-$uamt-$camt;
    $wamt = ($wamt > 0) ? $wamt : 0;
    
    $wamt = check_3x($touid, $wamt);
    
    my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type, wamt, uamt, camt, pool) VALUES ('" .$touid."','".$uid."','".$amount."','".date('c')."','".($level)."', 4,'".$wamt."','".$uamt."','".$camt."', '".$pool."')");
    my_query("UPDATE user SET wallet= wallet+'$wamt' WHERE uid='".$touid."'");
    
    $tnum = my_num_rows(my_query( "SELECT recid FROM userre WHERE placement_id = '".$placement->recid."' ORDER BY datetime ASC"));
    if($touid != 100 && $tnum == $num && $uamt > 0 && $level <= 9){
        $level++;
        global_matrix($touid, $pool, $level);
    }
    
    /*$k = 1;
    while($k){
        $user2 = get_user_details($touid);
        if($touid != 100 && $user2->$wl >= $arr[$pool-1][0]){
            my_query("UPDATE user SET $wl= $wl-'".$arr[$pool-1][0]."' WHERE uid='".$touid."'");
            global_matrix($touid, $pool, 1);
        }
        else{
            $k = 0;
        }
    }*/
    return;
}

redirect('./invest_now.php?i='.$recid);
?>