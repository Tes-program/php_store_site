<?php
session_start();
// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /admin/login.php');
    exit;
}
require_once '../includes/db.php';

$errors = [];
$success = false;
$product = null;

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

// Get product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: /admin/index.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
    $category = trim($_POST['category'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $stock = filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT);

    // Basic validation
    if (empty($name)) {
        $errors[] = "Product name is required";
    }

    if ($price === false || $price <= 0) {
        $errors[] = "Valid price is required";
    }

    if (empty($category)) {
        $errors[] = "Category is required";
    }

    if ($stock === false || $stock < 0) {
        $errors[] = "Valid stock quantity is required";
    }

    // More comprehensive validation
    if (empty($name) || strlen($name) > 255) {
        $errors[] = "Product name is required and must be less than 255 characters";
    }

    if ($price === false || $price <= 0) {
        $errors[] = "Valid price is required (must be greater than 0)";
    }

    if (empty($category)) {
        $errors[] = "Category is required";
    }

    if ($stock === false || $stock < 0) {
        $errors[] = "Valid stock quantity is required (must be 0 or greater)";
    }

    // Additional validation for image upload size
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Check file size (5MB max)
        if ($_FILES['image']['size'] > 5242880) {
            $errors[] = "Image file is too large. Maximum size is 5MB.";
        }
    }

    // Handle image upload
    $image = $product['image']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../images/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileType), $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $image = $fileName;
            } else {
                $errors[] = "There was an error uploading your file.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, PNG, & GIF files are allowed.";
        }
    }

    // If no errors, update product
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, category = ?, size = ?, color = ?, stock = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $image, $category, $size, $color, $stock, $id]);
            $success = true;

            // Refresh product data
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="admin-header">
    <h2 class="admin-title">Edit Product</h2>
    <a href="index.php" class="btn">Back to Products</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">
        Product updated successfully!
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
    </div>

    <div class="form-group">
        <label for="price">Price ($)</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
    </div>

    <div class="form-group">
        <label for="image">Product Image</label>
        <?php if ($product['image']): ?>
            <div style="margin-bottom: 10px;">
                <img src="/images/<?php echo htmlspecialchars($product['image']); ?>" alt="Current image" style="max-width: 200px; max-height: 200px;">
                <p>Current image: <?php echo htmlspecialchars($product['image']); ?></p>
            </div>
        <?php endif; ?>
        <input type="file" id="image" name="image">
        <small>Leave empty to keep current image</small>
    </div>
    <div class="form-group">
        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">Select Category</option>
            <option value="Running" <?php echo $product['category'] === 'Running' ? 'selected' : ''; ?>>Running</option>
            <option value="Casual" <?php echo $product['category'] === 'Casual' ? 'selected' : ''; ?>>Casual</option>
            <option value="Formal" <?php echo $product['category'] === 'Formal' ? 'selected' : ''; ?>>Formal</option>
            <option value="Sports" <?php echo $product['category'] === 'Sports' ? 'selected' : ''; ?>>Sports</option>
            <option value="Boots" <?php echo $product['category'] === 'Boots' ? 'selected' : ''; ?>>Boots</option>
        </select>
    </div>

    <div class="form-group">
        <label for="size">Size</label>
        <input type="text" id="size" name="size" value="<?php echo htmlspecialchars($product['size']); ?>">
    </div>

    <div class="form-group">
        <label for="color">Color</label>
        <input type="text" id="color" name="color" value="<?php echo htmlspecialchars($product['color']); ?>">
    </div>

    <div class="form-group">
        <label for="stock">Stock Quantity</label>
        <input type="number" id="stock" name="stock" min="0" value="<?php echo $product['stock']; ?>" required>
    </div>

    <button type="submit" class="btn btn-success">Update Product</button>
</form>

<?php include '../includes/footer.php'; ?>