CREATE TABLE IF NOT EXISTS incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    severity ENUM('P1','P2','P3') NOT NULL,
    status ENUM('open','investigating','resolved') DEFAULT 'open',
    service VARCHAR(100) NOT NULL,
    description TEXT,
    reported_by VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS slos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    metric VARCHAR(100) NOT NULL,
    target_percent DECIMAL(5,2) NOT NULL,
    current_percent DECIMAL(5,2) NOT NULL,
    period VARCHAR(50) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS runbooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    service VARCHAR(100) NOT NULL,
    severity_level ENUM('P1','P2','P3','General') NOT NULL,
    steps TEXT NOT NULL,
    created_by VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS oncall (
    id INT AUTO_INCREMENT PRIMARY KEY,
    engineer_name VARCHAR(100) NOT NULL,
    team VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    contact VARCHAR(100) NOT NULL
);

-- Sample incidents
INSERT INTO incidents (title, severity, status, service, description, reported_by) VALUES
('Payment service latency spike', 'P1', 'investigating', 'payment-api', 'p99 latency crossed 5s threshold on payment-api in prod', 'satvik.b'),
('Auth token validation failing', 'P1', 'open', 'auth-service', 'JWT validation returning 401 for valid tokens intermittently', 'priya.k'),
('Database connection pool exhausted', 'P2', 'resolved', 'user-service', 'MySQL max_connections hit during peak traffic, increased pool size', 'rahul.m'),
('CDN cache miss rate elevated', 'P2', 'resolved', 'cdn', 'Cache hit rate dropped to 40% due to cache invalidation bug', 'ankit.s'),
('Disk usage warning on log nodes', 'P3', 'open', 'logging-infra', 'Log retention not running, disk at 85% capacity', 'satvik.b');

-- Sample SLOs
INSERT INTO slos (service_name, metric, target_percent, current_percent, period) VALUES
('payment-api', 'Availability', 99.95, 99.82, '30 days'),
('auth-service', 'Availability', 99.99, 99.97, '30 days'),
('user-service', 'Availability', 99.90, 99.94, '30 days'),
('payment-api', 'Latency p99 < 500ms', 99.50, 98.10, '7 days'),
('auth-service', 'Latency p99 < 200ms', 99.50, 99.63, '7 days');

-- Sample runbooks
INSERT INTO runbooks (title, service, severity_level, steps, created_by) VALUES
('Payment API High Latency Response', 'payment-api', 'P1',
'1. Check Grafana dashboard for latency spike timeline\n2. kubectl get pods -n production | grep payment\n3. Check pod logs: kubectl logs <pod> -n production --tail=100\n4. Verify DB connection pool: SELECT count(*) FROM information_schema.processlist\n5. Scale up replicas if needed: kubectl scale deployment payment-api --replicas=6\n6. If DB issue, failover to read replica\n7. Page on-call DBA if not resolved in 15 min',
'satvik.b'),
('Auth Service Token Failures', 'auth-service', 'P1',
'1. Check auth-service error rate in CloudWatch\n2. Verify JWT secret rotation did not cause key mismatch\n3. kubectl describe pod <auth-pod> for recent events\n4. Rollback last deployment if it coincides with incident: kubectl rollout undo deployment/auth-service\n5. Check Redis cache for token blacklist corruption\n6. Monitor error rate for 10 min post-fix',
'priya.k'),
('High Disk Usage on Log Nodes', 'logging-infra', 'P3',
'1. SSH into log node and run: df -h\n2. Find large files: find /var/log -size +100M\n3. Check log rotation config: cat /etc/logrotate.conf\n4. Manually rotate: logrotate -f /etc/logrotate.conf\n5. If still critical, archive old logs to S3: aws s3 cp /var/log/old s3://logs-archive/\n6. Set up CloudWatch alarm for disk > 80%',
'ankit.s');

-- Sample on-call
INSERT INTO oncall (engineer_name, team, start_date, end_date, contact) VALUES
('Satvik Bodke', 'Platform Engineering', '2026-04-28', '2026-05-05', '+91-9876543210'),
('Priya Kulkarni', 'Backend Services', '2026-04-28', '2026-05-05', '+91-9876543211'),
('Rahul Mehta', 'Database & Storage', '2026-04-21', '2026-04-27', '+91-9876543212'),
('Ankit Sharma', 'Infrastructure', '2026-05-05', '2026-05-12', '+91-9876543213');
