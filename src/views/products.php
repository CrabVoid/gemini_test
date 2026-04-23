<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Preču pārvaldība - Tasker</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 14px Arial, sans-serif; background: #ecf0f1; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }

        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-add { background: #27ae60; }
        .btn-edit { background: #3498db; font-size: 0.9em; }
        .btn-delete { background: #e74c3c; font-size: 0.9em; }
        .btn-cancel { background: #95a5a6; }

        /* Card Layout */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .product-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        .product-card h3 { margin-bottom: 15px; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.95em; }
        .info-label { color: #7f8c8d; }
        .info-value { font-weight: bold; color: #2c3e50; }
        .profit-pos { color: #27ae60; }
        .profit-neg { color: #e74c3c; }
        .source-box { background: #f9f9f9; padding: 10px; border-radius: 4px; margin-top: 10px; font-size: 0.9em; color: #34495e; font-style: italic; border-left: 3px solid #3498db; flex-grow: 1; }
        .actions { margin-top: 15px; display: flex; gap: 10px; border-top: 1px solid #eee; padding-top: 15px; }

        /* Modal / Form Styling */
        .modal { display: none; position: fixed; z-index: 100; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 30px; border-radius: 8px; width: 100%; max-width: 500px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #7f8c8d; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .form-group textarea { height: 80px; resize: vertical; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navigation.php'; ?>

    <div class="container">
        <div class="header-section">
            <h1>🏷️ Preču pārvaldība</h1>
            <button class="btn btn-add" onclick="showModal('addModal')">+ Pievienot jaunu preci</button>
        </div>

        <?php if ($success): ?> <div class="alert alert-success">✅ <?= $success ?></div> <?php endif; ?>
        <?php if ($error): ?> <div class="alert alert-error">❌ <?= $error ?></div> <?php endif; ?>

        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <?php 
                    $profit = $p->price - $p->buy_price;
                    $margin = $p->price > 0 ? ($profit / $p->price) * 100 : 0;
                ?>
                <div class="product-card">
                    <h3><?= htmlspecialchars($p->name) ?></h3>
                    
                    <div class="info-row">
                        <span class="info-label">Pārdošanas cena:</span>
                        <span class="info-value"><?= number_format($p->price, 2) ?> €</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Iepirkuma cena:</span>
                        <span class="info-value"><?= number_format($p->buy_price, 2) ?> €</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Peļņa (bruto):</span>
                        <span class="info-value <?= $profit >= 0 ? 'profit-pos' : 'profit-neg' ?>">
                            <?= number_format($profit, 2) ?> € (<?= number_format($margin, 1) ?>%)
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Svars:</span>
                        <span class="info-value"><?= number_format($p->weight, 2) ?> kg</span>
                    </div>

                    <div class="source-box">
                        <strong>Izcelsme/Piezīmes:</strong><br>
                        <?= nl2br(htmlspecialchars($p->source)) ?>
                    </div>

                    <div class="actions">
                        <button class="btn btn-edit" onclick='editProduct(<?= json_encode($p) ?>)'>Labot</button>
                        <form method="POST" onsubmit="return confirm('Dzēst šo preci?');" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $p->id ?>">
                            <button type="submit" class="btn btn-delete">Dzēst</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Pievienot jaunu preci</h2><br>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label>Nosaukums</label>
                    <input type="text" name="name" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Pārdošanas cena (€)</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Iepirkuma cena (€)</label>
                        <input type="number" step="0.01" name="buy_price" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Svars (kg)</label>
                    <input type="number" step="0.01" name="weight" required>
                </div>
                <div class="form-group">
                    <label>Izcelsme / Piezīmes</label>
                    <textarea name="source"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
                    <button type="button" class="btn btn-cancel" onclick="hideModal('addModal')">Atcelt</button>
                    <button type="submit" class="btn btn-add">Saglabāt</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Labot preci</h2><br>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-group">
                    <label>Nosaukums</label>
                    <input type="text" name="name" id="edit-name" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Pārdošanas cena (€)</label>
                        <input type="number" step="0.01" name="price" id="edit-price" required>
                    </div>
                    <div class="form-group">
                        <label>Iepirkuma cena (€)</label>
                        <input type="number" step="0.01" name="buy_price" id="edit-buy_price" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Svars (kg)</label>
                    <input type="number" step="0.01" name="weight" id="edit-weight" required>
                </div>
                <div class="form-group">
                    <label>Izcelsme / Piezīmes</label>
                    <textarea name="source" id="edit-source"></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
                    <button type="button" class="btn btn-cancel" onclick="hideModal('editModal')">Atcelt</button>
                    <button type="submit" class="btn btn-edit">Saglabāt izmaiņas</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function hideModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        function editProduct(p) {
            document.getElementById('edit-id').value = p.id;
            document.getElementById('edit-name').value = p.name;
            document.getElementById('edit-price').value = p.price;
            document.getElementById('edit-buy_price').value = p.buy_price;
            document.getElementById('edit-weight').value = p.weight;
            document.getElementById('edit-source').value = p.source || '';
            showModal('editModal');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
