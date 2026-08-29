<?php include_once 'config.php';
$action = $_REQUEST['action'];

if ($action == 'login_id') {
    $login_id = $_GET['login_id'];
    $rs = my_query("SELECT login_id FROM user WHERE login_id='" . $login_id . "'");
    if (my_num_rows($rs) == 0) {
        $invalid = FALSE;
    } else {
        $invalid = TRUE;
    }
    echo json_encode(array('invalid' => $invalid));
} elseif ($action == 'sponsor') {
    $login_id = $_GET['refer_id'];
    $rs = my_query("SELECT name FROM user WHERE login_id='" . $login_id . "'");
    if (my_num_rows($rs) > 0) {
        $invalid = FALSE;
        $name = my_fetch_object($rs)->name;
    } else {
        $invalid = TRUE;
    }
    echo json_encode(array('invalid' => $invalid, 'name' => $name));
} elseif ($action == 'sponsor2') {
    $login_id = $_GET['refer_id'];
    $rs = my_query("SELECT login_id FROM user WHERE uid='" . $login_id . "'");
    if (my_num_rows($rs) > 0) {
        $invalid = FALSE;
        $name = my_fetch_object($rs)->login_id;
    } else {
        $invalid = TRUE;
    }
    echo json_encode(array('invalid' => $invalid, 'name' => $name));
} elseif ($action == 'email') {
    $email = $_GET['email'];
    $rs = my_query("SELECT email FROM user WHERE email='" . $email . "'");
    if (my_num_rows($rs) > 0) {
        $invalid = TRUE;
    } else {
        $invalid = FALSE;
    }
    echo json_encode(array('invalid' => $invalid));
} elseif ($action == 'mobile') {
    $mobile = $_GET['mobile'];
    $rs = my_query("SELECT mobile FROM user WHERE mobile='" . $mobile . "'");

    if (my_num_rows($rs) > 0) {
        $invalid = TRUE;
    } else {
        $invalid = FALSE;
    }
    echo json_encode(array('invalid' => $invalid));
} elseif ($action == 'get_countries') {
    // Get all countries for autocomplete
    $countries = array();
    $rs = my_query("SELECT country_id, short_name, calling_code FROM country ORDER BY short_name ASC");

    while ($row = my_fetch_object($rs)) {
        $countries[] = array(
            'id' => $row->country_id,
            'name' => $row->short_name,
            'code' => $row->calling_code
        );
    }

    echo json_encode($countries);
}elseif ($action == 'active_user') {
    $login_id = $_GET['refer_id'];
    $rs = my_query("SELECT name FROM user WHERE login_id='" . $login_id . "' AND topup >= 100 ");
    if (my_num_rows($rs) > 0) {
        $invalid = TRUE;
        $name = my_fetch_object($rs)->name;
    } else {
        $invalid = FALSE;
        $name = '';
    }
    echo json_encode(array('invalid' => $invalid, 'name' => $name));
}




?>