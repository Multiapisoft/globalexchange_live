<?php include_once '../lib/config.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();


$arr = array(
    'bnb_address' => '',
);

// $otpverified = false;

 
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (isset($_POST['otp'])) {
//         $inputOtp = $_POST['otp'];
//         if (isset($_SESSION['otp']) && $inputOtp == $_SESSION['otp']) {
//             echo json_encode(['success' => true, 'message' => 'OTP verified successfully.']);
//             // Optionally, unset the session variables after successful verification
//             unset($_SESSION['otp']);
//             unset($_SESSION['otp_email']);
//             setMessage('Otp Verified.', 'success');
//         } else {
//             echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
//             setMessage('Invalid otp.', 'error');
//         }
//     } else {
//         echo json_encode(['success' => false, 'message' => 'OTP not provided.']);
//         setMessage('Otp NOt provided.', 'error');
//     }
// } else {
//     echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
// }

if (isset($_POST)) {

    $uid = createId();
    $pin_no = isset($_POST['pin_no']) ? tres($_POST['pin_no']) : '';
    $refer_login_id = tres($_POST['refer_id']);
    $refer_id = registeredUserId($refer_login_id);
    if ($refer_id == 0 && !$refer_login_id) {
        $refer_id = 100;
    }
    $position = isset($_POST['position']) ? tres($_POST['position']) : '';
    $login_id = "TB".$uid ;
    $password = tres($_POST['password']);
    $confirm_password = tres($_POST['confirm_password']);

    $password_md5 = encryptPassword($password);

    $name = isset($_POST['name']) ? tres($_POST['name']) : '';
    $dob = isset($_POST['dob']) ? date('Y-m-d', strtotime(tres($_POST['dob']))) : date('Y-m-d');
    $gender = isset($_POST['gender']) ? tres($_POST['gender']) : 'Male';
    $email = isset($_POST['email']) ? tres($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? tres($_POST['phone']) : '';
    $mobile = isset($_POST['mobile']) ? tres($_POST['mobile']) : '';
    $address = isset($_POST['address']) ? tres($_POST['address']) : '';
    $city = isset($_POST['city']) ? tres($_POST['city']) : '';
    $state = isset($_POST['state']) ? tres($_POST['state']) : '';
    $country = isset($_POST['country']) ? tres($_POST['country']) : 'IN';

    $otp = isset($_POST['otp']) ? tres($_POST['otp']) : '';


    foreach ($arr as $key => $value) {
        ${$key} = isset($_POST[$key]) ? tres($_POST[$key]) : $value;
    }

    // $mobile_code = isset($_POST['mobile_code']) ? tres($_POST['mobile_code']) : '';

    $pin_data = isset($_POST['pin_no']) ? checkPin($pin_no, '') : '';
    // $inputOtp = $_POST['otp'];
    // if (isset($_SESSION['otp']) && $inputOtp == $_SESSION['otp']) {
    //     echo json_encode(['success' => true, 'message' => 'OTP verified successfully.']);
    //     echo $inputOtp;
    //     echo $_SESSION['otp'];
    //     // die;
    //     $otpverified=true;
    //     // Optionally, unset the session variables after successful verification
    //     //  setMessage('otp', 'error');
    //     unset($_SESSION['otp']);
    //     unset($_SESSION['otp_email']);
    // } else {
      
    //     echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
    //       echo $inputOtp;
    //     echo $_SESSION['otp'];
    //     // die;
    //     setMessage('Invalid otp.', 'error');
    // }
    if (isset($_SESSION['placement_id'])) {
        $placement_id = $_SESSION['placement_id'];
    } else if ($position) {
        $placement_id = get_terminal_id($refer_id, $position);
    } else {
        //$placement_id = get_placement_id($refer_id);
        $placement_id = $refer_id;
    }

    $child_ids = get_single_dimensional(get_child_levels($refer_id, 'yes'));

    $check_position = my_query("SELECT uid, position FROM user WHERE placement_id='$placement_id'");
    $position_row = @mysqli_fetch_object($check_position)->position;

    $product = isset($_POST['pin_no']) ? get_product_detail($pin_data[2]) : '';

    $checkrefer = mysqli_num_rows(my_query("SELECT uid FROM user WHERE uid='" . $refer_id . "' AND topup > 0"));

    //if($refer_id==0 || $checkrefer == 0){
    if ($refer_id == 0) {
        setMessage('Invalid sponsor id.', 'error');
    } elseif (checkLoginId($login_id) == 0) {
        setMessage('Login user id.', 'error');
    } elseif (checkLoginIdAvailability($login_id) == 0) {
        setMessage('Login id already exist.', 'error');
    } elseif (checkPassword($password) == 0) {
        setMessage('Invalid password.', 'error');
    } elseif ($password != $confirm_password) {
        setMessage('Confirm password does not match password.', 'error');
    } elseif (isset($_POST['pin_no']) && $pin_data == 0) {
        setMessage('Invalid pin.', 'error');
    } elseif (isset($_POST['pin_no']) && !in_array($product->recid, array(1))) {
        setMessage('Invalid pin.', 'error');
    } elseif ($placement_id && !in_array($placement_id, $child_ids)) {
        setMessage('Invalid placement id.', 'error');
    } elseif (isset($_POST['position']) && $position == '') {
        setMessage('Invalid position.', 'error');
    } elseif (isset($_POST['position']) && $placement_id && mysqli_num_rows($check_position) >= 2) {
        setMessage('2 children already exist. No more positions available.', 'error');
    } elseif (isset($_POST['position']) && mysqli_num_rows($check_position) == 1 && $position_row == $position) {
        setMessage('Position already exist.', 'error');
    } elseif (checkMobile($mobile) == 0) {
        setMessage('Invalid mobile.', 'error');
    } elseif (checkMobileAvailability($mobile) == 0) {
        setMessage('Mobile number already exists. One unique mobile is required.', 'error');
    } elseif (checkEmail($email) == 0) {
        setMessage('Invalid email.', 'error');
    } elseif (checkEmailAvailability($email) == 0) {
        setMessage('Email already exists. One unique email is required.', 'error');
    }
    // elseif (isset($_POST['otp']) && ($_SESSION['otp'] != $otp || $_SESSION['otp_type'] != 'register' || strtolower($_SESSION['otp_email']) != $email)) {
    //     setMessage('Invalid OTP.', 'error');
    // }
    // elseif ($otpverified==false) {
    //     setMessage('Invalid Otp', 'error');
    // }
    // elseif(isset($_POST['mobile_code']) && $_SESSION['mobile_code']!=$mobile_code){
    //     setMessage('Invalid mobile code.','error');
    // }
    else {
        $sql = "INSERT INTO `user` (`uid`, `login_id`, `refer_id`, `placement_id`, `position`, `password`, `name`, `dob`,`gender`, `address`, `city`, `state`, `country`,
            `mobile`, `email`, `phone`, `datetime`, `transaction_password`) VALUES ('" . $uid . "','" . $login_id . "','" . $refer_id . "','" . $placement_id . "','" . $position . "','" . $password_md5 . "','" . $name . "','" . $dob . "','" . $gender . "','" . $address . "','" . $city . "','" . $state . "','" . $country . "','" . $mobile . "','" . $email . "','" . $phone . "','" . date('c') . "', '" . $password_md5 . "')";

        my_query($sql);

        $sql = "UPDATE user SET name = '" . $name . "'";

        foreach ($arr as $key => $value) {
            if (isset($_POST[$key])) {
                $sql .= ", $key = '" . ${$key} . "'";
            }
        }

        $sql .= " WHERE uid='" . $uid . "'";

        my_query($sql);

        /*$new_amount = 5;
        $account = $bnb_address;
        $ct = SITE_CURRENCY_TKN;

        my_query("UPDATE user SET wallet_token= wallet_token+'$new_amount' WHERE uid='" . $uid . "'");
        my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `type`) VALUES ('" . $uid . "', '" . $new_amount . "', '" . date('c') . "', '2')");
        //my_query("INSERT INTO withdrawal_block (uid, amount, fee, net_amount, amount_coin, datetime, status, withdrawal_address, type, type2) VALUES ('" . $uid . "', '" . $new_amount . "', '0', '" . $new_amount . "', '" . $new_amount . "', '" . date('c') . "', 0, '" . $account . "', '" . $ct . "', '" . $ct . "')");

        $checkrefer = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id='" . $refer_id . "'"));
        if ($checkrefer == 10 || $checkrefer == 25 || $checkrefer == 50) {
            $new_amount = ($checkrefer >= 50) ? 20 : (($checkrefer >= 25) ? 10 : 2.5);
            //my_query("UPDATE user SET wallet_token= wallet_token+'$new_amount' WHERE uid='".$refer_id."'");

            my_query("UPDATE user SET wallet_topup= wallet_topup+'" . ($new_amount / TKN_RATE_USD) . "' WHERE uid='" . $refer_id . "'");
            my_query("INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, tamt, `datetime`, `type`, `remark`) VALUES ('" . $refer_id . "', '" . $refer_id . "', '" . ($new_amount / TKN_RATE_USD) . "','" . $new_amount . "', '" . date('c') . "', '4', 'Airdrop to topup R')");

            my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type) VALUES ('" . $refer_id . "','" . $uid . "','" . $new_amount . "','" . date('c') . "','0', 2)");
            //my_query("INSERT INTO withdrawal_block (uid, amount, fee, net_amount, amount_coin, datetime, status, withdrawal_address, type, type2) VALUES ('" . $uid . "', '" . $new_amount . "', '0', '" . $new_amount . "', '" . $new_amount . "', '" . date('c') . "', 0, '" . $account . "', '" . $ct . "', '" . $ct . "')");
        }

        /*************************
        $top = get_top_level_uids2($uid, 10);

        $level_amount = array(0.06, 0.05, 0.04, 0.03, 0.02, 0.01, 0.005, 0.005, 0.005, 0.005, 0.005, 0.005);

        $i = 0;
        $level = count($top);
        if ($level > 20) {
            $level = 20;
        }
        if ($level > 0) {
            while ($i < $level) {
                $value = $top[$i];
                if ($i < 6) {
                    $j = $i;
                } else {
                    $j = 6;
                }
                $percentage = $level_amount[$j];
                //$new_amount = $percentage * $amount;
                $new_amount = 0.5;
                $user2 = get_user_details($value);
                if ($user2->topup > 0 || 1) {
                    $checkrefer = mysqli_num_rows(my_query("SELECT uid FROM user WHERE uid='" . $value . "' AND topup >= 0"));
                    if ($checkrefer >= 10) {
                        my_query("UPDATE user SET wallet_topup= wallet_topup+'" . ($new_amount / TKN_RATE_USD) . "' WHERE uid='" . $value . "'");
                        my_query("INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, tamt, `datetime`, `type`, `remark`) VALUES ('" . $value . "', '" . $value . "', '" . ($new_amount / TKN_RATE_USD) . "', '" . $new_amount . "', '" . date('c') . "', '4', 'Airdrop to topup L')");
                    } else {
                        my_query("UPDATE user SET wallet_token= wallet_token+'$new_amount' WHERE uid='" . $value . "'");
                    }

                    /*if($i==0){
                        my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, ipid, iamount) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."', '".$recid."', '".$iamount."')");
                    }
                    else{*
                    $account = $user2->bnb_address;
                    my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, type) VALUES ('" . $value . "','" . $uid . "','" . $new_amount . "','" . date('c') . "','" . ($i + 1) . "', 1)");
                    //my_query("INSERT INTO withdrawal_block (uid, amount, fee, net_amount, amount_coin, datetime, status, withdrawal_address, type, type2) VALUES ('" . $value . "', '" . $new_amount . "', '0', '" . $new_amount . "', '" . $new_amount . "', '" . date('c') . "', 0, '" . $account . "', '" . $ct . "', '" . $ct . "')");
                    //}
                }
                $i++;
            }
        }

        $user2 = get_user_details($refer_id);
        $checkrefer = mysqli_num_rows(my_query("SELECT uid FROM user WHERE refer_id='" . $refer_id . "'"));
        if ($checkrefer >= 10 && $user2->wallet_token >= 5) {
            my_query("UPDATE user SET wallet_topup= wallet_topup+'" . ($user2->wallet_token / TKN_RATE_USD) . "', wallet_token=0 WHERE uid='" . $refer_id . "'");
            my_query("INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, tamt, `datetime`, `type`, `remark`) VALUES ('" . $refer_id . "', '" . $refer_id . "', '" . ($user2->wallet_token / TKN_RATE_USD) . "', '" . $user2->wallet_token . "', '" . date('c') . "', '4', 'Airdrop to topup C')");
        }
        /*************************/

        /* Send SMS */
        //$msg = "Dear $name, Your user id is $login_id and password is $password thank,s www.".SITE_URL;
        $msg = "Dear $name Your id no. is $login_id and password is $password thank,s " . SITE_URL . ".";
        send_sms($mobile, $msg);

        if (isset($_SESSION['placement_id'])) {
            unset($_SESSION['placement_id']);
        }
        if (isset($_SESSION['position'])) {
            unset($_SESSION['position']);
        }
        if (isset($_SESSION['otp'])) {
            unset($_SESSION['otp']);
        }
        if (isset($_SESSION['otp_type'])) {
            unset($_SESSION['otp_type']);
        }
        if (isset($_SESSION['otp_email'])) {
            unset($_SESSION['otp_email']);
        }

        $uniqid = uniqid();
        $_SESSION['uniqid'] = $uniqid;
        $_SESSION['pass'] = $password;
        redirect("register_success.php?uid=$uid&&uniqid=$uniqid");
        die();
    }
}
redirect($_SERVER['HTTP_REFERER']);
redirect('register.php');


/* get top level uids */
function get_top_level_uids2($uid, $level = 0, $arr = array())
{
    global $link;
    $result = my_query("SELECT refer_id FROM user WHERE uid = '$uid'");
    if (count($arr) == $level && $level != 0) {
        return $arr;
    } elseif ($uid == 100) {
        return $arr;
    }
    if (my_num_rows($result) > 0) {
        $data = my_fetch_array($result);
        $arr[count($arr)] = $data[0];
        return get_top_level_uids2($data[0], $level, $arr);
    } else {
        return $arr;
    }
}
?>