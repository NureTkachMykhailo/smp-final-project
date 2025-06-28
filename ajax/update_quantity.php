<?php
// ajax/update_quantity.php
require_once '../includes/functions.php';
require_once '../classes/Session.php';
require_once '../classes/Security.php';
require_once '../classes/Product.php';
require_once '../classes/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

Session::start();

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if ($product_id <= 0) {
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

if ($quantity < 0) {
    echo json_encode(['error' => 'Invalid quantity']);
    exit;
}

// Перевірка наявності товару
$product = Product::getById($product_id);
if (!$product) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}

if ($quantity > $product->getStockQuantity()) {
    echo json_encode(['error' => 'Not enough stock available']);
    exit;
}

if (Session::updateCartQuantity($product_id, $quantity)) {
    echo json_encode([
        'success' => true,
        'message' => 'Quantity updated',
        'cart_count' => Session::getCartCount()
    ]);
} else {
    echo json_encode(['error' => 'Failed to update quantity']);
}
?>