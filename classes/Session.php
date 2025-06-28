<?php
// classes/Session.php
class Session {
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
    }
    
    public static function addToCart($product_id, $quantity = 1) {
        self::start();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        // Перевірка наявності товару
        $product = Product::getById($product_id);
        if ($product && $_SESSION['cart'][$product_id] > $product->getStockQuantity()) {
            $_SESSION['cart'][$product_id] = $product->getStockQuantity();
        }
        
        return true;
    }
    
    public static function removeFromCart($product_id) {
        self::start();
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            return true;
        }
        
        return false;
    }
    
    public static function updateCartQuantity($product_id, $quantity) {
        self::start();
        
        if ($quantity <= 0) {
            return self::removeFromCart($product_id);
        }
        
        $product = Product::getById($product_id);
        if ($product) {
            $max_quantity = min($quantity, $product->getStockQuantity());
            $_SESSION['cart'][$product_id] = $max_quantity;
            return true;
        }
        
        return false;
    }
    
    public static function getCartCount() {
        self::start();
        
        if (!isset($_SESSION['cart'])) {
            return 0;
        }
        
        return array_sum($_SESSION['cart']);
    }
    
    public static function addToFavorites($product_id) {
        self::start();
        
        if (!isset($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }
        
        if (!in_array($product_id, $_SESSION['favorites'])) {
            $_SESSION['favorites'][] = $product_id;
        }
        
        return true;
    }
    
    public static function removeFromFavorites($product_id) {
        self::start();
        
        if (isset($_SESSION['favorites'])) {
            $key = array_search($product_id, $_SESSION['favorites']);
            if ($key !== false) {
                unset($_SESSION['favorites'][$key]);
                $_SESSION['favorites'] = array_values($_SESSION['favorites']);
            }
        }
        
        return true;
    }
    
    public static function isInFavorites($product_id) {
        self::start();
        
        return isset($_SESSION['favorites']) && in_array($product_id, $_SESSION['favorites']);
    }
    
    public static function clearCart() {
        self::start();
        unset($_SESSION['cart']);
    }
    
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function setLanguage($language) {
        self::start();
        $_SESSION['language'] = $language;
        
        // Fix: Set cookie with proper path and immediate effect
        $expire = time() + (86400 * 30); // 30 days
        $path = '/';
        $domain = '';
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $httponly = false; // Allow JavaScript access for language switching
        
        setcookie('language', $language, $expire, $path, $domain, $secure, $httponly);
        
        // Immediately update $_COOKIE for current request
        $_COOKIE['language'] = $language;
    }
    
    public static function getLanguage() {
        self::start();
        // Prioritize session over cookie for consistency
        if (isset($_SESSION['language'])) {
            return $_SESSION['language'];
        }
        
        // Fallback to cookie, then default
        return $_COOKIE['language'] ?? 'uk';
    }
}