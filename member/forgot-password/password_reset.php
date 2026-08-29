<?php
// session_start();
include_once '../../lib/config.php';


if (empty($_SESSION['reset_user_id']) && empty($_SESSION['reset_user_email'])) {
    echo "<script> window.location.href = 'forgot.php';</script>";
    unset($_SESSION['reset_user_id']); // Clear the OTP after successful verification
    unset($_SESSION['reset_user_email']); // Clear the OTP after successful verification

    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  isset($_POST['password_reset'])) {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $uid = $_SESSION['reset_user_id'];
    $email = $_SESSION['reset_user_email'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
        echo "<script>alert('$error'); window.location.href = 'password_reset.php';</script>";
        exit();
    }

    // Hash the new password
    $hashedPassword = encryptPassword($newPassword);

    // Update the user's password in the database
    $updateQuery = "UPDATE user SET password = '" . $hashedPassword . "', verified = 0 WHERE uid = '" . $uid . "'";
    if (my_query($updateQuery)) {
        // Clear reset session variables
        unset($_SESSION['reset_user_id']);
        unset($_SESSION['reset_user_email']);

        $msg = "Password reset successfully! You can now log in with your new password.";
        echo "<script>alert('$msg'); window.location.href = '../index.php';</script>";
    } else {
        $error = "Failed to reset password. Please try again.";
        echo "<script>alert('$error'); window.location.href = 'password_reset.php';</script>";
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


                    <!-- STEP 3: Create New Password -->
                    <div id="step3">
                        <form id="changePasswordForm" action="" method="post" novalidate>
                            <div class="form-group">
                                <label>New Password</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input type="password" class="form-control" name="password" placeholder="Enter new password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password"
                                        required>
                                </div>
                            </div>
                            <!-- Pass along the login_id and email for final submission -->
                            <!-- <input type="text" name="login_id" id="hidden_login_id" value="<?php echo isset($_SESSION['reset_user_id']) ? $_SESSION['reset_user_id'] : ''; ?>">
                            <input type="text" name="email" id="hidden_email" value="<?php echo isset($_SESSION['reset_user_email']) ? $_SESSION['reset_user_email'] : ''; ?>"> -->
                            <button type="submit" id="finalSubmitBtn" name="password_reset" class="btn btn-primary pull-right">Submit</button>
                            <div id="changePwdError" class="alert alert-danger hidden margin-top"></div>
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