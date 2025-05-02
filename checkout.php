<?php
// checkout.php
require_once 'includes/db.php';
session_start();

$errors = [];
$success = false;

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: /cart.php');
    exit;
}

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    // Basic validation
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    // If no errors, process order
    if (empty($errors)) {
        // In a real application, you would:
        // 1. Create an order in the database
        // 2. Add order items
        // 3. Update product stock
        // 4. Send confirmation email
        // Here we'll just simulate success
        $success = true;
        
        // Clear cart after successful order
        $_SESSION['cart'] = [];
    }
}

include 'includes/header.php';
?>

<div class="page-header">
    <h2>Checkout</h2>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">
        <p>Your order has been placed successfully! Thank you for shopping with us.</p>
        <a href="/index.php" class="btn">Continue Shopping</a>
    </div>
<?php else: ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="checkout-container">
        <div class="order-summary">
            <h3>Order Summary</h3>
            <ul class="order-items">
                <?php 
                $total = 0;
                foreach ($_SESSION['cart'] as $item): 
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                ?>
                    <li>
                        <span class="item-name"><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                        <span class="item-price">$<?php echo number_format($itemTotal, 2); ?></span>
                    </li>
                <?php endforeach; ?>
                <li class="total">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </li>
            </ul>
        </div>
        
        <div class="checkout-form">
            <h3>Shipping Information</h3>
            <form action="/checkout.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Shipping Address</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Place Order</button>
                <a href="/cart.php" class="btn">Back to Cart</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>