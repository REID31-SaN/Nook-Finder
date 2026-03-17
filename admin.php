<?php
session_start();
include 'config.php';

// Block non-admins
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if the logged-in user is an Admin
$stmt = $conn->prepare("SELECT Type FROM accounts WHERE account_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($userType);
$stmt->fetch();
$stmt->close();

if ($userType !== 'Admin') {
    header('Location: index.php');
    exit;
}
?>

<?php include 'header.php'; ?>

<style>
    .admin-wrapper { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
    .admin-tabs { display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
    .tab-btn { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; background: #eee; color: #333; }
    .tab-btn.active { background: #062b53; color: white; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    th { background: #062b53; color: white; padding: 12px 14px; text-align: left; }
    td { padding: 10px 14px; border-bottom: 1px solid #eee; vertical-align: middle; }
    tr:hover { background: #f9f9f9; }
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    .badge-pending  { background: #fff3cd; color: #856404; }
    .badge-approved { background: #d1e7dd; color: #0a5c36; }
    .badge-rejected { background: #f8d7da; color: #842029; }
    .badge-admin { background: #062b53; color: white; }
    .badge-user  { background: #e2e8f0; color: #333; }
    .action-btn { padding: 6px 14px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600; }
    .btn-approve { background: #198754; color: white; }
    .btn-reject  { background: #dc3545; color: white; }
    .section-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 16px; color: #062b53; }
    .reject-reason { padding: 6px 10px; border: 1px solid #ccc; border-radius:6px; font-size:0.85rem; width: 180px; }
</style>

<div class="admin-wrapper">
    <h1 style="margin-bottom: 6px;">Admin Panel</h1>
    <p style="color: #888; margin-bottom: 24px;">Manage proposals, locations, and user accounts.</p>

    <div class="admin-tabs">
        <button class="tab-btn active" onclick="switchTab('pending', event)">Pending Proposals</button>
        <button class="tab-btn" onclick="switchTab('locations', event)">All Locations</button>
        <button class="tab-btn" onclick="switchTab('users', event)">User Accounts</button>
    </div>

    <!-- ========== PENDING PROPOSALS ========== -->
    <div id="tab-pending" class="tab-content active">
        <div class="section-title">Pending Proposals</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Proposed By</th>
                    <th>Submitted</th>
                    <th>Coordinates</th>
                    <th>Amenities</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("
                    SELECT p.*, a.username 
                    FROM places p 
                    LEFT JOIN accounts a ON p.proposed_by = a.account_id 
                    WHERE p.status = 'pending'
                    ORDER BY p.created_at DESC
                ");
                if ($result->num_rows === 0): ?>
                    <tr><td colspan="7" style="text-align:center; color:#888; padding:20px;">No pending proposals.</td></tr>
                <?php else:
                    while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($row['name']) ?></b><br><small style="color:#888;"><?= htmlspecialchars($row['description']) ?></small></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= $row['username'] ? htmlspecialchars($row['username']) : '<i style="color:#aaa;">Guest</i>' ?></td>
                        <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                        <td style="font-size: 0.8rem; color: #555;">
    📍                      <?= $row['latitude'] ?>, <?= $row['longitude'] ?>
                        </td>
                        <td style="font-size: 0.82rem;">
                            <?= $row['wifi']    === 'Yes' ? '<div>• Wifi</div>' : '' ?>
                            <?= $row['outlet']  === 'Yes' ? '<div>• Outlets</div>' : '' ?>
                            <?= $row['aircon']  === 'Yes' ? '<div>• Aircon</div>' : '' ?>
                            <?= $row['parking'] === 'Yes' ? '<div>• Parking</div>' : '' ?>
                        </td>
                        <td style="display:flex; gap:8px; align-items:center; flex-wrap:nowrap; white-space:nowrap;">
                            <button class="action-btn btn-approve" onclick="reviewPlace(<?= $row['id'] ?>, 'approved', '')">Approve</button>
                            <input type="text" id="reason-<?= $row['id'] ?>" class="reject-reason" placeholder="Reason (optional)">
                            <button class="action-btn btn-reject" onclick="reviewPlace(<?= $row['id'] ?>, 'rejected', document.getElementById('reason-<?= $row['id'] ?>').value)">Reject</button>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ========== ALL LOCATIONS ========== -->
    <div id="tab-locations" class="tab-content">
        <div class="section-title">All Locations</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Reviewed By</th>
                    <th>Reviewed At</th>
                    <th>Accepted/Rejection Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("
                    SELECT p.*, a.username 
                    FROM places p 
                    LEFT JOIN accounts a ON p.reviewed_by = a.account_id
                    ORDER BY p.created_at DESC
                ");
                while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><b><?= htmlspecialchars($row['name']) ?></b></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                    <td><?= $row['username'] ? htmlspecialchars($row['username']) : '<i style="color:#aaa;">—</i>' ?></td>
                    <td><?= $row['reviewed_at'] ? date('M d, Y', strtotime($row['reviewed_at'])) : '—' ?></td>
                    <td><?= $row['rejection_reason'] ? htmlspecialchars($row['rejection_reason']) : '—' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- ========== USER ACCOUNTS ========== -->
    <div id="tab-users" class="tab-content">
        <div class="section-title">User Accounts</div>
        <table>
            <thead>
                <tr>
                    <th>Account ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM accounts ORDER BY created_at DESC");
                while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['account_id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><span class="badge badge-<?= strtolower($row['Type']) ?>"><?= htmlspecialchars($row['Type']) ?></span></td>
                    <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function switchTab(tab, event) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');
}

function reviewPlace(id, status, reason) {
    if (status === 'rejected' && !confirm('Reject this proposal?')) return;
    if (status === 'approved' && !confirm('Approve this proposal? It will appear on the map.')) return;

    fetch('review_place.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&status=' + status + '&reason=' + encodeURIComponent(reason)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(status === 'approved' ? '✅ Location approved!' : '❌ Proposal rejected.');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>