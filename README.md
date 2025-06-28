# 🎨 Інтернет-магазин виробів ручної роботи

Повнофункціональний веб-проект інтернет-магазину з сучасною архітектурою та всіма необхідними функціями для продажу виробів ручної роботи.

## 📋 Функціональність

### ✅ Основні вимоги (за чек-листом)
- **Структура сайту**: Головна, товари, кошик з загальними блоками (header, body, footer)
- **Функціональність товарів**: Список з зображеннями, описом, ціною, вибором кількості
- **Робота з сесією**: Збереження кошика через `$_SESSION`
- **PHP класи**: Page, ProductPage, CartPage, Product з усіма методами
- **POST-форми**: Всі дії використовують POST-методи
- **Додатково**: Зворотний зв'язок, уніфіковане оформлення, фільтри

### 🚀 Розширений функціонал
- 🔍 **Пошук товарів** з live-пошуком та фільтрами
- ❤️ **Система обраного** з AJAX функціональністю
- 🛒 **Оформлення замовлення** з різними способами оплати/доставки
- 🌐 **Багатомовність** (українська та англійська)
- 🛡️ **Система безпеки** (CSRF, XSS захист, Rate Limiting)
- 📱 **Адаптивний дизайн** для всіх пристроїв
- 🎨 **Сучасний UI** з анімаціями та ефектами
- 📊 **Відстеження відвідувань** та логування
- 🔐 **Шифрування даних** та захищені сесії
- 📧 **Форма зворотного зв'язку** з валідацією
- ⚡ **AJAX функціональність** без перезавантаження сторінок

## 🏗️ Архітектура

### Backend
- **PHP 8.0+** з об'єктно-орієнтованим програмуванням
- **MySQL 8.0** база даних з оптимізованими індексами
- **PDO** для безпечної роботи з БД
- **Паттерн Singleton** для Database класу
- **MVC структура** з розділенням логіки

### Frontend
- **Сучасний JavaScript** (ES6+) з класами
- **CSS Grid/Flexbox** для адаптивної верстки
- **AJAX** для асинхронних запитів
- **Progressive Enhancement** підхід

## 📁 Повна структура проекту (26 файлів)

```
handmade-shop/
├── index.php                    # Головна сторінка з рекомендованими товарами
├── products.php                 # Каталог товарів з фільтрами
├── cart.php                     # Кошик покупок
├── checkout.php                 # Оформлення замовлення
├── favorites.php                # Сторінка обраних товарів
├── contact.php                  # Форма зворотного зв'язку
├── search.php                   # Розширений пошук товарів
├── .htaccess                    # Конфігурація Apache сервера
├── README.md                    # Документація проекту
├── classes/                     # PHP класи
│   ├── Page.php                # Базовий клас сторінки
│   ├── ProductPage.php         # Клас сторінки товарів
│   ├── CartPage.php            # Клас сторінки кошика
│   ├── Product.php             # Клас товару з методами
│   ├── Database.php            # Singleton клас БД
│   ├── Session.php             # Клас роботи з сесіями
│   └── Security.php            # Клас безпеки
├── config/                      # Конфігураційні файли
│   ├── database.php            # Налаштування підключення БД
│   └── config.php              # Загальні константи
├── lang/                        # Мовні файли
│   ├── uk.php                  # Українська локалізація
│   └── en.php                  # Англійська локалізація
├── assets/                      # Статичні ресурси
│   ├── css/
│   │   ├── style.css           # Основні стилі
│   │   └── responsive.css      # Адаптивні стилі
│   ├── js/
│   │   └── main.js             # JavaScript функціональність
│   └── images/
│       └── products/           # Зображення товарів
├── ajax/                        # AJAX обробники
│   ├── add_to_cart.php         # Додавання товару в кошик
│   ├── add_to_favorites.php    # Додавання в обране
│   ├── remove_from_cart.php    # Видалення з кошика
│   ├── update_quantity.php     # Оновлення кількості
│   ├── get_cart_count.php      # Отримання кількості в кошику
│   ├── get_favorites_count.php # Лічильник обраних товарів
│   ├── live_search.php         # Живий пошук
│   └── change_language.php     # Зміна мови інтерфейсу
├── includes/                    # Include файли
│   ├── header.php              # Заголовок сайту
│   ├── footer.php              # Підвал сайту
│   ├── navigation.php          # Головне меню
│   └── functions.php           # Допоміжні функції
├── sql/
│   └── database.sql            # SQL схема та тестові дані
└── logs/                       # Логи (створюється автоматично)
```

## 🚀 Встановлення та запуск

### 1. Системні вимоги
- **PHP**: 7.4+ (рекомендовано 8.0+)
- **MySQL**: 5.7+ або MariaDB 10.2+
- **Веб-сервер**: Apache 2.4+ або Nginx 1.18+
- **Розширення PHP**: PDO, PDO_MySQL, OpenSSL, MBString, GD

### 2. Покрокова інструкція

