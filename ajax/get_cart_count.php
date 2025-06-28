<?php
// ajax/get_cart_count.php
require_once '../classes/Session.php';

header('Content-Type: application/json');
Session::start();

echo json_encode(['count' => Session::getCartCount()]);
?>