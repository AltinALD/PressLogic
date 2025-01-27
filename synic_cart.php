<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

// Read the raw input
$rawInput = file_get_contents('php://input');
error_log("Raw input received: " . $rawInput); // Debugging line

$input = json_decode($rawInput, true);

// Log any JSON errors
if (json_last_error() !== JSON_ERROR_NONE) {
    $jsonError = json_last_error_msg();
    error_log("JSON error: " . $jsonError);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input: ' . $jsonError]);
    exit;
}

if (is_array($input)) {
    $_SESSION['cart'] = $input;
    error_log("Cart stored in session: " . json_encode($_SESSION['cart'])); // Debugging line
    echo json_encode(['status' => 'success', 'message' => 'Cart synced successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid cart data']);
}
?>
