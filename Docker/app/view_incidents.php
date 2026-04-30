<?php
require 'db.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE incidents SET status=?, resolved_at=? WHERE id=?");
    $resolved_at = ($_POST['new_status'] === 'resolved') ? date('Y-m-d H:i:s') : null;
    $stmt->execute([$_POST['new_status'], $resolved_at, $_POST['incident_id']]);
    header("Location: view_incidents.php");
    exit;
}

$filter_sev    = $_GET['severity'] ?? '';
$filter_status = $_GET['status'] ?? '';

$where = [];
$params = [];
if ($filter_sev)    { $where[] = "severity = ?";  $params[] = $filter_sev; }
if ($filter_status) { $where[] = "status = ?";    $params[] = $filter_status; }

$sql = "SELECT * FROM incidents" . ($where ? " WHERE " . implode(" AND ", $where) : "") . " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);

require 'header.php';
?>

<div class="page-header">
    <h1>All Incidents</h1>
    <p>Track, filter, and update all reported incidents</p>
</div>

<!-- Filters -->
<div style="display: flex; gap: 12px; margin-bottom: 20px; align-items: center;">
    <form method="GET" style="display: flex; gap: 12px;">
        <select name="severity" onchange="this.form.submit()" style="width:180px;">
            <option value="">All Severities</option>
            <option value="P1" <?= $filter_sev==='P1'?'selected':'' ?>>P1 - Critical</option>
            <option value="P2" <?= $filter_sev==='P2'?'selected':'' ?>>P2 - High</option>
            <option value="P3" <?= $filter_sev==='P3'?'selected':'' ?>>P3 - Medium</option>
        </select>
        <select name="status" onchange="this.form.submit()" style="width:180px;">
            <option value="">All Statuses</option>
            <option value="open" <?= $filter_status==='open'?'selected':'' ?>>Open</option>
            <option value="investigating" <?= $filter_status==='investigating'?'selected':'' ?>>Investigating</option>
            <option value="resolved" <?= $filter_status==='resolved'?'selected':'' ?>>Resolved</option>
        </select>
        <?php if ($filter_sev || $filter_status): ?>
            <a href="view_incidents.php" style="color:#58a6ff; font-size:13px; align-self:center;">Clear filters</a>
        <?php endif; ?>
    </form>
    <div style="margin-left: auto; color: #8b949e; font-size: 13px;"><?= count($incidents) ?> incidents</div>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Service</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Reported By</th>
                <th>Created</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($incidents as $inc): ?>
            <tr>
                <td style="color:#8b949e;"><?= $inc['id'] ?></td>
                <td>
                    <div style="font-weight:600;"><?= htmlspecialchars($inc['title']) ?></div>
                    <?php if ($inc['description']): ?>
                    <div style="color:#8b949e; font-size:12px; margin-top:2px;"><?= htmlspecialchars(substr($inc['description'], 0, 80)) ?>...</div>
                    <?php endif; ?>
                </td>
                <td style="color:#58a6ff;"><?= htmlspecialchars($inc['service']) ?></td>
                <td><span class="badge badge-<?= strtolower($inc['severity']) ?>"><?= $inc['severity'] ?></span></td>
                <td><span class="badge badge-<?= $inc['status'] ?>"><?= $inc['status'] ?></span></td>
                <td style="color:#8b949e;"><?= htmlspecialchars($inc['reported_by']) ?></td>
                <td style="color:#8b949e; font-size:12px;"><?= date('M d, H:i', strtotime($inc['created_at'])) ?></td>
                <td>
                    <?php if ($inc['status'] !== 'resolved'): ?>
                    <form method="POST" style="display:flex; gap:6px;">
                        <input type="hidden" name="incident_id" value="<?= $inc['id'] ?>">
                        <select name="new_status" style="width:130px; padding:5px 8px; font-size:12px;">
                            <option value="open" <?= $inc['status']==='open'?'selected':'' ?>>Open</option>
                            <option value="investigating" <?= $inc['status']==='investigating'?'selected':'' ?>>Investigating</option>
                            <option value="resolved">Resolved</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary" style="padding:5px 12px; font-size:12px;">Update</button>
                    </form>
                    <?php else: ?>
                        <span style="color:#3fb950; font-size:12px;">&#10003; Resolved <?= $inc['resolved_at'] ? date('M d', strtotime($inc['resolved_at'])) : '' ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'footer.php'; ?>
