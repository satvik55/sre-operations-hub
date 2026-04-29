<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRE Operations Hub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #0d1117; color: #e6edf3; display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 240px; min-height: 100vh; background: #161b22;
            border-right: 1px solid #30363d; padding: 0; position: fixed; top: 0; left: 0;
        }
        .sidebar-brand {
            padding: 20px 24px; border-bottom: 1px solid #30363d;
            font-size: 16px; font-weight: 700; color: #58a6ff;
            letter-spacing: 0.5px;
        }
        .sidebar-brand span { color: #3fb950; }
        .sidebar-label {
            padding: 20px 24px 8px; font-size: 11px; font-weight: 600;
            color: #8b949e; text-transform: uppercase; letter-spacing: 1px;
        }
        .sidebar a {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 24px; color: #8b949e; text-decoration: none;
            font-size: 14px; transition: all 0.2s;
        }
        .sidebar a:hover { background: #21262d; color: #e6edf3; }
        .sidebar a.active { background: #1f3a5c; color: #58a6ff; border-right: 3px solid #58a6ff; }
        .sidebar .icon { width: 18px; text-align: center; }

        /* Main content */
        .main { margin-left: 240px; flex: 1; padding: 32px; }

        /* Page header */
        .page-header { margin-bottom: 28px; }
        .page-header h1 { font-size: 22px; font-weight: 700; color: #e6edf3; }
        .page-header p { color: #8b949e; font-size: 14px; margin-top: 4px; }

        /* Cards */
        .card {
            background: #161b22; border: 1px solid #30363d;
            border-radius: 8px; padding: 20px; margin-bottom: 20px;
        }
        .card h3 { font-size: 14px; color: #8b949e; margin-bottom: 4px; }
        .card .value { font-size: 32px; font-weight: 700; color: #e6edf3; }

        .cards-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }

        /* Tables */
        .table-wrap { background: #161b22; border: 1px solid #30363d; border-radius: 8px; overflow: hidden; }
        .table-wrap h2 { padding: 16px 20px; font-size: 15px; border-bottom: 1px solid #30363d; color: #e6edf3; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600;
             color: #8b949e; text-transform: uppercase; background: #0d1117; border-bottom: 1px solid #30363d; }
        td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #21262d; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #1c2128; }

        /* Badges */
        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
        }
        .badge-p1 { background: #3d1515; color: #f85149; border: 1px solid #6e1c1c; }
        .badge-p2 { background: #2d2000; color: #d29922; border: 1px solid #5a3e00; }
        .badge-p3 { background: #1a2d1a; color: #3fb950; border: 1px solid #1f4a1f; }
        .badge-open { background: #3d1515; color: #f85149; }
        .badge-investigating { background: #2d2000; color: #d29922; }
        .badge-resolved { background: #1a2d1a; color: #3fb950; }
        .badge-general { background: #1a2040; color: #58a6ff; }

        /* Forms */
        .form-card { background: #161b22; border: 1px solid #30363d; border-radius: 8px; padding: 28px; max-width: 680px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #8b949e; margin-bottom: 6px; }
        input, select, textarea {
            width: 100%; padding: 9px 12px; background: #0d1117;
            border: 1px solid #30363d; border-radius: 6px; color: #e6edf3;
            font-size: 14px; font-family: inherit; transition: border 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none; border-color: #58a6ff; box-shadow: 0 0 0 3px rgba(88,166,255,0.1);
        }
        textarea { resize: vertical; min-height: 100px; }
        .btn {
            padding: 9px 20px; border: none; border-radius: 6px; cursor: pointer;
            font-size: 14px; font-weight: 600; transition: all 0.2s;
        }
        .btn-primary { background: #238636; color: #fff; }
        .btn-primary:hover { background: #2ea043; }
        .btn-danger { background: #da3633; color: #fff; }
        .btn-danger:hover { background: #f85149; }

        /* Alert */
        .alert {
            padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;
        }
        .alert-success { background: #1a2d1a; border: 1px solid #1f4a1f; color: #3fb950; }
        .alert-error { background: #3d1515; border: 1px solid #6e1c1c; color: #f85149; }

        /* SLO bar */
        .slo-bar { background: #30363d; border-radius: 4px; height: 8px; margin-top: 6px; }
        .slo-fill { height: 8px; border-radius: 4px; }
        .slo-good { background: #3fb950; }
        .slo-warn { background: #d29922; }
        .slo-bad { background: #f85149; }

        select option { background: #161b22; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">&#9881; SRE <span>OpsHub</span></div>
    <div class="sidebar-label">Overview</div>
    <a href="index.php" class="<?= $current==='index.php' ? 'active' : '' ?>">
        <span class="icon">&#128202;</span> Dashboard
    </a>
    <div class="sidebar-label">Incidents</div>
    <a href="log_incident.php" class="<?= $current==='log_incident.php' ? 'active' : '' ?>">
        <span class="icon">&#43;</span> Log Incident
    </a>
    <a href="view_incidents.php" class="<?= $current==='view_incidents.php' ? 'active' : '' ?>">
        <span class="icon">&#128680;</span> All Incidents
    </a>
    <div class="sidebar-label">Reliability</div>
    <a href="manage_slo.php" class="<?= $current==='manage_slo.php' ? 'active' : '' ?>">
        <span class="icon">&#127919;</span> SLO Tracker
    </a>
    <a href="runbooks.php" class="<?= $current==='runbooks.php' ? 'active' : '' ?>">
        <span class="icon">&#128214;</span> Runbooks
    </a>
    <div class="sidebar-label">Team</div>
    <a href="oncall.php" class="<?= $current==='oncall.php' ? 'active' : '' ?>">
        <span class="icon">&#128222;</span> On-Call Schedule
    </a>
</div>
<div class="main">
