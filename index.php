<?php
// index.php - Receives the $pdo connection from db_connect.php
require_once 'db_connect.php';

try {
    // Fetch all clients
    $stmt = $pdo->query("SELECT * FROM clients");
    $clients = $stmt->fetchAll();

    echo "<h1>Client List</h1>";

    if (empty($clients)) {
        echo "<p>No clients found in the database.</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Points</th><th>Orders (N+1 Example)</th></tr>";
        foreach ($clients as $client) {
            // N+1 Problem: We are executing a query for EVERY client found.
            // 1 query for clients + N queries for their orders.
            $stmtOrders = $pdo->prepare("SELECT * FROM orders WHERE client_id = ?");
            $stmtOrders->execute([$client['id']]);
            $orders = $stmtOrders->fetchAll();

            echo "<tr>";
            echo "<td>" . htmlspecialchars($client['id']) . "</td>";
            echo "<td>" . htmlspecialchars($client['firstname'] . ' ' . $client['lastname']) . "</td>";
            echo "<td>" . htmlspecialchars($client['email']) . "</td>";
            echo "<td>" . htmlspecialchars($client['points']) . "</td>";
            
            echo "<td>";
            if (empty($orders)) {
                echo "No orders";
            } else {
                echo "<ul>";
                foreach ($orders as $order) {
                    echo "<li>Order ID: " . htmlspecialchars($order['id']) . " - Status: " . htmlspecialchars($order['status']) . "</li>";
                }
                echo "</ul>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
}
?>
