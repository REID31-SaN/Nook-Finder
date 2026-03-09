<?php
session_start();
include_once 'config.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $user_id = $_SESSION['user_id'];
    $cafe_name = $data['cafe_name'] ?? '';
    $action = $data['action'] ?? '';

    // Look up the place_id from the places table using the name
    $place_stmt = $conn->prepare("SELECT id FROM places WHERE name = ?");
    $place_stmt->bind_param("s", $cafe_name);
    $place_stmt->execute();
    $place_result = $place_stmt->get_result();
    $place = $place_result->fetch_assoc();

    if (!$place) {
        echo json_encode(['status' => 'error', 'message' => 'Place not found']);
        exit();
    }

    $place_id = $place['id'];

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT IGNORE INTO favorites (account_id, place_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $place_id);
        if($stmt->execute()) {
            echo json_encode(['status' => 'success', 'newState' => 'added']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    } elseif ($action == 'remove') {
        $stmt = $conn->prepare("DELETE FROM favorites WHERE account_id = ? AND place_id = ?");
        $stmt->bind_param("ii", $user_id, $place_id);
        if($stmt->execute()) {
            echo json_encode(['status' => 'success', 'newState' => 'removed']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
    exit();
}
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>