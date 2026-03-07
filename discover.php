<?php include_once 'header.php'; ?>

<section class="discover" style="margin-top: 20px; padding-top: 20px; min-height: 80vh;">
    
    <div class="top-text" style="width: 100%; text-align: center; margin-bottom: 40px; padding: 0;">
        <h2>Discover</h2>
        <p>Explore the best student-friendly nooks, cafes, and study hubs near Holy Angel University.</p>
    </div>

    <div class="discover-cards">

        <div class="place-card">
            <a href="cafe_window.php?cafe=Kuwento Cafe&img=images/kwento.jpg" style="display:block;"><img src="images/kwento.jpg" alt="Kuwento Cafe"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Kuwento Cafe&img=images/kwento.jpg" style="text-decoration:none; color:inherit; flex-grow:1;">
                    <div>Kuwento Cafe<div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">📍 1.3 km away</div></div>
                </a>
                <?= renderHeartButton("Kuwento Cafe", "images/kwento.jpg") ?>
            </div>
        </div>

        <div class="place-card">
            <a href="cafe_window.php?cafe=Cush Lounge&img=images/Cush.jpg" style="display:block;"><img src="images/Cush.jpg" alt="Cush Lounge"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Cush Lounge&img=images/Cush.jpg" style="text-decoration:none; color:inherit; flex-grow:1;">
                    <div>Cush Lounge<div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">📍 1.4 km away</div></div>
                </a>
                <?= renderHeartButton("Cush Lounge", "images/Cush.jpg") ?>
            </div>
        </div>   

        <div class="place-card">
            <a href="cafe_window.php?cafe=Vessel Coworking Space&img=images/Vessel.jpg" style="display:block;"><img src="images/Vessel.jpg" alt="Vessel Coworking"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Vessel Coworking Space&img=images/Vessel.jpg" style="text-decoration:none; color:inherit; flex-grow:1;">
                    <div>Vessel Coworking<div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">📍 0.55 km away</div></div>
                </a>
                <?= renderHeartButton("Vessel Coworking Space", "images/Vessel.jpg") ?>
            </div>
        </div>

        <div class="place-card">
            <a href="cafe_window.php?cafe=Co.Create&img=images/CoCreate.PNG" style="display:block;"><img src="images/CoCreate.PNG" alt="Co.Create"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Co.Create&img=images/CoCreate.PNG" style="text-decoration:none; color:inherit; flex-grow:1;">
                    <div>Co.Create<div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">📍 0.25 km away</div></div>
                </a>
                <?= renderHeartButton("Co.Create", "images/CoCreate.PNG") ?>
            </div>
        </div>

        <div class="place-card">
            <a href="cafe_window.php?cafe=oFTr&img=images/OFTR.jpg" style="display:block;"><img src="images/OFTR.jpg" alt="oFTr"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=oFTr&img=images/OFTR.jpg" style="text-decoration:none; color:inherit; flex-grow:1;">
                    <div>oFTr<div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">📍 0.25 km away</div></div>
                </a>
                <?= renderHeartButton("oFTr", "images/OFTR.jpg") ?>
            </div>
        </div>

        <div class="place-card">
            <a href="cafe_window.php?cafe=Angeles City Library&img=images/ACLib.jpg" style="display:block;"><img src="images/ACLib.jpg" alt="Angeles City Library"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Angeles City Library&img=images/ACLib.jpg" style="text-decoration:none; color:inherit; flex-grow:1;">
                    <div>Angeles City Lib.<div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">📍 0.75 km away</div></div>
                </a>
                <?= renderHeartButton("Angeles City Library", "images/ACLib.jpg") ?>
            </div>
        </div>

        <div class="place-card">
            <a href="cafe_window.php?cafe=BRUDR&img=images/BRUDR.jpg" style="display:block;"><img src="images/BRUDR.jpg" alt="BRÜDR"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=BRUDR&img=images/BRUDR.jpg" style="text-decoration:none; color:inherit; flex-grow:1;">
                    <div>BRÜDR<div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">📍 0.5 km away</div></div>
                </a>
                <?= renderHeartButton("BRUDR", "images/BRUDR.jpg") ?>
            </div>
        </div>

        <div class="place-card">
            <a href="cafe_window.php?cafe=Arte Cafe&img=images/ARTE.jpg" style="display:block;"><img src="images/ARTE.jpg" alt="Artè Cafè"></a>
            <div class="place-name">
                <a href="cafe_window.php?cafe=Arte Cafe&img=images/ARTE.jpg" style="text-decoration:none; color:inherit; flex-grow:1;">
                    <div>Artè Cafè<div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">📍 1.0 km away</div></div>
                </a>
                <?= renderHeartButton("Arte Cafe", "images/ARTE.jpg") ?>
            </div>
        </div>

        <?php for($i=0; $i<4; $i++): ?>
        <div class="place-card">
            <div style="height: 280px; background-color: #eee; display: flex; align-items: center; justify-content: center; border-radius: 10px; color: #999; font-weight: bold;">
                COMING SOON
            </div>
            <div class="place-name" style="display: block;">
                Future Expansion
                <div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">
                    More nooks being discovered. <br>
                    <span style="font-size: 0.8rem;">--</span>
                </div>
            </div>
        </div>
        <?php endfor; ?>

    </div>
</section>

<?php include 'footer.php'; ?>