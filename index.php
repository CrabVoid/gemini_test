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
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Points</th></tr>";
        foreach ($clients as $client) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($client['id']) . "</td>";
            echo "<td>" . htmlspecialchars($client['firstname'] . ' ' . $client['lastname']) . "</td>";
            echo "<td>" . htmlspecialchars($client['email']) . "</td>";
            echo "<td>" . htmlspecialchars($client['points']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
}
?>
