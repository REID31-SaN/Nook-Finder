<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'config.php';

date_default_timezone_set('Asia/Manila');

$cafeName = $_GET['cafe'] ?? 'Cafe Name';
$cafeImage = $_GET['img'] ?? 'images/default.jpg';

// Fetch user type for Admin privileges
$user_type = 'User';
$current_user_id = $_SESSION['user_id'] ?? 0;
if ($current_user_id > 0 && isset($conn)) {
    $u_stmt = $conn->prepare("SELECT Type FROM accounts WHERE account_id = ?");
    $u_stmt->bind_param("i", $current_user_id);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result();
    if($u_row = $u_res->fetch_assoc()) {
        $user_type = $u_row['Type'];
    }
}

// Fetch place_id from the database
$place_id = null;
if (isset($conn)) {
    $stmt_place = $conn->prepare("SELECT id FROM places WHERE name = ?");
    $stmt_place->bind_param("s", $cafeName);
    $stmt_place->execute();
    $res = $stmt_place->get_result();
    if ($row = $res->fetch_assoc()) {
        $place_id = $row['id'];
    }
}

// Handle Add Review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && $current_user_id > 0 && $place_id) {
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);
    $allow_replies = isset($_POST['allow_replies']) ? 1 : 0;
    
    if ($rating >= 1 && $rating <= 5 && !empty($review_text)) {
        $ins = $conn->prepare("INSERT INTO reviews (place_id, account_id, rating, review_text, allow_replies) VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param("iiisi", $place_id, $current_user_id, $rating, $review_text, $allow_replies);
        $ins->execute();
    }
    header("Location: cafe_window.php?cafe=" . urlencode($cafeName) . "&img=" . urlencode($cafeImage) . "&sort=" . urlencode($_GET['sort'] ?? 'recent'));
    exit();
}

// Handle Add Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply']) && $current_user_id > 0) {
    $review_id = intval($_POST['review_id']);
    $reply_text = trim($_POST['reply_text']);
    
    if (!empty($reply_text)) {
        $ins_rep = $conn->prepare("INSERT INTO review_replies (review_id, account_id, reply_text) VALUES (?, ?, ?)");
        $ins_rep->bind_param("iis", $review_id, $current_user_id, $reply_text);
        $ins_rep->execute();
    }
    header("Location: cafe_window.php?cafe=" . urlencode($cafeName) . "&img=" . urlencode($cafeImage) . "&sort=" . urlencode($_GET['sort'] ?? 'recent'));
    exit();
}

// Handle Delete Review (User owns it, OR User is Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review']) && $current_user_id > 0) {
    $review_id = intval($_POST['review_id']);
    
    if ($user_type === 'Admin') {
        $del = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        $del->bind_param("i", $review_id);
    } else {
        $del = $conn->prepare("DELETE FROM reviews WHERE id = ? AND account_id = ?");
        $del->bind_param("ii", $review_id, $current_user_id);
    }
    $del->execute();
    
    header("Location: cafe_window.php?cafe=" . urlencode($cafeName) . "&img=" . urlencode($cafeImage) . "&sort=" . urlencode($_GET['sort'] ?? 'recent'));
    exit();
}

// Fetch Review Stats
$avg_rating = 0;
$total_reviews = 0;
if ($place_id) {
    $stat = $conn->prepare("SELECT AVG(rating) as avg, COUNT(*) as cnt FROM reviews WHERE place_id = ?");
    $stat->bind_param("i", $place_id);
    $stat->execute();
    $stat_res = $stat->get_result()->fetch_assoc();
    $avg_rating = round($stat_res['avg'], 1);
    $total_reviews = $stat_res['cnt'];
}

$sort = $_GET['sort'] ?? 'recent';

include_once 'header.php'; 

$descriptions = [
    "Co.Create" => "A modern collaborative coworking space designed for productivity and creativity.",
    "Cush Lounge" => "Is a cozy, premium co-working and relaxation spot for students and remote workers in Angeles City (MC Place)",
    "Vessel Coworking Space" => "A professional coworking environment perfect for startups and remote workers.",
    "Kuwento Cafe" => "Cozy workspace vibes Matcha Bar Supporting local farmers, and local roasters, a warm cafe space where stories are shared over great coffee.",
    "oFTr" => "Small and Cozy hangout spot, Perfect for a quick break and small group study.",
    "Angeles City Library" => "Located in the heart of Angeles City, this library offers a quiet, well-equipped environment with a wealth of materials for students",
    "BRUDR" => "A bistro styel cafe that serves good food and coffee, with a cozy ambiance perfect for work or study.",
    "Arte Cafe" => "An artsy cafe with a relaxed atmosphere for students."
];

