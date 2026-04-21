<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =========================================================================
// SECTION: Logic & Data Initialization
// Purpose: Orchestrates data fetching via the Customer Model.
// =========================================================================

require_once __DIR__ . '/../src/Models/Customer.php';

// Fetch data using the static Model function
$clients = Customer::all();

// Default: Show orders unless explicitly set to 'none'
$showOrders = !isset($_GET['with-orders']) || $_GET['with-orders'] !== 'none';

// Delegate to the view
require_once __DIR__ . '/../src/views/customers.php';

// -------------------------------------------------------------------------
// END SECTION: Logic & Data Initialization
// -------------------------------------------------------------------------
?>
