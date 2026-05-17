<?php
session_start();

// Simple admin check (implement proper authentication)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$logFile = '../logs/error.log';
$logs = [];

if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $logs = array_reverse(explode("\n", $content)); // Show newest first
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Logs - Ayu Hotel Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: #8B4513;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #8B4513;
        }
        
        .controls {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .controls input, .controls select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1;
        }
        
        .controls button {
            padding: 10px 20px;
            background: #8B4513;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .controls button:hover {
            background: #5C2E0B;
        }
        
        .log-entry {
            background: white;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        
        .log-entry.ERROR { border-left-color: #dc3545; background: #fff5f5; }
        .log-entry.EXCEPTION { border-left-color: #ff6b6b; background: #fff0f0; }
        .log-entry.WARNING { border-left-color: #ffc107; background: #fffbf0; }
        .log-entry.INFO { border-left-color: #17a2b8; background: #f0f9ff; }
        .log-entry.SUCCESS { border-left-color: #28a745; background: #f0fff4; }
        .log-entry.QUERY { border-left-color: #6f42c1; background: #f8f0ff; }
        
        .log-timestamp {
            color: #666;
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .log-message {
            margin-bottom: 5px;
        }
        
        .log-data {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            margin-top: 5px;
            font-size: 12px;
            overflow-x: auto;
        }
        
        .empty-log {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 8px;
            color: #666;
        }
        
        .empty-log i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #28a745;
        }
        
        .btn-clear {
            background: #dc3545;
        }
        
        .btn-clear:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Error Log Viewer</h1>
            <div>
                <a href="dashboard.php" style="color: white; margin-right: 15px;">← Back to Dashboard</a>
                <button onclick="clearLogs()" class="btn-clear" style="background: #dc3545; padding: 8px 15px;">Clear Logs</button>
            </div>
        </div>
        
        <div class="stats" id="stats">
            <!-- Stats will be loaded via JavaScript -->
        </div>
        
        <div class="controls">
            <input type="text" id="searchInput" placeholder="Search logs..." onkeyup="filterLogs()">
            <select id="filterLevel" onchange="filterLogs()">
                <option value="all">All Levels</option>
                <option value="ERROR">ERROR</option>
                <option value="EXCEPTION">EXCEPTION</option>
                <option value="WARNING">WARNING</option>
                <option value="INFO">INFO</option>
                <option value="SUCCESS">SUCCESS</option>
            </select>
            <button onclick="refreshLogs()">🔄 Refresh</button>
            <button onclick="downloadLogs()">📥 Download Logs</button>
        </div>
        
        <div id="logEntries">
            <?php if (empty($logs) || (count($logs) == 1 && empty($logs[0]))): ?>
                <div class="empty-log">
                    <i>✅</i>
                    <h2>No errors logged!</h2>
                    <p>Your website is running smoothly. No issues have been detected.</p>
                    <small>This is a good sign! Keep up the good work.</small>
                </div>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <?php if (!empty(trim($log))): ?>
                        <?php
                        // Parse log entry
                        preg_match('/\[(.*?)\]\s+\[(.*?)\]\s+(.*)/', $log, $matches);
                        $timestamp = $matches[1] ?? '';
                        $level = $matches[2] ?? 'INFO';
                        $message = $matches[3] ?? $log;
                        
                        // Extract data if exists
                        $data = null;
                        if (preg_match('/- Data: (.*)/', $message, $dataMatch)) {
                            $data = json_decode($dataMatch[1], true);
                            $message = str_replace($dataMatch[0], '', $message);
                        }
                        ?>
                        <div class="log-entry <?php echo $level; ?>" data-level="<?php echo $level; ?>" data-search="<?php echo htmlspecialchars($message); ?>">
                            <div class="log-timestamp"><?php echo htmlspecialchars($timestamp); ?></div>
                            <div class="log-message"><strong>[<?php echo $level; ?>]</strong> <?php echo htmlspecialchars($message); ?></div>
                            <?php if ($data): ?>
                                <div class="log-data">
                                    <strong>Additional Data:</strong>
                                    <pre><?php print_r($data); ?></pre>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Calculate and display stats
        function updateStats() {
            const entries = document.querySelectorAll('.log-entry');
            const total = entries.length;
            const errors = document.querySelectorAll('.log-entry.ERROR, .log-entry.EXCEPTION').length;
            const warnings = document.querySelectorAll('.log-entry.WARNING').length;
            const info = document.querySelectorAll('.log-entry.INFO, .log-entry.SUCCESS').length;
            
            document.getElementById('stats').innerHTML = `
                <div class="stat-card">
                    <h3>Total Log Entries</h3>
                    <div class="number">${total}</div>
                </div>
                <div class="stat-card">
                    <h3>Errors</h3>
                    <div class="number" style="color: #dc3545;">${errors}</div>
                </div>
                <div class="stat-card">
                    <h3>Warnings</h3>
                    <div class="number" style="color: #ffc107;">${warnings}</div>
                </div>
                <div class="stat-card">
                    <h3>Info/Success</h3>
                    <div class="number" style="color: #28a745;">${info}</div>
                </div>
            `;
        }
        
        // Filter logs
        function filterLogs() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const filterLevel = document.getElementById('filterLevel').value;
            
            const logs = document.querySelectorAll('.log-entry');
            logs.forEach(log => {
                const level = log.getAttribute('data-level');
                const searchText = log.getAttribute('data-search').toLowerCase();
                
                let levelMatch = filterLevel === 'all' || level === filterLevel;
                let searchMatch = searchTerm === '' || searchText.includes(searchTerm);
                
                log.style.display = levelMatch && searchMatch ? 'block' : 'none';
            });
        }
        
        // Refresh logs
        function refreshLogs() {
            location.reload();
        }
        
        // Download logs
        function downloadLogs() {
            window.location.href = 'download-logs.php';
        }
        
        // Clear logs (with confirmation)
        function clearLogs() {
            if (confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
                fetch('clear-logs.php', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Logs cleared successfully!');
                            location.reload();
                        } else {
                            alert('Failed to clear logs');
                        }
                    });
            }
        }
        
        // Auto-refresh every 30 seconds
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                refreshLogs();
            }
        }, 30000);
        
        // Update stats on load
        setTimeout(updateStats, 100);
    </script>
</body>
</html>
