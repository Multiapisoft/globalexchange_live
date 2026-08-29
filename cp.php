<?php include_once 'lib/config.php';
include_once 'lib/coinpayments.php';

$data = isset($_POST) ? $_POST : array();
file_put_contents("cp.json", json_encode($data));
if($data){
    coinpayments_deposit($data);
}
echo 'success';