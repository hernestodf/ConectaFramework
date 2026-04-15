<?php
$title = $title ?? '500';
$message = $message ?? 'Ocorreu um erro inesperado.';
$errorId = $errorId ?? '';
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
            background: #f5f5f5;
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
        .error-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .error-title {
            color: #e74c3c;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .error-message {
            color: #666;
            margin-bottom: 20px;
        }
        .error-id {
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 4px;
            font-family: monospace;
            color: #333;
        }
        .error-hint {
            margin-top: 20px;
            font-size: 13px;
            color: #999;
        }
        .error-link {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            transition: background 0.2s;
        }
        .error-link:hover {
            background: #2980b9;
        }
        <?php if (!empty($simple)): ?>
        .error-stack { display: none; }
        <?php else: ?>
        .error-stack {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: left;
        }
        .error-stack h4 {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .error-stack pre {
            background: #1a1a2e;
            color: #eee;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-icon">⚠️</div>
        <h1 class="error-title"><?= htmlspecialchars($title) ?></h1>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>
        <?php if (!empty($errorId)): ?>
        <div class="error-id">ID: <?= htmlspecialchars($errorId) ?></div>
        <?php endif; ?>
        <p class="error-hint">Entre em contato com o suporte informando este ID.</p>
        <a href="<?= $baseUrl ?? '/' ?>" class="error-link">Voltar ao Início</a>
    </div>
</body>
</html>