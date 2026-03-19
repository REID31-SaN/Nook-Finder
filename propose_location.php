<?php
session_start();
header('Content-Type: application/json');

// Only logged-in users can propose
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

include 'config.php'; // Database connection file

$name        = trim($_POST['name'] ?? '');
$location    = trim($_POST['location'] ?? '');
$description = trim($_POST['description'] ?? '');
$latitude    = floatval($_POST['latitude'] ?? 0);
$longitude   = floatval($_POST['longitude'] ?? 0);
$wifi        = $_POST['wifi'] === 'Yes' ? 'Yes' : 'No';
$outlet      = $_POST['outlet'] === 'Yes' ? 'Yes' : 'No';
$aircon      = $_POST['aircon'] === 'Yes' ? 'Yes' : 'No';
$parking     = $_POST['parking'] === 'Yes' ? 'Yes' : 'No';
$proposed_by = $_SESSION['user_id'];

// Basic validation
if (!$name || !$location || !$description || !$latitude || !$longitude) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO places (name, location, description, latitude, longitude, wifi, outlet, aircon, parking, status, proposed_by, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())
");
$stmt->bind_param("sssddssssi", $name, $location, $description, $latitude, $longitude, $wifi, $outlet, $aircon, $parking, $proposed_by);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}

$stmt->close();
?>