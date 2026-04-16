<?php
$dbFile = 'tasker.db';

try {
    // Create (connect to) SQLite database in file
    $pdo = new PDO("sqlite:" . __DIR__ . DIRECTORY_SEPARATOR . $dbFile);
    
    // Set errormode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to the database successfully!\n";

    // Verify tables exist
    $query = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name IN ('clients', 'orders')");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);

    if (count($tables) > 0) {
        echo "Found tables: " . implode(", ", $tables) . "\n";
    } else {
        echo "Database connected, but no tables were found.\n";
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
