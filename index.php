<?php
require_once 'db_connect.php';
$clients = [];
$query = "SELECT c.*, o.id as oid, o.status, p.name as prod, oi.quantity as qty FROM clients c 
          LEFT JOIN orders o ON c.id = o.client_id 
          LEFT JOIN order_items oi ON o.id = oi.order_id 
          LEFT JOIN products p ON oi.product_id = p.id ORDER BY c.id, o.id";

foreach ($db->query($query) as $r) {
    $c = &$clients[$r->id];
    $c ??= (object)['id' => $r->id, 'name' => "$r->firstname $r->lastname", 'orders' => []];
    if ($r->oid) {
        $o = &$c->orders[$r->oid];
        $o ??= (object)['id' => $r->oid, 'status' => $r->status, 'items' => []];
        if ($r->prod) $o->items[] = "$r->prod (x$r->qty)";
    }
}
?>
<style>body{font:14px Arial,sans-serif;background:#eee} .card{background:#fff;padding:15px;margin:10px;border-radius:4px;box-shadow:0 1px 3px #ccc} ul{margin:5px 0 5px 20px;padding:0;color:#555}</style>
<h2>Compact Hierarchical View</h2>
<?php foreach ($clients as $c): ?>
    <div class="card">
        <b><?= $c->name ?></b> (ID: <?= $c->id ?>)
        <?php foreach ($c->orders as $o): ?>
            <ul><li>Order #<?= $o->id ?> [<?= $o->status ?>]
                <ul><?php foreach ($o->items as $i): ?> <li><?= $i ?></li> <?php endforeach; ?></ul>
            </li></ul>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
