<?php
// checkout.php
require_once 'includes/functions.php';
require_once 'classes/Page.php';
require_once 'classes/CartPage.php';
require_once 'classes/Product.php';
require_once 'classes/Database.php';
require_once 'classes/Session.php';
require_once 'classes/Security.php';
require_once 'config/config.php';

Security::setSecurityHeaders();
Session::start();

$language = Session::getLanguage();

class CheckoutPage extends Page {
    private $cart_items = [];
    private $total_amount = 0;
    private $order_placed = false;
    private $order_id = null;
    
    public function __construct($title = 'Checkout', $language = 'uk') {
        parent::__construct($title, $language);
        $this->loadCartItems();
        $this->calculateTotal();
        $this->handleOrderSubmission();
    }
    
    private function loadCartItems() {
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            $product_ids = array_keys($_SESSION['cart']);
            $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
            
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
            $stmt->execute($product_ids);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $product = new Product($row);
                $quantity = $_SESSION['cart'][$row['id']];
                $this->cart_items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->getPrice() * $quantity
                ];
            }
        }
    }
    
    private function calculateTotal() {
        $this->total_amount = array_sum(array_column($this->cart_items, 'subtotal'));
    }
    
    private function handleOrderSubmission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
            if (Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $this->processOrder();
            }
        }
    }
    
    private function processOrder() {
        if (empty($this->cart_items)) {
            return;
        }
        
        $customer_name = Security::sanitizeInput($_POST['customer_name'] ?? '');
        $customer_email = Security::sanitizeEmail($_POST['customer_email'] ?? '');
        $customer_phone = Security::sanitizeInput($_POST['customer_phone'] ?? '');
        $delivery_address = Security::sanitizeInput($_POST['delivery_address'] ?? '');
        $payment_method = Security::sanitizeInput($_POST['payment_method'] ?? 'card');
        $delivery_method = Security::sanitizeInput($_POST['delivery_method'] ?? 'courier');
        $notes = Security::sanitizeInput($_POST['notes'] ?? '');
        
        if (empty($customer_name) || empty($customer_email) || !Security::validateEmail($customer_email)) {
            return;
        }
        
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            // Створення замовлення
            $stmt = $db->prepare("
                INSERT INTO orders (customer_name, customer_email, customer_phone, 
                                  delivery_address, total_amount, payment_method, 
                                  delivery_method, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $customer_name,
                $customer_email,
                $customer_phone,
                $delivery_address,
                $this->total_amount,
                $payment_method,
                $delivery_method,
                $notes
            ]);
            
            $this->order_id = $db->lastInsertId();
            
            // Додавання позицій замовлення
            $stmt = $db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($this->cart_items as $item) {
                $stmt->execute([
                    $this->order_id,
                    $item['product']->getId(),
                    $item['quantity'],
                    $item['product']->getPrice()
                ]);
            }
            
            $db->commit();
            
            // Очищення кошика
            Session::clearCart();
            
            $this->order_placed = true;
            
        } catch (Exception $e) {
            $db->rollback();
            error_log("Order processing failed: " . $e->getMessage());
        }
    }
    
    protected function renderBody() {
        echo '<main class="container">';
        
        if ($this->order_placed) {
            $this->renderOrderSuccess();
        } else {
            if (empty($this->cart_items)) {
                echo '<div class="checkout-empty">';
                echo '<h1>' . $this->t('cannot_place_order') . '</h1>';
                echo '<p>' . $this->t('cart_empty_message') . '</p>';
                echo '<a href="products.php" class="btn btn-primary">' . $this->t('continue_shopping') . '</a>';
                echo '</div>';
            } else {
                echo '<h1>' . $this->t('order_form') . '</h1>';
                $this->renderCheckoutForm();
            }
        }
        
        echo '</main>';
    }
    
    private function renderOrderSuccess() {
        echo '<div class="order-success">';
        echo '<div class="success-icon">✅</div>';
        echo '<h1>' . $this->t('order_success') . '</h1>';
        echo '<p>' . $this->t('order_number') . ': <strong>#' . $this->order_id . '</strong></p>';
        echo '<p>' . $this->t('contact_soon') . '</p>';
        echo '<div class="success-actions">';
        echo '<a href="index.php" class="btn btn-primary">' . $this->t('back_to_shop') . '</a>';
        echo '<a href="products.php" class="btn btn-secondary">' . $this->t('continue_shopping_btn') . '</a>';
        echo '</div>';
        echo '</div>';
    }
    
    private function renderCheckoutForm() {
        echo '<div class="checkout-layout">';
        echo '<div class="checkout-form">';
        echo '<form method="POST" action="">';
        echo '<input type="hidden" name="csrf_token" value="' . Security::generateCSRFToken() . '">';
        
        echo '<fieldset class="customer-info">';
        echo '<legend>' . $this->t('customer_info') . '</legend>';
        
        echo '<div class="form-group">';
        echo '<label for="customer_name">' . $this->t('name') . ' *</label>';
        echo '<input type="text" id="customer_name" name="customer_name" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="customer_email">' . $this->t('email') . ' *</label>';
        echo '<input type="email" id="customer_email" name="customer_email" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="customer_phone">' . $this->t('phone') . '</label>';
        echo '<input type="tel" id="customer_phone" name="customer_phone">';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="delivery_address">' . $this->t('delivery_address') . '</label>';
        echo '<textarea id="delivery_address" name="delivery_address" rows="3"></textarea>';
        echo '</div>';
        
        echo '</fieldset>';
        
        echo '<fieldset class="delivery-payment">';
        echo '<legend>' . $this->t('delivery_payment') . '</legend>';
        
        echo '<div class="form-group">';
        echo '<label for="payment_method">' . $this->t('payment_method') . '</label>';
        echo '<select id="payment_method" name="payment_method">';
        foreach (PAYMENT_METHODS as $key => $value) {
            echo '<option value="' . $key . '">' . $this->t('payment_' . $key) . '</option>';
        }
        echo '</select>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="delivery_method">' . $this->t('delivery_method') . '</label>';
        echo '<select id="delivery_method" name="delivery_method">';
        foreach (DELIVERY_METHODS as $key => $value) {
            echo '<option value="' . $key . '">' . $this->t('delivery_' . $key) . '</option>';
        }
        echo '</select>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="notes">' . $this->t('order_notes') . '</label>';
        echo '<textarea id="notes" name="notes" rows="3" placeholder="' . $this->t('order_notes_placeholder') . '"></textarea>';
        echo '</div>';
        
        echo '</fieldset>';
        
        echo '<button type="submit" name="place_order" class="btn btn-primary btn-large">' . $this->t('place_order') . '</button>';
        echo '</form>';
        echo '</div>';
        
        echo '<div class="order-summary">';
        $this->renderOrderSummary();
        echo '</div>';
        
        echo '</div>';
    }
    
    private function renderOrderSummary() {
        echo '<div class="summary-card">';
        echo '<h3>' . $this->t('your_order') . '</h3>';
        
        foreach ($this->cart_items as $item) {
            echo '<div class="summary-item">';
            echo '<span class="item-name">' . htmlspecialchars($item['product']->getName($this->language)) . '</span>';
            echo '<span class="item-details">' . $item['quantity'] . ' × ' . number_format($item['product']->getPrice(), 2) . ' ' . $this->t('currency') . '</span>';
            echo '<span class="item-total">' . number_format($item['subtotal'], 2) . ' ' . $this->t('currency') . '</span>';
            echo '</div>';
        }
        
        echo '<div class="summary-total">';
        echo '<strong>' . $this->t('total_amount') . ': ' . number_format($this->total_amount, 2) . ' ' . $this->t('currency') . '</strong>';
        echo '</div>';
        echo '</div>';
    }
}

$translations = include "lang/{$language}.php";
$checkoutPage = new CheckoutPage($translations['checkout'], $language);
$checkoutPage->render();
?>