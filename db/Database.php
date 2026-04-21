<?php
// =========================================================================
// SECTION: Database Connection (Singleton)
// Purpose: Provides a single, shared PDO instance for the entire application.
// =========================================================================

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Use __DIR__ to ensure the path is always relative to THIS file
        $dbFile = __DIR__ . DIRECTORY_SEPARATOR . 'tasker.db';
        try {
            $this->pdo = new PDO("sqlite:" . $dbFile);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}

// -------------------------------------------------------------------------
// END SECTION: Database Connection
// -------------------------------------------------------------------------
?>
