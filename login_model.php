<?php include 'lib/config.php';
$login_id = tres($_POST['login_id']);
//if(checkLoginId($login_id)==1 && SITE_WORKING_STATUS==0){
if(SITE_WORKING_STATUS==0){
    $result = my_query( "SELECT uid, type, login_id, password FROM user WHERE status=0 AND (login_id LIKE '".$login_id."' OR uid='".$login_id."')");
    if(my_num_rows($result)==1){
        $row = my_fetch_object($result);
        if(strtolower($row->login_id)==strtolower($login_id) || $row->uid==$login_id){
            $_SESSION['loginid'] = $row->login_id;
            $_SESSION['userid'] = $row->uid;
            $_SESSION['type'] = $row->type;

            /* login detail */
            my_query( "INSERT INTO user_login_detail (`uid`, `datetime`, `ip`) VALUES ('".$row->uid."', '".date('c')."', '".USER_IP."')");
            
            redirect('member/dashboard.php');
            die();
        }
    }
}
setMessage('You need to register first', 'error');
redirect('index.php');
?>