<?php include_once '../lib/config.php';
admin();
$column = array("i", "login_id", "days", "datetime", "amount");

$query = "SELECT g.*, u.login_id FROM income_growth as g LEFT JOIN user as u ON u.uid=g.uid WHERE g.uid != 0";
$search = "";
if(isset($_POST["search"]["value"]) && $_POST["search"]["value"]){
	$search = tres($_POST["search"]["value"]);
	$query .= " AND (
		g.uid LIKE '%".$search."%'
		OR u.login_id LIKE '%".$search."%'
		OR g.days LIKE '%".$search."%'
		OR g.amount LIKE '%".$search."%'
	)";
}

if(isset($_POST["order"])){
    $pre = ($column[$_POST['order']['0']['column']] == 'login_id') ? 'u' : 'g';
	$query .= ' ORDER BY '.$pre.'.'.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}else{ 
	$query .= ' ORDER BY g.datetime DESC';
}

$queryl = '';
 
if($_POST["length"] != -1){
	$queryl = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$result = my_query($query);
$number_filter_row = my_num_rows($result);
 
$result = my_query($query . $queryl);
$data = array();
$i = (isset($_POST['start'])) ? (int) $_POST['start'] : 0;
while ($row = my_fetch_object($result)){$i++;
	$data[] = array(
		$i,
		$row->login_id,
		$row->days,
		date("d M, Y h:i A", strtotime($row->datetime)),
		$row->amount*1,
	);
}
 
function count_all_data(){
	return my_fetch_object(my_query("SELECT COUNT(recid) AS count FROM income_growth WHERE uid != 0"))->count;
}

$output = array(
	'draw'   => intval($_POST['draw']),
	'recordsTotal' => count_all_data(),
	'recordsFiltered' => $number_filter_row,
	'data'   => $data
);

echo json_encode($output);
?>