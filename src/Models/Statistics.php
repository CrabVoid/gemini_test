<?php
// =========================================================================
// SECTION: Statistics Model
// Purpose: Encapsulates business logic for calculating dashboard statistics.
// Note: Provides aggregate data about customers and orders.
// =========================================================================

require_once __DIR__ . '/../../db/Database.php';

class Statistics {

    /**
     * Calculates and returns key business statistics.
     * Includes customer count, order counts, total revenue, etc.
     * 
     * @return array Associative array with all statistics.
     */
    public static function getDashboardStats() {
        $pdo = Database::getInstance()->getConnection();

        return [
            'totalCustomers' => self::getTotalCustomers($pdo),
            'totalOrders' => self::getTotalOrders($pdo),
            'ordersByStatus' => self::getOrdersByStatus($pdo),
            'totalRevenue' => self::getTotalRevenue($pdo),
            'averageOrderValue' => self::getAverageOrderValue($pdo),
            'topCustomers' => self::getTopCustomers($pdo),
        ];
    }

    /**
     * Get total number of customers.
     */
    private static function getTotalCustomers($pdo) {
        $result = $pdo->query("SELECT COUNT(*) as count FROM clients")->fetch();
        return $result['count'];
    }

    /**
     * Get total number of orders.
     */
    private static function getTotalOrders($pdo) {
        $result = $pdo->query("SELECT COUNT(*) as count FROM orders")->fetch();
        return $result['count'];
    }

    /**
     * Get count of orders grouped by status.
     */
    private static function getOrdersByStatus($pdo) {
        $result = $pdo->query("
            SELECT status, COUNT(*) as count
            FROM orders
            GROUP BY status
            ORDER BY status
        ")->fetchAll();
        
        $stats = [
            'pending' => 0,
            'shipped' => 0,
            'completed' => 0,
        ];
        
        foreach ($result as $row) {
            $stats[strtolower($row['status'])] = $row['count'];
        }
        
        return $stats;
    }

    /**
     * Calculate total revenue from all completed orders.
     */
    private static function getTotalRevenue($pdo) {
        $result = $pdo->query("
            SELECT SUM(oi.quantity * oi.price_at_purchase) as total
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'completed'
        ")->fetch();
        
        return $result['total'] ? (float)$result['total'] : 0.00;
    }

    /**
     * Calculate average order value (from completed orders).
     */
    private static function getAverageOrderValue($pdo) {
        $result = $pdo->query("
            SELECT AVG(order_total) as average
            FROM (
                SELECT o.id, SUM(oi.quantity * oi.price_at_purchase) as order_total
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.status = 'completed'
                GROUP BY o.id
            ) as completed_orders
        ")->fetch();
        
        return $result['average'] ? (float)$result['average'] : 0.00;
    }

    /**
     * Get top 5 customers by total spending.
     */
    private static function getTopCustomers($pdo) {
        $result = $pdo->query("
            SELECT 
                c.id,
                c.firstname,
                c.lastname,
                c.email,
                c.points,
                COUNT(DISTINCT o.id) as order_count,
                SUM(oi.quantity * oi.price_at_purchase) as total_spent
            FROM clients c
            LEFT JOIN orders o ON c.id = o.client_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.status = 'completed' OR o.id IS NULL
            GROUP BY c.id
            ORDER BY total_spent DESC
            LIMIT 5
        ")->fetchAll();
        
        return $result;
    }
}

// -------------------------------------------------------------------------
// END SECTION: Statistics Model
// -------------------------------------------------------------------------
?>
