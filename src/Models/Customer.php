<?php
require_once __DIR__ . '/../../db/Database.php';
require_once __DIR__ . '/../../Models.php';

// =========================================================================
// SECTION: Customer Model
// Purpose: Handles all database interactions for the 'clients' table.
// =========================================================================
class CustomerModel {

    /**
     * SUB-SECTION: Fetch All Customers
     * Purpose: Get every customer from the DB and turn them into Client objects.
     */
    public static function all() {
        // 1. Iegūstam savienojumu ar datubāzi
        $db = Database::getConnection();
        
        // 2. Sagatavojam un izpildām SQL vaicājumu (kārtojam pēc ID dilstošā secībā)
        $stmt = $db->query("SELECT * FROM clients ORDER BY id DESC");
        
        // 3. Hydration: fetchAll automātiski izveido 'Client' klases objektu katrai rindai
        // Tas ļauj mums vēlāk rakstīt $customer->firstname, nevis $customer['firstname']
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Client');
    }

    /**
     * SUB-SECTION: Create New Customer
     * Purpose: Validates data and inserts a new row into the clients table.
     */
    public static function create($data) {
        $db = Database::getConnection();
        
        // Sagatavojam SQL komandu ar "placeholders" (:name), lai novērstu SQL injekcijas
        $sql = "INSERT INTO clients (firstname, lastname, email, points) 
                VALUES (:firstname, :lastname, :email, :points)";
        
        $stmt = $db->prepare($sql);
        
        // Izpildām komandu, piesaistot reālos datus no formas
        return $stmt->execute([
            ':firstname' => $data['firstname'],
            ':lastname'  => $data['lastname'],
            ':email'     => $data['email'],
            ':points'    => $data['points'] ?? 0 // Ja punkti nav norādīti, liekam 0
        ]);
    }

    /**
     * SUB-SECTION: Delete Customer
     * Purpose: Removes a customer by their unique ID.
     */
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM clients WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
// =========================================================================
// END SECTION: Customer Model
// =========================================================================
?>