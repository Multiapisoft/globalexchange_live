<?php include_once '../lib/config.php';
$marr = array(0, 5000, 15000, 45000, 125000, 450000, 1250000, 2500000, 5000000, 10000000, 40000000, 40000000);
//$amtarr = array(0, 1.5, 5, 15, 40, 80, 225, 650, 1250, 1250);
$amtarr = array(0, 2, 5, 8, 20, 50, 100, 200, 500, 500);
$result = mysqli_query($link, "SELECT * FROM user WHERE topup > 0 AND reward <= 10 AND datetime >= '2023-10-01'");
while ($row = mysqli_fetch_object($result)) {
    $uid = $row->uid;
    $reward = 0;
    
    $childs_left = get_single_dimensional(get_child_levels_position($uid, 'L'));
    $childs_right = get_single_dimensional(get_child_levels_position($uid, 'R'));
    $amount_left = get_amount($childs_left);
    $amount_right = get_amount($childs_right);
    
    $i = 10;
    while($i <= 10 && $i > 0){
        $r = 0;
        if($row->reward > $i){
            //$r = 1;
        }
        elseif($row->reward == $i){
            $r = 1;
        }
        //elseif($matching >= $marr[$i]){
        elseif($amount_left >= ($marr[$i]) && $amount_right >= ($marr[$i])){
            $r = 1;
            mysqli_query($link, "UPDATE user SET reward = '".$i."' WHERE uid = '$uid'");
        }
        
        if($r && $i <= 8){
            $tr = get_sum('income_royalty', 'amount', "uid = '".$uid."' AND type = 0 AND level = '".$i."'") + $amtarr[$i];
            if($tr <= ($marr[$i]*0.15)){
                $new_amount = $amtarr[$i];
                royalty_insert($uid, $new_amount, $i);
                break;
            }
        }
        $i--;
    }
}

$tin = get_sum('investments', 'amount', "uid='" . $uid . "' AND statusc = 0")*0.03;

if($tin>0){
    $sq1 = my_query("SELECT uid FROM user WHERE topup>0 AND status = 0 AND reward = 2");
    $sq2 = my_query("SELECT uid FROM user WHERE topup>0 AND status = 0 AND reward = 9");
    $sq3 = my_query("SELECT uid FROM user WHERE topup>0 AND status = 0 AND reward = 10");
    
    $n1 = my_num_rows($sq1);
    $n2 = my_num_rows($sq2);
    $n3 = my_num_rows($sq3);
    
    $amt1 = ($n1 > 0) ? round(($tin / $n1), 4) : 0;
    $amt2 = ($n2 > 0) ? round(($tin / $n2), 4) : 0;
    $amt3 = ($n3 > 0) ? round(($tin / $n3), 4) : 0;
    
    if($amt3 > 0){
        while($row = my_fetch_object($sq3)){
            royalty_insert($row->uid, $amt3, 3, 1, $n1);
        }
    }
    if($amt2 > 0){
        while($row = my_fetch_object($sq2)){
            royalty_insert($row->uid, $amt2, 2, 1, $n1);
        }
    }
    if($amt1 > 0){
        while($row = my_fetch_object($sq1)){
            royalty_insert($row->uid, $amt1, 1, 1, $n1);
        }
    }
}


function royalty_insert($uid, $new_amount, $l, $type = 0, $n = 0){
    $tamt = $n * $new_amount;
    my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$uid."'");
    my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`, tamt, tid) VALUES ('".$uid ."', '".$new_amount."', '".date('c')."', '".$l."', '".$type."', '".$tamt."', '".$n."')");
    
    if($type == 1){
        $type = 3;
        $new_amount = ($l == 3) ? 25000 : ($l == 2 ? 25000 : 1000);
        $check = my_num_rows(my_query("SELECT uid FROM income_royalty WHERE uid='".$uid."' AND type = '".$type."' AND level = '".$l."'"));
        if(!$check){
            my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$uid."'");
            my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`) VALUES ('".$uid ."', '".$new_amount."', '".date('c')."', '".$l."', '".$type."')");
        }
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

my_query("UPDATE investments SET statusc=1 WHERE statusc=0");
echo "<br/> Closing complete. Please close this browser.";
?>