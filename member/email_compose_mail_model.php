<?php include_once '../lib/config.php';
// include_once '../lib/imageresize.php';
user();
$uid = $_SESSION['userid'];

if (isset($_POST)) {
    $subject = tres($_POST['subject']);
    $message = tres($_POST['message']);
    my_query("INSERT INTO `message` (`sender`, `receiver`, `subject`, `message`, `datetime`) VALUES('" . $uid . "', 0, '" . $subject . "', '" . $message . "', '" . date('c') . "')");
    $last_insert_id = my_insert_id();

    if (!empty($_FILES['file']['name'])) {
        if (isset($_FILES['file']['name']) && array_search($_FILES['file']['type'], array("image/gif", "image/jpeg", "image/png", "image/jpg")) !== FALSE) {
            $resize = new resizeImage();
            // upload image in three dimesions
            $largePath = "../uploads/";
            $largeImage = $resize->do_resize(500, 400, $_FILES['file'], $largePath, 0, "large");

            my_query("UPDATE message SET filename = '" . $largeImage . "' WHERE recid='" . $last_insert_id . "'");

            setMessage('Send successfully.', 'success');
        } else {
            // uploaded file is not a image
            setMessage('Image not added. Something went wrong. Please try again.', 'error');
        }
    } else {
        setMessage('Send successfully.', 'success');
    }
}
redirect('./email_compose_mail.php');
?>