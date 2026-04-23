<?php
require_once __DIR__ . '/../../db/Database.php';
require_once __DIR__ . '/../../Models.php';

// =========================================================================
// SECTION: Delivery Company Model
// Purpose: Handles database interactions for 'delivery_companies' table.
// =========================================================================
class DeliveryCompanyModel {

    /**
     * SUB-SECTION: Fetch All Delivery Companies
     */
    public static function all() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM delivery_companies ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'DeliveryCompany');
    }

    /**
     * SUB-SECTION: Find Delivery Company by ID
     */
    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM delivery_companies WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchObject('DeliveryCompany');
    }

    /**
     * SUB-SECTION: Create New Delivery Company
     */
    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO delivery_companies (name, comments, base_cost, cost_per_kg) 
                             VALUES (:name, :comments, :base_cost, :cost_per_kg)");
        return $stmt->execute([
            ':name'        => $data['name'],
            ':comments'    => $data['comments'],
            ':base_cost'   => (float)$data['base_cost'],
            ':cost_per_kg' => (float)$data['cost_per_kg']
        ]);
    }

    /**
     * SUB-SECTION: Delete Delivery Company
     */
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM delivery_companies WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
// =========================================================================
// END SECTION: Delivery Company Model
// =========================================================================
?>
