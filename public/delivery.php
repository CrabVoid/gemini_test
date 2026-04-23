<?php
require_once __DIR__ . '/../src/Models/DeliveryCompany.php';

// =========================================================================
// SECTION: Delivery Controller
// Purpose: Handles logic for managing delivery companies.
// =========================================================================

// Handle Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    DeliveryCompanyModel::create([
        'name'        => $_POST['name'],
        'comments'    => $_POST['comments'],
        'base_cost'   => $_POST['base_cost'],
        'cost_per_kg' => $_POST['cost_per_kg']
    ]);
    header('Location: delivery.php');
    exit;
}

// Handle Deletion
if (isset($_GET['delete'])) {
    DeliveryCompanyModel::delete($_GET['delete']);
    header('Location: delivery.php');
    exit;
}

// Fetch Data for View
$companies = DeliveryCompanyModel::all();

// Load View
include __DIR__ . '/../src/views/delivery.php';
?>
