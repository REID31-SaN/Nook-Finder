<?php 
include_once 'config.php';
include_once 'header.php'; 

date_default_timezone_set('Asia/Manila');

$cafeName = $_GET['cafe'] ?? 'Cafe Name';
$cafeImage = $_GET['img'] ?? 'images/default.jpg';

$descriptions = [
    "Co.Create" => "A modern collaborative coworking space designed for productivity and creativity.",
    "Cush Lounge" => "Is a cozy, premium co-working and relaxation spot for students and remote workers in Angeles City (MC Place)",
    "Vessel Coworking Space" => "A professional coworking environment perfect for startups and remote workers.",
    "Kuwento Cafe" => "Cozy workspace vibes Matcha Bar Supporting local farmers, and local roasters, a warm cafe space where stories are shared over great coffee."
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
    <link rel="stylesheet" href="cafe-window.css">
    <script>
        function toggleBigFavAJAX(btn) {
            const cafeName = btn.getAttribute('data-cafe');
            const cafeImage = btn.getAttribute('data-image');
            const action = btn.getAttribute('data-action');
            
            // Optimistic UI Update
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
    </div>
</div>

</body>
</html>

<?php include 'footer.php'; ?>