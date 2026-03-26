<?php
session_start();
header('Content-Type: application/json');
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$id = (int)$_POST['id'];

// Only allow deleting regular users, never admins
$check = $conn->prepare("SELECT r.role_name FROM accounts a JOIN roles r ON a.role_id = r.role_id WHERE a.account_id = ?");
$check->bind_param("i", $id);
$check->execute();
$result = $check->get_result()->fetch_assoc();

if (!$result || $result['role_name'] === 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Cannot delete admin accounts.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM accounts WHERE account_id = ?");
$stmt->bind_param("i", $id);
echo json_encode(['success' => $stmt->execute()]);
?>