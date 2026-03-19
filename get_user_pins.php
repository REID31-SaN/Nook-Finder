<?php
session_start();
include 'config.php';

// If the user is not logged in, return an empty array
// This means non-logged-in users simply see no personal pins
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

// Fetch all pins that belong to this specific user only
$account_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, name, note, latitude, longitude FROM user_pins WHERE account_id = ?");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();

// Collect all rows into an array
$pins = [];
while ($row = $result->fetch_assoc()) {
    $pins[] = $row;
}

// Send the pins back as JSON so JavaScript can place them on the map
header('Content-Type: application/json');
echo json_encode($pins);
?>
