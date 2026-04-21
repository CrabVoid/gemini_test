<?php
// =========================================================================
// SECTION: Customers Controller
// Purpose: Handles logic for the customers page using the Customer Model.
// =========================================================================

require_once __DIR__ . '/../src/Models/Customer.php';

// Check if we should show the full hierarchy
$showOrders = isset($_GET['with-orders']) && $_GET['with-orders'] === 'full';

// Fetch data using the static Model function
$clients = Customer::all();

// Delegate to the view
require_once __DIR__ . '/../src/views/customers.php';

// -------------------------------------------------------------------------
// END SECTION: Customers Controller
// -------------------------------------------------------------------------
?>
