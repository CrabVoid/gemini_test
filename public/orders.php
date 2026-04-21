<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =========================================================================
// SECTION: Logic & Data Initialization
// Purpose: Orchestrates data fetching via the Order Model.
// =========================================================================

require_once __DIR__ . '/../src/Models/Order.php';

// Fetch data using the static Model function
$orders = OrderModel::all();

// Delegate to the view
require_once __DIR__ . '/../src/views/orders.php';

// -------------------------------------------------------------------------
// END SECTION: Logic & Data Initialization
// -------------------------------------------------------------------------
?>
