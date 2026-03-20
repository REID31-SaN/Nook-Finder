<?php
// ═══════════════════════════════════════════════════════
//  cafe_window.php  —  Individual place detail page
// ═══════════════════════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) session_start();

include_once 'config.php';

date_default_timezone_set('Asia/Manila');

$current_user_id = (int) ($_SESSION['user_id'] ?? 0);

// ── Load place from DB ────────────────────────────────────────────────────────
$place = null;
if (!empty($_GET['place_id']) && intval($_GET['place_id']) > 0) {
    $id   = intval($_GET['place_id']);
    $stmt = $conn->prepare("SELECT * FROM places WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $place = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} elseif (!empty($_GET['cafe'])) {
    $name = trim($_GET['cafe']);
    $stmt = $conn->prepare("SELECT * FROM places WHERE name = ? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $place = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// 404
if (!$place) {
    include_once 'header.php';
    echo '<div style="text-align:center;padding:80px 20px;color:#888;">
            <h2>Place not found</h2>
            <p>This location may no longer be available.</p>
            <a href="discover.php" style="color:#6D3E1C;font-weight:700;">← Back to Discover</a>
          </div>';
    include 'footer.php';
    exit;
}

$place_id = (int) $place['id'];

// ── User type ─────────────────────────────────────────────────────────────────
$user_type = 'User';
if ($current_user_id > 0) {
    $u = $conn->prepare("SELECT Type FROM accounts WHERE account_id = ?");
    $u->bind_param("i", $current_user_id);
    $u->execute();
    if ($ur = $u->get_result()->fetch_assoc()) $user_type = $ur['Type'];
}

// ── Favourites ────────────────────────────────────────────────────────────────
$fav_ids = [];
if ($current_user_id > 0) {
    $fr = $conn->query("SELECT place_id FROM favorites WHERE account_id = $current_user_id");
    while ($r = $fr->fetch_assoc()) $fav_ids[] = (int) $r['place_id'];
}
$is_favourite = in_array($place_id, $fav_ids);

// ── POST handlers ─────────────────────────────────────────────────────────────
$redirect = "cafe_window.php?place_id=$place_id&sort=" . urlencode($_GET['sort'] ?? 'recent');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_user_id > 0) {

    if (isset($_POST['submit_review'])) {
        $rating        = (int) ($_POST['rating'] ?? 0);
        $review_text   = trim($_POST['review_text'] ?? '');
        $allow_replies = isset($_POST['allow_replies']) ? 1 : 0;
        if ($rating >= 1 && $rating <= 5 && $review_text !== '') {
            $ins = $conn->prepare("INSERT INTO reviews (place_id, account_id, rating, review_text, allow_replies) VALUES (?,?,?,?,?)");
            $ins->bind_param("iiisi", $place_id, $current_user_id, $rating, $review_text, $allow_replies);
            $ins->execute();
        }
    }

    if (isset($_POST['submit_reply'])) {
        $review_id  = (int) ($_POST['review_id'] ?? 0);
        $reply_text = trim($_POST['reply_text'] ?? '');
        if ($reply_text !== '') {
            $ins = $conn->prepare("INSERT INTO review_replies (review_id, account_id, reply_text) VALUES (?,?,?)");
            $ins->bind_param("iis", $review_id, $current_user_id, $reply_text);
            $ins->execute();
        }
    }

    if (isset($_POST['delete_review'])) {
        $review_id = (int) ($_POST['review_id'] ?? 0);
        if ($user_type === 'Admin') {
            $del = $conn->prepare("DELETE FROM reviews WHERE id = ?");
            $del->bind_param("i", $review_id);
        } else {
            $del = $conn->prepare("DELETE FROM reviews WHERE id = ? AND account_id = ?");
            $del->bind_param("ii", $review_id, $current_user_id);
        }
        $del->execute();
    }

    header("Location: $redirect");
    exit;
}

// ── Review stats ──────────────────────────────────────────────────────────────
$stat = $conn->prepare("SELECT AVG(rating) AS avg, COUNT(*) AS cnt FROM reviews WHERE place_id = ?");
$stat->bind_param("i", $place_id);
$stat->execute();
$sr = $stat->get_result()->fetch_assoc();
$avg_rating    = round((float) $sr['avg'], 1);
$total_reviews = (int) $sr['cnt'];
$sort          = $_GET['sort'] ?? 'recent';

// ── Static fallback data for original 8 places ───────────────────────────────
$static_hours = [
    'Co.Create'              => '8 AM – 10 PM (Mon–Sat), 10:30 AM – 7:30 PM (Sun)',
    'Cush Lounge'            => '8 AM – 2 AM (Weekdays), 10 AM – 2 AM (Weekends)',
    'Vessel Coworking Space' => '7 AM – 6 PM (Mon–Fri), Reserved Only (Sat), Closed (Sun)',
    'Kuwento Cafe'           => '7 AM – 12 AM (Daily)',
    'oFTr'                   => '8 AM – 8 PM (Daily)',
    'Angeles City Library'   => '9 AM – 5 PM (Daily)',
    'BRUDR'                  => '10 AM – 10 PM (Daily)',
    'Arte Cafe'              => '9 AM – 9 PM (Daily)',
];
$static_locations = [
    'Co.Create'              => 'Unit 101 Mission Plaza Bldg, MacArthur Hwy, Angeles, 2009 Pampanga',
    'Cush Lounge'            => '2F MC Place, Brgy. Santo Cristo, Angeles City',
    'Vessel Coworking Space' => 'Unit 14, 2nd Flr Marcel Bldg., 2355 Sto. Entierro, Cor Jesus St, Santo Cristo, Angeles, 2009 Pampanga',
    'Kuwento Cafe'           => 'Unit 101 Mission Plaza Bldg, MacArthur Hwy, Angeles, 2009 Pampanga',
    'oFTr'                   => '2F, Bart Mall, Santo Rosario St, Angeles, 2009 Pampanga',
    'Angeles City Library'   => 'Santo Entierro St, Angeles City, 2009 Pampanga',
    'BRUDR'                  => 'Miranda Street, Angeles City, Philippines, 2009 Pampanga',
    'Arte Cafe'              => 'Angeles City, Pampanga',
];
$static_descriptions = [
    'Co.Create'              => 'A modern collaborative coworking space designed for productivity and creativity.',
    'Cush Lounge'            => 'A cozy, premium co-working and relaxation spot for students and remote workers in Angeles City (MC Place).',
    'Vessel Coworking Space' => 'A professional coworking environment perfect for startups and remote workers.',
    'Kuwento Cafe'           => 'Cozy workspace vibes — Matcha Bar supporting local farmers and roasters. A warm cafe where stories are shared over great coffee.',
    'oFTr'                   => 'Small and cozy hangout spot. Perfect for a quick break and small group study.',
    'Angeles City Library'   => 'Located in the heart of Angeles City, this library offers a quiet, well-equipped environment with a wealth of study materials.',
    'BRUDR'                  => 'A bistro-style cafe serving good food and coffee with a cozy ambiance perfect for work or study.',
    'Arte Cafe'              => 'An artsy cafe with a relaxed atmosphere for students.',
];

// ── Build display data ────────────────────────────────────────────────────────
$name = $place['name'];

$description = !empty($place['description'])
    ? $place['description']
    : ($static_descriptions[$name] ?? 'No description available.');

$location = $place['location'];
if (trim($location) === 'Angeles City, Pampanga' && isset($static_locations[$name])) {
    $location = $static_locations[$name];
}

$hoursWD = $place['hours_weekday'] ?? '';
$hoursWE = $place['hours_weekend'] ?? '';
$hours   = !empty($hoursWD)
    ? $hoursWD . (!empty($hoursWE) ? ', ' . $hoursWE : '')
    : ($static_hours[$name] ?? 'Hours not available');

$features = [];
if ($place['wifi']    === 'Yes') $features[] = 'Wifi – Available';
if ($place['outlet']  === 'Yes') $features[] = 'Power Outlets – Available';
if ($place['aircon']  === 'Yes') $features[] = 'Air-Conditioned';
if ($place['parking'] === 'Yes') $features[] = 'Parking – Available';

$info = [
    'Location' => $location,
    'Hours'    => $hours,
    'Features' => implode(', ', $features) ?: 'No amenities listed',
];

// ── Open/closed ───────────────────────────────────────────────────────────────
function is_open_now(string $name, string $hoursWD): bool {
    $day  = date('D');
    $time = date('H:i');
    $wd   = ['Mon','Tue','Wed','Thu','Fri'];

    if (!empty($hoursWD)) {
        preg_match('/(\d{1,2}(?::\d{2})?\s*(?:AM|PM))\s*[–\-]\s*(\d{1,2}(?::\d{2})?\s*(?:AM|PM))/i', $hoursWD, $m);
        if (count($m) === 3) {
            return date('H:i') >= date('H:i', strtotime($m[1])) && date('H:i') <= date('H:i', strtotime($m[2]));
        }
    }
    switch ($name) {
        case 'Co.Create':              return $day==='Sun' ? ($time>='10:30'&&$time<='19:30') : ($time>='08:00'&&$time<='22:00');
        case 'Cush Lounge':            return in_array($day,$wd) ? ($time>='08:00'||$time<='02:00') : ($time>='10:00'||$time<='02:00');
        case 'Vessel Coworking Space': return in_array($day,$wd) && $time>='07:00' && $time<='18:00';
        case 'Kuwento Cafe':           return $time>='07:00'||$time<='00:00';
        case 'oFTr':                   return $time>='08:00'&&$time<='20:00';
        case 'Angeles City Library':   return $time>='09:00'&&$time<='17:00';
        case 'BRUDR':                  return $time>='10:00'&&$time<='22:00';
        case 'Arte Cafe':              return $time>='09:00'&&$time<='21:00';
    }
    return false;
}

$is_open   = is_open_now($name, $hoursWD);
$image     = !empty($place['image']) ? $place['image'] : null;

include_once 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($name) ?> — Nook Finder</title>
</head>
<body>

<div class="cafe-top">
    <?php if ($image): ?>
        <img src="<?= htmlspecialchars($image) ?>" class="cafe-hero" alt="<?= htmlspecialchars($name) ?>">
    <?php else: ?>
        <div class="cafe-hero cafe-hero-placeholder">
            <div class="placeholder-inner">
                <span class="placeholder-icon">🏙️</span>
                <span class="placeholder-text"><?= htmlspecialchars($name) ?></span>
            </div>
        </div>
    <?php endif; ?>
    <div class="frosted"><h1><?= htmlspecialchars($name) ?></h1></div>
</div>

<style>
    .cafe-hero-placeholder { display:flex; align-items:center; justify-content:center; height:600px; background:linear-gradient(135deg,#522e15,#6D3E1C 55%,#8B5A2B); }
    .placeholder-inner     { display:flex; flex-direction:column; align-items:center; gap:12px; color:rgba(255,255,255,0.35); }
    .placeholder-icon      { font-size:4rem; opacity:0.45; }
    .placeholder-text      { font-size:1.1rem; font-weight:600; letter-spacing:0.05em; }
    .cw-action-bar         { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:15px; }
    .cw-action-bar h2      { margin:0; }
    .cw-action-buttons     { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .cw-edit-btn           { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:#6D3E1C; color:#FFF3DB; border-radius:8px; text-decoration:none; font-size:0.85rem; font-weight:700; }
    .cw-edit-btn:hover     { background:#522e15; }
    .cw-login-hint         { font-size:0.9rem; color:#888; }
    .cw-login-hint a       { color:#6D3E1C; }
    .reply-pref-row        { display:flex; align-items:center; gap:8px; margin-bottom:15px; font-size:0.9rem; color:#555; }
    .add-review-box--guest { text-align:center; }
    .add-review-box--guest a { color:#6D3E1C; font-weight:bold; text-decoration:underline; }
</style>

<div class="cafe-bottom">
    <div class="info-container">

        <div class="cw-action-bar">
            <h2>About <?= htmlspecialchars($name) ?></h2>
            <div class="cw-action-buttons">
                <?php if ($user_type === 'Admin'): ?>
                    <a href="admin_edit_place.php?id=<?= $place_id ?>" class="cw-edit-btn">✏️ Edit Cafe Window</a>
                <?php endif; ?>
                <?php if ($current_user_id > 0): ?>
                    <button class="btn-fav <?= $is_favourite ? 'unfav' : 'fav' ?>"
                            data-cafe="<?= htmlspecialchars($name) ?>"
                            data-image="<?= htmlspecialchars($image ?? '') ?>"
                            data-action="<?= $is_favourite ? 'remove' : 'add' ?>"
                            onclick="toggleBigFavAJAX(this)">
                        <?= $is_favourite ? '🤎 Remove from Favorites' : '🤍 Add to Favorites' ?>
                    </button>
                <?php else: ?>
                    <span class="cw-login-hint"><em><a href="login.php">Log in</a> to save</em></span>
                <?php endif; ?>
            </div>
        </div>

        <p><?= htmlspecialchars($description) ?></p>

        <div class="info-grid">
            <?php foreach ($info as $label => $value): ?>
                <div class="info-box">
                    <h4><?= htmlspecialchars($label) ?></h4>
                    <?php if ($label === 'Hours'): ?>
                        <div class="status-badge <?= $is_open ? 'open' : 'closed' ?>">
                            <?= $is_open ? 'Open Now' : 'Closed Now' ?>
                        </div>
                    <?php endif; ?>
                    <p><?php
                        if (in_array($label, ['Hours','Features'])) {
                            echo nl2br(str_replace(',', '<br>', htmlspecialchars($value)));
                        } else {
                            echo htmlspecialchars($value);
                        }
                    ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <hr class="reviews-divider">

        <div class="reviews-section">
            <div class="reviews-header-bar">
                <div>
                    <h3>Reviews (<?= $total_reviews ?>)</h3>
                    <?php if ($total_reviews > 0): ?>
                        <div class="avg-rating">
                            <span class="stars-large"><?= str_repeat('★', (int)round($avg_rating)) ?><?= str_repeat('☆', 5-(int)round($avg_rating)) ?></span>
                            <span class="score"><?= number_format($avg_rating,1) ?> / 5.0</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="reviews-controls">
                    <input type="hidden" id="current_place_id" value="<?= $place_id ?>">
                    <label for="sort">Sort By:</label>
                    <select id="sort" class="sort-select" onchange="fetchSortedReviews()">
                        <option value="recent"  <?= $sort==='recent'  ? 'selected':'' ?>>Most Recent</option>
                        <option value="popular" <?= $sort==='popular' ? 'selected':'' ?>>Most Popular (Likes)</option>
                    </select>
                </div>
            </div>

            <?php if ($current_user_id > 0): ?>
                <div class="add-review-box">
                    <h4>Write a Review</h4>
                    <form method="POST">
                        <div class="rating-input">
                            <label>Rating:</label>
                            <select name="rating" required class="sort-select">
                                <option value="5">★★★★★ (5/5) – Excellent</option>
                                <option value="4">★★★★☆ (4/5) – Very Good</option>
                                <option value="3">★★★☆☆ (3/5) – Average</option>
                                <option value="2">★★☆☆☆ (2/5) – Poor</option>
                                <option value="1">★☆☆☆☆ (1/5) – Terrible</option>
                            </select>
                        </div>
                        <textarea name="review_text" rows="3" placeholder="Share your experience here…" required class="review-textarea"></textarea>
                        <div class="reply-pref-row">
                            <input type="checkbox" name="allow_replies" id="allow_replies" value="1" checked>
                            <label for="allow_replies">Allow others to reply to my review</label>
                        </div>
                        <button type="submit" name="submit_review" class="submit-btn">Post Review</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="add-review-box add-review-box--guest">
                    <p>Please <a href="login.php">log in</a> to write a review.</p>
                </div>
            <?php endif; ?>

            <div class="reviews-list" id="reviews-container">
                <?php include 'fetch_reviews.php'; ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleBigFavAJAX(btn) {
    const action   = btn.getAttribute('data-action');
    const isAdding = action === 'add';
    btn.innerHTML  = isAdding ? '🤎 Remove from Favorites' : '🤍 Add to Favorites';
    btn.className  = 'btn-fav ' + (isAdding ? 'unfav' : 'fav');
    btn.setAttribute('data-action', isAdding ? 'remove' : 'add');
    fetch('toggle_favorite.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cafe_name: btn.getAttribute('data-cafe'), cafe_image: btn.getAttribute('data-image'), action: action })
    }).then(r=>r.json()).then(d=>{ if(d.status!=='success') location.reload(); }).catch(()=>location.reload());
}

function toggleReviewLike(btn, reviewId) {
    fetch('toggle_like.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({review_id:reviewId}) })
    .then(r=>r.json()).then(d=>{
        if (d.status==='success') {
            const liked = d.action==='liked';
            btn.classList.toggle('liked', liked);
            btn.querySelector('.like-icon').innerText  = liked ? '❤️' : '🤍';
            btn.querySelector('.like-count').innerText = d.likes;
        } else if (d.message==='Not logged in') window.location.href='login.php';
    });
}

function toggleReplyForm(reviewId) {
    const f = document.getElementById('reply-form-'+reviewId);
    f.style.display = f.style.display==='flex' ? 'none' : 'flex';
}

function fetchSortedReviews() {
    const sort      = document.getElementById('sort').value;
    const placeId   = document.getElementById('current_place_id').value;
    const container = document.getElementById('reviews-container');
    container.style.opacity = '0.4';
    fetch(`fetch_reviews.php?ajax=1&place_id=${placeId}&sort=${sort}`)
        .then(r=>r.text())
        .then(html=>{ container.innerHTML=html; container.style.opacity='1'; })
        .catch(()=>{ container.style.opacity='1'; });
}
</script>

</body>
</html>
<?php include 'footer.php'; ?>