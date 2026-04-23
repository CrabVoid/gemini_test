<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Piegādes uzņēmumi - Tasker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

    <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <!-- SECTION: Piegādes uzņēmumu pārvaldība -->
    <h1>🚚 Piegādes uzņēmumi</h1>
    <p>Šeit varat pievienot piegādes partnerus un definēt to izmaksas atkarībā no pakas svara.</p>

    <!-- SUB-SECTION: Pievienošanas forma -->
    <div style="background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 30px; color: #333;">
        <h3>Pievienot jaunu uzņēmumu</h3>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <label>Nosaukums: <input type="text" name="name" required placeholder="Piem. Omniva"></label>
                <label>Bāzes cena (€): <input type="number" step="0.01" name="base_cost" value="2.50"></label>
                <label>Cena par kg (€): <input type="number" step="0.01" name="cost_per_kg" value="0.50"></label>
                <label>Piezīmes (pašam): <input type="text" name="comments" placeholder="Piem. Ātra piegāde, labs serviss"></label>
            </div>
            <button type="submit" style="margin-top: 15px; width: 100%;">Saglabāt uzņēmumu</button>
        </form>
    </div>

    <!-- SUB-SECTION: Uzņēmumu saraksts -->
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
                    <a href="?delete=<?= $c->id ?>" style="color: #e74c3c;" onclick="return confirm('Tiešām dzēst?')">Dzēst</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($companies)): ?>
            <tr><td colspan="5" style="text-align: center;">Nav pievienotu uzņēmumu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