$cafeInfos = [
    "Co.Create" => [
        "Location" => "Unit 101 Mission Plaza Bldg, MacArthur Hwy, Angeles, 2009 Pampanga",
        "Hours" => "8 AM - 10 PM (Mon-Sat), 10:30AM-7:30PM (Sunday)",
        "Features" => "Wifi - Available, Power Outlets - Available, Renting - Available, Air-Conditioned"
    ],
    "Cush Lounge" => [
        "Location" => "2F MC Place, Brgy. Santo Cristo, Angeles City",
        "Hours" => "8 AM - 2 AM (Weekdays), 10 AM - 2 AM (Weekends)",
        "Features" => "Wifi - Available, Power Outlets - Available, Renting - Available, Air-Conditioned"
    ],
    "Vessel Coworking Space" => [
        "Location" => "Unit 14, 2nd Flr Marcel Bldg., 2355 Sto. Entierro, Cor Jesus St, Santo Cristo, Angeles, 2009 Pampanga",
        "Hours" => "7 AM - 6 PM (Mon-Fri), Reserved Only (Saturday), Closed (Sunday)",
        "Features" => "Wifi - Available, Power Outlets - Available, Renting - Available, Air-Conditioned"
    ],
    "Kuwento Cafe" => [
        "Location" => "Unit 101 Mission Plaza Bldg, MacArthur Hwy, Angeles, 2009 Pampanga",
        "Hours" => "7 AM - 12 AM (Daily)",
        "Features" => "Wifi - Available, Power Outlets - Available, Renting - Available, Air-Conditioned"
    ],
    "oFTr" => [
        "Location" => "2F, Bart Mall, Santo Rosario St, Angeles, 2009 Pampanga",
        "Hours" => "8 AM - 8 PM (Daily)",
        "Features" => "Power Outlets - Available,  Air-Conditioned"
    ],   
    "Angeles City Library" => [
        "Location" => "Santo Entierro St, Angeles City, 2009 Pampanga",
        "Hours" => "9 AM - 5 PM (Daily)",
        "Features" => "Wifi - Available, Power Outlets - Available,  Air-Conditioned"
    ],
    "BRUDR" => [
        "Location" => "Miranda Street, Angeles City, Philippines, 2009 Pampanga",
        "Hours" => "10 AM - 10 PM (Daily)",
        "Features" => "Power Outlets - Available,  Air-Conditioned"
    ],
    "Arte Cafe" => [
        "Location" => "Angeles City, Pampanga",
        "Hours" => "9 AM - 9 PM (Daily)",
        "Features" => "Wifi - Available, Air-Conditioned"
    ]    
];

function isOpenNow($cafeName) {
    $day = date('D');
    $time = date('H:i');
    switch ($cafeName) {
        case "Co.Create": return ($day == "Sun") ? ($time >= "10:30" && $time <= "19:30") : ($time >= "08:00" && $time <= "22:00");
        case "Cush Lounge": return (in_array($day, ["Mon","Tue","Wed","Thu","Fri"])) ? ($time >= "08:00" || $time <= "02:00") : ($time >= "10:00" || $time <= "02:00");
        case "Vessel Coworking Space": return (in_array($day, ["Mon","Tue","Wed","Thu","Fri"])) ? ($time >= "07:00" && $time <= "18:00") : false;
        case "Kuwento Cafe": return ($time >= "07:00" || $time <= "00:00");
        default: return false;
    }
}

$isOpen = isOpenNow($cafeName);
$cafeDescription = $descriptions[$cafeName] ?? "No descriptions yet...";
$info = $cafeInfos[$cafeName] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= ucfirst($cafeName); ?></title>
    <script>
        function toggleBigFavAJAX(btn) {
            const cafeName = btn.getAttribute('data-cafe');
            const cafeImage = btn.getAttribute('data-image');
            const action = btn.getAttribute('data-action');
            
            if (action === 'add') {
                btn.innerHTML = '🤎 Remove from Favorites';
                btn.className = 'btn-fav unfav'; 
                btn.setAttribute('data-action', 'remove');
            } else {
                btn.innerHTML = '🤍 Add to Favorites';
                btn.className = 'btn-fav fav';
                btn.setAttribute('data-action', 'add');
            }
            
            fetch('toggle_favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cafe_name: cafeName, cafe_image: cafeImage, action: action })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status !== 'success') {
                    alert("Something went wrong!");
                    window.location.reload();
                }
            });
        }

        function toggleReviewLike(btn, reviewId) {
            fetch('toggle_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ review_id: reviewId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    if (data.action === 'liked') {
                        btn.classList.add('liked');
                        btn.querySelector('.like-icon').innerText = '❤️';
                    } else {
                        btn.classList.remove('liked');
                        btn.querySelector('.like-icon').innerText = '🤍';
                    }
                    btn.querySelector('.like-count').innerText = data.likes;
                } else if (data.status === 'error' && data.message === 'Not logged in') {
                    window.location.href = 'login.php';
                }
            });
        }

        function toggleReplyForm(reviewId) {
            const form = document.getElementById('reply-form-' + reviewId);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'flex';
            } else {
                form.style.display = 'none';
            }
        }

        // --- SEAMLESS FILTER AJAX ---
        function fetchSortedReviews() {
            const sortVal = document.getElementById('sort').value;
            const placeId = document.getElementById('current_place_id').value;
            const container = document.getElementById('reviews-container');
            
            container.style.opacity = '0.4'; // loading state
            
            fetch(`fetch_reviews.php?ajax=1&place_id=${placeId}&sort=${sortVal}`)
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                container.style.opacity = '1';
            })
            .catch(err => {
                console.error("Failed to load sorted reviews", err);
                container.style.opacity = '1';
            });
        }
    </script>
