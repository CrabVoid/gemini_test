<?php
require_once __DIR__ . '/../../db/Database.php';
require_once __DIR__ . '/../../Models.php';

// =========================================================================
// SECTION: Product Model
// Purpose: Handles database interactions for the 'products' table.
// =========================================================================
class ProductModel {

    /**
     * SUB-SECTION: Fetch All Products
     * Purpose: Retrieves a list of all products for order creation.
     */
    public static function all() {
        $db = Database::getConnection();
        
        // Atgriežam sarakstu ar produktiem, sakārtotiem pēc nosaukuma
        $stmt = $db->query("SELECT * FROM products ORDER BY name ASC");
        
        // Hydration: Katra rinda kļūst par 'Product' objektu
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Product');
    }

    /**
     * SUB-SECTION: Find Product by ID
     * Purpose: Get a single product's details (like its current price).
     */
    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        // Atgriežam vienu objektu
        return $stmt->fetchObject('Product');
    }
}
// =========================================================================
// END SECTION: Product Model
// =========================================================================
?>