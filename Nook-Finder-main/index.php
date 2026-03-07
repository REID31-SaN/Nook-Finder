<?php include_once 'header.php'; ?>

<main class="hero hero-home">
    <h1>Find a spot</h1>
    <p>Find your focus.</p>
</main>

<section class="top-three">
    <div class="top-text">
        <h2>Top 3 Places This Week</h2>
        <p>Student favorites based on recent activity.</p>
    </div>
    <div class="top-images">
        <div class="place-card">
            <a href="cafe_window.php?cafe=oFTr&img=images/OFTR.jpg" style="display:block;"><img src="images/OFTR.jpg" alt="oFTr"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=oFTr&img=images/OFTR.jpg" style="text-decoration:none; color:inherit; flex-grow:1;"><span>oFTr</span></a>
                <?= renderHeartButton("oFTr", "images/OFTR.jpg") ?>
            </div>
        </div>
        <div class="place-card">
            <a href="cafe_window.php?cafe=Angeles City Library&img=images/ACLib.jpg" style="display:block;"><img src="images/ACLib.jpg" alt="Angeles City Library"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Angeles City Library&img=images/ACLib.jpg" style="text-decoration:none; color:inherit; flex-grow:1;"><span>Angeles City Library</span></a>
                <?= renderHeartButton("Angeles City Library", "images/ACLib.jpg") ?>
            </div>
        </div>
        <div class="place-card">
            <a href="cafe_window.php?cafe=BRUDR&img=images/BRUDR.jpg" style="display:block;"><img src="images/BRUDR.jpg" alt="BRÜDR"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=BRUDR&img=images/BRUDR.jpg" style="text-decoration:none; color:inherit; flex-grow:1;"><span>BRÜDR</span></a>
                <?= renderHeartButton("BRUDR", "images/BRUDR.jpg") ?>
            </div>
        </div>
    </div>
</section>

<section class="discover">
    <h2 class="discover-title">Discover</h2>
    <div class="discover-cards">
        <div class="place-card">
            <a href="cafe_window.php?cafe=Co.Create&img=images/CoCreate.PNG" style="display:block;"><img src="images/CoCreate.PNG" alt="Co.Create"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Co.Create&img=images/CoCreate.PNG" style="text-decoration:none; color:inherit; flex-grow:1;"><span>Co.Create</span></a>
                <?= renderHeartButton("Co.Create", "images/CoCreate.PNG") ?>
            </div>
        </div>
        <div class="place-card">
            <a href="cafe_window.php?cafe=Cush Lounge&img=images/Cush.jpg" style="display:block;"><img src="images/Cush.jpg" alt="Cush Lounge"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Cush Lounge&img=images/Cush.jpg" style="text-decoration:none; color:inherit; flex-grow:1;"><span>Cush Lounge</span></a>
                <?= renderHeartButton("Cush Lounge", "images/Cush.jpg") ?>
            </div>
        </div>
        <div class="place-card">
            <a href="cafe_window.php?cafe=Vessel Coworking Space&img=images/Vessel.jpg" style="display:block;"><img src="images/Vessel.jpg" alt="Vessel Coworking space"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Vessel Coworking Space&img=images/Vessel.jpg" style="text-decoration:none; color:inherit; flex-grow:1;"><span>Vessel Coworking</span></a>
                <?= renderHeartButton("Vessel Coworking Space", "images/Vessel.jpg") ?>
            </div>
        </div>
        <div class="place-card">
            <a href="cafe_window.php?cafe=Kuwento Cafe&img=images/kwento.jpg" style="display:block;"><img src="images/kwento.jpg" alt="Kuwento Cafe"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Kuwento Cafe&img=images/kwento.jpg" style="text-decoration:none; color:inherit; flex-grow:1;"><span>Kuwento Cafe</span></a>
                <?= renderHeartButton("Kuwento Cafe", "images/kwento.jpg") ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>