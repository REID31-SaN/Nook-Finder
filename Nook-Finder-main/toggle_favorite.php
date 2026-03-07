<?php
session_start();
include_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Read JSON payload from Javascript fetch
    $data = json_decode(file_get_contents('php://input'), true);
    
    $user_id = $_SESSION['user_id'];
    $cafe_name = $data['cafe_name'] ?? '';
    $cafe_image = $data['cafe_image'] ?? '';
    $action = $data['action'] ?? '';

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO favorites (account_id, cafe_name, cafe_image) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $cafe_name, $cafe_image);
        if($stmt->execute()) {
            echo json_encode(['status' => 'success', 'newState' => 'added']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    } elseif ($action == 'remove') {
        $stmt = $conn->prepare("DELETE FROM favorites WHERE account_id = ? AND cafe_name = ?");
        $stmt->bind_param("is", $user_id, $cafe_name);
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