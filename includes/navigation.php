<?php
// includes/navigation.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="main-nav">
    <div class="container">
        <ul class="nav-list">
            <li>
                <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">
                    🏠 <?= $this->t('home') ?>
                </a>
            </li>
            <li>
                <a href="products.php" class="<?= $current_page === 'products.php' ? 'active' : '' ?>">
                    🛍️ <?= $this->t('products') ?>
                </a>
            </li>
            <li>
                <a href="favorites.php" class="<?= $current_page === 'favorites.php' ? 'active' : '' ?>">
                    ❤️ <?= $this->t('favorites') ?>
                </a>
            </li>
            <li>
                <a href="cart.php" class="<?= $current_page === 'cart.php' ? 'active' : '' ?>">
                    🛒 <?= $this->t('cart') ?>
                </a>
            </li>
            <li>
                <a href="contact.php" class="<?= $current_page === 'contact.php' ? 'active' : '' ?>">
                    📞 <?= $this->t('contact') ?>
                </a>
            </li>
        </ul>
    </div>
</nav>