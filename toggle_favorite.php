<?php
// ═══════════════════════════════════════════════════════
//  toggle_favorite.php  —  AJAX: add/remove a favourite
// ═══════════════════════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
include 'config.php';

// ── Guard ─────────────────────────────────────────────────────────────────────
if (empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$data   = json_decode(file_get_contents('php://input'), true);
$name   = trim($data['cafe_name']  ?? '');
$action = trim($data['action']     ?? '');
$uid    = (int) $_SESSION['user_id'];

if (!$name || !in_array($action, ['add', 'remove'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// ── Resolve place_id ──────────────────────────────────────────────────────────
$stmt = $conn->prepare("SELECT id FROM places WHERE name = ? LIMIT 1");
$stmt->bind_param("s", $name);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Place not found']);
    exit;
}

$place_id = (int) $row['id'];

// ── Toggle ────────────────────────────────────────────────────────────────────
if ($action === 'add') {
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO favorites (account_id, place_id) VALUES (?, ?)"
    );
    $stmt->bind_param("ii", $uid, $place_id);
} else {
    $stmt = $conn->prepare(
        "DELETE FROM favorites WHERE account_id = ? AND place_id = ?"
    );
    $stmt->bind_param("ii", $uid, $place_id);
}

$stmt->execute();
$stmt->close();

echo json_encode(['status' => 'success']);