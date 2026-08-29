<?php include_once '../lib/config.php';
user();
$uid = $_SESSION['userid'];
$wallet_field_arr = get_wallet_field();
$recid = isset($_POST['recid']) ? (int) tres($_POST['recid']) : 0;
$iRow = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='".$recid."'"));
if(isset($_POST) && $iRow){
    $user = get_user_details($uid);
    $uid2 = (int) tres($_POST['uid']);
    $user_2 = get_user_details($uid2);
    
    $type = (int) tres($_POST['type']);
    //$wallet_field = $wallet_field_arr[0];
    $wallet_field = 'wallet_topup';
    $wallet = $user->$wallet_field;
    
    $min = $iRow->amount_from;
    $max = $iRow->amount_to;
    $mul = 1;
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
    }*/
    elseif($iRow->recid <= $user_2->package && $iRow->recid <= 6){
        setMessage('Invalid amount.', 'error');
    }
    elseif($iRow->recid != ($user_2->package+1) && $iRow->recid <= 6){
        setMessage('Invalid amount.', 'error');
    }
    elseif(!$user_2->package && $iRow->recid == 7){
        setMessage('Invalid amount.', 'error');
    }
    /*elseif($amount < $user_2->topup){
        setMessage('Invalid amount.', 'error');
    }*/
    elseif($amount<$min || $amount>$max){
        setMessage('Invest min '.SITE_CURRENCY.''.$min.' and max '.SITE_CURRENCY.''.$max.' multiple of '.$mul.'.', 'error');
    }
    /*elseif($amount%$mul){
        setMessage('Invest min '.SITE_CURRENCY.''.$min.' and max '.SITE_CURRENCY.''.$max.' multiple of '.$mul.'.', 'error');
    }*/
    elseif($wallet<$amount){
        setMessage('Insufficient funds to cover this investment.', 'error');
    }
    else{
        my_query("UPDATE user SET $wallet_field=$wallet_field-'".($amount)."' WHERE uid='".$uid."'");
        
        $_uid = $uid;
        $uid = (int) tres($_POST['uid']);
        
        $camt = round($amount/B_RATE_, 2);
        $bamt = 0;
        
        $iamount = $amount;
        $_package = ($recid == 7) ? $user2->package : $recid;
        
        if($user_2->topup > 0){
            my_query("UPDATE user SET package='".$_package."', topup=topup+'$amount' WHERE uid='$uid'");
        }
        else{
            my_query("UPDATE user SET package='".$_package."', topup=topup+'$amount', topup_datetime='".date('c')."' WHERE uid='$uid'");
        }
        
        my_query("INSERT INTO investments (uid, amount, amount2, ipid, datetime, type, uid2, amount_coin, bonus) VALUES ('$uid', '$amount', '$amount', '$recid', '".date('c')."', '$type', '$_uid', '".$camt."', '".$bamt."')");
        
        $pool = $iRow->recid;
        $ruser = get_user_details($uid);
        
        $user2 = get_user_details($ruser->refer_id);
        if($user2->topup > 0){
            $new_amount = $amount*0.05;
            /*$tinv = get_sum('investments', 'amount', "uid='".$ruser->refer_id."'")*4;
            $tinc = get_sum('income_direct', 'amount', "uid='".$ruser->refer_id."'")+get_sum('income_binary', 'amount', "uid='".$ruser->refer_id."'")+get_sum('income_growth', 'amount', "uid='".$ruser->refer_id."'")+get_sum('income_royalty', 'amount', "uid='".$ruser->refer_id."'")+get_sum('income_level', 'amount', "uid='".$ruser->refer_id."'");
            $tincn = $tinc + $new_amount;
            
            if($tinv < $tincn){
                $new_amount = $tinv - $tinc;
            }
            
            if($new_amount < 0){
                $new_amount = 0;
            }*/
            
            /*if($new_amount){
                my_query("UPDATE user SET wallet_promo= wallet_promo+'$new_amount' WHERE uid='".$ruser->refer_id."'");
                my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, `level`, pool) VALUES ('" .$ruser->refer_id ."','".$uid."','".$new_amount."','".date('c')."','0', '".$pool."')");
            }*/
        }
        
        /*if($type == 0){
            $_refer_id = 100;
            $placement_id = get_placement_id_by_pool($_refer_id, $pool);
            my_query("UPDATE user SET placement_id='".$placement_id."', pt = '".date('c')."' WHERE uid='$uid' AND uid != 100");
            if($ruser->refer_id){
                $placement_id = get_placement_id_by_pool($ruser->refer_id, 2);
                my_query("UPDATE user SET placement_id2='".$placement_id."', pt2 = '".date('c')."' WHERE uid='$uid' AND uid != 100");
            }
        }
        elseif($iRow->recid > $user_2->package){
            $placement_id = get_placement_id_by_pool(100, $pool);
            if($pool<2){
                $pool = 2;
            }
            elseif($pool>10){
                $pool = 10;
            }
            $pfield = 'placement_id'.$pool;
            $_pt = 'pt'.$pool;
            my_query("UPDATE user SET $pfield='".$placement_id."', $_pt = '".date('c')."' WHERE uid='$uid' AND uid != 100");
        }*/
        
        /*if($ruser->refer_id){
            $user2 = get_user_details($ruser->refer_id);
            if($user2->topup > 0){
                $new_amount = $amount*0.15;
                $tinv = get_sum('investments', 'amount', "uid='".$ruser->refer_id."'")*4;
                $tinc = get_sum('income_direct', 'amount', "uid='".$ruser->refer_id."'")+get_sum('income_binary', 'amount', "uid='".$ruser->refer_id."'")+get_sum('income_growth', 'amount', "uid='".$ruser->refer_id."'")+get_sum('income_royalty', 'amount', "uid='".$ruser->refer_id."'")+get_sum('income_level', 'amount', "uid='".$ruser->refer_id."'");
                $tincn = $tinc + $new_amount;
                
                if($tinv < $tincn){
                    $new_amount = $tinv - $tinc;
                }
                
                if($new_amount < 0){
                    $new_amount = 0;
                }
                
                if($new_amount){
                    my_query("UPDATE user SET wallet_promo= wallet_promo+'$new_amount' WHERE uid='".$ruser->refer_id."'");
                    my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, `level`, pool) VALUES ('" .$ruser->refer_id ."','".$uid."','".$new_amount."','".date('c')."','0', '".$pool."')");
                }
                
                $user3 = get_user_details($user2->refer_id);
                if($user3->topup > 0 && $new_amount){
                    $new_amount = $new_amount*0.1;
                    $tinv = get_sum('investments', 'amount', "uid='".$user2->refer_id."'")*4;
                    $tinc = get_sum('income_direct', 'amount', "uid='".$user2->refer_id."'")+get_sum('income_binary', 'amount', "uid='".$user2->refer_id."'")+get_sum('income_growth', 'amount', "uid='".$user2->refer_id."'")+get_sum('income_royalty', 'amount', "uid='".$user2->refer_id."'")+get_sum('income_level', 'amount', "uid='".$user2->refer_id."'");
                    $tincn = $tinc + $new_amount;
                    
                    if($tinv < $tincn){
                        $new_amount = $tinv - $tinc;
                    }
                    
                    if($new_amount < 0){
                        $new_amount = 0;
                    }
                    
                    if($new_amount){
                        my_query("UPDATE user SET wallet_promo= wallet_promo+'$new_amount' WHERE uid='".$user2->refer_id."'");
                        my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`) VALUES ('" .$user2->refer_id ."','".$user2->uid."','".$new_amount."','".date('c')."')");
                    }
                }
            }
        }*/
        
        /*************************/
        if($recid != 7){
            $top = get_top_level_uids2($uid, 10);
    
            $level_amount = array(0.4, 0.03, 0.03, 0.03, 0.03, 0.02, 0.02, 0.02, 0.02, 0.02, 0.02, 0.02);
    
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
                    if($user2->topup > 0){
                        my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$value."'");
    
                        if($i==0){
                            my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, ipid, iamount) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."', '".$recid."', '".$iamount."')");
                        }
                        else{
                            my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, ipid, iamount) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."','".($i+1)."', '".$recid."', '".$iamount."')");
                        }
                    }
                    $i++;
                }
            }
            if($uid != 100){
                upgrade_pool2($uid, $recid);
            }
        }
        else{
            if($uid != 100){
                upgrade_pool($uid, $recid);
            }
        }
        /*************************/
        
        setMessage('Success - thank you for invest.', 'success');
    }
}

