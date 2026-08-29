<?php include_once '../lib/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
admin();
$arr = array(
    'bitcoin' => '',
    'bnb_address' => '',
    'royalty' => 0
);

if (isset($_POST)) {
    $uid = tres($_POST['uid']);
    $login_id = tres($_POST['login_id']);
    $password = isset($_POST['password']) ? tres($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? tres($_POST['confirm_password']) : '';
    $type = tres($_POST['type']);
    $status = tres($_POST['status']);
    $status_recharge = isset($_POST['status_recharge']) ? tres($_POST['status_recharge']) : 0;

    $name = isset($_POST['name']) ? tres($_POST['name']) : '';
    $dob = isset($_POST['dob']) ? date('Y-m-d', strtotime(tres($_POST['dob']))) : date('Y-m-d');
    $gender = isset($_POST['gender']) ? tres($_POST['gender']) : 'Male';
    $email = isset($_POST['email']) ? tres($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? tres($_POST['phone']) : '';
    $mobile = isset($_POST['mobile']) ? tres($_POST['mobile']) : '';
    $address = isset($_POST['address']) ? tres($_POST['address']) : '';
    $city = isset($_POST['city']) ? tres($_POST['city']) : '';
    $district = isset($_POST['district']) ? tres($_POST['district']) : '';
    $state = isset($_POST['state']) ? tres($_POST['state']) : '';
    $country = isset($_POST['country']) ? tres($_POST['country']) : 'IN';
    $pincode = isset($_POST['pincode']) ? tres($_POST['pincode']) : '';
    $father_name = isset($_POST['father_name']) ? tres($_POST['father_name']) : '';

    $account_number = isset($_POST['account_number']) ? tres($_POST['account_number']) : '';
    $account_holder_name = isset($_POST['account_holder_name']) ? tres($_POST['account_holder_name']) : '';
    $account_type = isset($_POST['account_type']) ? tres($_POST['account_type']) : '';
    $ifsc = isset($_POST['ifsc']) ? tres($_POST['ifsc']) : '';
    $bank_name = isset($_POST['bank_name']) ? tres($_POST['bank_name']) : '';
    $branch_name = isset($_POST['branch_name']) ? tres($_POST['branch_name']) : '';
    $bank_address = isset($_POST['bank_address']) ? tres($_POST['bank_address']) : '';
    $pan_no = isset($_POST['pan_no']) ? tres($_POST['pan_no']) : '';

    foreach ($arr as $key => $value) {
        ${$key} = isset($_POST[$key]) ? tres($_POST[$key]) : $value;
    }

    if ($password != '') {
        if (checkPassword($password) == 0) {
            setMessage('Invalid password.', 'error');
            redirect('./edit_profile.php?uid=' . $uid);
            die();
        } elseif ($password != $confirm_password) {
            setMessage('Confirm password does not match password.', 'error');
            redirect('./edit_profile.php?uid=' . $uid);
            die();
        }
        $password_md5 = encryptPassword($password);
    }

    if (checkLoginId($login_id) == 0) {
        setMessage('Invalid user id.', 'error');
    } elseif (checkLoginIdAvailability($login_id, $uid) == 0) {
        setMessage('User id already axist.', 'error');
    } elseif ($mobile && checkMobile($mobile) == 0) {
        setMessage('Invalid mobile.', 'error');
    } elseif ($mobile && checkMobileAvailability($mobile, $uid) == 0) {
        setMessage('Mobile already axist.', 'error');
    } elseif ($mobile && checkEmail($email) == 0) {
        setMessage('Invalid email.', 'error');
    } elseif ($mobile && checkEmailAvailability($email, $uid) == 0) {
        setMessage('Email already axist.', 'error');
    } else {
        $sql = "UPDATE user SET login_id='" . $login_id . "', bnb_address='" . $login_id . "', name='" . $name . "', dob='" . $dob . "', gender='" . $gender . "', email='" . $email . "', phone='" . $phone . "'";
        $sql .= ", mobile='" . $mobile . "', address='" . $address . "', city='" . $city . "', state='" . $state . "', country='" . $country . "'";
        //$sql .= ", pincode='" . $pincode . "', account_number='" . $account_number . "', account_holder_name='" . $account_holder_name . "', account_type='" . $account_type . "'";
        //$sql .= ", ifsc='" . $ifsc . "', bank_name='" . $bank_name . "', branch_name='" . $branch_name . "', pan_no='" . $pan_no . "'";
        $sql .= ", type='" . $type . "', status='" . $status . "', status_recharge='" . $status_recharge . "'";

        foreach ($arr as $key => $value) {
            if (isset($_POST[$key])) {
                $sql .= ", $key = '" . ${$key} . "'";
            }
        }

        if ($password != '') {
            $sql .= ", password='" . $password_md5 . "', transaction_password='" . $password_md5 . "'";
        }
        $sql .= " WHERE uid='" . $uid . "'";

        my_query($sql);

        setMessage('Profile edit successfully.', 'success');
    }
    redirect('./edit_profile.php?uid=' . $uid);
} else {
    redirect('./users.php');
}
?>