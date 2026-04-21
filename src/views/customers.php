<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasker - Client Overview</title>
    <!-- 
    // =========================================================================
    // SECTION: Styling
    // Purpose: Visual presentation of the client list.
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
    </style>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Styling
    // -------------------------------------------------------------------------
    -->
</head>
<body>

    <h1 style="color: #2c3e50;">Store Dashboard</h1>

    <!-- 
    // =========================================================================
    // SECTION: Content Rendering
    // Purpose: Loops through data and outputs structured HTML.
    // =========================================================================
    -->
    <?php foreach ($clients as $c): ?>
        <div class="card">
            <div class="client-header">
                <strong><?= htmlspecialchars($c->name) ?></strong> 
                <span style="font-size: 0.8em; color: #7f8c8d;">(ID: <?= $c->id ?> | <?= htmlspecialchars($c->email) ?>)</span>
            </div>
            
            <?php if (empty($c->orders)): ?>
                <p style="color: #95a5a6; font-style: italic;">No orders found.</p>
            <?php endif; ?>

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
        </div>
    <?php endforeach; ?>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Content Rendering
    // -------------------------------------------------------------------------
    -->

</body>
</html>
