<?php
// ═══════════════════════════════════════════════════════
//  index.php  —  Homepage
// ═══════════════════════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) session_start();

include_once 'config.php';

$current_user_id = (int) ($_SESSION['user_id'] ?? 0);

// ── User favourites ───────────────────────────────────────────────────────────
$fav_ids = [];
if ($current_user_id > 0) {
    $fr = $conn->query("SELECT place_id FROM favorites WHERE account_id = $current_user_id");
    while ($r = $fr->fetch_assoc()) $fav_ids[] = (int) $r['place_id'];
}

// ── Top 3: most-favourited approved places (falls back to oldest approved) ───
$top3 = $conn->query("
    SELECT p.id, p.name, p.image,
           COUNT(f.id) AS fav_count
    FROM places p
    LEFT JOIN favorites f ON f.place_id = p.id
    WHERE p.status = 'approved'
      AND p.image IS NOT NULL AND p.image != ''
    GROUP BY p.id
    ORDER BY fav_count DESC, p.created_at ASC
    LIMIT 3
")->fetch_all(MYSQLI_ASSOC);

// ── Discover: 4 random approved places with images (randomises on each page load) ──
$discover_places = $conn->query("
    SELECT id, name, image
    FROM places
    WHERE status = 'approved'
      AND image IS NOT NULL AND image != ''
    ORDER BY RAND()
    LIMIT 4
")->fetch_all(MYSQLI_ASSOC);

// ── Helpers ───────────────────────────────────────────────────────────────────
function cw_link(int $pid, string $name, string $img = ''): string {
    return $pid
        ? 'cafe_window.php?place_id=' . $pid . '&cafe=' . urlencode($name)
        : 'cafe_window.php?cafe=' . urlencode($name) . '&img=' . urlencode($img);
}

function heart_button(int $pid, string $name, string $img, array $fav_ids, int $uid): string {
    if ($uid <= 0) {
        return '<a href="login.php" class="heart-btn" title="Log in to save">🤍</a>';
    }
    $isFav = in_array($pid, $fav_ids);
    return sprintf(
        '<button class="heart-btn %s" data-id="%d" data-name="%s" data-image="%s"
                 onclick="toggleFav(this)" title="%s">%s</button>',
        $isFav ? 'faved' : '',
        $pid,
        htmlspecialchars($name),
        htmlspecialchars($img),
        $isFav ? 'Remove from favorites' : 'Add to favorites',
        $isFav ? '🤎' : '🤍'
    );
}

include_once 'header.php';
?>

<main class="hero hero-home">
    <!-- ADD SUCCESS MESSAGE HERE - RIGHT AFTER MAIN OPENS -->
    <?php if(isset($_GET['account_deleted'])): ?>
        <div id="deletedMessage" style="position: fixed; top: 70px; left: 0; right: 0; background-color: #28a745; color: white; padding: 15px; text-align: center; z-index: 9999; font-weight: bold;">
            ✓ Your account has been permanently deleted. We're sorry to see you go!
        </div>
        <script>
            setTimeout(function() {
                const msg = document.getElementById('deletedMessage');
                if(msg) {
                    msg.style.opacity = '0';
                    msg.style.transition = 'opacity 0.5s ease';
                    setTimeout(function() {
                        msg.remove();
                    }, 500);
                }
                // Remove the parameter from URL without refreshing
                const url = new URL(window.location.href);
                url.searchParams.delete('account_deleted');
                window.history.replaceState({}, document.title, url.pathname);
            }, 5000);
        </script>
    <?php endif; ?>
    
    <h1>Find a spot</h1>
    <p>Find your focus.</p>
</main>

<!-- ── Top 3 most popular ── -->
<section class="top-three">
    <div class="top-text">
        <h2>Developers' Top Picks</h2>
        <p>Personal Favorites of Nook Finder's Creators.</p>
    </div>
    <div class="top-images">
        <?php foreach ($top3 as $place):
            $pid  = (int) $place['id'];
            $img  = $place['image'];
            $link = cw_link($pid, $place['name'], $img);
        ?>
            <div class="place-card">
                <a href="<?= $link ?>" style="display:block;">
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($place['name']) ?>">
                </a>
                <div class="place-name">
                    <a href="<?= $link ?>" style="text-decoration:none; color:inherit; flex-grow:1;">
                        <span><?= htmlspecialchars($place['name']) ?></span>
                    </a>
                </div>
                <div class="card-heart-pin">
                    <?= heart_button($pid, $place['name'], $img, $fav_ids, $current_user_id) ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($top3)): ?>
            <p style="color:#888;">No places available yet.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ── Discover: 4 random places (refreshes on page reload) ── -->
<section class="discover">
    <h2 class="discover-title">Discover</h2>
    <div class="discover-cards">
        <?php foreach ($discover_places as $place):
            $pid  = (int) $place['id'];
            $img  = $place['image'];
            $link = cw_link($pid, $place['name'], $img);
        ?>
            <div class="place-card">
                <a href="<?= $link ?>" style="display:block;">
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($place['name']) ?>">
                </a>
                <div class="place-name">
                    <a href="<?= $link ?>" style="text-decoration:none; color:inherit; flex-grow:1;">
                        <span><?= htmlspecialchars($place['name']) ?></span>
                    </a>
                </div>
                <div class="card-heart-pin">
                    <?= heart_button($pid, $place['name'], $img, $fav_ids, $current_user_id) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
    /* Heart pinned to a fixed spot — bottom-right of card, never moves */
    .card-heart-pin {
        position: absolute;
        bottom: 14px;
        right: 14px;
        z-index: 10;
        pointer-events: all;
    }
    /* Override the base heart-btn style for index cards to match discover frosted style */
    .card-heart-pin .heart-btn {
        background: rgba(255,255,255,0.20);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        cursor: pointer;
        text-shadow: none;
        text-decoration: none;
        color: inherit;
        transition: background 0.2s, transform 0.2s;
        padding: 0;
        margin: 0;
    }
    .card-heart-pin .heart-btn:hover { background: rgba(255,255,255,0.35); transform: scale(1.12); }
    .card-heart-pin .heart-btn.faved { background: rgba(109,62,28,0.55); }
</style>

<!-- ── Welcome ── -->
<?php if (isset($_GET['login']) && $_GET['login'] === 'success' && isset($_SESSION['username'])): ?>
    <div id="welcome-toast">
        👋 Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!
    </div>
    <script>
        const toast = document.getElementById('welcome-toast');
        if (toast) {
            setTimeout(() => toast.classList.add('hide'), 3000);
            setTimeout(() => toast.remove(), 3600);
        }
    </script>
<?php endif; ?>

<script>
function toggleFav(btn) {
    const isFaved = btn.classList.contains('faved');
    const action  = isFaved ? 'remove' : 'add';

    btn.innerHTML = action === 'add' ? '🤎' : '🤍';
    btn.classList.toggle('faved', action === 'add');
    btn.title = action === 'add' ? 'Remove from favorites' : 'Add to favorites';

    fetch('toggle_favorite.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            cafe_name:  btn.getAttribute('data-name'),
            cafe_image: btn.getAttribute('data-image'),
            action:     action
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.status !== 'success') {
            btn.innerHTML = isFaved ? '🤎' : '🤍';
            btn.classList.toggle('faved', isFaved);
        }
    })
    .catch(() => {
        btn.innerHTML = isFaved ? '🤎' : '🤍';
        btn.classList.toggle('faved', isFaved);
    });
}
</script>

<?php include 'footer.php'; ?>