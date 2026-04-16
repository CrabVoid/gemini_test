<?php
require_once 'ClientRepository.php';

$repository = new ClientRepository();
$clients = $repository->getAllWithHierarchy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Content - OOP Hierarchical View</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .client-card { background: #fff; border-radius: 8px; margin-bottom: 30px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-left: 5px solid #007bff; }
        .client-info h2 { margin: 0; color: #333; }
        .client-meta { color: #666; font-size: 0.9em; margin-bottom: 15px; }
        
        .order-section { margin-left: 40px; border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px; }
        .order-header { font-weight: bold; background: #e9ecef; padding: 5px 10px; border-radius: 4px; display: inline-block; margin-bottom: 10px; }
        .order-status { font-size: 0.8em; text-transform: uppercase; padding: 2px 6px; border-radius: 3px; background: #28a745; color: #fff; }
        
        .items-list { margin-left: 40px; list-style: none; padding: 0; }
        .item-row { display: flex; gap: 20px; padding: 5px 0; border-bottom: 1px dashed #ddd; font-size: 0.95em; }
        .item-row span { color: #555; }
        .item-product { font-weight: bold; min-width: 150px; }
    </style>
</head>
<body>

<h1>Client & Order Overview (OOP Version)</h1>

<?php if (empty($clients)): ?>
    <p>No records found.</p>
<?php else: ?>
    <?php foreach ($clients as $client): ?>
        <div class="client-card">
            <!-- Client Object Properties -->
            <div class="client-info">
                <h2><?php echo htmlspecialchars($client->name); ?> (ID: <?php echo $client->id; ?>)</h2>
                <div class="client-meta">
                    Email: <?php echo htmlspecialchars($client->email); ?> | Points: <?php echo $client->points; ?>
                </div>
            </div>

            <?php if (empty($client->orders)): ?>
                <p style="color: #999; margin-left: 40px;">No orders found.</p>
            <?php else: ?>
                <?php foreach ($client->orders as $order): ?>
                    <!-- Order Object Properties -->
                    <div class="order-section">
                        <div class="order-header">
                            Order #<?php echo $order->id; ?> | <?php echo $order->date; ?> 
                            <span class="order-status"><?php echo htmlspecialchars($order->status); ?></span>
                        </div>

                        <?php if (empty($order->items)): ?>
                            <p style="color: #999; margin-left: 40px;">Empty order.</p>
                        <?php else: ?>
                            <ul class="items-list">
                                <?php foreach ($order->items as $item): ?>
                                    <!-- OrderItem Object Properties & Method -->
                                    <li class="item-row">
                                        <span class="item-product"><?php echo htmlspecialchars($item->product); ?></span>
                                        <span>Qty: <?php echo $item->qty; ?></span>
                                        <span>Price: $<?php echo number_format($item->price, 2); ?></span>
                                        <span>Total: $<?php echo number_format($item->getTotal(), 2); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
