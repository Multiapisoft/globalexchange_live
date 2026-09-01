<?php
/* define constant */
define("SMS_USERNAME", "");
define("SMS_PASSWORD", "");
define("SMS_SENDER_ID", "");
define("RECHARGE_USERNAME", "503138");
define("RECHARGE_PASSWORD", "3a8b8550ced237c2f0615c00ffef7f1f");
$site_details = get_site_details();
define("SITE_PHONE", $site_details->phone);
define("SITE_MOBILE", $site_details->mobile);
define("SITE_NAME", $site_details->site_name);
define("SITE_SLOGAN", $site_details->site_slogan);
define("SITE_URL", $site_details->site_url);
define("SITE_EMAIL", $site_details->email);
define("SITE_EMAIL_INFO", $site_details->email_info);
define("SITE_EMAIL_MAIL", $site_details->email_mail);
define("SITE_EMAIL_SUPPORT", $site_details->email_support);
define("SITE_EMAIL_SALES", $site_details->email_sales);
define("SITE_TDS", $site_details->tds);
define("SITE_SERVICE_TAX", $site_details->service_tax);
define("SITE_SERVICE", $site_details->service);
define("PRODUCT_TDS", 10);
define("PRODUCT_SERVICE_TAX", 0);
define("PRODUCT_SERVICE", 0);
define("PRODUCT_SERVICE_M", 250);
define("SITE_WORKING_STATUS", $site_details->working_status);
define("SITE_SMS_COUNT", $site_details->sms_count);
define("SITE_VISITORS", $site_details->visitors);
define("USER_IP", isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
define("SITE_CURRENCY_", 'BNB');
define("SITE_CURRENCY_TOKEN", 'USDT');
define("SITE_CURRENCY_TKN", 'USDT');
define("SITE_CURRENCY", 'USDT ');
define("SITE_CURRENCY_S", 'USDT ');
define("SITE_CURRENCY_CLASS", 'USDT');
define("PRODUCT_OFF", 20);
define("SITE_ADRESS", $site_details->address);
define("SITE_CITY", $site_details->city);
define("SITE_STATE", $site_details->state);
define("SITE_COUNTRY", 'IN');
define("SITE_PINCODE", $site_details->pincode);
define("BV_VALUE", $site_details->bv_value);
define("CAPPING_BINARY", $site_details->capping_binary);
define("CAPPING_GIFT", $site_details->capping_gift);
define("SITE_LOGO", $site_details->logo);
define("SITE_USER_SMS_FORMAT", $site_details->user_sms_format);
define("DATETIME_FORMAT", "d M, Y h:i A");
define("DATE_FORMAT", "d M, Y");
define("TIME_FORMAT", "h:i A");
define("SITE_LOGIN_ID_TEXT", "Login Id");
define("SITE_API_TYPE", 0);
define("ACNAME", 'Wallet');
define("BOX_TOP_BORDER", "info");
define("AUTO_WD", 1);
define("SITE_G_S_DATE", '');
define("B_RATE", 1);
define("S_RATE", 1);
$tknr = round(get_tkn_price(), 4);
define("SITE_OTP", $site_details->otp);

if ($tknr <= 0) {
    $tknp = $site_details->coin_rate * 1;
} else {
    $tknp = round(1 / $tknr, 4);
}

if ($tknr <= 0) {
    $tknr = $site_details->b_rate * 1;
}

define("TKN_RATE_USD", $tknp);

define("B_RATE_", $tknr * 1);
define("S_RATE_", $site_details->s_rate * 1);

function get_tkn_price()
{
    return 1;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.coinbrain.com/public/coin-info');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"56\": [\"0x222929Fe31dECb2aF4dEa82864545D1eE630226A\"]}");

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    $arr = json_decode($result, true);
    return isset($arr[0]['priceUsd']) ? $arr[0]['priceUsd'] : 0;
}

/* generate a member id */
function generateId()
{
    $id = rand(1, 7);
    for ($i = 1; $i <= 5; $i++) {
        $id = $id . rand(0, 9);
    }
    return $id;
}

/* create a unique user id */
function createId()
{
    global $link;
    $id = generateId();
    $result = my_query("SELECT uid FROM user WHERE uid = '" . $id . "'");
    if (my_num_rows($result)) {
        return createId();
    } else {
        return $id;
    }
}

/* my_real_escape_string and trim */
function tres($text)
{
    global $link;
    return trim(my_real_escape_string($text));
}

function normalize_otp($otp)
{
    return preg_replace('/\D+/', '', (string) $otp);
}

function store_email_otp($otp, $type, $email, $uid = 0)
{
    $otp = normalize_otp($otp);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_type'] = (string) $type;
    $_SESSION['otp_email'] = strtolower(trim((string) $email));
    $_SESSION['otp_time'] = time();
    if ($uid) {
        try {
            my_query("UPDATE user SET otp='" . tres($otp) . "', otp_time=NOW() WHERE uid='" . (int) $uid . "'");
        } catch (Throwable $e) {
        }
    }
}

function verify_email_otp($inputOtp, $type, $email, $user = null)
{
    $input = normalize_otp($inputOtp);
    if ($input === '' || strlen($input) < 4) {
        return false;
    }

    $stored = normalize_otp(isset($_SESSION['otp']) ? $_SESSION['otp'] : '');
    $storedType = isset($_SESSION['otp_type']) ? (string) $_SESSION['otp_type'] : '';
    $storedEmail = strtolower(trim((string) (isset($_SESSION['otp_email']) ? $_SESSION['otp_email'] : '')));
    $checkEmail = strtolower(trim((string) $email));

    if ($stored !== '' && $storedType === (string) $type && $storedEmail !== '' && $storedEmail === $checkEmail && hash_equals($stored, $input)) {
        return true;
    }

    if ($user && !empty($user->otp)) {
        $dbOtp = normalize_otp($user->otp);
        $otpTime = !empty($user->otp_time) ? strtotime($user->otp_time) : 0;
        $fresh = $otpTime && (time() - $otpTime) <= 600;
        if ($fresh && $dbOtp !== '' && hash_equals($dbOtp, $input)) {
            return true;
        }
    }

    return false;
}

function clear_email_otp($uid = 0)
{
    unset($_SESSION['otp'], $_SESSION['otp_type'], $_SESSION['otp_email'], $_SESSION['otp_time']);
    if ($uid) {
        my_query("UPDATE user SET otp='' WHERE uid='" . (int) $uid . "'");
    }
}

/* HEADER LOCATION */
function redirect($location)
{
    echo '<script>window.location.href="' . $location . '";</script>';
}

/* set msg */
function setMessage($message, $type)
{
    $type = str_replace('alert alert-', '', $type);
    $type = str_replace('error', 'danger', $type);
    $msg = '';

    if ($type == 'success') {
        $msg = 'Success!';
    } elseif ($type == 'danger') {
        $msg = 'Error!';
    }

    $_SESSION['SetMessage'] = '<div class="alert alert-' . $type . ' alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <strong>' . $msg . '</strong> ' . $message . '
    </div>';
}

/* get msg */
function getMessage()
{
    $GetMessage = @$_SESSION['SetMessage'];
    unset($_SESSION['SetMessage']);
    return $GetMessage;
}

function randomString()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < 10; $i++) {
        $randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}

