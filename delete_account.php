<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get and validate input
$data = json_decode(file_get_contents('php://input'), true);
$password = $data['password'] ?? '';

if (empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Password required']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Verify password before deletion
$query = "SELECT password FROM accounts WHERE account_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid password']);
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {

    $delete_query = "DELETE FROM accounts WHERE account_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    mysqli_commit($conn);
    
    // Clear session and logout
    session_destroy();
    
    echo json_encode(['success' => true, 'message' => 'Account permanently deleted']);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete account: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>