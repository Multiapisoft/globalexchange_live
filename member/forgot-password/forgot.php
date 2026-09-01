<?php
include_once '../../lib/config.php';
require_once '../../lib/PHPMailer/index.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  isset($_POST['send_otp'])) {

    $login_id = isset($_POST['login_id']) ? tres($_POST['login_id']) : '';
    $email = isset($_POST['email']) ? trim(tres($_POST['email'])) : '';
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Please enter a valid email address.';
        echo "<script>alert('$msg'); window.location.href = 'forgot.php';</script>";
        exit();
    }
    
    $check_user = my_query("SELECT * FROM user WHERE login_id ='$login_id' AND email = '$email'");
    if (my_num_rows($check_user) > 0) {
        $otp = rand(100000, 999999);  // Generate 6-digit OTP
        // Save OTP in session
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;
        $update = my_query("UPDATE user SET otp = '$otp' , otp_time = NOW() WHERE login_id ='$login_id' AND email = '$email'");
        // Prepare email content
        
        $subject = 'Your Forgot Password OTP - ' . SITE_NAME;
        $message = ge_email_otp($otp, array(
            'subject' => $subject,
            'eyebrow' => 'Password reset',
            'greeting' => 'Reset your password',
            'intro' => 'Use this one-time password to verify your identity and set a new password. It expires in 10 minutes.',
            'cta_label' => 'Continue Reset',
        ));
        // Send OTP via email using _sendMail function
        if (_sendMail('', $email, $subject, $message)) {
            $msg = 'OTP sent successfully to your email!';
            echo "<script>alert('$msg'); window.location.href = 'otp_verify.php';</script>";
            exit();
        } else {
            $msg = 'Failed to send OTP. Please try again.';
            echo "<script>alert('$msg'); window.location.href = 'forgot.php';</script>";
            exit();
        }
    } else {
        $msg = 'User ID and Email do not match our records.';
        echo "<script>alert('$msg'); window.location.href = 'forgot.php';</script>";
        exit();
    }
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
  <link href="../css/ge-theme.css" rel="stylesheet" type="text/css" />
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
                    <!-- STEP 1: Enter User ID and Email -->
                    <div id="step1">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label>User ID</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input type="text" class="form-control" id="login_id" name="login_id" placeholder="Enter your User ID"
                                        maxlength="20" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your Email"
                                        maxlength="100" required>
                                </div>
                            </div>
                            <button type="submit" id="sendOtpBtn" name="send_otp" class="btn btn-primary pull-right">Send OTP</button>
                            <div id="userDetailsError" class="alert alert-danger hidden margin-top"></div>
                        </form>
                    </div>
                    <div id="bottom_text">
                        <a href="../index.php">Login</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="../../assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
    <script src="../../assets/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>