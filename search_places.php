<?php
include_once 'config.php';

// Get the search query from the URL
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// If query is empty, return all approved places (used for loading pins on page load)
if ($query === '') {
    $result = $conn->query("SELECT name, location, latitude, longitude, distance_km, wifi, outlet, aircon, parking, image FROM places WHERE status = 'approved'");
    $places = [];
    while ($row = $result->fetch_assoc()) {
        $places[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($places);
    exit;
}

// Search the places table
$stmt = $conn->prepare("SELECT name, location, latitude, longitude, distance_km, wifi, outlet, aircon, parking, image FROM places WHERE status = 'approved' AND name LIKE ?");
$search = '%' . $query . '%';
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

// Collect all matching rows into an array
$places = [];
while ($row = $result->fetch_assoc()) {
    $places[] = $row;
}

// Send the results back as JSON so JavaScript can read it
header('Content-Type: application/json');
echo json_encode($places);
?>