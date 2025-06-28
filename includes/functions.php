<?php
// includes/functions.php

// Автозавантаження класів
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Функція для безпечного виведення
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Функція для форматування ціни
function formatPrice($price, $currency = 'грн') {
    return number_format($price, 2, '.', ' ') . ' ' . $currency;
}

// Функція для скорочення тексту
function truncate($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

// Функція для генерації URL
function url($path = '') {
    $base_url = rtrim(SITE_URL, '/');
    $path = ltrim($path, '/');
    return $base_url . ($path ? '/' . $path : '');
}

// Функція для поточного URL
function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// Функція для перевірки AJAX запиту
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Функція для JSON відповіді
function jsonResponse($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Функція для редиректу
function redirect($url, $status_code = 302) {
    header("Location: $url", true, $status_code);
    exit;
}

// Функція для flash повідомлень
function setFlashMessage($message, $type = 'info') {
    Session::start();
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

function getFlashMessage() {
    Session::start();
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Функція для валідації даних
function validate($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule_set) {
        $value = $data[$field] ?? null;
        $rules_array = explode('|', $rule_set);
        
        foreach ($rules_array as $rule) {
            if ($rule === 'required' && empty($value)) {
                $errors[$field] = "Поле $field є обов'язковим";
                break;
            }
            
            if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Поле $field має бути валідним email";
                break;
            }
            
            if (strpos($rule, 'min:') === 0) {
                $min_length = (int)substr($rule, 4);
                if (strlen($value) < $min_length) {
                    $errors[$field] = "Поле $field має містити мінімум $min_length символів";
                    break;
                }
            }
            
            if (strpos($rule, 'max:') === 0) {
                $max_length = (int)substr($rule, 4);
                if (strlen($value) > $max_length) {
                    $errors[$field] = "Поле $field має містити максимум $max_length символів";
                    break;
                }
            }
        }
    }
    
    return $errors;
}

// Функція для завантаження файлів
function uploadFile($file, $upload_dir = 'assets/images/products/', $allowed_types = ['jpg', 'jpeg', 'png', 'webp']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Помилка завантаження файлу'];
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        return ['error' => 'Недозволений тип файлу'];
    }
    
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        return ['error' => 'Файл занадто великий (максимум 5MB)'];
    }
    
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $new_filename];
    }
    
    return ['error' => 'Не вдалося завантажити файл'];
}

// Функція для генерації хлібних крихт
function generateBreadcrumbs($current_page) {
    $breadcrumbs = ['<a href="index.php">Головна</a>'];
    
    switch ($current_page) {
        case 'products.php':
            $breadcrumbs[] = 'Товари';
            break;
        case 'cart.php':
            $breadcrumbs[] = '<a href="products.php">Товари</a>';
            $breadcrumbs[] = 'Кошик';
            break;
        case 'checkout.php':
            $breadcrumbs[] = '<a href="products.php">Товари</a>';
            $breadcrumbs[] = '<a href="cart.php">Кошик</a>';
            $breadcrumbs[] = 'Оформлення замовлення';
            break;
        case 'favorites.php':
            $breadcrumbs[] = 'Обране';
            break;
        case 'contact.php':
            $breadcrumbs[] = 'Контакти';
            break;
    }
    
    return implode(' &raquo; ', $breadcrumbs);
}

// Функція для логування
function logActivity($message, $level = 'info', $context = []) {
    $log_file = __DIR__ . '/../logs/' . date('Y-m-d') . '.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $log_entry = [
        'timestamp' => $timestamp,
        'level' => strtoupper($level),
        'message' => $message,
        'ip' => $ip,
        'user_agent' => $user_agent,
        'context' => $context
    ];
    
    file_put_contents($log_file, json_encode($log_entry, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
}

// Функція для генерації випадкового рядка
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Функція для очищення кешу
function clearCache($pattern = '*') {
    $cache_dir = __DIR__ . '/../cache/';
    if (is_dir($cache_dir)) {
        $files = glob($cache_dir . $pattern);
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

// Функція для перевірки ботів
function isBot() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $bots = ['googlebot', 'bingbot', 'slurp', 'crawler', 'spider', 'bot'];
    
    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
            return true;
        }
    }
    
    return false;
}