function upgrade_pool($uid, $pool = 1, $level = 1){
    $user2 = get_user_details($uid);
    $refer_id = ($user2->refer_id) ? $user2->refer_id : 100;
    $ruser2 = get_user_details($refer_id);
    $amt_arr = array(10, 14, 44, 256, 2560, 32768, 524288, 0);
    $new_amount = $amt_arr[$pool-7];
    $new_amount2 = $amt_arr[$pool-6];
    
    $num = 2;
    if($pool == 8){
        $num = 4;
    }
    elseif($pool == 9){
        $num = 8;
    }
    elseif($pool == 10){
        $num = 16;
    }
    elseif($pool == 11){
        $num = 32;
    }
    elseif($pool == 12){
        $num = 64;
    }
    elseif($pool == 13){
        $num = 128;
    }
    $placement = get_placement_id_re(100, $num, $pool);
    $touid = $placement->uid;
    
    my_query("INSERT INTO `userre` (`uid`, `placement_id`, `placement_uid`, `datetime`, pool) VALUES ('" .$uid ."','".$placement->recid."','".$touid."','".date('c')."','".$pool."')");
    $last_insert_id = my_insert_id();
    
    
    $placement_id = $placement->recid;
    $placement_uid = $placement->uid;
    
    $check = my_num_rows(my_query( "SELECT * FROM userre WHERE placement_id = '$placement_id'"));

    if($check <= $num){
        if($check != $num || 1){
            if($new_amount2 >= $new_amount*$check){
                $wamt = 0;
            }
            elseif($new_amount*($check-1) >= $new_amount2){
                $wamt = $new_amount;
            }
            else{
                $wamt = ($new_amount*$check)%$new_amount2;
            }
            
            $uamt = $new_amount - $wamt;
            $camt = 0;
            $user3 = get_user_details($placement_uid);
            $checkd = my_num_rows(my_query( "SELECT * FROM user WHERE refer_id = '$placement_uid' AND topup > 0"));
            if($pool >= 9 && ($user3->package < 3 || $checkd < 2)){
                $camt = $wamt;
            }
            elseif($pool >= 10 && ($user3->package < 5 || $checkd < 5)){
                $camt = $wamt;
            }
            elseif($pool >= 11 && ($user3->package < 6) || $checkd < 15){
                $camt = $wamt;
            }
            
            $wamt = $wamt - $camt;
            
            my_query("UPDATE user SET wallet= wallet+'$wamt' WHERE uid='".$placement_uid."'");
            my_query("INSERT INTO `income_level` (ipid, `uid`, `from_uid`, `amount`, `datetime`, `level`, type, wamt, uamt, camt, pool) VALUES ('".$pool."' ,'" .$placement_uid ."','".$uid."','".$new_amount."','".date('c')."','".($level)."', 2,'".$wamt."','".$uamt."','".$camt."','".$pool."')");
        }
        if($check == $num && $placement_uid != 100){
            $pool++;
            upgrade_pool($placement_uid, $pool);
        }
    }
}

