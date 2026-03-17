<?php
session_start();
header('Content-Type: application/json');
include 'config.php';

if (!isset($_SESSION['user_id'])) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

// Verify admin
$stmt = $conn->prepare("SELECT Type FROM accounts WHERE account_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($userType);
$stmt->fetch();
$stmt->close();

if ($userType !== 'Admin') { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

$id     = intval($_POST['id']);
$status = $_POST['status'] === 'approved' ? 'approved' : 'rejected';
$reason = trim($_POST['reason'] ?? '');
$admin  = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE places SET status = ?, reviewed_by = ?, reviewed_at = NOW(), rejection_reason = ? WHERE id = ?");
$stmt->bind_param("sisi", $status, $admin, $reason, $id);

echo $stmt->execute() ? json_encode(['success' => true]) : json_encode(['success' => false, 'message' => 'DB error']);
$stmt->close();
?>