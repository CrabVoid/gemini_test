<?php
// =========================================================================
// SECTION: Customers Controller
// Purpose: Handles logic for the customers page and processes GET parameters.
// =========================================================================

require_once __DIR__ . '/../ClientRepository.php';

$repo = new ClientRepository();

// Check if we should show the full hierarchy
$showOrders = isset($_GET['with-orders']) && $_GET['with-orders'] === 'full';

// Fetch data (we can always use hierarchy, but only display it if requested)
$clients = $repo->getAllWithHierarchy();

// Delegate to the view
require_once __DIR__ . '/../src/views/customers.php';

// -------------------------------------------------------------------------
// END SECTION: Customers Controller
// -------------------------------------------------------------------------
?>
