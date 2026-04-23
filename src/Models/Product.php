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
     */
    public static function all() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM products ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Product');
    }

    /**
     * SUB-SECTION: Find Product by ID
     */
    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchObject('Product');
    }

    /**
     * SUB-SECTION: Create New Product
     */
    public static function create($data) {
        $db = Database::getConnection();
        $sql = "INSERT INTO products (name, price, buy_price, weight, source) 
                VALUES (:name, :price, :buy, :weight, :source)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':name'   => $data['name'],
            ':price'  => $data['price'],
            ':buy'    => $data['buy_price'],
            ':weight' => $data['weight'],
            ':source' => $data['source']
        ]);
    }

    /**
     * SUB-SECTION: Update Product
     */
    public static function update($id, $data) {
        $db = Database::getConnection();
        $sql = "UPDATE products 
                SET name = :name, price = :price, buy_price = :buy, weight = :weight, source = :source 
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':id'     => $id,
            ':name'   => $data['name'],
            ':price'  => $data['price'],
            ':buy'    => $data['buy_price'],
            ':weight' => $data['weight'],
            ':source' => $data['source']
        ]);
    }

    /**
     * SUB-SECTION: Delete Product
     */
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
// =========================================================================
// END SECTION: Product Model
// =========================================================================
?>