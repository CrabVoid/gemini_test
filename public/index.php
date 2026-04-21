<?php
// =========================================================================
// SECTION: Logic & Data Initialization
// Purpose: Orchestrates the data fetching and delegates to the view.
// =========================================================================

require_once __DIR__ . '/../ClientRepository.php';

$repo = new ClientRepository();
$clients = $repo->getAllWithHierarchy();

// Default: Show orders unless explicitly set to 'none'
$showOrders = !isset($_GET['with-orders']) || $_GET['with-orders'] !== 'none';

// Delegate to the view
require_once __DIR__ . '/../src/views/customers.php';

// -------------------------------------------------------------------------
// END SECTION: Logic & Data Initialization
// -------------------------------------------------------------------------
?>
