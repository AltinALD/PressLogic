<?php
// Include the database connection only once
include('db_connection.php');

// Fetch products from the database
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
    <link rel="stylesheet" href="ee.css">
    <script src="Logic.js" defer></script>
</head>
<body>

    <!-- Navbar -->
    <header>
        <nav class="navbar">
     
           <h1 class="logo">
           <img id="fotologo" src="images/p5.jpg" alt="Logo" style="width: 55px; height: 55px; border-radius: 50%; margin-right: 10px; object-fit: cover;">


            Press Logic</h1>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="./orders.php">Orders</a></li>
                <li><a href="./cart.php">Cart (<span id="cart-count">0</span>)</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Products Section -->
    <section class="products">
        <h2>Our Products</h2>
        <div class="product-list">
        <?php
        // Check if there are products
        if (mysqli_num_rows($result) > 0) {
            // Loop through each product and display it
            while ($row = mysqli_fetch_assoc($result)) {
                // Safely escape product name and price for JavaScript
                $escapedName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                $price = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
                $image = htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8');
                $productId = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');

                echo "
                <div class='product'>
                    <img src='images/{$image}' alt='{$escapedName}'>
                    <h3>{$escapedName}</h3>
                    <p>\${$price}</p>
                    <button onclick=\"addToCart({$productId}, '{$escapedName}', {$price})\">Add to Cart</button>
                </div>";
            }
        } else {
            echo "<p>No products found.</p>";
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>© 2024 My Store - All rights reserved</p>
    </footer>
   
</body>
</html>
