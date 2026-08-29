<?php
// Clean JSON only — no warnings in response
@ini_set('display_errors', '0');
error_reporting(0);

include '../lib/config.php';
if (!function_exists('_sendMail')) {
    include '../lib/PHPMailer/index.php';
}
if (!function_exists('ge_email_otp')) {
    include '../lib/email_template.php';
}

header('Content-Type: application/json; charset=utf-8');

while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();

$type = isset($_GET['type']) ? tres($_GET['type']) : 'register';
$email = isset($_GET['email']) ? strtolower(tres($_GET['email'])) : '';
$status = 0;
$msg = 'Failed';

$otp = (string) random_int(100000, 999999);

try {
    if ($type == 'register' && $email) {
        $check = mysqli_num_rows(my_query("SELECT uid FROM user WHERE email LIKE '" . $email . "'"));
        $subject = 'Registration Verification Email OTP';
        $message = ge_email_otp($otp, array(
            'subject' => $subject,
            'eyebrow' => 'Registration',
            'greeting' => 'Verify your email',
            'intro' => 'Enter this one-time password to complete your ' . htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') . ' registration.',
            'cta_label' => 'Complete Registration',
        ));

        if (!$check && _sendMail(SITE_URL, $email, $subject, $message)) {
            $status = 1;
            $msg = 'OTP sent to your email.';
        } elseif ($check) {
            $msg = 'Email already registered.';
        } else {
            $msg = 'Unable to send email. Please try again.';
        }
    } elseif ($type == 'transfer' || $type == 'withdrawal') {
        if (empty($_SESSION['userid'])) {
            $msg = 'Please login again.';
        } else {
            $uid = (int) $_SESSION['userid'];
            $user = get_user_details($uid);
            $email = $user ? strtolower(trim((string) $user->email)) : '';
            $label = ($type == 'withdrawal') ? 'withdrawal' : 'transfer';
            $subject = 'Fund ' . ucfirst($label) . ' OTP';
            $message = ge_email_otp($otp, array(
                'subject' => $subject,
                'eyebrow' => 'Fund ' . ucfirst($label),
                'greeting' => 'Confirm your ' . $label,
                'intro' => 'Enter this one-time password to authorize your fund ' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '. If you did not start this request, ignore this email.',
                'cta_label' => 'Open Dashboard',
            ));

            if ($user && $email) {
                store_email_otp($otp, $type, $email, $uid);
            }

            if ($user && $email && _sendMail(SITE_URL, $email, $subject, $message)) {
                $status = 1;
                $msg = 'OTP sent to your email.';
            } elseif (!$email) {
                $msg = 'Email not found on your profile.';
            } else {
                $msg = 'Unable to send email. Please try again.';
            }
        }
    } elseif ($type == 'register') {
        $msg = 'Please enter mail.';
    } else {
        $msg = 'Invalid OTP request.';
    }
} catch (Throwable $e) {
    $status = 0;
    $msg = 'Unable to send email. Please try again.';
}

if ($status == 1) {
    $otpUid = 0;
    if (($type == 'transfer' || $type == 'withdrawal') && !empty($_SESSION['userid'])) {
        $otpUid = (int) $_SESSION['userid'];
    }
    store_email_otp($otp, $type, $email, $otpUid);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
}

ob_end_clean();
echo json_encode(array('_status' => (int) $status, 'msg' => $msg));
exit;
