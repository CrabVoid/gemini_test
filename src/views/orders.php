<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pasūtījumu pārvaldība - Tasker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

    <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <h1>📦 Pasūtījumu pārvaldība</h1>
    <p>Šajā sadaļā varat izveidot jaunus pasūtījumus, izvēloties klientu, piegādes partneri un preces.</p>

    <!-- SECTION: Paziņojumi -->
    <?php if ($success): ?> <div style="color: green; font-weight: bold; margin-bottom: 20px;">✓ <?= $success ?></div> <?php endif; ?>
    <?php if ($error): ?> <div style="color: red; font-weight: bold; margin-bottom: 20px;">✖ <?= $error ?></div> <?php endif; ?>

    <!-- SECTION: Jauna pasūtījuma izveide -->
    <div style="background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 30px; color: #333;">
        <h3>Sākt jaunu pasūtījumu</h3>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- Klients -->
                <label>Klients: 
                    <select name="client_id" required>
                        <option value="">-- Izvēlieties klientu --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c->id ?>"><?= htmlspecialchars($c->firstname . ' ' . $c->lastname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <!-- Piegāde -->
                <label>Piegādes uzņēmums: 
                    <select name="delivery_company_id" required>
                        <option value="">-- Izvēlieties piegādi --</option>
                        <?php foreach ($deliveryCompanies as $dc): ?>
                            <option value="<?= $dc->id ?>"><?= htmlspecialchars($dc->name) ?> (<?= number_format($dc->base_cost, 2) ?> €)</option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <!-- Preču saraksts (Dinamiska pievienošana ar JS) -->
            <div id="items-container" style="margin-top: 20px;">
                <h4>Izvēlētās preces:</h4>
                <div class="item-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <select name="product_ids[]" style="flex: 2;">
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p->id ?>"><?= htmlspecialchars($p->name) ?> (<?= number_format($p->price, 2) ?> €)</option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="quantities[]" value="1" min="1" style="flex: 1;" placeholder="Daudzums">
                </div>
            </div>
            
            <button type="button" onclick="addItem()" style="background: #7f8c8d; color: white;">+ Pievienot vēl vienu preci</button>
            <button type="submit" style="margin-top: 20px; width: 100%;">Apstiprināt pasūtījumu</button>
        </form>
    </div>

    <!-- SECTION: Pasūtījumu saraksts -->
    <h3>Visi pasūtījumi</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Klients</th>
                <th>Piegāde</th>
                <th>Summa</th>
                <th>Peļņa (Neto)</th>
                <th>Statuss</th>
                <th>Darbības</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?= $o->id ?></td>
                <td><?= htmlspecialchars($o->client_name) ?></td>
                <td><?= htmlspecialchars($o->delivery_name ?? 'Nav norādīts') ?> <br> <small>(Piegāde: <?= number_format($o->shipping_cost, 2) ?> €)</small></td>
                <td><strong><?= number_format($o->total_amount, 2) ?> €</strong></td>
                <td style="color: <?= $o->total_profit >= 0 ? 'green' : 'red' ?>;">
                    <strong><?= number_format($o->total_profit, 2) ?> €</strong>
                </td>
                <td>
                    <span style="padding: 3px 8px; border-radius: 4px; font-size: 0.9em; 
                        background: <?= $o->status == 'completed' ? '#2ecc71' : ($o->status == 'shipped' ? '#3498db' : '#f1c40f') ?>; color: white;">
                        <?= ucfirst($o->status) ?>
                    </span>
                </td>
                <td>
                    <!-- Statusa maiņa -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?= $o->id ?>">
                        <?php if ($o->status == 'pending'): ?>
                            <button name="status" value="shipped" style="padding: 2px 5px;">Izsūtīt</button>
                        <?php elseif ($o->status == 'shipped'): ?>
                            <button name="status" value="completed" style="padding: 2px 5px;">Pabeigt</button>
                        <?php endif; ?>
                    </form>

                    <!-- Dzēšana -->
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Dzēst pasūtījumu?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $o->id ?>">
                        <button style="padding: 2px 5px; background: none; color: #e74c3c; border: none; cursor: pointer;">[x]</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- SUB-SECTION: JavaScript preču rindu pievienošanai -->
    <script>
        function addItem() {
            const container = document.getElementById('items-container');
            const firstRow = container.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);
            newRow.querySelector('input').value = 1; // Reset quantity to 1
            container.appendChild(newRow);
        }
    </script>

</body>
</html>
