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
    $image = '';
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

    // If no errors, insert product
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category, size, color, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $image, $category, $size, $color, $stock]);
            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="admin-header">
    <h2 class="admin-title">Add New Product</h2>
    <a href="index.php" class="btn">Back to Products</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">
        Product added successfully! <a href="/admin/index.php">Return to product list</a>
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

<form action="admin/add.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description"></textarea>
    </div>

    <div class="form-group">
        <label for="price">Price ($)</label>
        <input type="number" id="price" name="price" step="0.01" min="0" required>
    </div>

    <div class="form-group">
        <label for="image">Product Image</label>
        <input type="file" id="image" name="image">
    </div>

    <div class="form-group">
        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">Select Category</option>
            <option value="Running">Running</option>
            <option value="Casual">Casual</option>
            <option value="Formal">Formal</option>
            <option value="Sports">Sports</option>
            <option value="Boots">Boots</option>
        </select>
    </div>

    <div class="form-group">
        <label for="size">Size</label>
        <input type="text" id="size" name="size">
    </div>

    <div class="form-group">
        <label for="color">Color</label>
        <input type="text" id="color" name="color">
    </div>

    <div class="form-group">
        <label for="stock">Stock Quantity</label>
        <input type="number" id="stock" name="stock" min="0" required>
    </div>

    <button type="submit" class="btn btn-success">Add Product</button>
</form>

<?php include '../includes/footer.php'; ?>