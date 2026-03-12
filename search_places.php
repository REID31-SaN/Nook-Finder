<?php
include_once 'config.php';

// Get the search query from the URL
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// If query is empty, return all places (used for loading pins on page load)
if ($query === '') {
    $result = $conn->query("SELECT name, location, latitude, longitude, distance_km FROM places");
    $places = [];
    while ($row = $result->fetch_assoc()) {
        $places[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($places);
    exit;
}

// Search the places table for any name that contains the query
// % is a wildcard - so %query% means "anything before or after the word"
$stmt = $conn->prepare("SELECT name, location, latitude, longitude, distance_km FROM places WHERE name LIKE ?");
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
