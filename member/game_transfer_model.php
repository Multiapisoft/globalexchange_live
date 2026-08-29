<?php
include_once '../lib/config.php';
user();

$wallet_field_arr = get_wallet_field();
$from_uid = $_SESSION['userid'];
$user = get_user_details($from_uid);
$min = 10;
$max = 50000;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id = isset($_POST['login_id']) ? tres($_POST['login_id']) : '';
    $amount = floatval(tres($_POST['amount']));
    $otp = isset($_POST['otp']) ? tres($_POST['otp']) : '';
    $net_amount = $amount*90 +($amount * 90 * 0.1);
    $wallet_field = 'wallet';
    $wallet = get_wallet($from_uid, $wallet_field);

    if (checkDecimal($amount) == 0) {
        setMessage('Invalid amount.', 'error');
    } elseif ($user->topup <= 0) {
        setMessage('Please topup your account.', 'error');
    } elseif ($wallet < $amount) {
        setMessage('Insufficient fund.', 'error');
    } elseif ($amount < $min) {
        setMessage('Minimum fund transfer is ' . $min . '.', 'error');
    } elseif ($amount > $max) {
        setMessage('Maximum fund transfer is ' . $max . '.', 'error');
    } else {
        // ✅ Deduct from user's wallet locally
        my_query("UPDATE user SET $wallet_field = $wallet_field - '$amount' WHERE uid = '$from_uid'");

        // ✅ Call Node.js API using cURL
        $apiUrl = 'https://skillclash.live/api/third-party/transfer';
        $payload = [
            'phone' => $login_id,
            'amount' => $net_amount
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
            // 'x-api-key: YOUR_SECRET_KEY' // optional if needed
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            setMessage('cURL error: ' . $curl_error, 'error');
        } else {
            $res = json_decode($response, true);

            if ($http_code === 200 && isset($res['status']) && $res['status'] === true) {
                setMessage($res['message'], 'success');
            } else {
                $error_msg = isset($res['message']) ? $res['message'] : 'Transfer failed.';
                setMessage('API Error: ' . $error_msg, 'error');
            }
        }
    }
}

redirect('./game_fund_transfer.php');
exit;
?>
