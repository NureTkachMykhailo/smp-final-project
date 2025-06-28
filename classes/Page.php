<?php
// classes/Page.php
class Page {
    public $title;
    public $language;
    public $translations;
    
    public function __construct($title = 'Handmade Products Shop', $language = 'uk') {
        $this->title = $title;
        $this->language = $language;
        $this->loadTranslations();
        // Temporarily disabled to fix the error
        // $this->trackPageVisit();
    }
    
    protected function loadTranslations() {
        $langFile = __DIR__ . "/../lang/{$this->language}.php";
        if (file_exists($langFile)) {
            $this->translations = include $langFile;
        } else {
            $this->translations = include __DIR__ . "/../lang/uk.php";
        }
    }
    
    public function t($key) {
        return isset($this->translations[$key]) ? $this->translations[$key] : $key;
    }
    
    protected function trackPageVisit() {
        // Temporarily commented out - table may not exist
        /*
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO page_visits (page_url, user_ip, user_agent) VALUES (?, ?, ?)");
        $stmt->execute([
            $_SERVER['REQUEST_URI'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        */
    }
    
    public function renderHeader() {
        include __DIR__ . '/../includes/header.php';
    }
    
    public function renderFooter() {
        include __DIR__ . '/../includes/footer.php';
    }
    
    public function renderNavigation() {
        include __DIR__ . '/../includes/navigation.php';
    }
    
    public function render() {
        $this->renderHeader();
        $this->renderNavigation();
        $this->renderBody();
        $this->renderFooter();
    }
    
    protected function renderBody() {
        echo '<main class="container">';
        echo '<h1>' . htmlspecialchars($this->title) . '</h1>';
        echo '</main>';
    }
}