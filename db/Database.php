<?php
require_once __DIR__ . '/../config.php';

// =========================================================================
// SECTION: Database Connection
// Purpose: Implements a single point of connection to the SQLite database.
// =========================================================================
class Database {
    // Mainīgais, kurā glabāsies aktīvais savienojums
    private static $connection = null;

    /**
     * SUB-SECTION: Get Active Connection
     * Purpose: Returns the existing connection or creates a new one if needed.
     */
    public static function getConnection() {
        // Ja savienojums vēl nav izveidots (ir null)
        if (self::$connection === null) {
            try {
                // Iegūstam ceļu uz DB failu no konfigurācijas
                $dbFile = Config::get('DB_FILE');
                
                // Izveidojam jaunu PDO (PHP Data Objects) instanci
                self::$connection = new PDO("sqlite:" . $dbFile);
                
                // Iestatām kļūdu režīmu: ja notiks kļūda, tā metīs Exception (izņēmumu)
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Iestatām noklusējuma datu saņemšanas veidu uz asociatīvu masīvu
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Ja nevar savienoties, parāda saprotamu kļūdu
                die("Kļūda savienojoties ar datubāzi: " . $e->getMessage());
            }
        }
        
        // Atgriežam aktīvo savienojumu citu modeļu lietošanai
        return self::$connection;
    }
}
// =========================================================================
// END SECTION: Database Connection
// =========================================================================
?>