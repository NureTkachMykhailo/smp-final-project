<?php
// products.php
require_once 'config/config.php';       
require_once 'config/database.php';      
require_once 'includes/functions.php';
require_once 'classes/Page.php';
require_once 'classes/ProductPage.php';
require_once 'classes/Product.php';
require_once 'classes/Database.php';
require_once 'classes/Session.php';
require_once 'classes/Security.php';

Security::setSecurityHeaders();
Session::start();

$language = Session::getLanguage();
$translations = include "lang/{$language}.php";
$productPage = new ProductPage($translations['products'], $language);
$productPage->render();
?>