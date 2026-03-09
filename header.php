<?php
session_start();
include_once 'config.php';

// Fetch user favorites globally so any card can check if it's favorited
$user_favorites = [];
if (isset($_SESSION['user_id']) && isset($conn)) {
    $fav_stmt = $conn->prepare("SELECT p.name FROM favorites f JOIN places p ON f.place_id = p.id WHERE f.account_id = ?");
    $fav_stmt->bind_param("i", $_SESSION['user_id']);
    $fav_stmt->execute();
    $fav_res = $fav_stmt->get_result();
    while($row = $fav_res->fetch_assoc()) {
        $user_favorites[] = $row['name'];
    }
}

// Global function to print the heart button on ANY place card
function renderHeartButton($cafeName, $cafeImage) {
    global $user_favorites;
    if (!isset($_SESSION['user_id'])) return ''; // Hide if not logged in
    
    $isFav = in_array($cafeName, $user_favorites);
    $action = $isFav ? 'remove' : 'add';
    $heart = $isFav ? '🤎' : '🤍'; // Brown heart for theme
    
    return '
    <button type="button" 
            class="heart-btn" 
            data-cafe="'.htmlspecialchars($cafeName).'" 
            data-image="'.htmlspecialchars($cafeImage).'" 
            data-action="'.$action.'" 
            onclick="toggleFavAJAX(event, this)" 
            title="Toggle Favorite">'.$heart.'</button>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nook Finder</title>
    <link rel="stylesheet" href="style.css">

    <!-- For map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- End - For map -->

    <script>
        function toggleFavAJAX(e, btn) {
            e.preventDefault(); 
            e.stopPropagation();
            
            const cafeName = btn.getAttribute('data-cafe');
            const cafeImage = btn.getAttribute('data-image');
            const action = btn.getAttribute('data-action');
            
            // 1. Optimistic UI update
            if (action === 'add') {
                btn.innerHTML = '🤎'; // Change to brown heart
                btn.setAttribute('data-action', 'remove');
            } else {
                btn.innerHTML = '🤍'; // Change to white heart
                btn.setAttribute('data-action', 'add');
            }
            
            // 2. Send request to database silently
            fetch('toggle_favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    cafe_name: cafeName,
                    cafe_image: cafeImage,
                    action: action
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'error' && data.message === 'Not logged in') {
                    window.location.href = 'login.php'; 
                }
                // Revert visual if database fails
                if(data.status !== 'success') {
                    btn.innerHTML = action === 'add' ? '🤍' : '🤎';
                    btn.setAttribute('data-action', action);
                }
            })
            .catch(err => console.error("Error toggling favorite:", err));
        }
    </script>
</head>

<body>

<header>
    <div class="logo">
        <a href="index.php">
            <img src="images/NookFindr Logo.png" alt="Nook Finder Logo">
        </a>
    </div>

    <nav>
        <a href="discover.php" class="nav-link">Discover</a>
        <a href="map.php" class="nav-link">Map</a>
        <a href="about.php" class="nav-link">About</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php" class="nav-link">My Profile</a>
            <a href="profile.php" class="nav-link">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
            </a>
            <a href="logout.php" class="nav-link">Logout</a>
        <?php else: ?>
            <a href="login.php" class="nav-link login-circle">Login / Register</a>
        <?php endif; ?>
    </nav>

</header>