/* two to single dimensional array */
function get_single_dimensional($two_dimensional)
{
    $single_dimensional_array = array();
    foreach ($two_dimensional as $single_dimensional) {
        foreach ($single_dimensional as $value) {
            $single_dimensional_array[] = $value;
        }
    }
    return $single_dimensional_array;
}

/* get child levels */
function get_child_levels($uid, $with = '', $_level = 0)
{
    global $link;
    //with uid
    if ($with == 'yes') {
        $level = array(array($uid));
    } else {
        $level = array();
    }
    $result = my_query("SELECT uid FROM user WHERE placement_id = '$uid'");
    if (my_num_rows($result) != 0) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->uid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT uid FROM user WHERE placement_id = '$value'");
                //if(my_num_rows($result) != 0){
                while ($row = my_fetch_object($result)) {
                    $level[$i + 1][] = $row->uid;
                }
                //}
            }

            if (!empty($level[$i + 1])) {
                $i++;
                if ($_level && $i == $_level) {
                    break;
                } else {
                    continue;
                }
            } else {
                //return $level;
                break;
            }
        }
    }
    return $level;
}

/* get child levels */
function get_child_levels_refer_($uid, $with = '')
{
    global $link;
    //with uid
    if ($with == 'yes') {
        $level = array(array($uid));
    } else {
        $level = array();
    }
    $result = my_query("SELECT uid FROM user WHERE refer_id = '$uid'");
    if (my_num_rows($result) != 0) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->uid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT uid FROM user WHERE refer_id = '$value'");
                //if(my_num_rows($result) != 0){
                while ($row = my_fetch_object($result)) {
                    $level[$i + 1][] = $row->uid;
                }
                //}
            }
            // if($i==4){break;}
            if (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                //return $level;
                break;
            }
        }
    }
    return $level;
}

/* get child levels */
function get_child_levels_auto($uid, $with = '', $_level = 0)
{
    global $link;
    //with uid
    if ($with == 'yes') {
        $level = array(array($uid));
    } else {
        $level = array();
    }
    $result = my_query("SELECT uid FROM user WHERE placement_id2 = '$uid'");
    if (my_num_rows($result) != 0) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->uid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT uid FROM user WHERE placement_id2 = '$value'");
                //if(my_num_rows($result) != 0){
                while ($row = my_fetch_object($result)) {
                    $level[$i + 1][] = $row->uid;
                }
                //}
            }

            if (!empty($level[$i + 1])) {
                $i++;
                if ($_level && $i == $_level) {
                    break;
                } else {
                    continue;
                }
            } else {
                //return $level;
                break;
            }
        }
    }
    return $level;
}



/* get child levels */
function get_child_levels_by_pool($uid, $pool = 0, $with = '')
{
    global $link;
    if ($pool < 2) {
        $pool = '';
    }
    $pfield = 'placement_id' . $pool;
    //with uid
    if ($with == 'yes') {
        $level = array(array($uid));
    } else {
        $level = array();
    }
    $result = my_query("SELECT uid FROM user WHERE $pfield = '$uid'");
    if (my_num_rows($result) != 0) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->uid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT uid FROM user WHERE $pfield = '$value'");
                //if(my_num_rows($result) != 0){
                while ($row = my_fetch_object($result)) {
                    $level[$i + 1][] = $row->uid;
                }
                //}
            }
            // if($i==4){break;}
            if (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                //return $level;
                break;
            }
        }
    }
    return $level;
}

/* get top level uids */
function get_top_level_uids($uid, $level = 0, $arr = array())
{
    global $link;
    $result = my_query("SELECT placement_id FROM user WHERE uid = '$uid'");
    if (count($arr) == $level && $level != 0) {
        return $arr;
    } elseif ($uid == 100) {
        return $arr;
    }
    if (my_num_rows($result) > 0) {
        $data = my_fetch_array($result);
        $arr[count($arr)] = $data[0];
        return get_top_level_uids($data[0], $level, $arr);
    } else {
        return $arr;
    }
}

/* get top level uids */
function get_top_level_uids_by_pool($uid, $level = 0, $pool = 0, $arr = array())
{
    global $link;
    if ($pool < 2) {
        $pool = '';
    }
    $pfield = 'placement_id' . $pool;
    $result = my_query("SELECT $pfield FROM user WHERE uid = '$uid'");
    if (count($arr) == $level && $level != 0) {
        return $arr;
    } elseif ($uid == 100) {
        return $arr;
    }
    if (my_num_rows($result) > 0) {
        $data = my_fetch_array($result);
        $arr[count($arr)] = $data[0];
        return get_top_level_uids_by_pool($data[0], $level, $pool, $arr);
    } else {
        return $arr;
    }
}

/* check if a user is in another user's upline or downline */
function check_user_relation($current_uid, $target_uid)
{
    // self check
    if ($current_uid == $target_uid) {
        return 'self';
    }

    // downline check: all placement-based descendants of current user
    $downlines = get_single_dimensional(get_child_levels($current_uid, 'yes'));
    if (!empty($downlines) && in_array($target_uid, $downlines)) {
        return 'downline';
    }

    // upline check: all placement-based ancestors of current user
    $uplines = get_top_level_uids($current_uid, 0);
    if (!empty($uplines) && in_array($target_uid, $uplines)) {
        return 'upline';
    }

    // no direct placement relation found
    return 'none';
}

/* get terminal id */
function get_terminal_id($refer_id, $position)
{
    global $link;
    do {
        $result = my_query("SELECT uid FROM user WHERE placement_id = '$refer_id' AND position = '$position'");
        $row = my_fetch_object($result);
        if (my_num_rows($result) == 1) {
            $refer_id = $row->uid;
            continue;
        } else {
            break;
        }
    } while (TRUE);
    return $refer_id;
}

/* get placement id */
function get_placement_id($refer_id, $unum = 2)
{
    global $link;
    $level = array();
    $placement_id = $refer_id;

    $result = my_query("SELECT uid FROM user WHERE placement_id = '$refer_id'");
    $num = my_num_rows($result);
    if ($num >= $unum) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->uid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT uid FROM user WHERE placement_id = '$value'");
                if (my_num_rows($result) >= $unum) {
                    while ($row = my_fetch_object($result)) {
                        $level[$i + 1][] = $row->uid;
                    }
                } else {
                    $placement_id = $value;
                    return $placement_id;
                    break;
                }
            }
            if (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                break;
            }
        }
    }
    return $placement_id;
}

