<?php
// =========================================================================
// SECTION: Logic & Data Initialization
// Purpose: Orchestrates the data fetching using the ClientRepository.
// =========================================================================

require_once 'ClientRepository.php';

$repo = new ClientRepository();
$clients = $repo->getAllWithHierarchy();

// -------------------------------------------------------------------------
// END SECTION: Logic & Data Initialization
// -------------------------------------------------------------------------
?>

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
        .card { background: #fff; padding: 15px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 3px #ccc; }
        ul { margin: 5px 0 5px 20px; padding: 0; color: #555; }
        .order-id { font-weight: bold; }
        .client-header { font-size: 1.1em; color: #333; }
    </style>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Styling
    // -------------------------------------------------------------------------
    -->
</head>
<body>

    <h2>Veikals (Store)</h2>

    <!-- 
    // =========================================================================
    // SECTION: Content Rendering
    // Purpose: Loops through data and outputs HTML.
    // =========================================================================
    -->
    <?php foreach ($clients as $c): ?>
        <div class="card">
            <div class="client-header">
                <b><?= htmlspecialchars($c->name) ?></b> (ID: <?= $c->id ?>)
            </div>
            
            <?php foreach ($c->orders as $o): ?>
                <ul>
                    <li>
                        <span class="order-id">Order #<?= $o->id ?></span> 
                        [<?= htmlspecialchars($o->status) ?>]
                        <ul>
                            <?php foreach ($o->items as $i): ?>
                                <li><?= htmlspecialchars($i->product) ?> (x<?= $i->qty ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <!-- -------------------------------------------------------------------------
    // END SECTION: Content Rendering
    // -------------------------------------------------------------------------
    -->

</body>
</html>
