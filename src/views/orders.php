<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pasūtījumu pārvaldība - Tasker</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .section { background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 20px; }
        h2 { border-bottom: 2px solid #27ae60; padding-bottom: 10px; margin-bottom: 20px; }
        
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }

        form label { display: block; margin-bottom: 5px; font-weight: bold; color: #7f8c8d; }
        form select, form input { padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }
        button { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; }
        .btn-submit { background: #27ae60; width: 100%; margin-top: 10px; }
        .btn-add-item { background: #7f8c8d; padding: 5px 10px; font-size: 0.9em; }
        .btn-action { padding: 5px 10px; font-size: 0.85em; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; vertical-align: top; }
        th { background: #f9f9f9; color: #2c3e50; }
        
        .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 0.85em; font-weight: bold; color: white; }
        
        .item-table { width: 100%; font-size: 0.85em; border: 1px solid #eee; margin-top: 10px; border-collapse: collapse; }
        .item-table th { background: #f8f9fa; padding: 6px; border-bottom: 2px solid #dee2e6; text-align: left; color: #495057; }
        .item-table td { padding: 6px; border-bottom: 1px solid #eee; color: #212529; }
        .text-right { text-align: right !important; }
        .text-muted { color: #6c757d; font-size: 0.9em; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <div class="container">
        <h1>📦 Pasūtījumu pārvaldība</h1>

        <?php if ($success): ?> <div class="alert alert-success">✅ <?= $success ?></div> <?php endif; ?>
        <?php if ($error): ?> <div class="alert alert-error">❌ <?= $error ?></div> <?php endif; ?>

        <div class="section">
            <h2>Jauns pasūtījums</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label>Klients</label>
                        <select name="client_id" required>
                            <option value="">-- Izvēlieties --</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= $c->id ?>"><?= htmlspecialchars($c->firstname . ' ' . $c->lastname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Piegāde</label>
                        <select name="delivery_company_id" required>
                            <option value="">-- Izvēlieties --</option>
                            <?php foreach ($deliveryCompanies as $dc): ?>
                                <option value="<?= $dc->id ?>"><?= htmlspecialchars($dc->name) ?> (<?= number_format($dc->base_cost, 2) ?> €)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="items-container" style="margin-top: 10px;">
                    <label>Preces</label>
                    <div class="item-row" style="display: flex; gap: 10px; margin-bottom: 5px;">
                        <select name="product_ids[]" style="flex: 3;">
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p->id ?>"><?= htmlspecialchars($p->name) ?> (<?= number_format($p->price, 2) ?> €)</option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="quantities[]" value="1" min="1" style="flex: 1;" placeholder="Daudzums">
                    </div>
                </div>
                
                <button type="button" onclick="addItem()" class="btn-add-item">+ Pievienot vēl vienu preci</button>
                <button type="submit" class="btn-submit">Izveidot pasūtījumu</button>
            </form>
        </div>

        <div class="section">
            <h2>Pasūtījumu vēsture</h2>
            <table>
                <thead>
                    <tr>
                        <th>Pasūtījums</th>
                        <th>Klients & Preces</th>
                        <th>Piegāde</th>
                        <th>Summa</th>
                        <th>Peļņa</th>
                        <th>Statuss</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><strong>#<?= $o->id ?></strong><br><small><?= $o->order_date ?></small></td>
                        <td>
                            <strong><?= htmlspecialchars($o->client_name) ?></strong>
                            <table class="item-table">
                                <thead>
                                    <tr>
                                        <th>Prece</th>
                                        <th class="text-right">Daudz.</th>
                                        <th class="text-right">Cena</th>
                                        <th class="text-right">Kopā</th>
                                        <th class="text-right">Svars</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($o->items as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                                            <td class="text-right"><?= $item['quantity'] ?></td>
                                            <td class="text-right"><?= number_format($item['price_at_purchase'], 2) ?> €</td>
                                            <td class="text-right"><strong><?= number_format($item['price_at_purchase'] * $item['quantity'], 2) ?> €</strong></td>
                                            <td class="text-right text-muted"><?= number_format($item['weight'] * $item['quantity'], 2) ?> kg</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                        <td><?= htmlspecialchars($o->delivery_name) ?><br><small>+<?= number_format($o->shipping_cost, 2) ?> €</small></td>
                        <td><strong><?= number_format($o->total_amount, 2) ?> €</strong></td>
                        <td style="color: <?= $o->total_profit >= 0 ? '#27ae60' : '#e74c3c' ?>; font-weight: bold;">
                            <?= number_format($o->total_profit, 2) ?> €
                        </td>
                        <td>
                            <span class="status-badge" style="background: <?= $o->status == 'completed' ? '#2ecc71' : ($o->status == 'shipped' ? '#3498db' : '#f1c40f') ?>;">
                                <?= ucfirst($o->status) ?>
                            </span>
                        </td>
                        <td style="display: flex; gap: 5px;">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="id" value="<?= $o->id ?>">
                                <?php if ($o->status == 'pending'): ?>
                                    <button name="status" value="shipped" class="btn-action" style="background: #3498db;">Sūtīt</button>
                                <?php elseif ($o->status == 'shipped'): ?>
                                    <button name="status" value="completed" class="btn-action" style="background: #2ecc71;">Pabeigt</button>
                                <?php endif; ?>
                            </form>
                            <form method="POST" onsubmit="return confirm('Dzēst?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $o->id ?>">
                                <button class="btn-action" style="background: #e74c3c;">[x]</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function addItem() {
            const container = document.getElementById('items-container');
            const row = container.querySelector('.item-row').cloneNode(true);
            row.querySelector('input').value = 1;
            container.appendChild(row);
        }
    </script>
</body>
</html>
