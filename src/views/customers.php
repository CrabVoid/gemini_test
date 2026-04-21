<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasker - Customers List</title>
    <!-- 
    // =========================================================================
    // SECTION: Styling
    // Purpose: Visual presentation of the customers list.
    // =========================================================================
    -->
    <style>
        body { font: 14px Arial, sans-serif; background: #eee; margin: 20px; }
        .card { background: #fff; padding: 15px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px #ccc; }
        .client-header { font-size: 1.2em; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; color: #2c3e50; }
        .order-container { margin-left: 15px; border-left: 3px solid #3498db; padding-left: 10px; margin-top: 10px; }
        .order-title { font-weight: bold; color: #2980b9; }
        .item-list { margin: 5px 0 10px 0; padding: 0; list-style: none; }
        .item-row { display: flex; justify-content: space-between; padding: 3px 0; color: #555; }
        .item-details { color: #7f8c8d; font-size: 0.9em; }
        .price-text { font-family: monospace; }
        .total-row { border-top: 1px dashed #ddd; margin-top: 5px; padding-top: 5px; text-align: right; font-weight: bold; }
        .btn-toggle { display: inline-block; background: #3498db; color: #fff; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 0.85em; margin-bottom: 15px; font-weight: bold; transition: background 0.2s; }
        .btn-toggle:hover { background: #2980b9; }
        .btn-hide { background: #95a5a6; }
        .btn-hide:hover { background: #7f8c8d; }
    </style>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Styling
    // -------------------------------------------------------------------------
    -->
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navigation.php'; ?>

    <!-- Page Title -->
    <h2 style="color: #2c3e50; margin-bottom: 20px; border-bottom: 2px solid #3498db; padding-bottom: 10px;">Customers Directory</h2>

    <!-- 
    // =========================================================================
    // SECTION: Toggle Control
    // Purpose: Provides a button to show or hide the detailed order hierarchy.
    // =========================================================================
    -->
    <?php if (!$showOrders): ?>
        <a href="?with-orders=full" class="btn-toggle">Show Orders</a>
    <?php else: ?>
        <a href="?with-orders=none" class="btn-toggle btn-hide">Hide Orders</a>
    <?php endif; ?>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Toggle Control
    // -------------------------------------------------------------------------
    -->

    <!-- 
    // =========================================================================
    // SECTION: Content Rendering
    // Purpose: Loops through customers and optionally their orders.
    // =========================================================================
    -->
    <?php foreach ($clients as $c): ?>
        <div class="card">
            <div class="client-header">
                <strong><?= htmlspecialchars($c->name) ?></strong> 
                <span style="font-size: 0.8em; color: #7f8c8d;">(ID: <?= $c->id ?> | <?= htmlspecialchars($c->email) ?>)</span>
            </div>
            
            <?php if ($showOrders): ?>
                <?php if (empty($c->orders)): ?>
                    <p style="color: #95a5a6; font-style: italic;">No orders found.</p>
                <?php else: ?>
                    <?php foreach ($c->orders as $o): ?>
                        <div class="order-container">
                            <div class="order-title">
                                Order #<?= $o->id ?> 
                                <span style="font-weight: normal; font-size: 0.9em;">[<?= htmlspecialchars($o->status) ?>]</span>
                            </div>
                            <div style="font-size: 0.85em; color: #95a5a6; margin-bottom: 5px;">Date: <?= htmlspecialchars($o->date) ?></div>
                            
                            <div class="item-list">
                                <?php $orderTotal = 0; ?>
                                <?php foreach ($o->items as $i): ?>
                                    <?php $itemTotal = $i->getTotal(); $orderTotal += $itemTotal; ?>
                                    <div class="item-row">
                                        <span>
                                            <?= htmlspecialchars($i->product) ?> 
                                            <span class="item-details">(x<?= $i->qty ?> @ <?= number_format($i->price, 2) ?> €)</span>
                                        </span>
                                        <span class="price-text"><?= number_format($itemTotal, 2) ?> €</span>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="total-row">
                                    Total: <span class="price-text" style="color: #e74c3c;"><?= number_format($orderTotal, 2) ?> €</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Content Rendering
    // -------------------------------------------------------------------------
    -->

</body>
</html>
