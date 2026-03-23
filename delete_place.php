<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

include 'config.php';

// Verify admin
$stmt = $conn->prepare("SELECT role_id FROM accounts WHERE account_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($roleId);
$stmt->fetch();
$stmt->close();

if ($roleId !== 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM places WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
$stmt->close();
?>