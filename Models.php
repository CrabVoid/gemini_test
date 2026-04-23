<?php
// =========================================================================
// SECTION: Data Models
// Purpose: Simple classes to represent the core entities of the application.
// =========================================================================

// -------------------------------------------------------------------------
// SUB-SECTION: OrderItem
// -------------------------------------------------------------------------
class OrderItem {
    public $id;
    public $product;
    public $qty;
    public $price;

    public function __construct($id, $product, $qty, $price) {
        $this->id = $id;
        $this->product = $product;
        $this->qty = $qty;
        $this->price = $price;
    }

    public function getTotal() {
        return $this->qty * $this->price;
    }
}

// -------------------------------------------------------------------------
// SUB-SECTION: Order
// -------------------------------------------------------------------------
class Order {
    public $id;
    public $status;
    public $date;
    public $items = [];
    public $client_id;
    public $client_name;
    public $client_email;

    public function __construct($id, $status, $date) {
        $this->id = $id;
        $this->status = $status;
        $this->date = $date;
    }

    public function addItem(OrderItem $item) {
        $this->items[] = $item;
    }
}

// -------------------------------------------------------------------------
// SUB-SECTION: Client
// -------------------------------------------------------------------------
class Client {
    public $id;
    public $name;
    public $email;
    public $points;
    public $orders = [];

    public function __construct($id, $name, $email, $points) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->points = $points;
    }

    public function addOrder(Order $order) {
        $this->orders[$order->id] = $order;
    }
}

// -------------------------------------------------------------------------
// SUB-SECTION: Product
// -------------------------------------------------------------------------
class Product {
    public $id;
    public $name;
    public $price;

    public function __construct($id, $name, $price) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }
}

// -------------------------------------------------------------------------
// END SECTION: Data Models
// -------------------------------------------------------------------------
?>
