<?php
// =========================================================================
// SECTION: Customers Controller
// Purpose: Handles logic for the customers page using the Customer Model.
// =========================================================================

require_once __DIR__ . '/../src/Models/Customer.php';

// -------------------------------------------------------------------------
// SUB-SECTION: Handle POST Requests
// Purpose: Process new customer creation with validation.
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_customer') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $points = (int)($_POST['points'] ?? 0);

    $validationErrors = [];

    // 1. Validate Required Fields
    if (empty($firstname)) $validationErrors[] = "First name is required.";
    if (empty($lastname))  $validationErrors[] = "Last name is required.";
    if (empty($email))     $validationErrors[] = "Email address is required.";

    // 2. Validate Email Format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validationErrors[] = "Invalid email format.";
    }

    // 3. Process or Set Error
    if (empty($validationErrors)) {
        CustomerModel::create($firstname, $lastname, $email, $points);
        header('Location: customers.php?success=customer_created');
        exit;
    } else {
        $error = implode(" ", $validationErrors);
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
