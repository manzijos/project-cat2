<?php
require_once __DIR__ . '/includes/db.php';
$user = require_login(['admin']);
include __DIR__ . '/includes/header.php';

$db = get_db();
$users = $db->query('SELECT id, username, role, full_name, location FROM users ORDER BY id DESC')->fetchAll();
$products = $db->query('SELECT p.id, p.name, p.price, u.username AS seller_name FROM products p JOIN users u ON u.id = p.seller_id ORDER BY p.id DESC')->fetchAll();
$orders = $db->query('SELECT o.id, o.status, o.total, u.username AS customer_name, p.name AS product_name FROM orders o JOIN users u ON u.id = o.customer_id JOIN products p ON p.id = o.product_id ORDER BY o.id DESC')->fetchAll();
?>
<section class="panel">
    <h2>Admin panel</h2>
    <p>System management view for users, products, and orders.</p>
    <h3>Users</h3>
    <table class="table">
        <thead><tr><th>Username</th><th>Role</th><th>Full name</th><th>Location</th></tr></thead>
        <tbody>
            <?php foreach ($users as $entry): ?>
                <tr><td><?php echo htmlspecialchars($entry['username']); ?></td><td><?php echo htmlspecialchars($entry['role']); ?></td><td><?php echo htmlspecialchars($entry['full_name']); ?></td><td><?php echo htmlspecialchars($entry['location'] ?? ''); ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Products</h3>
    <table class="table">
        <thead><tr><th>Name</th><th>Seller</th><th>Price</th></tr></thead>
        <tbody>
            <?php foreach ($products as $entry): ?>
                <tr><td><?php echo htmlspecialchars($entry['name']); ?></td><td><?php echo htmlspecialchars($entry['seller_name']); ?></td><td>$<?php echo number_format($entry['price'], 2); ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Orders</h3>
    <table class="table">
        <thead><tr><th>Order</th><th>Customer</th><th>Product</th><th>Status</th><th>Total</th></tr></thead>
        <tbody>
            <?php foreach ($orders as $entry): ?>
                <tr><td>#<?php echo $entry['id']; ?></td><td><?php echo htmlspecialchars($entry['customer_name']); ?></td><td><?php echo htmlspecialchars($entry['product_name']); ?></td><td><?php echo htmlspecialchars($entry['status']); ?></td><td>$<?php echo number_format($entry['total'], 2); ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>