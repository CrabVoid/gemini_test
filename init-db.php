<?php
require_once 'config.php';

$dbFile = Config::get('DB_FILE', __DIR__ . '/db/tasker.db');

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Izveidojam tabulas
    $sql = file_get_contents(__DIR__ . '/db/init.sql');
    $pdo->exec($sql);
    
    // 2. Iztīrām datus, apejot foreign key ierobežojumus uz brīdi
    $pdo->exec("PRAGMA foreign_keys = OFF;");
    $pdo->exec("DELETE FROM order_items; DELETE FROM orders; DELETE FROM products; DELETE FROM delivery_companies; DELETE FROM clients;");
    $pdo->exec("PRAGMA foreign_keys = ON;");

    // 3. Pievienojam datus
    $pdo->exec("INSERT INTO clients (id, firstname, lastname, email, points) VALUES 
        (1, 'Jānis', 'Bērziņš', 'janis@piemeri.lv', 150),
        (2, 'Līga', 'Kalniņa', 'liga@piemeri.lv', 300)");

    $pdo->exec("INSERT INTO delivery_companies (id, name, comments, base_cost, cost_per_kg) VALUES 
        (1, 'Omniva', 'Pakomāti visā Baltijā', 2.50, 0.20),
        (2, 'DPD Courier', 'Piegāde līdz durvīm', 5.00, 0.50)");

    $pdo->exec("INSERT INTO products (id, name, price, buy_price, weight) VALUES 
        (1, 'Kafijas aparāts', 120.00, 75.00, 4.5),
        (2, 'Tējkanna', 25.00, 12.00, 1.2),
        (3, 'Maizes tosters', 35.00, 18.00, 2.0)");

    $pdo->exec("INSERT INTO orders (id, client_id, delivery_company_id, status, tax_rate, shipping_cost, total_profit, order_date) 
               VALUES (1, 1, 1, 'completed', 0.21, 2.98, 8.52, datetime('now'))");
    
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, buy_price_at_purchase) 
               VALUES (1, 2, 2, 25.00, 12.00)");

    echo "✓ Database initialized and seeded successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
