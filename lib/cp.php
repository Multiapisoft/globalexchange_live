<?php include_once 'config.php';
include_once 'coinpayments.php';

$data = isset($_POST) ? $_POST : array();
file_put_contents("cp.json", json_encode($data));
if($data){
    coinpayments_deposit($data);
}
echo 'success';