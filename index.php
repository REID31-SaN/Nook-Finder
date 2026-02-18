<?php include 'header.php'; ?>

<main>
    <h1>Find a spot</h1>
    <p>Find your focus.</p>
</main>


<!-- =login func====================================== -->
<?php
if (isset($_GET['login']) && $_GET['login'] == 'success') {
    echo '<p style="color: green;">Login successful! Welcome back.</p>';
}
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    echo '<p style="color: blue;">You have been logged out.</p>';
}
?>
<!-- =========================================== -->

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
        <div class="place-card">
            <img src="images/CoCreate.PNG" alt="Co.Create">
            <div class="place-name">Co.Create</div>
        </div>
        <div class="place-card">
            <img src="images/Cush.jpg" alt="Cush Lounge">
            <div class="place-name">Cush Lounge</div>
        </div>
        <div class="place-card">
            <img src="images/Vessel.jpg" alt="Vessel Coworking space">
            <div class="place-name">Vessel Coworking space</div>
        </div>
        <div class="place-card">
            <img src="images/kwento.jpg" alt="Kuwento Cafe">
            <div class="place-name">Kuwento Cafe</div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
