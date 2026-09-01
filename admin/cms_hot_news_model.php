<?php include_once '../lib/config.php';
include('../lib/imageresize.php');
admin();
if (isset($_POST)) {
    $hot_news = my_real_escape_string($_POST['hot_news']);
    $status = (int) $_POST['status'];
    $now = date('Y-m-d H:i:s');

    if (!empty($_FILES['image']) && !empty($_POST) && $_FILES['image']['name'] != '') {
        if (isset($_FILES['image']['name']) && array_search($_FILES['image']['type'], array('image/gif', 'image/jpeg', 'image/png', 'image/jpg')) !== false) {
            $fileData = pathinfo(basename($_FILES['image']['name']));
            $fileName = uniqid() . '.' . $fileData['extension'];
            $target_path = ('../uploads/' . $fileName);

            while (file_exists($target_path)) {
                $fileName = uniqid() . '.' . $fileData['extension'];
                $target_path = ('../uploads/' . $fileName);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                my_query("UPDATE hot_news SET hot_news='" . $hot_news . "', image='" . my_real_escape_string($fileName) . "', datetime='" . $now . "', status='" . $status . "' WHERE recid=1");
                setMessage('Hot news edit successfully.', 'success');
            } else {
                setMessage('Image upload failed.', 'error');
            }
        } else {
            setMessage('Uploaded file is not a image.', 'error');
        }
    } else {
        my_query("UPDATE hot_news SET hot_news='" . $hot_news . "', datetime='" . $now . "', status='" . $status . "' WHERE recid=1");
        setMessage('Hot news edit successfully.', 'success');
    }
}
redirect('./cms_hot_news.php');
?>