function get_placement_id2($refer_id, $unum = 2)
{
    global $link;
    $level = array();
    $placement_id = $refer_id;

    $result = my_query("SELECT uid FROM user WHERE placement_id2 = '$refer_id'");
    $num = my_num_rows($result);
    if ($num >= $unum) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->uid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT uid FROM user WHERE placement_id2 = '$value'");
                if (my_num_rows($result) >= $unum) {
                    while ($row = my_fetch_object($result)) {
                        $level[$i + 1][] = $row->uid;
                    }
                } else {
                    $placement_id = $value;
                    return $placement_id;
                    break;
                }
            }
            if (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                break;
            }
        }
    }
    return $placement_id;
}

function get_placement_id_by_pool($refer_id, $pool = 0, $unum = 2)
{
    global $link;
    $level = array();
    $placement_id = $refer_id;
    if ($pool < 2) {
        $pool = '';
    } elseif ($pool > 24) {
        $pool = 24;
    }
    $pfield = 'placement_id' . $pool;
    $_pt = 'pt' . $pool;

    $result = my_query("SELECT uid FROM user WHERE $pfield = '$refer_id' ORDER BY $_pt ASC");
    $num = my_num_rows($result);
    if ($num >= $unum) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->uid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT uid FROM user WHERE $pfield = '$value' ORDER BY $_pt ASC");
                if (my_num_rows($result) >= $unum) {
                    while ($row = my_fetch_object($result)) {
                        $level[$i + 1][] = $row->uid;
                    }
                } else {
                    $placement_id = $value;
                    return $placement_id;
                    break;
                }
            }
            if (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                break;
            }
        }
    }
    return $placement_id;
}

/* get direct refer */
function get_direct_refer_uids($uid, $position = '')
{
    global $link;
    $level = array();
    $result = my_query("SELECT uid FROM user WHERE placement_id = '$uid' AND position = '$position'");
    if (my_num_rows($result) != 0) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->uid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT uid FROM user WHERE placement_id = '$value' AND position = '$position'");
                //if(my_num_rows($result) != 0){
                while ($row = my_fetch_object($result)) {
                    $level[$i + 1][] = $row->uid;
                }
                //}
            }
            // if($i==4){break;}
            if (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                //return $level;
                break;
            }
        }
    }
    return get_single_dimensional($level);
}

/* get group child */
function get_group_child_uids($uid, $position = '')
{
    global $link;
    $childs_all = get_single_dimensional(get_child_levels_position($uid, $position));
    $childs_direct = get_direct_refer_uids($uid, $position);

    $group_uids = array();
    foreach ($childs_all as $key => $value) {
        if (!in_array($value, $childs_direct)) {
            $group_uids[] = $value;
        }
    }
    return $childs_all;
}

/* get count child ids */
function get_count_child_ids($uidp, $position)
{
    global $link;
    $count = 0;

    if ($position == 'L') {
        $uid = my_fetch_object(my_query("SELECT uid FROM user WHERE placement_id = '$uidp' AND position = 'L'"))->uid;
    } elseif ($position == 'R') {
        $uid = my_fetch_object(my_query("SELECT uid FROM user WHERE placement_id = '$uidp' AND position = 'R'"))->uid;
    }

    if (!empty($uid)) {
        $level = array(array($uid));
        $count++;
        $result = my_query("SELECT uid FROM user WHERE placement_id = '$uid'");
        if (my_num_rows($result) != 0) {
            $i = 1;
            while ($row = my_fetch_object($result)) {
                $level[$i][] = $row->uid;
                $count++;
            }
            while (TRUE) {
                foreach ($level[$i] as $value) {
                    $result = my_query("SELECT uid FROM user WHERE placement_id = '$value'");
                    //if(my_num_rows($result) != 0){
                    while ($row = my_fetch_object($result)) {
                        $level[$i + 1][] = $row->uid;
                        $count++;
                    }
                    //}
                }
                // if($i==4){break;}
                if (!empty($level[$i + 1])) {
                    $i++;
                    continue;
                } else {
                    //return $level;
                    break;
                }
            }
        }
    }
    return $count;
}

/* get child levels by position */

function get_child_levels_position($uid, $position, $l = '')
{
    global $link;
    $level = array();
    $result = my_query("SELECT uid FROM user WHERE placement_id = '$uid' AND position = '$position'");
    if (my_num_rows($result) != 0) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->uid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT uid FROM user WHERE placement_id = '$value'");
                //if(my_num_rows($result) != 0){
                while ($row = my_fetch_object($result)) {
                    $level[$i + 1][] = $row->uid;
                }
                //}
            }
            if ($l && $i == $l) {
                break;
            }
            if (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                //return $level;
                break;
            }
        }
    }
    return $level;
}

/* get user details */
function get_user_details($uid)
{
    global $link;
    $user_details = my_fetch_object(my_query("SELECT * FROM user WHERE uid = '$uid'"));
    return $user_details;
}

/* get site details */
function get_site_details()
{
    global $link;
    $site_details = my_fetch_object(my_query("SELECT * FROM admin WHERE recid = 1"));
    return $site_details;
}

/* admin authentication */
function admin()
{
    if (!isset($_SESSION['adminid']) && empty($_SESSION['adminid'])) {
        redirect('./');
        die();
    }
}

/* user authentication */
function user()
{
    $uid = (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) ? $_SESSION['userid'] : 0;
    $user = get_user_details($uid);
    if (!$uid || !$user || $user->status) {
        redirect('./');
        die();
    }
}

/* encrypted password */
function encryptPassword($plainPassword)
{
    $salt = '987456321&';
    $pepper = 'lkjfghdsa';
    return sha1($salt . sha1($plainPassword . $pepper));
}

/* check user_id */
function checkLoginId($plainLoginId)
{
    if (strlen($plainLoginId) <= 100 && strlen($plainLoginId) >= 6) {
        return 1;
    } else {
        return 0;
    }
}

/* check registered user_id */
function registeredUserId($plainLoginId)
{
    global $link;
    $result = my_query("SELECT uid FROM user WHERE login_id='$plainLoginId' AND status=0");
    if (checkLoginId($plainLoginId) == 0) {
        return 0;
    } elseif (my_num_rows($result) != 1) {
        return 0;
    } else {
        return my_fetch_object($result)->uid;
    }
}

/* check user_id availability */
function checkLoginIdAvailability($plainLoginId, $uid = 0)
{
    global $link;
    if ($uid != 0) {
        $check = my_num_rows(my_query("SELECT uid FROM user WHERE login_id='" . $plainLoginId . "' AND uid!='$uid'"));
    } else {
        $check = my_num_rows(my_query("SELECT uid FROM user WHERE login_id='" . $plainLoginId . "'"));
    }

    if ($check == 0) {
        return 1;
    } else {
        return 0;
    }
}

/* check password */
function checkPassword($plainPassword)
{
    if (strlen($plainPassword) <= 20 && strlen($plainPassword) >= 6) {
        return 1;
    } else {
        return 0;
    }
}

/* check login */
// function checkLogin()
// {
//     global $link;
//     $login_id = tres($_POST['login_id']);
//     $password = tres($_POST['password']);

