<?php
require_once __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$db = get_db();
$products = $db->query('SELECT p.*, u.username AS seller_name FROM products p JOIN users u ON u.id = p.seller_id ORDER BY p.id DESC')->fetchAll();
?>
<section class="panel">
    <h2>Products</h2>
    <div class="grid grid-3">
        <?php foreach ($products as $product): ?>
            <div class="card">
                <?php if (!empty($product['image_path'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/600x400?text=No+Image" alt="No image">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>$<?php echo number_format($product['price'], 2); ?></strong> · <?php echo htmlspecialchars($product['seller_name']); ?></p>
                <a class="btn" href="product.php?id=<?php echo $product['id']; ?>">Buy now</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>