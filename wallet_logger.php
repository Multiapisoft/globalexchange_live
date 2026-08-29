<?php
// wallet_logger.php
// Simple logging utility (console + file + browser viewer)

// --- CONFIG ---
define('WALLET_LOG_FILE', __DIR__ . '/wallet_transfer.log'); 
define('WALLET_LOG_TOKEN', 'token'); // change this token!

class WalletLogger {
    public static function log($message) {
        $ts = date("Y-m-d H:i:s");
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        }
        $line = "[{$ts}] {$message}\n";

        // console output
        echo $line;

        // write to file
        error_log($line, 3, WALLET_LOG_FILE);
    }

    public static function tail($lines = 200) {
        if (!file_exists(WALLET_LOG_FILE)) {
            return "--- log file not found ---\n";
        }
        $data = file(WALLET_LOG_FILE);
        return implode("", array_slice($data, -$lines));
    }

    public static function httpEndpoint() {
        if (php_sapi_name() === 'cli') return;
        if (!isset($_GET['token']) || $_GET['token'] !== WALLET_LOG_TOKEN) {
            http_response_code(403);
            echo "Forbidden";
            exit;
        }
        $lines = isset($_GET['lines']) ? intval($_GET['lines']) : 200;
        header("Content-Type: text/plain; charset=utf-8");
        echo self::tail($lines);
        exit;
    }
}

if (!function_exists('wallet_log')) {
    function wallet_log($msg) { WalletLogger::log($msg); }
}

// run endpoint when accessed directly
WalletLogger::httpEndpoint();
