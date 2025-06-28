<?php
// ajax/add_to_cart.php
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

// Перевірка CSRF токену
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

// Rate limiting
if (!Security::rateLimitCheck('add_to_cart', 20, 60)) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests']);
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

// Перевірка існування товару
$product = Product::getById($product_id);
if (!$product) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}

// Перевірка наявності
if ($product->getStockQuantity() < $quantity) {
    echo json_encode(['error' => 'Not enough stock']);
    exit;
}

// Додавання в кошик
if (Session::addToCart($product_id, $quantity)) {
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart',
        'cart_count' => Session::getCartCount()
    ]);
} else {
    echo json_encode(['error' => 'Failed to add product to cart']);
}
?>