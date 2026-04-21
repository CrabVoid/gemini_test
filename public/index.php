<?php
// =========================================================================
// SECTION: Logic & Data Initialization
// Purpose: Orchestrates the data fetching and delegates to the view.
// =========================================================================

// Paths are relative to the 'public' directory
require_once __DIR__ . '/../ClientRepository.php';

$repo = new ClientRepository();
$clients = $repo->getAllWithHierarchy();

// By default, index shows full hierarchy
$showOrders = true;

// Delegate to the view
require_once __DIR__ . '/../src/views/customers.php';

// -------------------------------------------------------------------------
// END SECTION: Logic & Data Initialization
// -------------------------------------------------------------------------
?>
