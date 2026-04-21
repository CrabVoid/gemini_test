<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =========================================================================
// SECTION: Logic & Data Initialization
// Purpose: Orchestrates data fetching via the Order Model.
// Supports filtering by status via GET parameter.
// =========================================================================

require_once __DIR__ . '/../src/Models/Order.php';

// Get status filter from GET parameter (if provided)
$statusFilter = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : null;

// Fetch data using the static Model function with optional status filter
$orders = OrderModel::all($statusFilter);

// Delegate to the view
require_once __DIR__ . '/../src/views/orders.php';

// -------------------------------------------------------------------------
// END SECTION: Logic & Data Initialization
// -------------------------------------------------------------------------
?>
