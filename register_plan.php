<?php 
function register_plan($uid, $refer_id, $pool = 1) {
    if($pool == 1){
        
    }
}

function register_plan_buy($uid, $amount, $recid) {
    /*************************/
    /*$refer = my_fetch_object(my_query("SELECT * FROM user WHERE uid='" . $uid . "'"));
    $refer_id = $refer->refer_id;
    if($recid == 1){
        $new_amount = $amount*0.1;
        my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$refer_id."'");
        my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`) VALUES ('" .$refer_id ."','".$uid."','".$new_amount."','".date('c')."')");
    }*/
    
    /*$top = get_top_level_uids($uid, 8);

    $level_amount = array(0.08, 0.04, 0.03, 0.02, 0.02, 0.01, 0.005, 0.005, 0.005, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001);
    //$level_amount = array(10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);

    $i = 0;
    $level = count($top);
    if($level>15){$level=15;}
    if($level>0){
        while($i<$level){
            $value = $top[$i];
            if($i<15){$j=$i;}else{$j=15;}
            $percentage = $level_amount[$j];
            $new_amount = $percentage * $amount;
            //$new_amount = $percentage;
            $user2 = get_user_details($value);
            $dcheck = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '$value' AND status = 0 AND topup > 0"));
            
            if($user2->topup > 0 && $dcheck > $i){
                my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$value."'");

                /*if($i==0){
                    my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."')");
                }
                else{*
                    my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."','".($i+1)."')");
                //}
                
                if($i == 0){
                    $refer = my_fetch_object(my_query("SELECT * FROM user WHERE uid='" . $value . "'"));
                    $refer_id = $refer->refer_id;
                    $new_amount = $new_amount*0.2;
                    my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$refer_id."'");
                    my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type) VALUES ('" .$refer_id ."','".$value."','".$new_amount."','".date('c')."','".($i+1)."', 1)");
                }
            }
            $i++;
        }
    }*/
    /*************************/
}

function register_plan_buy2($uid, $amount, $recid) {
    /*************************/
    if($recid <= 8){
        $user = my_fetch_object(my_query("SELECT * FROM user WHERE uid='" . $uid . "'"));
        if($user->placement_id2 == 0 && $uid != 100){
            $placement_id = get_placement_id_by_pool(100, 2, 1);
            my_query("UPDATE user SET placement_id2='".$placement_id."', pt2 = '".date('c')."' WHERE uid='$uid' AND uid != 100");
        }
        /*************************/
        /*$refer = my_fetch_object(my_query("SELECT * FROM user WHERE uid='" . $uid . "'"));
        $refer_id = $refer->refer_id;
        if($recid == 1){
            $new_amount = 500;
            my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$refer_id."'");
            my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`) VALUES ('" .$refer_id ."','".$uid."','".$new_amount."','".date('c')."')");
        }*/
        /*************************/
        //else{
            //community($uid, $refer_id, $recid);
        //}
        
        $top = get_top_level_uids($uid, 7);

        $level_amount = array(0.2, 0.1, 0.03, 0.03, 0.1, 0.02, 0.02, 0.003, 0.002, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001);
        //$level_amount = array(10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);

        $i = 0;
        $level = count($top);
        if($level>15){$level=15;}
        if($level>0){
            while($i<$level){
                $value = $top[$i];
                if($i<15){$j=$i;}else{$j=15;}
                $percentage = $level_amount[$j];
                $new_amount = $percentage * $amount;
                //$new_amount = $percentage;
                $user2 = get_user_details($value);
                if($user2->topup > 0){
                    my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$value."'");

                    /*if($i==0){
                        my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."')");
                    }
                    else{*/
                        my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."','".($i+1)."')");
                    //}
                }
                $i++;
            }
        }
        
        /* =============== */
        $top = get_top_level_uids_by_pool($uid, 30, 2);

        $level_amount = array(0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01, 0.01);
        //$level_amount = array(10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);

        $i = 0;
        $level = count($top);
        if($level>15){$level=15;}
        if($level>0){
            while($i<$level){
                $value = $top[$i];
                if($i<15){$j=$i;}else{$j=15;}
                $percentage = $level_amount[$j];
                $new_amount = $percentage * $amount;
                //$new_amount = $percentage;
                $user2 = get_user_details($value);
                if($user2->topup > 0){
                    my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$value."'");

                    /*if($i==0){
                        my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."')");
                    }
                    else{*/
                        my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."','".($i+1)."', 1)");
                    //}
                    
                    if($i < 20){
                        $new_amount = $percentage * $user2->topup;
                        my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$uid."'");
                        my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type) VALUES ('" .$uid."','".$value."','".$new_amount."','".date('c')."','".(-($i+1))."', 2)");
                    }
                }
                $i++;
            }
        }
        /* =============== */
    }
    /*************************/
    else{
        //$pool = $recid;
        //smart($recid, $uid, $pool);
    }
    /*************************/
}

