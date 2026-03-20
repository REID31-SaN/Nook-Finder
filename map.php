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
            <!-- PIN A PLACE - activates pin mode for saving a private spot -->
            <a class="sidebar-btn" href="#" onclick="activatePinAPlace(event)">PIN A PLACE</a>
            <button class="sidebar-btn" type="button" onclick="toggleFilterBox()">APPLY FILTERS</button>
            <div id="modal-dimmer" class="modal-dimmer" onclick="toggleFilterBox()"></div>
            <div id="filter-popout" class="filter-popout">
                <button type="button" class="close-filter" onclick="toggleFilterBox()">&times;</button>
                <h2 class="filter-title">FILTERS</h2>
                <form id="filter-form">
                    <label class="filter-item"><input type="checkbox" name="amenity" value="wifi"> <span>Wifi</span></label>
                    <label class="filter-item"><input type="checkbox" name="amenity" value="outlets"> <span>Power Outlets</span></label>
                    <label class="filter-item"><input type="checkbox" name="amenity" value="aircon"> <span>Aircon</span></label>
                    <label class="filter-item"><input type="checkbox" name="amenity" value="parking"> <span>Parking</span></label>
                    <button type="button" class="apply-filter-confirm" onclick="applyFilters()">APPLY</button>
                    <button type="button" class="clear-filter-confirm" onclick="clearFilters()">CLEAR</button>
                </form>
            </div>
            <a class="sidebar-btn" href="favorites.php">FAVOURITES</a>
        </div>

        <a class="sidebar-btn propose-location" onclick="openProposalModal()">PROPOSE<br>LOCATION</a>
    </div>

    <!-- ===== PIN A PLACE MODAL ===== -->
    <div id="pin-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:white; border-radius:12px; padding:30px; width:90%; max-width:480px; position:relative;">
            <button onclick="closePinModal()" style="position:absolute; top:12px; right:16px; background:none; border:none; font-size:1.4rem; cursor:pointer;">&times;</button>
            <h2 style="margin-bottom:6px;">Pin a Place</h2>
            <p style="font-size:0.85rem; color:#888; margin-bottom:20px;">Save a private spot only you can see.</p>

            <!-- Shows the pin status - changes once user clicks the map -->
            <div id="pin-place-status" style="background:#f0f4ff; border:1px solid #c0cdff; border-radius:6px; padding:10px 14px; font-size:0.85rem; margin-bottom:16px; color:#3a5bd9;">
                📍 No pin placed yet — <button onclick="startPinPlacement()" style="background:#6D3E1C; color:white; border:none; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:0.85rem;">Click here to place pin</button>
            </div>

            <!-- Hidden fields to store the clicked coordinates -->
            <input type="hidden" id="pin-lat">
            <input type="hidden" id="pin-lng">

            <div style="display:flex; flex-direction:column; gap:12px;">
                <input type="text" id="pin-name" placeholder="Name your spot *" style="padding:10px 14px; border:1px solid #ccc; border-radius:8px; font-size:0.95rem;">
                <textarea id="pin-note" placeholder="Add a note (optional)" rows="3" style="padding:10px 14px; border:1px solid #ccc; border-radius:8px; font-size:0.95rem; resize:vertical;"></textarea>
                <button onclick="savePin()" style="background:#6D3E1C; color:white; border:none; padding:12px; border-radius:8px; font-size:1rem; cursor:pointer; font-weight:600;">SAVE PIN</button>
            </div>

            <p id="pin-save-msg" style="font-size:0.8rem; margin-top:8px; text-align:center;"></p>
        </div>
    </div>

    <!-- ===== PROPOSE LOCATION MODAL ===== -->
    <div id="propose-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:white; border-radius:12px; padding:30px; width:90%; max-width:480px; position:relative;">
            <button onclick="closeProposalModal()" style="position:absolute; top:12px; right:16px; background:none; border:none; font-size:1.4rem; cursor:pointer;">&times;</button>
            <h2 style="margin-bottom:6px;">Propose a Location</h2>
            <p style="font-size:0.85rem; color:#888; margin-bottom:20px;">Place a pin on the map, then fill in the details below.</p>

            <div id="pin-status" style="background:#f0f4ff; border:1px solid #c0cdff; border-radius:6px; padding:10px 14px; font-size:0.85rem; margin-bottom:16px; color:#3a5bd9;">
                📍 No pin placed yet — <button onclick="activatePinMode()" style="background:#062b53; color:white; border:none; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:0.85rem;">Click here to place pin</button>
            </div>

            <input type="hidden" id="propose-lat">
            <input type="hidden" id="propose-lng">

            <div style="display:flex; flex-direction:column; gap:12px;">
                <input type="text" id="propose-name" placeholder="Place Name *" style="padding:10px 14px; border:1px solid #ccc; border-radius:8px; font-size:0.95rem;">
                <input type="text" id="propose-location" placeholder="Address / Area *" style="padding:10px 14px; border:1px solid #ccc; border-radius:8px; font-size:0.95rem;">
                <textarea id="propose-description" placeholder="Description *" rows="3" style="padding:10px 14px; border:1px solid #ccc; border-radius:8px; font-size:0.95rem; resize:vertical;"></textarea>

                <div style="display:flex; gap:16px; flex-wrap:wrap;">
                    <label><input type="checkbox" id="p-wifi"> Wifi</label>
                    <label><input type="checkbox" id="p-outlet"> Power Outlets</label>
                    <label><input type="checkbox" id="p-aircon"> Aircon</label>
                    <label><input type="checkbox" id="p-parking"> Parking</label>
                </div>

                <button onclick="submitProposal()" style="background:#062b53; color:white; border:none; padding:12px; border-radius:8px; font-size:1rem; cursor:pointer; font-weight:600;">SUBMIT PROPOSAL</button>
            </div>
        </div>
    </div>
