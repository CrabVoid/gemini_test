<?php
// db_connect.php
$dbFile = __DIR__ . DIRECTORY_SEPARATOR . 'tasker.db';

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 1. The Query: Get all data in one hit (No N+1)
    $stmt = $pdo->query("
        SELECT 
            c.id as client_id, c.firstname, c.lastname, c.email, c.points,
            o.id as order_id, o.status, o.order_date
        FROM clients c
        LEFT JOIN orders o ON c.id = o.client_id
    ");
    $rawData = $stmt->fetchAll();

    // 2. Data Editing: Transform raw rows into a structured array
    $clients = [];
    foreach ($rawData as $row) {
        $cid = $row['client_id'];
        
        // If this is the first time we see this client, initialize their entry
        if (!isset($clients[$cid])) {
            $clients[$cid] = [
                'id' => $cid,
                'name' => $row['firstname'] . ' ' . $row['lastname'],
                'email' => $row['email'],
                'points' => $row['points'],
                'orders' => []
            ];
        }

        // Add order info if it exists (LEFT JOIN might return NULL for orders)
        if ($row['order_id']) {
            $clients[$cid]['orders'][] = [
                'id' => $row['order_id'],
                'status' => $row['status']
            ];
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
