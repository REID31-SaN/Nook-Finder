<?php include_once 'header.php'; ?>

<main class="map-wrapper">
    <div id="map"></div>

    <div class="map-sidebar">
        <h1 class="map-sidebar-text">ACTIONS</h1>

        <!-- Search box - searches places from the database only -->
        <div style="position: relative; width: 100%;">
            <input type="text" id="search-box" placeholder="Search places..." class="search-box"
                onkeydown="if(event.key === 'Enter') searchPlace()"
                style="width: 100%; box-sizing: border-box;">
            <div id="suggestions-box" style="
                display: none;
                position: absolute;
                top: 100%; left: 0; right: 0;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 9999;
                max-height: 260px;
                overflow-y: auto;
            "></div>
        </div>

        <div class="sidebar-buttons">
            <a class="sidebar-btn" href="map.php">PIN A PLACE</a>
            <a class="sidebar-btn" href="map.php">APPLY FILTERS</a>
            <a class="sidebar-btn" href="favorites.php">FAVOURITES</a>
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
        
        <div class="place-card">
            <a href="cafe_window.php?cafe=Co.Create&img=images/CoCreate.PNG" style="text-decoration: none; color: inherit; display: block;">
                <img src="images/CoCreate.PNG" alt="Co.Create">
                <div class="place-name">
                    <div>
                        Co.Create
                        <div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">
                            📍 0.8 km away
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="place-card">
            <a href="cafe_window.php?cafe=Cush Lounge&img=images/Cush.jpg" style="text-decoration: none; color: inherit; display: block;">
                <img src="images/Cush.jpg" alt="Cush Lounge">
                <div class="place-name">
                    <div>
                        Cush Lounge
                        <div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">
                            📍 1.0 km away
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="place-card">
            <a href="cafe_window.php?cafe=Vessel Coworking Space&img=images/Vessel.jpg" style="text-decoration: none; color: inherit; display: block;">
                <img src="images/Vessel.jpg" alt="Vessel Coworking">
                <div class="place-name">
                    <div>
                        Vessel Coworking
                        <div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">
                            📍 1.6 km away
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="place-card">
            <a href="cafe_window.php?cafe=Kuwento Cafe&img=images/kwento.jpg" style="text-decoration: none; color: inherit; display: block;">
                <img src="images/kwento.jpg" alt="Kuwento Cafe">
                <div class="place-name">
                    <div>
                        Kuwento Cafe
                        <div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">
                            📍 1.2 km away
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>
</section>

<?php include 'footer.php'; ?>

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

    // Red marker icon for HAU center point
    var redIcon = new L.Icon({
        // Borrowed from: https://github.com/pointhi/leaflet-color-markers by Pointhi (Thomas Pointhuber)
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        shadowUrl: 'https://unpkg.com/leaflet/dist/images/marker-shadow.png',
        iconSize: [30, 46],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Drop the red HAU marker at the center
    L.marker([15.13324, 120.59063], { icon: redIcon }).addTo(map)
        .bindPopup('<b>Holy Angel University</b><br>Center point');

    // Load all places from the database and show them on the map on page load
    fetch('search_places.php?q=')
    .then(function(response) { return response.json(); })
    .then(function(places) {
        for (var i = 0; i < places.length; i++) {
            var place = places[i];
            var lat = parseFloat(place.latitude);
            var lng = parseFloat(place.longitude);

            // Skip places with no coordinates yet
            if (lat === 0 && lng === 0) continue;

            L.marker([lat, lng]).addTo(map)
                .bindPopup('<b>' + place.name + '</b><br>' + place.location);
        }
    });


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

    // ================================== SEARCH ==================================

    // Keeps track of search result markers so we can remove them on the next search
    var searchMarkers = [];

    function searchPlace() {
        var query = document.getElementById('search-box').value.trim();

        // Stop if search box is empty
        if (query === '') return;

        // Remove markers from the previous search
        for (var i = 0; i < searchMarkers.length; i++) {
            map.removeLayer(searchMarkers[i]);
        }
        searchMarkers = [];

        // Send the query to search_places.php which searches the database
        fetch('search_places.php?q=' + encodeURIComponent(query))
        .then(function(response) {
            return response.json();
        })
        .then(function(results) {

            var suggestionsBox = document.getElementById('suggestions-box');
            suggestionsBox.innerHTML = '';

            // If nothing was found, show a message in the dropdown
            if (results.length === 0) {
                suggestionsBox.innerHTML = '<div style="padding: 12px; color: #888;">No places found.</div>';
                suggestionsBox.style.display = 'block';
                return;
            }

            // Loop through each result from the database
            for (var i = 0; i < results.length; i++) {
                var place = results[i];
                var lat = parseFloat(place.latitude);
                var lng = parseFloat(place.longitude);

                // Drop a marker on the map for this place
                var marker = L.marker([lat, lng]).addTo(map);
                marker.bindPopup('<b>' + place.name + '</b><br>' + place.location);
                searchMarkers.push(marker);

                // Add a circle around the search result to make it stand out
                var pulse = L.circle([lat, lng], {
                    color: '#e74c3c',
                    fillColor: '#e74c3c',
                    fillOpacity: 0.15,
                    radius: 80
                }).addTo(map);
                searchMarkers.push(pulse);

                // Add this place as a row in the suggestions dropdown
                var row = document.createElement('div');
                row.style.padding = '10px 14px';
                row.style.cursor = 'pointer';
                row.style.borderBottom = '1px solid #f0f0f0';
                row.innerHTML = '<b>' + place.name + '</b><br><small>' + place.location + '</small>';

                // When user clicks a suggestion, pan to that marker and open its popup
                row.onclick = (function(m, lt, ln) {
                    return function() {
                        map.setView([lt, ln], 17);
                        m.openPopup();
                        suggestionsBox.style.display = 'none';
                    };
                })(marker, lat, lng);

                row.onmouseenter = function() { this.style.background = '#f5f5f5'; };
                row.onmouseleave = function() { this.style.background = 'white'; };

                suggestionsBox.appendChild(row);
            }

            // If only one result, zoom straight to it
            if (results.length === 1) {
                map.setView([parseFloat(results[0].latitude), parseFloat(results[0].longitude)], 17);
                searchMarkers[0].openPopup();
            }

            suggestionsBox.style.display = 'block';
        })
        .catch(function() {
            alert('Search failed. Check your connection.');
        });
    }

    // Close the suggestions dropdown when clicking anywhere else on the page
    document.addEventListener('click', function(e) {
        var box = document.getElementById('suggestions-box');
        var input = document.getElementById('search-box');
        if (e.target !== input && !box.contains(e.target)) {
            box.style.display = 'none';
        }
    });

    // ================================== END OF SEARCH ==================================

</script>
<!-- ================================== END OF MAP ================================== -->
</body></html>