function community($uid, $refer_id, $pool = 1){
    $amt_arr = array(
        1 => array(1000, 1000, 0, 0),
        2 => array(4000, 2000, 0, 0),
        3 => array(16000, 6000, 0, 0),
        4 => array(96000, 18000, 0, 0),
        5 => array(576000, 50000, 0, 0),
        6 => array(3200000, 100000, 0, 0),
        7 => array(12800000, 0, 0, 0)
    );
    
    $amt_arr = array(
        1 => array(1000, 0, 0, 0),
        2 => array(4000, 0, 0, 0),
        3 => array(16000, 0, 0, 0),
        4 => array(96000, 0, 0, 0),
        5 => array(576000, 0, 0, 0),
        6 => array(3200000, 0, 0, 0),
        7 => array(12800000, 0, 0, 0)
    );
    
    $l = $level = $pool;
    $_num = 1;
    while($l > 0){
        $_num = $_num*2;
        $l--;
    }
    
    $pfield = ($pool > 1) ? 'placement_id'.$pool : 'placement_id';
    $_pt = ($pool > 1) ? 'pt'.$pool : 'pt';
    
    $refer_id = _get_refer_id_by_pool($refer_id, $pfield);
    if($pool == 1){
        $refer_id2 = $refer_id;
    }
    else{
        $top = get_top_level_uids($uid, 10);
        $top_id = (isset($top[$pool])) ? $top[$pool] : 100;
        $refer_id2 = _get_refer_id_by_pool2($top_id, $pfield);
    }
    
    $placement_id = get_placement_id_by_pool($refer_id2, $pool, $_num);
    
    my_query("UPDATE user SET $pfield='".$placement_id."', $_pt = '".date('c')."' WHERE uid='$uid' AND uid != 100");
    
    if($pool > 1){
        $uamt = ($amt_arr[$level][1] == 0) ? 0 : $amt_arr[$level][1]/$_num;
        $amount = ($amt_arr[$level][0] == 0) ? 0 : $amt_arr[$level][0]/$_num;
        $income = $amount - $uamt;
        //$wamt = $new_amount = ($amt_arr[$level][2] == 0) ? 0 : $amt_arr[$level][2]/$_num;
        //$camt = ($amt_arr[$level][3] == 0) ? 0 : $amt_arr[$level][3]/$_num;
        
        $wamt = $income*0.6;
        $camt = $income*0.2;
        $samt = $income*0.2;
        
        my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type, wamt, uamt, camt, samt) VALUES ('" .$placement_id ."','".$uid."','".$amount."','".date('c')."','".($level)."', 0,'".$wamt."','".$uamt."','".$camt."','".$samt."')");
        my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$placement_id."'");
    
        $new_amount = $samt;
        my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$refer_id."'");
        my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, type) VALUES ('" .$refer_id ."','".$uid."','".$new_amount."','".date('c')."', 1)");
    }
    
    //$check = my_num_rows(my_query("SELECT * FROM income_level WHERE uid='".$placement_id."' AND type = 0 AND level = '".($level)."'"));
    //if($pool < 7 && $check == $_num){
        //$pool++;
        //community($placement_id, $refer_id, $pool);
    //}
}

