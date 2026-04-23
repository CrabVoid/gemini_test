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
// Purpose: Process new order creation.
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_order') {
    $client_id = (int)($_POST['client_id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($client_id > 0 && $product_id > 0 && $quantity > 0) {
        $items = [
            ['product_id' => $product_id, 'quantity' => $quantity]
        ];
        
        $orderId = OrderModel::create($client_id, $status, $items);
        
        if ($orderId) {
            header('Location: orders.php?success=order_created');
            exit;
        } else {
            $error = "Failed to create order. Please check the logs.";
        }
    } else {
        $error = "All fields are required.";
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
