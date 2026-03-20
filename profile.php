<?php 
include_once 'config.php';
include_once 'header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, profile_pic FROM accounts WHERE account_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$_SESSION['profile_pic'] = $user_data['profile_pic'];

$display_img = (!empty($user_data['profile_pic']) && file_exists($user_data['profile_pic'])) ? $user_data['profile_pic'] : 'images/default-user.jpg';
?>

<main class="profile-container">
    
    <div class="profile-header">
        <div class="profile-avatar">
            <img src="<?php echo $display_img; ?>" alt="User Profile">
        </div>
        <h1><?php echo htmlspecialchars($user_data['username']); ?></h1>
        
        <?php if(isset($_GET['success'])): ?>
            <p style="color: green;">Profile updated successfully!</p>
        <?php elseif(isset($_GET['error'])): ?>
            <p style="color: red;">Current password was incorrect.</p>
        <?php endif; ?>
    </div>

    <div class="profile-box">
        <h2>Account Settings</h2>
        <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
            <div class="form-group">
                <label>Profile Picture</label>
                <input type="file" name="profile_image" accept="image/*">
                <?php if (!empty($user_data['profile_pic'])): ?>
                    <button type="submit" name="remove_photo" style="background: none; border: none; color: red; cursor: pointer; padding: 5px 0; text-decoration: underline;">
                        Remove current photo
                    </button>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>New Username</label>
                <input type="text" name="new_username" placeholder="Leave blank to keep current">
            </div>
            <hr>
            <div class="form-group">
                <label>Current Password (Required to save changes)</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Leave blank to keep current">
            </div>
            <button type="submit" class="update-btn">Update Profile</button>
        </form>
    </div>

    <section id="my-favourites">
        <h2 style="text-align: center; margin-top: 50px;">Recent Favorites</h2>
        
        <?php
        // Fetch up to 4 recent favorites with place details
        $fav_stmt = $conn->prepare("
            SELECT p.id, p.name, p.image 
            FROM favorites f 
            JOIN places p ON f.place_id = p.id 
            WHERE f.account_id = ? 
            ORDER BY f.created_at DESC 
            LIMIT 4
        ");
        $fav_stmt->bind_param("i", $user_id);
        $fav_stmt->execute();
        $fav_res = $fav_stmt->get_result();
        ?>

        <div class="discover-cards" style="margin-top: 20px;">
            <?php if($fav_res->num_rows > 0): ?>
                <?php while($fav = $fav_res->fetch_assoc()):
                    // Use place_id link so proposed/new places without hardcoded data resolve correctly
                    $card_link = 'cafe_window.php?place_id=' . intval($fav['id']) . '&cafe=' . urlencode($fav['name']);
                    // Fallback image for places that have no photo yet
                    $card_img  = (!empty($fav['image']) && file_exists($fav['image'])) ? htmlspecialchars($fav['image']) : null;
                ?>
                    <div class="place-card">
                        <a href="<?= $card_link ?>" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                            <?php if ($card_img): ?>
                                <img src="<?= $card_img ?>" alt="<?= htmlspecialchars($fav['name']) ?>" style="height: 200px; object-fit:cover; width:100%;">
                            <?php else: ?>
                                <!-- Placeholder for places without an uploaded image -->
                                <div style="height:200px; width:100%; background: linear-gradient(135deg,#522e15,#6D3E1C,#8B5A2B); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; border-radius:10px 10px 0 0;">
                                    <span style="font-size:2.2rem; opacity:0.45;">🏙️</span>
                                    <span style="font-size:0.78rem; color:rgba(255,243,219,0.5); font-weight:600; letter-spacing:0.04em;">No image yet</span>
                                </div>
                            <?php endif; ?>
                            <div class="place-name">
                                <span><?= htmlspecialchars($fav['name']) ?></span>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; width: 100%; color: #888; grid-column: 1 / -1;">
                    Visit the <a href="discover.php" style="color: #6D3E1C;">Discover</a> page to add favorites!
                </p>
            <?php endif; ?>
        </div>

        <?php if($fav_res->num_rows > 0): ?>
            <div style="text-align: center; margin-top: 30px;">
                <a href="favorites.php" class="update-btn" style="text-decoration: none; display: inline-block; width: auto; padding: 10px 30px;">Manage All Favorites</a>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>