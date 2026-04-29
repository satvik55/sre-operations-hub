<?php
require 'db.php';

$total     = $pdo->query("SELECT COUNT(*) FROM incidents")->fetchColumn();
$open_p1   = $pdo->query("SELECT COUNT(*) FROM incidents WHERE severity='P1' AND status != 'resolved'")->fetchColumn();
$open_p2   = $pdo->query("SELECT COUNT(*) FROM incidents WHERE severity='P2' AND status != 'resolved'")->fetchColumn();
$resolved  = $pdo->query("SELECT COUNT(*) FROM incidents WHERE status='resolved'")->fetchColumn();

$recent = $pdo->query(
    "SELECT * FROM incidents ORDER BY created_at DESC LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

$oncall_today = $pdo->query(
    "SELECT * FROM oncall WHERE CURDATE() BETWEEN start_date AND end_date LIMIT 1"
)->fetch(PDO::FETCH_ASSOC);

$slo_breach = $pdo->query(
    "SELECT COUNT(*) FROM slos WHERE current_percent < target_percent"
)->fetchColumn();

require 'header.php';
?>

<div class="page-header">
    <h1>Operations Dashboard</h1>
    <p>Real-time overview of service reliability and active incidents</p>
</div>

<div class="cards-grid">
    <div class="card">
        <h3>Total Incidents</h3>
        <div class="value"><?= $total ?></div>
    </div>
    <div class="card" style="border-color: #6e1c1c;">
        <h3>Open P1 Incidents</h3>
        <div class="value" style="color: #f85149;"><?= $open_p1 ?></div>
    </div>
    <div class="card" style="border-color: #5a3e00;">
        <h3>Open P2 Incidents</h3>
        <div class="value" style="color: #d29922;"><?= $open_p2 ?></div>
    </div>
    <div class="card" style="border-color: #1f4a1f;">
        <h3>Resolved</h3>
        <div class="value" style="color: #3fb950;"><?= $resolved ?></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 320px; gap: 20px;">

    <div class="table-wrap">
        <h2>Recent Incidents</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Service</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Reported</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td style="color:#58a6ff;"><?= htmlspecialchars($r['service']) ?></td>
                    <td><span class="badge badge-<?= strtolower($r['severity']) ?>"><?= $r['severity'] ?></span></td>
                    <td><span class="badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
                    <td style="color:#8b949e; font-size:12px;"><?= date('M d, H:i', strtotime($r['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div>
        <div class="card" style="margin-bottom: 16px;">
            <h3 style="margin-bottom:12px;">&#128222; On-Call Now</h3>
            <?php if ($oncall_today): ?>
                <div style="font-size:18px; font-weight:700; color:#e6edf3;"><?= htmlspecialchars($oncall_today['engineer_name']) ?></div>
                <div style="color:#58a6ff; font-size:13px; margin-top:4px;"><?= htmlspecialchars($oncall_today['team']) ?></div>
                <div style="color:#8b949e; font-size:12px; margin-top:8px;">&#128222; <?= htmlspecialchars($oncall_today['contact']) ?></div>
                <div style="color:#8b949e; font-size:11px; margin-top:4px;">
                    Until <?= date('M d', strtotime($oncall_today['end_date'])) ?>
                </div>
            <?php else: ?>
                <div style="color:#8b949e;">No on-call assigned today</div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3 style="margin-bottom:12px;">&#127919; SLO Status</h3>
            <?php if ($slo_breach > 0): ?>
                <div style="color:#f85149; font-weight:700; font-size:16px;"><?= $slo_breach ?> SLO<?= $slo_breach > 1 ? 's' : '' ?> breached</div>
                <div style="color:#8b949e; font-size:12px; margin-top:4px;">
                    <a href="manage_slo.php" style="color:#58a6ff;">View SLO Tracker &rarr;</a>
                </div>
            <?php else: ?>
                <div style="color:#3fb950; font-weight:700; font-size:16px;">All SLOs healthy</div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php require 'footer.php'; ?>
