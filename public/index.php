<?php
require_once __DIR__ . '/../src/Models/Statistics.php';

// =========================================================================
// SECTION: Home Controller
// Purpose: Prepares data for the dashboard landing page.
// =========================================================================

// 1. Iegūstam apkopotos statistikas datus no modeļa
$stats = StatisticsModel::getDashboardStats();

// 2. Ielādējam vizuālo skatu
require_once __DIR__ . '/../src/views/home.php';
// =========================================================================
// END SECTION: Home Controller
// =========================================================================
?>