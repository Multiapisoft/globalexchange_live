<?php
// session_start();
include_once '../../lib/config.php';
error_reporting(E_ALL);

// Display errors on screen (useful in development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


if (empty($_SESSION['otp']) && empty($_SESSION['otp_email'])) {
    echo "<script> window.location.href = 'forgot.php';</script>";
    unset($_SESSION['otp']); // Clear the OTP after successful verification
    unset($_SESSION['otp_email']); // Clear the OTP after successful verification

    exit();
}
// echo $storedOtp = $_SESSION['otp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  isset($_POST['verify_otp'])) {
    $inputOtp = $_POST['otp'];
    $storedOtp = $_SESSION['otp'];
    $email = $_SESSION['otp_email'];

    if ($inputOtp == $storedOtp) {
        // OTP is valid
        unset($_SESSION['otp']); // Clear the OTP after successful verification

        // Get user ID from email
        $result = my_query("SELECT uid, email FROM user WHERE email = '" . $email . "' LIMIT 1");

        if ($result && $row = mysqli_fetch_assoc($result)) {
            $uid = $row['uid'];
            $email = $row['email'];

            // Update user's verification status
            $updateQuery = "UPDATE user SET verified = 1 WHERE uid = '" . $uid . "'";
            my_query($updateQuery);

            $_SESSION['reset_user_id'] = $uid;
            $_SESSION['reset_user_email'] = $email;

            echo "<script>alert('OTP verified successfully!'); window.location.href = 'password_reset.php';</script>";
        } else {
            echo "<script>alert('User not found with this email.'); window.location.href = 'forgot.php';</script>";
        }
    } else {
        // Invalid OTP
        echo "<script>alert('Invalid OTP. Please try again.'); window.location.href = 'otp_verify.php';</script>";
    }
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Forgot Password</title>
    <link rel="shortcut icon" href="../extra/img/favicon.png" type="image/x-icon">
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/dist/css/component_ui.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/dist/css/skins/component_ui_black.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/dist/css/custom.css" rel="stylesheet" type="text/css" />
    <style>
        .form-control {
            color: #fff;
        }

        .form-control::placeholder {
            color: #fff;
            opacity: 1;
        }

        .hidden {
            display: none;
        }

        .margin-top {
            margin-top: 15px;
        }
    </style>
  <link href="../css/ge-theme.css" rel="stylesheet" type="text/css" />
  <style>body{background:#050505!important;color:#f5f5f5!important}.panel,.panel-bd{background:#121212!important;border:1px solid rgba(212,175,55,.22)!important;border-radius:16px!important}.btn-primary{background:linear-gradient(135deg,#f5d76e,#d4af37,#c9970a)!important;border:none!important;color:#111!important;border-radius:12px!important}.form-control{background:#0a0a0a!important;border-color:rgba(212,175,55,.22)!important;color:#f5f5f5!important}</style>
</head>

<body>
    <div class="login-wrapper">
        <div class="container-center">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="view-header">
                        <div class="header-icon">
                            <i class="pe-7s-refresh-2"></i>
                        </div>
                        <div class="header-title">
                            <h3>Forgot Password</h3>
                            <small>Please fill the form to recover your password</small>
                        </div>
                    </div>
                </div>
                <div class="panel-body">


                    <!-- STEP 2: OTP Verification -->
                    <div id="step2">
                        <form id="otpForm" method="POST" action="">
                            <div class="form-group">
                                <label>OTP</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP" maxlength="6"
                                        required>
                                </div>
                            </div>
                            <button type="submit" id="verifyOtpBtn" name="verify_otp" class="btn btn-primary pull-right">Verify OTP</button>
                            <div id="otpError" class="alert alert-danger hidden margin-top"></div>
                        </form>
                    </div>


                </div>
            </div>
            <div id="bottom_text">
                <a href="../index.php">Login</a>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="../../assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
    <script src="../../assets/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>