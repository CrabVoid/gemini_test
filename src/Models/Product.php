<?php
// =========================================================================
// SECTION: Product Model
// Purpose: Encapsulates business logic and data access for Products.
// =========================================================================

require_once __DIR__ . '/../../db/Database.php';

class Product {

    /**
     * Fetches all products from the database.
     * 
     * @return array List of products.
     */
    public static function all() {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM products ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Finds a single product by ID.
     * 
     * @param int $id The product ID.
     * @return array|false The product data or false if not found.
     */
    public static function find($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}

// -------------------------------------------------------------------------
// END SECTION: Product Model
// -------------------------------------------------------------------------
?>
