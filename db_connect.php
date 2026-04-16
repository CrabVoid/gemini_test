<?php
// db_connect.php
$dbFile = __DIR__ . DIRECTORY_SEPARATOR . 'tasker.db';

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Fetch EVERYTHING in one pass using JOINS
    // (Still no N+1, just a bit more data to organize in PHP)
    $stmt = $pdo->query("
        SELECT 
            c.id as client_id, c.firstname, c.lastname, c.email, c.points,
            o.id as order_id, o.status as order_status, o.order_date,
            oi.id as item_id, oi.quantity, oi.price_at_purchase,
            p.name as product_name
        FROM clients c
        LEFT JOIN orders o ON c.id = o.client_id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        ORDER BY c.id, o.id, oi.id
    ");
    $rawData = $stmt->fetchAll();

    // Organize into a nested hierarchical structure
    $clients = [];
    foreach ($rawData as $row) {
        $cid = $row['client_id'];
        $oid = $row['order_id'];
        $iid = $row['item_id'];

        // Init Client
        if (!isset($clients[$cid])) {
            $clients[$cid] = [
                'info' => [
                    'id' => $cid,
                    'name' => $row['firstname'] . ' ' . $row['lastname'],
                    'email' => $row['email'],
                    'points' => $row['points']
                ],
                'orders' => []
            ];
        }

        // Init/Add Order to Client
        if ($oid) {
            if (!isset($clients[$cid]['orders'][$oid])) {
                $clients[$cid]['orders'][$oid] = [
                    'id' => $oid,
                    'status' => $row['order_status'],
                    'date' => $row['order_date'],
                    'items' => []
                ];
            }

            // Add Item to Order
            if ($iid) {
                $clients[$cid]['orders'][$oid]['items'][] = [
                    'id' => $iid,
                    'product' => $row['product_name'],
                    'qty' => $row['quantity'],
                    'price' => $row['price_at_purchase']
                ];
            }
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
