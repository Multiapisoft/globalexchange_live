<?php include_once '../lib/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
admin();
$status_arr = array('Active', 'Inactive');
$user_type_arr = get_user_type();
$column = array("i", "uid", "login_id","refer_id", "name", "email", "mobile", "datetime", "wallet", "wallet_topup", "topup", "teamb", "type", "status", "action");

$query = "SELECT recid, uid, login_id, refer_id, name, email, mobile, datetime, wallet_topup, wallet, topup, teamb, type, status FROM user WHERE uid != 0";
$search = "";
if(isset($_POST["search"]["value"])){
	$search = tres($_POST["search"]["value"]);
	$query .= " AND (
		uid LIKE '%".$search."%'
		OR login_id LIKE '%".$search."%'
		OR name LIKE '%".$search."%'
		OR email LIKE '%".$search."%'
		OR mobile LIKE '%".$search."%'
	)";
}

if(isset($_POST["order"])){
	$query .= ' ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}else{ 
	$query .= ' ORDER BY datetime ASC';
}

$queryl = '';
 
if($_POST["length"] != -1){
	$queryl = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$result = my_query($query);
$number_filter_row = my_num_rows($result);
 
$result = my_query($query . $queryl);
$data = array();
$i = 0;
while ($row = my_fetch_object($result)){$i++;
	$data[] = array(
		$i,
		$row->uid,
		'<a href="user_login_process.php?uid='.$row->uid.'" title="Login this user" target="_blank">'.$row->login_id.'</a>',
		$row->refer_id,
		$row->name,
		$row->email,
		$row->mobile,
		date("d M, Y h:i A", strtotime($row->datetime)),
		$row->wallet*1,
		$row->wallet_topup*1,
		$row->topup*1,
		$row->teamb*1,
		$user_type_arr[$row->type],
		$status_arr[$row->status],
		'<a class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update" href="edit_profile.php?uid='.$row->uid.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>'
	);
}
 
function count_all_data(){
	return my_fetch_object(my_query("SELECT COUNT(recid) AS count FROM user WHERE uid != 0"))->count;
}

$output = array(
	'draw'   => intval($_POST['draw']),
	'recordsTotal' => count_all_data(),
	'recordsFiltered' => $number_filter_row,
	'data'   => $data
);

echo json_encode($output);
?>