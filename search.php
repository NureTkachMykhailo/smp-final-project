<?php
// search.php
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

// Debug: Check current language state
$current_language = Session::getLanguage();

// Force language detection from URL parameter if present
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'uk'])) {
    Session::setLanguage($_GET['lang']);
    $language = $_GET['lang'];
} else {
    $language = $current_language;
}

// Debug output (remove this in production)
// echo "<!-- Debug: Current language: $current_language, Using language: $language -->";

class SearchPage extends Page {
    private $search_query = '';
    private $search_results = [];
    private $categories = [];
    private $selected_category = null;
    
    public function __construct($title = 'Search', $language = 'uk') {
        // Ensure the title is translated
        parent::__construct($title, $language);
        $this->title = $this->t('search'); // Use translated title
        $this->loadCategories();
        $this->handleSearch();
    }
    
    private function loadCategories() {
        $db = Database::getInstance();
        // Updated: Select both name_en and description_en columns (after database update)
        $stmt = $db->query("SELECT id, name, name_en, description, description_en FROM categories ORDER BY name");
        $this->categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function handleSearch() {
        $this->search_query = Security::sanitizeInput($_GET['q'] ?? '');
        $this->selected_category = (int)($_GET['category'] ?? 0);
        
        if (!empty($this->search_query)) {
            $this->search_results = Product::search($this->search_query, $this->selected_category ?: null);
        }
    }
    
    protected function renderBody() {
        echo '<main class="container">';
        echo '<div class="search-page-header">';
        echo '<h1>' . $this->t('search') . '</h1>';
        echo '<p>' . $this->t('search_description') . '</p>';
        echo '</div>';
        
        $this->renderAdvancedSearchForm();
        
        if (!empty($this->search_query)) {
            $this->renderSearchResults();
        } else {
            $this->renderSearchSuggestions();
        }
        
        echo '</main>';
    }
    
    private function renderAdvancedSearchForm() {
        echo '<div class="advanced-search-section">';
        echo '<form method="GET" action="search.php" class="advanced-search-form">';
        
        echo '<div class="search-input-container">';
        echo '<div class="search-input-group">';
        echo '<input type="text" name="q" placeholder="' . $this->t('search_placeholder') . '" value="' . htmlspecialchars($this->search_query) . '" autofocus class="search-input-main">';
        echo '<button type="submit" class="search-btn-main">';
        echo '<span>🔍</span> ' . $this->t('search_button');
        echo '</button>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="search-filters">';
        echo '<div class="filter-group">';
        echo '<label for="category">' . $this->t('category') . ':</label>';
        echo '<select name="category" id="category">';
        echo '<option value="">' . $this->t('all_categories') . '</option>';
        foreach ($this->categories as $category) {
            $selected = ($this->selected_category == $category['id']) ? 'selected' : '';
            echo '<option value="' . $category['id'] . '" ' . $selected . '>' . htmlspecialchars($category['name']) . '</option>';
        }
        echo '</select>';
        echo '</div>';
        echo '</div>';
        
        echo '</form>';
        echo '</div>';
    }
    
    private function renderSearchResults() {
        echo '<div class="search-results-section">';
        
        if (empty($this->search_results)) {
            echo '<div class="no-results">';
            echo '<div class="no-results-icon">🔍</div>';
            echo '<h2>' . $this->t('no_results_for') . ' "' . htmlspecialchars($this->search_query) . '" ' . $this->t('not_found') . '</h2>';
            echo '<div class="search-suggestions-text">';
            echo '<p>' . $this->t('try_suggestions') . ':</p>';
            echo '<ul>';
            echo '<li>' . $this->t('check_spelling') . '</li>';
            echo '<li>' . $this->t('use_general_terms') . '</li>';
            echo '<li>' . $this->t('try_synonyms') . '</li>';
            echo '<li>' . $this->t('browse_categories') . '</li>';
            echo '</ul>';
            echo '</div>';
            echo '<div class="no-results-actions">';
            echo '<a href="products.php" class="btn btn-primary">' . $this->t('view_all_products') . '</a>';
            echo '<a href="contact.php" class="btn btn-secondary">' . $this->t('contact_us') . '</a>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="results-header">';
            echo '<h2>' . $this->t('search_results') . '</h2>';
            echo '<div class="results-meta">';
            echo '<span class="results-count">' . $this->t('found_products') . ': <strong>' . count($this->search_results) . '</strong> ' . $this->t('items') . '</span>';
            echo '<span class="search-query">' . $this->t('for_query') . ': "<em>' . htmlspecialchars($this->search_query) . '</em>"</span>';
            echo '</div>';
            echo '</div>';
            
            echo '<div class="search-results-grid">';
            foreach ($this->search_results as $product) {
                $product->render(true, 'search-result-item fade-in');
            }
            echo '</div>';
            
            // Пропозиції схожих товарів
            if (count($this->search_results) < 5) {
                $this->renderRelatedSuggestions();
            }
        }
        
        echo '</div>';
    }
    
    private function renderRelatedSuggestions() {
        // Показуємо випадкові товари як "схожі"
        $random_products = Product::getAll(6);
        
        // Fixed: Instead of using array_diff with objects, filter manually
        $suggestions = [];
        $search_result_ids = array_map(function($product) { return $product->getId(); }, $this->search_results);
        
        foreach ($random_products as $product) {
            if (!in_array($product->getId(), $search_result_ids)) {
                $suggestions[] = $product;
            }
        }
        
        $suggestions = array_slice($suggestions, 0, 4);
        
        if (!empty($suggestions)) {
            echo '<div class="related-suggestions">';
            echo '<h3>' . $this->t('you_might_like') . ':</h3>';
            echo '<div class="suggestions-grid">';
            foreach ($suggestions as $product) {
                $product->render(true, 'suggestion-item');
            }
            echo '</div>';
            echo '</div>';
        }
    }
    
    private function renderSearchSuggestions() {
        echo '<div class="search-suggestions-section">';
        echo '<h2>' . $this->t('popular_categories') . '</h2>';
        echo '<p>' . $this->t('select_category_text') . '</p>';
        
        echo '<div class="categories-grid">';
        foreach ($this->categories as $category) {
            echo '<a href="products.php?category=' . $category['id'] . '" class="category-suggestion-card">';
            echo '<div class="category-icon">🎨</div>';
            
            // Use name_en when available and language is English, otherwise use Ukrainian name
            $categoryName = ($this->language === 'en' && !empty($category['name_en'])) 
                ? $category['name_en'] 
                : $category['name'];
            
            // Category descriptions are only in Ukrainian (no description_en in database)
            // For English, we can provide simple generic descriptions or use the Ukrainian ones
            $categoryDesc = $category['description'] ?? '';
            if ($this->language === 'en') {
                // Provide English descriptions for categories since description_en doesn't exist in DB
                $englishDescriptions = [
                    'Керамічні вироби' => 'Unique handmade ceramic products',
                    'Текстильні вироби' => 'Soft toys and textile decorations',
                    'Дерев\'яні вироби' => 'Carved and wooden handicrafts',
                    'Прикраси' => 'Handmade jewelry and accessories'
                ];
                
                $categoryDesc = $englishDescriptions[$categoryName] ?? 'Handmade products in this category';
            }

            echo '<h3>' . htmlspecialchars($categoryName) . '</h3>';
            echo '<p>' . htmlspecialchars($categoryDesc) . '</p>';
            echo '<span class="category-link">' . $this->t('view_products') . ' →</span>';
            echo '</a>';
        }
        echo '</div>';
        
        // Показуємо рекомендовані товари
        $featured_products = Product::getAll(4, true);
        if (!empty($featured_products)) {
            echo '<div class="featured-suggestions">';
            echo '<h2>' . $this->t('recommendations') . '</h2>';
            echo '<div class="featured-grid">';
            foreach ($featured_products as $product) {
                $product->render(true, 'featured-suggestion');
            }
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
    }
}

$searchPage = new SearchPage('Search', $language);
$searchPage->render();
?>