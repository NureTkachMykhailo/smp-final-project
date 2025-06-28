<?php
// ajax/remove_from_cart.php
require_once '../includes/functions.php';
require_once '../classes/Session.php';
require_once '../classes/Security.php';

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

if ($product_id <= 0) {
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

if (Session::removeFromCart($product_id)) {
    echo json_encode([
        'success' => true,
        'message' => 'Product removed from cart',
        'cart_count' => Session::getCartCount()
    ]);
} else {
    echo json_encode(['error' => 'Failed to remove product from cart']);
}
?>