<?php
include_once '../lib/config.php';
admin();
$aid = $_SESSION['adminid'];
$arr = array(
    'site_name' => '',
    'phone' => '',
    'tds' => 0,
    'service_tax' => 0,
    'service' => 0,
    'working_status' => 0,
    'bv_value' => 1,
    'capping_binary' => 0,
    'capping_gift' => 0,
    'address' => '',
    'city' => '',
    'state' => '',
    'country' => 'IN',
    'pincode' => '',
    'user_sms_format' => '',
    'api_type' => 0,
    'b_rate' => 0.1,
    's_rate' => 0.1,
    'coin_rate' => 1,
    'bot_liquidity'=>0,
    'bot_profit'=>0,
);

if (isset($_POST)) {
    foreach ($arr as $key => $value) {
        ${$key} = isset($_POST[$key]) ? tres($_POST[$key]) : $value;
    }

    if (checkMobile($phone) == 0) {
        setMessage('Invalid phone.', 'error');
    } elseif (checkDecimal($tds) == 0) {
        setMessage('Invalid tds.', 'error');
    } elseif (checkDecimal($service_tax) == 0) {
        setMessage('Invalid service tax.', 'error');
    } elseif (checkDecimal($service) == 0) {
        setMessage('Invalid service charge.', 'error');
    } else {
        $sql = "UPDATE admin SET phone = '" . $phone . "'";

        foreach ($arr as $key => $value) {
            if (isset($_POST[$key])) {
                $sql .= ", $key = '" . ${$key} . "'";
            }
        }

        $sql .= " WHERE recid='" . $aid . "'";

        my_query($sql);
        setMessage('Settings edit successfully.', 'success');
    }
}
redirect('./settings.php');
?>