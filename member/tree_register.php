<?php include_once '../lib/config.php';
user();
$uid = $_SESSION['userid'];

if(isset($_GET['placement_id']) && isset($_GET['position'])){
    $position = tres($_GET['position']);
    $result = mysqli_query($link, "SELECT uid FROM `user` WHERE uid='".tres($_GET['placement_id'])."'");
    if(mysqli_num_rows($result)==1){
        $row = mysqli_fetch_object($result);
        $result2 = mysqli_query($link, "SELECT uid, position FROM `user` WHERE placement_id='".tres($_GET['placement_id'])."' AND position='".$position."'");
        if(mysqli_num_rows($result2)==0){
            $placement_id = $row->uid;
            $_SESSION['placement_id'] = $placement_id;
            $_SESSION['position'] = $position;
            redirect("../register.php");
            die();
        }
    }
}
redirect("./tree_view.php");
?>