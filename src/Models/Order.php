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
}

// -------------------------------------------------------------------------
// END SECTION: Order Model
// -------------------------------------------------------------------------
?>
