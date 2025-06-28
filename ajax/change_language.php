<?php
// ajax/change_language.php
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

$language = $_POST['language'] ?? '';
$allowed_languages = ['uk', 'en'];

if (!in_array($language, $allowed_languages)) {
    echo json_encode(['error' => 'Invalid language']);
    exit;
}

Session::setLanguage($language);

echo json_encode([
    'success' => true,
    'language' => $language,
    'message' => 'Language changed successfully'
]);
?>