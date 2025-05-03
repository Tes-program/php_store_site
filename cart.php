<?php
// cart.php
require_once 'includes/db.php';

session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if (isset($_GET['add']) && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id) {
        // Get product info
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if ($product && $product['stock'] > 0) {
            // Add to cart or increment quantity
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity']++;
            } else {
                $_SESSION['cart'][$id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => 1
                ];
            }
            header('Location: /cart.php');
            exit;
        }
    }
}

// Remove from cart
if (isset($_GET['remove']) && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header('Location: /cart.php');
    exit;
}

// Update quantity
if (isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $id => $quantity) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
        
        if ($id && $quantity > 0) {
            $_SESSION['cart'][$id]['quantity'] = $quantity;
        }
    }
    header('Location: /cart.php');
    exit;
}

// Clear cart
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header('Location: /cart.php');
    exit;
}

include 'includes/header.php';
?>

<div class="page-header">
    <h2>Shopping Cart</h2>
</div>

<?php if (empty($_SESSION['cart'])): ?>
    <div class="cart-empty">
        <p>Your cart is empty.</p>
        <a href="/index.php" class="btn">Continue Shopping</a>
    </div>
<?php else: ?>
    <form action="cart.php" method="POST">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($_SESSION['cart'] as $item): 
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                ?>
                    <tr>
                        <td>
                            <div class="cart-product">
                                <?php if ($item['image']): ?>
                                    <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                        </td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="99">
                        </td>
                        <td>$<?php echo number_format($itemTotal, 2); ?></td>
                        <td>
                            <a href="cart.php?remove&id=<?php echo $item['id']; ?>" class="btn btn-danger">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td>$<?php echo number_format($total, 2); ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="cart-actions">
            <button type="submit" name="update" class="btn">Update Cart</button>
            <a href="cart.php?clear" class="btn btn-danger">Clear Cart</a>
            <a href="checkout.php" class="btn btn-success">Checkout</a>
        </div>
    </form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>