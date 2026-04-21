<?php
require_once __DIR__ . '/../config.php';

$dbFile = Config::get('DB_FILE', __DIR__ . '/../db/tasker.db');

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop old tables if they exist
    $pdo->exec("DROP TABLE IF EXISTS order_items");
    $pdo->exec("DROP TABLE IF EXISTS products");
    $pdo->exec("DROP TABLE IF EXISTS orders");
    $pdo->exec("DROP TABLE IF EXISTS clients");
    
    // Read and execute the init SQL file
    $sql = file_get_contents(__DIR__ . '/../db/init.sql');
    $pdo->exec($sql);
    
    // Insert test data
    // Insert products
    $pdo->exec("INSERT INTO products (name, price) VALUES ('Laptop', 999.99)");
    $pdo->exec("INSERT INTO products (name, price) VALUES ('Mouse', 29.99)");
    $pdo->exec("INSERT INTO products (name, price) VALUES ('Keyboard', 79.99)");
    $pdo->exec("INSERT INTO products (name, price) VALUES ('Monitor', 299.99)");
    $pdo->exec("INSERT INTO products (name, price) VALUES ('Headphones', 149.99)");
    
    // Insert test clients
    $pdo->exec("INSERT INTO clients (firstname, lastname, email, points) VALUES ('John', 'Doe', 'john@example.com', 150)");
    $pdo->exec("INSERT INTO clients (firstname, lastname, email, points) VALUES ('Jane', 'Smith', 'jane@example.com', 250)");
    $pdo->exec("INSERT INTO clients (firstname, lastname, email, points) VALUES ('Bob', 'Johnson', 'bob@example.com', 100)");
    $pdo->exec("INSERT INTO clients (firstname, lastname, email, points) VALUES ('Alice', 'Williams', 'alice@example.com', 300)");
    
    // Insert test orders
    $pdo->exec("INSERT INTO orders (client_id, status, order_date) VALUES (1, 'completed', datetime('now'))");
    $pdo->exec("INSERT INTO orders (client_id, status, order_date) VALUES (1, 'pending', datetime('now'))");
    $pdo->exec("INSERT INTO orders (client_id, status, order_date) VALUES (2, 'completed', datetime('now'))");
    $pdo->exec("INSERT INTO orders (client_id, status, order_date) VALUES (3, 'shipped', datetime('now'))");
    $pdo->exec("INSERT INTO orders (client_id, status, order_date) VALUES (4, 'pending', datetime('now'))");
    
    // Insert test order items
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (1, 1, 1, 999.99)");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (1, 2, 2, 29.99)");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (2, 3, 1, 79.99)");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (3, 4, 1, 299.99)");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (3, 5, 1, 149.99)");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (4, 1, 1, 999.99)");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (5, 2, 3, 29.99)");
    
    echo "<h2 style='color: green;'>✓ Database initialized successfully!</h2>";
    echo "<p>Tables created with test data.</p>";
    echo "<p><a href='http://localhost:8000/' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;'>View Customers List</a></p>";
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error initializing database:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
