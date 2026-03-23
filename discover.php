<?php
// ═══════════════════════════════════════════════════════
// discover.php — Browse study spots (8 random per visit)
// ═══════════════════════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) session_start();

include_once 'config.php';

$current_user_id = (int) ($_SESSION['user_id'] ?? 0);

// Fetch user's favourite place IDs
$fav_ids = [];
if ($current_user_id > 0) {
    $fr = $conn->query("SELECT place_id FROM favorites WHERE account_id = $current_user_id");
    while ($r = $fr->fetch_assoc()) $fav_ids[] = (int) $r['place_id'];
}

// All approved places
$all_places = $conn->query("
    SELECT id, name, image, distance_km
    FROM places
    WHERE status = 'approved'
    ORDER BY id ASC
")->fetch_all(MYSQLI_ASSOC);

// Seen tracking via session
$all_ids = array_column($all_places, 'id');
if (!isset($_SESSION['seen_place_ids'])) $_SESSION['seen_place_ids'] = [];

$seen_ids = array_values(array_intersect($_SESSION['seen_place_ids'], $all_ids));
if (count($seen_ids) >= count($all_ids)) {
    $seen_ids = [];
    $_SESSION['seen_place_ids'] = [];
}

// Pick 8 random unseen
$unseen = array_values(array_filter($all_places, fn($p) => !in_array($p['id'], $seen_ids)));
shuffle($unseen);
$show_places = array_slice($unseen, 0, 8);

foreach ($show_places as $p) {
    if (!in_array($p['id'], $_SESSION['seen_place_ids'])) {
        $_SESSION['seen_place_ids'][] = $p['id'];
    }
}

$seen_places = array_values(array_filter($all_places, fn($p) => in_array($p['id'], $seen_ids)));

// Card renderer
function render_discover_card(array $place, array $fav_ids, int $uid): string {
    $pid      = (int) $place['id'];
    $name     = htmlspecialchars($place['name']);
    $img      = $place['image'];
    $hasImg   = !empty($img) && file_exists($img);
    $imgAttr  = htmlspecialchars($img ?? '');
    $isFav    = in_array($pid, $fav_ids);
    $dist     = (float) $place['distance_km'];
    $distText = $dist > 0 ? number_format($dist, 2) . ' km away' : 'Nearby';
    $link     = 'cafe_window.php?place_id=' . $pid . '&cafe=' . urlencode($place['name']);

    $heartBtn = $uid > 0
        ? sprintf(
            '<button class="heart-btn %s" data-id="%d" data-name="%s" data-image="%s" onclick="event.stopPropagation(); toggleFav(this)" title="%s">%s</button>',
            $isFav ? 'faved' : '',
            $pid,
            $name,
            $imgAttr,
            $isFav ? 'Remove from favorites' : 'Add to favorites',
            $isFav ? '🤎' : '🤍'
        )
        : '<a href="login.php" class="heart-btn" onclick="event.stopPropagation()" title="Log in to save">🤍</a>';

    $visual = $hasImg
        ? sprintf('<img src="%s" alt="%s" class="card-img">', $imgAttr, $name)
        : '<div class="card-coming-soon"><span>Coming Soon</span></div>';

    $futureBar = $hasImg ? '' : '
        <div class="card-future-bar">
            <div class="card-future-title">Future Expansion</div>
            <div class="card-future-sub">More nooks being discovered.</div>
        </div>';

    $overlayClass = $hasImg ? 'place-name' : 'place-name place-name--dark';

    return sprintf(
        '
        <div class="place-card%s" id="card-%d" onclick="window.location=\'%s\'">
            %s
            <div class="%s">
                <div class="place-name-row"><span class="place-name-text">%s</span></div>
                <div class="place-distance">📍 %s</div>
            </div>
            <div class="card-heart-pin">%s</div>
            %s
        </div>',
        $hasImg ? '' : ' card-no-img',
        $pid,
        $link,
        $visual,
        $overlayClass,
        $name,
        $distText,
        $heartBtn,
        $futureBar
    );
}

include_once 'header.php';
?>

<style>
    html, body {
        height: 100%;
        margin: 0;
        background: #ffffff;
    }

    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    main {
        flex: 1;
        display: block;
        background: #ffffff;
    }

    .discover {
        padding-bottom: 0;
        background: #ffffff;
    }

    .discover-cards { align-items: start; }
    .place-card { cursor: pointer; overflow: hidden; border-radius: 10px; box-shadow: 0 4px 14px rgba(0,0,0,0.12); background: #1a1a1a; position: relative; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .place-card:hover { transform: translateY(-5px); box-shadow: 0 10px 28px rgba(0,0,0,0.22); }
    .card-img { width: 100%; height: 280px; object-fit: cover; display: block; }
    .card-coming-soon { width: 100%; height: 280px; background: #e8e8e8; display: flex; align-items: center; justify-content: center; }
    .card-coming-soon span { font-size: 0.88rem; font-weight: 800; letter-spacing: 0.2em; color: #b8b8b8; text-transform: uppercase; }
    .place-name { position: absolute; bottom: 0; left: 0; width: 100%; padding: 40px 14px 14px; background: linear-gradient(to top, rgba(0,0,0,0.85), rgba(0,0,0,0.4) 55%, transparent); color: white; display: flex; flex-direction: column; gap: 5px; border-radius: 0 0 10px 10px; pointer-events: none; }
    .place-name--dark { bottom: 52px; border-radius: 0; background: linear-gradient(to top, rgba(30,20,10,0.88), rgba(30,20,10,0.5) 60%, transparent); }
    .place-name-row { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
    .place-name-text { font-size: 1.05rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; }
    .place-distance { font-size: 0.8rem; color: rgba(255,255,255,0.85); display: flex; align-items: center; gap: 3px; }
    .heart-btn { pointer-events: all; background: rgba(255,255,255,0.20); backdrop-filter: blur(4px); border: none; border-radius: 50%; width: 36px; height: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.15rem; text-decoration: none; color: inherit; transition: background 0.2s, transform 0.2s; }
    .heart-btn:hover { background: rgba(255,255,255,0.35); transform: scale(1.12); }
    .heart-btn.faved { background: rgba(109,62,28,0.55); }

    .card-heart-pin { position: absolute; bottom: 14px; right: 14px; z-index: 10; pointer-events: all; }
    .card-future-bar { background: #2b2b2b; padding: 11px 14px 13px; }
    .card-future-title { font-size: 0.88rem; font-weight: 700; color: #fff; margin-bottom: 2px; }
    .card-future-sub { font-size: 0.74rem; color: rgba(255,255,255,0.45); }
    .discover-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 14px; }
    .discover-title { margin-bottom: 0 !important; }
    .discover-controls { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .btn-refresh { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; background: #6D3E1C; color: #FFF3DB; border: none; border-radius: 8px; font-family: inherit; font-size: 0.88rem; font-weight: 700; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.2s; }
    .btn-refresh:hover { background: #522e15; transform: translateY(-1px); }
    .btn-refresh .spin { display: inline-block; transition: transform 0.4s; }
    .btn-refresh:hover .spin { transform: rotate(180deg); }
    .seen-toggle-wrap { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; color: #6D3E1C; cursor: pointer; user-select: none; }
    .seen-toggle-wrap input { display: none; }
    .toggle-track { width: 42px; height: 22px; background: #d0c4b4; border-radius: 20px; position: relative; transition: background 0.25s; flex-shrink: 0; }
    .toggle-track::after { content: ''; position: absolute; top: 3px; left: 3px; width: 16px; height: 16px; background: white; border-radius: 50%; transition: transform 0.25s; box-shadow: 0 1px 4px rgba(0,0,0,0.2); }
    .seen-toggle-wrap input:checked ~ .toggle-track { background: #6D3E1C; }
    .seen-toggle-wrap input:checked ~ .toggle-track::after { transform: translateX(20px); }
    #seen-section { display: none; margin-top: 50px; }
    #seen-section.visible { display: block; }
    .seen-section-title { font-size: 1.1rem; font-weight: 700; color: #6D3E1C; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .seen-section-title::after { content: ''; flex: 1; height: 2px; background: #faeac8; }
    .seen-cards-dim .place-card { opacity: 0.72; filter: saturate(0.7); }
    .seen-cards-dim .place-card:hover { opacity: 1; filter: saturate(1); }

    @media (max-width: 768px) {
        .card-img, .card-coming-soon { height: 200px; }
        .discover-bar { flex-direction: column; align-items: flex-start; }
    }
</style>

<main>
    <div class="discover">
        <div class="discover-bar">
            <h2 class="discover-title">Discover Study Spots</h2>
            <div class="discover-controls">
                <?php if (!empty($seen_places)): ?>
                    <label class="seen-toggle-wrap">
                        <input type="checkbox" onchange="toggleSeen(this)">
                        <span class="toggle-track"></span>
                        Show seen (<?= count($seen_places) ?>)
                    </label>
                <?php endif; ?>
                <a href="discover.php" class="btn-refresh"><span class="spin">↻</span> Shuffle</a>
            </div>
        </div>

        <div class="discover-cards">
            <?php if (empty($show_places)): ?>
                <p style="grid-column:1/-1;text-align:center;color:#888;padding:40px 0;">No spots available yet — check back soon!</p>
            <?php else: ?>
                <?php foreach ($show_places as $place): ?>
                    <?= render_discover_card($place, $fav_ids, $current_user_id) ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($seen_places)): ?>
            <div id="seen-section">
                <div class="seen-section-title">Previously seen</div>
                <div class="discover-cards seen-cards-dim">
                    <?php foreach ($seen_places as $place): ?>
                        <?= render_discover_card($place, $fav_ids, $current_user_id) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function toggleSeen(checkbox) {
    document.getElementById('seen-section')?.classList.toggle('visible', checkbox.checked);
}

function toggleFav(btn) {
    const isFaved = btn.classList.contains('faved');
    const action = isFaved ? 'remove' : 'add';

    btn.innerHTML = action === 'add' ? '🤎' : '🤍';
    btn.classList.toggle('faved', action === 'add');
    btn.title = action === 'add' ? 'Remove from favorites' : 'Add to favorites';

    fetch('toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            cafe_name: btn.getAttribute('data-name'),
            cafe_image: btn.getAttribute('data-image'),
            action: action
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