```bash
# 1. Завантаження проекту
git clone https://github.com/your-repo/handmade-shop.git
cd handmade-shop

# 2. Створення бази даних
mysql -u root -p
CREATE DATABASE handmade_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit

# 3. Імпорт структури та даних
mysql -u root -p handmade_shop < sql/database.sql

# 4. Налаштування конфігурації
nano config/database.php
# Вкажіть ваші дані для підключення до БД

# 5. Встановлення прав доступу
chmod 755 assets/images/products
chmod 644 config/*.php
mkdir logs && chmod 755 logs
```

### 3. Конфігурація

**config/database.php:**
```php
<?php
return [
    'host' => 'localhost',
    'database' => 'handmade_shop',
    'username' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4'
];
```

**config/config.php:**
```php
<?php
define('SITE_URL', 'https://yourdomain.com');
define('ADMIN_EMAIL', 'admin@yourdomain.com');
define('ENCRYPTION_KEY', 'your-unique-secret-key-here');
```

### 4. Запуск

1. Розмістіть файли на веб-сервері
2. Налаштуйте віртуальний хост
3. Відкрийте `https://yourdomain.com/index.php`

## 🛡️ Безпека

### Реалізовані заходи:
- **CSRF захист** - унікальні токени для всіх форм
- **XSS захист** - екранування всього виводу
- **SQL Injection** - тільки підготовлені PDO запити
- **Rate Limiting** - обмеження кількості запитів на IP
- **Secure Headers** - HTTP заголовки безпеки
- **Session Security** - захищені налаштування сесій
- **Input Validation** - валідація всіх користувацьких даних
- **File Upload Security** - обмеження типів та розмірів файлів

### Рекомендації для продакшн:
- Використовуйте HTTPS (SSL сертифікат)
- Налаштуйте файрвол на сервері
- Регулярно робіть бекапи БД
- Моніторьте логи безпеки
- Оновлюйте PHP та MySQL

## 🎨 Кастомізація

### Зміна кольорової схеми
У файлі `assets/css/style.css`:
```css
:root {
    --primary-color: #8B4513;    /* Основний колір */
    --secondary-color: #D2691E;  /* Вторинний колір */
    --accent-color: #F4A460;     /* Акцентний колір */
    --text-color: #333;          /* Колір тексту */
    --background: #FFF;          /* Фон */
}
```

### Додавання нової сторінки
```php
<?php
// new-page.php
require_once 'includes/functions.php';
require_once 'classes/Page.php';

class NewPage extends Page {
    protected function renderBody() {
        echo '<main class="container">';
        echo '<h1>Нова сторінка</h1>';
        echo '<p>Контент сторінки</p>';
        echo '</main>';
    }
}

$page = new NewPage('Назва сторінки');
$page->render();
?>
```

### Додавання нової мови
1. Створіть файл `lang/de.php` (наприклад, для німецької)
2. Додайте мову в `header.php`
3. Оновіть `change_language.php`

## 📊 База даних

### Основні таблиці:
- **products** - товари магазину
- **categories** - категорії товарів
- **orders** - замовлення клієнтів
- **order_items** - позиції в замовленнях
- **page_visits** - статистика відвідувань
- **contact_messages** - повідомлення зворотного зв'язку
- **users** - користувачі (для майбутнього розширення)

### Оптимізація продуктивності:
```sql
-- Корисні індекси
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_orders_date ON orders(created_at);
CREATE INDEX idx_visits_page ON page_visits(page_url, visit_time);

-- Очищення старих логів (рекомендується налаштувати cron)
DELETE FROM page_visits WHERE visit_time < DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

## 🚀 Деплойment

### Shared Hosting
1. Завантажте файли через FTP/cPanel File Manager
2. Створіть MySQL базу через cPanel
3. Імпортуйте `database.sql`
4. Налаштуйте `config/database.php`

### VPS/Dedicated Server
```bash
# Nginx конфігурація
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /var/www/handmade-shop;
    index index.php;

    # SSL
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    # PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Static files
    location ~* \.(css|js|png|jpg|jpeg|gif|webp|svg|ico)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## 📈 Оптимізація та SEO

### Продуктивність:
- **Кешування**: Налаштуйте Redis або Memcached
- **CDN**: Використовуйте CloudFlare або Amazon CloudFront
- **Оптимізація зображень**: Конвертація у WebP формат
- **Мінімізація**: CSS/JS compression
- **GZIP**: Compression на рівні сервера

### SEO оптимізація:
- Семантична HTML5 розмітка
- Open Graph та Twitter Card теги
- Schema.org structured data
- Автогенерація sitemap.xml
- Дружні URL (можна розширити)

## 🔧 API та розширення

### Додавання товару програмно:
```php
$product = new Product([
    'name' => 'Керамічна чашка',
    'description' => 'Унікальна чашка ручної роботи',
    'price' => 299.99,
    'category_id' => 1,
    'stock_quantity' => 10,
    'is_featured' => true
]);
// Зберегти через відповідний метод
```

