<?php include_once '../lib/config.php';
admin();
$status_arr = array('Panding', 'Success', 'Failed');
$type = (isset($_GET['type']) && (int) $_GET['type'] <= 2) ? (int) $_GET['type'] : 0;
$column = array("i", "login_id", "datetime", "amount", "fee", "net_amount", "amount_coin", "txid", "type", "status", "action");

$select = "d.*, u.login_id";

$query = "SELECT __SELECT__ FROM deposit_block as d LEFT JOIN user as u ON u.uid=d.uid WHERE d.status = '".$type."'";
$search = "";
if(isset($_POST["search"]["value"]) && $_POST["search"]["value"]){
	$search = tres($_POST["search"]["value"]);
	$query .= " AND (
		d.uid LIKE '%".$search."%'
		OR u.login_id LIKE '%".$search."%'
		OR d.amount LIKE '%".$search."%'
		OR d.fee LIKE '%".$search."%'
		OR d.net_amount LIKE '%".$search."%'
		OR d.amount_coin LIKE '%".$search."%'
		OR d.txid LIKE '%".$search."%'
		OR d.type LIKE '%".$search."%'
		OR d.status LIKE '%".$search."%'
	)";
}

if(isset($_POST["order"]) && $_POST["order"]){
    $c = $column[$_POST['order']['0']['column']];
    $pre = ($c == 'login_id') ? 'u' : 'd';
	$query .= ' ORDER BY '.$pre.'.'.$c.' '.$_POST['order']['0']['dir'].' ';
}else{ 
	$query .= ' ORDER BY d.datetime DESC';
}

$query2 = str_replace('__SELECT__', "COUNT(d.recid) AS count", $query);
$query = str_replace('__SELECT__', $select, $query);

$queryl = '';
 
if($_POST["length"] != -1){
	$queryl = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

//echo $query . $queryl;

$number_filter_row = my_fetch_object(my_query($query2))->count;
 
$result = my_query($query . $queryl);
$data = array();
$i = (isset($_POST['start'])) ? (int) $_POST['start'] : 0;
while ($row = my_fetch_object($result)){$i++;
    $st = "Approved";
    if($row->status == 0){
        $st = '<a href="deposit.php?recid='.$row->recid.'" onclick="return confirm(\'Are you sure you want to approve?\');">Approve</a> | <a href="deposit.php?recid='.$row->recid.'&cancel=1" onclick="return confirm(\'Are you sure you want to cancel?\');">Cancel</a>';
    }
    elseif($row->status == 2){
        $st = "Cancelled";
    }
    
	$data[] = array(
		$i,
		$row->login_id,
		date("d M, Y h:i A", strtotime($row->datetime)),
		$row->amount*1,
	
		$row->type,
		$status_arr[$row->status],
		$st
	);
}
 
function count_all_data(){
    global $type;
    return my_fetch_object(my_query("SELECT COUNT(recid) AS count FROM deposit_block WHERE status = '".$type."'"))->count;
}

$output = array(
	'draw'   => intval($_POST['draw']),
	'recordsTotal' => count_all_data(),
	'recordsFiltered' => $number_filter_row,
	'data'   => $data
);

echo json_encode($output);
?>