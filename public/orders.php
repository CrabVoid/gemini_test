<?php
require_once __DIR__ . '/../src/Models/Order.php';
require_once __DIR__ . '/../src/Models/Customer.php';
require_once __DIR__ . '/../src/Models/Product.php';

// =========================================================================
// SECTION: Order Action Dispatcher
// Purpose: Handles creating, updating, and deleting orders.
// =========================================================================

$error = '';
$success = '';

/**
 * SUB-SECTION: POST Handler
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Darbība: DELETE (Dzēst pasūtījumu)
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        if (OrderModel::delete($id)) {
            header("Location: /orders.php?success=Order+deleted");
            exit;
        }
    }

    // 2. Darbība: UPDATE_STATUS (Mainīt statusu uz 'shipped' vai 'completed')
    elseif ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $status = $_POST['status'];
        if (OrderModel::updateStatus($id, $status)) {
            header("Location: /orders.php?success=Status+updated+to+" . $status);
            exit;
        }
    }

    // 3. Darbība: CREATE (Jauns pasūtījums ar vairākām precēm)
    elseif ($action === 'create') {
        $clientId = (int)$_POST['client_id'];
        $productIds = $_POST['product_ids'] ?? [];
        $quantities = $_POST['quantities'] ?? [];

        // a) Validācija: Pārbaudām, vai ir izvēlēts klients un vismaz viena prece
        if (empty($clientId) || empty($productIds)) {
            $error = "Izvēlieties klientu un vismaz vienu preci.";
        } else {
            // b) Sagatavojam preču masīvu priekš modeļa
            $items = [];
            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 1);
                if ($qty > 0) {
                    $items[] = [
                        'product_id' => (int)$pid,
                        'quantity'   => $qty
                    ];
                }
            }

            // c) Saglabājam datubāzē
            if (!empty($items) && OrderModel::create(['client_id' => $clientId, 'items' => $items])) {
                header("Location: /orders.php?success=Order+placed+successfully");
                exit;
            } else {
                $error = "Neizdevās izveidot pasūtījumu.";
            }
        }
    }
}

/**
 * SUB-SECTION: Load View Data
 */
$orders    = OrderModel::all();      // Visi pasūtījumi sarakstam
$customers = CustomerModel::all();   // Klienti priekš "Select" izvēlnes
$products  = ProductModel::all();    // Produkti priekš preču pievienošanas

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

// Ielādējam vizuālo skatu
require_once __DIR__ . '/../src/views/orders.php';
?>