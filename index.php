<?php
// index.php
require_once 'db_connect.php';

echo "<h1>Client List (Processed in db_connect)</h1>";

if (empty($clients)) {
    echo "<p>No clients found.</p>";
} else {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Points</th><th>Orders</th></tr>";
    
    // Single loop: Data is already processed and grouped
    foreach ($clients as $client) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($client['id']) . "</td>";
        echo "<td>" . htmlspecialchars($client['name']) . "</td>";
        echo "<td>" . htmlspecialchars($client['email']) . "</td>";
        echo "<td>" . htmlspecialchars($client['points']) . "</td>";
        
        echo "<td>";
        if (empty($client['orders'])) {
            echo "No orders";
        } else {
            echo "<ul>";
            foreach ($client['orders'] as $order) {
                echo "<li>ID: " . htmlspecialchars($order['id']) . " (" . htmlspecialchars($order['status']) . ")</li>";
            }
            echo "</ul>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
