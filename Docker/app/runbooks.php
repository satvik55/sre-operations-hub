<?php
require 'db.php';

$success = $error = '';
$view_id = isset($_GET['view']) ? (int)$_GET['view'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $service  = trim($_POST['service'] ?? '');
    $severity = $_POST['severity_level'] ?? '';
    $steps    = trim($_POST['steps'] ?? '');
    $created_by = trim($_POST['created_by'] ?? '');

    if ($title && $service && $severity && $steps && $created_by) {
        $stmt = $pdo->prepare(
            "INSERT INTO runbooks (title, service, severity_level, steps, created_by)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $service, $severity, $steps, $created_by]);
        $success = "Runbook saved successfully.";
    } else {
        $error = "Please fill all required fields.";
    }
}

$runbooks = $pdo->query("SELECT * FROM runbooks ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$viewed = null;
if ($view_id) {
    $s = $pdo->prepare("SELECT * FROM runbooks WHERE id = ?");
    $s->execute([$view_id]);
    $viewed = $s->fetch(PDO::FETCH_ASSOC);
}

require 'header.php';
?>

<div class="page-header">
    <h1>Runbooks</h1>
    <p>Step-by-step response procedures for incidents and operational tasks</p>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

<?php if ($viewed): ?>
<div class="card" style="max-width: 800px; margin-bottom: 24px;">
    <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:16px;">
        <div>
            <h2 style="font-size:18px; color:#e6edf3;"><?= htmlspecialchars($viewed['title']) ?></h2>
            <div style="margin-top:6px; display:flex; gap:8px; align-items:center;">
                <span style="color:#58a6ff; font-size:13px;"><?= htmlspecialchars($viewed['service']) ?></span>
                <span class="badge badge-<?= strtolower($viewed['severity_level']) ?>">
                    <?= $viewed['severity_level'] ?>
                </span>
                <span style="color:#8b949e; font-size:12px;">by <?= htmlspecialchars($viewed['created_by']) ?></span>
            </div>
        </div>
        <a href="runbooks.php" style="color:#8b949e; font-size:13px; text-decoration:none;">&times; Close</a>
    </div>
    <div style="background:#0d1117; border:1px solid #30363d; border-radius:6px; padding:16px;">
        <pre style="white-space:pre-wrap; font-family:'Segoe UI',sans-serif; font-size:14px; color:#e6edf3; line-height:1.7;"><?= htmlspecialchars($viewed['steps']) ?></pre>
    </div>
</div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start;">

    <div class="table-wrap">
        <h2>All Runbooks</h2>
        <table>
            <thead>
                <tr><th>Title</th><th>Service</th><th>Severity</th><th>Author</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($runbooks as $rb): ?>
                <tr>
                    <td style="font-weight:600;"><?= htmlspecialchars($rb['title']) ?></td>
                    <td style="color:#58a6ff;"><?= htmlspecialchars($rb['service']) ?></td>
                    <td><span class="badge badge-<?= strtolower($rb['severity_level']) ?>"><?= $rb['severity_level'] ?></span></td>
                    <td style="color:#8b949e;"><?= htmlspecialchars($rb['created_by']) ?></td>
                    <td><a href="?view=<?= $rb['id'] ?>" style="color:#58a6ff; font-size:13px; text-decoration:none;">View &rarr;</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-card" style="max-width:100%;">
        <h2 style="font-size:15px; margin-bottom:20px; color:#e6edf3;">Add Runbook</h2>
        <form method="POST">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" placeholder="e.g. High CPU on Auth Service" required>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group">
                    <label>Service *</label>
                    <input type="text" name="service" placeholder="e.g. auth-service" required>
                </div>
                <div class="form-group">
                    <label>Severity Level *</label>
                    <select name="severity_level" required>
                        <option value="">-- Select --</option>
                        <option value="P1">P1</option>
                        <option value="P2">P2</option>
                        <option value="P3">P3</option>
                        <option value="General">General</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Steps *</label>
                <textarea name="steps" placeholder="1. Check pod logs&#10;2. Verify DB connections&#10;3. Scale up if needed" style="min-height:140px;" required></textarea>
            </div>
            <div class="form-group">
                <label>Created By *</label>
                <input type="text" name="created_by" placeholder="Your username" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Runbook</button>
        </form>
    </div>

</div>

<?php require 'footer.php'; ?>
