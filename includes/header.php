<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoe Store</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="/index.php">Shoe Store</a></h1>
            </div>
            <div class="search-form">
                <form action="/index.php" method="GET">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>
            <nav>
                <ul>
                    <li><a href="/index.php">Home</a></li>
                    <li><a href="/index.php?category=Running">Running</a></li>
                    <li><a href="/index.php?category=Casual">Casual</a></li>
                    <li><a href="/index.php?category=Formal">Formal</a></li>
                    <li><a href="/cart.php">Cart <?php
                                                    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                                        echo '(' . count($_SESSION['cart']) . ')';
                                                    }
                                                    ?></a></li>
                    <li><a href="/admin/index.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">