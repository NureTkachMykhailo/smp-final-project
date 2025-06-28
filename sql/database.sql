-- Create database
CREATE DATABASE handmade_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE handmade_shop;

-- Таблиця категорій
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(255),
    description TEXT,
    description_en TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблиця товарів
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    name_en VARCHAR(255),
    description TEXT,
    description_en TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category_id INT,
    stock_quantity INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category_id),
    INDEX idx_featured (is_featured),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Таблиця замовлень
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    delivery_address TEXT,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('card', 'cash', 'paypal') DEFAULT 'card',
    delivery_method ENUM('pickup', 'courier', 'post') DEFAULT 'courier',
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблиця позицій замовлення
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Таблиця користувачів
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблиця відвідувань
CREATE TABLE page_visits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_url VARCHAR(255) NOT NULL,
    user_ip VARCHAR(45),
    user_agent TEXT,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_page_url (page_url),
    INDEX idx_visit_time (visit_time)
);

-- Таблиця контактних повідомлень
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied') DEFAULT 'new'
);

-- Вставка тестових даних у категорії
INSERT INTO categories (name, name_en, description, description_en) VALUES
('Керамічні вироби', 'Ceramic Products', 'Унікальна кераміка ручної роботи', 'Unique handmade ceramic products'),
('Текстільні вироби', 'Textile Products', 'М''які іграшки та декор з тканини', 'Soft toys and textile decorations'),
('Дерев''яні вироби', 'Wooden Products', 'Різьблення та столярні роботи', 'Carved and wooden handicrafts'),
('Прикраси', 'Jewelry', 'Авторські прикраси з різних матеріалів', 'Handmade jewelry and accessories');

-- Вставка тестових даних у товари
INSERT INTO products (name, name_en, description, description_en, price, image, category_id, stock_quantity, is_featured) VALUES
('Керамічна ваза "Сонячна"', 'Ceramic Vase "Sunny"', 'Унікальна ваза з яскравим орнаментом, виготовлена вручну майстром-керамістом', 'Unique vase with bright ornament, handcrafted by master ceramist. Perfect for flowers or as decorative element.', 1250.00, 'ceramic_vase_1.jpg', 1, 3, TRUE),
('М''яка іграшка "Ведмедик Боб"', 'Soft Toy "Teddy Bear Bob"', 'Плюшевий ведмедик з натуральних матеріалів, гіпоалергенний наповнювач', 'Plush teddy bear made from natural materials, hypoallergenic filling. Great gift for children and adults.', 890.00, 'teddy_bear_1.jpg', 2, 8, FALSE),
('Дерев''яна шкатулка', 'Wooden Jewelry Box', 'Шкатулка з дуба з різьбленим орнаментом для зберігання прикрас', 'Oak jewelry box with carved ornament for storing jewelry and small items. Handcrafted with attention to detail.', 2100.00, 'wooden_box_1.jpg', 3, 2, TRUE),
('Сережки "Осіння мелодія"', 'Autumn Melody Earrings', 'Авторські сережки з натурального каменю та срібла', 'Handmade earrings with natural stones and silver elements. Unique design inspired by autumn colors.', 650.00, 'earrings_1.jpg', 4, 5, FALSE),
('Керамічний набір чашок', 'Ceramic Cup Set', 'Набір з 4 чашок у народному стилі з ручним розписом', 'Set of 4 cups in folk style with hand-painted design. Perfect for tea ceremonies and cozy evenings.', 1800.00, 'ceramic_cups_set.jpg', 1, 6, TRUE),
('Текстильна сумка "Етно"', 'Textile Bag "Etno"', 'Сумка з льняної тканини з вишивкою ручної роботи', 'Linen bag with embroidered pattern, handmade. Eco-friendly and stylish accessory for everyday use.', 750.00, 'textile_bag_1.jpg', 2, 12, FALSE);