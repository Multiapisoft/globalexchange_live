<?php include '../lib/config.php';

//remove all the variables in the session
session_unset();

// destroy the session
session_destroy();

setMessage('Logout successfully','success');
redirect('./index.php');
?>