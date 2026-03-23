<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include 'config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uid      = (int) $_SESSION['user_id'];
$userType = $_SESSION['user_type'] ?? '';

if ($userType !== 'Admin') {
    header('Location: index.php');
    exit;
}

$role_filter = $_GET['role_filter'] ?? 'all';

$pending_places = $conn->query("
    SELECT p.*, a.username AS proposer
    FROM places p
    LEFT JOIN accounts a ON p.proposed_by = a.account_id
    WHERE p.status = 'pending'
    ORDER BY p.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$all_places = $conn->query("
    SELECT p.*, a.username AS reviewer
    FROM places p
    LEFT JOIN accounts a ON p.reviewed_by = a.account_id
    ORDER BY p.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$role_sql_filter = match($role_filter) {
    'Admin' => "WHERE Type = 'Admin'",
    'User'  => "WHERE Type = 'User'",
    default => ''
};

$all_users = $conn->query("
    SELECT account_id, username, profile_pic, Type, created_at
    FROM accounts
    $role_sql_filter
    ORDER BY created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$count_all   = (int) $conn->query("SELECT COUNT(*) FROM accounts")->fetch_row()[0];
$count_admin = (int) $conn->query("SELECT COUNT(*) FROM accounts WHERE Type = 'Admin'")->fetch_row()[0];
$count_user  = (int) $conn->query("SELECT COUNT(*) FROM accounts WHERE Type = 'User'")->fetch_row()[0];

include 'header.php';
?>

<style>
    .admin-wrapper      { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
    .admin-tabs         { display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
    .tab-btn            { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; background: #eee; color: #333; transition: all 0.2s; }
    .tab-btn.active     { background: #062b53; color: white; }
    .tab-content        { display: none; }
    .tab-content.active { display: block; }
    table               { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    th                  { background: #062b53; color: white; padding: 12px 14px; text-align: left; }
    td                  { padding: 10px 14px; border-bottom: 1px solid #eee; vertical-align: middle; }
    tr:hover            { background: #f9f9f9; }
    .badge              { padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    .badge-pending      { background: #fff3cd; color: #856404; }
    .badge-approved     { background: #d1e7dd; color: #0a5c36; }
    .badge-rejected     { background: #f8d7da; color: #842029; }
    .badge-admin        { background: #062b53; color: white; }
    .badge-user         { background: #e2e8f0; color: #333; }
    .action-btn         { padding: 6px 14px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600; }
    .btn-approve        { background: #198754; color: white; }
    .btn-reject         { background: #dc3545; color: white; }
    .btn-edit           { background: #062b53; color: white; text-decoration: none; display: inline-block; }
    .reject-input       { padding: 6px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 0.85rem; width: 160px; }
    .section-title      { font-size: 1.3rem; font-weight: 700; margin-bottom: 16px; color: #062b53; }
    .action-cell        { display: flex; flex-direction: column; gap: 8px; }
    .action-row         { display: flex; gap: 8px; align-items: center; flex-wrap: nowrap; }
    .hint               { font-size: 0.75rem; color: #888; }
    .toast              { position: fixed; bottom: 32px; right: 32px; background: #062b53; color: white; padding: 14px 22px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; box-shadow: 0 8px 32px rgba(6,43,83,0.35); display: flex; align-items: center; gap: 10px; animation: slideUp 0.4s ease, fadeOut 0.5s ease 3.5s forwards; z-index: 9999; }
    @keyframes slideUp  { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeOut  { from { opacity:1; } to { opacity:0; pointer-events:none; } }
    .role-filter-bar        { display: flex; align-items: center; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }
    .role-filter-bar > span { font-weight: 700; color: #062b53; font-size: 0.88rem; margin-right: 4px; }
    .role-filter-btn        { padding: 5px 16px; border-radius: 20px; border: 2px solid #062b53; background: transparent; color: #062b53; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .role-filter-btn:hover,
    .role-filter-btn.active { background: #062b53; color: white; }
    .role-filter-count      { opacity: 0.6; font-size: 0.75rem; }
    .user-avatar      { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 2px solid #e8d9c4; vertical-align: middle; }
    .user-avatar-init { width: 34px; height: 34px; border-radius: 50%; background: #e8d9c4; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; color: #6D3E1C; font-size: 0.85rem; vertical-align: middle; }
</style>

<?php if (isset($_GET['published'])): ?>
    <div class="toast">🚀 Location published! It's now live across the site.</div>
<?php endif; ?>

<div class="admin-wrapper">
    <h1>Admin Panel</h1>
    <p style="color:#888; margin-bottom:24px;">Manage proposals, locations, and user accounts.</p>

    <div class="admin-tabs">
        <button class="tab-btn active" onclick="switchTab('pending', this)">
            Pending Proposals
            <?php if ($pending_places): ?>
                <span style="background:#dc3545;color:white;border-radius:20px;padding:1px 7px;font-size:0.72rem;margin-left:4px;">
                    <?= count($pending_places) ?>
                </span>
            <?php endif; ?>
        </button>
        <button class="tab-btn" onclick="switchTab('locations', this)">All Locations</button>
        <button class="tab-btn" onclick="switchTab('users', this)">User Accounts</button>
    </div>

    <div id="tab-pending" class="tab-content active">
        <div class="section-title">Pending Proposals</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th><th>Location</th><th>Proposed By</th>
                    <th>Submitted</th><th>Coordinates</th><th>Amenities</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($pending_places)): ?>
                <tr><td colspan="7" style="text-align:center;color:#888;padding:20px;">No pending proposals.</td></tr>
            <?php else: ?>
                <?php foreach ($pending_places as $row): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($row['name']) ?></strong><br>
                        <small style="color:#888;"><?= htmlspecialchars($row['description'] ?? '') ?></small>
                    </td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><?= $row['proposer'] ? htmlspecialchars($row['proposer']) : '<em style="color:#aaa">Guest</em>' ?></td>
                    <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                    <td style="font-size:0.8rem;color:#555;">📍 <?= $row['latitude'] ?>, <?= $row['longitude'] ?></td>
                    <td style="font-size:0.82rem;">
                        <?= $row['wifi']    === 'Yes' ? '<div>• Wifi</div>'    : '' ?>
                        <?= $row['outlet']  === 'Yes' ? '<div>• Outlets</div>' : '' ?>
                        <?= $row['aircon']  === 'Yes' ? '<div>• Aircon</div>'  : '' ?>
                        <?= $row['parking'] === 'Yes' ? '<div>• Parking</div>' : '' ?>
                    </td>
                    <td>
                        <div class="action-cell">
                            <a href="admin_edit_place.php?id=<?= $row['id'] ?>" class="action-btn btn-edit">
                                ✏️ Edit &amp; Publish
                            </a>
                            <span class="hint">Configure cafe window before publishing</span>
                            <div class="action-row">
                                <button class="action-btn btn-approve"
                                        onclick="reviewPlace(<?= $row['id'] ?>, 'approved', '')">
                                    Quick Approve
                                </button>
                                <input type="text" id="reason-<?= $row['id'] ?>"
                                       class="reject-input" placeholder="Rejection reason">
                                <button class="action-btn btn-reject"
                                        onclick="reviewPlace(<?= $row['id'] ?>, 'rejected',
                                            document.getElementById('reason-<?= $row['id'] ?>').value)">
                                    Reject
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="tab-locations" class="tab-content">
        <div class="section-title">All Locations</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th><th>Location</th><th>Status</th>
                    <th>Hours</th><th>Reviewed By</th><th>Reviewed At</th><th>Notes / Edit</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($all_places)): ?>
                <tr><td colspan="7" style="text-align:center;color:#888;padding:20px;">No locations found.</td></tr>
            <?php else: ?>
                <?php foreach ($all_places as $row): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                    <td style="font-size:0.82rem;color:#555;">
                        <?php if (!empty($row['hours_weekday'])): ?>
                            <?= htmlspecialchars($row['hours_weekday']) ?>
                            <?= !empty($row['hours_weekend']) ? '<br>' . htmlspecialchars($row['hours_weekend']) : '' ?>
                        <?php else: ?>
                            <em style="color:#bbb;">Not set</em>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['reviewer'] ? htmlspecialchars($row['reviewer']) : '—' ?></td>
                    <td><?= $row['reviewed_at'] ? date('M d, Y', strtotime($row['reviewed_at'])) : '—' ?></td>
                    <td>
                        <?= !empty($row['rejection_reason']) ? htmlspecialchars($row['rejection_reason']) : '—' ?>
                        <br><a href="admin_edit_place.php?id=<?= $row['id'] ?>" style="font-size:0.8rem;color:#062b53;">✏️ Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="tab-users" class="tab-content">
        <div class="section-title">User Accounts</div>

        <div class="role-filter-bar">
            <span>Filter by Role:</span>
            <a href="?role_filter=all"
               class="role-filter-btn <?= $role_filter === 'all'   ? 'active' : '' ?>">
                All <span class="role-filter-count">(<?= $count_all ?>)</span>
            </a>
            <a href="?role_filter=Admin"
               class="role-filter-btn <?= $role_filter === 'Admin' ? 'active' : '' ?>">
                Admins <span class="role-filter-count">(<?= $count_admin ?>)</span>
            </a>
            <a href="?role_filter=User"
               class="role-filter-btn <?= $role_filter === 'User'  ? 'active' : '' ?>">
                Users <span class="role-filter-count">(<?= $count_user ?>)</span>
            </a>
        </div>

        <table>
            <thead>
                <tr><th>ID</th><th>Avatar</th><th>Username</th><th>Role</th><th>Joined</th></tr>
            </thead>
            <tbody>
            <?php if (empty($all_users)): ?>
                <tr><td colspan="5" style="text-align:center;color:#888;padding:20px;">No accounts found.</td></tr>
            <?php else: ?>
                <?php foreach ($all_users as $row): ?>
                <tr>
                    <td>#<?= $row['account_id'] ?></td>
                    <td>
                        <?php if (!empty($row['profile_pic'])): ?>
                            <img src="<?= htmlspecialchars($row['profile_pic']) ?>"
                                 alt="<?= htmlspecialchars($row['username']) ?>"
                                 class="user-avatar">
                        <?php else: ?>
                            <span class="user-avatar-init">
                                <?= strtoupper(substr($row['username'], 0, 1)) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:600;"><?= htmlspecialchars($row['username']) ?></td>
                    <td><span class="badge badge-<?= strtolower($row['Type']) ?>"><?= htmlspecialchars($row['Type']) ?></span></td>
                    <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}

(function () {
    if (new URLSearchParams(window.location.search).has('role_filter')) {
        switchTab('users', document.querySelectorAll('.tab-btn')[2]);
    }
})();

function reviewPlace(id, status, reason) {
    const msg = status === 'approved'
        ? 'Quick-approve this proposal? It will be visible across the site without editing.'
        : 'Reject this proposal?';
    if (!confirm(msg)) return;

    fetch('review_place.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:    `id=${id}&status=${encodeURIComponent(status)}&reason=${encodeURIComponent(reason)}`
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) location.reload();
        else alert('Error: ' + d.message);
    })
    .catch(() => alert('Network error. Please try again.'));
}
</script>

