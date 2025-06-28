<?php
// favorites.php
require_once 'config/config.php';   
require_once 'config/database.php';     
require_once 'includes/functions.php';
require_once 'classes/Page.php';
require_once 'classes/Product.php';
require_once 'classes/Database.php';
require_once 'classes/Session.php';
require_once 'classes/Security.php';

Security::setSecurityHeaders();
Session::start();

$language = Session::getLanguage();

class FavoritesPage extends Page {
    private $favorite_products = [];
    
    public function __construct($title = 'Favorites', $language = 'uk') {
        parent::__construct($title, $language);
        $this->loadFavoriteProducts();
    }
    
    private function loadFavoriteProducts() {
        $favorites = $_SESSION['favorites'] ?? [];
        
        if (!empty($favorites)) {
            $placeholders = str_repeat('?,', count($favorites) - 1) . '?';
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders) ORDER BY name");
            $stmt->execute($favorites);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->favorite_products[] = new Product($row);
            }
        }
    }
    
    protected function renderBody() {
        echo '<main class="container">';
        echo '<h1>' . $this->t('favorites') . '</h1>';
        
        if (empty($this->favorite_products)) {
            $this->renderEmptyFavorites();
        } else {
            $this->renderFavoriteProducts();
        }
        
        echo '</main>';
    }
    
    private function renderEmptyFavorites() {
        echo '<div class="empty-favorites">';
        echo '<div class="empty-icon">♡</div>';
        echo '<h2>' . $this->t('no_favorites_yet') . '</h2>';
        echo '<p>' . $this->t('add_favorites_message') . '</p>';
        echo '<a href="products.php" class="btn btn-primary">' . $this->t('view_catalog') . '</a>';
        echo '</div>';
    }
    
    private function renderFavoriteProducts() {
        echo '<div class="favorites-count-info">';
        echo '<p>' . $this->t('selected_items') . ': <strong>' . count($this->favorite_products) . '</strong></p>';
        echo '</div>';
        
        echo '<div class="favorites-grid">';
        foreach ($this->favorite_products as $product) {
            $product->render(true, 'favorite-item');
        }
        echo '</div>';
        
        echo '<div class="favorites-actions">';
        echo '<a href="products.php" class="btn btn-secondary">' . $this->t('continue_shopping_btn') . '</a>';
        echo '</div>';
    }
}

$translations = include "lang/{$language}.php";
$favoritesPage = new FavoritesPage($translations['favorites'], $language);
$favoritesPage->render();
?>