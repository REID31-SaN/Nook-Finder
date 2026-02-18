<?php include 'header.php'; ?>

<?php
$cafeName = $_GET['cafe'] ?? 'Cafe Name';
$cafeImage = $_GET['img'] ?? 'images/default.jpg';

// Description of clicked discover tab
$descriptions = [
    "Co.Create" => "A modern collaborative coworking space designed for productivity and creativity.",
    "Cush Lounge" => "Is a cozy, premium co-working and relaxation spot for students and remote workers in Angeles City (MC Place)",
    "Vessel Coworking Space" => "A professional coworking environment perfect for startups and remote workers.",
    "Kuwento Cafe" => "Cozy workspace vibes Matcha Bar Supporting local farmers, and local roasters, a warm cafe space where stories are shared over great coffee."
];

// Extra information and features of cafes
$cafeInfos = [
    "Co.Create" => [
        "Location" => "Unit 101 Mission Plaza Bldg, MacArthur Hwy, Angeles, 2009 Pampanga",
        "Hours" => "8 AM - 10 PM (Mon-Sat), 10:30AM-7:30PM (Sunday)",
        "Features" => "Wifi - Available, Power Outlets - Available, Renting - Available, Air-Conditioned"
    ],
    "Cush Lounge" => [
        "Location" => " 2F MC Place, Brgy. Santo Cristo, Angeles City",
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

//Fallback incase codes not working and description is unavailable
$cafeDescription = $descriptions[$cafeName] ?? "No descriptions yet...";
$info = $cafeInfos[$cafeName] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= ucfirst($cafeName); ?></title>
    <link rel="stylesheet" href="cafe-window.css">
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
        <h2>About <?= htmlspecialchars($cafeName); ?></h2>
        <p><?= htmlspecialchars($cafeDescription); ?></p>

        <div class="info-grid">
            <?php foreach($info as $title => $value): ?>
                <div class="info-box">
                    <h4><?php echo htmlspecialchars($title); ?></h4>
                    <p><?php 
                        //Makes the commas into breaks for info box
                        if(in_array($title, ['Hours', 'Features'])){
                            echo nl2br(str_replace(',', "<br>", htmlspecialchars($value)));
                        } else {
                            echo htmlspecialchars($value);
                        }
                    ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>

<?php include 'footer.php'; ?>