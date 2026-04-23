<?php
// =========================================================================
// SECTION: Data Classes (Entities)
// Purpose: Standardized objects representing rows in the database.
// =========================================================================

/**
 * SUB-SECTION: Client Class
 * Represents a single customer in the 'clients' table.
 */
class Client {
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $points;
}

/**
 * SUB-SECTION: Product Class
 * Represents a single item in the 'products' table.
 */
class Product {
    public $id;
    public $name;
    public $price;
}

/**
 * SUB-SECTION: Order Class
 * Represents a single transaction in the 'orders' table.
 */
class Order {
    public $id;
    public $client_id;
    public $status;
    public $order_date;
    
    // Papildu lauki, kas tiek pievienoti no citām tabulām (JOIN)
    public $client_name;
    public $total_amount;
}

/**
 * SUB-SECTION: OrderItem Class
 * Represents a specific product within a single order.
 */
class OrderItem {
    public $id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $price_at_purchase; // Fiksēta cena pirkuma brīdī
    
    // Papildu lauks ērtai rādīšanai
    public $product_name;
}
// =========================================================================
// END SECTION: Data Classes
// =========================================================================
?>