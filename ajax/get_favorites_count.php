<?php
// ajax/get_favorites_count.php
require_once '../classes/Session.php';

header('Content-Type: application/json');
Session::start();

$count = isset($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0;
echo json_encode(['count' => $count]);
?>