function upgrade_pool2($uid, $pool = 1){
    $user2 = get_user_details($uid);
    $refer_id = ($user2->refer_id) ? $user2->refer_id : 100;
    $ruser2 = get_user_details($refer_id);
    $amt_arr = array(
        array(2.5, 5, 10, 20, 40, 55, 0),
    );
    $new_amount = $amt_arr[$pool-1];
    
    $num = 2;
    $placement = get_placement_id_re(100, $num, $pool);
    $touid = $placement->uid;
    
    my_query("INSERT INTO `userre` (`uid`, `placement_id`, `placement_uid`, `datetime`, pool) VALUES ('" .$uid ."','".$placement->recid."','".$touid."','".date('c')."','".$pool."')");
    $last_insert_id = my_insert_id();
    
    $top = get_top_level_uids_by_pool_re($last_insert_id, 3, $pool);
    
    $placement_id = $top[1];
    $placement_uid = my_fetch_object(my_query( "SELECT * FROM userre WHERE recid = '$placement_id'"))->uid;
    $childs_uids = get_single_dimensional(get_child_levels_by_pool_re($placement_id, $pool, '', 4));
    $check = count($childs_uids);

    if($check <= 6){
        $check2 = my_num_rows(my_query( "SELECT * FROM income_level WHERE uid = '$placement_uid' AND type = 3 AND pool = '".$pool."'"));
        if($check <= 4 && $check2 < 4){
            $wamt = $new_amount;
            $uamt = 0;
            
            my_query("UPDATE user SET wallet= wallet+'$wamt' WHERE uid='".$placement_uid."'");
            my_query("INSERT INTO `income_level` (ipid, `uid`, `from_uid`, `amount`, `datetime`, `level`, type, wamt, uamt, pool) VALUES ('".$pool."' ,'" .$placement_uid ."','".$uid."','".$new_amount."','".date('c')."','".($level)."', 3,'".$wamt."','".$uamt."','".$pool."')");
        }
        if($check == 6 && $placement_uid != 100){
            upgrade_pool2($placement_uid, $pool);
        }
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
redirect('./invest_now.php?i='.$recid);
?>