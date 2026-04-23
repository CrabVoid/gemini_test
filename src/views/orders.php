<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .section { background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
        
        /* Statusu krāsas */
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.85em; font-weight: bold; }
        .badge-pending { background: #fef9e7; color: #f39c12; }
        .badge-shipped { background: #ebf5fb; color: #3498db; }
        .badge-completed { background: #e9f7ef; color: #27ae60; }

        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .btn-green { background: #27ae60; color: white; }
        .btn-red { background: #e74c3c; color: white; }
        .btn-blue { background: #3498db; color: white; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navigation.php'; ?>

    <div class="container">
        <h1>📦 Manage Orders</h1>

        <!-- Jauna Pasūtījuma Izveide -->
        <div class="section">
            <h2>Place New Order</h2>
            <form action="/orders.php" method="POST" id="order-form">
                <input type="hidden" name="action" value="create">
                
                <!-- 1. Izvēlamies Klientu -->
                <div style="margin-bottom: 15px;">
                    <label><strong>1. Select Customer:</strong></label><br>
                    <select name="client_id" required style="width: 100%; padding: 8px; margin-top: 5px;">
                        <option value="">-- Choose a Customer --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>">
                                <?php echo htmlspecialchars($c->firstname . ' ' . $c->lastname); ?> (Points: <?php echo $c->points; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Pievienojam Preces (Dinamiski) -->
                <div style="margin-bottom: 15px;">
                    <label><strong>2. Select Products:</strong></label>
                    <div id="product-rows-container">
                        <div class="product-row" style="display: flex; gap: 10px; margin-top: 5px;">
                            <select name="product_ids[]" required style="flex: 2; padding: 8px;">
                                <option value="">-- Select Product --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?> ($<?php echo $p->price; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" name="quantities[]" value="1" min="1" style="flex: 1; padding: 8px;">
                        </div>
                    </div>
                    <button type="button" id="add-product-btn" style="margin-top: 10px; background: #ecf0f1; border: 1px solid #bdc3c7; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                        ➕ Add Another Product
                    </button>
                </div>

                <button type="submit" class="btn btn-green">Create Order</button>
            </form>
        </div>

        <!-- Pasūtījumu Saraksts -->
        <div class="section">
            <h2>Order History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order->id; ?></td>
                            <td><?php echo htmlspecialchars($order->client_name); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $order->status; ?>">
                                    <?php echo strtoupper($order->status); ?>
                                </span>
                            </td>
                            <td><strong>$<?php echo number_format($order->total_amount, 2); ?></strong></td>
                            <td><?php echo $order->order_date; ?></td>
                            <td style="display: flex; gap: 5px;">
                                <!-- Statusa maiņas pogas -->
                                <?php if ($order->status === 'pending'): ?>
                                    <form method="POST" action="/orders.php">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                        <input type="hidden" name="status" value="shipped">
                                        <button type="submit" class="btn btn-blue" style="font-size: 0.75em;">Mark Shipped</button>
                                    </form>
                                <?php elseif ($order->status === 'shipped'): ?>
                                    <form method="POST" action="/orders.php">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-green" style="font-size: 0.75em;">Mark Completed</button>
                                    </form>
                                <?php endif; ?>

                                <!-- Dzēšanas poga -->
                                <form method="POST" action="/orders.php" onsubmit="return confirm('Dzēst pasūtījumu?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                    <button type="submit" class="btn btn-red" style="font-size: 0.75em;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript priekš dinamiskām preču rindām -->
    <script>
        document.getElementById('add-product-btn').addEventListener('click', function() {
            const container = document.getElementById('product-rows-container');
            const firstRow = container.querySelector('.product-row');
            const newRow = firstRow.cloneNode(true);
            
            // Notīrām izvēli jaunajā rindā
            newRow.querySelector('select').value = "";
            newRow.querySelector('input').value = "1";
            
            container.appendChild(newRow);
        });
    </script>
</body>
</html>
