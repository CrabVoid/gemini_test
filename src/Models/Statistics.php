<?php
require_once __DIR__ . '/../../db/Database.php';

// =========================================================================
// SECTION: Statistics Model
// Purpose: Provides high-level data summaries for the dashboard.
// =========================================================================
class StatisticsModel {

    /**
     * SUB-SECTION: Get Dashboard Statistics
     * Purpose: Aggregates data from multiple tables for the home view.
     */
    public static function getDashboardStats() {
        $db = Database::getConnection();
        
        $stats = [];

        // 1. Saskaitām visus klientus
        $stats['totalCustomers'] = $db->query("SELECT COUNT(*) FROM clients")->fetchColumn();

        // 2. Saskaitām visus pasūtījumus
        $stats['totalOrders'] = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

        // 3. Saskaitām pasūtījumus pēc statusiem
        $stats['ordersByStatus'] = [
            'pending'   => $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
            'shipped'   => $db->query("SELECT COUNT(*) FROM orders WHERE status = 'shipped'")->fetchColumn(),
            'completed' => $db->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetchColumn(),
        ];

        // 4. Aprēķinām kopējo ieņēmumu summu (tikai pabeigtajiem pasūtījumiem)
        $sqlRevenue = "SELECT SUM(oi.quantity * oi.price_at_purchase) 
                      FROM order_items oi 
                      JOIN orders o ON oi.order_id = o.id 
                      WHERE o.status = 'completed'";
        $stats['totalRevenue'] = $db->query($sqlRevenue)->fetchColumn() ?? 0;

        // 5. Aprēķinām vidējo pasūtījuma vērtību
        if ($stats['ordersByStatus']['completed'] > 0) {
            $stats['averageOrderValue'] = $stats['totalRevenue'] / $stats['ordersByStatus']['completed'];
        } else {
            $stats['averageOrderValue'] = 0;
        }

        // 6. Atrodam TOP klientus (kas iztērējuši visvairāk naudas)
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
// =========================================================================
// END SECTION: Statistics Model
// =========================================================================
?>