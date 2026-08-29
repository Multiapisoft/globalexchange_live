<?php include_once '../lib/config.php';
admin();
$column = array("i", "login_id", "title", "datetime", "amount", "amount2");

$query = "SELECT i.*, u.login_id, ip.title FROM investments as i LEFT JOIN user as u ON u.uid=i.uid LEFT JOIN investments_plan as ip ON ip.recid = i.ipid WHERE i.uid != 0";
$search = "";
if(isset($_POST["search"]["value"]) && $_POST["search"]["value"]){
	$search = tres($_POST["search"]["value"]);
	$query .= " AND (
		i.uid LIKE '%".$search."%'
		OR u.login_id LIKE '%".$search."%'
		OR ip.title LIKE '%".$search."%'
		OR i.amount LIKE '%".$search."%'
	)";
}

if(isset($_POST["order"])){
    $pre = ($column[$_POST['order']['0']['column']] == 'login_id') ? 'u' : (($column[$_POST['order']['0']['column']] == 'title') ? 'ip' : 'i');
	$query .= ' ORDER BY '.$pre.'.'.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}else{ 
	$query .= ' ORDER BY i.datetime DESC';
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
    $st = "-";
    if ($row->status == 0) {
        $st = '<a href="istatus.php?recid='.$row->recid.'&stop=1" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to stop?\');">Stop</a>';
    } elseif ($row->status == 1) {
        $st = '<a href="istatus.php?recid='.$row->recid.'&start=1" class="btn btn-success btn-sm" onclick="return confirm(\'Are you sure you want to start?\');">Start</a>';
    }
    // elseif($row->status == 1){
    //     $st = "Stoped";
    // }
	$data[] = array(
		$i,
		$row->login_id,
		$row->title,
		date("d M, Y h:i A", strtotime($row->datetime)),
		$row->amount*1,
// 		$row->amount2*1,
		$st
	);
}
 
function count_all_data(){
	return my_fetch_object(my_query("SELECT COUNT(recid) AS count FROM investments WHERE uid != 0"))->count;
}

$output = array(
	'draw'   => intval($_POST['draw']),
	'recordsTotal' => count_all_data(),
	'recordsFiltered' => $number_filter_row,
	'data'   => $data
);

echo json_encode($output);
?>