<?php
// includes/footer.php
?>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= $this->t('about_us') ?></h3>
                    <p><?= $this->t('footer_about_text') ?></p>
                    <p><?= $this->t('footer_crafted_with_love') ?></p>
                </div>
                <div class="footer-section">
                    <h3><?= $this->t('navigation') ?></h3>
                    <a href="index.php"><?= $this->t('home') ?></a>
                    <a href="products.php"><?= $this->t('products') ?></a>
                    <a href="favorites.php"><?= $this->t('favorites') ?></a>
                    <a href="cart.php"><?= $this->t('cart') ?></a>
                    <a href="contact.php"><?= $this->t('contact') ?></a>
                </div>
                <div class="footer-section">
                    <h3><?= $this->t('contact_info') ?></h3>
                    <p>📍 <?= $this->t('footer_address') ?></p>
                    <p>📞 <a href="tel:+380501234567">+38 (050) 123-45-67</a></p>
                    <p>✉️ <a href="mailto:info@handmade-shop.com">info@handmade-shop.com</a></p>
                    <p>🕒 <?= $this->t('footer_hours') ?></p>
                </div>
                <div class="footer-section">
                    <h3><?= $this->t('social_media') ?></h3>
                    <a href="#" target="_blank" rel="noopener">📘 Facebook</a>
                    <a href="#" target="_blank" rel="noopener">📷 Instagram</a>
                    <a href="#" target="_blank" rel="noopener">💬 Telegram</a>
                    <a href="#" target="_blank" rel="noopener">🐦 Twitter</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> HandCraft. <?= $this->t('all_rights_reserved') ?></p>
                <p><?= $this->t('footer_made_with_love') ?></p>
            </div>
        </div>
    </footer>
    
    <script src="assets/js/main.js"></script>
    
    <!-- Google Analytics (замініть на свій код) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_TRACKING_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_TRACKING_ID');
    </script>
</body>
</html>