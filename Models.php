<?php
// Models.php

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

class Order {
    public $id;
    public $status;
    public $date;
    public $items = [];

    public function __construct($id, $status, $date) {
        $this->id = $id;
        $this->status = $status;
        $this->date = $date;
    }

    public function addItem(OrderItem $item) {
        $this->items[] = $item;
    }
}

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
?>
