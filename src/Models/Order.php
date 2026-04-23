<?php
// =========================================================================
// SECTION: Order Model
// Purpose: Encapsulates business logic and data access for Orders.
// Note: Currently used as a service-layer with static methods.
// =========================================================================

require_once __DIR__ . '/../../db/Database.php';
require_once __DIR__ . '/../../Models.php';

class OrderModel {

    /**
     * Fetches all orders with their client and item details.
     * Optionally filter by status.
     * 
     * @param string|null $status Filter by order status (pending, shipped, completed)
     * @return array List of Order objects with client and items information.
     */
    public static function all($status = null) {
        $pdo = Database::getInstance()->getConnection();

        $query = "
            SELECT 
                o.id as order_id,
                o.client_id,
                o.status as order_status,
                o.order_date,
                c.firstname,
                c.lastname,
                c.email,
                oi.id as item_id,
                oi.quantity,
                oi.price_at_purchase,
                p.name as product_name
            FROM orders o
            LEFT JOIN clients c ON o.client_id = c.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
        ";

        // Add status filter if provided
        if ($status !== null) {
            $query .= " WHERE o.status = :status";
        }

        $query .= " ORDER BY o.id, oi.id";
        
        $stmt = $pdo->prepare($query);
        
        // Bind status parameter if provided
        if ($status !== null) {
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $rawData = $stmt->fetchAll();

        return self::mapOrders($rawData);
    }

    /**
     * Helper to map flat database rows into order objects with items.
     */
    private static function mapOrders($rawData) {
        $orders = [];
        
        foreach ($rawData as $row) {
            $oid = $row['order_id'];
            $iid = $row['item_id'];

            // 1. Map Order if not exists
            if (!isset($orders[$oid])) {
                $orders[$oid] = new Order(
                    $oid,
                    $row['order_status'],
                    $row['order_date']
                );
                // Store client info
                $orders[$oid]->client_id = $row['client_id'];
                $orders[$oid]->client_name = $row['firstname'] . ' ' . $row['lastname'];
                $orders[$oid]->client_email = $row['email'];
            }

            // 2. Map Item if exists
            if ($iid) {
                $orders[$oid]->addItem(new OrderItem(
                    $iid,
                    $row['product_name'],
                    $row['quantity'],
                    $row['price_at_purchase']
                ));
            }
        }
        
        return $orders;
    }

    /**
     * Creates a new order with items in the database.
     * Uses a transaction to ensure both order and items are saved.
     * 
     * @param int $client_id The customer ID
     * @param string $status The order status
     * @param array $items List of items (product_id, quantity)
     * @return int|false The ID of the newly created order or false on failure
     */
    public static function create($client_id, $status, $items) {
        $pdo = Database::getInstance()->getConnection();

        try {
            $pdo->beginTransaction();

            // 1. Insert the main order
            $query = "INSERT INTO orders (client_id, status, order_date) VALUES (:client_id, :status, datetime('now'))";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':client_id' => $client_id,
                ':status' => $status
            ]);

            $orderId = $pdo->lastInsertId();

            // 2. Insert each item
            $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                          VALUES (:order_id, :product_id, :quantity, :price_at_purchase)";
            $itemStmt = $pdo->prepare($itemQuery);

            foreach ($items as $item) {
                // Fetch current product price
                $productStmt = $pdo->prepare("SELECT price FROM products WHERE id = :pid");
                $productStmt->execute([':pid' => $item['product_id']]);
                $product = $productStmt->fetch();

                if (!$product) {
                    throw new Exception("Product ID " . $item['product_id'] . " not found.");
                }

                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price_at_purchase' => $product['price']
                ]);
            }

            $pdo->commit();
            return $orderId;

        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Finds a single order by ID.
     */
    public static function find($id) {
        $pdo = Database::getInstance()->getConnection();
        $query = "
            SELECT o.*, c.firstname, c.lastname, c.email 
            FROM orders o 
            JOIN clients c ON o.client_id = c.id 
            WHERE o.id = :id
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return false;

        $order = new Order($row['id'], $row['status'], $row['order_date']);
        $order->client_id = $row['client_id'];
        $order->client_name = $row['firstname'] . ' ' . $row['lastname'];
        $order->client_email = $row['email'];

        // Fetch items
        $itemQuery = "
            SELECT oi.*, p.name as product_name 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = :oid
        ";
        $itemStmt = $pdo->prepare($itemQuery);
        $itemStmt->execute([':oid' => $id]);
        
        foreach ($itemStmt->fetchAll() as $itemRow) {
            $order->addItem(new OrderItem(
                $itemRow['id'],
                $itemRow['product_name'],
                $itemRow['quantity'],
                $itemRow['price_at_purchase']
            ));
        }

        return $order;
    }

    /**
     * Updates an existing order.
     */
    public static function update($id, $client_id, $status, $items = []) {
        $pdo = Database::getInstance()->getConnection();

        try {
            $pdo->beginTransaction();

            // 1. Update main order
            $query = "UPDATE orders SET client_id = :client_id, status = :status WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':client_id' => $client_id,
                ':status' => $status,
                ':id' => $id
            ]);

            // 2. Update items (simplest way: delete and re-insert if items provided)
            if (!empty($items)) {
                $pdo->prepare("DELETE FROM order_items WHERE order_id = :id")->execute([':id' => $id]);
                
                $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                                          VALUES (:order_id, :product_id, :quantity, :price_at_purchase)");
                
                foreach ($items as $item) {
                    $productStmt = $pdo->prepare("SELECT price FROM products WHERE id = :pid");
                    $productStmt->execute([':pid' => $item['product_id']]);
                    $productPrice = $productStmt->fetchColumn();

                    $itemStmt->execute([
                        ':order_id' => $id,
                        ':product_id' => $item['product_id'],
                        ':quantity' => $item['quantity'],
                        ':price_at_purchase' => $productPrice
                    ]);
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Deletes an order and its items.
     */
    public static function delete($id) {
        $pdo = Database::getInstance()->getConnection();
        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM order_items WHERE order_id = :id")->execute([':id' => $id]);
            $pdo->prepare("DELETE FROM orders WHERE id = :id")->execute([':id' => $id]);
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}

// -------------------------------------------------------------------------
// END SECTION: Order Model
// -------------------------------------------------------------------------
?>
