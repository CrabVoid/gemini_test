<?php
// =========================================================================
// SECTION: Customers Controller
// Purpose: Handles logic for the customers page using the Customer Model.
// =========================================================================

require_once __DIR__ . '/../src/Models/Customer.php';

// -------------------------------------------------------------------------
// SUB-SECTION: Handle POST Requests
// Purpose: Process new customer creation.
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_customer') {
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email = $_POST['email'] ?? '';
    $points = (int)($_POST['points'] ?? 0);

    if (!empty($firstname) && !empty($lastname) && !empty($email)) {
        CustomerModel::create($firstname, $lastname, $email, $points);
        // Redirect to avoid form resubmission
        header('Location: customers.php?success=customer_created');
        exit;
    }
}
// -------------------------------------------------------------------------

// Check if we should show the full hierarchy
$showOrders = isset($_GET['with-orders']) && $_GET['with-orders'] === 'full';

// Fetch customers data
$clients = CustomerModel::all();

// Delegate to the view
require_once __DIR__ . '/../src/views/customers.php';

// -------------------------------------------------------------------------
// END SECTION: Customers Controller
// -------------------------------------------------------------------------
?>