function smart($planid, $uid, $pool = 8, $level = 1){
    $amt_arr = array(
        8 => array(6000, 0, 0),
        9 => array(36000, 0, 0),
        10 => array(15000, 0, 0),
        11 => array(90000, 0, 0),
        12 => array(150000, 0, 0),
        13 => array(900000, 0, 0),
        14 => array(300000, 0, 0),
        15 => array(1800000, 0, 0),
        16 => array(1500000, 0, 0),
        17 => array(9000000, 0, 0),
        18 => array(15000000, 0, 0),
        19 => array(90000000, 0, 0)
    );
    
    $type = 1;
    if($pool <= 9){
        $type = 1;
    }
    elseif($pool <= 11){
        $type = 2;
    }
    elseif($pool <= 13){
        $type = 3;
    }
    elseif($pool <= 15){
        $type = 4;
    }
    elseif($pool <= 17){
        $type = 5;
    }
    elseif($pool <= 19){
        $type = 6;
    }
    
    if($pool == 9 || $pool == 11 || $pool == 13 || $pool == 15 || $pool == 17 || $pool == 19){
        $level = 2;
    }
    
    $l = $level;
    $_num = 1;
    while($l > 0){
        $_num = $_num*3;
        $l--;
    }
    
    $placement_id = get_placement_id_by_pool(100, $pool, $_num);
    $pfield = 'placement_id'.$pool;
    $_pt = 'pt'.$pool;
    my_query("UPDATE user SET $pfield='".$placement_id."', $_pt = '".date('c')."' WHERE uid='$uid' AND uid != 100");
    
    //$wamt = $new_amount = $amt_arr[$pool][2]/$_num;
    $uamt = ($amt_arr[$pool][1] == 0) ? 0 : $amt_arr[$pool][1]/$_num;
    $amount = ($amt_arr[$pool][0] == 0) ? 0 : $amt_arr[$pool][0]/$_num;
    $income = $amount - $uamt;
    
    $wamt = $income*0.8;
    $camt = $income*0.2;
    
    my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type, wamt, uamt, camt) VALUES ('" .$placement_id ."','".$uid."','".$amount."','".date('c')."','".($level)."', $type,'".$wamt."','".$uamt."','".$camt."')");
    my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$placement_id."'");
    
    //$check = my_num_rows(my_query("SELECT * FROM income_level WHERE uid='".$placement_id."' AND type = $type AND level = '".($level)."'"));
    //if($level < 2 && $check == $_num){
        //$level++;
        //smart($planid, $placement_id, $pool, $level);
    //}
}

function _get_refer_id_by_pool($refer_id, $pfield){
    $ruser = get_user_details($refer_id);
    if($refer_id == 100 || ($ruser && $ruser->$pfield)){
        return $refer_id;
    }
    elseif($ruser){
        return _get_refer_id_by_pool($ruser->refer_id, $pfield);
    }
    else{
        return 100;
    }
}

function _get_refer_id_by_pool2($refer_id, $pfield){
    $ruser = get_user_details($refer_id);
    if($refer_id == 100 || ($ruser && $ruser->$pfield)){
        return $refer_id;
    }
    elseif($ruser){
        return _get_refer_id_by_pool2($ruser->placement_id, $pfield);
    }
    else{
        return 100;
    }
}

/* get placement id */
function _get_placement_id_($refer_id, $pool, $unum = 3, $type = 0){
    global $link;
    $level = array();
    $refer_id = ($refer_id) ? $refer_id : 100;
    $placement_id = 0;
    
    /*************/
    $recid = my_fetch_object(my_query("SELECT recid FROM income_level WHERE from_uid = '$refer_id' AND type = $type AND pool = $pool AND is_reopen = 1 ORDER BY recid DESC LIMIT 1"))->recid;
    $recid = ($type == 4) ? $recid : 0;
    if(!$recid){
        $recid = my_fetch_object(my_query("SELECT recid FROM income_level WHERE type = $type AND pool = $pool ORDER BY recid ASC LIMIT 1"))->recid;
        $recid = ($recid) ? $recid : 0;
    }
    elseif($recid && !my_num_rows(my_query("SELECT recid FROM income_level WHERE pid = '$recid' AND type = $type AND pool = $pool"))){
        //$recid = 0;
        return $recid;
    }
    //$recid = 0;
    /*************/
    
    $result = my_query( "SELECT recid FROM income_level WHERE pid = '$recid' AND type = $type AND pool = $pool ORDER BY recid ASC");
    $num = my_num_rows($result);
    if ($num>=$unum) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->recid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query( "SELECT recid FROM income_level WHERE pid = '$value' AND type = $type AND pool = $pool ORDER BY recid ASC");
                if(my_num_rows($result) >= $unum){
                    while ($row = my_fetch_object($result)) {
                        $level[$i + 1][] = $row->recid;
                    }
                }
                else{
                    $placement_id = $value;
                    return $placement_id;
                    break;
                }
            }
            if (!empty($level[$i + 1])) {
                $i++;
                continue;
            }else {
                break;
            }
        }
    }
    else{
        $placement_id = $recid;
    }
    
    return $placement_id;
}

function get_color($uid, $fuid, $pool = 0){
    $color = '';
    $user = get_user_details($uid);
    $fuser = get_user_details($fuid);
    
    if($fuser->refer_id == $uid){
        $color = 'blue'; //refer
    }
    elseif($fuser->recid == $user->recid){
        $color = '#e1b62d';
    }
    elseif($fuser->recid > $user->recid){
        $color = '#pink'; // up
    }
    elseif($fuser->recid < $user->recid){
        $color = '#lightgreen'; // down
    }
    return $color;
}
?>