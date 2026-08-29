<?php include_once '../lib/config.php';
admin();

if(isset($_POST)){
    $login_id = tres($_POST['to']);
    $subject = tres($_POST['subject']);
    $message = tres($_POST['message']);
    
    $uid = registeredUserId($login_id);
    if($uid==0){
        setMessage('Invalid user id.', 'error');
    }
    else{
        my_query( "INSERT INTO `message` (`sender`, `receiver`, `subject`, `message`, `datetime`) VALUES(0, '".$uid."', '".$subject."', '".$message."', '".date('c')."')");
        setMessage('Send successfully.', 'success');
    }
}
redirect('./email_compose_mail.php');
?>