<?php
// =========================================================================
// SECTION: Customer Model
// Purpose: Encapsulates business logic and data access for Customers.
// Note: Currently used as a service-layer with static methods.
// =========================================================================

<?php
// =========================================================================
// SECTION: Customer Model
// Purpose: Encapsulates business logic and data access for Customers.
// Note: Currently used as a service-layer with static methods.
// =========================================================================

require_once __DIR__ . '/../../db/Database.php';
require_once __DIR__ . '/../../Models.php';

class Customer {

    /**
     * Fetches all customers with their full hierarchical order data.
     * 
     * @return array List of Client objects with nested Orders and Items.
     */
    public static function all() {
        $pdo = Database::getInstance()->getConnection();

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
        
        $stmt = $pdo->query($query);
        $rawData = $stmt->fetchAll();

        return self::mapHierarchy($rawData);
    }

    /**
     * Helper to map flat database rows into a nested object hierarchy.
     */
    private static function mapHierarchy($rawData) {
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
// END SECTION: Customer Model
// -------------------------------------------------------------------------
?>
