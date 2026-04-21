<?php
// =========================================================================
// SECTION: Configuration Manager
// Purpose: Handles environment-specific settings and sensitive data.
// =========================================================================

/**
 * Simple configuration helper to mimic .env behavior without external libs.
 */
class Config {
    private static $config = [];

    public static function init() {
        // Default values
        self::$config = [
            'DB_FILE' => __DIR__ . '/db/tasker.db',
            'APP_ENV' => 'production'
        ];

        // Load .env if it exists
        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                list($name, $value) = explode('=', $line, 2);
                self::$config[trim($name)] = trim($value);
            }
        }
    }

    public static function get($key, $default = null) {
        return self::$config[$key] ?? $default;
    }
}

// Initialize immediately on include
Config::init();

// -------------------------------------------------------------------------
// END SECTION: Configuration Manager
// -------------------------------------------------------------------------
?>
