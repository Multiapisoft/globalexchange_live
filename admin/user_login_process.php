<?php include '../lib/config.php';
admin();
$uid = tres($_GET['uid']);

$result = my_query( "SELECT uid, login_id, type FROM user WHERE uid = '$uid'");
if(mysqli_num_rows($result)){
    $row = mysqli_fetch_object($result);
    $_SESSION['loginid'] = $row->login_id;
    $_SESSION['userid'] = $row->uid;
    $_SESSION['type'] = $row->type;
    $_SESSION['transaction'] = $uid;
    redirect('../member/dashboard.php');
}
else{
    setMessage('Login failed','error');
    redirect('./users.php');
}
?>