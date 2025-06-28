<?php
// contact.php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/Page.php';
require_once 'classes/Database.php';
require_once 'classes/Session.php';
require_once 'classes/Security.php';

Security::setSecurityHeaders();
Session::start();

$language = Session::getLanguage();

class ContactPage extends Page {
    private $message_sent = false;
    private $errors = [];
    
    public function __construct($title = 'Contact', $language = 'uk') {
        parent::__construct($title, $language);
        $this->handleContactForm();
    }
    
    private function handleContactForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $this->errors[] = $this->t('security_error');
                return;
            }
            
            if (!Security::rateLimitCheck('contact_form', 3, 300)) {
                $this->errors[] = $this->t('rate_limit_error');
                return;
            }
            
            $name = Security::sanitizeInput($_POST['name'] ?? '');
            $email = Security::sanitizeEmail($_POST['email'] ?? '');
            $subject = Security::sanitizeInput($_POST['subject'] ?? '');
            $message = Security::sanitizeInput($_POST['message'] ?? '');
            
            if (empty($name)) {
                $this->errors[] = $this->t('name_required');
            }
            
            if (empty($email) || !Security::validateEmail($email)) {
                $this->errors[] = $this->t('email_required');
            }
            
            if (empty($subject)) {
                $this->errors[] = $this->t('subject_required');
            }
            
            if (empty($message)) {
                $this->errors[] = $this->t('message_required');
            }
            
            if (empty($this->errors)) {
                $this->saveContactMessage($name, $email, $subject, $message);
                $this->message_sent = true;
            }
        }
    }
    
    private function saveContactMessage($name, $email, $subject, $message) {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                INSERT INTO contact_messages (name, email, subject, message, ip_address) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $subject, $message, $_SERVER['REMOTE_ADDR']]);
            
        } catch (Exception $e) {
            error_log("Contact form error: " . $e->getMessage());
            $this->errors[] = $this->t('send_error');
        }
    }
    
    protected function renderBody() {
        echo '<main class="container">';
        echo '<h1>' . $this->t('contact_us') . '</h1>';
        
        if ($this->message_sent) {
            echo '<div class="success-message">';
            echo '<h2>' . $this->t('thank_you_message') . '</h2>';
            echo '<p>' . $this->t('message_sent_text') . '</p>';
            echo '<a href="index.php" class="btn btn-primary">' . $this->t('back_to_home') . '</a>';
            echo '</div>';
        } else {
            $this->renderContactForm();
        }
        
        $this->renderContactInfo();
        echo '</main>';
    }
    
    private function renderContactForm() {
        if (!empty($this->errors)) {
            echo '<div class="error-messages">';
            foreach ($this->errors as $error) {
                echo '<p class="error">' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
        
        echo '<div class="contact-layout">';
        echo '<div class="contact-form-section">';
        echo '<h2>' . $this->t('send_message') . '</h2>';
        echo '<form method="POST" action="" class="contact-form">';
        echo '<input type="hidden" name="csrf_token" value="' . Security::generateCSRFToken() . '">';
        
        echo '<div class="form-group">';
        echo '<label for="name">' . $this->t('name') . ' *</label>';
        echo '<input type="text" id="name" name="name" required value="' . htmlspecialchars($_POST['name'] ?? '') . '">';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="email">' . $this->t('email') . ' *</label>';
        echo '<input type="email" id="email" name="email" required value="' . htmlspecialchars($_POST['email'] ?? '') . '">';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="subject">' . $this->t('subject') . ' *</label>';
        echo '<select id="subject" name="subject" required>';
        echo '<option value="">' . $this->t('select_subject') . '</option>';
        echo '<option value="product">' . $this->t('product_question') . '</option>';
        echo '<option value="order">' . $this->t('order_question') . '</option>';
        echo '<option value="delivery">' . $this->t('delivery') . '</option>';
        echo '<option value="return">' . $this->t('return_exchange') . '</option>';
        echo '<option value="cooperation">' . $this->t('cooperation') . '</option>';
        echo '<option value="other">' . $this->t('other') . '</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="message">' . $this->t('message') . ' *</label>';
        echo '<textarea id="message" name="message" rows="6" required placeholder="' . $this->t('message_placeholder') . '">' . htmlspecialchars($_POST['message'] ?? '') . '</textarea>';
        echo '</div>';
        
        echo '<button type="submit" name="send_message" class="btn btn-primary">' . $this->t('send_message_btn') . '</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }
    
    private function renderContactInfo() {
        echo '<div class="contact-info-section">';
        echo '<h2>' . $this->t('our_contacts') . '</h2>';
        echo '<div class="contact-details-grid">';
        
        echo '<div class="contact-item">';
        echo '<div class="contact-icon">📍</div>';
        echo '<div class="contact-content">';
        echo '<h4>' . $this->t('address') . '</h4>';
        echo '<p>' . $this->t('contact_address') . '</p>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="contact-item">';
        echo '<div class="contact-icon">📞</div>';
        echo '<div class="contact-content">';
        echo '<h4>' . $this->t('phone') . '</h4>';
        echo '<p><a href="tel:+380501234567">+38 (050) 123-45-67</a><br>';
        echo '<a href="tel:+380441234567">+38 (044) 123-45-67</a></p>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="contact-item">';
        echo '<div class="contact-icon">✉️</div>';
        echo '<div class="contact-content">';
        echo '<h4>' . $this->t('email') . '</h4>';
        echo '<p><a href="mailto:info@handmade-shop.com">info@handmade-shop.com</a><br>';
        echo '<a href="mailto:orders@handmade-shop.com">orders@handmade-shop.com</a></p>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="contact-item">';
        echo '<div class="contact-icon">🕒</div>';
        echo '<div class="contact-content">';
        echo '<h4>' . $this->t('working_hours') . '</h4>';
        echo '<p>' . $this->t('schedule') . '</p>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
        echo '</div>';
    }
}

$translations = include "lang/{$language}.php";
$contactPage = new ContactPage($translations['contact'], $language);
$contactPage->render();
?>