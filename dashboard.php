<?php
require_once __DIR__ . '/includes/db.php';
$user = require_login(['admin','seller','customer']);
include __DIR__ . '/includes/header.php';

$db = get_db();
$products = $db->prepare('SELECT * FROM products WHERE seller_id = :seller_id ORDER BY id DESC');
$products->execute([':seller_id' => $user['id']]);
$productRows = $products->fetchAll();

$orders = $db->prepare('SELECT o.*, p.name AS product_name FROM orders o JOIN products p ON p.id = o.product_id WHERE o.customer_id = :customer_id ORDER BY o.id DESC');
$orders->execute([':customer_id' => $user['id']]);
$orderRows = $orders->fetchAll();
?>
<section class="hero">
    <div class="hero-card">
        <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h2>
        <p>Your role: <strong><?php echo htmlspecialchars(ucfirst($user['role'])); ?></strong></p>
        <?php if ($user['role'] === 'seller'): ?><a class="btn" href="manage-products.php">Manage products</a><?php endif; ?>
        <?php if ($user['role'] === 'admin'): ?><a class="btn secondary" href="admin.php">Admin panel</a><?php endif; ?>
    </div>
    <div class="panel">
        <h3>Dashboard summary</h3>
        <ul>
            <li><strong>Products you manage:</strong> <?php echo count($productRows); ?></li>
            <li><strong>Orders placed:</strong> <?php echo count($orderRows); ?></li>
            <li><strong>Location:</strong> <?php echo htmlspecialchars($user['location'] ?? 'Not set'); ?></li>
        </ul>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>