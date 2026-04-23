<?php
// =========================================================================
// SECTION: Product Model
// Purpose: Encapsulates business logic and data access for Products.
// =========================================================================

require_once __DIR__ . '/../../db/Database.php';
require_once __DIR__ . '/../../Models.php';

class ProductModel {

    /**
     * Fetches all products from the database.
     * 
     * @return Product[] List of Product objects.
     */
    public static function all() {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM products ORDER BY name ASC");
        $rawData = $stmt->fetchAll();
        
        $products = [];
        foreach ($rawData as $row) {
            $products[] = new Product($row['id'], $row['name'], $row['price']);
        }
        return $products;
    }

    /**
     * Finds a single product by ID.
     * 
     * @param int $id The product ID.
     * @return Product|false The Product object or false if not found.
     */
    public static function find($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        
        if ($row) {
            return new Product($row['id'], $row['name'], $row['price']);
        }
        return false;
    }
}

// -------------------------------------------------------------------------
// END SECTION: Product Model
// -------------------------------------------------------------------------
?>
