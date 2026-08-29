<?php include_once '../lib/config.php';
admin();

$recid = isset($_POST['recid']) ? (int) $_POST['recid'] : 0;
$amount_from = isset($_POST['amount_from']) ? (float) $_POST['amount_from'] : 0;
$amount_to = isset($_POST['amount_to']) ? (float) $_POST['amount_to'] : 0;
$status = isset($_POST['status']) ? (int) $_POST['status'] : 0;
$percentage = isset($_POST['percentage']) ? (float) $_POST['percentage'] : 0;
$percentage_to = isset($_POST['percentage_to']) ? (float) $_POST['percentage_to'] : 0;
$line1 = isset($_POST['line1']) ? tres($_POST['line1']) : '';
$line2 = isset($_POST['line2']) ? tres($_POST['line2']) : '';

if (isset($_POST['recid']) && $recid > 0 && $amount_from > 0 && $amount_to >= $amount_from) {
    $title = tres($_POST['title']);
    my_query("UPDATE investments_plan SET
        title='" . $title . "',
        amount_from='" . $amount_from . "',
        amount_to='" . $amount_to . "',
        percentage='" . $percentage . "',
        percentage_to='" . $percentage_to . "',
        line1='" . $line1 . "',
        line2='" . $line2 . "',
        action='" . $status . "'
        WHERE recid='" . $recid . "'");
    setMessage('Edited successfully.', 'success');
} else {
    setMessage('Invalid package data.', 'error');
}
redirect('./cms_package_edit.php?recid=' . $recid);