</head>
<body>

<div class="cafe-top">
    <img src="<?= htmlspecialchars($cafeImage); ?>" class="cafe-hero">
    <div class="frosted">
        <h1><?= htmlspecialchars($cafeName); ?></h1>
    </div>
</div>

<div class="cafe-bottom">
    <div class="info-container">
        
        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 15px;">
            <h2 style="margin: 0;">About <?= htmlspecialchars($cafeName); ?></h2>
            <div>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php 
                        $isFav = in_array($cafeName, $user_favorites); 
                        $action = $isFav ? 'remove' : 'add';
                        $btnTxt = $isFav ? '🤎 Remove from Favorites' : '🤍 Add to Favorites';
                        $btnCls = $isFav ? 'unfav' : 'fav';
                    ?>
                    <button type="button" 
                            class="btn-fav <?= $btnCls ?>" 
                            data-cafe="<?= htmlspecialchars($cafeName); ?>" 
                            data-image="<?= htmlspecialchars($cafeImage); ?>" 
                            data-action="<?= $action ?>" 
                            onclick="toggleBigFavAJAX(this)">
                        <?= $btnTxt ?>
                    </button>
                <?php else: ?>
                    <span style="font-size: 0.9rem; color: #888;"><em><a href="login.php" style="color: #6D3E1C;">Log in</a> to save</em></span>
                <?php endif; ?>
            </div>
        </div>

        <p><?= htmlspecialchars($cafeDescription); ?></p>

        <div class="info-grid">
            <?php foreach($info as $title => $value): ?>
                <div class="info-box">
                    <h4><?php echo htmlspecialchars($title); ?></h4>
                    <?php if($title === "Hours"): ?>
                        <div class="status-badge <?= $isOpen ? 'open' : 'closed'; ?>">
                            <?= $isOpen ? 'Open Now' : 'Closed Now'; ?>
                        </div>
                    <?php endif; ?>
                    <p>
                        <?php 
                        if(in_array($title, ['Hours', 'Features'])){
                            echo nl2br(str_replace(',', "<br>", htmlspecialchars($value)));
                        } else {
                            echo htmlspecialchars($value);
                        }
                        ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <hr class="reviews-divider">
        
        <div class="reviews-section">
            <div class="reviews-header-bar">
                <div>
                    <h3 style="margin-bottom: 5px;">Reviews (<?= $total_reviews ?>)</h3>
                    <?php if($total_reviews > 0): ?>
                        <div class="avg-rating">
                            <span class="stars-large"><?= str_repeat('★', round($avg_rating)) ?><?= str_repeat('☆', 5 - round($avg_rating)) ?></span>
                            <span class="score"><?= number_format($avg_rating, 1) ?> / 5.0</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="reviews-controls">
                    <input type="hidden" id="current_place_id" value="<?= $place_id ?>">
                    <label for="sort" style="font-weight: 500; font-size: 0.9rem;">Sort By:</label>
                    <select id="sort" class="sort-select" onchange="fetchSortedReviews()">
                        <option value="recent" <?= ($sort == 'recent') ? 'selected' : '' ?>>Most Recent</option>
                        <option value="popular" <?= ($sort == 'popular') ? 'selected' : '' ?>>Most Popular (Likes)</option>
                    </select>
                </div>
            </div>

            <?php if($current_user_id > 0): ?>
                <div class="add-review-box">
                    <h4 style="margin-bottom: 10px;">Write a Review</h4>
                    <form method="POST" action="">
                        <div class="rating-input">
                            <label>Rating:</label>
                            <select name="rating" required class="sort-select">
                                <option value="5">★★★★★ (5/5) - Excellent</option>
                                <option value="4">★★★★☆ (4/5) - Very Good</option>
                                <option value="3">★★★☆☆ (3/5) - Average</option>
                                <option value="2">★★☆☆☆ (2/5) - Poor</option>
                                <option value="1">★☆☆☆☆ (1/5) - Terrible</option>
                            </select>
                        </div>
                        <textarea name="review_text" rows="3" placeholder="Share your experience here..." required class="review-textarea"></textarea>
                        
                        <div style="margin-bottom: 15px; display:flex; align-items:center; gap: 8px;">
                            <input type="checkbox" name="allow_replies" id="allow_replies" value="1" checked>
                            <label for="allow_replies" style="font-size:0.9rem; color:#555; cursor:pointer;">Allow others to reply to my review</label>
                        </div>

                        <button type="submit" name="submit_review" class="submit-btn">Post Review</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="add-review-box" style="text-align: center;">
                    <p style="margin: 0;">Please <a href="login.php" style="color: #6D3E1C; font-weight: bold; text-decoration: underline;">log in</a> to write a review.</p>
                </div>
            <?php endif; ?>

            <div class="reviews-list" id="reviews-container">
                <?php include 'fetch_reviews.php'; ?>
            </div>
            
        </div>
    </div>
</div>

</body>
</html>

<?php include 'footer.php'; ?>