<?php include_once '../lib/config.php';
admin();
$mid = isset($_GET['mid']) ? (int) $_GET['mid'] : 0;
if (isset($_GET['delete'])) {
    $recid = tres($_GET['delete']);
    my_query("DELETE FROM cms WHERE recid='$recid'");
    setMessage('Delete successfully.', 'success');
} elseif (isset($_POST)) {
    $mid = isset($_POST['mid']) ? (int) $_POST['mid'] : 0;
    $cid = isset($_POST['cid']) ? (int) $_POST['cid'] : 0;
    $title = tres($_POST['title']);
    $description = tres($_POST['description']);

    my_query("INSERT INTO cms (mid, cid, title, description, datetime) VALUES ('" . $mid . "', '" . $cid . "', '" . $title . "', '" . $description . "', '" . date('c') . "')");
    setMessage('Added successfully.', 'success');
}
redirect('./cms.php?mid=' . $mid);
?>