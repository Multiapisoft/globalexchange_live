<?php
include_once '../lib/config.php';
admin();

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action) {
    case 'create_batch':
        createBatch();
        break;
    case 'update_batch_status':
        updateBatchStatus();
        break;
    case 'get_batch_history':
        getBatchHistory();
        break;
    case 'get_batch_details':
        getBatchDetails();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function createBatch() {
    try {
        $withdrawal_ids = isset($_POST['withdrawal_ids']) ? $_POST['withdrawal_ids'] : [];
        $admin_address = isset($_POST['admin_address']) ? $_POST['admin_address'] : '';
        $tx_hash = isset($_POST['tx_hash']) ? $_POST['tx_hash'] : '';
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
        
        if ((empty($withdrawal_ids) || empty($admin_address)) && status != 1) {
            throw new Exception('Missing required parameters');
        }
        
        if (count($withdrawal_ids) > 20) {
            throw new Exception('Maximum 20 addresses allowed per batch');
        }
        
        // Validate withdrawal IDs and calculate totals
        $total_amount = 0;
        $addresses = [];
        $amounts = [];
        
        foreach ($withdrawal_ids as $recid) {
            $withdrawal = my_fetch_object(my_query("SELECT * FROM withdrawal_block WHERE recid = '".(int)$recid."' AND status = 0"));
            if (!$withdrawal) {
                throw new Exception('Invalid withdrawal ID: ' . $recid);
            }
            
            $total_amount += $withdrawal->net_amount;
            $addresses[] = $withdrawal->withdrawal_address;
            $amounts[] = $withdrawal->net_amount;
        }
        
        // Create batch record
        $withdrawal_ids_json = json_encode($withdrawal_ids);
        $addresses_json = json_encode($addresses);
        $amounts_json = json_encode($amounts);
        
        $insert_query = "INSERT INTO smart_contract_batches 
                        (admin_address, total_addresses, total_amount, withdrawal_ids, tx_hash, status) 
                        VALUES 
                        ('".$admin_address."', ".count($withdrawal_ids).", ".$total_amount.", '".$withdrawal_ids_json."', '".$tx_hash."', 0)";
        
        my_query($insert_query);
        $batch_id = my_insert_id();
        
        // Update withdrawal records to processing status
        foreach ($withdrawal_ids as $recid) {
            my_query("UPDATE withdrawal_block SET status = 0, remark = 'Smart Contract Batch #".$batch_id."' WHERE recid = '".(int)$recid."'");
        }
        
        echo json_encode([
            'success' => true, 
            'batch_id' => $batch_id,
            'message' => 'Batch created successfully',
            'addresses' => $addresses,
            'amounts' => $amounts
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateBatchStatus() {
    try {
        $batch_id = isset($_POST['batch_id']) ? (int)$_POST['batch_id'] : 0;
        $tx_hash = isset($_POST['tx_hash']) ? $_POST['tx_hash'] : '';
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
        $gas_used = isset($_POST['gas_used']) ? $_POST['gas_used'] : '';
        $block_number = isset($_POST['block_number']) ? $_POST['block_number'] : '';
        $error_message = isset($_POST['error_message']) ? $_POST['error_message'] : '';
        
        if (!$batch_id) {
            throw new Exception('Invalid batch ID');
        }
        
        // Get batch details
        $batch = my_fetch_object(my_query("SELECT * FROM smart_contract_batches WHERE batch_id = '".$batch_id."'"));
        if (!$batch) {
            throw new Exception('Batch not found');
        }
        
        // Update batch status
        $update_query = "UPDATE smart_contract_batches SET 
                        status = ".$status.",
                        tx_hash = '".$tx_hash."',
                        gas_used = '".$gas_used."',
                        block_number = '".$block_number."',
                        error_message = '".$error_message."',
                        confirmed_at = NOW()
                        WHERE batch_id = ".$batch_id;
        
        my_query($update_query);
        
        // Update withdrawal records
        $withdrawal_ids = json_decode($batch->withdrawal_ids, true);
        $withdrawal_status = ($status == 1) ? 1 : 2; // 1 = Success, 2 = Failed
        
        foreach ($withdrawal_ids as $recid) {
            $remark = ($status == 1) ? 
                'Smart Contract Success - TX: '.$tx_hash : 
                'Smart Contract Failed - '.$error_message;
            
            // Get withdrawal details
            $withdrawal = my_fetch_object(my_query("SELECT * FROM withdrawal_block WHERE recid = '".(int)$recid."'"));
                
            my_query("UPDATE withdrawal_block SET 
                     status = ".$withdrawal_status.",
                     approved_datetime = NOW(),
                     remark = '".$remark."'
                     WHERE recid = '".(int)$recid."'");
            
            if($withdrawal_status == 1){
                // Store fee in walfare_fund when approved successfully
                my_query("INSERT INTO walfare_fund (uid, amount, datetime) VALUES ('" . $withdrawal->uid . "', '" . $withdrawal->fee . "', '" . date('c') . "')");
            }
            
            if($withdrawal_status != 1){
                my_query("UPDATE user SET wallet=wallet+ '".$withdrawal->amount."' WHERE uid = '".$withdrawal->uid."'");
                
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Batch status updated successfully']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getBatchHistory() {
    try {
        $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 50;
        $offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
        
        $query = "SELECT * FROM smart_contract_batches 
                  ORDER BY created_at DESC 
                  LIMIT ".$offset.", ".$limit;
        
        $result = my_query($query);
        $batches = [];
        
        while ($row = my_fetch_object($result)) {
            $status_text = '';
            $status_class = '';
            
            switch($row->status) {
                case 0:
                    $status_text = 'Pending';
                    $status_class = 'status-pending';
                    break;
                case 1:
                    $status_text = 'Success';
                    $status_class = 'status-success';
                    break;
                case 2:
                    $status_text = 'Failed';
                    $status_class = 'status-failed';
                    break;
            }
            
            $batches[] = [
                'batch_id' => $row->batch_id,
                'created_at' => date('d M, Y h:i A', strtotime($row->created_at)),
                'admin_address' => $row->admin_address,
                'total_addresses' => $row->total_addresses,
                'total_amount' => number_format($row->total_amount, 2),
                'status' => $status_text,
                'status_class' => $status_class,
                'tx_hash' => $row->tx_hash,
                'gas_used' => $row->gas_used,
                'block_number' => $row->block_number,
                'error_message' => $row->error_message
            ];
        }
        
        echo json_encode(['success' => true, 'batches' => $batches]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getBatchDetails() {
    try {
        $batch_id = isset($_POST['batch_id']) ? (int)$_POST['batch_id'] : 0;
        
        if (!$batch_id) {
            throw new Exception('Invalid batch ID');
        }
        
        $batch = my_fetch_object(my_query("SELECT * FROM smart_contract_batches WHERE batch_id = '".$batch_id."'"));
        if (!$batch) {
            throw new Exception('Batch not found');
        }
        
        $withdrawal_ids = json_decode($batch->withdrawal_ids, true);
        $withdrawals = [];
        
        foreach ($withdrawal_ids as $recid) {
            $withdrawal = my_fetch_object(my_query("
                SELECT w.*, u.login_id 
                FROM withdrawal_block w 
                LEFT JOIN user u ON u.uid = w.uid 
                WHERE w.recid = '".(int)$recid."'
            "));
            
            if ($withdrawal) {
                $withdrawals[] = [
                    'recid' => $withdrawal->recid,
                    'login_id' => $withdrawal->login_id,
                    'withdrawal_address' => $withdrawal->withdrawal_address,
                    'net_amount' => number_format($withdrawal->net_amount, 2),
                    'status' => $withdrawal->status
                ];
            }
        }
        
        echo json_encode([
            'success' => true, 
            'batch' => [
                'batch_id' => $batch->batch_id,
                'admin_address' => $batch->admin_address,
                'total_addresses' => $batch->total_addresses,
                'total_amount' => number_format($batch->total_amount, 2),
                'status' => $batch->status,
                'tx_hash' => $batch->tx_hash,
                'created_at' => date('d M, Y h:i A', strtotime($batch->created_at)),
                'withdrawals' => $withdrawals
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
