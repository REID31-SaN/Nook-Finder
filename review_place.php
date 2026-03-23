<?php
// ═══════════════════════════════════════════════════════
//  review_place.php  —  AJAX: admin approve/reject
// ═══════════════════════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
include 'config.php';

// Guard: must be logged-in admin
if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$uid  = (int) $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role_id FROM accounts WHERE account_id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$stmt->bind_result($roleId);
$stmt->fetch();
$stmt->close();

if ($roleId !== 2) {
    echo json_encode(['success' => false, 'message' => 'Not authorized.']);
    exit;
}

// Validate input
$id     = (int)  ($_POST['id']     ?? 0);
$status = trim(   $_POST['status'] ?? '');
$reason = trim(   $_POST['reason'] ?? '');

if (!$id || !in_array($status, ['approved', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$reviewed_at = date('Y-m-d H:i:s');

if ($status === 'approved') {
    $stmt = $conn->prepare("
        UPDATE places
        SET status = 'approved', reviewed_by = ?, reviewed_at = ?, rejection_reason = NULL
        WHERE id = ?
    ");
    $stmt->bind_param("isi", $uid, $reviewed_at, $id);
} else {
    $stmt = $conn->prepare("
        UPDATE places
        SET status = 'rejected', reviewed_by = ?, reviewed_at = ?, rejection_reason = ?
        WHERE id = ?
    ");
    $stmt->bind_param("issi", $uid, $reviewed_at, $reason, $id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
$stmt->close();