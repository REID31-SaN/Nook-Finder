<?php include 'header.php'; ?>

<main>
    <h1>Find a spot</h1>
    <p>Find your focus.</p>
</main>


<!-- Login and Logout message -->
<?php
if (isset($_GET['login']) && $_GET['login'] == 'success') {
    echo '<p style="color: green; text-align: center;">Login successful! Welcome back! (,,>ヮ<,,)</p>';
}
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    echo '<p style="color: red; text-align: center;">You have been logged out. ヾ(˃ᴗ˂)◞ </p>';
}
?>
<!-- end of Login and Logout message -->

<section class="top-three">
    <div class="top-text">
        <h2>Top 3 Places This Week</h2>
        <p>Student favorites based on recent activity.</p>
    </div>
    <div class="top-images">
        <div class="place-card">
            <img src="images/OFTR.jpg" alt="oFTr">
            <div class="place-name">oFTr</div>
        </div>
        <div class="place-card">
            <img src="images/aclib.jpg" alt="Angeles City Library">
            <div class="place-name">Angeles City Library</div>
        </div>
        <div class="place-card">
            <img src="images/brudr.jpg" alt="BRÜDR">
            <div class="place-name">BRÜDR</div>
        </div>
    </div>
</section>

<section class="discover">
    <h2 class="discover-title">Discover</h2>
    <div class="discover-cards">
        <a href="cafe_window.php?cafe=Co.Create&img=images/Cocreate.png" class="place-card">
            <img src="images/CoCreate.PNG" alt="Co.Create">
            <div class="place-name">Co.Create</div>
        </a>
        <a href="cafe_window.php?cafe=Cush Lounge&img=images/cush.jpg" class="place-card">
            <img src="images/Cush.jpg" alt="Cush Lounge">
            <div class="place-name">Cush Lounge</div>
        </a>
        <a href="cafe_window.php?cafe=Vessel Coworking Space&img=images/vessel.jpg" class="place-card">
            <img src="images/Vessel.jpg" alt="Vessel Coworking space">
            <div class="place-name">Vessel Coworking space</div>
        </a>
        <a href="cafe_window.php?cafe=Kuwento Cafe&img=images/kwento.jpg" class="place-card">
            <img src="images/kwento.jpg" alt="Kuwento Cafe">
            <div class="place-name">Kuwento Cafe</div>
        </a>
    </div>
</section>

<?php include 'footer.php'; ?>


