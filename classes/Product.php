<?php
// classes/Product.php
class Product {
    private $id;
    private $name;
    private $description;
    private $price;
    private $image;
    private $category_id;
    private $stock_quantity;
    private $is_featured;
    private $name_en;
    private $description_en;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? '';
            $this->description = $data['description'] ?? '';
            $this->price = $data['price'] ?? 0;
            $this->image = $data['image'] ?? '';
            $this->category_id = $data['category_id'] ?? 0;
            $this->stock_quantity = $data['stock_quantity'] ?? 0;
            $this->is_featured = $data['is_featured'] ?? false;
            
            // Store English translations if available
            $this->name_en = $data['name_en'] ?? '';
            $this->description_en = $data['description_en'] ?? '';
        }
    }
    
    // Геттери with language support
    public function getId() { return $this->id; }
    
    public function getName($language = null) { 
        if ($language === 'en' && !empty($this->name_en)) {
            return $this->name_en;
        }
        return $this->name; 
    }
    
    public function getDescription($language = null) { 
        if ($language === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description; 
    }
    
    public function getPrice() { return $this->price; }
    public function getImage() { return $this->image; }
    public function getCategoryId() { return $this->category_id; }
    public function getStockQuantity() { return $this->stock_quantity; }
    public function isFeatured() { return $this->is_featured; }
    
    // Сеттери
    public function setName($name) { $this->name = $name; }
    public function setDescription($description) { $this->description = $description; }
    public function setPrice($price) { $this->price = $price; }
    public function setImage($image) { $this->image = $image; }
    public function setStockQuantity($quantity) { $this->stock_quantity = $quantity; }
    
    public static function getAll($limit = null, $featured_only = false) {
        $db = Database::getInstance();
        // Fixed: Always select multilingual columns
        $sql = "SELECT p.*, c.name as category_name, c.name_en as category_name_en 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        
        if ($featured_only) {
            $sql .= " WHERE p.is_featured = 1";
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $db->query($sql);
        $products = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new self($row);
        }
        
        return $products;
    }
    
    public static function getById($id) {
        $db = Database::getInstance();
        // Fixed: Always select multilingual columns
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new self($row);
        }
        
        return null;
    }
    
    public static function search($query, $category_id = null) {
        $db = Database::getInstance();
        // Fixed: Always select multilingual columns and search in both languages
        $sql = "SELECT * FROM products WHERE (name LIKE ? OR description LIKE ? OR name_en LIKE ? OR description_en LIKE ?)";
        $params = ["%$query%", "%$query%", "%$query%", "%$query%"];
        
        if ($category_id) {
            $sql .= " AND category_id = ?";
            $params[] = $category_id;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new self($row);
        }
        
        return $products;
    }
    
    public function render($show_buy_button = true, $additional_classes = '') {
        // Get current language and translations
        $language = Session::getLanguage();
        $translations = include __DIR__ . "/../lang/{$language}.php";
        
        $imagePath = 'assets/images/products/' . $this->image;
        $imageExists = file_exists($imagePath);
        
        echo '<div class="product-card ' . $additional_classes . '" data-product-id="' . $this->id . '">';
        echo '<div class="product-image">';
        
        if ($imageExists && $this->image) {
            echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($this->getName($language)) . '" loading="lazy">';
        } else {
            echo '<div class="no-image">' . ($translations['no_image'] ?? 'No image') . '</div>';
        }
        
        echo '<button class="favorite-btn" data-product-id="' . $this->id . '">';
        echo '<i class="icon-heart"></i>';
        echo '</button>';
        echo '</div>';
        
        echo '<div class="product-info">';
        echo '<h3 class="product-name">' . htmlspecialchars($this->getName($language)) . '</h3>';
        echo '<p class="product-description">' . htmlspecialchars(mb_substr($this->getDescription($language), 0, 100)) . '...</p>';
        echo '<div class="product-price">' . number_format($this->price, 2) . ' ' . ($translations['currency'] ?? 'UAH') . '</div>';
        
        if ($this->stock_quantity > 0) {
            echo '<div class="product-stock in-stock">' . ($translations['in_stock'] ?? 'In stock') . ': ' . $this->stock_quantity . ' ' . ($translations['pieces'] ?? 'pcs') . '.</div>';
        } else {
            echo '<div class="product-stock out-of-stock">' . ($translations['out_of_stock'] ?? 'Out of stock') . '</div>';
        }
        
        if ($show_buy_button && $this->stock_quantity > 0) {
            echo '<div class="product-actions">';
            echo '<div class="quantity-selector">';
            echo '<button class="qty-btn minus" data-action="decrease">-</button>';
            echo '<input type="number" class="quantity-input" value="1" min="1" max="' . $this->stock_quantity . '">';
            echo '<button class="qty-btn plus" data-action="increase">+</button>';
            echo '</div>';
            echo '<button class="add-to-cart-btn" data-product-id="' . $this->id . '">' . ($translations['add_to_cart'] ?? 'Add to Cart') . '</button>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
    }
}