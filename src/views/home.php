<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasker - Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #ecf0f1; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #2c3e50; margin-bottom: 30px; font-size: 2.5em; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat-value { font-size: 2.5em; font-weight: bold; color: #3498db; margin: 10px 0; }
        .stat-label { font-size: 0.9em; color: #7f8c8d; text-transform: uppercase; letter-spacing: 1px; }
        .stat-subtext { font-size: 0.85em; color: #95a5a6; margin-top: 5px; }
        
        .status-breakdown { display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap; }
        .status-item { display: flex; align-items: center; gap: 5px; font-size: 0.9em; }
        .status-dot { width: 12px; height: 12px; border-radius: 50%; }
        
        .pending { background: #f39c12; }
        .shipped { background: #3498db; }
        .completed { background: #27ae60; }
        
        .section { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .section h2 { color: #2c3e50; margin-bottom: 20px; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        
        .top-customers { margin-bottom: 30px; }
        .customer-card { background: #f9f9f9; padding: 15px; border-radius: 6px; border-left: 4px solid #3498db; margin-bottom: 10px; }
        .customer-name { font-weight: bold; color: #2c3e50; }
        .customer-email { font-size: 0.85em; color: #7f8c8d; }
        .customer-stats { display: flex; gap: 20px; margin-top: 8px; font-size: 0.9em; }
        .customer-stat { display: flex; flex-direction: column; }
        .customer-stat-label { color: #95a5a6; font-size: 0.8em; }
        .customer-stat-value { color: #27ae60; font-weight: bold; }
        
        .empty-state { text-align: center; color: #95a5a6; padding: 30px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #ecf0f1; padding: 12px; text-align: left; color: #2c3e50; font-weight: bold; border-bottom: 2px solid #bdc3c7; }
        td { padding: 12px; border-bottom: 1px solid #ecf0f1; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navigation.php'; ?>

    <div class="container">
        <!-- =====================================================================
        SECTION: Page Header
        Purpose: Display dashboard title and welcome message
        ===================================================================== -->
        <h1>📊 Dashboard</h1>

        <!-- =====================================================================
        SECTION: Statistics Cards
        Purpose: Display key metrics in an easy-to-read grid
        ===================================================================== -->
        <div class="stats-grid">
            <!-- Total Customers Card -->
            <div class="stat-card">
                <div class="stat-label">Total Customers</div>
                <div class="stat-value"><?php echo $stats['totalCustomers']; ?></div>
                <div class="stat-subtext">Active customer accounts</div>
            </div>

            <!-- Total Orders Card -->
            <div class="stat-card">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value"><?php echo $stats['totalOrders']; ?></div>
                <div class="stat-subtext">Across all statuses</div>
                <div class="status-breakdown">
                    <div class="status-item">
                        <span class="status-dot pending"></span>
                        <span>Pending: <?php echo $stats['ordersByStatus']['pending']; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-dot shipped"></span>
                        <span>Shipped: <?php echo $stats['ordersByStatus']['shipped']; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-dot completed"></span>
                        <span>Completed: <?php echo $stats['ordersByStatus']['completed']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Total Revenue Card -->
            <div class="stat-card">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">$<?php echo number_format($stats['totalRevenue'], 2); ?></div>
                <div class="stat-subtext">From completed orders</div>
            </div>

            <!-- Average Order Value Card -->
            <div class="stat-card">
                <div class="stat-label">Average Order Value</div>
                <div class="stat-value">$<?php echo number_format($stats['averageOrderValue'], 2); ?></div>
                <div class="stat-subtext">Per completed order</div>
            </div>
        </div>

        <!-- =====================================================================
        SECTION: Top Customers
        Purpose: Show highest-spending customers
        ===================================================================== -->
        <div class="section">
            <h2>🏆 Top Customers</h2>
            <?php if (!empty($stats['topCustomers'])): ?>
                <div class="top-customers">
                    <?php foreach ($stats['topCustomers'] as $customer): ?>
                        <div class="customer-card">
                            <div class="customer-name">
                                <?php echo htmlspecialchars($customer['firstname'] . ' ' . $customer['lastname']); ?>
                            </div>
                            <div class="customer-email">
                                <?php echo htmlspecialchars($customer['email']); ?>
                            </div>
                            <div class="customer-stats">
                                <div class="customer-stat">
                                    <span class="customer-stat-label">Orders</span>
                                    <span class="customer-stat-value"><?php echo $customer['order_count']; ?></span>
                                </div>
                                <div class="customer-stat">
                                    <span class="customer-stat-label">Total Spent</span>
                                    <span class="customer-stat-value">$<?php echo number_format($customer['total_spent'] ?? 0, 2); ?></span>
                                </div>
                                <div class="customer-stat">
                                    <span class="customer-stat-label">Loyalty Points</span>
                                    <span class="customer-stat-value"><?php echo $customer['points']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No customer data available yet</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- =====================================================================
        SECTION: Quick Links
        Purpose: Navigation to main sections
        ===================================================================== -->
        <div class="section">
            <h2>📍 Quick Navigation</h2>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="/customers.php" style="flex: 1; min-width: 150px; padding: 12px; background: #3498db; color: white; text-decoration: none; border-radius: 6px; text-align: center; transition: background 0.2s; font-weight: bold;"
                   onmouseover="this.style.background='#2980b9'" onmouseout="this.style.background='#3498db'">
                    👥 View Customers Directory
                </a>
                <a href="/orders.php" style="flex: 1; min-width: 150px; padding: 12px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; text-align: center; transition: background 0.2s; font-weight: bold;"
                   onmouseover="this.style.background='#229954'" onmouseout="this.style.background='#27ae60'">
                    📦 Manage Orders
                </a>
            </div>
        </div>    </div>

</body>
</html>
