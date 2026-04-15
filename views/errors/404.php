<?php
$title = $title ?? '404';
$message = $message ?? 'Página não encontrada';
$simple = $simple ?? false;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-color, #f5f5f5);
            color: var(--text-color, #333);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .error-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .error-message {
            color: #666;
            margin-bottom: 20px;
        }
        .error-link {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .error-link:hover {
            background: #2980b9;
        }
        <?php if (!empty($simple)): ?>
        .error-details { display: none; }
        <?php else: ?>
        .error-details {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: left;
            font-size: 13px;
            color: #666;
        }
        .error-details dt {
            font-weight: bold;
            color: #999;
        }
        .error-details dd {
            margin-bottom: 10px;
            font-family: monospace;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-code">404</div>
        <h1 class="error-title"><?= htmlspecialchars($title) ?></h1>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>
        <a href="<?= $baseUrl ?? '/' ?>" class="error-link">Voltar ao Início</a>
        
        <?php if (!empty($errorId)): ?>
        <dl class="error-details">
            <dt>Error ID</dt>
            <dd><?= htmlspecialchars($errorId) ?></dd>
            <dt>URL</dt>
            <dd><?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') ?></dd>
            <dt>IP</dt>
            <dd><?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '') ?></dd>
        </dl>
        <?php endif; ?>
    </div>
</body>
</html>