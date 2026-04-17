<?php

namespace App\Core;

class Debug
{
    private static array $queries = [];
    private static array $messages = [];
    private static float $startTime;
    private static int $startMemory;

    public static function start(): void
    {
        if (Env::get('APP_ENV') !== 'local') return;

        self::$startTime = microtime(true);
        self::$startMemory = memory_get_usage();
    }

    public static function logQuery(string $query, float $time = 0, array $params = []): void
    {
        if (Env::get('APP_ENV') !== 'local') return;

        self::$queries[] = [
            'query' => $query,
            'time' => $time,
            'params' => $params,
            'timestamp' => microtime(true),
        ];
    }

    public static function log(string $message, mixed $data = null): void
    {
        if (Env::get('APP_ENV') !== 'local') return;

        self::$messages[] = [
            'message' => $message,
            'data' => $data,
            'timestamp' => microtime(true),
        ];
    }

    public static function getQueries(): array
    {
        return self::$queries;
    }

    public static function getMessages(): array
    {
        return self::$messages;
    }

    public static function getExecutionTime(): float
    {
        return microtime(true) - self::$startTime;
    }

    public static function getMemoryUsage(): int
    {
        return memory_get_usage() - self::$startMemory;
    }

    public static function getPeakMemory(): int
    {
        return memory_get_peak_usage();
    }

    public static function getRequestData(): array
    {
        return [
            'GET' => $_GET,
            'POST' => $_POST,
            'COOKIE' => $_COOKIE,
            'FILES' => $_FILES,
            'SERVER' => [
                'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
                'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
                'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? '',
                'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? '',
            ],
        ];
    }

    public static function getSessionData(): array
    {
        return $_SESSION ?? [];
    }

    public static function render(): string
    {
        if (Env::get('APP_ENV') !== 'local') return '';

        $queries = self::$queries;
        $messages = self::$messages;
        $execTime = self::getExecutionTime();
        $memory = self::getMemoryUsage();
        $peakMemory = self::getPeakMemory();
        $requestData = self::getRequestData();
        $sessionData = self::getSessionData();

        $queryCount = count($queries);
        $messageCount = count($messages);
        $totalQueryTime = array_sum(array_column($queries, 'time'));

        $queryRows = '';
        foreach ($queries as $q) {
            $queryRows .= '<tr>';
            $queryRows .= '<td>' . htmlspecialchars(substr($q['query'], 0, 100)) . '</td>';
            $queryRows .= '<td>' . number_format($q['time'] * 1000, 2) . 'ms</td>';
            $queryRows .= '</tr>';
        }

        $messageRows = '';
        foreach ($messages as $m) {
            $messageRows .= '<div class="debug-msg">';
            $messageRows .= '<strong>' . htmlspecialchars($m['message']) . '</strong>';
            if ($m['data'] !== null) {
                $messageRows .= '<pre>' . htmlspecialchars(print_r($m['data'], true)) . '</pre>';
            }
            $messageRows .= '</div>';
        }

        $getData = htmlspecialchars(print_r($_GET, true));
        $postData = htmlspecialchars(print_r($_POST, true));
        $sessionDisplay = htmlspecialchars(print_r($sessionData, true));

        return <<<HTML
<div id="debug-bar">
    <div class="debug-bar-toggle" onclick="document.getElementById('debug-bar').classList.toggle('debug-bar-expanded')">
        <span class="debug-bar-logo">D</span>
        <span class="debug-bar-stats">
            {$queryCount} queries · {$totalQueryTime}ms · 
            {$execTime}s · {$memory} bytes
        </span>
    </div>
    <div class="debug-bar-content">
        <div class="debug-bar-tabs">
            <button class="debug-tab active" onclick="showDebugTab('queries')">SQL ({$queryCount})</button>
            <button class="debug-tab" onclick="showDebugTab('messages')">Log ({$messageCount})</button>
            <button class="debug-tab" onclick="showDebugTab('request')">Request</button>
            <button class="debug-tab" onclick="showDebugTab('session')">Session</button>
        </div>
        <div class="debug-tab-content" id="debug-tab-queries">
            <table class="debug-table">
                <thead><tr><th>Query</th><th>Time</th></tr></thead>
                <tbody>{$queryRows}</tbody>
            </table>
        </div>
        <div class="debug-tab-content" id="debug-tab-messages" style="display:none">
            {$messageRows}
        </div>
        <div class="debug-tab-content" id="debug-tab-request" style="display:none">
            <div class="debug-section"><h4>GET</h4><pre>{$getData}</pre></div>
            <div class="debug-section"><h4>POST</h4><pre>{$postData}</pre></div>
        </div>
        <div class="debug-tab-content" id="debug-tab-session" style="display:none">
            <pre>{$sessionDisplay}</pre>
        </div>
        <div class="debug-bar-footer">
            Peak Memory: {$peakMemory} bytes | Execution: {$execTime}s
        </div>
    </div>
</div>
<script>
function showDebugTab(tab) {
    document.querySelectorAll('.debug-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.debug-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('debug-tab-' + tab).style.display = 'block';
    event.target.classList.add('active');
}
</script>
<style>
#debug-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 99999;
    font-family: 'Courier New', monospace;
    font-size: 12px;
}
.debug-bar-toggle {
    background: #e74c3c;
    color: white;
    padding: 8px 15px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
}
.debug-bar-logo {
    background: white;
    color: #e74c3c;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.debug-bar-content {
    background: #1a1a2e;
    color: #eee;
    display: none;
    max-height: 300px;
    overflow-y: auto;
}
.debug-bar-expanded .debug-bar-content {
    display: block;
}
.debug-bar-tabs {
    display: flex;
    background: #16213e;
}
.debug-tab {
    background: transparent;
    color: #aaa;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 2px solid transparent;
}
.debug-tab:hover { color: white; }
.debug-tab.active {
    color: #e74c3c;
    border-bottom-color: #e74c3c;
}
.debug-tab-content {
    padding: 15px;
}
.debug-table {
    width: 100%;
    border-collapse: collapse;
}
.debug-table th,
.debug-table td {
    text-align: left;
    padding: 8px;
    border-bottom: 1px solid #333;
}
.debug-table th {
    color: #e74c3c;
}
.debug-msg {
    padding: 8px;
    border-bottom: 1px solid #333;
}
.debug-msg pre {
    margin-top: 5px;
    color: #aaa;
}
.debug-section {
    margin-bottom: 15px;
}
.debug-section h4 {
    color: #e74c3c;
    margin-bottom: 5px;
}
.debug-section pre {
    background: #0f0f23;
    padding: 10px;
    color: #aaa;
}
.debug-bar-footer {
    padding: 10px 15px;
    background: #16213e;
    color: #666;
    border-top: 1px solid #333;
}
</style>
HTML;
    }

    public static function isLocal(): bool
    {
        return Env::get('APP_ENV') === 'local';
    }

    public static function dump(mixed $data): void
    {
        if (!self::isLocal()) return;

        echo '<pre style="background:#1a1a2e;color:#eee;padding:20px;margin:10px 0;border-radius:8px;">';
        print_r($data);
        echo '</pre>';
    }
}