//     if ((checkLoginId($login_id) == 1 || checkEmail($login_id) == 1) && checkPassword($password) == 1 && SITE_WORKING_STATUS == 0) {
//         $password_md5 = encryptPassword($password);
//         $result = my_query("SELECT uid, type, login_id, password FROM user WHERE status=0 AND (login_id='" . $login_id . "' OR email='" . $login_id . "') AND password='" . $password_md5 . "'");
//         if (my_num_rows($result) == 1) {
//             $row = my_fetch_object($result);
//             // echo my_num_rows($result);
//             // exit;
//             if (($row->login_id == $login_id || $row->email == $login_id) && $row->password == $password_md5) {
//                 $_SESSION['loginid'] = $row->login_id;
//                 $_SESSION['userid'] = $row->uid;
//                 $_SESSION['type'] = $row->type;

//                 /* login detail */
//                 my_query("INSERT INTO user_login_detail (`uid`, `datetime`, `ip`) VALUES ('" . $row->uid . "', '" . date('c') . "', '" . USER_IP . "')");
//                 //my_query( "INSERT INTO sql_inject_pattern (`login_id`, `password`, `datetime`, `ip`) VALUES ('".$login_id."', '".$password."', '".date('c')."', '".USER_IP."')");

//                 redirect($_REQUEST['url']);
//                 die();
//             }
//         }
//     }
//     setMessage('Login failed', 'error');
//     redirect('index.php');
// }


