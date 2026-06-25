<?php
require_once __DIR__ . '/includes/db.php';

$id = (int)($_GET['id'] ?? 0);
$db = get_db();
$product = $db->prepare('SELECT p.*, u.username AS seller_name FROM products p JOIN users u ON u.id = p.seller_id WHERE p.id = :id');
$product->execute([':id' => $id]);
$product = $product->fetch();

if (!$product) {
    redirect('products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    $total = $quantity * (float)$product['price'];
    $stmt = $db->prepare('INSERT INTO orders (customer_id, product_id, quantity, total, status) VALUES (:customer_id, :product_id, :quantity, :total, :status)');
    $stmt->execute([
        ':customer_id' => current_user()['id'],
        ':product_id' => $product['id'],
        ':quantity' => $quantity,
        ':total' => $total,
        ':status' => 'Placed'
    ]);
    $_SESSION['message'] = 'Order placed successfully.';
    redirect('dashboard.php');
}

include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="card">
        <?php if (!empty($product['image_path'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php endif; ?>
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <p><?php echo htmlspecialchars($product['description']); ?></p>
        <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
        <p><strong>Seller:</strong> <?php echo htmlspecialchars($product['seller_name']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($product['location'] ?? 'Not set'); ?></p>
        <?php if (!empty($product['document_path'])): ?><a class="btn" href="uploads/<?php echo htmlspecialchars($product['document_path']); ?>" target="_blank">Download document</a><?php endif; ?>
    </div>
    <div class="panel">
        <h3>Place order</h3>
        <?php if (!is_logged_in()): ?><p>Please log in to place an order.</p><?php else: ?>
        <form method="post">
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" min="1" name="quantity" value="1">
            </div>
            <button class="btn" type="submit">Buy now</button>
        </form>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>