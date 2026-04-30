<?php
require 'db.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service  = trim($_POST['service_name'] ?? '');
    $metric   = trim($_POST['metric'] ?? '');
    $target   = $_POST['target_percent'] ?? '';
    $current  = $_POST['current_percent'] ?? '';
    $period   = trim($_POST['period'] ?? '');

    if ($service && $metric && $target && $current && $period) {
        $stmt = $pdo->prepare(
            "INSERT INTO slos (service_name, metric, target_percent, current_percent, period)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$service, $metric, $target, $current, $period]);
        $success = "SLO added successfully.";
    } else {
        $error = "Please fill all required fields.";
    }
}

$slos = $pdo->query("SELECT * FROM slos ORDER BY service_name")->fetchAll(PDO::FETCH_ASSOC);

require 'header.php';
?>

<div class="page-header">
    <h1>SLO Tracker</h1>
    <p>Monitor Service Level Objectives and identify reliability gaps</p>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start;">

    <div class="table-wrap">
        <h2>Current SLOs</h2>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Metric</th>
                    <th>Target</th>
                    <th>Current</th>
                    <th>Period</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($slos as $slo):
                    $diff = $slo['current_percent'] - $slo['target_percent'];
                    $pct  = min(100, ($slo['current_percent'] / $slo['target_percent']) * 100);
                    $bar_class = $diff >= 0 ? 'slo-good' : ($diff >= -0.5 ? 'slo-warn' : 'slo-bad');
                    $status_label = $diff >= 0 ? 'Healthy' : 'Breached';
                    $status_color = $diff >= 0 ? '#3fb950' : '#f85149';
                ?>
                <tr>
                    <td style="color:#58a6ff; font-weight:600;"><?= htmlspecialchars($slo['service_name']) ?></td>
                    <td><?= htmlspecialchars($slo['metric']) ?></td>
                    <td><?= $slo['target_percent'] ?>%</td>
                    <td>
                        <div><?= $slo['current_percent'] ?>%</div>
                        <div class="slo-bar"><div class="slo-fill <?= $bar_class ?>" style="width: <?= $pct ?>%;"></div></div>
                    </td>
                    <td style="color:#8b949e; font-size:12px;"><?= htmlspecialchars($slo['period']) ?></td>
                    <td style="color:<?= $status_color ?>; font-weight:600; font-size:12px;">
                        <?= $status_label ?>
                        <?php if ($diff < 0): ?>
                            <div style="color:#8b949e; font-weight:400;"><?= number_format($diff, 2) ?>%</div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-card" style="max-width:100%;">
        <h2 style="font-size:15px; margin-bottom:20px; color:#e6edf3;">Add New SLO</h2>
        <form method="POST">
            <div class="form-group">
                <label>Service Name *</label>
                <input type="text" name="service_name" placeholder="e.g. payment-api" required>
            </div>
            <div class="form-group">
                <label>Metric *</label>
                <input type="text" name="metric" placeholder="e.g. Availability, Latency p99 < 500ms" required>
            </div>
            <div class="form-group">
                <label>Target % *</label>
                <input type="number" name="target_percent" step="0.01" min="0" max="100" placeholder="e.g. 99.95" required>
            </div>
            <div class="form-group">
                <label>Current % *</label>
                <input type="number" name="current_percent" step="0.01" min="0" max="100" placeholder="e.g. 99.87" required>
            </div>
            <div class="form-group">
                <label>Period *</label>
                <input type="text" name="period" placeholder="e.g. 30 days, 7 days" required>
            </div>
            <button type="submit" class="btn btn-primary">Add SLO</button>
        </form>
    </div>

</div>

<?php require 'footer.php'; ?>
