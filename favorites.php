<?php
include_once 'config.php';
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all favourites with place id
$stmt = $conn->prepare("
    SELECT p.id, p.name, p.image
    FROM favorites f
    JOIN places p ON f.place_id = p.id
    WHERE f.account_id = ?
    ORDER BY f.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$favorites = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<section class="discover" style="margin-top:20px; padding-top:20px; min-height:80vh;">
    <div class="top-text" style="width:100%; text-align:center; margin-bottom:40px; padding:0;">
        <h2>My Favorite Nooks</h2>
        <p>Click on a place to view its details or remove it from your favorites.</p>
    </div>
    <div class="discover-cards">
        <?php if (!empty($favorites)): ?>
            <?php foreach ($favorites as $row):
                $pid    = (int) $row['id'];
                $link   = 'cafe_window.php?place_id=' . $pid . '&cafe=' . urlencode($row['name']);
                $hasImg = !empty($row['image']) && file_exists($row['image']);
            ?>
                <div class="place-card">
                    <a href="<?= $link ?>" style="text-decoration:none; color:inherit; display:block; height:100%;">
                        <?php if ($hasImg): ?>
                            <img src="<?= htmlspecialchars($row['image']) ?>"
                                 alt="<?= htmlspecialchars($row['name']) ?>"
                                 style="height:200px; object-fit:cover; width:100%;">
                        <?php else: ?>
                            <div style="height:200px; width:100%; background:linear-gradient(135deg,#522e15,#6D3E1C,#8B5A2B); display:flex; align-items:center; justify-content:center; border-radius:10px 10px 0 0;">
                                <span style="font-size:2.2rem; opacity:0.45;">🏙️</span>
                            </div>
                        <?php endif; ?>
                        <div class="place-name">
                            <span><?= htmlspecialchars($row['name']) ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="grid-column:1/-1; color:#888;">
                You haven't added any favorites yet.
                <a href="discover.php" style="color:#6D3E1C; font-weight:bold;">Discover places</a>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>