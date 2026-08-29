<?php
include_once '../lib/config.php';

header('Content-Type: application/json');

try {
    $recid  = isset($_POST['recid']) ? (int)$_POST['recid'] : 0;
    $status = isset($_POST['status']) ? (int)$_POST['status'] : -1; // 1=Approve, 2=Reject

    if (!$recid || !in_array($status, [1, 2])) {
        throw new Exception('Invalid request data');
    }

    // Check withdrawal exist
    $withdrawal = my_fetch_object(my_query("SELECT * FROM withdrawal_block WHERE recid = '" . $recid . "'"));
    if (!$withdrawal) {
        throw new Exception('Withdrawal not found');
    }

    if ($withdrawal->status != 0) {
        throw new Exception('This withdrawal is already processed.');
    }

    $uid    = $withdrawal->uid;
    $amount = $withdrawal->amount;
    $wallet_field = 'wallet';

    // Prepare remark
    $remark = ($status == 1)
        ? 'Manually Approved by Admin'
        : 'Manually Rejected by Admin';

    // If approved → deduct money
    if ($status == 1) {

        // Mark approved
        my_query("UPDATE withdrawal_block 
                  SET status = 1, 
                      approved_datetime = NOW(), 
                      remark = '" . $remark . "' 
                  WHERE recid = '" . $recid . "'");
    }
    // If rejected →  update status and refund wallet
    else {
        // Refund wallet balance
        my_query("UPDATE user 
                  SET $wallet_field = $wallet_field + '" . $amount . "' 
                  WHERE uid = '" . $uid . "'");



        my_query("UPDATE withdrawal_block 
                  SET status = 2, 
                      approved_datetime = NOW(), 
                      remark = '" . $remark . "' 
                  WHERE recid = '" . $recid . "'");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Withdrawal updated successfully',
        'recid'   => $recid,
        'status'  => $status
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
