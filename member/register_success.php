<?php
// session_start();
include '../lib/config.php';
// include '../lib/function_lib.php';
// echo 'Hello, World!';
include '../lib/PHPMailer/index.php';
if (!function_exists('ge_email_welcome')) {
    include '../lib/email_template.php';
}

$uid = $_GET['uid'];
$uniqid = $_GET['uniqid'];

if (!isset($_SESSION['uniqid']) && $_SESSION['uniqid'] != $uniqid) {
    redirect('../index.php');
    die();
}
$pass = $_SESSION['pass'];

$result = my_query("SELECT * FROM user WHERE uid='" . $uid . "'");
$row = mysqli_fetch_object($result);
$refer = mysqli_fetch_object(my_query("SELECT login_id, name, email, mobile FROM user WHERE uid='" . $row->refer_id . "'"));
if (mysqli_num_rows($result) == 0) {
    redirect('../index.php');
    die();
}

$company_domain = SITE_URL;
$company_name = SITE_NAME;
$email_info = SITE_EMAIL_INFO;
if (isset($_SESSION['uniqid']) && $_SESSION['uniqid'] == $uniqid) {
    $to = $row->email;
    $subject = 'Welcome to ' . $company_name;
    $txt = ge_email_welcome($row->name, $row->login_id, $pass);
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    $headers .= 'From: ' . $email_info;

    if (_sendMail(SITE_URL, $to, $subject, $txt)) {
        setMessage('Your account details successfully send to your email.', 'success');
        // echo '<p style="text-align: center;">Your account details successfully send to your email<p>';
    } else {
        // echo '<p style="text-align: center;">Something Went Wrong<p>';
        setMessage('Something Went Wrong', 'error');
    }

    // setMessage(
    //     '🎉 Congratulations! 🎉 You\'re now part of our amazing community.
    //     <div style="margin-top: 10px; font-size: 16px; line-height: 1.5;">
    //         <p>🆔 <strong>Login ID:</strong> ' . $row->login_id . '</p>
    //         <p>🔐 <strong>Password:</strong> ' . $pass . '</p>
    //     </div>
    //     Let\'s start your journey! 🚀',
    //     'success'
    // );

    setMessage(
        '✨ Woohoo! Welcome aboard! ✨  
        🎊 You\'ve successfully registered with us.  
        <div style="margin-top: 10px; font-size: 14px; line-height: 1;">  
        <p>🆔 <strong>Login ID:</strong> 
    <span id="login-id-text">' . $row->login_id . '</span>
        <button onclick="copyLoginId()" style="margin-left: 10px; padding: 2px 8px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">
        📋 Copy
        </button>
        </p>  
        <p>🔐 <strong>Password:</strong> 
            <span id="password-text">' . $pass . '</span>
            <button onclick="copyPassword()" style="margin-left: 10px; padding: 2px 8px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">
            📋 Copy
            </button>
            </p>  
            </div> 
            🌟 Get ready to explore amazing opportunities with us! 🚀
            
            <script>
            function copyLoginId() {
                const loginId = document.getElementById("login-id-text").textContent;
                navigator.clipboard.writeText(loginId).then(() => {
                    alert("Login ID copied to clipboard!");
                    });
                    }
                    
                    function copyPassword() {
                        const password = document.getElementById("password-text").textContent;
                        navigator.clipboard.writeText(password).then(() => {
                            alert("Password copied to clipboard!");
                            });
                            }
                            </script>',
        'success'
    );
}
unset($_SESSION['uniqid']);
unset($_SESSION['pass']);
redirect('./register.php');
?>
