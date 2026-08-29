<?php
include_once '../lib/config.php';
admin();

$status_arr = array('Pending', 'Success', 'Failed');
$type = (isset($_GET['type']) && (int) $_GET['type'] <= 2) ? (int) $_GET['type'] : 0;
$column = array("i", "c", "login_id", "datetime", "amount", "fee", "net_amount", "withdrawal_address", "status", "batch_id", "action");

$select = "d.*, u.login_id, b.batch_id, b.tx_hash";

$query = "SELECT __SELECT__ FROM withdrawal_block as d 
          LEFT JOIN user as u ON u.uid=d.uid 
          LEFT JOIN smart_contract_batches as b ON FIND_IN_SET(d.recid, REPLACE(REPLACE(b.withdrawal_ids, '[', ''), ']', ''))
          WHERE d.status = '" . $type . "'";

$search = "";
if (isset($_POST["search"]["value"]) && $_POST["search"]["value"]) {
    $search = tres($_POST["search"]["value"]);
    $query .= " AND (
        d.uid LIKE '%" . $search . "%'
        OR u.login_id LIKE '%" . $search . "%'
        OR d.amount LIKE '%" . $search . "%'
        OR d.fee LIKE '%" . $search . "%'
        OR d.net_amount LIKE '%" . $search . "%'
        OR d.withdrawal_address LIKE '%" . $search . "%'
        OR d.type LIKE '%" . $search . "%'
        OR b.tx_hash LIKE '%" . $search . "%'
    )";
}

if (isset($_POST["order"]) && $_POST["order"]) {
    $c = $column[$_POST['order']['0']['column']];
    $pre = ($c == 'login_id') ? 'u' : 'd';
    $query .= ' ORDER BY ' . $pre . '.' . $c . ' ' . $_POST['order']['0']['dir'] . ' ';
} else {
    $query .= ' ORDER BY d.datetime DESC';
}

$query2 = str_replace('__SELECT__', "COUNT(d.recid) AS count", $query);
$query = str_replace('__SELECT__', $select, $query);

$queryl = '';
if ($_POST["length"] != -1) {
    $queryl = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$number_filter_row = my_fetch_object(my_query($query2))->count;
$result = my_query($query . $queryl);
$data = array();
$i = (isset($_POST['start'])) ? (int) $_POST['start'] : 0;

while ($row = my_fetch_object($result)) {
    $i++;

    // // Status display
    // $status_display = "Pending";
    // $status_class = "status-pending";
    // if($row->status == 1){
    //     $status_display = "Success";
    //     $status_class = "status-success";
    // } elseif($row->status == 2){
    //     $status_display = "Failed";
    //     $status_class = "status-failed";
    // }




    // Default status display
    // $status_display = "Pending";
    // $status_class = "status-pending";

    // अगर Success
    if ($row->status == 0) { // Pending
        $status_display = '
        <div class="status-actions">
            <span class="badge badge-warning">Pending</span><br>
            <button onclick="updateStatus(' . $row->recid . ', 1)" class="btn-approve">Approve</button>
            <button onclick="updateStatus(' . $row->recid . ', 2)" class="btn-reject">Reject</button>
        </div>
    ';
    } elseif ($row->status == 1) {
        $status_display = '<span class="badge badge-success">Approved</span>';
    } elseif ($row->status == 2) {
        $status_display = '<span class="badge badge-danger">Rejected</span>';
    }

    // Batch ID display
    $batch_display = $row->batch_id ?
        '<a href="#" onclick="viewBatchDetails(' . $row->batch_id . ')" class="badge badge-info">#' . $row->batch_id . '</a>' :
        '<span class="text-muted">-</span>';

    // Action buttons
    $action = '';
    if ($row->status == 0) { // Pending
        $action = '<span class="badge badge-warning">Ready for Batch</span>';
    } elseif ($row->status == 1 && $row->tx_hash) { // Success with transaction
        $action = '<a href="https://bscscan.com/tx/' . $row->tx_hash . '" target="_blank" class="btn btn-xs btn-info">
                    <i class="fa fa-external-link"></i> View TX
                   </a>';
    } elseif ($row->status == 2) { // Failed
        $action = '<span class="badge badge-danger">Failed</span>';
    }

    // Checkbox for pending items only
    $checkbox = '';
    if ($row->status == 0) {
        $checkbox = '<input class="withdrawal-checkbox" name="recid[]" value="' . $row->recid . '" 
                     type="checkbox" 
                     data-address="' . $row->withdrawal_address . '" 
                     data-amount="' . $row->net_amount . '" 
                     data-user="' . $row->login_id . '">';
    }
    
    $data[] = array(
        $i,
        $checkbox,
        $row->login_id,
        date("d M, Y h:i A", strtotime($row->datetime)),
         $row->widthdrawal_type == "INR" ? number_format($row->amount*90, 2) ." INR" : number_format($row->amount, 2) . " " . SITE_CURRENCY,
        $row->widthdrawal_type == "INR" ? number_format($row->fee*90, 2) ." INR" : number_format($row->fee, 2) . " " . SITE_CURRENCY,
        $row->widthdrawal_type == "INR" ? number_format($row->net_amount*90, 2) ." INR" : number_format($row->net_amount, 2) . " " . SITE_CURRENCY,
        '<span style="font-family: monospace; font-size: 12px;">' . $row->withdrawal_address . '</span>',
        $row->widthdrawal_type,
        // '<span class="status-badge ' . $status_class . '">' . $status_display . '</span>',
        $status_display,
        $batch_display,
        $action
    );
}

function count_all_data()
{
    global $type;
    return my_fetch_object(my_query("SELECT COUNT(recid) AS count FROM withdrawal_block WHERE status = '" . $type . "'"))->count;
}

$output = array(
    'draw'   => intval($_POST['draw']),
    'recordsTotal' => count_all_data(),
    'recordsFiltered' => $number_filter_row,
    'data'   => $data
);

echo json_encode($output);
