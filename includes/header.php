<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="<?= $this->language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Security::generateCSRFToken() ?>">
    <title><?= htmlspecialchars($this->title) ?> - <?= $this->t('site_title') ?></title>
    <meta name="description" content="<?= $this->t('meta_description') ?>">
    <meta name="keywords" content="<?= $this->t('meta_keywords') ?>">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Open Graph для соціальних мереж -->
    <meta property="og:title" content="<?= htmlspecialchars($this->title) ?> - <?= $this->t('site_title') ?>">
    <meta property="og:description" content="<?= $this->t('og_description') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= SITE_URL . $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/og-image.jpg">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><a href="index.php" style="color: white; text-decoration: none;">🎨 HandCraft</a></h1>
                </div>
                <div class="header-actions">
                    <a href="search.php" class="search-link" title="<?= $this->t('search') ?>">🔍</a>
                    <a href="favorites.php" class="favorites-icon" title="<?= $this->t('favorites') ?>">
                        ♡
                        <span class="favorites-count" style="display: none;">0</span>
                    </a>
                    <a href="cart.php" class="cart-icon" title="<?= $this->t('cart') ?>">
                        🛒
                        <span class="cart-count" style="display: none;">0</span>
                    </a>
                    <select class="language-switcher" onchange="changeLanguage(this.value)" title="<?= $this->t('language') ?>">
                        <option value="uk" <?= $this->language === 'uk' ? 'selected' : '' ?>>🇺🇦 УК</option>
                        <option value="en" <?= $this->language === 'en' ? 'selected' : '' ?>>🇺🇸 EN</option>
                    </select>
                </div>
            </div>
        </div>
    </header>