function checkLogin()
{
    global $link;
    $login_id = tres($_POST['login_id']);  // can be login_id or email
    $password = tres($_POST['password']);

    // check if user exists (by login_id OR email)
    if ((checkLoginId($login_id) == 1 || checkEmail($login_id) == 1) && checkPassword($password) == 1 && SITE_WORKING_STATUS == 0) {
        $password_md5 = encryptPassword($password);

        $result = my_query("
            SELECT uid, type, login_id, email, password 
            FROM user 
            WHERE status=0 
            AND (login_id='" . $login_id . "' OR email='" . $login_id . "') 
            AND password='" . $password_md5 . "'
        ");

        if (my_num_rows($result) == 1) {
            $row = my_fetch_object($result);

            // confirm login
            if (($row->login_id == $login_id || $row->email == $login_id) && $row->password == $password_md5) {
                $_SESSION['loginid'] = $row->login_id;
                $_SESSION['userid'] = $row->uid;
                $_SESSION['type'] = $row->type;

                /* login detail */
                my_query("INSERT INTO user_login_detail (`uid`, `datetime`, `ip`) VALUES ('" . $row->uid . "', '" . date('c') . "', '" . USER_IP . "')");

                redirect($_REQUEST['url']);
                die();
            }
        }
    }

    setMessage('Login failed', 'error');
    redirect('index.php');
}


/* check login */
function checkLoginAdmin()
{
    global $link;
    $login_id = tres($_POST['login_id']);
    $password = tres($_POST['password']);
    if (checkLoginId($login_id) == 1 && checkPassword($password) == 1) {
        $password_md5 = encryptPassword($password);
        $result = my_query("SELECT recid, login_id, password FROM admin WHERE status=0 AND login_id='" . $login_id . "' AND password='" . $password_md5 . "'");
        if (my_num_rows($result) == 1) {
            $row = my_fetch_object($result);
            if ($row->login_id == $login_id && $row->password == $password_md5) {
                $_SESSION['adminid'] = $row->recid;

                /* login detail */
                //my_query( "INSERT INTO admin_login_detail (`datetime`, `ip`) VALUES ('".date('c')."', '".USER_IP."')");
                redirect('dashboard.php');
                die();
            }
        }
    }
    setMessage('Login failed', 'error');
    redirect('index.php');
}

/* check mobile */
function checkMobile($plainMobile)
{
    if (strlen($plainMobile) == 10 && ctype_digit($plainMobile)) {
        return 1;
    } else {
        return 0;
    }
}

/* check mobile availability */
function checkMobileAvailability($plainMobile, $uid = 0, $count = 1)
{
    global $link;
    if ($uid != 0) {
        $check = my_num_rows(my_query("SELECT uid FROM user WHERE mobile='" . $plainMobile . "' AND uid!='$uid'"));
    } else {
        $check = my_num_rows(my_query("SELECT uid FROM user WHERE mobile='" . $plainMobile . "'"));
    }

    if ($check < $count) {
        return 1;
    } else {
        return 0;
    }
}

/* check email */
function checkEmail($plainEmail)
{
    if ($plainEmail) {
        return 1;
    } else {
        return 0;
    }
}

/* check email availability */
function checkEmailAvailability($plainEmail, $uid = 0, $count = 5)
{
    global $link;
    if ($uid != 0) {
        $check = my_num_rows(my_query("SELECT uid FROM user WHERE email='" . $plainEmail . "' AND uid!='$uid'"));
    } else {
        $check = my_num_rows(my_query("SELECT uid FROM user WHERE email='" . $plainEmail . "'"));
    }

    if ($check < $count) {
        return 1;
    } else {
        return 0;
    }
}

/* check decimal */

function checkDecimal($decimal, $sing = '>')
{
    if (!is_numeric($decimal)) {
        return 0;
    } elseif ($decimal < 0 && $sing == '>') {
        return 0;
    } else {
        return 1;
    }
}

/* check digit */

function checkDigit($digit)
{
    if (!ctype_digit($digit)) {
        return 0;
    } elseif ($digit == '') {
        return 0;
    } elseif ($digit <= 0) {
        return 0;
    } else {
        return 1;
    }
}

function checkPin($plainPin, $plainType = '')
{
    global $link;
    $query_pin = "SELECT pinumber, type, pid FROM pins WHERE pinumber='" . $plainPin . "' AND status!='used'";
    if ($plainType != '') {
        $query_pin .= "type='" . $plainType . "'";
    }
    $result_pin = my_query($query_pin);
    if (strlen($plainPin) > 30 && strlen($plainPin) < 8) {
        return 0;
    } elseif (checkDigit($plainPin) == 0) {
        return 0;
    } elseif (checkDigit($plainPin) == 0) {
        return 0;
    } elseif (my_num_rows($result_pin) != 1) {
        return 0;
    } elseif (my_num_rows($result_pin) == 1) {
        $row_pin = my_fetch_object($result_pin);
        return array($row_pin->pinumber, $row_pin->type, $row_pin->pid);
    }
    return 0;
}

function get_unread_message_count($uid = 0)
{
    global $link;
    return my_fetch_object(my_query("SELECT COUNT(recid) as count FROM `message` WHERE `receiver`='" . $uid . "' AND `read`=0"))->count;
}

function get_user_type()
{
    //return $user_type_arr = array(0=>'RETAILER', 1=>'DISTRIBUTOR', 2=>'MASTER DISTRIBUTOR', 3=>'ADMIN', 4=>'SUPER ADMIN', 5=>'MASTER ADMIN');
    return $user_type_arr = array(0 => 'Normal', 1 => 'Franchise');
}

function get_reward()
{
    //return $reward_arr = array(0 => 'No Rank', 1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'Team Founder Manager', 8=>'Manager', 9=>'S Manager', 10=>'G Manager', 11=>'Core Director', 12=>'Director', 13=>'Core Chairman', 14=>'Chairman', 15=>'Royal Chairman');
    return $reward_arr = array(0 => 'No Rank', 1 => 'Entry Trader', 2 => 'Smart Trader', 3 => 'Prime Trader', 4 => 'Elite Trader', 5 => 'Double Eagle Executive', 6 => 'Sapphire Executive', 7 => 'Diamond Executive', 8 => 'Blue Diamond Executive', 9 => 'Black Diamond Executive', 10 => 'Royal Diamond', 11=>'The Ambassador', 12=>'Universal Ambassador', 13=>'Crown Ambassador', 14=>'The Royal King');
}

/** Royalty rank names (for dashboard/reports) - same as cron_royalty.php */
function get_royalty_rank_names()
{
    return array(0 => '-', 1 => 'SMART', 2 => 'PLAYER', 3 => 'MASTER', 4 => 'PROFESSION', 5 => 'EXPERT', 6 => 'DIAMOND', 7 => 'DIRECTOR', 8 => 'PRESIDENT', 9 => 'AMBASSADOR', 10 => 'CROWN AMBASSADOR');
}

function get_pin_type()
{
    return $pin_type_arr = array('a' => 'Laptour Kit 1', 'b' => 'Laptour Kit 2', 'c' => 'Laptour Kit 3', 'd' => 'Laptour Kit 4', 'e' => 'Laptour Kit 5', 'f' => 'Alto Kit 1', 'g' => 'Alto Kit 2', 'h' => 'Eritga Kit 1', 'i' => 'Eritga Kit 2', 'j' => 'Flat Kit 1', 'k' => 'Flat Kit 2', 'l' => 'Benz Kit 1', 'm' => 'Benz Kit 2', 'o' => 'LapTour Kit 6', 'p' => 'LapTour Kit 7', 'q' => 'POSITION', 'r' => 'LapTour Kit 8', 's' => 'LapTour Kit 9', 't' => 'LapTour Kit 10', 'u' => 'Business', 'v' => 'MRP1', 'w' => 'MRP2', 'x' => 'MRP3');
}

function get_pin_amount()
{
    return $pin_amount_arr = array('a' => 2000, 'b' => 70000, 'c' => 1650, 'd' => 200, 'e' => 12, 'f' => 13, 'g' => 14, 'h' => 15, 'i' => 16, 'j' => 17, 'k' => 18, 'l' => 19, 'm' => 20, 'o' => 18, 'p' => 19, 'q' => 0, 'r' => 20, 's' => 20, 't' => 0, 'u' => 0, 'v' => 0, 'w' => 0, 'x' => 0);
}

function pin_type_length()
{
    return $pin_type_length_arr = array('a' => 8, 'b' => 9, 'c' => 10, 'd' => 11, 'e' => 12, 'f' => 13, 'g' => 14, 'h' => 15, 'i' => 16, 'j' => 17, 'k' => 18, 'l' => 19, 'm' => 20, 'o' => 8, 'p' => 8, 'q' => 8, 'r' => 8, 's' => 8, 't' => 8, 'u' => 8, 'v' => 8, 'w' => 8, 'x' => 8);
}

function get_pin_pbv()
{
    return $pin_pbv_arr = array('a' => 200, 'b' => 300, 'c' => 300, 'd' => 300, 'e' => 500, 'f' => 500, 'g' => 500, 'h' => 1000, 'i' => 1000, 'j' => 1000, 'k' => 2000, 'l' => 5000, 'm' => 5000, 'o' => 500, 'p' => 400, 'q' => 0, 'r' => 300, 's' => 250, 't' => 0, 'u' => 0, 'v' => 50, 'w' => 50, 'x' => 100);
}

function get_pin_gbv()
{
    return $pin_gbv_arr = array('a' => 200, 'b' => 50, 'c' => 50, 'd' => 50, 'e' => 100, 'f' => 300, 'g' => 300, 'h' => 500, 'i' => 500, 'j' => 500, 'k' => 1000, 'l' => 3000, 'm' => 3000, 'o' => 100, 'p' => 100, 'q' => 0, 'r' => 100, 's' => 100, 't' => 0, 'u' => 0, 'v' => 50, 'w' => 50, 'x' => 100);
}

function get_pin_lbv()
{
    return $pin_lbv_arr = array('a' => 0, 'b' => 50, 'c' => 100, 'd' => 500, 'e' => 300, 'f' => 500, 'g' => 500, 'h' => 1000, 'i' => 1000, 'j' => 500, 'k' => 1000, 'l' => 2000, 'm' => 2000, 'o' => 300, 'p' => 400, 'q' => 0, 'r' => 200, 's' => 300, 't' => 0, 'u' => 0, 'v' => 50, 'w' => 100, 'x' => 100);
}

function get_pin_rp()
{
    return $pin_rp_arr = array('a' => 1, 'b' => 1, 'c' => 2, 'd' => 3, 'e' => 3, 'f' => 4, 'g' => 4, 'h' => 8, 'i' => 8, 'j' => 18, 'k' => 35, 'l' => 25, 'm' => 25, 'o' => 3, 'p' => 5, 'q' => 0, 'r' => 3, 's' => 3, 't' => 0, 'u' => 0, 'v' => 0.5, 'w' => 0.5, 'x' => 0.5);
}

function get_fund_type()
{
    //return $fund_type_arr = array(0=>'A/C', 1=>'Recharge');
    //return $fund_type_arr = array(0=>'Wallet', 3=>'Wallet Topup', 4=>'Wallet');
    return $fund_type_arr = array(0 => 'Wallet', 3 => 'Wallet Topup');
}

function get_wallet_field()
{
    return $wallet_field_arr = array(0 => 'wallet', 1 => 'balance', 3 => 'wallet_topup', 4 => 'wallet_token');
}

function get_withdrawal_type()
{
    //return $withdrawal_type_arr = array(0=>'Bank', 1=>'Recharge');
    return $withdrawal_type_arr = array(0 => 'Bank');
}

function get_wallet($uid, $field)
{
    global $link;
    return my_fetch_object(my_query("SELECT $field FROM user WHERE uid='" . $uid . "'"))->$field;
}

function get_category_type()
{
    return $category_type_arr = array(0 => 'News', 1 => 'Gallery', 2 => 'Video', 3 => 'Product');
}

function checkCMSCategoryAvailability($category, $cmid, $recid = 0)
{
    global $link;
    if ($recid != 0) {
        $check = my_num_rows(my_query("SELECT recid FROM cms_categories WHERE category='" . $category . "' AND cmid='" . $cmid . "' AND recid!='$recid'"));
    } else {
        $check = my_num_rows(my_query("SELECT recid FROM cms_categories WHERE category='" . $category . "' AND cmid='" . $cmid . "'"));
    }

    if ($check == 0) {
        return 1;
    } else {
        return 0;
    }
}

function checkCategoryAvailability($category, $type, $recid = 0)
{
    global $link;
    if ($recid != 0) {
        $check = my_num_rows(my_query("SELECT recid FROM products_categories WHERE category='" . $category . "' AND type='" . $type . "' AND recid!='$recid'"));
    } else {
        $check = my_num_rows(my_query("SELECT recid FROM products_categories WHERE category='" . $category . "' AND type='" . $type . "'"));
    }

    if ($check == 0) {
        return 1;
    } else {
        return 0;
    }
}

function checkPoductAvailability($name, $cid, $recid = 0)
{
    global $link;
    if ($recid != 0) {
        $check = my_num_rows(my_query("SELECT recid FROM products WHERE name='" . $name . "' AND cid='" . $cid . "' AND recid!='$recid'"));
    } else {
        $check = my_num_rows(my_query("SELECT recid FROM products WHERE name='" . $name . "' AND cid='" . $cid . "'"));
    }

    if ($check == 0) {
        return 1;
    } else {
        return 0;
    }
}

function get_sum($table, $field, $where = '')
{
    global $link;
    $query_sum = "SELECT SUM($field) as total_sum FROM $table";
    if ($where != '') {
        $query_sum .= " WHERE $where";
    }

    $res = my_query($query_sum);
    if (!$res) {
        return 0;
    }
    $row = my_fetch_object($res);
    if (!$row || !isset($row->total_sum) || $row->total_sum === null) {
        return 0;
    }
    return (float) $row->total_sum;
}

function get_count($table, $field, $where = '')
{
    global $link;
    $query_count = "SELECT COUNT($field) as count FROM $table";
    if ($where != '') {
        $query_count .= " WHERE $where";
    }
    return $count = my_fetch_object(my_query($query_count))->count;
}

/* get downline pbv */

function get_child_pbv($uid, $position = '')
{
    $childs_uids = get_single_dimensional(get_child_levels_position($uid, $position));
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('orders', 'bv', "uid IN ( $uid_in )");
}

function get_child_pbv_current($uid, $position = '', $status = 0)
{
    $childs_uids = get_single_dimensional(get_child_levels_position($uid, $position));
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('orders', 'bv', "uid IN ( $uid_in ) AND status_pbv = $status");
}

function get_child_pbv_current_inv($uid, $position = '', $status = 0)
{
    $childs_uids = get_single_dimensional(get_child_levels_position($uid, $position));
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('income_growth', 'amount', "uid IN ( $uid_in ) AND binary_status = $status");
}

function get_child_bv_current($uid, $position = '', $status = 0)
{
    $childs_uids = get_single_dimensional(get_child_levels_position($uid, $position));
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('income_growth', 'amount', "uid IN ( $uid_in ) AND binary_status = $status");
}

function get_child_bv_total($uid, $position = '')
{
    $childs_uids = get_single_dimensional(get_child_levels_position($uid, $position));
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('income_growth', 'amount', "uid IN ( $uid_in )");
}

function get_child_bv_total2($uid, $position = '')
{
    $childs_uids = get_single_dimensional(get_child_levels_position($uid, $position));
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('investments', 'amount', "uid IN ( $uid_in )");
}

/* get downline gbv */

function get_child_gbv($uid, $position = '')
{
    $childs_uids = get_group_child_uids($uid, $position);
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('orders', 'gbv', "uid IN ( $uid_in )");
}

function get_child_gbv_current($uid, $position = '', $status = 0)
{
    $childs_uids = get_group_child_uids($uid, $position);
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('orders', 'gbv', "uid IN ( $uid_in ) AND status_gbv = $status");
}

/* get downline lbv */

function get_child_lbv($uid)
{
    $childs_uids = get_single_dimensional(get_child_levels($uid));
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('orders', 'lbv', "uid IN ( $uid_in )");
}

/* get downline rp */

function get_child_rp($uid, $position = '')
{
    $childs_uids = get_single_dimensional(get_child_levels_position($uid, $position));
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('orders', 'rp', "uid IN ( $uid_in )");
}

function get_child_rp_current($uid, $position = '', $status = 0)
{
    $childs_uids = get_group_child_uids($uid, $position);
    $uid_in = implode(" , ", $childs_uids);
    if (!$uid_in) {
        $uid_in = 0;
    }
    return get_sum('orders', 'rp', "uid IN ( $uid_in ) AND status_gbv = $status");
}

function get_product_type()
{
    return array(0 => 'Repurchase', 1 => 'Joining');
}

function get_product_detail($pid)
{
    global $link;
    return my_fetch_object(my_query("SELECT * FROM products WHERE recid='" . $pid . "'"));
}

function get_reward_capping()
{
    return array(
        0 => array(10, 20000),
        1 => array(12, 35000),
        2 => array(13, 50000),
        3 => array(14, 74000),
        4 => array(15, 100000),
        5 => array(16, 125000),
        6 => array(17, 150000),
        7 => array(18, 200000),
        8 => array(19, 225000),
        9 => array(20, 250000),
    );
}

/* send sms */
function send_sms($mobile, $message)
{
    return;
    if (!empty($mobile) && !empty($message)) {
        $message = str_replace(' ', '%20', $message);

        $url = "https://securesmpp.com1111/api/sendmessage.php?usr=coinsdeal&apikey=0881791F9F107085E4D7&sndr=NIUUPK&ph=$mobile&message=$message";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
        //echo $curl_scraped_page;
    }
}

/* get recharge type */
function get_recharge_type()
{
    return $recharge_type = array(0 => 'Mobile Recharge', 1 => 'DTH Recharge', 2 => 'Data Card Recharge', 3 => 'Postpaid', 4 => 'Landline/Broadband Billers', 5 => 'Electricity/Gas Billers');
}

/* get recharge type */
function get_recharge_type_by_operator($operator)
{
    global $link;
    return $recharge_type = my_fetch_object(my_query("SELECT type FROM `operator` WHERE `operator` = '" . $operator . "'"))->type;
}

/* get api balance */
function get_api_balance()
{
    //return 100000000000;
    $user_id = RECHARGE_USERNAME;
    $password = RECHARGE_PASSWORD;
    $ch = curl_init();
    $timeout = 60; // set to zero for no timeout"
    $myurl = "http://rechargeapi.biz/api/balance.php?uid=616c6c706169736162617a616172&pin=551ec2152a310&route=recharge&version=4";
    $myurl = "https://myrc.in/v3/recharge/balance?username=" . RECHARGE_USERNAME . "&token=" . RECHARGE_PASSWORD . "&format=json";
    curl_setopt($ch, CURLOPT_URL, $myurl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    $file_contents = json_decode($file_contents);
    $balance = isset($file_contents->balance) ? $file_contents->balance : 0;
    return $balance;
}

/* check api balance */
function checkAPIBalance($plainAmount)
{
    global $link;
    $api_balance = get_api_balance();
    $user_balance = my_fetch_object(my_query("SELECT SUM(balance) as balance FROM `user`"))->balance;
    $total_balance = $plainAmount + $user_balance;
    if ($api_balance >= $total_balance) {
        return 1;
    } else {
        return 0;
    }
}

/* check api balance for recharge */
function checkAPIBalanceRecharge($plainAmount)
{
    $api_balance = get_api_balance();
    if ($api_balance >= $plainAmount) {
        return 1;
    } else {
        return 0;
    }
}

/* recharge */
function recharge($type, $operator, $number, $amount, $yourwebsiteorderid = '', $p1 = '', $p2 = '')
{
    $data = array();
    $user_id = RECHARGE_USERNAME;
    $password = RECHARGE_PASSWORD;
    $mode = 1;

    $ch = curl_init();
    $timeout = 60; // set to zero for no timeout

    //$myurl = "http://rechargeapi.biz/api/recharge.php?uid=616c6c706169736162617a616172&pin=551ec2152a310&number=$number&operator=$operator&circle=$p1&amount=$amount&usertx=$yourwebsiteorderid&format=csv&version=4";
    $myurl = "https://myrc.in/v3/recharge/api?username=" . RECHARGE_USERNAME . "&token=" . RECHARGE_PASSWORD . "&opcode=$operator&number=$number&amount=$amount&orderid=$yourwebsiteorderid&format=json";
    curl_setopt($ch, CURLOPT_URL, $myurl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    //print_r($file_contents);die;

    $maindata = json_decode($file_contents);

    $transactionid = isset($maindata->txid) ? $maindata->txid : 0;
    $status = isset($maindata->status) ? $maindata->status : 'Pending'; // Success / Failure

    $data[] = $transactionid;
    $data[] = strtoupper($status);

    return $data;
}

function recharge_change_status($yourwebsiteorderid)
{
    $data = array();
    $user_id = RECHARGE_USERNAME;

    $ch = curl_init();
    $timeout = 60; // set to zero for no timeout
    $myurl = "http://recharge.com/api/status.php?user_id=$user_id&orderid=$yourwebsiteorderid";
    curl_setopt($ch, CURLOPT_URL, $myurl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    curl_close($ch);

    //splitting each data as single
    $maindata = explode(",", $file_contents);

    $transactionid = $maindata[0];
    $status = $maindata[1];

    $data[] = $transactionid;
    $data[] = $status;

    return $data;
}

/* mobile, data card, dth, postpaid recharge */
function recharge_jolo($operator, $servicenumber, $amount, $websiteorderid)
{
    global $link;
    $data = array();
    $myjoloappkey = "521233317892440"; //your jolo appkey
    $mode = "1"; //set 1 for live recharge, set 0 for demo recharge

    $ch = curl_init();
    $timeout = 300; // set to zero for no timeout
    $myurl = "http://joloapi.com/api/recharge.php?mode=$mode&userid=dilipsingh&key=$myjoloappkey&operator=$operator&service=$servicenumber&amount=$amount&orderid=$websiteorderid";
    curl_setopt($ch, CURLOPT_URL, $myurl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    curl_close($ch);

    //splitting each data as single
    $maindata = explode(",", $file_contents);

    $transactionid = $maindata[0];
    $status = $maindata[1];

    $data[] = $transactionid;
    $data[] = $status;

    return $data;
}

function rechargebalance($mykey = '521233317892440')
{
    global $link;
    $ch = curl_init();
    $timeout = 60; // set to zero for no timeout
    $myurl = "http://joloapi.com/api/balance.php?userid=dilipsingh&key=$mykey";
    curl_setopt($ch, CURLOPT_URL, $myurl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    $maindata = explode(",", $file_contents);
    return $maindata[0];
}

function recharge_api_2($type, $operator, $number, $amount, $circle, $yourwebsiteorderid = '')
{
    $params = array(
        'ResellerID' => 'K601845',
        'Password' => '670451208',
        'ServiceType' => $type,
        'MobileNo' => $number,
        'OperatorCode' => $operator,
        'CircleID' => $circle,
        'Amount' => $amount,
        'ResellerOrderID' => $yourwebsiteorderid,
        'RequestTimestamp' => time(),
        'IPAddress' => USER_IP
    );

    $postData = '';
    foreach ($params as $k => $v) {
        $postData .= $k . '=' . $v . '&';
    }
    rtrim($postData, '&');
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "http://www.payumagic.biz/API/PrepaidRecharge.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, count($postData));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $output = curl_exec($ch);
    curl_close($ch);

    $data = explode(',', $output);

    return $data;
}

function get_help_status()
{
    return $reward_arr = array(0 => 'Not Confirm', 1 => 'Confirm', 2 => 'Lock');
}

function check_upgrade($uidu, $l)
{
    global $link;
    $stage = my_fetch_object(my_query("SELECT stage FROM user WHERE uid='" . $uidu . "'"))->stage;
    if ($stage >= $l || $uidu == 100) {
        return $uidu;
    } else {
        $top_arr = get_top_level_uids($uidu, $l);
        if ((isset($top_arr[$l - 1]))) {
            return check_upgrade($top_arr[$l - 1], $l);
        } else {
            return 100;
        }
    }
}

function get_floor($amt)
{
    return floor($amt);
}

function get_round($amt, $p = 2)
{
    return round($amt, $p);
}

/* get top level uids */
function get_top_level_uids_by_pool_re($recid, $level = 0, $pool = 0, $arr = array())
{
    global $link;
    $pfield = 'placement_id';
    $result = my_query("SELECT $pfield FROM userre WHERE recid = '$recid'");
    if (count($arr) == $level && $level != 0) {
        return $arr;
    } elseif ($recid <= 170) {
        return $arr;
    }
    if (my_num_rows($result) > 0) {
        $data = my_fetch_array($result);
        $arr[count($arr)] = $data[0];
        return get_top_level_uids_by_pool_re($data[0], $level, $pool, $arr);
    } else {
        return $arr;
    }
}

/* get placement id */
function get_placement_id_re($placement_uid, $unum = 3, $pool = 1)
{
    global $link;
    $level = array();
    //$placement_id = 1;
    $placement_id = $pool;

    $placement_id = my_fetch_object(my_query("SELECT recid FROM userre WHERE uid = '$placement_uid' AND pool = '" . $pool . "' ORDER BY datetime DESC LIMIT 1"))->recid;

    $result = my_query("SELECT recid FROM userre WHERE placement_id = '$placement_id' ORDER BY datetime ASC");
    $num = my_num_rows($result);
    if ($num >= $unum) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row->recid;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT recid FROM userre WHERE placement_id = '$value' ORDER BY datetime ASC");
                if (my_num_rows($result) >= $unum) {
                    while ($row = my_fetch_object($result)) {
                        $level[$i + 1][] = $row->recid;
                    }
                } else {
                    $placement_id = $value;
                    return my_fetch_object(my_query("SELECT * FROM userre WHERE recid = '$placement_id'"));
                    break;
                }
            }
            if (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                break;
            }
        }
    }

    return my_fetch_object(my_query("SELECT * FROM userre WHERE recid = '$placement_id'"));
}

/* get child levels */
function get_child_levels_by_pool_re($recid, $pool = 1, $with = '', $l = 3)
{
    global $link;
    //with recid
    if ($with == 'yes') {
        $level = array(array(my_fetch_object(my_query("SELECT * FROM userre WHERE recid = '$recid'"))));
    } else {
        $level = array();
    }
    $result = my_query("SELECT * FROM userre WHERE placement_id = '$recid'");
    if (my_num_rows($result) != 0) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT * FROM userre WHERE placement_id = '" . $value->recid . "'");
                while ($row = my_fetch_object($result)) {
                    $level[$i + 1][] = $row;
                }
            }
            if ($i == $l) {
                break;
            } elseif (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                break;
            }
        }
    }
    return $level;
}

