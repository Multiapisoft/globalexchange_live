<?php
include_once '../lib/config.php';
session_start();

// Function to generate random string
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Function to generate random email
function generateRandomEmail()
{
    $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'example.com'];
    return generateRandomString(8) . '@' . $domains[array_rand($domains)];
}

// Function to generate random phone number
function generateRandomPhone()
{
    return '9' . rand(100000000, 999999999);
}

// Function to generate random date of birth
function generateRandomDOB()
{
    $start = strtotime('1970-01-01');
    $end = strtotime('2005-12-31');
    $randomTimestamp = rand($start, $end);
    return date('Y-m-d', $randomTimestamp);
}

// Function to generate random txid
function generateRandomTxid($length = 64)
{
    $characters = '0123456789abcdef';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Function to generate n users
function generateUsers($numUsers, $refer_data = null)
{
    global $link;

    $genders = ['Male', 'Female'];
    $states = ['MH', 'DL', 'GJ', 'KA', 'TN'];
    $cities = ['Mumbai', 'Delhi', 'Ahmedabad', 'Bangalore', 'Chennai'];

    $refer_id = $refer_data ?: 100; // Use provided refer_data or default to 100

    for ($i = 0; $i < $numUsers; $i++) {
        $uid = createId();
        $login_id =  $uid;
        $password = '123456'; // Default password
        $password_md5 = encryptPassword($password);
        $name = generateRandomString(6) . ' ' . generateRandomString(6);
        $email = generateRandomEmail();
        $mobile = generateRandomPhone();
        $dob = generateRandomDOB();
        $gender = $genders[array_rand($genders)];
        $address = generateRandomString(15);
        $city = $cities[array_rand($cities)];
        $state = $states[array_rand($states)];
        $country = 'IN';
        $position = ''; // Position blank as requested
        $wallet_topup = 100000; // Default wallet_topup value
        $amount = $wallet_topup; // Same amount as wallet_topup
        $fee = 0; // Assuming no fee
        $net_amount = $amount - $fee;
        $amount_coin = $amount; // Assuming same as amount
        $txid = generateRandomTxid();
        $type = 'USDT';
        $status = 1;
        $data = '';

        // Get placement_id and determine position
        $placement_id = $refer_id;
        $child_ids = get_single_dimensional(get_child_levels($refer_id, 'yes'));

        // Check existing positions under the referrer
        $position_result = my_query("SELECT position, COUNT(*) as count FROM user WHERE placement_id='$refer_id' AND position IN ('L', 'R') GROUP BY position");
        $positions = [];
        while ($row = my_fetch_array($position_result)) {
            $positions[$row['position']] = $row['count'];
        }

        // Determine new position - 'L' if left has fewer or equal users, otherwise 'R'
        $position = (($positions['L'] ?? 0) <= ($positions['R'] ?? 0)) ? 'L' : 'R';

        $check_position = my_query("SELECT uid, position FROM user WHERE placement_id='$placement_id'");

        // Validation checks
        if ($refer_id == 0) {
            setMessage('Invalid sponsor id.', 'error');
            continue;
        }
        if (checkLoginId($login_id) == 0 || checkLoginIdAvailability($login_id) == 0) {
            setMessage('Login id issue.', 'error');
            continue;
        }
        if (checkMobile($mobile) == 0 || checkMobileAvailability($mobile) == 0) {
            setMessage('Mobile issue.', 'error');
            continue;
        }
        if (checkEmail($email) == 0 || checkEmailAvailability($email) == 0) {
            setMessage('Email issue.', 'error');
            continue;
        }
        if ($placement_id && !in_array($placement_id, $child_ids)) {
            setMessage('Invalid placement id.', 'error');
            continue;
        }

        // Insert user into database with position
        $sql = "INSERT INTO `user` (`uid`, `login_id`, `refer_id`, `placement_id`, `position`, `password`, `name`, `dob`, `gender`, `address`, `city`, `state`, `country`, `mobile`, `email`, `phone`, `datetime`, `transaction_password`, `wallet_topup`) 
                VALUES ('$uid', '$login_id', '$refer_id', '$placement_id', '$position', '$password_md5', '$name', '$dob', '$gender', '$address', '$city', '$state', '$country', '$mobile', '$email', '$mobile', '" . date('c') . "', '$password_md5', '$wallet_topup')";

        my_query($sql);

        // Update BNB address if provided
        $bnb_address = generateRandomString(20);
        $sql = "UPDATE user SET name = '$name', bnb_address = '$bnb_address', wallet_topup = '$wallet_topup' WHERE uid='$uid'";
        my_query($sql);

        // Insert into deposit_block with same amount as wallet_topup
        $deposit_sql = "INSERT INTO deposit_block (uid, datetime, status, amount, fee, net_amount, amount_coin, txid, data, type) VALUES ('$uid', '" . date('c') . "', '$status', '$amount', '$fee', '$net_amount', '$amount_coin', '$txid', '$data', '$type')";
        my_query($deposit_sql);

        // Send SMS
        $msg = "Dear $name Your id no. is $login_id and password is $password thanks, " . SITE_URL . ".";
        send_sms($mobile, $msg);

        // Log success
        setMessage("User $login_id created successfully.", 'success');
    }

    return json_encode(['success' => true, 'message' => "$numUsers users generated successfully."]);
}

// Handle POST request to generate users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['num_users'])) {
    $numUsers = (int)$_POST['num_users'];
    $refer_data = $_POST['refer_data'] ?? ''; // Get refer_data from POST
    if ($numUsers > 0) {
        echo generateUsers($numUsers, $refer_data);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid number of users.']);
    }
    exit();
}

// Simple form to input number of users
?>
<!DOCTYPE html>
<html>

<head>
    <title>Generate Users</title>
</head>

<body>
    <form method="POST" action="">
        <label>Number of Users to Generate:</label>
        <input type="number" name="num_users" min="1" placeholder="Enter number of users" required>
        <input type="text" name="refer_data" placeholder="Referral number" required>
        <button type="submit">Generate Users</button>
    </form>
</body>

</html>
<?php
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