<?php
require 'db.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $severity    = $_POST['severity'] ?? '';
    $service     = trim($_POST['service'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $reported_by = trim($_POST['reported_by'] ?? '');

    if ($title && $severity && $service && $reported_by) {
        $stmt = $pdo->prepare(
            "INSERT INTO incidents (title, severity, service, description, reported_by)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $severity, $service, $description, $reported_by]);
        $success = "Incident logged successfully. ID: " . $pdo->lastInsertId();
    } else {
        $error = "Please fill all required fields.";
    }
}

require 'header.php';
?>

<div class="page-header">
    <h1>Log New Incident</h1>
    <p>Report a production or service incident for tracking and response</p>
</div>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

<div class="form-card">
    <form method="POST">
        <div class="form-group">
            <label>Incident Title *</label>
            <input type="text" name="title" placeholder="e.g. Payment API response time degraded" required>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label>Severity *</label>
                <select name="severity" required>
                    <option value="">-- Select Severity --</option>
                    <option value="P1">P1 - Critical (Production down)</option>
                    <option value="P2">P2 - High (Degraded performance)</option>
                    <option value="P3">P3 - Medium (Minor impact)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Affected Service *</label>
                <input type="text" name="service" placeholder="e.g. payment-api, auth-service" required>
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" placeholder="Describe the symptoms, impact, and any initial observations..."></textarea>
        </div>
        <div class="form-group">
            <label>Reported By *</label>
            <input type="text" name="reported_by" placeholder="Your username or email" required>
        </div>
        <button type="submit" class="btn btn-primary">&#128680; Log Incident</button>
    </form>
</div>

<?php require 'footer.php'; ?>