### JavaScript API:
```javascript
// Робота з кошиком
const shop = new HandmadeShop();
await shop.addToCart(productId, quantity);
await shop.updateQuantity(productId, newQuantity);
await shop.removeFromCart(productId);

// Обране
await shop.toggleFavorite(productId);
```

## 🧪 Тестування

### Функціональне тестування:
1. Додавання/видалення товарів з кошика
2. Оформлення замовлення
3. Робота фільтрів та пошуку
4. Зміна мови інтерфейсу
5. Форма зворотного зв'язку

### Тестування безпеки:
1. Спроби CSRF атак
2. XSS інʼєкції
3. SQL інʼєкції
4. Завантаження шкідливих файлів

## 🐛 Вирішення проблем

### Типові помилки:

**Помилка підключення до БД:**
```
PDOException: SQLSTATE[HY000] [2002] Connection refused
```
*Рішення: Перевірте налаштування в `config/database.php`*

**Права доступу:**
```
Permission denied for assets/images/products/
```
*Рішення: `chmod 755 assets/images/products`*

**Session помилки:**
```
Warning: session_start()
```
*Рішення: Перевірте права на папку `/tmp` або налаштуйте `session.save_path`*

## 🤝 Внесок у проект

1. Fork репозиторій
2. Створіть feature branch (`git checkout -b feature/new-feature`)
3. Commit зміни (`git commit -am 'Add new feature'`)
4. Push в branch (`git push origin feature/new-feature`)
5. Створіть Pull Request

### Стандарти коду:
- PSR-12 для PHP
- ESLint для JavaScript
- Semantic commit messages
- Обов'язкове тестування нових функцій

## 📝 Ліцензія

MIT License - дивіться LICENSE файл для повних деталей.

## 📞 Підтримка та контакти

- **Email**: support@handmade-shop.com
- **GitHub Issues**: [Створити тікет](https://github.com/your-repo/handmade-shop/issues)
- **Документація**: [Wiki проекту](https://github.com/your-repo/handmade-shop/wiki)
- **Telegram**: @handmade_shop_support

## 🗓️ Changelog

### v1.0.0 (поточна версія)
- ✅ Базовий функціонал інтернет-магазину
- ✅ Система кошика та оформлення замовлень
- ✅ Багатомовність (українська/англійська)
- ✅ AJAX функціональність
- ✅ Адаптивний дизайн
- ✅ Система безпеки

## 🎯 Roadmap майбутніх версій

### v1.1 (планується)
- [ ] Система рейтингів та відгуків
- [ ] Email сповіщення про замовлення
- [ ] Інтеграція з Nova Poshta API
- [ ] Система знижок та промокодів
- [ ] Експорт замовлень у Excel

### v1.2 (планується)
- [ ] Адмін-панель для управління
- [ ] Система користувачів з реєстрацією
- [ ] Інтеграція з платіжними системами (LiqPay, Fondy)
- [ ] Push-повідомлення
- [ ] Прогресивний веб-додаток (PWA)

### v2.0 (довгострокові плани)
- [ ] Мобільний додаток (React Native)
- [ ] REST API для інтеграцій
- [ ] Система рекомендацій на основі AI
- [ ] Інтеграція з соціальними мережами
- [ ] Мультивендорна платформа

## 🏆 Особливості проекту

### Переваги архітектури:
- **Модульність**: Легко додавати нові функції
- **Безпека**: Комплексний захист від атак
- **Продуктивність**: Оптимізований код та запити
- **Масштабованість**: Готовність до збільшення навантаження
- **Підтримуваність**: Чистий та документований код

### Технічні особливості:
- Використання сучасних PHP практик
- Responsive Design з Mobile First підходом
- Progressive Enhancement для JavaScript
- Semantic HTML5 розмітка
- Accessibility (WCAG 2.1) підтримка

---

**Створено з ❤️ для підтримки українських майстрів ручної роботи**

*Цей проект демонструє повний цикл розробки сучасного веб-додатку з використанням класичних технологій PHP/MySQL та сучасних підходів до фронтенд розробки.*

MIT License - дивіться LICENSE файл для деталей.

## 📞 Підтримка

- Email: support@handmade-shop.com
- GitHub Issues: [створити issue](https://github.com/your-repo/handmade-shop/issues)
- Документація: [docs.handmade-shop.com](https://docs.handmade-shop.com)

## 🎯 Roadmap

### v2.0 (найближчі оновлення):
- [ ] Система користувачів з реєстрацією
- [ ] Адмін-панель для управління
- [ ] Інтеграція з платіжними системами
- [ ] Система знижок та промокодів
- [ ] Відгуки та рейтинги товарів
- [ ] Інтеграція з соціальними мережами
- [ ] Push-сповіщення
- [ ] PWA функціональність

### v2.1:
- [ ] Мобільний додаток
- [ ] API для третіх сторін
- [ ] Система рекомендацій
- [ ] Багаторівневе кешування
- [ ] Аналітика та звіти

---

**Створено з ❤️ для любителів ручної роботи**