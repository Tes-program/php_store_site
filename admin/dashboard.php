<?php
session_start();
// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /admin/login.php');
    exit;
}
require_once '../includes/db.php';

// Get total products count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $stmt->fetch()['total'];

// Get products by category
$stmt = $pdo->query("SELECT category, COUNT(*) as count FROM products GROUP BY category");
$categoryCounts = $stmt->fetchAll();

// Get low stock products
$stmt = $pdo->query("SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC");
$lowStockProducts = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="admin-header">
    <h2 class="admin-title">Admin Dashboard</h2>
    <a href="/admin/index.php" class="btn">Manage Products</a>
</div>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Total Products</h3>
        <div class="stat-value"><?php echo $totalProducts; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Products by Category</h3>
        <ul class="stat-list">
            <?php foreach ($categoryCounts as $category): ?>
                <li>
                    <span><?php echo htmlspecialchars($category['category']); ?></span>
                    <span class="stat-count"><?php echo $category['count']; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="dashboard-section">
    <h3>Low Stock Products</h3>
    <?php if (empty($lowStockProducts)): ?>
        <p>No products with low stock.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lowStockProducts as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td class="<?php echo $product['stock'] <= 3 ? 'low-stock' : ''; ?>">
                            <?php echo $product['stock']; ?>
                        </td>
                        <td>
                            <a href="/admin/edit.php?id=<?php echo $product['id']; ?>" class="btn">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>