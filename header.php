<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nook Finder</title>
    <link rel="stylesheet" href="style.css">
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
            <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php" class="nav-link">Logout</a>
        <?php else: ?>
            <a href="login.php" class="nav-link login-circle">Login</a>
        <?php endif; ?>
    </nav>
</header>