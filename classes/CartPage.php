<?php
// classes/CartPage.php
class CartPage extends Page {
    private $cart_items = [];
    private $total_amount = 0;
    
    public function __construct($title = 'Cart', $language = 'uk') {
        parent::__construct($title, $language);
        $this->loadCartItems();
        $this->calculateTotal();
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
    
    protected function renderBody() {
        echo '<main class="container">';
        echo '<h1>' . $this->t('shopping_cart') . '</h1>';
        
        if (empty($this->cart_items)) {
            $this->renderEmptyCart();
        } else {
            $this->renderCartItems();
            $this->renderCartSummary();
        }
        
        echo '</main>';
    }
    
    private function renderEmptyCart() {
        echo '<div class="empty-cart">';
        echo '<div class="empty-cart-icon">';
        echo '<i class="icon-cart-empty">🛒</i>';
        echo '</div>';
        echo '<h2>' . $this->t('cart_empty') . '</h2>';
        echo '<p>' . $this->t('cart_empty_message') . '</p>';
        echo '<a href="products.php" class="btn btn-primary">' . $this->t('continue_shopping') . '</a>';
        echo '</div>';
    }
    
    private function renderCartItems() {
        echo '<div class="cart-items">';
        echo '<div class="cart-header">';
        echo '<div class="item-info">' . $this->t('product') . '</div>';
        echo '<div class="item-price">' . $this->t('price') . '</div>';
        echo '<div class="item-quantity">' . $this->t('quantity') . '</div>';
        echo '<div class="item-subtotal">' . $this->t('subtotal') . '</div>';
        echo '<div class="item-actions"></div>';
        echo '</div>';
        
        foreach ($this->cart_items as $item) {
            $product = $item['product'];
            echo '<div class="cart-item" data-product-id="' . $product->getId() . '">';
            
            echo '<div class="item-info">';
            echo '<div class="item-image">';
            $imagePath = 'assets/images/products/' . $product->getImage();
            if (file_exists($imagePath) && $product->getImage()) {
                echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($product->getName()) . '">';
            } else {
                echo '<div class="no-image-small">' . $this->t('photo') . '</div>';
            }
            echo '</div>';
            echo '<div class="item-details">';
            echo '<h3>' . htmlspecialchars($product->getName($this->language)) . '</h3>';
            echo '<p>' . htmlspecialchars(mb_substr($product->getDescription($this->language), 0, 80)) . '...</p>';
            echo '</div>';
            echo '</div>';
            
            echo '<div class="item-price">' . number_format($product->getPrice(), 2) . ' ' . $this->t('currency') . '</div>';
            
            echo '<div class="item-quantity">';
            echo '<div class="quantity-controls">';
            echo '<button class="qty-btn minus" data-product-id="' . $product->getId() . '">-</button>';
            echo '<input type="number" class="quantity-input" value="' . $item['quantity'] . '" min="1" max="' . $product->getStockQuantity() . '" data-product-id="' . $product->getId() . '">';
            echo '<button class="qty-btn plus" data-product-id="' . $product->getId() . '">+</button>';
            echo '</div>';
            echo '</div>';
            
            echo '<div class="item-subtotal">' . number_format($item['subtotal'], 2) . ' ' . $this->t('currency') . '</div>';
            
            echo '<div class="item-actions">';
            echo '<button class="remove-item-btn" data-product-id="' . $product->getId() . '" title="' . $this->t('remove_from_cart') . '">';
            echo '<i class="icon-trash">🗑️</i>';
            echo '</button>';
            echo '</div>';
            
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    private function renderCartSummary() {
        echo '<div class="cart-summary">';
        echo '<div class="summary-row">';
        echo '<span>' . $this->t('total_items') . ':</span>';
        echo '<span>' . count($this->cart_items) . '</span>';
        echo '</div>';
        echo '<div class="summary-row total-row">';
        echo '<span>' . $this->t('total_amount') . ':</span>';
        echo '<span class="total-price">' . number_format($this->total_amount, 2) . ' ' . $this->t('currency') . '</span>';
        echo '</div>';
        echo '<div class="cart-actions">';
        echo '<a href="products.php" class="btn btn-secondary">' . $this->t('continue_shopping') . '</a>';
        echo '<a href="checkout.php" class="btn btn-primary">' . $this->t('checkout') . '</a>';
        echo '</div>';
        echo '</div>';
    }
    
    public function getTotalAmount() {
        return $this->total_amount;
    }
    
    public function getCartItems() {
        return $this->cart_items;
    }
}