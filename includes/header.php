<?php
// Only start session if not already started and not explicitly disabled
if (!isset($disable_session_start) && session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoe Store</title>
    <!-- Use absolute path to CSS file -->
    <link rel="stylesheet" href="/shoe_store/css/style.css">
    <!-- Optional: Add Font Awesome if you want better icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="/shoe_store/index.php">Shoe Store</a></h1>
            </div>
            <div class="search-form">
                <form action="/shoe_store/index.php" method="GET">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>
            <nav>
                <ul>
                    <li><a href="/shoe_store/index.php">Home</a></li>
                    <li><a href="/shoe_store/index.php?category=Running">Running</a></li>
                    <li><a href="/shoe_store/index.php?category=Casual">Casual</a></li>
                    <li><a href="/shoe_store/index.php?category=Formal">Formal</a></li>
                    <li><a href="/shoe_store/cart.php">Cart <?php
                        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                            echo '(' . count($_SESSION['cart']) . ')';
                        }
                    ?></a></li>
                    <li><a href="/shoe_store/admin/index.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">