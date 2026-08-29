<?php include 'lib/config.php';
include 'lib/PHPMailer3/index.php';

$email = 'mlmdilipsingh@gmail.com';
$name = 'hi';
$subject = "Forgot Password";
$message = "Dear ".$name."<br><br>
    <b>Team</b> <br><br>".SITE_NAME."<br><br>www.".SITE_URL."
";
/* Always set content-type when sending HTML email */
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
/* More headers */
$headers .= 'From: <'.SITE_EMAIL_INFO.'>' . "\r\n"; 

_sendMail(SITE_URL, $email, $subject, $message);
?>