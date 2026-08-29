<?php 
include '../lib/config.php';
include '../lib/PHPMailer/index.php';

// Sanitize input values
$login_id = tres($_POST['login_id']);
$email = tres($_POST['email']);
$password = tres($_POST['password']);
$confirm_password = tres($_POST['confirm_password']);

// Check if password fields are provided
if(empty($password) || empty($confirm_password)){
    setMessage('Password fields are required.','error');
    redirect('./forgot.php');
    exit;
}

// Check if passwords match
if($password !== $confirm_password){
    setMessage('Passwords do not match.','error');
    redirect('./forgot.php');
    exit;
}


// Validate user ID and site status
if(checkLoginId($login_id)==1 && SITE_WORKING_STATUS==0){ 
    $result = my_query("SELECT recid, login_id, name, email, mobile 
                        FROM user 
                        WHERE status=0 
                        AND login_id='".$login_id."' 
                        AND email='".$email."'");
    if(mysqli_num_rows($result)==1){
        $row = mysqli_fetch_object($result);
        
        if($row->login_id==$login_id && $row->email==$email){
            // $name = $row->name;
            // $mobile = $row->mobile;
            
            // Encrypt and update the password with the new one provided by the user
            $password_md5 = encryptPassword($password);
            
            
            my_query("UPDATE user SET password='".$password_md5."' WHERE login_id='".$row->login_id."'");
            
            
            setMessage('Your password has been updated.','success');
           
            // Optional: Send a confirmation email about the password change
            // $subject = "Password Changed";
            // $message = "Dear ".$name."<br><br>Your password has been updated successfully.<br><br>
            //             <b>User ID:</b> ".$login_id."<br>
            //             <b>Password:</b> ".$password."<br><br>
            //             Regards,<br>".SITE_NAME."<br><br>www.".SITE_URL;
                        
            // $headers = "MIME-Version: 1.0" . "\r\n";
            // $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
            // $headers .= 'From: <'.SITE_EMAIL_INFO.'>' . "\r\n";
            
            // if(_sendMail(SITE_URL, $email, $subject, $message)) {
            //     setMessage('Your password has been updated. A confirmation email has been sent.','success');
            // } else {
            //     // If email sending fails, you can still consider the password updated.
            //     setMessage('Your password has been updated, but we could not send a confirmation email.','error');
            // }
        } else {
            setMessage('Invalid user id or email.','error');
        }
    } else {
        setMessage('Invalid user id or email.','error');
    }
} else {
    setMessage('Invalid user id or email.','error');
}
setMessage('Password changed Successfully.','success');
redirect('./forgot.php');
?>
