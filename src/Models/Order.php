<?php
require_once __DIR__ . '/../../db/Database.php';
require_once __DIR__ . '/../../Models.php';
require_once __DIR__ . '/DeliveryCompany.php';

// =========================================================================
// SECTION: Order Model
// Purpose: Manages complex interactions with 'orders' and 'order_items' tables.
// =========================================================================
class OrderModel {

    /**
     * SUB-SECTION: Fetch All Orders
     */
    public static function all() {
        $db = Database::getConnection();
        
        $sql = "SELECT o.*, 
                       (c.firstname || ' ' || c.lastname) as client_name,
                       dc.name as delivery_name,
                       SUM(oi.quantity * oi.price_at_purchase) as total_amount
                FROM orders o
                JOIN clients c ON o.client_id = c.id
                LEFT JOIN delivery_companies dc ON o.delivery_company_id = dc.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                GROUP BY o.id
                ORDER BY o.id DESC";
                
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Order');
    }

    /**
     * SUB-SECTION: Create Order with Profit & Tax
     */
    public static function create($data) {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $dc = DeliveryCompanyModel::find($data['delivery_company_id']);
            $taxRate = 0.21; // 21% PVN
            
            // 1. Aprēķinām svaru un kopējās cenas (Weight & Prices)
            $totalWeight = 0;
            $totalSell = 0;
            $totalBuy = 0;
            $processedItems = [];

            foreach ($data['items'] as $item) {
                $prodStmt = $db->prepare("SELECT price, buy_price, weight FROM products WHERE id = :id");
                $prodStmt->execute([':id' => $item['product_id']]);
                $product = $prodStmt->fetch(PDO::FETCH_ASSOC);

                $qty = (int)$item['quantity'];
                $totalWeight += $product['weight'] * $qty;
                $totalSell += $product['price'] * $qty;
                $totalBuy += $product['buy_price'] * $qty;

                $processedItems[] = [
                    'pid'   => $item['product_id'],
                    'qty'   => $qty,
                    'price' => $product['price'],
                    'buy'   => $product['buy_price']
                ];
            }

            // 2. Aprēķinām piegādes izmaksas (Shipping Cost)
            $shipping = $dc ? ($dc->base_cost + ($dc->cost_per_kg * $totalWeight)) : 0;
            
            // 3. Aprēķinām nodokli un peļņu (Tax & Profit)
            $taxAmount = $totalSell * $taxRate;
            $profit = ($totalSell - $totalBuy) - $taxAmount - $shipping;

            // 4. Saglabājam pasūtījumu
            $stmt = $db->prepare("INSERT INTO orders (client_id, delivery_company_id, status, tax_rate, shipping_cost, total_profit, order_date) 
                                 VALUES (:cid, :dcid, 'pending', :tax, :ship, :profit, datetime('now'))");
            $stmt->execute([
                ':cid'   => $data['client_id'],
                ':dcid'  => $data['delivery_company_id'],
                ':tax'   => $taxRate,
                ':ship'  => $shipping,
                ':profit' => $profit
            ]);
            
            $orderId = $db->lastInsertId();

            // 5. Saglabājam preces ar vēsturiskajām cenām
            foreach ($processedItems as $pi) {
                $itemStmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, buy_price_at_purchase) 
                                        VALUES (:oid, :pid, :qty, :price, :buy)");
                $itemStmt->execute([
                    ':oid'   => $orderId,
                    ':pid'   => $pi['pid'],
                    ':qty'   => $pi['qty'],
                    ':price' => $pi['price'],
                    ':buy'   => $pi['buy']
                ]);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public static function updateStatus($id, $status) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE orders SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM orders WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
// =========================================================================
// END SECTION: Order Model
// =========================================================================
?>