/* get child levels */
function get_child_levels_by_pool_re2($uid, $pool = 1, $with = '', $l = 3)
{
    global $link;
    //with recid
    if ($with == 'yes') {
        $level = array(array(my_fetch_object(my_query("SELECT * FROM userre WHERE uid = '$uid' AND pool = '" . $pool . "'"))));
    } else {
        $level = array();
    }
    $result = my_query("SELECT * FROM userre WHERE placement_uid = '$uid' AND pool = '" . $pool . "'");
    if (my_num_rows($result) != 0) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT * FROM userre WHERE placement_id = '" . $value->recid . "'");
                while ($row = my_fetch_object($result)) {
                    $level[$i + 1][] = $row;
                }
            }
            if ($i == $l) {
                break;
            } elseif (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                break;
            }
        }
    }
    return $level;
}

/* get child levels */
function get_child_levels_by_pool_re_byuid($uid, $pool = 1, $level = 1, $with = '')
{
    global $link;

    $recid = my_fetch_object(my_query("SELECT recid FROM userre WHERE uid = '$uid' AND pool = '" . $pool . "' ORDER BY datetime ASC LIMIT " . ($level - 1) . ", 1"))->recid;
    $recid = !$recid ? -1 : $recid;

    //with recid
    if ($with == 'yes') {
        $level = array(array(my_fetch_object(my_query("SELECT * FROM userre WHERE recid = '$recid'"))));
    } else {
        $level = array();
    }
    $result = my_query("SELECT * FROM userre WHERE placement_id = '$recid'");
    if (my_num_rows($result) != 0) {
        $i = 1;
        while ($row = my_fetch_object($result)) {
            $level[$i][] = $row;
        }
        while (TRUE) {
            foreach ($level[$i] as $value) {
                $result = my_query("SELECT * FROM userre WHERE placement_id = '" . $value->recid . "'");
                while ($row = my_fetch_object($result)) {
                    $level[$i + 1][] = $row;
                }
            }
            if ($i == 2) {
                break;
            } elseif (!empty($level[$i + 1])) {
                $i++;
                continue;
            } else {
                break;
            }
        }
    }
    return $level;
}

