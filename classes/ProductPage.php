<?php
// classes/ProductPage.php
class ProductPage extends Page {
    private $products = [];
    private $categories = [];
    private $current_category = null;
    private $search_query = '';
    
    public function __construct($title = 'Products', $language = 'uk') {
        parent::__construct($title, $language);
        $this->loadCategories();
        $this->handleFilters();
        $this->loadProducts();
    }
    
    private function loadCategories() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, name, name_en, description FROM categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Modify category names based on current language
        foreach ($categories as &$category) {
            if ($this->language === 'en' && !empty($category['name_en'])) {
                $category['display_name'] = $category['name_en'];
            } else {
                $category['display_name'] = $category['name'];
            }
        }
        
        $this->categories = $categories;
    }
    
    private function handleFilters() {
        $this->current_category = $_GET['category'] ?? null;
        $this->search_query = $_GET['search'] ?? '';
    }
    
    private function loadProducts() {
        if (!empty($this->search_query)) {
            $this->products = Product::search($this->search_query, $this->current_category);
        } else {
            $this->products = Product::getAll(null, false);
            
            if ($this->current_category) {
                $this->products = array_filter($this->products, function($product) {
                    return $product->getCategoryId() == $this->current_category;
                });
            }
        }
    }
    
    protected function renderBody() {
        echo '<main class="container">';
        echo '<div class="page-header">';
        echo '<h1>' . $this->t('products') . '</h1>';
        $this->renderSearchForm();
        echo '</div>';
        
        echo '<div class="products-layout">';
        $this->renderSidebar();
        $this->renderProductGrid();
        echo '</div>';
        echo '</main>';
    }
    
    private function renderSearchForm() {
        echo '<form class="search-form" method="GET" action="">';
        echo '<div class="search-input-group">';
        echo '<input type="text" name="search" placeholder="' . $this->t('search_placeholder') . '" value="' . htmlspecialchars($this->search_query) . '">';
        echo '<button type="submit" class="search-btn">';
        echo '<i class="icon-search">🔍</i>';
        echo '</button>';
        echo '</div>';
        
        if ($this->current_category) {
            echo '<input type="hidden" name="category" value="' . $this->current_category . '">';
        }
        
        echo '</form>';
    }
    
    private function renderSidebar() {
        echo '<aside class="products-sidebar">';
        echo '<div class="filter-section">';
        echo '<h3>' . $this->t('categories') . '</h3>';
        echo '<ul class="category-list">';
        
        $activeClass = !$this->current_category ? 'active' : '';
        echo '<li><a href="products.php" class="' . $activeClass . '">' . $this->t('all_categories') . '</a></li>';
        
        foreach ($this->categories as $category) {
            $activeClass = ($this->current_category == $category['id']) ? 'active' : '';
            $url = 'products.php?category=' . $category['id'];
            if ($this->search_query) {
                $url .= '&search=' . urlencode($this->search_query);
            }
            echo '<li><a href="' . $url . '" class="' . $activeClass . '">' . htmlspecialchars($category['display_name']) . '</a></li>';
        }
        
        echo '</ul>';
        echo '</div>';
        echo '</aside>';
    }
    
    private function renderProductGrid() {
        echo '<div class="products-content">';
        
        if (empty($this->products)) {
            echo '<div class="no-products">';
            echo '<p>' . $this->t('no_products_found') . '</p>';
            echo '<a href="products.php" class="btn btn-primary">' . $this->t('view_all_products') . '</a>';
            echo '</div>';
        } else {
            echo '<div class="products-count">';
            echo $this->t('found_products') . ': ' . count($this->products);
            echo '</div>';
            
            echo '<div class="products-grid">';
            foreach ($this->products as $product) {
                $product->render(true, 'fade-in');
            }
            echo '</div>';
        }
        
        echo '</div>';
    }
}