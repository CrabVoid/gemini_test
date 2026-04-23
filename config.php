<?php
// =========================================================================
// SECTION: Global Configuration
// Purpose: Stores environment settings like database paths and base URLs.
// =========================================================================
class Config {
    // Statisks masīvs ar visiem iestatījumiem
    private static $settings = [
        'DB_FILE' => __DIR__ . '/db/tasker.db', // Ceļš uz SQLite failu
        'BASE_URL' => 'http://localhost:8000'   // Lietotnes galvenā adrese
    ];

    /**
     * SUB-SECTION: Configuration Getter
     * Purpose: Safely retrieve a setting by its key.
     */
    public static function get($key, $default = null) {
        // Ja atslēga eksistē masīvā, atgriežam to, pretējā gadījumā - noklusējuma vērtību
        return self::$settings[$key] ?? $default;
    }
}
// =========================================================================
// END SECTION: Global Configuration
// =========================================================================
?>