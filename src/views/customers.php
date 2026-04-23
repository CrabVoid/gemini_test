<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .section { background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; }
        
        /* Formas stils */
        form input { padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; }
        .btn-add { background: #27ae60; }
        .btn-del { background: #e74c3c; font-size: 0.8em; }

        /* Tabulas stils */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
        
        /* Paziņojumi */
        .alert { padding: 10px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .alert-success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navigation.php'; ?>

    <div class="container">
        <h1>👥 Manage Customers</h1>

        <!-- Paziņojumu rādīšana -->
        <?php if ($error): ?> <div class="alert alert-error">❌ <?php echo $error; ?></div> <?php endif; ?>
        <?php if ($success): ?> <div class="alert alert-success">✅ <?php echo $success; ?></div> <?php endif; ?>

        <!-- Jauna Klienta Forma -->
        <div class="section">
            <h2>Add New Customer</h2>
            <form action="/customers.php" method="POST" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                <div style="display: flex; flex-direction: column;">
                    <label>First Name</label>
                    <input type="text" name="firstname" required>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <label>Last Name</label>
                    <input type="text" name="lastname" required>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <label>Loyalty Points</label>
                    <input type="number" name="points" value="0">
                </div>
                <button type="submit" class="btn-add">Add Customer</button>
            </form>
        </div>

        <!-- Klientu Saraksts -->
        <div class="section">
            <h2>Customer Directory</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Points</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td>#<?php echo $customer->id; ?></td>
                            <td><?php echo htmlspecialchars($customer->firstname . ' ' . $customer->lastname); ?></td>
                            <td><?php echo htmlspecialchars($customer->email); ?></td>
                            <td><strong><?php echo $customer->points; ?></strong></td>
                            <td>
                                <!-- Dzēšanas poga (izmantojot mazu formu, lai nosūtītu POST) -->
                                <form action="/customers.php" method="POST" onsubmit="return confirm('Tiešām dzēst?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $customer->id; ?>">
                                    <button type="submit" class="btn-del">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
