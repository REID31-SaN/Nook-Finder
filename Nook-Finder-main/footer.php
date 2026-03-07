<footer>
    <div class="footer-container">

        <div class="footer-nav">
            <a href="discover.php">Discover</a>
            <a href="map.php">Map</a>
            <a href="about.php">About</a>
        </div>

        <div class="footer-logo">
            <img src="images/NookFinder Footer.png" alt="Nook Finder Logo">
        </div>

    </div>

    <div class="footer-bottom">
        Copyright &copy; NookFinder. All rights are reserved.
    </div> 
</footer>

<!-- ================================== TEST MAP ================================== -->
<script>
    //Draggable, Scroll to Zoom, Double click to zoom functions on map
    var map = L.map('map', {
    dragging: true,
    scrollWheelZoom: true,
    doubleClickZoom: true

    // Map centered near HAU
    }).setView([15.133270, 120.591433], 15);

    // Load OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Static markers (Temporary for testing - will be replaced by dynamic markers from database)
    //25-2 Update: Walang gumagana, as in
    L.marker([15.1332, 120.5914]).addTo(map)
        .bindPopup("<b>Co.Create</b><br>0.8 km away");

    L.marker([15.1332, 120.5914]).addTo(map)
        .bindPopup("<b>Cush Lounge</b><br>1.0 km away");

    L.marker([15.1332, 120.5914]).addTo(map)
        .bindPopup("<b>Vessel Coworking Space</b><br>1.6 km away");

    L.marker([15.133270, 120.591433]).addTo(map)
        .bindPopup("<b>Kuwento Cafe</b><br>1.2 km away");

// When user clicks anywhere on the map
map.on('click', function(e) {

    // e.latlng contains the coordinates of where the user clicked
    var clickedLat = e.latlng.lat;
    var clickedLng = e.latlng.lng;

    // Create a new marker at the clicked location
    var newMarker = L.marker([clickedLat, clickedLng], {
        draggable: true   // Allows user to drag the marker
    }).addTo(map);

    // Add popup to the new marker
    newMarker.bindPopup(
        "<b>New Pin</b><br>" +
        "Latitude: " + clickedLat.toFixed(5) + "<br>" +
        "Longitude: " + clickedLng.toFixed(5) + "<br><br>" +
        "You can drag this pin!"
    ).openPopup();

});
</script>
<!-- ================================== END OF TEST MAP ================================== -->
</body>
</html>
