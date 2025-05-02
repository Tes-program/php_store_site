<?php
require_once 'includes/db.php';

// Get category filter if set
$category = isset($_GET['category']) ? $_GET['category'] : null;

// Prepare query based on category filter
if ($category) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("SELECT * FROM products");
}

$products = $stmt->fetchAll();

// Get product detail if ID is set
$product = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
}

// Get search query if set
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

// Prepare query based on category filter and search
if ($category && $search) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND (name LIKE ? OR description LIKE ?)");
    $stmt->execute([$category, "%$search%", "%$search%"]);
} elseif ($category) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->execute([$category]);
} elseif ($search) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products");
}

include 'includes/header.php';
?>

<?php if ($product): ?>
    <!-- Product Detail View -->
    <div class="product-detail">
        <div class="product-detail-image">
            <?php if ($product['image']): ?>
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php else: ?>
                <div style="width: 100%; height: 100%; background-color: #ddd; display: flex; align-items: center; justify-content: center;">
                    <span>No Image</span>
                </div>
            <?php endif; ?>
        </div>
        <div class="product-detail-info">
            <h2 class="product-detail-name"><?php echo htmlspecialchars($product['name']); ?></h2>
            <div class="product-detail-price">$<?php echo number_format($product['price'], 2); ?></div>
            <p class="product-detail-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <div class="product-meta">
                <div class="meta-item">
                    <div class="meta-label">Category</div>
                    <div class="meta-value"><?php echo htmlspecialchars($product['category']); ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Size</div>
                    <div class="meta-value"><?php echo htmlspecialchars($product['size']); ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Color</div>
                    <div class="meta-value"><?php echo htmlspecialchars($product['color']); ?></div>
                </div>
            </div>

            <div class="stock-info">
                <?php if ($product['stock'] > 0): ?>
                    <p class="in-stock">In Stock (<?php echo $product['stock']; ?> available)</p>
                    <a href="/cart.php?add&id=<?php echo $product['id']; ?>" class="btn btn-success">Add to Cart</a>
                <?php else: ?>
                    <p class="out-of-stock">Out of Stock</p>
                <?php endif; ?>
            </div>

            <a href="/index.php" class="btn">Back to Products</a>
        </div>
    </div>
<?php else: ?>
    <!-- Products Grid View -->
    <div class="page-header">
        <h2><?php echo $category ? htmlspecialchars($category) . ' Shoes' : 'All Shoes'; ?></h2>
    </div>

    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background-color: #ddd; display: flex; align-items: center; justify-content: center;">
                                <span>No Image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                        <a href="/index.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>