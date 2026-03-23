<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
include 'config.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$uid      = (int) $_SESSION['user_id'];
$place_id = (int) ($_POST['place_id'] ?? 0);

if (!$place_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid place ID.']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM places WHERE id = ?");
$stmt->bind_param("i", $place_id);
$stmt->execute();
$place = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$place) {
    echo json_encode(['success' => false, 'message' => 'Place not found.']);
    exit;
}

$name          = trim($_POST['name']          ?? $place['name']);
$location      = trim($_POST['location']      ?? $place['location']);
$description   = trim($_POST['description']   ?? $place['description']);
$hours_weekday = trim($_POST['hours_weekday']  ?? '');
$hours_weekend = trim($_POST['hours_weekend']  ?? '');
$wifi          = ($_POST['wifi']    ?? '') === 'Yes' ? 'Yes' : 'No';
$outlet        = ($_POST['outlet']  ?? '') === 'Yes' ? 'Yes' : 'No';
$aircon        = ($_POST['aircon']  ?? '') === 'Yes' ? 'Yes' : 'No';
$parking       = ($_POST['parking'] ?? '') === 'Yes' ? 'Yes' : 'No';
$latitude      = (float) ($_POST['latitude']    ?? $place['latitude']);
$longitude     = (float) ($_POST['longitude']   ?? $place['longitude']);
$distance_km   = (float) ($_POST['distance_km'] ?? $place['distance_km']);

$image = $place['image'];
if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (in_array($ext, $allowed)) {
        $filename = 'images/place_' . $place_id . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filename)) {
            $image = $filename;
        }
    }
}

$is_publish = ($_POST['action'] ?? '') === 'publish';

if ($is_publish) {
    $reviewed_by = $uid;
    $reviewed_at = date('Y-m-d H:i:s');
    $upd = $conn->prepare("
        UPDATE places
        SET name          = ?,
            location      = ?,
            description   = ?,
            image         = ?,
            wifi          = ?,
            outlet        = ?,
            aircon        = ?,
            parking       = ?,
            latitude      = ?,
            longitude     = ?,
            distance_km   = ?,
            hours_weekday = ?,
            hours_weekend = ?,
            status        = 'approved',
            reviewed_by   = ?,
            reviewed_at   = ?
        WHERE id = ?
    ");
    $upd->bind_param(
        "ssssssssdddssisi",
        $name, $location, $description, $image,
        $wifi, $outlet, $aircon, $parking,
        $latitude, $longitude, $distance_km,
        $hours_weekday, $hours_weekend,
        $reviewed_by, $reviewed_at,
        $place_id
    );
} else {
    $upd = $conn->prepare("
        UPDATE places
        SET name          = ?,
            location      = ?,
            description   = ?,
            image         = ?,
            wifi          = ?,
            outlet        = ?,
            aircon        = ?,
            parking       = ?,
            latitude      = ?,
            longitude     = ?,
            distance_km   = ?,
            hours_weekday = ?,
            hours_weekend = ?
        WHERE id = ?
    ");
    $upd->bind_param(
        "ssssssssdddssi",
        $name, $location, $description, $image,
        $wifi, $outlet, $aircon, $parking,
        $latitude, $longitude, $distance_km,
        $hours_weekday, $hours_weekend,
        $place_id
    );
}

if (!$upd->execute()) {
    $upd->close();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}
$upd->close();

if ($is_publish) {
    unset($_SESSION['seen_place_ids']);
}

echo json_encode([
    'success'    => true,
    'published'  => $is_publish,
    'image'      => $image,
    'place_id'   => $place_id,
]);