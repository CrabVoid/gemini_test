<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =========================================================================
// SECTION: Logic & Data Initialization
// Purpose: Orchestrates data fetching via the Order Model.
// Supports filtering by status via GET parameter.
// =========================================================================

require_once __DIR__ . '/../src/Models/Order.php';
require_once __DIR__ . '/../src/Models/Customer.php';
require_once __DIR__ . '/../src/Models/Product.php';

// -------------------------------------------------------------------------
// SUB-SECTION: Handle POST Requests
// Purpose: Process new order creation with multi-item validation.
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_order') {
    $client_id = (int)($_POST['client_id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $product_ids = $_POST['product_ids'] ?? [];
    $quantities = $_POST['quantities'] ?? [];

    $errors = [];

    // 1. Basic Customer Check
    if ($client_id <= 0) {
        $errors[] = "Please select a valid customer.";
    }

    // 2. Multi-Item Validation
    if (empty($product_ids)) {
        $errors[] = "Order must contain at least one product.";
    } else {
        $validItems = [];
        foreach ($product_ids as $idx => $pid) {
            $qty = (int)($quantities[$idx] ?? 0);
            if ($pid > 0 && $qty > 0) {
                $validItems[] = ['product_id' => (int)$pid, 'quantity' => $qty];
            }
        }

        if (empty($validItems)) {
            $errors[] = "Each item must have a valid product and quantity > 0.";
        }
    }

    // 3. Process or Set Errors
    if (empty($errors)) {
        $orderId = OrderModel::create($client_id, $status, $validItems);
        if ($orderId) {
            header('Location: orders.php?success=order_created');
            exit;
        } else {
            $error = "Critical: Order creation failed in database transaction.";
        }
    } else {
        $error = implode(" ", $errors);
    }
}
// -------------------------------------------------------------------------
// SUB-SECTION: Handle Update Request
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $client_id = (int)($_POST['client_id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    
    if ($order_id > 0 && $client_id > 0) {
        if (OrderModel::update($order_id, $client_id, $status)) {
            header('Location: orders.php?success=order_updated');
            exit;
        } else {
            $error = "Failed to update order.";
        }
    }
}

// -------------------------------------------------------------------------
// SUB-SECTION: Handle Delete Request
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_order') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    
    if ($order_id > 0) {
        if (OrderModel::delete($order_id)) {
            header('Location: orders.php?success=order_deleted');
            exit;
        } else {
            $error = "Failed to delete order.";
        }
    }
}
// -------------------------------------------------------------------------

// Get status filter from GET parameter (if provided)
$statusFilter = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : null;

// Fetch orders data
$orders = OrderModel::all($statusFilter);

// Fetch additional data for the form
$clients = CustomerModel::all();
$products = ProductModel::all();

// Delegate to the view
require_once __DIR__ . '/../src/views/orders.php';

// -------------------------------------------------------------------------
// END SECTION: Logic & Data Initialization
// -------------------------------------------------------------------------
?>