</main>

<!-- ===== DISCOVER SECTION - dynamically loaded from DB (Doc 2 improvement) ===== -->
<section class="discover" style="margin-top: 20px; padding-top: 20px; min-height: 80vh;">
    <div class="top-text" style="width: 100%; text-align: center; margin-bottom: 40px;">
        <h2>Locations available near HAU</h2>
        <p>Explore the best student-friendly nooks, cafes, and study hubs near Holy Angel University.</p>
    </div>

    <div class="discover-cards">
        <?php
        include_once 'config.php';
        $res = $conn->query("SELECT id, name, image, distance_km FROM places WHERE status = 'approved' ORDER BY distance_km ASC LIMIT 4");
        while ($p = $res->fetch_assoc()):
            $pid      = (int) $p['id'];
            $link     = 'cafe_window.php?place_id=' . $pid . '&cafe=' . urlencode($p['name']);
            $dist     = (float) $p['distance_km'];
            $distLabel = $dist > 0 ? number_format($dist, 2) . ' km away' : 'Nearby';
        ?>
            <div class="place-card">
                <a href="<?= $link ?>" style="text-decoration: none; color: inherit; display: block;">
                    <img src="<?= htmlspecialchars($p['image'] ?? '') ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    <div class="place-name">
                        <div><?= htmlspecialchars($p['name']) ?>
                            <div style="font-size: 0.85rem; font-weight: 400; margin-top: 5px; opacity: 0.9;">📍 <?= $distLabel ?></div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- ================================== MAP ================================== -->
