<?php 
include 'config.php';
include 'header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, profile_pic FROM accounts WHERE account_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

if (!empty($user_data['profile_pic']) && file_exists($user_data['profile_pic'])) {
    $display_img = $user_data['profile_pic'];
} else {
    $display_img = 'images/default-user.jpg';
}
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
                    <button type="submit" name="remove_photo" class="remove-btn" 
                            style="background: none; border: none; color: red; cursor: pointer; padding: 5px 0; font-size: 0.85rem; text-decoration: underline;">
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
        <h2 style="text-align: center; margin-top: 50px;">My Favorite Nooks</h2>
        <div class="discover-cards">
            <p style="text-align: center; width: 100%; color: #888;">Visit the <a href="discover.php">Discover</a> page to add favorites!</p>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>