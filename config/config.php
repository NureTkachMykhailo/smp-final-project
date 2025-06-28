<?php
// config/config.php
define('SITE_URL', 'http://localhost/project');
define('ADMIN_EMAIL', 'admin@handmade-shop.com');
define('ENCRYPTION_KEY', 'your-secret-encryption-key-here');
define('ITEMS_PER_PAGE', 12);
define('MAX_CART_ITEMS', 50);
define('CURRENCY', 'UAH');
define('DEFAULT_LANGUAGE', 'uk');

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