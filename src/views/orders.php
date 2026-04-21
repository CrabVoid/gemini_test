<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasker - Orders List</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #eee; margin: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h1 { color: #2c3e50; }
        .nav-links { display: flex; gap: 10px; }
        .nav-links a { background: #3498db; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; transition: background 0.2s; }
        .nav-links a:hover { background: #2980b9; }
        .card { background: #fff; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 5px #ccc; }
        .order-header { font-size: 1.1em; font-weight: bold; color: #2c3e50; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .order-meta { color: #7f8c8d; font-size: 0.9em; margin: 5px 0; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 0.85em; font-weight: bold; }
        .status-pending { background: #f39c12; color: white; }
        .status-shipped { background: #3498db; color: white; }
        .status-completed { background: #27ae60; color: white; }
        .items-section { margin-top: 10px; padding: 10px; background: #f9f9f9; border-left: 3px solid #3498db; }
        .items-header { font-weight: bold; color: #2980b9; margin-bottom: 8px; }
        .item-row { display: flex; justify-content: space-between; padding: 5px 0; color: #555; border-bottom: 1px solid #eee; }
        .item-row:last-child { border-bottom: none; }
        .item-details { flex: 1; }
        .item-product { font-weight: 500; }
        .item-qty { color: #7f8c8d; font-size: 0.9em; }
        .item-price { text-align: right; font-family: monospace; }
        .order-total { text-align: right; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #ddd; font-weight: bold; color: #27ae60; }
        .empty-message { text-align: center; color: #7f8c8d; padding: 20px; }
        .client-link { color: #3498db; text-decoration: none; }
        .client-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="header">
        <h1>📦 Orders</h1>
        <div class="nav-links">
            <a href="/">← Home</a>
            <a href="/customers.php">👥 Customers</a>
        </div>
    </div>

    <!-- 
    // =========================================================================
    // SECTION: Status Filter Controls
    // Purpose: Allows filtering orders by status (pending, shipped, completed)
    // =========================================================================
    -->
    <div style="margin-bottom: 20px; padding: 15px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px #ccc;">
        <strong style="color: #2c3e50;">Filter by Status:</strong>
        <div style="margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="/orders.php" style="padding: 6px 12px; border-radius: 4px; text-decoration: none; background: <?php echo ($statusFilter === null) ? '#27ae60' : '#95a5a6'; ?>; color: white; transition: background 0.2s;" 
               onmouseover="this.style.background='<?php echo ($statusFilter === null) ? '#229954' : '#7f8c8d'; ?>'" 
               onmouseout="this.style.background='<?php echo ($statusFilter === null) ? '#27ae60' : '#95a5a6'; ?>'">
                All Orders
            </a>
            <a href="/orders.php?status=pending" style="padding: 6px 12px; border-radius: 4px; text-decoration: none; background: <?php echo ($statusFilter === 'pending') ? '#f39c12' : '#95a5a6'; ?>; color: white; transition: background 0.2s;" 
               onmouseover="this.style.background='<?php echo ($statusFilter === 'pending') ? '#e67e22' : '#7f8c8d'; ?>'" 
               onmouseout="this.style.background='<?php echo ($statusFilter === 'pending') ? '#f39c12' : '#95a5a6'; ?>'">
                Pending
            </a>
            <a href="/orders.php?status=shipped" style="padding: 6px 12px; border-radius: 4px; text-decoration: none; background: <?php echo ($statusFilter === 'shipped') ? '#3498db' : '#95a5a6'; ?>; color: white; transition: background 0.2s;" 
               onmouseover="this.style.background='<?php echo ($statusFilter === 'shipped') ? '#2980b9' : '#7f8c8d'; ?>'" 
               onmouseout="this.style.background='<?php echo ($statusFilter === 'shipped') ? '#3498db' : '#95a5a6'; ?>'">
                Shipped
            </a>
            <a href="/orders.php?status=completed" style="padding: 6px 12px; border-radius: 4px; text-decoration: none; background: <?php echo ($statusFilter === 'completed') ? '#27ae60' : '#95a5a6'; ?>; color: white; transition: background 0.2s;" 
               onmouseover="this.style.background='<?php echo ($statusFilter === 'completed') ? '#229954' : '#7f8c8d'; ?>'" 
               onmouseout="this.style.background='<?php echo ($statusFilter === 'completed') ? '#27ae60' : '#95a5a6'; ?>'">
                Completed
            </a>
        </div>
    </div>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Status Filter Controls
    // -------------------------------------------------------------------------
    -->

    <?php if (empty($orders)): ?>
        <div class="card empty-message">
            <p>
                <?php if ($statusFilter !== null): ?>
                    No orders found with status: <strong><?php echo htmlspecialchars(ucfirst($statusFilter)); ?></strong>
                <?php else: ?>
                    No orders found.
                <?php endif; ?>
            </p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card">
                <div class="order-header">
                    <div>
                        <span>Order #<?php echo htmlspecialchars($order->id); ?></span>
                        <span class="status-badge status-<?php echo strtolower($order->status); ?>">
                            <?php echo htmlspecialchars(ucfirst($order->status)); ?>
                        </span>
                    </div>
                </div>

                <div class="order-meta">
                    <strong>Customer:</strong> 
                    <span class="client-link"><?php echo htmlspecialchars($order->client_name); ?></span>
                    (<?php echo htmlspecialchars($order->client_email); ?>)
                </div>

                <div class="order-meta">
                    <strong>Order Date:</strong> <?php echo htmlspecialchars($order->date); ?>
                </div>

                <?php if (!empty($order->items)): ?>
                    <div class="items-section">
                        <div class="items-header">Items:</div>
                        <?php $total = 0; ?>
                        <?php foreach ($order->items as $item): ?>
                            <?php $itemTotal = $item->getTotal(); $total += $itemTotal; ?>
                            <div class="item-row">
                                <div class="item-details">
                                    <div class="item-product"><?php echo htmlspecialchars($item->product); ?></div>
                                    <div class="item-qty">Qty: <?php echo htmlspecialchars($item->qty); ?></div>
                                </div>
                                <div class="item-price">
                                    $<?php echo number_format($item->price, 2); ?>
                                    <br>
                                    <span style="color: #27ae60; font-weight: bold;">$<?php echo number_format($itemTotal, 2); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="order-total">
                            Total: $<?php echo number_format($total, 2); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="items-section empty-message">
                        No items in this order
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
