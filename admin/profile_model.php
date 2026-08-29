<?php include_once '../lib/config.php';
admin();
$aid = $_SESSION['adminid'];

if (isset($_POST)) {
    $name = tres($_POST['name']);
    $email = tres($_POST['email']);
    $mobile = tres($_POST['mobile']);

    $old_password = tres($_POST['old_password']);
    $password = tres($_POST['password']);
    $confirm_password = tres($_POST['confirm_password']);

    if ($password != '' && $old_password != '') {
        if (checkPassword($password) == 0) {
            setMessage('Invalid password.', 'error');
            redirect('./profile.php');
            die();
        } elseif (checkPassword($old_password) == 0) {
            setMessage('Invalid old password.', 'error');
            redirect('./profile.php');
            die();
        } elseif ($password != $confirm_password) {
            setMessage('Confirm password does not match password.', 'error');
            redirect('./profile.php');
            die();
        }
        $old_password_md5 = encryptPassword($old_password);
        $check = mysqli_num_rows(my_query("SELECT recid FROM admin WHERE recid='" . $aid . "' AND password='" . $old_password_md5 . "'"));
        if ($check != 1) {
            setMessage('Your old password incorrect.', 'error');
            redirect('./profile.php');
            die();
        }
        $password_md5 = encryptPassword($password);
    }

    if (checkMobile($mobile) == 0) {
        setMessage('Invalid mobile.', 'error');
    } elseif (checkEmail($email) == 0) {
        setMessage('Invalid email.', 'error');
    } else {
        $sql = "UPDATE admin SET name='" . $name . "', email='" . $email . "', mobile='" . $mobile . "'";
        if ($password != '' && $old_password != '') {
            $sql .= ", password='" . $password_md5 . "'";
        }
        $sql .= " WHERE recid='" . $aid . "'";

        my_query($sql);
        setMessage('Profile edit successfully.', 'success');
    }
}
redirect('./profile.php');
?>