<?php
session_start();
include 'config.php';

// Only logged in users can delete pins
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$pin_id = $data['id'];
$account_id = $_SESSION['user_id'];

// Only delete the pin if it belongs to the logged in user
// The AND account_id = ? prevents users from deleting each other's pins
$stmt = $conn->prepare("DELETE FROM user_pins WHERE id = ? AND account_id = ?");
$stmt->bind_param("ii", $pin_id, $account_id);
$stmt->execute();

echo json_encode(['status' => 'success']);
?>