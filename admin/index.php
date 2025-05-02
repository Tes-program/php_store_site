<?php
require_once '../includes/db.php';

// Get all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="admin-header">
    <h2 class="admin-title">Product Management</h2>
    <a href="/admin/add.php" class="btn btn-success">Add New Product</a>
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
                        <a href="/index.php?id=<?php echo $product['id']; ?>" class="btn">View</a>
                        <a href="/admin/edit.php?id=<?php echo $product['id']; ?>" class="btn">Edit</a>
                        <a href="/admin/delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>