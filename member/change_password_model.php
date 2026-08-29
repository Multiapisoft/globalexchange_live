<?php include_once '../lib/config.php';
user();
$uid = $_SESSION['userid'];

if(isset($_POST)){    
    $old_password = tres($_POST['old_password']);
    $password = tres($_POST['password']);
    $confirm_password = tres($_POST['confirm_password']);
    
    $password_md5 = encryptPassword($password);
    $old_password_md5 = encryptPassword($old_password);
    $check = my_num_rows(my_query( "SELECT recid FROM user WHERE uid='".$uid."' AND password='".$old_password_md5."'"));
    
    if(checkPassword($password)==0){
        setMessage('Invalid password.','error');
    }
    elseif(checkPassword($old_password)==0){
        setMessage('Invalid old password.','error');
    }
    elseif($password!=$confirm_password){
        setMessage('Confirm password does not match password.','error');
    }
    elseif($check!=1){
        setMessage('Your old password incorrect.', 'error');
    }
    elseif($check==1){
        my_query( "UPDATE user SET password='".$password_md5."' WHERE uid='".$uid."'");
        setMessage('Password change successfully.', 'success');
    }
}
redirect('./change_password.php');
?>