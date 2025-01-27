<?php
// Include the database connection
include('db_connection.php');

// Query to get the order details
$sql = "SELECT o.id AS order_id, o.total_price, o.order_date, o.quantity, p.name AS product_name
        FROM orders o
        JOIN products p ON o.product_id = p.id
        ORDER BY o.order_date DESC";  // Adjust sorting as needed
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="ee.css">
</head>
<body>
    <header class="navbar">
        <h1 class="logo">My Store - Orders</h1>
        <a href="index.php" class="back-home">Back to Home</a>
    </header>

    <main class="orders-container">
        <h2>Order History</h2>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are any orders
                if ($result->num_rows > 0) {
                    // Loop through and display each order
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['order_id']}</td>
                                <td>{$row['product_name']}</td>
                                <td>{$row['quantity']}</td>
                                <td>\${$row['total_price']}</td>
                                <td>{$row['order_date']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No orders found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>

    <footer class="footer">
        <p>&copy; 2024 My Store</p>
    </footer>

    <?php
    // Close the database connection after all output is done
    $conn->close();
    ?>
</body>
</html>
