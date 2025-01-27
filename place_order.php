<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('db_connection.php');
include('stock_notification.php'); // Include the new notification file

header('Content-Type: application/json');

// Start output buffering
ob_start();

// Error logging
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
error_log("Start processing order...");

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }

    $cart = $data['cart'] ?? [];
    if (empty($cart)) {
        throw new Exception('Cart is empty');
    }

    foreach ($cart as $item) {
        $productId = $item['id'] ?? null;
        $quantity = $item['quantity'] ?? null;
        $price = $item['price'] ?? null;

        if ($productId === null || $quantity === null || $price === null) {
            throw new Exception('Invalid product data: ' . json_encode($item));
        }

        $totalPrice = $quantity * $price;
        $orderDate = date('Y-m-d H:i:s');

        // Insert order into database
        $stmt = $conn->prepare("INSERT INTO orders (product_id, quantity, total_price, order_date) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param('iids', $productId, $quantity, $totalPrice, $orderDate);
        if (!$stmt->execute()) {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }

        // Reduce product stock accordingly
        $updateStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        if (!$updateStmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $updateStmt->bind_param('ii', $quantity, $productId);
        if (!$updateStmt->execute()) {
            throw new Exception("Execute statement failed: " . $updateStmt->error);
        }

        // Check current stock level
        $stockStmt = $conn->prepare("SELECT name, stock FROM products WHERE id = ?");
        if (!$stockStmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stockStmt->bind_param('i', $productId);
        $stockStmt->execute();
        $stockResult = $stockStmt->get_result();
        $product = $stockResult->fetch_assoc();

        // Check against the threshold
        $thresholdStmt = $conn->prepare("SELECT threshold FROM stock_thresholds WHERE product_id = ?");
        if (!$thresholdStmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $thresholdStmt->bind_param('i', $productId);
        $thresholdStmt->execute();
        $thresholdResult = $thresholdStmt->get_result();
        $threshold = $thresholdResult->fetch_assoc()['threshold'];

        if ($product['stock'] < $threshold) {
            sendEmailNotification($product['name'], $threshold, $product['stock']);
        }
    }

    $response = ['status' => 'success'];
} catch (Exception $e) {
    error_log($e->getMessage());
    $response['message'] = $e->getMessage();
}

// Clean (erase) the output buffer and turn off output buffering
ob_end_clean();

// Ensure a JSON response is always returned
echo json_encode($response);

$conn->close();
?>
