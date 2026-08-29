<?php include_once '../lib/config.php';
include_once '../lib/own_pay/wd.php';

$c = 'USDT';
echo '<br />/*******'.$c.'*******/<br />';
$result = mysqli_query($link, "SELECT * FROM withdrawal_block WHERE status = 0 AND amount_coin>0 AND type='".$c."' ORDER BY amount_coin ASC");
while ($row = mysqli_fetch_object($result)) {
    $account = $row->withdrawal_address;
    $amount = $row->amount_coin;
    $hash = startWalletWithdrawal($account, $amount, $c);
    if($hash){
        echo $hash;
        $recid = $row->recid;
        my_query("UPDATE withdrawal_block SET approved_datetime='".date('c')."', status=1, withdrawal_id = '".$hash."' WHERE recid='".$recid."'");
        
        // Store fee in walfare_fund
        my_query("INSERT INTO walfare_fund (uid, amount, datetime) VALUES ('" . $row->uid . "', '" . $row->fee . "', '" . date('c') . "')");
    }else{
        echo $hash;
    }
    echo '<br>========================<br>';
}

echo "<br/> Closing complete. Please close this browser.";
?>