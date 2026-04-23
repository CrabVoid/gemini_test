<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Piegādes uzņēmumi - Tasker</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .section { background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 20px; }
        h2 { border-bottom: 2px solid #e67e22; padding-bottom: 10px; margin-bottom: 20px; }
        
        form label { display: block; margin-bottom: 5px; font-weight: bold; color: #7f8c8d; }
        form input { padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }
        button { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; }
        .btn-add { background: #e67e22; }
        .btn-del { background: #e74c3c; font-size: 0.8em; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; color: #2c3e50; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <div class="container">
        <h1>🚚 Piegādes uzņēmumi</h1>

        <div class="section">
            <h2>Pievienot jaunu uzņēmumu</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <label>Nosaukums</label>
                        <input type="text" name="name" required placeholder="Piem. Omniva">
                    </div>
                    <div>
                        <label>Bāzes cena (€)</label>
                        <input type="number" step="0.01" name="base_cost" value="2.50">
                    </div>
                    <div>
                        <label>Cena par kg (€)</label>
                        <input type="number" step="0.01" name="cost_per_kg" value="0.50">
                    </div>
                    <div>
                        <label>Piezīmes</label>
                        <input type="text" name="comments" placeholder="Piem. Ātra piegāde">
                    </div>
                </div>
                <button type="submit" class="btn-add" style="margin-top: 10px;">Saglabāt uzņēmumu</button>
            </form>
        </div>

        <div class="section">
            <h2>Aktīvie partneri</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nosaukums</th>
                        <th>Bāzes cena</th>
                        <th>Cena/kg</th>
                        <th>Piezīmes</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($companies as $c): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($c->name) ?></strong></td>
                        <td><?= number_format($c->base_cost, 2) ?> €</td>
                        <td><?= number_format($c->cost_per_kg, 2) ?> €</td>
                        <td><small><?= htmlspecialchars($c->comments) ?></small></td>
                        <td>
                            <a href="?delete=<?= $c->id ?>" style="color: #e74c3c; text-decoration: none; font-weight: bold;" onclick="return confirm('Tiešām dzēst?')">Dzēst</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
