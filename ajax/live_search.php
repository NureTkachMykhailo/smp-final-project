<?php
// ajax/live_search.php
require_once '../classes/Product.php';
require_once '../classes/Database.php';
require_once '../classes/Security.php';

header('Content-Type: application/json');

$query = Security::sanitizeInput($_GET['q'] ?? '');

if (strlen($query) < 2) {
    echo json_encode(['results' => []]);
    exit;
}

$products = Product::search($query);
$results = [];

foreach (array_slice($products, 0, 5) as $product) {
    $results[] = [
        'id' => $product->getId(),
        'name' => $product->getName(),
        'price' => number_format($product->getPrice(), 2),
        'image' => $product->getImage(),
        'url' => 'products.php?id=' . $product->getId()
    ];
}

echo json_encode(['results' => $results]);
?>