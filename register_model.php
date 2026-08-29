<?php include 'lib/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
include 'register_plan.php';
include 'r.php';
$_status = 0;
$_msg = "Transaction failed.";

if(1 || _TEST_){
    //if(isset($_POST['login_id']) && $_POST['login_id'] && isset($_POST['position']) && $_POST['position']){
    if(isset($_POST['login_id']) && $_POST['login_id']){
        $data = array(
            'login_id' => $_POST['login_id'],
            'refer_id' => isset($_POST['refer_id']) ? $_POST['refer_id'] : 100,
            'position' => isset($_POST['position']) ? $_POST['position'] : '',
            'status' => 1,
            'transactionHash' => "test".rand(100000, 99999999)
        );
        $register = register($data, 1);
        $_status = isset($register[0]) ? $register[0] : 0;
        $_msg = isset($register[1]) ? $register[1] : $_msg;
    }
    if($_status){
        $_msg = "Success - thank you for register with us.";
    }
}else{
    if(isset($_POST['status']) && $_POST['status']){
        $data = $_POST;
        $register = register($data, 1);
        $_status = isset($register[0]) ? $register[0] : 0;
        $_msg = isset($register[1]) ? $register[1] : $_msg;
        if($_status){
            $_msg = "Success - thank you for register with us.";
        }
    }
    else{
        $data_json = @json_encode($_POST);
        my_query("INSERT INTO deposit_block (uid, datetime, status, data, type) VALUES ('0', '" . date('c') . "', 0, '" . $data_json . "', '".SITE_CURRENCY_TKN."')");
    }
}
echo json_encode(array('Success' => $_status, 'Message' => $_msg));die;
?>