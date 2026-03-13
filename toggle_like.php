<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$review_id = intval($data['review_id']);
$user_id = $_SESSION['user_id'];

if (isset($conn)) {
    $stmt = $conn->prepare("SELECT id FROM review_likes WHERE review_id = ? AND account_id = ?");
    $stmt->bind_param("ii", $review_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $del = $conn->prepare("DELETE FROM review_likes WHERE review_id = ? AND account_id = ?");
        $del->bind_param("ii", $review_id, $user_id);
        $del->execute();
        $action = 'unliked';
    } else {
        $ins = $conn->prepare("INSERT INTO review_likes (review_id, account_id) VALUES (?, ?)");
        $ins->bind_param("ii", $review_id, $user_id);
        $ins->execute();
        $action = 'liked';
    }

    $cnt_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM review_likes WHERE review_id = ?");
    $cnt_stmt->bind_param("i", $review_id);
    $cnt_stmt->execute();
    $cnt_res = $cnt_stmt->get_result()->fetch_assoc();

    echo json_encode(['status' => 'success', 'action' => $action, 'likes' => $cnt_res['cnt']]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>