/**
 * Trading self-investment only (excludes Bot Activation ipid=1).
 */
function get_trading_investment($uid)
{
    return (float) get_sum('investments', 'amount', "uid='" . $uid . "' AND ipid IN (2,3)");
}

/**
 * Business Plan capping multiplier:
 * - Gold ($10,000+): 300%
 * - Silver: 200% default; 300% if self-investment $100–$500 and 6 paid directs
 */
function get_capping_multiplier($uid)
{
    $tinv = get_trading_investment($uid);
    if ($tinv <= 0) {
        return 0;
    }
    if ($tinv >= 10000) {
        return 3; // Gold Package
    }
    // Silver Package special rule
    if ($tinv >= 100 && $tinv <= 500) {
        $directs = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id='" . (int) $uid . "' AND status = 0 AND topup > 0"));
        if ($directs >= 6) {
            return 3;
        }
    }
    return 2; // Silver default 200%
}

function get_total_earnings($uid)
{
    return (float) (
        get_sum('income_growth', 'amount', "uid='" . $uid . "'")
        + get_sum('income_direct', 'amount', "uid='" . $uid . "'")
        + get_sum('income_level', 'amount', "uid='" . $uid . "'")
        + get_sum('income_royalty', 'amount', "uid='" . $uid . "'")
    );
}

