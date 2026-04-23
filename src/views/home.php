<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasker - Dashboard</title>
    <style>
        /* Pamata stils */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #ecf0f1; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        /* Statistikas kartītes */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat-value { font-size: 2.5em; font-weight: bold; color: #3498db; margin: 10px 0; }
        
        /* Statusu punkti */
        .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .pending { background: #f39c12; }
        .shipped { background: #3498db; }
        .completed { background: #27ae60; }
        
        /* Sekcijas */
        .section { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        h2 { border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        
        /* Klientu kartītes */
        .customer-card { background: #f9f9f9; padding: 15px; border-radius: 6px; border-left: 4px solid #3498db; margin-bottom: 10px; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navigation.php'; ?>

    <div class="container">
        <!-- Dashboard Virsraksts -->
        <h1 style="margin-bottom: 20px;">📊 Dashboard</h1>

        <!-- Statistikas Rindas -->
        <div class="stats-grid">
            <!-- Kopējais Klientu Skaits -->
            <div class="stat-card">
                <div style="color: #7f8c8d; text-transform: uppercase;">Customers</div>
                <div class="stat-value"><?php echo $stats['totalCustomers']; ?></div>
            </div>

            <!-- Pasūtījumi un Statusi -->
            <div class="stat-card">
                <div style="color: #7f8c8d; text-transform: uppercase;">Orders</div>
                <div class="stat-value"><?php echo $stats['totalOrders']; ?></div>
                <div style="font-size: 0.85em;">
                    <span class="status-dot pending"></span> P: <?php echo $stats['ordersByStatus']['pending']; ?>
                    <span class="status-dot shipped"></span> S: <?php echo $stats['ordersByStatus']['shipped']; ?>
                    <span class="status-dot completed"></span> C: <?php echo $stats['ordersByStatus']['completed']; ?>
                </div>
            </div>

            <!-- Kopējie Ieņēmumi -->
            <div class="stat-card">
                <div style="color: #7f8c8d; text-transform: uppercase;">Revenue</div>
                <div class="stat-value">$<?php echo number_format($stats['totalRevenue'], 2); ?></div>
            </div>

            <!-- Vidējā Vērtība -->
            <div class="stat-card">
                <div style="color: #7f8c8d; text-transform: uppercase;">Avg. Order</div>
                <div class="stat-value">$<?php echo number_format($stats['averageOrderValue'], 2); ?></div>
            </div>
        </div>

        <!-- TOP Klienti -->
        <div class="section">
            <h2>🏆 Top Customers (High Spenders)</h2>
            <?php foreach ($stats['topCustomers'] as $customer): ?>
                <div class="customer-card">
                    <strong><?php echo htmlspecialchars($customer['firstname'] . ' ' . $customer['lastname']); ?></strong>
                    <span style="color: #7f8c8d;"> - Spent: $<?php echo number_format($customer['total_spent'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Ātrā Navigācija -->
        <div class="section">
            <h2>📍 Quick Actions</h2>
            <div style="display: flex; gap: 10px;">
                <a href="/customers.php" style="background: #3498db; color: white; padding: 12px; border-radius: 6px; text-decoration: none;">👥 View Customers</a>
                <a href="/orders.php" style="background: #27ae60; color: white; padding: 12px; border-radius: 6px; text-decoration: none;">📦 Manage Orders</a>
            </div>
        </div>
    </div>
</body>
</html>
