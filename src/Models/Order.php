<?php
require_once __DIR__ . '/../../db/Database.php';
require_once __DIR__ . '/../../Models.php';

// =========================================================================
// SECTION: Order Model
// Purpose: Manages complex interactions with 'orders' and 'order_items' tables.
// =========================================================================
class OrderModel {

    /**
     * SUB-SECTION: Fetch All Orders
     * Purpose: Get orders with customer names and total amounts (JOINs).
     */
    public static function all() {
        $db = Database::getConnection();
        
        // Sarežģīts SQL: 
        // 1. Apvienojam (JOIN) pasūtījumus ar klientiem, lai dabūtu vārdu.
        // 2. Apvienojam (JOIN) ar precēm, lai saskaitītu (SUM) kopējo summu.
        // 3. Grupējam (GROUP BY), lai katrs pasūtījums būtu viena rinda.
        $sql = "SELECT o.*, 
                       (c.firstname || ' ' || c.lastname) as client_name,
                       SUM(oi.quantity * oi.price_at_purchase) as total_amount
                FROM orders o
                JOIN clients c ON o.client_id = c.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                GROUP BY o.id
                ORDER BY o.id DESC";
                
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Order');
    }

    /**
     * SUB-SECTION: Create Order with Items
     * Purpose: Inserts a new order and its items in a single transaction.
     */
    public static function create($data) {
        $db = Database::getConnection();
        
        // 1. Sākam transakciju: ja kāds solis neizdosies, nekas netiks saglabāts
        $db->beginTransaction();

        try {
            // 2. Izveidojam galveno pasūtījuma ierakstu
            $stmt = $db->prepare("INSERT INTO orders (client_id, status, order_date) VALUES (:client_id, 'pending', datetime('now'))");
            $stmt->execute([':client_id' => $data['client_id']]);
            
            // 3. Iegūstam tikko izveidotā pasūtījuma ID
            $orderId = $db->lastInsertId();

            // 4. Pievienojam pasūtījuma preces
            foreach ($data['items'] as $item) {
                // Atrodam aktuālo produkta cenu pirkuma brīdī (Price Locking)
                $prodStmt = $db->prepare("SELECT price FROM products WHERE id = :id");
                $prodStmt->execute([':id' => $item['product_id']]);
                $price = $prodStmt->fetchColumn();

                // Ievietojam preces rindu
                $itemStmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                                        VALUES (:oid, :pid, :qty, :price)");
                $itemStmt->execute([
                    ':oid'   => $orderId,
                    ':pid'   => $item['product_id'],
                    ':qty'   => $item['quantity'],
                    ':price' => $price
                ]);
            }

            // 5. Ja viss izdevās, apstiprinām izmaiņas
            $db->commit();
            return true;
        } catch (Exception $e) {
            // 6. Ja radās kļūda, atceļam pilnīgi visu (Rollback)
            $db->rollBack();
            return false;
        }
    }

    /**
     * SUB-SECTION: Update Order Status
     */
    public static function updateStatus($id, $status) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE orders SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    /**
     * SUB-SECTION: Delete Order
     */
    public static function delete($id) {
        $db = Database::getConnection();
        // Pateicoties datubāzes ON DELETE CASCADE, automātiski tiks dzēsti arī saistītie order_items
        $stmt = $db->prepare("DELETE FROM orders WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
// =========================================================================
// END SECTION: Order Model
// =========================================================================
?>