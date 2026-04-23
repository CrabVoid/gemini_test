<?php
require_once 'config.php';

$dbFile = Config::get('DB_FILE', __DIR__ . '/db/tasker.db');

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Pārliecināmies, ka tabulas tiek pārizveidotas (Drop & Create)
    $pdo->exec("PRAGMA foreign_keys = OFF;");
    $pdo->exec("DROP TABLE IF EXISTS order_items;");
    $pdo->exec("DROP TABLE IF EXISTS orders;");
    $pdo->exec("DROP TABLE IF EXISTS products;");
    $pdo->exec("DROP TABLE IF EXISTS delivery_companies;");
    $pdo->exec("DROP TABLE IF EXISTS clients;");
    $pdo->exec("PRAGMA foreign_keys = ON;");

    $sql = file_get_contents(__DIR__ . '/db/init.sql');
    $pdo->exec($sql);

    // 1. Klienti
    $pdo->exec("INSERT INTO clients (id, firstname, lastname, email, points) VALUES 
        (1, 'Jānis', 'Bērziņš', 'janis@piemeri.lv', 150),
        (2, 'Līga', 'Kalniņa', 'liga@piemeri.lv', 300),
        (3, 'Artūrs', 'Ozols', 'arturs@piemeri.lv', 50),
        (4, 'Māris', 'Liepa', 'maris@piemeri.lv', 120),
        (5, 'Inese', 'Kļaviņa', 'inese@piemeri.lv', 450),
        (6, 'Kārlis', 'Krūmiņš', 'karlis@piemeri.lv', 0)");

    // 2. Piegāde
    $pdo->exec("INSERT INTO delivery_companies (id, name, comments, base_cost, cost_per_kg) VALUES 
        (1, 'Omniva', 'Pakomāti visā Baltijā', 2.50, 0.20),
        (2, 'DPD Courier', 'Piegāde līdz durvīm', 5.00, 0.50),
        (3, 'Venipak', 'Ekonomiskā piegāde', 2.00, 0.15)");

    // 3. Produkti
    $pdo->exec("INSERT INTO products (id, name, price, buy_price, weight, source) VALUES 
        (1, 'Kafijas aparāts', 120.00, 75.00, 4.5, 'Vācija, Philips rūpnīca'),
        (2, 'Tējkanna', 25.00, 12.00, 1.2, 'Polija, vietējais vairumtirgotājs'),
        (3, 'Maizes tosters', 35.00, 18.00, 2.0, 'Ķīna, importēts caur Dāniju'),
        (4, 'Mikseris', 45.00, 25.00, 1.5, 'Polija, Bosch pārstāvis'),
        (5, 'Gludeklis', 60.00, 35.00, 1.8, 'Vācija, Tefal izplatītājs'),
        (6, 'Blenderis', 55.00, 30.00, 2.2, 'Ķīna, OEM pasūtījums'),
        (7, 'Virtuves svari', 15.00, 7.50, 0.5, 'Lietuva, Maxima noliktava'),
        (8, 'Putekļu sūcējs', 180.00, 110.00, 5.5, 'Vācija, Miele noliktava'),
        (9, 'Fēns', 30.00, 15.00, 0.8, 'Francija, vietējais salons'),
        (10, 'Elektriskā zobu birste', 45.00, 22.00, 0.3, 'Lielbritānija, Amazon outlet')");

    // 4. Pasūtījumi (Manuāli aprēķināti piemēri)
    
    // Pasūtījums #1: Jānis pērk 2 tējkannas (Omniva)
    $pdo->exec("INSERT INTO orders (id, client_id, delivery_company_id, status, tax_rate, shipping_cost, total_profit, order_date) 
               VALUES (1, 1, 1, 'completed', 0.21, 2.98, 8.52, '2026-04-20 10:00:00')");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, buy_price_at_purchase) 
               VALUES (1, 2, 2, 25.00, 12.00)");

    // Pasūtījums #2: Līga pērk Kafijas aparātu un Mikseri (DPD)
    $pdo->exec("INSERT INTO orders (id, client_id, delivery_company_id, status, tax_rate, shipping_cost, total_profit, order_date) 
               VALUES (2, 2, 2, 'shipped', 0.21, 8.00, 22.35, '2026-04-21 14:30:00')");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, buy_price_at_purchase) VALUES (2, 1, 1, 120.00, 75.00), (2, 4, 1, 45.00, 25.00)");

    // Pasūtījums #3: Artūrs pērk Gludekli (Venipak)
    $pdo->exec("INSERT INTO orders (id, client_id, delivery_company_id, status, tax_rate, shipping_cost, total_profit, order_date) 
               VALUES (3, 3, 3, 'pending', 0.21, 2.27, 10.13, '2026-04-23 09:15:00')");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, buy_price_at_purchase) VALUES (3, 5, 1, 60.00, 35.00)");

    // Pasūtījums #4: Māris pērk Blenderi un Virtuves svarus (Omniva)
    // Aprēķins: Pārdod=70, Iepērk=37.5, Svars=2.7
    // Piegāde: 2.50 + (2.7 * 0.20) = 3.04
    // PVN (21% no 70): 14.70
    // Peļņa: (70 - 37.5) - 14.70 - 3.04 = 14.76
    $pdo->exec("INSERT INTO orders (id, client_id, delivery_company_id, status, tax_rate, shipping_cost, total_profit, order_date) 
               VALUES (4, 4, 1, 'pending', 0.21, 3.04, 14.76, '2026-04-23 10:45:00')");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, buy_price_at_purchase) 
               VALUES (4, 6, 1, 55.00, 30.00), (4, 7, 1, 15.00, 7.50)");

    // Pasūtījums #5: Inese pērk Putekļu sūcēju un Fēnu (DPD)
    // Aprēķins: Pārdod=210, Iepērk=125, Svars=6.3
    // Piegāde: 5.00 + (6.3 * 0.50) = 8.15
    // PVN (21% no 210): 44.10
    // Peļņa: (210 - 125) - 44.10 - 8.15 = 32.75
    $pdo->exec("INSERT INTO orders (id, client_id, delivery_company_id, status, tax_rate, shipping_cost, total_profit, order_date) 
               VALUES (5, 5, 2, 'shipped', 0.21, 8.15, 32.75, '2026-04-23 11:20:00')");
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, buy_price_at_purchase) 
               VALUES (5, 8, 1, 180.00, 110.00), (5, 9, 1, 30.00, 15.00)");

    echo "✓ Database initialized and expanded with more test data!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
