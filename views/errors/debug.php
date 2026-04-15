<?php
$data = $data ?? [];
$queries = $queries ?? [];
$messages = $messages ?? [];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug - NovoFramework</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; background: #1a1a2e; color: #eee; padding: 20px; }
        .debug-container { max-width: 1200px; margin: 0 auto; }
        .debug-header { background: #e74c3c; padding: 20px; border-radius: 8px 8px 0 0; }
        .debug-header h1 { font-size: 24px; }
        .debug-section { background: #16213e; padding: 20px; margin-bottom: 10px; }
        .debug-section h3 { color: #e74c3c; margin-bottom: 15px; font-size: 14px; text-transform: uppercase; }
        .debug-table { width: 100%; border-collapse: collapse; }
        .debug-table th, .debug-table td { text-align: left; padding: 10px; border-bottom: 1px solid #333; }
        .debug-table th { color: #3498db; }
        .debug-pre { background: #0f0f23; padding: 15px; border-radius: 4px; overflow-x: auto; max-height: 300px; }
        .debug-key { color: #3498db; }
        .debug-value { color: #eee; word-break: break-all; }
    </style>
</head>
<body>
    <div class="debug-container">
        <div class="debug-header">
            <h1>Debug Information</h1>
        </div>
        
        <?php if (!empty($queries)): ?>
        <div class="debug-section">
            <h3>Database Queries (<?= count($queries) ?>)</h3>
            <table class="debug-table">
                <thead>
                    <tr><th>Query</th><th>Time</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($queries as $q): ?>
                    <tr>
                        <td><?= htmlspecialchars($q['query'] ?? '') ?></td>
                        <td><?= number_format(($q['time'] ?? 0) * 1000, 2) ?>ms</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if (!empty($messages)): ?>
        <div class="debug-section">
            <h3>Log Messages (<?= count($messages) ?>)</h3>
            <div class="debug-pre">
                <?php foreach ($messages as $m): ?>
                <div style="padding: 8px 0; border-bottom: 1px solid #333;">
                    <span class="debug-key"><?= htmlspecialchars($m['message'] ?? '') ?></span>
                    <?php if (!empty($m['data'])): ?>
                    <pre><?= htmlspecialchars(print_r($m['data'], true)) ?></pre>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="debug-section">
            <h3>Request Data</h3>
            <div class="debug-pre">
                <div><span class="debug-key">GET:</span> <?= htmlspecialchars(print_r($_GET, true)) ?></div>
                <div><span class="debug-key">POST:</span> <?= htmlspecialchars(print_r($_POST, true)) ?></div>
            </div>
        </div>

        <div class="debug-section" style="border-radius: 0 0 8px 8px;">
            <h3>Session Data</h3>
            <div class="debug-pre">
                <div><?= htmlspecialchars(print_r($_SESSION ?? [], true)) ?></div>
            </div>
        </div>
    </div>
</body>
</html>