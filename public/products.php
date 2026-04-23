<?php
require_once __DIR__ . '/../src/Models/Product.php';

$success = '';
$error = '';

// Handle CRUD Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    try {
        if ($action === 'create') {
            $data = [
                'name'      => $_POST['name'],
                'price'     => (float)$_POST['price'],
                'buy_price' => (float)$_POST['buy_price'],
                'weight'    => (float)$_POST['weight'],
                'source'    => $_POST['source']
            ];
            if (ProductModel::create($data)) {
                $success = "Prece veiksmīgi pievienota!";
            }
        } 
        elseif ($action === 'update') {
            $id = (int)$_POST['id'];
            $data = [
                'name'      => $_POST['name'],
                'price'     => (float)$_POST['price'],
                'buy_price' => (float)$_POST['buy_price'],
                'weight'    => (float)$_POST['weight'],
                'source'    => $_POST['source']
            ];
            if (ProductModel::update($id, $data)) {
                $success = "Prece veiksmīgi atjaunināta!";
            }
        } 
        elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            if (ProductModel::delete($id)) {
                $success = "Prece dzēsta!";
            }
        }
    } catch (Exception $e) {
        $error = "Kļūda: " . $e->getMessage();
    }
}

// Fetch all products
$products = ProductModel::all();

// Include the view
include __DIR__ . '/../src/views/products.php';
?>
