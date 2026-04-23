<?php
// =========================================================================
// SECTION: Models Definitions
// Purpose: Defines Simple Objects for DB Hydration.
// =========================================================================

/**
 * SUB-SECTION: Client Model
 */
class Client {
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $points;
}

/**
 * SUB-SECTION: Delivery Company Model
 */
class DeliveryCompany {
    public $id;
    public $name;
    public $comments;
    public $base_cost;
    public $cost_per_kg;
}

/**
 * SUB-SECTION: Order Model
 */
class Order {
    public $id;
    public $client_id;
    public $delivery_company_id;
    public $order_date;
    public $status;
    public $delivery_date;
    public $tax_rate;
    public $shipping_cost;
    public $total_profit;

    // Joins
    public $client_name;
    public $total_amount;
    public $delivery_name;
}

/**
 * SUB-SECTION: Product Model
 */
class Product {
    public $id;
    public $name;
    public $price;      // Sell Price
    public $buy_price;  // Buy Price
    public $weight;     // Weight in kg
}

/**
 * SUB-SECTION: OrderItem Model
 */
class OrderItem {
    public $id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $price_at_purchase;
    public $buy_price_at_purchase;
}
?>
