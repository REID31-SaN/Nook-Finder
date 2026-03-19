<?php
session_start();
include 'config.php';

// Only logged-in users can save pins
// If the user is not logged in, stop here and return an error
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

// Get the data sent from the form (sent as JSON from JavaScript)
$data = json_decode(file_get_contents('php://input'), true);

// Grab each field from the submitted data
$account_id = $_SESSION['user_id'];
$name       = trim($data['name']);
$note       = trim($data['note']);
$latitude   = $data['latitude'];
$longitude  = $data['longitude'];

// Make sure name and coordinates are not empty before saving
if ($name === '' || !$latitude || !$longitude) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Insert the new pin into the user_pins table
$stmt = $conn->prepare("INSERT INTO user_pins (account_id, name, note, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issdd", $account_id, $name, $note, $latitude, $longitude);
$stmt->execute();

// Send back a success response so JavaScript knows it worked
echo json_encode(['status' => 'success', 'message' => 'Pin saved']);
?>
