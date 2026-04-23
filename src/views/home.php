<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Tasker - Sākums</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat-label { color: #7f8c8d; text-transform: uppercase; font-size: 0.8em; font-weight: bold; }
        .stat-value { font-size: 2em; font-weight: bold; color: #2c3e50; margin: 10px 0; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 3px; }
        .section { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        h2 { border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .top-item { background: #f9f9f9; padding: 12px; border-radius: 6px; border-left: 4px solid #3498db; margin-bottom: 10px; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navigation.php'; ?>

    <div class="container">
        <h1>📊 Galvenais panelis</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Klienti</div>
                <div class="stat-value"><?= $stats['totalCustomers'] ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Pasūtījumi</div>
                <div class="stat-value"><?= $stats['totalOrders'] ?></div>
                <div style="font-size: 0.8em;">
                    <span class="status-dot" style="background:#f1c40f"></span> <?= $stats['ordersByStatus']['pending'] ?> 
                    <span class="status-dot" style="background:#3498db"></span> <?= $stats['ordersByStatus']['shipped'] ?> 
                    <span class="status-dot" style="background:#2ecc71"></span> <?= $stats['ordersByStatus']['completed'] ?>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Ieņēmumi</div>
                <div class="stat-value" style="color: #27ae60;"><?= number_format($stats['totalRevenue'], 2) ?> €</div>
            </div>

            <div class="stat-card" style="border: 2px solid #2ecc71;">
                <div class="stat-label" style="color: #27ae60;">Neto Peļņa</div>
                <div class="stat-value" style="color: #2ecc71;"><?= number_format($stats['totalProfit'], 2) ?> €</div>
                <div style="font-size: 0.75em; color: #7f8c8d;">Pēc PVN un Piegādes</div>
            </div>
        </div>

        <div class="section">
            <h2>🏆 Labākie klienti</h2>
            <?php foreach ($stats['topCustomers'] as $customer): ?>
                <div class="top-item">
                    <strong><?= htmlspecialchars($customer['firstname'] . ' ' . $customer['lastname']) ?></strong>
                    <span style="color: #7f8c8d;"> - Iztērēti: <?= number_format($customer['total_spent'], 2) ?> € (<?= $customer['order_count'] ?> pasūt.)</span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="section">
            <h2>📍 Ātrās darbības</h2>
            <div style="display: flex; gap: 10px;">
                <a href="/customers.php" style="background: #3498db; color: white; padding: 12px 20px; border-radius: 6px; text-decoration: none; font-weight: bold;">👥 Klienti</a>
                <a href="/orders.php" style="background: #27ae60; color: white; padding: 12px 20px; border-radius: 6px; text-decoration: none; font-weight: bold;">📦 Pasūtījumi</a>
                <a href="/delivery.php" style="background: #e67e22; color: white; padding: 12px 20px; border-radius: 6px; text-decoration: none; font-weight: bold;">🚚 Piegāde</a>
            </div>
        </div>
    </div>
</body>
</html>
