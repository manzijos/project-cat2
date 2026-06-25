<?php
require_once __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$db = get_db();
$products = $db->query('SELECT p.*, u.username AS seller_name FROM products p JOIN users u ON u.id = p.seller_id ORDER BY p.id DESC LIMIT 6')->fetchAll();
?>
<section class="hero">
    <div class="hero-card">
        <h2>Welcome to Az Mask e-Shopping</h2>
        <p>Manage sales, place orders, upload product evidence, and leave quality feedback in one secure marketplace.</p>
        <a class="btn" href="register.php">Create an account</a>
        <a class="btn secondary" href="products.php">Browse products</a>
    </div>
    <div class="panel">
        <div class="stat-row">
            <div class="stat"><strong><?php echo count($products); ?></strong><br>Featured products</div>
            <div class="stat"><strong>10+</strong><br>Webpages included</div>
        </div>
    </div>
</section>
<section class="panel">
    <h3>Featured products</h3>
    <div class="grid grid-3">
        <?php foreach ($products as $product): ?>
            <div class="card">
                <?php if (!empty($product['image_path'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/600x400?text=No+Image" alt="No image">
                <?php endif; ?>
                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                <p><?php echo htmlspecialchars(substr($product['description'], 0, 90)); ?>...</p>
                <p><strong>$<?php echo number_format($product['price'], 2); ?></strong> · Seller: <?php echo htmlspecialchars($product['seller_name']); ?></p>
                <a class="btn" href="product.php?id=<?php echo $product['id']; ?>">View details</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>