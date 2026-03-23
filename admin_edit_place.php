<?php
// ═══════════════════════════════════════════════════════
//  admin_edit_place.php  —  Admin: edit & publish a place
// ═══════════════════════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) session_start();

include 'config.php';

// Guard: must be logged-in admin
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uid  = (int) $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role_id FROM accounts WHERE account_id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$stmt->bind_result($roleId);
$stmt->fetch();
$stmt->close();

if ($roleId !== 2) {
    header('Location: index.php');
    exit;
}

// Load place
$place_id = (int) ($_GET['id'] ?? 0);
if (!$place_id) {
    header('Location: admin.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM places WHERE id = ?");
$stmt->bind_param("i", $place_id);
$stmt->execute();
$place = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$place) {
    header('Location: admin.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'name'          => trim($_POST['name']         ?? $place['name']),
        'location'      => trim($_POST['location']     ?? $place['location']),
        'description'   => trim($_POST['description']  ?? $place['description']),
        'hours_weekday' => trim($_POST['hours_weekday'] ?? ''),
        'hours_weekend' => trim($_POST['hours_weekend'] ?? ''),
        'wifi'          => ($_POST['wifi']    ?? '') === 'Yes' ? 'Yes' : 'No',
        'outlet'        => ($_POST['outlet']  ?? '') === 'Yes' ? 'Yes' : 'No',
        'aircon'        => ($_POST['aircon']  ?? '') === 'Yes' ? 'Yes' : 'No',
        'parking'       => ($_POST['parking'] ?? '') === 'Yes' ? 'Yes' : 'No',
        'latitude'      => (float) ($_POST['latitude']    ?? $place['latitude']),
        'longitude'     => (float) ($_POST['longitude']   ?? $place['longitude']),
        'distance_km'   => (float) ($_POST['distance_km'] ?? $place['distance_km']),
    ];

    // Image upload
    $image = $place['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'images/place_' . $place_id . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filename)) {
            $image = $filename;
        }
    }

    $is_publish  = ($_POST['action'] ?? '') === 'publish';
    $new_status  = $is_publish ? 'approved'            : $place['status'];
    $reviewed_by = $is_publish ? $uid                  : $place['reviewed_by'];
    $reviewed_at = $is_publish ? date('Y-m-d H:i:s')  : $place['reviewed_at'];

    $upd = $conn->prepare("
        UPDATE places
        SET name = ?, location = ?, description = ?, image = ?,
            wifi = ?, outlet = ?, aircon = ?, parking = ?,
            latitude = ?, longitude = ?, distance_km = ?,
            hours_weekday = ?, hours_weekend = ?,
            status = ?, reviewed_by = ?, reviewed_at = ?
        WHERE id = ?
    ");
    $upd->bind_param(
        "ssssssssdddsssssi",
        $fields['name'],        $fields['location'],    $fields['description'], $image,
        $fields['wifi'],        $fields['outlet'],      $fields['aircon'],      $fields['parking'],
        $fields['latitude'],    $fields['longitude'],   $fields['distance_km'],
        $fields['hours_weekday'], $fields['hours_weekend'],
        $new_status, $reviewed_by, $reviewed_at,
        $place_id
    );
    $upd->execute();
    $upd->close();

    header($is_publish
        ? 'Location: admin.php?published=1'
        : "Location: admin_edit_place.php?id=$place_id&saved=1"
    );
    exit;
}

$amenities = [
    'wifi'    => ['🛜', 'Wi-Fi'],
    'outlet'  => ['🔌', 'Outlets'],
    'aircon'  => ['❄️', 'Air-Con'],
    'parking' => ['🅿️', 'Parking'],
];

include 'header.php';
?>

<style>
    :root {
        --brown:      #6D3E1C;
        --brown-dark: #522e15;
        --cream:      #FFF3DB;
        --cream-dark: #faeac8;
        --surface:    #ffffff;
        --border:     #e8d9c4;
        --text:       #2a1a0e;
        --muted:      #8a7060;
        --shadow:     0 4px 24px rgba(109,62,28,0.12);
    }
    .edit-layout { display: grid; grid-template-columns: 1fr 400px; max-width: 1280px; margin: 36px auto; padding: 0 24px; align-items: start; }
    .edit-panel { background: var(--surface); border: 1px solid var(--border); border-right: none; border-radius: 16px 0 0 16px; padding: 40px 44px; box-shadow: var(--shadow); }
    .edit-panel-header { display: flex; align-items: center; gap: 14px; margin-bottom: 34px; padding-bottom: 22px; border-bottom: 2px solid var(--cream-dark); flex-wrap: wrap; }
    .back-btn { display: flex; align-items: center; gap: 6px; padding: 7px 14px; border: 2px solid var(--brown); border-radius: 8px; color: var(--brown); text-decoration: none; font-size: 0.83rem; font-weight: 700; transition: all 0.2s; white-space: nowrap; }
    .back-btn:hover { background: var(--brown); color: white; }
    .panel-title { font-size: 1.5rem; font-weight: 800; color: var(--brown); }
    .panel-subtitle { font-size: 0.85rem; color: var(--muted); margin-top: 2px; }
    .status-chip { display: inline-flex; padding: 4px 13px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-left: auto; }
    .status-pending  { background: #fff3cd; color: #856404; }
    .status-approved { background: #d1e7dd; color: #0a5c36; }
    .status-rejected { background: #f8d7da; color: #842029; }
    .form-section { margin-bottom: 30px; }
    .section-label { font-size: 0.7rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: var(--brown); margin-bottom: 14px; display: flex; align-items: center; gap: 10px; }
    .section-label::after { content: ''; flex: 1; height: 1.5px; background: var(--cream-dark); }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-grid.full { grid-template-columns: 1fr; }
    .field-group { display: flex; flex-direction: column; gap: 6px; }
    .field-group label { font-size: 0.82rem; font-weight: 700; color: var(--brown); }
    .field-group input, .field-group textarea, .field-group select { padding: 10px 14px; border: 1.5px solid rgba(109,62,28,0.22); border-radius: 9px; font-family: inherit; font-size: 0.88rem; background: var(--cream); color: var(--text); outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
    .field-group input:focus, .field-group textarea:focus { border-color: var(--brown); box-shadow: 0 0 0 3px rgba(109,62,28,0.10); background: white; }
    .field-group textarea { resize: vertical; min-height: 88px; }
    .field-hint { font-size: 0.75rem; color: var(--muted); }
    .amenity-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; }
    .amenity-toggle { display: none; }
    .amenity-label { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 7px; padding: 14px 8px; border: 2px solid rgba(109,62,28,0.18); border-radius: 12px; cursor: pointer; font-size: 0.78rem; font-weight: 700; color: var(--muted); background: var(--cream); transition: all 0.2s; text-align: center; user-select: none; }
    .amenity-label .icon { font-size: 1.45rem; }
    .amenity-toggle:checked + .amenity-label { border-color: var(--brown); background: var(--cream-dark); color: var(--brown); }
    .image-upload-area { position: relative; border: 2px dashed rgba(109,62,28,0.28); border-radius: 12px; padding: 22px; text-align: center; cursor: pointer; background: var(--cream); transition: all 0.2s; }
    .image-upload-area:hover { border-color: var(--brown); background: var(--cream-dark); }
    .image-upload-area input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .image-preview { width: 100%; height: 175px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; display: block; }
    .upload-hint { font-size: 0.8rem; color: var(--muted); margin-top: 6px; }
    .action-bar { display: flex; gap: 12px; padding-top: 22px; border-top: 2px solid var(--cream-dark); }
    .btn { padding: 12px 22px; border: none; border-radius: 9px; font-family: inherit; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-save { background: var(--cream); color: var(--brown); border: 2px solid var(--brown); flex: 1; justify-content: center; }
    .btn-save:hover { background: var(--cream-dark); }
    .btn-publish { background: var(--brown); color: var(--cream); flex: 2; justify-content: center; box-shadow: 0 4px 14px rgba(109,62,28,0.28); }
    .btn-publish:hover { background: var(--brown-dark); transform: translateY(-1px); }
    .alert-success { padding: 12px 16px; border-radius: 9px; background: #d1e7dd; color: #0a5c36; font-size: 0.87rem; font-weight: 600; margin-bottom: 20px; border: 1px solid #a3cfbb; }
    .preview-panel { background: var(--brown); border: 1px solid var(--border); border-left: none; border-radius: 0 16px 16px 0; position: sticky; top: 24px; overflow: hidden; box-shadow: var(--shadow); }
    .preview-header { padding: 18px 22px 15px; background: var(--brown-dark); border-bottom: 1px solid rgba(255,243,219,0.12); display: flex; align-items: center; justify-content: space-between; }
    .preview-title { font-size: 0.7rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,243,219,0.45); }
    .preview-badge { font-size: 0.68rem; padding: 3px 10px; border-radius: 20px; background: rgba(255,243,219,0.15); color: var(--cream); font-weight: 700; border: 1px solid rgba(255,243,219,0.22); }
    .preview-hero { width: 100%; height: 175px; object-fit: cover; display: block; }
    .preview-hero-placeholder { width: 100%; height: 175px; background: linear-gradient(135deg, var(--brown-dark), var(--brown) 60%, #8B5A2B); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; color: rgba(255,243,219,0.3); font-size: 0.8rem; }
    .preview-hero-placeholder .ph-icon { font-size: 2.2rem; opacity: 0.5; }
    .preview-body { padding: 20px 22px 26px; }
    .preview-name { font-size: 1.25rem; font-weight: 800; color: var(--cream); margin-bottom: 4px; }
    .preview-location { font-size: 0.78rem; color: rgba(255,243,219,0.52); margin-bottom: 12px; }
    .preview-desc { font-size: 0.81rem; color: rgba(255,243,219,0.70); line-height: 1.6; margin-bottom: 16px; }
    .preview-info-row { display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; }
    .preview-info-item { display: flex; gap: 10px; align-items: flex-start; font-size: 0.8rem; }
    .preview-info-key { min-width: 58px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--cream-dark); opacity: 0.9; padding-top: 1px; }
    .preview-info-val { color: rgba(255,243,219,0.72); line-height: 1.5; }
    .preview-amenities { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 14px; }
    .preview-amenity { padding: 4px 12px; border-radius: 20px; background: rgba(255,243,219,0.10); color: rgba(255,243,219,0.72); font-size: 0.74rem; font-weight: 700; border: 1px solid rgba(255,243,219,0.18); }
    .preview-coords { margin-top: 14px; padding: 9px 13px; background: rgba(0,0,0,0.20); border-radius: 7px; font-family: 'Courier New', monospace; font-size: 0.72rem; color: rgba(255,243,219,0.35); border: 1px solid rgba(255,243,219,0.08); }
    @media (max-width: 960px) {
        .edit-layout { grid-template-columns: 1fr; }
        .edit-panel { border-right: 1px solid var(--border); border-radius: 16px; padding: 28px 24px; }
        .preview-panel { border-left: 1px solid var(--border); border-radius: 16px; position: static; }
        .amenity-grid { grid-template-columns: repeat(2,1fr); }
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="edit-layout">

    <!-- FORM -->
    <div class="edit-panel">
        <div class="edit-panel-header">
            <a href="admin.php" class="back-btn">← Back</a>
            <div>
                <div class="panel-title">Edit Cafe Window</div>
                <div class="panel-subtitle">Configure how this location appears to users</div>
            </div>
            <span class="status-chip status-<?= $place['status'] ?>"><?= ucfirst($place['status']) ?></span>
        </div>

        <?php if (isset($_GET['saved'])): ?>
            <div class="alert-success">✅ Changes saved as draft.</div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <!-- Basic info -->
            <div class="form-section">
                <div class="section-label">Basic Information</div>
                <div class="form-grid full">
                    <div class="field-group">
                        <label>Place Name</label>
                        <input type="text" name="name" id="prev_name"
                               value="<?= htmlspecialchars($place['name']) ?>"
                               oninput="updatePreview()" required>
                    </div>
                </div>
                <div class="form-grid" style="margin-top:14px;">
                    <div class="field-group">
                        <label>Location / Address</label>
                        <input type="text" name="location" id="prev_location"
                               value="<?= htmlspecialchars($place['location']) ?>"
                               oninput="updatePreview()">
                    </div>
                    <div class="field-group">
                        <label>Short Description</label>
                        <input type="text" name="description" id="prev_desc"
                               value="<?= htmlspecialchars($place['description'] ?? '') ?>"
                               oninput="updatePreview()">
                    </div>
                </div>
            </div>

            <!-- Hours -->
            <div class="form-section">
                <div class="section-label">Operating Hours</div>
                <div class="form-grid">
                    <div class="field-group">
                        <label>Weekday Hours</label>
                        <input type="text" name="hours_weekday" id="prev_hours_wd"
                               value="<?= htmlspecialchars($place['hours_weekday'] ?? '') ?>"
                               placeholder="e.g. 8 AM – 10 PM" oninput="updatePreview()">
                    </div>
                    <div class="field-group">
                        <label>Weekend Hours</label>
                        <input type="text" name="hours_weekend" id="prev_hours_we"
                               value="<?= htmlspecialchars($place['hours_weekend'] ?? '') ?>"
                               placeholder="e.g. 10 AM – 8 PM" oninput="updatePreview()">
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="form-section">
                <div class="section-label">Amenities</div>
                <div class="amenity-grid">
                    <?php foreach ($amenities as $key => [$icon, $label]): ?>
                    <div>
                        <input type="checkbox" class="amenity-toggle" id="am_<?= $key ?>"
                               <?= $place[$key] === 'Yes' ? 'checked' : '' ?>
                               onchange="syncAmenity('<?= $key ?>', this.checked)">
                        <input type="hidden" name="<?= $key ?>" id="hid_<?= $key ?>"
                               value="<?= $place[$key] ?>">
                        <label class="amenity-label" for="am_<?= $key ?>">
                            <span class="icon"><?= $icon ?></span><?= $label ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Coordinates & distance -->
            <div class="form-section">
                <div class="section-label">Location &amp; Distance from HAU</div>
                <div id="coordMap" style="height:220px; border-radius:10px; border:1.5px solid rgba(109,62,28,0.2); margin-bottom:14px;"></div>
                <p class="field-hint" style="margin-bottom:14px;">🗺️ Drag the marker or click inside the circle. Places must be within <strong>1.5 km</strong> of HAU.</p>
                <div class="form-grid">
                    <div class="field-group">
                        <label>Latitude</label>
                        <input type="number" name="latitude" id="prev_lat" step="0.0000001"
                               value="<?= $place['latitude'] ?>" oninput="onCoordsChange()">
                    </div>
                    <div class="field-group">
                        <label>Longitude</label>
                        <input type="number" name="longitude" id="prev_lng" step="0.0000001"
                               value="<?= $place['longitude'] ?>" oninput="onCoordsChange()">
                    </div>
                </div>
                <div class="form-grid" style="margin-top:14px;">
                    <div class="field-group">
                        <label>Distance from HAU (km)</label>
                        <input type="number" name="distance_km" id="prev_dist" step="0.01" min="0"
                               value="<?= number_format((float)$place['distance_km'], 2) ?>"
                               oninput="updatePreview()">
                        <span class="field-hint">Auto-calculated · or edit manually</span>
                    </div>
                    <div class="field-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-save" onclick="recalcDistance()">
                            📏 Recalculate
                        </button>
                    </div>
                </div>
            </div>

            <!-- Image -->
            <div class="form-section">
                <div class="section-label">Cover Image</div>
                <div class="image-upload-area">
                    <?php if ($place['image']): ?>
                        <img src="<?= htmlspecialchars($place['image']) ?>" class="image-preview" id="imgPreview">
                    <?php else: ?>
                        <div id="imgPlaceholder">
                            <div style="font-size:2rem;">🖼️</div>
                            <div style="font-weight:600;font-size:0.9rem;color:var(--muted);">Click to upload image</div>
                        </div>
                        <img src="" class="image-preview" id="imgPreview" style="display:none;">
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                    <div class="upload-hint">JPG, PNG, WEBP — recommended 1200×600px</div>
                </div>
            </div>

            <!-- Submit -->
            <div class="action-bar">
                <button type="submit" name="action" value="save" class="btn btn-save">
                    💾 Save Draft
                </button>
                <button type="submit" name="action" value="publish" class="btn btn-publish"
                        onclick="return confirm('Publish this location? It will be visible across the site.')">
                    🚀 Publish
                </button>
            </div>

        </form>
    </div>

    <!-- LIVE PREVIEW -->
    <div class="preview-panel">
        <div class="preview-header">
            <span class="preview-title">Live Preview</span>
            <span class="preview-badge">Cafe Window</span>
        </div>

        <?php if ($place['image']): ?>
            <img src="<?= htmlspecialchars($place['image']) ?>" class="preview-hero" id="previewHeroImg">
            <div class="preview-hero-placeholder" id="previewHeroPlaceholder" style="display:none;">
                <span class="ph-icon">🏙️</span><span>No image yet</span>
            </div>
        <?php else: ?>
            <img src="" class="preview-hero" id="previewHeroImg" style="display:none;">
            <div class="preview-hero-placeholder" id="previewHeroPlaceholder">
                <span class="ph-icon">🏙️</span><span>No image yet</span>
            </div>
        <?php endif; ?>

        <div class="preview-body">
            <div class="preview-name" id="pv_name"><?= htmlspecialchars($place['name']) ?></div>
            <div class="preview-location">📍 <span id="pv_location"><?= htmlspecialchars($place['location']) ?></span></div>
            <div class="preview-desc" id="pv_desc"><?= htmlspecialchars($place['description'] ?? 'No description provided.') ?></div>

            <div class="preview-info-row">
                <div class="preview-info-item">
                    <span class="preview-info-key">Hours</span>
                    <span class="preview-info-val" id="pv_hours">
                        <?php
                            $wd = $place['hours_weekday'] ?? '';
                            $we = $place['hours_weekend'] ?? '';
                            echo $wd
                                ? htmlspecialchars($wd) . ($we ? '<br>' . htmlspecialchars($we) : '')
                                : '<em style="color:rgba(255,243,219,0.25)">Not set</em>';
                        ?>
                    </span>
                </div>
                <div class="preview-info-item">
                    <span class="preview-info-key">Dist.</span>
                    <span class="preview-info-val" id="pv_dist">
                        <?php $d = (float)$place['distance_km']; echo $d > 0 ? number_format($d,2).' km from HAU' : 'Nearby'; ?>
                    </span>
                </div>
            </div>

            <div class="preview-amenities" id="pv_amenities">
                <?php foreach ($amenities as $key => [$icon, $label]): ?>
                    <span class="preview-amenity" id="pva_<?= $key ?>"
                          style="<?= $place[$key] !== 'Yes' ? 'display:none' : '' ?>">
                        <?= $icon ?> <?= $label ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <div class="preview-coords" id="pv_coords">
                📍 <?= $place['latitude'] ?>, <?= $place['longitude'] ?>
            </div>
        </div>
    </div>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const HAU    = { lat: 15.13350, lng: 120.59130 };
const MAX_KM = 1.5;
const MAX_M  = MAX_KM * 1000;

function haversineKm(lat1, lng1, lat2, lng2) {
    const R  = 6371;
    const dL = (lat2 - lat1) * Math.PI / 180;
    const dG = (lng2 - lng1) * Math.PI / 180;
    const a  = Math.sin(dL/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dG/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

const initLat = parseFloat(document.getElementById('prev_lat').value) || HAU.lat;
const initLng = parseFloat(document.getElementById('prev_lng').value) || HAU.lng;

const map = L.map('coordMap').setView([HAU.lat, HAU.lng], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap contributors' }).addTo(map);

L.circle([HAU.lat, HAU.lng], { radius: MAX_M, color: '#6D3E1C', weight: 2, dashArray: '6 4', fillColor: '#6D3E1C', fillOpacity: 0.06 }).addTo(map);

L.marker([HAU.lat, HAU.lng], {
    icon: L.divIcon({ className: '', html: '<div style="background:#062b53;color:#fff;padding:4px 8px;border-radius:6px;font-size:0.7rem;font-weight:800;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,0.3)">🏫 HAU</div>', iconAnchor: [20,12] })
}).addTo(map);

const marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);
marker.on('dragend', e => applyCoords(e.target.getLatLng().lat, e.target.getLatLng().lng));
map.on('click', e => applyCoords(e.latlng.lat, e.latlng.lng));

function applyCoords(lat, lng) {
    const km = haversineKm(HAU.lat, HAU.lng, lat, lng);
    if (km > MAX_KM) {
        const lastLat = parseFloat(document.getElementById('prev_lat').value) || HAU.lat;
        const lastLng = parseFloat(document.getElementById('prev_lng').value) || HAU.lng;
        marker.setLatLng([lastLat, lastLng]);
        showOOB(km); return;
    }
    hideOOB();
    document.getElementById('prev_lat').value = lat.toFixed(7);
    document.getElementById('prev_lng').value = lng.toFixed(7);
    marker.setLatLng([lat, lng]);
    recalcDistance();
}

function showOOB(km) {
    let el = document.getElementById('oob-warning');
    if (!el) {
        el = document.createElement('div'); el.id = 'oob-warning';
        el.style.cssText = 'margin-top:8px;padding:10px 14px;border-radius:8px;background:#f8d7da;color:#842029;font-size:0.83rem;font-weight:700;display:flex;align-items:center;gap:8px;border:1px solid #f5c2c7';
        document.getElementById('coordMap').insertAdjacentElement('afterend', el);
    }
    el.innerHTML = `⚠️ That spot is <strong>${km.toFixed(2)} km</strong> from HAU — outside the 1.5 km limit.`;
    el.style.display = 'flex';
}
function hideOOB() { const el = document.getElementById('oob-warning'); if (el) el.style.display = 'none'; }

function onCoordsChange() {
    const lat = parseFloat(document.getElementById('prev_lat').value);
    const lng = parseFloat(document.getElementById('prev_lng').value);
    if (!isNaN(lat) && !isNaN(lng)) applyCoords(lat, lng);
}

function recalcDistance() {
    const lat = parseFloat(document.getElementById('prev_lat').value);
    const lng = parseFloat(document.getElementById('prev_lng').value);
    if (isNaN(lat) || isNaN(lng)) return;
    document.getElementById('prev_dist').value = haversineKm(HAU.lat, HAU.lng, lat, lng).toFixed(2);
    updatePreview();
}

function updatePreview() {
    const get = id => document.getElementById(id)?.value ?? '';
    document.getElementById('pv_name').textContent     = get('prev_name')     || 'Place Name';
    document.getElementById('pv_location').textContent = get('prev_location') || '—';
    document.getElementById('pv_desc').textContent     = get('prev_desc')     || 'No description provided.';
    const wd = get('prev_hours_wd'), we = get('prev_hours_we');
    document.getElementById('pv_hours').innerHTML = wd ? wd + (we ? '<br>'+we : '') : '<em style="color:rgba(255,243,219,0.25)">Not set</em>';
    const dist = parseFloat(get('prev_dist'));
    document.getElementById('pv_dist').textContent = (!isNaN(dist) && dist > 0) ? dist.toFixed(2)+' km from HAU' : 'Nearby';
    document.getElementById('pv_coords').textContent = '📍 '+(get('prev_lat')||'—')+', '+(get('prev_lng')||'—');
}

function syncAmenity(key, checked) {
    document.getElementById('hid_'+key).value         = checked ? 'Yes' : 'No';
    document.getElementById('pva_'+key).style.display = checked ? 'inline-flex' : 'none';
}

function previewImage(input) {
    if (!input.files?.[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const src = e.target.result;
        document.getElementById('imgPreview').src = src;
        document.getElementById('imgPreview').style.display = 'block';
        document.getElementById('previewHeroImg').src = src;
        document.getElementById('previewHeroImg').style.display = 'block';
        document.getElementById('previewHeroPlaceholder').style.display = 'none';
        const ph = document.getElementById('imgPlaceholder');
        if (ph) ph.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}

document.querySelector('form').addEventListener('submit', function(e) {
    const lat = parseFloat(document.getElementById('prev_lat').value);
    const lng = parseFloat(document.getElementById('prev_lng').value);
    if (!isNaN(lat) && !isNaN(lng) && haversineKm(HAU.lat, HAU.lng, lat, lng) > MAX_KM) {
        e.preventDefault();
        showOOB(haversineKm(HAU.lat, HAU.lng, lat, lng));
        document.getElementById('coordMap').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

recalcDistance();
</script>

<?php include 'footer.php'; ?>