<?php
// Start session at the beginning
session_start();

// // Debug session variables (remove in production)
// echo "<div style='background: #f8f9fa; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd;'>";
// echo "Session ID: " . session_id() . "<br>";
// echo "Admin logged in: " . (isset($_SESSION['admin_logged_in']) ? 'Yes' : 'No') . "<br>";
// echo "SESSION contents: <pre>" . print_r($_SESSION, true) . "</pre>";
// echo "</div>";

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo "Not logged in, redirecting to login page...";
    header('Location: /shoe_store/admin/login.php');
    exit;
}

require_once '../includes/db.php';

// Get all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

// Variable to prevent duplicate session start in header.php
$disable_session_start = true;

include '../includes/header.php';
?>

<div class="admin-header">
    <h2 class="admin-title">Product Management</h2>
    <div class="admin-actions">
        <a href="/shoe_store/admin/dashboard.php" class="btn">Dashboard</a>
        <a href="/shoe_store/admin/add.php" class="btn btn-success">Add New Product</a>
        <a href="/shoe_store/admin/logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>


<?php if (empty($products)): ?>
    <p>No products found.</p>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td class="action-buttons">
                        <a href="index.php?id=<?php echo $product['id']; ?>" class="btn">View</a>
                        <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn">Edit</a>
                        <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>