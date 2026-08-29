<?php include '../lib/config.php';// include your DB connection and `my_query` 

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['recids']) || !is_array($data['recids'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Sanitize and build a safe IN clause
$recids = array_map('intval', $data['recids']);
$inClause = implode(',', $recids);

$result = my_query("DELETE FROM message WHERE recid IN ($inClause)");

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
redirect("email_inbox.php")
?>