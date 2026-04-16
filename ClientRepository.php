<?php
// ClientRepository.php
require_once 'Database.php';
require_once 'Models.php';

class ClientRepository {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAllWithHierarchy() {
        $query = "
            SELECT 
                c.id as client_id, c.firstname, c.lastname, c.email, c.points,
                o.id as order_id, o.status as order_status, o.order_date,
                oi.id as item_id, oi.quantity, oi.price_at_purchase,
                p.name as product_name
            FROM clients c
            LEFT JOIN orders o ON c.id = o.client_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            ORDER BY c.id, o.id, oi.id
        ";
        
        $stmt = $this->pdo->query($query);
        $rawData = $stmt->fetchAll();

        $clients = [];
        foreach ($rawData as $row) {
            $cid = $row['client_id'];
            $oid = $row['order_id'];
            $iid = $row['item_id'];

            // Map Client
            if (!isset($clients[$cid])) {
                $clients[$cid] = new Client(
                    $cid,
                    $row['firstname'] . ' ' . $row['lastname'],
                    $row['email'],
                    $row['points']
                );
            }

            // Map Order
            if ($oid) {
                if (!isset($clients[$cid]->orders[$oid])) {
                    $clients[$cid]->addOrder(new Order(
                        $oid,
                        $row['order_status'],
                        $row['order_date']
                    ));
                }

                // Map Item
                if ($iid) {
                    $clients[$cid]->orders[$oid]->addItem(new OrderItem(
                        $iid,
                        $row['product_name'],
                        $row['quantity'],
                        $row['price_at_purchase']
                    ));
                }
            }
        }
        return $clients;
    }
}
?>