/**
 * Earning wallet balance derived from income credits minus withdrawal requests.
 */
function get_member_earning_wallet($uid)
{
    $uid = (int) $uid;
    $income = get_total_earnings($uid);
    $withdrawn = (float) get_sum('withdrawal_block', 'amount', "uid='" . $uid . "'");
    return max(0, round($income - $withdrawn, 2));
}

/**
 * Package wallet balance derived from deposits, transfers, and investments.
 */
function get_member_package_wallet($uid)
{
    $uid = (int) $uid;
    $deposits = (float) get_sum('deposit_block', 'amount', "uid='" . $uid . "' AND status=1");
    $investments = (float) get_sum('investments', 'amount', "uid='" . $uid . "'");
    $transfer_in = (float) get_sum('fund_transfer', 'amount', "uid='" . $uid . "'");
    $transfer_out = (float) get_sum('fund_transfer', 'amount', "from_uid='" . $uid . "'");
    return max(0, round($deposits + $transfer_in - $transfer_out - $investments, 2));
}

/**
 * Sync user.wallet and user.wallet_topup from transaction ledger.
 */
function sync_member_wallet_balances($uid)
{
    $uid = (int) $uid;
    if ($uid <= 0) {
        return false;
    }
    $earning_wallet = get_member_earning_wallet($uid);
    $package_wallet = get_member_package_wallet($uid);
    my_query("UPDATE user SET wallet='" . $earning_wallet . "', wallet_topup='" . $package_wallet . "' WHERE uid='" . $uid . "'");
    return true;
}

/**
 * Level ROI unlock: L1–L2 need 2 directs; each further direct unlocks next level (L3→10).
 */
function get_level_roi_required_directs($level)
{
    $level = (int) $level;
    if ($level <= 0) {
        return 999;
    }
    if ($level <= 2) {
        return 2;
    }
    return $level; // L3 needs 3, L4 needs 4, ... L10 needs 10
}

function check_2x($uid, $amount)
{
    return check_capping($uid, $amount);
}

function check_3x($uid, $amount)
{
    return check_capping($uid, $amount);
}

function check_capping($uid, $amount)
{
    if (in_array($uid, array(100, 31753049, 14461162, 71039087))) {
        return $amount;
    }
    $multiplier = get_capping_multiplier($uid);
    if ($multiplier <= 0) {
        return 0;
    }
    $tinv = get_trading_investment($uid) * $multiplier;
    $tin = get_total_earnings($uid);
    $tin2 = $tin + $amount;
    if ($tinv < $tin2) {
        $amount = $tinv - $tin;
        if ($amount < 0) {
            $amount = 0;
        }
    }
    return $amount;
}
