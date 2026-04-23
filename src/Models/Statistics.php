<?php
require_once __DIR__ . '/../../db/Database.php';

// =========================================================================
// SECTION: Statistics Model
// Purpose: Provides high-level data summaries for the dashboard.
// =========================================================================
class StatisticsModel {

    /**
     * SUB-SECTION: Get Dashboard Statistics
     */
    public static function getDashboardStats() {
        $db = Database::getConnection();
        
        $stats = [];

        // 1. Klientu un pasūtījumu skaits
        $stats['totalCustomers'] = $db->query("SELECT COUNT(*) FROM clients")->fetchColumn();
        $stats['totalOrders']    = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

        // 2. Pasūtījumi pa statusiem
        $stats['ordersByStatus'] = [
            'pending'   => $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
            'shipped'   => $db->query("SELECT COUNT(*) FROM orders WHERE status = 'shipped'")->fetchColumn(),
            'completed' => $db->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetchColumn(),
        ];

        // 3. Ieņēmumi un Peļņa (tikai pabeigtajiem)
        $sqlFin = "SELECT 
                    SUM(oi.quantity * oi.price_at_purchase) as revenue,
                    SUM(o.total_profit) as profit
                   FROM orders o
                   LEFT JOIN order_items oi ON o.id = oi.order_id
                   WHERE o.status = 'completed'";
        $row = $db->query($sqlFin)->fetch(PDO::FETCH_ASSOC);
        
        $stats['totalRevenue'] = $row['revenue'] ?? 0;
        $stats['totalProfit']  = $row['profit'] ?? 0;

        // 4. Vidējā pasūtījuma vērtība
        $stats['averageOrderValue'] = ($stats['ordersByStatus']['completed'] > 0) 
            ? $stats['totalRevenue'] / $stats['ordersByStatus']['completed'] : 0;

        // 5. TOP klienti
        $sqlTop = "SELECT c.*, 
                          COUNT(DISTINCT o.id) as order_count,
                          SUM(oi.quantity * oi.price_at_purchase) as total_spent
                   FROM clients c
                   JOIN orders o ON c.id = o.client_id
                   JOIN order_items oi ON o.id = oi.order_id
                   GROUP BY c.id
                   ORDER BY total_spent DESC
                   LIMIT 5";
        $stats['topCustomers'] = $db->query($sqlTop)->fetchAll();

        return $stats;
    }
}
?>
