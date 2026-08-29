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
    
    $wallet_field2 = 'wallet_token';
    //$wallet2t = $user->$wallet_field2;
    $wallet2t = 0;
    
    $wallet2 = $wallet2t * TKN_RATE_USD;
    
    $wallet3 = $wallet + $wallet2;
    
    $min = $iRow->amount_from - $user_2->topup;
    $max = $iRow->amount_to - $user_2->topup;
    //$mul = $min;
    $mul = 1;
    $recid =$iRow->recid;
    $amount = tres($_POST['amount']) - $user_2->topup;
    
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
    elseif($iRow->recid <= $user_2->package){
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
    //elseif($wallet3<$amount){
        setMessage('Insufficient funds to cover this investment.', 'error');
    }
    elseif($wallet<$amount*0.5){
        setMessage('Insufficient funds to cover this investment.', 'error');
    }
    else{
        /*if($wallet2 >= $amount*0.5){
            my_query("UPDATE user SET $wallet_field=$wallet_field-'".($amount*0.5)."', $wallet_field2=$wallet_field2-'".($amount*0.5/TKN_RATE_USD)."' WHERE uid='".$uid."'");
        }
        else{
            my_query("UPDATE user SET $wallet_field=$wallet_field-'".($amount - $wallet2)."', $wallet_field2=0 WHERE uid='".$uid."'");
        }*/
        
        my_query("UPDATE user SET $wallet_field=$wallet_field-'".($amount)."' WHERE uid='".$uid."'");
        
        $_uid = $uid;
        $uid = (int) tres($_POST['uid']);
        
        $camt = round($amount/B_RATE_, 2);
        
        //$carr = array(200, 400, 800, 1200, 2000, 4000);
        //$camt = $carr[$recid-1];
        
        $bamt = 0;
        
        /*$bck = get_sum('investments', 'amount', "uid='".$uid."'");
        $tbck = my_num_rows(my_query("SELECT uid FROM investments WHERE bonus > 0"));
        
        if($bck == 0 && $tbck < 1000){
            $bamt = 1000;
        }*/
        
        $iamount = $amount;
        
        if($user_2->topup > 0){
            my_query("UPDATE user SET package='".$recid."', topup=topup+'$amount' WHERE uid='$uid'");
        }
        else{
            my_query("UPDATE user SET package='".$recid."', topup=topup+'$amount', topup_datetime='".date('c')."' WHERE uid='$uid'");
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
        $top = get_top_level_uids2($uid, 1);

        $level_amount = array(0.06, 0.05, 0.04, 0.03, 0.02, 0.01, 0.005, 0.005, 0.005, 0.005, 0.005, 0.005);

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
redirect('./invest_now.php?i='.$recid);
?>