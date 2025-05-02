<?php
session_start();
// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /admin/login.php');
    exit;
}

require_once '../includes/db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header('Location: /admin/index.php');
    exit;
}

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($id === false) {
    header('Location: /admin/index.php');
    exit;
}

// Get product data for confirmation
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: /admin/index.php');
    exit;
}

$error = null;
$deleted = false;

// Process deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        // Delete the product
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $deleted = true;
        
        // Redirect after short delay
        header("Refresh: 2; URL=/admin/index.php");
    } catch (PDOException $e) {
        $error = "Error deleting product: " . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="admin-header">
    <h2 class="admin-title">Delete Product</h2>
    <a href="/admin/index.php" class="btn">Back to Products</a>
</div>

<?php if ($deleted): ?>
    <div class="alert alert-success">
        Product deleted successfully! Redirecting to product list...
    </div>
<?php elseif ($error): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php else: ?>
    <div class="delete-confirmation">
        <h3>Are you sure you want to delete this product?</h3>
        <div class="product-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($product['name']); ?></p>
            <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
        </div>
        
        <form action="/admin/delete.php?id=<?php echo $id; ?>" method="POST">
            <input type="hidden" name="confirm_delete" value="1">
            <button type="submit" class="btn btn-danger">Yes, Delete Product</button>
            <a href="/admin/index.php" class="btn">Cancel</a>
        </form>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>