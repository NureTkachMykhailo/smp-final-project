<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// index.php
require_once 'config/config.php';        // Load constants including SITE_URL
require_once 'config/database.php';      // Load database config
require_once 'includes/functions.php';
require_once 'classes/Page.php';
require_once 'classes/Product.php';
require_once 'classes/Database.php';
require_once 'classes/Session.php';
require_once 'classes/Security.php';

Security::setSecurityHeaders();
Session::start();

$language = Session::getLanguage();

class HomePage extends Page {
    private $featured_products = [];
    
    public function __construct($title = 'Головна сторінка', $language = 'uk') {
        parent::__construct($title, $language);
        $this->loadFeaturedProducts();
    }
    
    private function loadFeaturedProducts() {
        $this->featured_products = Product::getAll(6, true);
    }
    
    protected function renderBody() {
        echo '<main>';
        $this->renderHeroSection();
        $this->renderFeaturedProducts();
        $this->renderAboutSection();
        echo '</main>';
    }
    
    private function renderHeroSection() {
        echo '<section class="hero">';
        echo '<div class="container">';
        echo '<div class="hero-content">';
        echo '<h1>' . $this->t('hero_title') . '</h1>';
        echo '<p>' . $this->t('hero_description') . '</p>';
        echo '<a href="products.php" class="btn btn-primary btn-large">' . $this->t('view_catalog') . '</a>';
        echo '</div>';
        echo '</div>';
        echo '</section>';
    }
    
    private function renderFeaturedProducts() {
        echo '<section class="featured-products">';
        echo '<div class="container">';
        echo '<h2>' . $this->t('recommendations') . '</h2>';
        
        if (!empty($this->featured_products)) {
            echo '<div class="products-grid">';
            foreach ($this->featured_products as $product) {
                $product->render(true, 'featured');
            }
            echo '</div>';
        }
        
        echo '<div class="text-center">';
        echo '<a href="products.php" class="btn btn-outline">' . $this->t('all_products') . '</a>';
        echo '</div>';
        echo '</div>';
        echo '</section>';
    }
    
    private function renderAboutSection() {
        echo '<section class="about-preview">';
        echo '<div class="container">';
        echo '<div class="about-content">';
        echo '<h2>' . $this->t('about_us') . '</h2>';
        echo '<p>' . $this->t('about_description') . '</p>';
        echo '<ul>';
        echo '<li>✓ ' . $this->t('quality_materials') . '</li>';
        echo '<li>✓ ' . $this->t('handmade_by_masters') . '</li>';
        echo '<li>✓ ' . $this->t('unique_design') . '</li>';
        echo '<li>✓ ' . $this->t('fast_delivery') . '</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</section>';
    }
}

$homePage = new HomePage('Головна сторінка', $language);
$homePage->render();
?>