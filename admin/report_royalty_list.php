<?php include_once '../lib/config.php';
admin();
$type = (isset($_GET['type']) && (int) $_GET['type'] <= 2) ? (int) $_GET['type'] : 0;
//$column = $type ? array("i", "login_id", "datetime", "amount") : array("i", "login_id", "datetime", "amount", "level", "days");
$column = $type ? array("i", "login_id", "datetime", "amount") : array("i", "login_id", "datetime", "amount", "level");

$select = "l.*, u.login_id";

$query = "SELECT __SELECT__ FROM income_royalty as l LEFT JOIN user as u ON u.uid=l.uid WHERE l.type = '".$type."'";
$search = "";
if(isset($_POST["search"]["value"]) && $_POST["search"]["value"]){
	$search = tres($_POST["search"]["value"]);
	$query .= " AND (
		l.uid LIKE '%".$search."%'
		OR u.login_id LIKE '%".$search."%'
		OR l.amount LIKE '%".$search."%'
	)";
}

if(isset($_POST["order"]) && $_POST["order"]){
    $pre = ($column[$_POST['order']['0']['column']] == 'login_id') ? 'u' : 'l';
    $c = $column[$_POST['order']['0']['column']];
	$query .= ' ORDER BY '.$pre.'.'.$c.' '.$_POST['order']['0']['dir'].' ';
}else{
	$query .= ' ORDER BY l.datetime DESC';
}

$query2 = str_replace('__SELECT__', "COUNT(l.recid) AS count", $query);
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
    if($type){
    	$data[] = array(
    		$i,
    		$row->login_id,
    		date("d M, Y h:i A", strtotime($row->datetime)),
    		$row->amount*1,
    		$row->level,
    	);
    }
    else{
    	$data[] = array(
    		$i,
    		$row->login_id,
    		date("d M, Y h:i A", strtotime($row->datetime)),
    		$row->amount*1,
    		$row->level,
    		//$row->days
    	);
    }
}
 
function count_all_data(){
	return my_fetch_object(my_query("SELECT COUNT(recid) AS count FROM income_royalty WHERE type = '".$type."'"))->count;
}

$output = array(
	'draw'   => intval($_POST['draw']),
	'recordsTotal' => count_all_data(),
	'recordsFiltered' => $number_filter_row,
	'data'   => $data
);

echo json_encode($output);
?>