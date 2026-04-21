<?php
require_once 'config.php';

$dbFile = Config::get('DB_FILE', __DIR__ . '/db/tasker.db');

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute the init SQL file
    $sql = file_get_contents(__DIR__ . '/db/init.sql');
    $pdo->exec($sql);
    
    echo "✓ Database initialized successfully!<br>";
    echo "Tables created. You can now access: <a href='http://localhost:8000/'>http://localhost:8000/</a>";
} catch (Exception $e) {
    echo "Error initializing database: " . $e->getMessage();
}
?>
