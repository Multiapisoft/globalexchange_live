<?php
session_start();
include '../lib/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id = tres($_POST['login_id']);
    $email = tres($_POST['email']);
    echo $login_id;
    echo $email;
    die;
    
    // Verify user exists before storing in session
    $result = my_query("SELECT recid FROM user WHERE status=0 AND login_id='".$login_id."' AND email='".$email."'");
    if(mysqli_num_rows($result) == 1) {
        // Store in session
        $_SESSION['forgot_login_id'] = $login_id;
        $_SESSION['forgot_email'] = $email;
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid user ID or email']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>