<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =========================================================================
// SECTION: Home Page Logic
// Purpose: Loads dashboard statistics and delegates to home view.
// =========================================================================

require_once __DIR__ . '/../src/Models/Statistics.php';

// Fetch all dashboard statistics
$stats = Statistics::getDashboardStats();

// Delegate to the view
require_once __DIR__ . '/../src/views/home.php';

// -------------------------------------------------------------------------
// END SECTION: Home Page Logic
// -------------------------------------------------------------------------
?>
