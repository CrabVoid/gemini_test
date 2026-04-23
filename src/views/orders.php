<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasker - Orders List</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #eee; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .page-title { color: #2c3e50; margin-bottom: 20px; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; display: none; }
        h1 { color: #2c3e50; display: none; }
        .nav-links { display: flex; gap: 10px; display: none; }
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
        
        /* Form Styling */
        .form-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px #ccc; margin-bottom: 30px; border-top: 4px solid #3498db; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #34495e; }
        .form-group select, .form-group input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; background: #fff; }
        .btn-submit { background: #3498db; color: #fff; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-submit:hover { background: #2980b9; }
        .alert { padding: 10px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navigation.php'; ?>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="page-title" style="margin-bottom: 0; border-bottom: none;">Orders Management</h2>
            <button id="toggleFormBtn" class="btn-submit" style="background: #27ae60;">+ Create New Order</button>
        </div>

        <!-- 
        // =========================================================================
        // SECTION: Notifications
        // Purpose: Displays success or error messages.
        // =========================================================================
        -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'order_created'): ?>
            <div class="alert alert-success">✓ New order placed successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] === 'order_updated'): ?>
            <div class="alert alert-success">✓ Order updated successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'order_deleted'): ?>
            <div class="alert alert-success">✓ Order deleted successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <!-- -------------------------------------------------------------------------
        // END SECTION: Notifications
        // -------------------------------------------------------------------------
        -->

        <!-- 
        // =========================================================================
        // SECTION: Order Creation Form
        // Purpose: Form to place a new order for a customer.
        // =========================================================================
        -->
        <div id="orderForm" class="form-card" style="display: <?= isset($error) ? 'block' : 'none' ?>;">
            <h3 style="margin-top: 0; color: #3498db;">Place New Order</h3>
            <form method="POST" action="orders.php">
                <input type="hidden" name="action" value="create_order">
                
                <div class="form-group">
                    <label for="client_id">Customer</label>
                    <select name="client_id" id="client_id" required>
                        <option value="">-- Select Customer --</option>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c->id ?>"><?= htmlspecialchars($c->name) ?> (<?= htmlspecialchars($c->email) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 2;">
                        <label for="product_id">Product</label>
                        <select name="product_id" id="product_id" required>
                            <option value="">-- Select Product --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p->id ?>"><?= htmlspecialchars($p->name) ?> - €<?= number_format($p->price, 2) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Initial Status</label>
                    <select name="status" id="status">
                        <option value="pending">Pending</option>
                        <option value="shipped">Shipped</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-submit">Place Order</button>
                    <button type="button" onclick="document.getElementById('orderForm').style.display='none'" class="btn-submit" style="background: #95a5a6;">Cancel</button>
                </div>
            </form>
        </div>

        <script>
            document.getElementById('toggleFormBtn').addEventListener('click', function() {
                var form = document.getElementById('orderForm');
                if (form.style.display === 'none') {
                    form.style.display = 'block';
                    form.scrollIntoView({ behavior: 'smooth' });
                } else {
                    form.style.display = 'none';
                }
            });

            function toggleEditForm(orderId) {
                var form = document.getElementById('editForm-' + orderId);
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            }
        </script>
        <!-- -------------------------------------------------------------------------
        // END SECTION: Order Creation Form
        // -------------------------------------------------------------------------
        -->

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

                    <!-- Actions -->
                    <div style="margin-top: 15px; display: flex; gap: 10px; border-top: 1px solid #eee; padding-top: 10px;">
                        <button onclick="toggleEditForm(<?= $order->id ?>)" class="btn-submit" style="padding: 5px 10px; font-size: 0.85em;">Edit Status/Customer</button>
                        
                        <form method="POST" action="orders.php" onsubmit="return confirm('Are you sure you want to delete this order?')">
                            <input type="hidden" name="action" value="delete_order">
                            <input type="hidden" name="order_id" value="<?= $order->id ?>">
                            <button type="submit" class="btn-submit" style="background: #e74c3c; padding: 5px 10px; font-size: 0.85em;">Delete</button>
                        </form>
                    </div>

                    <!-- Inline Edit Form (Hidden by default) -->
                    <div id="editForm-<?= $order->id ?>" style="display: none; margin-top: 15px; padding: 15px; border: 1px solid #3498db; border-radius: 4px; background: #f0f7fd;">
                        <h4 style="margin-top: 0; color: #3498db;">Edit Order #<?= $order->id ?></h4>
                        <form method="POST" action="orders.php">
                            <input type="hidden" name="action" value="update_order">
                            <input type="hidden" name="order_id" value="<?= $order->id ?>">
                            
                            <div class="form-group">
                                <label>Change Customer</label>
                                <select name="client_id" required>
                                    <?php foreach ($clients as $c): ?>
                                        <option value="<?= $c->id ?>" <?= $c->id == $order->client_id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Update Status</label>
                                <select name="status">
                                    <option value="pending" <?= $order->status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="shipped" <?= $order->status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="completed" <?= $order->status === 'completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn-submit" style="padding: 6px 12px; font-size: 0.9em;">Save Changes</button>
                                <button type="button" onclick="toggleEditForm(<?= $order->id ?>)" class="btn-submit" style="background: #95a5a6; padding: 6px 12px; font-size: 0.9em;">Cancel</button>
                            </div>
                        </form>
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
                                        €<?php echo number_format($item->price, 2); ?>
                                        <br>
                                        <span style="color: #27ae60; font-weight: bold;">€<?php echo number_format($itemTotal, 2); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="order-total">
                                Total: €<?php echo number_format($total, 2); ?>
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
    </div>
</body>
</html>
