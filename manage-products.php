<?php
require_once __DIR__ . '/includes/db.php';
$user = require_login(['seller','admin']);
include __DIR__ . '/includes/header.php';

$db = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 1);
    $location = trim($_POST['location'] ?? '');
    $imagePath = upload_file('image', UPLOAD_DIR);
    $documentPath = upload_file('document', UPLOAD_DIR);

    if ($name && $description && $price >= 0) {
        $db->prepare('INSERT INTO products (seller_id, name, description, price, stock, image_path, document_path, location) VALUES (:seller_id, :name, :description, :price, :stock, :image_path, :document_path, :location)')
            ->execute([
                ':seller_id' => $user['id'],
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':stock' => $stock,
                ':image_path' => $imagePath,
                ':document_path' => $documentPath,
                ':location' => $location
            ]);
        $_SESSION['message'] = 'Product created successfully.';
    }
}

$products = $db->prepare('SELECT * FROM products WHERE seller_id = :seller_id ORDER BY id DESC');
$products->execute([':seller_id' => $user['id']]);
$productRows = $products->fetchAll();
?>
<section class="hero">
    <div class="panel">
        <h2>Add product</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" required></textarea></div>
            <div class="form-group"><label>Price</label><input type="number" step="0.01" name="price" required></div>
            <div class="form-group"><label>Stock</label><input type="number" min="1" name="stock" value="1"></div>
            <div class="form-group"><label>Location</label><input type="text" name="location"></div>
            <div class="form-group"><label>Product image</label><input type="file" name="image"></div>
            <div class="form-group"><label>Related document</label><input type="file" name="document"></div>
            <button class="btn" type="submit">Save product</button>
        </form>
    </div>
    <div class="panel">
        <h3>Your listings</h3>
        <table class="table">
            <thead><tr><th>Name</th><th>Price</th><th>Stock</th></tr></thead>
            <tbody>
                <?php foreach ($productRows as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo (int)$product['stock']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>