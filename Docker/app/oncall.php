<?php
require 'db.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['engineer_name'] ?? '');
    $team    = trim($_POST['team'] ?? '');
    $start   = $_POST['start_date'] ?? '';
    $end     = $_POST['end_date'] ?? '';
    $contact = trim($_POST['contact'] ?? '');

    if ($name && $team && $start && $end && $contact) {
        $stmt = $pdo->prepare(
            "INSERT INTO oncall (engineer_name, team, start_date, end_date, contact)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $team, $start, $end, $contact]);
        $success = "On-call rotation added successfully.";
    } else {
        $error = "Please fill all required fields.";
    }
}

$today   = date('Y-m-d');
$active  = $pdo->query("SELECT * FROM oncall WHERE '$today' BETWEEN start_date AND end_date ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$all     = $pdo->query("SELECT * FROM oncall ORDER BY start_date DESC")->fetchAll(PDO::FETCH_ASSOC);

require 'header.php';
?>

<div class="page-header">
    <h1>On-Call Schedule</h1>
    <p>Current on-call rotation and upcoming schedule</p>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

<?php if ($active): ?>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; margin-bottom: 28px;">
    <?php foreach ($active as $a): ?>
    <div class="card" style="border-color: #238636;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <span style="font-size:11px; font-weight:700; color:#3fb950; text-transform:uppercase;">On-Call Now</span>
            <span style="font-size:11px; color:#8b949e;"><?= date('M d', strtotime($a['start_date'])) ?> - <?= date('M d', strtotime($a['end_date'])) ?></span>
        </div>
        <div style="font-size:20px; font-weight:700; color:#e6edf3;"><?= htmlspecialchars($a['engineer_name']) ?></div>
        <div style="color:#58a6ff; font-size:13px; margin-top:4px;"><?= htmlspecialchars($a['team']) ?></div>
        <div style="color:#8b949e; font-size:13px; margin-top:8px;">&#128222; <?= htmlspecialchars($a['contact']) ?></div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start;">

    <div class="table-wrap">
        <h2>Full Rotation Schedule</h2>
        <table>
            <thead>
                <tr><th>Engineer</th><th>Team</th><th>Start</th><th>End</th><th>Contact</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($all as $oc):
                    $is_active = ($today >= $oc['start_date'] && $today <= $oc['end_date']);
                    $is_past   = $today > $oc['end_date'];
                ?>
                <tr>
                    <td style="font-weight:600;"><?= htmlspecialchars($oc['engineer_name']) ?></td>
                    <td style="color:#58a6ff;"><?= htmlspecialchars($oc['team']) ?></td>
                    <td style="color:#8b949e;"><?= date('M d, Y', strtotime($oc['start_date'])) ?></td>
                    <td style="color:#8b949e;"><?= date('M d, Y', strtotime($oc['end_date'])) ?></td>
                    <td style="color:#8b949e;"><?= htmlspecialchars($oc['contact']) ?></td>
                    <td>
                        <?php if ($is_active): ?>
                            <span class="badge" style="background:#1a2d1a; color:#3fb950; border:1px solid #1f4a1f;">Active</span>
                        <?php elseif ($is_past): ?>
                            <span class="badge" style="background:#21262d; color:#8b949e;">Past</span>
                        <?php else: ?>
                            <span class="badge" style="background:#1a2040; color:#58a6ff;">Upcoming</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-card" style="max-width:100%;">
        <h2 style="font-size:15px; margin-bottom:20px; color:#e6edf3;">Add Rotation</h2>
        <form method="POST">
            <div class="form-group">
                <label>Engineer Name *</label>
                <input type="text" name="engineer_name" placeholder="Full name" required>
            </div>
            <div class="form-group">
                <label>Team *</label>
                <input type="text" name="team" placeholder="e.g. Platform Engineering" required>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group">
                    <label>Start Date *</label>
                    <input type="date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label>End Date *</label>
                    <input type="date" name="end_date" required>
                </div>
            </div>
            <div class="form-group">
                <label>Contact (phone/email) *</label>
                <input type="text" name="contact" placeholder="e.g. +91-9876543210" required>
            </div>
            <button type="submit" class="btn btn-primary">Add to Schedule</button>
        </form>
    </div>

</div>

<?php require 'footer.php'; ?>
