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
        // Fetch up to 4 recent favorites for preview
        $fav_stmt = $conn->prepare("SELECT cafe_name, cafe_image FROM favorites WHERE account_id = ? ORDER BY created_at DESC LIMIT 4");
        $fav_stmt->bind_param("i", $user_id);
        $fav_stmt->execute();
        $fav_res = $fav_stmt->get_result();
        ?>

        <div class="discover-cards" style="margin-top: 20px;">
            <?php if($fav_res->num_rows > 0): ?>
                <?php while($fav = $fav_res->fetch_assoc()): ?>
                    <div class="place-card">
                        <a href="cafe_window.php?cafe=<?= urlencode($fav['cafe_name']) ?>&img=<?= urlencode($fav['cafe_image']) ?>" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                            <img src="<?= htmlspecialchars($fav['cafe_image']) ?>" alt="<?= htmlspecialchars($fav['cafe_name']) ?>" style="height: 200px; object-fit:cover; width:100%;">
                            <div class="place-name">
                                <span><?= htmlspecialchars($fav['cafe_name']) ?></span>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; width: 100%; color: #888; grid-column: 1 / -1;">Visit the <a href="discover.php" style="color: #6D3E1C;">Discover</a> page to add favorites!</p>
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