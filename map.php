<?php include 'header.php'; ?>

<main class="hero hero-map">
    <div class="map-sidebar">
        <h1 class="map-sidebar-text">ACTIONS</h1>

        <input type="text" placeholder="Search" class="search-box">

        <div class="sidebar-buttons">
            <a class="sidebar-btn" href="map.php">PIN A PLACE</a>
            <a class="sidebar-btn" href="map.php">APPLY FILTERS</a>
            <a class="sidebar-btn" href="map.php">FAVOURITES</a>
        </div>

        <a class="sidebar-btn propose-location" href="map.php">PROPOSE<br>LOCATION</a>

    </div>
</main>

<section class="discover" style="margin-top: 20px; padding-top: 20px; min-height: 80vh;">

    <div class="top-text" style="width: 100%; text-align: center; margin-bottom: 40px;">
        <h2>Locations available near HAU</h2>
        <p>Explore the best student-friendly nooks, cafes, and study hubs near Holy Angel University.</p>
    </div>

    <div class="discover-cards">

        <a href="cafe_window.php?cafe=Co.Create&img=images/CoCreate.PNG" class="place-card">
            <img src="images/CoCreate.PNG" alt="Co.Create">
            <div class="place-name">
                Co.Create
                <div class="distance">📍 0.8 km away</div>
            </div>
        </a>

        <a href="cafe_window.php?cafe=Cush Lounge&img=images/cush.jpg" class="place-card">
            <img src="images/Cush.jpg" alt="Cush Lounge">
            <div class="place-name">
                Cush Lounge
                <div class="distance">📍 1.0 km away</div>
            </div>
        </a>

        <a href="cafe_window.php?cafe=Vessel Coworking Space&img=images/vessel.jpg" class="place-card">
            <img src="images/Vessel.jpg" alt="Vessel Coworking">
            <div class="place-name">
                Vessel Coworking
                <div class="distance">📍 1.6 km away</div>
            </div>
        </a>

        <a href="cafe_window.php?cafe=Kuwento Cafe&img=images/kwento.jpg" class="place-card">
            <img src="images/kwento.jpg" alt="Kuwento Cafe">
            <div class="place-name">
                Kuwento Cafe
                <div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">
                    <span style="font-size: 0.8rem;">📍 1.2 km away</span>
                </div>
            </div>
        </div>
    </a>
    </div>
</section>

<?php include 'footer.php'; ?>

