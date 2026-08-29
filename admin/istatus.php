<?php include '../lib/config.php';
include '../lib/coinpayments.php';
admin();
$recid = isset($_GET['recid']) ? $_GET['recid'] : 0;

if(isset($recid)){
    $check = my_fetch_object(my_query("SELECT * FROM investments WHERE recid='".$recid."' AND status=0"));
    if(isset($_GET['stop']) && $check){
        my_query("UPDATE investments SET status=1 WHERE recid='".$recid."' AND status=0");
        setMessage('Successfully stoped.', 'alert alert-success');
    }
    elseif(isset($_GET['start'])){
        my_query("UPDATE investments SET status=0 WHERE recid='".$recid."' AND status=1");
        setMessage('Successfully started.', 'alert alert-success');
    }
}
redirect('./report_invest.php');
?>