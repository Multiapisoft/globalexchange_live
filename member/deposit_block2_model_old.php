<?php 
session_start();
require '../lib/own_pay/vendor/autoload.php';
include_once '../lib/config.php';
include_once '../lib/function_lib.php';
include_once '../lib/own_pay/own_pay.php';
user();

if (!isset($_SESSION['userid'])) {
    die(json_encode(['status' => 'error', 'message' => 'Not authenticated']));
}

if (!isset($_POST['address']) ) {
    die(json_encode(['status' => 'error', 'message' => 'Missing required parameters']));
}

try {
    // Get the wallet details from database
    $user = get_user_details($_SESSION['userid']);
      my_query("UPDATE user SET got=0 WHERE uid='" . $user->uid . "'");
    // Start monitoring using the existing function and get the result
    $monitoringResult = startMonitoring($_POST['address'], $user->pay_privatekey);

    print_r($monitoringResult);
    die();
    
    if ($user->got==1) {
        // If monitoring was successful, insert into deposit_block
        
        $response = [
            'status' => 'success',
            'message' => 'Payment processed successfully',
            'return' => $monitoringResult,
           
        ];
         my_query("UPDATE user SET got=0 WHERE uid='" . $user->uid . "'");
        setMessage('Payment processed successfully', 'success');
    } else {
        // If monitoring failed, return the error
        $response = [
            'status' => 'error',
            'message' => $monitoringResult['message']
        ];
        
        setMessage("Payment not found", 'error');
        // redirect('./deposite_user.php');
    }
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
// if(isset($_POST) && $_POST['amount'] && $_POST['hash']){
//     $amount = tres($_POST['amount']);
//     $hash = tres($_POST['hash']);
//     $type = 2;
    
//     if(checkDecimal($amount)==0){
//         setMessage('Invalid amount.', 'alert alert-error');
//     }
//     elseif($amount<100){
//         setMessage('Minimum deposit'.SITE_CURRENCY.'10.00.', 'alert alert-error');
//     }
//     elseif($amount>50000){
//         setMessage('Maximum deposit'.SITE_CURRENCY.'50,000.00.', 'alert alert-error');
//     }
//     else{
        
//         $txid = $hash;
//         $amount_coin = $amount;
        
//         //$amount = round($amount / EUROO1);
//         $amount = $amount;
//         $fee = 0;
//         $net_amount = $amount-$fee;
        
//         my_query("INSERT INTO deposit_block (uid, datetime, status, amount, fee, net_amount, amount_coin, txid, data, type) VALUES ('" . $uid . "', '" . date('c') . "', 0, '" . $amount . "', '" . $fee . "', '" . $net_amount . "', '" . $amount_coin . "', '" . $txid . "', '', 'USDT.TRC20')");

//         $last_insert_id = my_insert_id();
//         setMessage('Success - Thank you, Your Amount deposit will be added within a few hours after successful transfer.', 'alert alert-success');
//     }
// }
// redirect('./deposite_user.php');
?>


