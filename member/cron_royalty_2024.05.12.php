<?php include_once '../lib/config.php';
$marr = array(0, 5000, 15000, 45000, 125000, 450000, 1250000, 2500000, 5000000, 10000000, 40000000, 40000000);
$amtarr = array(0, 2, 5, 8, 20, 50, 100, 200, 500, 500);
$result = mysqli_query($link, "SELECT * FROM user WHERE topup > 0 AND reward <= 4");
while ($row = mysqli_fetch_object($result)) {
    $uid = $row->uid;
    $reward = 0;
    
    $childs = get_child_levels($uid);
    
    if($reward == 0 && isset($childs[1]) && count($childs[1]) == 2 && isset($childs[2]) && count($childs[2]) == 4 && isset($childs[3]) && count($childs[3]) == 8 && isset($childs[4]) && count($childs[4]) == 16){
        $reward++;
    }
    
    $tr = get_team_rank($id_array, $reward);
    $check = mysqli_num_rows(mysqli_query($link, "SELECT uid FROM user WHERE refer_id = '".$uid."' AND reward = '".$r."'"));
    
    if($reward == 3 && $row->package >= 6 && $tr >= 3 && $check >= 2 && $check < $tr){
        $reward++;
    }
    if($reward == 2 && $row->package >= 4 && $tr >= 3 && $check >= 2 && $check < $tr){
        $reward++;
    }
    if($reward == 1 && $row->package >= 3 && $tr >= 3 && $check >= 2 && $check < $tr){
        $reward++;
    }
    
    mysqli_query($link, "UPDATE user SET reward = '".$reward."' WHERE uid = '$uid'");
}

$tin = get_sum('investments', 'amount', "uid='" . $uid . "' AND statusc = 0");
$tin4 = $tin*0.04;
$tin3 = $tin*0.03;
$tin2 = $tin*0.02;

if($tin>0){
    $sq1 = my_query("SELECT uid FROM user WHERE topup>0 AND status = 0 AND reward = 1");
    $sq2 = my_query("SELECT uid FROM user WHERE topup>0 AND status = 0 AND reward = 2");
    $sq3 = my_query("SELECT uid FROM user WHERE topup>0 AND status = 0 AND reward = 3");
    $sq4 = my_query("SELECT uid FROM user WHERE topup>0 AND status = 0 AND reward = 4");
    
    $n1 = my_num_rows($sq1);
    $n2 = my_num_rows($sq2);
    $n3 = my_num_rows($sq3);
    $n4 = my_num_rows($sq4);
    
    $amt1 = ($n1 > 0) ? round(($tin4 / $n1), 4) : 0;
    $amt2 = ($n2 > 0) ? round(($tin3 / $n2), 4) : 0;
    $amt3 = ($n3 > 0) ? round(($tin2 / $n3), 4) : 0;
    $amt4 = ($n3 > 0) ? round(($tin2 / $n4), 4) : 0;
    
    if($amt4 > 0){
        while($row = my_fetch_object($sq4)){
            royalty_insert($row->uid, $amt4, 4, 0, $n4);
        }
    }
    if($amt3 > 0){
        while($row = my_fetch_object($sq3)){
            royalty_insert($row->uid, $amt3, 3, 0, $n3);
        }
    }
    if($amt2 > 0){
        while($row = my_fetch_object($sq2)){
            royalty_insert($row->uid, $amt2, 2, 0, $n2);
        }
    }
    if($amt1 > 0){
        while($row = my_fetch_object($sq1)){
            royalty_insert($row->uid, $amt1, 1, 0, $n1);
        }
    }
}


function royalty_insert($uid, $new_amount, $l, $type = 0, $n = 0){
    $check = my_num_rows(my_query("SELECT uid FROM income_royalty WHERE uid='".$uid."' AND type = '".$type."' AND level = '".$l."'"));
    if(!$check){
        $tamt = $n * $new_amount;
        my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$uid."'");
        my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`, tamt, tid) VALUES ('".$uid ."', '".$new_amount."', '".date('c')."', '".$l."', '".$type."', '".$tamt."', '".$n."')");
    }
}

function get_amount($id_array){
    global $link;
    $amount = 0;
    if(!empty ($id_array)){
        $uid_in = implode(" , ", $id_array);
        //$amount = mysqli_fetch_object(mysqli_query($link, "SELECT sum(amount) as amount FROM income_growth WHERE uid IN ( $uid_in )"))->amount;
        $amount = mysqli_fetch_object(mysqli_query($link, "SELECT sum(amount) as amount FROM investments WHERE uid IN ( $uid_in )"))->amount;
    }
    return $amount;
}

function get_team_rank($id_array, $r = 1){
    global $link;
    $amount = 0;
    if(!empty ($id_array)){
        $uid_in = implode(" , ", $id_array);
        $amount = mysqli_num_rows(mysqli_query($link, "SELECT uid FROM user WHERE uid IN ( $uid_in ) AND reward = '".$r."'"));
    }
    return $amount;
}

my_query("UPDATE investments SET statusc=1 WHERE statusc=0");
echo "<br/> Closing complete. Please close this browser.";
?>