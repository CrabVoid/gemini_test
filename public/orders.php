<?php
require_once __DIR__ . '/../src/Models/Order.php';
require_once __DIR__ . '/../src/Models/Customer.php';
require_once __DIR__ . '/../src/Models/Product.php';
require_once __DIR__ . '/../src/Models/DeliveryCompany.php';

// =========================================================================
// SECTION: Order Action Dispatcher
// Purpose: Handles creating, updating, and deleting orders.
// =========================================================================

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        OrderModel::delete((int)$_POST['id']);
        header("Location: orders.php?success=Dzēsts");
        exit;
    }

    elseif ($action === 'update_status') {
        OrderModel::updateStatus((int)$_POST['id'], $_POST['status']);
        header("Location: orders.php?success=Statuss+mainīts");
        exit;
    }

    elseif ($action === 'create') {
        $clientId = (int)$_POST['client_id'];
        $dcId     = (int)$_POST['delivery_company_id'];
        $productIds = $_POST['product_ids'] ?? [];
        $quantities = $_POST['quantities'] ?? [];

        if (empty($clientId) || empty($dcId) || empty($productIds)) {
            $error = "Izvēlieties klientu, piegādi un vismaz vienu preci.";
        } else {
            $items = [];
            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 1);
                if ($qty > 0) $items[] = ['product_id' => (int)$pid, 'quantity' => $qty];
            }

            if (!empty($items) && OrderModel::create(['client_id' => $clientId, 'delivery_company_id' => $dcId, 'items' => $items])) {
                header("Location: orders.php?success=Pasūtījums+veikts");
                exit;
            } else {
                $error = "Neizdevās izveidot pasūtījumu.";
            }
        }
    }
}

$orders    = OrderModel::all();
$customers = CustomerModel::all();
$products  = ProductModel::all();
$deliveryCompanies = DeliveryCompanyModel::all();

if (isset($_GET['success'])) $success = htmlspecialchars($_GET['success']);

require_once __DIR__ . '/../src/views/orders.php';
?>
