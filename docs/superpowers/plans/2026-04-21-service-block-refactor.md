# Service-Oriented Block Refactor Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refactor the Tasker PHP codebase into a structured "block format" with clear separation of concerns and simplified logic.

**Architecture:** A service-oriented approach using a Singleton Database connection, simple Data Models, and a Repository for complex data mapping. The UI (index.php) will be separated from data fetching logic.

**Tech Stack:** PHP 8.x, SQLite (PDO).

---

### Task 1: Refactor Database.php

**Files:**
- Modify: `Database.php`

- [ ] **Step 1: Apply Block Format to Database.php**

```php
<?php
// =========================================================================
// SECTION: Database Connection (Singleton)
// Purpose: Provides a single, shared PDO instance for the entire application.
// =========================================================================

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dbFile = __DIR__ . DIRECTORY_SEPARATOR . 'tasker.db';
        try {
            $this->pdo = new PDO("sqlite:" . $dbFile);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}

// -------------------------------------------------------------------------
// END SECTION: Database Connection
// -------------------------------------------------------------------------
?>
```

- [ ] **Step 2: Commit changes**

```bash
git add Database.php
git commit -m "refactor: apply block format to Database.php"
```

---

### Task 2: Refactor Models.php

**Files:**
- Modify: `Models.php`

- [ ] **Step 1: Apply Block Format to Models.php**

```php
<?php
// =========================================================================
// SECTION: Data Models
// Purpose: Simple classes to represent the core entities of the application.
// =========================================================================

// -------------------------------------------------------------------------
// SUB-SECTION: OrderItem
// -------------------------------------------------------------------------
class OrderItem {
    public $id;
    public $product;
    public $qty;
    public $price;

    public function __construct($id, $product, $qty, $price) {
        $this->id = $id;
        $this->product = $product;
        $this->qty = $qty;
        $this->price = $price;
    }

    public function getTotal() {
        return $this->qty * $this->price;
    }
}

// -------------------------------------------------------------------------
// SUB-SECTION: Order
// -------------------------------------------------------------------------
class Order {
    public $id;
    public $status;
    public $date;
    public $items = [];

    public function __construct($id, $status, $date) {
        $this->id = $id;
        $this->status = $status;
        $this->date = $date;
    }

    public function addItem(OrderItem $item) {
        $this->items[] = $item;
    }
}

// -------------------------------------------------------------------------
// SUB-SECTION: Client
// -------------------------------------------------------------------------
class Client {
    public $id;
    public $name;
    public $email;
    public $points;
    public $orders = [];

    public function __construct($id, $name, $email, $points) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->points = $points;
    }

    public function addOrder(Order $order) {
        $this->orders[$order->id] = $order;
    }
}

// -------------------------------------------------------------------------
// END SECTION: Data Models
// -------------------------------------------------------------------------
?>
```

- [ ] **Step 2: Commit changes**

```bash
git add Models.php
git commit -m "refactor: apply block format to Models.php"
```

---

### Task 3: Refactor ClientRepository.php

**Files:**
- Modify: `ClientRepository.php`

- [ ] **Step 1: Apply Block Format and Simplify Mapping**

```php
<?php
// =========================================================================
// SECTION: Client Repository
// Purpose: Handles data fetching and complex mapping of relational data.
// =========================================================================

require_once 'Database.php';
require_once 'Models.php';

class ClientRepository {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Fetches all clients with their nested orders and items.
     * Simplified using a lookup-and-assign pattern.
     */
    public function getAllWithHierarchy() {
        $query = "
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
        ";
        
        $stmt = $this->pdo->query($query);
        $rawData = $stmt->fetchAll();

        $clients = [];
        foreach ($rawData as $row) {
            $cid = $row['client_id'];
            $oid = $row['order_id'];
            $iid = $row['item_id'];

            // 1. Map Client if not exists
            if (!isset($clients[$cid])) {
                $clients[$cid] = new Client(
                    $cid,
                    $row['firstname'] . ' ' . $row['lastname'],
                    $row['email'],
                    $row['points']
                );
            }

            // 2. Map Order if exists and not already mapped
            if ($oid && !isset($clients[$cid]->orders[$oid])) {
                $clients[$cid]->addOrder(new Order(
                    $oid,
                    $row['order_status'],
                    $row['order_date']
                ));
            }

            // 3. Map Item if exists
            if ($iid) {
                $clients[$cid]->orders[$oid]->addItem(new OrderItem(
                    $iid,
                    $row['product_name'],
                    $row['quantity'],
                    $row['price_at_purchase']
                ));
            }
        }
        return $clients;
    }
}

// -------------------------------------------------------------------------
// END SECTION: Client Repository
// -------------------------------------------------------------------------
?>
```

- [ ] **Step 2: Commit changes**

```bash
git add ClientRepository.php
git commit -m "refactor: apply block format and simplify mapping in ClientRepository.php"
```

---

### Task 4: Refactor index.php

**Files:**
- Modify: `index.php`

- [ ] **Step 1: Refactor index.php to use Repository and Block Format**

```php
<?php
// =========================================================================
// SECTION: Logic & Data Initialization
// Purpose: Orchestrates the data fetching using the ClientRepository.
// =========================================================================

require_once 'ClientRepository.php';

$repo = new ClientRepository();
$clients = $repo->getAllWithHierarchy();

// -------------------------------------------------------------------------
// END SECTION: Logic & Data Initialization
// -------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasker - Client Overview</title>
    <!-- 
    // =========================================================================
    // SECTION: Styling
    // Purpose: Visual presentation of the client list.
    // =========================================================================
    -->
    <style>
        body { font: 14px Arial, sans-serif; background: #eee; margin: 20px; }
        .card { background: #fff; padding: 15px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 3px #ccc; }
        ul { margin: 5px 0 5px 20px; padding: 0; color: #555; }
        .order-id { font-weight: bold; }
        .client-header { font-size: 1.1em; color: #333; }
    </style>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Styling
    // -------------------------------------------------------------------------
    -->
</head>
<body>

    <h2>Veikals (Store)</h2>

    <!-- 
    // =========================================================================
    // SECTION: Content Rendering
    // Purpose: Loops through data and outputs HTML.
    // =========================================================================
    -->
    <?php foreach ($clients as $c): ?>
        <div class="card">
            <div class="client-header">
                <b><?= htmlspecialchars($c->name) ?></b> (ID: <?= $c->id ?>)
            </div>
            
            <?php foreach ($c->orders as $o): ?>
                <ul>
                    <li>
                        <span class="order-id">Order #<?= $o->id ?></span> 
                        [<?= htmlspecialchars($o->status) ?>]
                        <ul>
                            <?php foreach ($o->items as $i): ?>
                                <li><?= htmlspecialchars($i->product) ?> (x<?= $i->qty ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Content Rendering
    // -------------------------------------------------------------------------
    -->

</body>
</html>
```

- [ ] **Step 2: Commit changes**

```bash
git add index.php
git commit -m "refactor: apply block format to index.php and use ClientRepository"
```

---

### Task 5: Final Cleanup

**Files:**
- Delete: `db_connect.php`

- [ ] **Step 1: Delete db_connect.php**

Run: `rm db_connect.php`

- [ ] **Step 2: Commit deletion**

```bash
git add db_connect.php
git commit -m "cleanup: remove deprecated db_connect.php"
```
