<?php include_once '../lib/config.php';
user();
$uid = $_SESSION['userid'];

if (isset($_GET['recid']) && $_GET['recid']) {
    $recid = (int) $_GET['recid'];
    
    $datetime_120 = date('Y-m-d H:i:s', strtotime('-90 days', strtotime(date('c'))));
    $check = my_fetch_object(my_query("SELECT * FROM investments WHERE recid='".$recid."' AND uid='".$uid."' AND status = 0 AND amount2 > 0 AND datetime <= '" . $datetime_120 . "'"));
    
    if ($check) {
        $time_floor = floor((strtotime(date('c')) - strtotime($check->datetime)) / (60 * 60 * 24));
        $amt = 0;
        
        /*if($time_floor >= 690){
            $amt = $check->amount2;
        }
        elseif($time_floor >= 660){
            $amt = $check->amount2-$check->amount*0.05;
        }
        elseif($time_floor >= 630){
            $amt = $check->amount2-$check->amount*0.10;
        }
        elseif($time_floor >= 600){
            $amt = $check->amount2-$check->amount*0.15;
        }
        elseif($time_floor >= 570){
            $amt = $check->amount2-$check->amount*0.20;
        }
        elseif($time_floor >= 540){
            $amt = $check->amount2-$check->amount*0.25;
        }
        elseif($time_floor >= 510){
            $amt = $check->amount2-$check->amount*0.30;
        }
        elseif($time_floor >= 480){
            $amt = $check->amount2-$check->amount*0.35;
        }
        elseif($time_floor >= 450){
            $amt = $check->amount2-$check->amount*0.40;
        }
        elseif($time_floor >= 420){
            $amt = $check->amount2-$check->amount*0.45;
        }
        elseif($time_floor >= 390){
            $amt = $check->amount2-$check->amount*0.50;
        }
        elseif($time_floor >= 360){
            $amt = $check->amount2-$check->amount*0.55;
        }
        elseif($time_floor >= 330){
            $amt = $check->amount2-$check->amount*0.60;
        }
        elseif($time_floor >= 300){
            $amt = $check->amount2-$check->amount*0.65;
        }
        elseif($time_floor >= 270){
            $amt = $check->amount2-$check->amount*0.70;
        }
        elseif($time_floor >= 240){
            $amt = $check->amount2-$check->amount*0.75;
        }
        elseif($time_floor >= 210){
            $amt = $check->amount2-$check->amount*0.80;
        }
        else*/ if($time_floor >= 180){
            $amt = $check->amount2*0.90;
        }
        elseif($time_floor >= 90){
            $amt = $check->amount2*0.80;
        }
        else{
            setMessage('Release after 90 days.', 'error');
        }
        
        if($amt > 0){
            //$amt = $check->amount2;
            my_query("UPDATE user SET wallet=wallet+'" . $amt . "' WHERE uid='" . $uid . "'");
            my_query("UPDATE investments SET rdatetime = '" . date('c') . "', status = 1 WHERE recid='" . $recid . "'");
            setMessage('Your release successfully.', 'success');
        }
    }
}
redirect('./report_invest.php');
?>