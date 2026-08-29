<?php session_start();

include_once(__DIR__ . '/../.env.php');

if (!isset($link)) {
    include_once 'connection.php';
}
include_once 'function_lib.php';
define("CONTRACT_NAME", 'X');
define("CONTRACT_ADDRESS", $_ENV['USDT_CONTRACT']);
define("_TEST_", 0);
define("_ISGLOBAL_", 1);

if (isset($title)) {
    $title = str_replace('CONTRACT_NAME', CONTRACT_NAME, $title);
}

function get_uid_by_address($login_id) {
    $uid = 0;
    if ($login_id && checkLoginId($login_id) == 1) {
        $result = my_query("SELECT uid, login_id FROM user WHERE status=0 AND login_id='" . $login_id . "'");
        if (my_num_rows($result) == 1) {
            $row = my_fetch_object($result);
            if ($row->login_id == $login_id) {
                $uid = $row->uid;
            }
        }
    }
    return $uid;
}
?>