<?php
// config/config.php - PRODUCTION VERSION for your InfinityFree account
define('SITE_URL', 'https://100-percent-correct-decision.infinityfreeapp.com');
define('ADMIN_EMAIL', 'admin@100-percent-correct-decision.infinityfreeapp.com');
define('ENCRYPTION_KEY', 'handmade-shop-secure-key-2025-ukraine-crafts-39340877');
define('ITEMS_PER_PAGE', 12);
define('MAX_CART_ITEMS', 50);
define('CURRENCY', 'UAH');
define('DEFAULT_LANGUAGE', 'uk');

// Production environment settings
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);

// Платіжні методи
define('PAYMENT_METHODS', [
    'card' => 'Банківська картка',
    'cash' => 'Готівка при отриманні',
    'paypal' => 'PayPal'
]);

// Методи доставки
define('DELIVERY_METHODS', [
    'pickup' => 'Самовивіз',
    'courier' => 'Кур\'єрська доставка',
    'post' => 'Нова пошта'
]);

// Security settings for production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
?>