<script>
    var allPlaces = [];
    var activeMarkers = [];

    // Define the bounds around HAU
    // Bounds (Zoom out): Dau, Baliti, Porac, Pulung Cacutud
    var maxBounds = L.latLngBounds(
        [15.1033, 120.5614], // southwest - Mega Dike (Near Bayung Porac Park)
        [15.1657, 120.6178]  // northeast - Pulung Cacutud (near Punta Verde Subdivision)
    );

    var map = L.map('map', {
        dragging: true,
        scrollWheelZoom: true,
        doubleClickZoom: true,
        maxBounds: maxBounds,       // locks panning to the area
        maxBoundsViscosity: 1.0,    // user cannot drag outside map, it bounces back immediately
        minZoom: 14                 // prevents zooming out too far
    }).setView([15.133270, 120.591433], 15); // Center - HAU, Zoom level 15 (Street View)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Red icon for HAU center marker
    // Credit: Leaflet Color Markers by Thomas Pointner - https://github.com/pointhi/leaflet-color-markers
    var redIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        shadowUrl: 'https://unpkg.com/leaflet/dist/images/marker-shadow.png',
        iconSize: [30, 46],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Green icon for the user's personal private pins
    var greenIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
        shadowUrl: 'https://unpkg.com/leaflet/dist/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    L.marker([15.13324, 120.59063], { icon: redIcon }).addTo(map)
        .bindPopup('<b>Holy Angel University</b><br>Center point');

    L.circle([15.13324, 120.59063], {
        radius: 1500,
        color: '#062b53',
        fillColor: 'green',
        fillOpacity: 0.2,
        weight: 2.5,
        dashArray: '6, 6'
    }).addTo(map);

    // Load all approved places from DB and show as markers on page load
    fetch('search_places.php?q=')
    .then(r => r.json())
    .then(places => { allPlaces = places; displayMarkers(allPlaces); });

    // Load the logged-in user's private pins and show as green markers
    fetch('get_user_pins.php')
    .then(r => r.json())
    .then(pins => {
        pins.forEach(pin => {
            var m = L.marker([parseFloat(pin.latitude), parseFloat(pin.longitude)], { icon: greenIcon }).addTo(map);
            // Each green marker popup has a delete button
            m.bindPopup(
                '<b>📌 ' + pin.name + '</b><br>' + (pin.note || 'No note added') + '<br><br>' +
                '<button onclick="deletePin(' + pin.id + ', this)" ' +
                'style="background:red; color:white; border:none; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:0.8rem;">🗑 Delete</button>'
            );
            // Store the marker reference so we can remove it from the map after deletion
            m.pinId = pin.id;
            activeMarkers.push(m);
        });
    });

    // Delete pin function - sends the pin ID to delete_pin.php,
    // which checks if the pin belongs to the user before deleting
    function deletePin(pinId, btn) {
        if (!confirm('Delete this pin?')) return;
        fetch('delete_pin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: pinId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                // Find and remove the marker from the map
                activeMarkers.forEach((m, i) => {
                    if (m.pinId === pinId) { map.removeLayer(m); activeMarkers.splice(i, 1); }
                });
            }
        });
    }

    // Doc 2 improvement: preserve green (private) pins when filters/search re-render markers
    function displayMarkers(placesToDisplay) {
        // Keep green pins — only remove place markers
        activeMarkers = activeMarkers.filter(m => {
            if (m.pinId) return true;
            map.removeLayer(m); return false;
        });

        placesToDisplay.forEach(place => {
            var lat = parseFloat(place.latitude);
            var lng = parseFloat(place.longitude);
            if (!lat || !lng) return;

            // Use place_id in link so cafe_window always resolves correctly (Doc 2 improvement)
            var pid  = place.id || 0;
            var link = pid
                ? 'cafe_window.php?place_id=' + pid + '&cafe=' + encodeURIComponent(place.name)
                : 'cafe_window.php?cafe=' + encodeURIComponent(place.name);

            var marker = L.marker([lat, lng]).addTo(map)
                .bindTooltip('<b>' + place.name + '</b><br>' + place.location, {
                    direction: 'top',
                    offset: [0, -10]
                });
            marker.on('click', () => { window.location.href = link; });
            activeMarkers.push(marker);
        });
    }

    // ================================== SEARCH ==================================
    var searchMarkers = [];

    function searchPlace() {
        var query = document.getElementById('search-box').value.trim();
        if (!query) return;

        searchMarkers.forEach(m => map.removeLayer(m));
        searchMarkers = [];

        fetch('search_places.php?q=' + encodeURIComponent(query))
        .then(r => r.json())
        .then(results => {
            var suggestionsBox = document.getElementById('suggestions-box');
            suggestionsBox.innerHTML = '';

            if (!results.length) {
                suggestionsBox.innerHTML = '<div style="padding: 12px; color: #888;">No places found.</div>';
                suggestionsBox.style.display = 'block';
                return;
            }

            results.forEach(place => {
                var lat = parseFloat(place.latitude);
                var lng = parseFloat(place.longitude);

                var marker = L.marker([lat, lng]).addTo(map);
                marker.bindPopup('<b>' + place.name + '</b><br>' + place.location);
                searchMarkers.push(marker);

                var pulse = L.circle([lat, lng], {
                    color: '#e74c3c', fillColor: '#e74c3c', fillOpacity: 0.15, radius: 80
                }).addTo(map);
                searchMarkers.push(pulse);

                var row = document.createElement('div');
                row.style.padding = '10px 14px';
                row.style.cursor = 'pointer';
                row.style.borderBottom = '1px solid #f0f0f0';
                row.innerHTML = '<b>' + place.name + '</b><br><small>' + place.location + '</small>';

                row.onclick = (() => { var _m = marker, _lat = lat, _lng = lng; return () => {
                    map.setView([_lat, _lng], 17);
                    _m.openPopup();
                    suggestionsBox.style.display = 'none';
                }; })();
                row.onmouseenter = function() { this.style.background = '#f5f5f5'; };
                row.onmouseleave = function() { this.style.background = 'white'; };
                suggestionsBox.appendChild(row);
            });

            if (results.length === 1) {
                map.setView([parseFloat(results[0].latitude), parseFloat(results[0].longitude)], 17);
                searchMarkers[0].openPopup();
            }
            suggestionsBox.style.display = 'block';
        })
        .catch(() => alert('Search failed. Check your connection.'));
    }

    document.addEventListener('click', e => {
        var box   = document.getElementById('suggestions-box');
        var input = document.getElementById('search-box');
        if (e.target !== input && !box.contains(e.target)) box.style.display = 'none';
    });
    // ================================== END OF SEARCH ==================================

    // ================================== FILTER ==================================
    function toggleFilterBox() {
        var box    = document.getElementById('filter-popout');
        var dimmer = document.getElementById('modal-dimmer');
        var opening = box.style.display === 'none' || box.style.display === '';
        box.style.display = opening ? 'block' : 'none';
        if (window.innerWidth < 768) dimmer.classList.toggle('active', opening);
    }

    function applyFilters() {
        var wifi    = document.querySelector('input[value="wifi"]').checked;
        var outlet  = document.querySelector('input[value="outlets"]').checked;
        var aircon  = document.querySelector('input[value="aircon"]').checked;
        var parking = document.querySelector('input[value="parking"]').checked;

        displayMarkers(allPlaces.filter(place =>
            (!wifi    || place.wifi    === 'Yes') &&
            (!outlet  || place.outlet  === 'Yes') &&
            (!aircon  || place.aircon  === 'Yes') &&
            (!parking || place.parking === 'Yes')
        ));
    }

    function clearFilters() {
        document.getElementById('filter-form').reset();
        displayMarkers(allPlaces);
    }
    // ================================== END OF FILTER ==================================

    // ================================== PIN A PLACE ==================================
    var pinPlaceMarker = null;
    var pinPlaceMode   = false;

    function activatePinAPlace(e) {
        e.preventDefault();
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('You need to be logged in to pin a place.');
            window.location.href = 'login.php';
            return;
        <?php endif; ?>
        document.getElementById('pin-modal').style.display = 'flex';
    }

    function startPinPlacement() {
        // Hide the modal so the user can click freely on the map
        document.getElementById('pin-modal').style.display = 'none';
        pinPlaceMode = true;

        var banner = document.createElement('div');
        banner.id = 'pin-place-banner';
        banner.style.cssText = 'position:fixed; top:20px; left:50%; transform:translateX(-50%); background:#6D3E1C; color:white; padding:10px 20px; border-radius:8px; z-index:9999; font-size:0.9rem;';
        banner.innerHTML = '📍 Click anywhere on the map to place your private pin';
        document.body.appendChild(banner);
    }

    function closePinModal() {
        pinPlaceMode = false;
        document.getElementById('pin-modal').style.display = 'none';

        if (pinPlaceMarker) { map.removeLayer(pinPlaceMarker); pinPlaceMarker = null; }

        // Reset form fields (Doc 2 improvement: forEach loop)
        ['pin-lat', 'pin-lng', 'pin-name', 'pin-note'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('pin-save-msg').textContent = '';

        // Reset status back to original prompt
        var status = document.getElementById('pin-place-status');
        status.innerHTML = '📍 No pin placed yet — <button onclick="startPinPlacement()" style="background:#6D3E1C; color:white; border:none; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:0.85rem;">Click here to place pin</button>';
        status.style.background   = '#f0f4ff';
        status.style.borderColor  = '#c0cdff';
        status.style.color        = '#3a5bd9';
    }

    function savePin() {
        var lat  = document.getElementById('pin-lat').value;
        var lng  = document.getElementById('pin-lng').value;
        var name = document.getElementById('pin-name').value.trim();
        var note = document.getElementById('pin-note').value.trim();
        var msg  = document.getElementById('pin-save-msg');

        if (!lat || !lng) { msg.style.color = 'red'; msg.textContent = 'Please place a pin on the map first.'; return; }
        if (!name)        { msg.style.color = 'red'; msg.textContent = 'Please enter a name for your pin.'; return; }

        // Send to save_pin.php to store in the user_pins table
        fetch('save_pin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, note, latitude: lat, longitude: lng })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                // Replace the temporary marker with a permanent green one
                if (pinPlaceMarker) map.removeLayer(pinPlaceMarker);
                L.marker([parseFloat(lat), parseFloat(lng)], { icon: greenIcon }).addTo(map)
                    .bindPopup('<b>📌 ' + name + '</b><br>' + (note || 'No note added'))
                    .openPopup();
                msg.style.color = 'green';
                msg.textContent = 'Pin saved! Only you can see this.';
                setTimeout(closePinModal, 1500);
            } else if (data.message === 'Not logged in') {
                msg.style.color = 'red'; msg.textContent = 'You must be logged in to save pins.';
            } else {
                msg.style.color = 'red'; msg.textContent = 'Something went wrong. Try again.';
            }
        })
        .catch(() => { msg.style.color = 'red'; msg.textContent = 'Failed to save. Check your connection.'; });
    }
    // ================================== END OF PIN A PLACE ==================================

    // ================================== PROPOSAL ==================================
    var proposalMarker = null;
    var proposalMode   = false;

    function openProposalModal() {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('You need to be logged in to propose a location.');
            window.location.href = 'login.php';
            return;
        <?php endif; ?>
        document.getElementById('propose-modal').style.display = 'flex';
    }

    function closeProposalModal() {
        proposalMode = false;
        document.getElementById('propose-modal').style.display = 'none';
        if (proposalMarker) { map.removeLayer(proposalMarker); proposalMarker = null; }

        var banner = document.getElementById('pin-banner');
        if (banner) banner.remove();

        // Reset form fields (Doc 2 improvement: forEach loop)
        ['propose-lat', 'propose-lng', 'propose-name', 'propose-location', 'propose-description']
            .forEach(id => document.getElementById(id).value = '');

        var pinStatus = document.getElementById('pin-status');
        pinStatus.innerHTML = '📍 No pin placed yet — <button onclick="activatePinMode()" style="background:#062b53; color:white; border:none; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:0.85rem;">Click here to place pin</button>';
        pinStatus.style.background  = '#f0f4ff';
        pinStatus.style.borderColor = '#c0cdff';
        pinStatus.style.color       = '#3a5bd9';
    }

    function activatePinMode() {
        document.getElementById('propose-modal').style.display = 'none';
        proposalMode = true;

        var banner = document.createElement('div');
        banner.id = 'pin-banner';
        banner.style.cssText = 'position:fixed; top:20px; left:50%; transform:translateX(-50%); background:#062b53; color:white; padding:10px 20px; border-radius:8px; z-index:9999; font-size:0.9rem;';
        banner.innerHTML = '📍 Click anywhere on the map to place your pin';
        document.body.appendChild(banner);
    }

    // Single map click handler - checks which mode is active
    map.on('click', e => {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        // Handle PIN A PLACE mode
        if (pinPlaceMode) {
            if (pinPlaceMarker) map.removeLayer(pinPlaceMarker);

            // Drop a temporary draggable marker at the clicked spot
            pinPlaceMarker = L.marker([lat, lng], { draggable: true }).addTo(map);
            pinPlaceMarker.bindPopup('📍 Your private pin').openPopup();

            document.getElementById('pin-lat').value = lat.toFixed(7);
            document.getElementById('pin-lng').value = lng.toFixed(7);

            var b = document.getElementById('pin-place-banner');
            if (b) b.remove();
            document.getElementById('pin-modal').style.display = 'flex';

            var status = document.getElementById('pin-place-status');
            status.innerHTML = '✅ Pin placed at (' + lat.toFixed(5) + ', ' + lng.toFixed(5) + ') — drag to adjust.';
            status.style.background  = '#f0fff4';
            status.style.borderColor = '#a0ddb0';
            status.style.color       = '#2d7a4f';

            // Update coordinates if the user drags the marker
            pinPlaceMarker.on('dragend', () => {
                var p = pinPlaceMarker.getLatLng();
                document.getElementById('pin-lat').value = p.lat.toFixed(7);
                document.getElementById('pin-lng').value = p.lng.toFixed(7);
                document.getElementById('pin-place-status').innerHTML =
                    '✅ Pin moved to (' + p.lat.toFixed(5) + ', ' + p.lng.toFixed(5) + ')';
            });

            pinPlaceMode = false;
            return;
        }

        // Handle PROPOSAL mode
        if (proposalMode) {
            if (proposalMarker) map.removeLayer(proposalMarker);

            proposalMarker = L.marker([lat, lng], { draggable: true }).addTo(map);
            proposalMarker.bindPopup('📍 Proposed location').openPopup();

            document.getElementById('propose-lat').value = lat.toFixed(7);
            document.getElementById('propose-lng').value = lng.toFixed(7);

            var b = document.getElementById('pin-banner');
            if (b) b.remove();
            document.getElementById('propose-modal').style.display = 'flex';

            var pinStatus = document.getElementById('pin-status');
            pinStatus.innerHTML = '✅ Pin placed at (' + lat.toFixed(5) + ', ' + lng.toFixed(5) + ') — you can drag it to adjust.';
            pinStatus.style.background  = '#f0fff4';
            pinStatus.style.borderColor = '#a0ddb0';
            pinStatus.style.color       = '#2d7a4f';

            proposalMarker.on('dragend', () => {
                var p = proposalMarker.getLatLng();
                document.getElementById('propose-lat').value = p.lat.toFixed(7);
                document.getElementById('propose-lng').value = p.lng.toFixed(7);
                document.getElementById('pin-status').innerHTML =
                    '✅ Pin moved to (' + p.lat.toFixed(5) + ', ' + p.lng.toFixed(5) + ')';
            });

            proposalMode = false;
            return;
        }
    });

    function submitProposal() {
        var lat      = document.getElementById('propose-lat').value;
        var lng      = document.getElementById('propose-lng').value;
        var name     = document.getElementById('propose-name').value.trim();
        var location = document.getElementById('propose-location').value.trim();
        var desc     = document.getElementById('propose-description').value.trim();

        // Doc 2 improvement: merged validation checks
        if (!lat || !lng)          { alert('Please place a pin on the map first.'); return; }
        if (!name || !location || !desc) { alert('Please fill in all required fields.'); return; }

        var formData = new FormData();
        formData.append('name',        name);
        formData.append('location',    location);
        formData.append('description', desc);
        formData.append('latitude',    lat);
        formData.append('longitude',   lng);
        formData.append('wifi',    document.getElementById('p-wifi').checked    ? 'Yes' : 'No');
        formData.append('outlet',  document.getElementById('p-outlet').checked  ? 'Yes' : 'No');
        formData.append('aircon',  document.getElementById('p-aircon').checked  ? 'Yes' : 'No');
        formData.append('parking', document.getElementById('p-parking').checked ? 'Yes' : 'No');

        fetch('propose_location.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('✅ Your proposal has been submitted! It will appear on the map once approved by an admin.');
                closeProposalModal();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(() => alert('Submission failed. Please check your connection.'));
    }
    // ================================== END OF PROPOSAL ==================================

</script>
<!-- ================================== END OF MAP ================================== -->
</body></html>