// Функція для мінімізації CSS/JS
function minify($content, $type = 'css') {
    if ($type === 'css') {
        // Видалення коментарів
        $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
        // Видалення зайвих пробілів
        $content = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $content);
    } elseif ($type === 'js') {
        // Видалення однорядкових коментарів
        $content = preg_replace('/\/\/.*$/m', '', $content);
        // Видалення багаторядкових коментарів
        $content = preg_replace('/\/\*[\s\S]*?\*\//', '', $content);
        // Видалення зайвих пробілів
        $content = preg_replace('/\s+/', ' ', $content);
    }
    
    return trim($content);
}

// Функція для стиснення відповіді
function enableGzipCompression() {
    if (!ob_get_level() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
        ob_start('ob_gzhandler');
    }
}

// Функція для генерації sitemap
function generateSitemap() {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Основні сторінки
    $pages = [
        'index.php' => ['priority' => '1.0', 'changefreq' => 'daily'],
        'products.php' => ['priority' => '0.9', 'changefreq' => 'daily'],
        'contact.php' => ['priority' => '0.5', 'changefreq' => 'monthly']
    ];
    
    foreach ($pages as $page => $config) {
        $sitemap .= '<url>' . "\n";
        $sitemap .= '<loc>' . url($page) . '</loc>' . "\n";
        $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $sitemap .= '<changefreq>' . $config['changefreq'] . '</changefreq>' . "\n";
        $sitemap .= '<priority>' . $config['priority'] . '</priority>' . "\n";
        $sitemap .= '</url>' . "\n";
    }
    
    // Додавання товарів
    try {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, updated_at FROM products WHERE stock_quantity > 0");
        while ($product = $stmt->fetch()) {
            $sitemap .= '<url>' . "\n";
            $sitemap .= '<loc>' . url("product.php?id=" . $product['id']) . '</loc>' . "\n";
            $sitemap .= '<lastmod>' . date('Y-m-d', strtotime($product['updated_at'])) . '</lastmod>' . "\n";
            $sitemap .= '<changefreq>weekly</changefreq>' . "\n";
            $sitemap .= '<priority>0.8</priority>' . "\n";
            $sitemap .= '</url>' . "\n";
        }
    } catch (Exception $e) {
        // Продовжуємо без товарів якщо помилка БД
    }
    
    $sitemap .= '</urlset>';
    
    file_put_contents(__DIR__ . '/../sitemap.xml', $sitemap);
}

// Функція для перевірки мобільного пристрою
function isMobile() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return preg_match('/Mobile|Android|iPhone|iPad/', $user_agent);
}

// Функція для конвертації зображень у WebP
function convertToWebP($source_path, $quality = 80) {
    if (!extension_loaded('gd')) {
        return false;
    }
    
    $info = getimagesize($source_path);
    if ($info === false) {
        return false;
    }
    
    $mime_type = $info['mime'];
    
    switch ($mime_type) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source_path);
            break;
        default:
            return false;
    }
    
    if ($image === false) {
        return false;
    }
    
    $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $source_path);
    
    $result = imagewebp($image, $webp_path, $quality);
    imagedestroy($image);
    
    return $result ? $webp_path : false;
}

// Налаштування часової зони
date_default_timezone_set('Europe/Kiev');

// Налаштування локалі для форматування чисел
setlocale(LC_MONETARY, 'uk_UA.UTF-8', 'uk_UA', 'ukrainian');

// Ініціалізація сесії якщо ще не ініціалізована
if (session_status() === PHP_SESSION_NONE) {
    Session::start();
}
?>