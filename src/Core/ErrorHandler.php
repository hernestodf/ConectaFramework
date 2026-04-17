<?php

namespace App\Core;

class ErrorHandler
{
    private static bool $registered = false;
    private static array $errorData = [];

    public static function register(): void
    {
        if (self::$registered) return;

        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);

        self::$registered = true;
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $errorTypes = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        $type = $errorTypes[$errno] ?? 'UNKNOWN';
        
        $error = [
            'type' => $type,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'time' => microtime(true),
        ];

        self::$errorData[] = $error;
        
        Logger::error("PHP {$type}: {$errstr}", [
            'file' => $errfile,
            'line' => $errline,
        ]);

        if (Env::get('APP_ENV') === 'local') {
            return false;
        }

        return true;
    }

    public static function handleException(\Throwable $exception): void
    {
        $errorId = Logger::getErrorId();
        $isLocal = Env::get('APP_ENV') === 'local';

        $errorData = [
            'errorId' => $errorId,
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'previous' => $exception->getPrevious() ? $exception->getPrevious()->getMessage() : null,
            'time' => microtime(true),
            'isLocal' => $isLocal,
        ];

        self::$errorData[] = $errorData;

        Logger::error("Exception {$errorId}: " . $exception->getMessage(), [
            'type' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        if ($isLocal) {
            self::renderDebugPage($errorData);
        } else {
            self::renderProductionPage($errorId, $exception->getMessage());
        }
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $errorId = Logger::getErrorId();
            $isLocal = Env::get('APP_ENV') === 'local';

            Logger::error("Fatal {$errorId}: {$error['message']}", [
                'file' => $error['file'],
                'line' => $error['line'],
            ]);

            if ($isLocal) {
                echo "<h1>Fatal Error</h1>";
                echo "<p>{$error['message']}</p>";
                echo "<pre>{$error['file']}:{$error['line']}</pre>";
            } else {
                http_response_code(500);
                echo "<h1>Erro Interno</h1>";
                echo "<p>ID: {$errorId}</p>";
            }
        }
    }

    private static function renderDebugPage(array $errorData): void
    {
        http_response_code(500);
        
        $trace = htmlspecialchars($errorData['trace'] ?? '');
        $file = htmlspecialchars($errorData['file'] ?? '');
        $line = $errorData['line'] ?? 0;
        $message = htmlspecialchars($errorData['message'] ?? '');
        $type = htmlspecialchars($errorData['type'] ?? '');
        
        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Error - {$errorData['errorId']}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Courier New', monospace; 
            background: #1a1a2e; 
            color: #eee;
            padding: 20px;
        }
        .error-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .error-header {
            background: #e74c3c;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .error-header h1 { font-size: 24px; margin-bottom: 10px; }
        .error-id { opacity: 0.8; font-size: 14px; }
        .error-body {
            background: #16213e;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .error-section {
            margin-bottom: 20px;
        }
        .error-section h3 {
            color: #e74c3c;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
        }
        .error-message {
            background: #0f0f23;
            padding: 15px;
            border-radius: 4px;
            color: #ff6b6b;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .error-file {
            color: #f39c12;
            font-size: 14px;
        }
        .error-trace {
            background: #0f0f23;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
            line-height: 1.6;
            max-height: 400px;
            overflow-y: auto;
        }
        .error-data {
            background: #0f0f23;
            padding: 15px;
            border-radius: 4px;
            font-size: 13px;
        }
        .error-data-row {
            display: flex;
            margin-bottom: 8px;
        }
        .error-data-key {
            color: #3498db;
            min-width: 120px;
        }
        .error-data-value {
            color: #eee;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <h1>⚠️ {$type}</h1>
            <div class="error-id">ID: {$errorData['errorId']}</div>
        </div>
        <div class="error-body">
            <div class="error-section">
                <h3>Mensagem</h3>
                <div class="error-message">{$message}</div>
            </div>
            <div class="error-section">
                <h3>Arquivo</h3>
                <div class="error-file">{$file}:{$line}</div>
            </div>
            <div class="error-section">
                <h3>Dados do Erro</h3>
                <div class="error-data">
                    <div class="error-data-row">
                        <span class="error-data-key">Type:</span>
                        <span class="error-data-value">{$type}</span>
                    </div>
                    <div class="error-data-row">
                        <span class="error-data-key">Code:</span>
                        <span class="error-data-value">{$errorData['code']}</span>
                    </div>
                    <div class="error-data-row">
                        <span class="error-data-key">Time:</span>
                        <span class="error-data-value">{$errorData['time']}</span>
                    </div>
                    <div class="error-data-row">
                        <span class="error-data-key">Previous:</span>
                        <span class="error-data-value">{$errorData['previous']}</span>
                    </div>
                </div>
            </div>
            <div class="error-section">
                <h3>Stack Trace</h3>
                <div class="error-trace">{$trace}</div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private static function renderProductionPage(string $errorId, string $message): void
    {
        http_response_code(500);
        
        $shortId = substr($errorId, 0, 12);
        
        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Erro Interno</title>
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
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-icon">⚠️</div>
        <h1 class="error-title">Erro Interno</h1>
        <p class="error-message">Ocorreu um erro inesperado. Tente novamente mais tarde.</p>
        <div class="error-id">ID: {$shortId}</div>
        <p class="error-hint">Entre em contato com o suporte informando este ID.</p>
    </div>
</body>
</html>
HTML;
    }

    public static function getErrorData(): array
    {
        return self::$errorData;
    }

    public static function clearErrorData(): void
    {
        self::$errorData = [];
    }
}