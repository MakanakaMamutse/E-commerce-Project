-- E-commerce Database Creation Script
-- Creates all tables with proper constraints and relationships

-- Drop tables if they exist to avoid errors when recreating
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS users;

-- Create Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(255) NOT NULL,
    registration_date DATETIME NOT NULL
);

-- Create User_roles table
CREATE TABLE user_roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_type ENUM('customer', 'seller', 'admin') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create Categories table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Create Products table
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (seller_id) REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Create Product_images table
CREATE TABLE product_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Create Orders table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    seller_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP,
    order_status ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL,
    shipping_address TEXT NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES users(user_id),
    FOREIGN KEY (seller_id) REFERENCES users(user_id)
);

-- Create Order_items table
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Create Payments table
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP,
    payment_method ENUM('credit', 'debit', 'eft') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- Add indexes for better performance
CREATE INDEX idx_products_seller ON products(seller_id);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_orders_customer ON orders(customer_id);
CREATE INDEX idx_orders_seller ON orders(seller_id);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_payments_order ON payments(order_id);

























Dataset 1:
-- Mock Data for Football Shop
-- Reset auto-increment counters before inserting data
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE user_roles AUTO_INCREMENT = 1;
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;
ALTER TABLE product_images AUTO_INCREMENT = 1;

-- Insert mock users
INSERT INTO users (email, username, password, phone_number, registration_date) VALUES
('admin@footballshop.com', 'admin', '1234', '+44123456789', '2025-01-01 10:00:00'),
('seller@footballshop.com', 'seller', '1234', '+44987654321', '2025-01-15 14:30:00'),
('customer@example.com', 'customer', '1234', '+44555666777', '2025-02-01 09:15:00');

-- Assign roles to users
INSERT INTO user_roles (user_id, role_type) VALUES
(1, 'admin'),
(1, 'seller'),
(2, 'seller'),
(3, 'customer');

-- Create product categories
INSERT INTO categories (category_name, description) VALUES
('Club Shirts', 'Official football shirts from clubs around the world'),
('National Team Shirts', 'Official national team football shirts'),
('Footballs', 'Professional match and training footballs'),
('Shin Pads', 'Protection gear for football players'),
('Football Boots', 'Professional football boots for all surfaces');

-- Insert products - Club Shirts
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 1, 'Manchester City Home Shirt 2024/25', 'Official Manchester City home shirt for the 2024/25 season. 100% polyester with moisture-wicking technology.', 89.99),
(1, 1, 'Manchester United Home Shirt 2024/25', 'Official Manchester United home shirt for the 2024/25 season. Features club crest and sponsor logo.', 89.99),
(1, 1, 'Liverpool FC Home Shirt 2024/25', 'Official Liverpool FC home shirt for the 2024/25 season in classic red. Features embroidered club crest.', 84.99),
(1, 1, 'Chelsea FC Home Shirt 2024/25', 'Official Chelsea home shirt for the 2024/25 season in royal blue. Lightweight breathable fabric.', 84.99),
(1, 1, 'Arsenal Home Shirt 2024/25', 'Official Arsenal home shirt for the 2024/25 season. Classic red with white sleeves design.', 84.99),
(1, 1, 'PSG Home Shirt 2024/25', 'Official Paris Saint-Germain home shirt for the 2024/25 season. Features club crest and sponsor logos.', 94.99),
(1, 1, 'Real Madrid Home Shirt 2024/25', 'Official Real Madrid home shirt for the 2024/25 season in classic white. Embroidered club crest.', 94.99),
(1, 1, 'Barcelona Home Shirt 2024/25', 'Official FC Barcelona home shirt for the 2024/25 season featuring the traditional blue and burgundy stripes.', 94.99),
(1, 1, 'Bayern Munich Home Shirt 2024/25', 'Official Bayern Munich home shirt for the 2024/25 season in classic red. Features embroidered club crest.', 89.99),
(2, 1, 'Juventus Home Shirt 2024/25', 'Official Juventus home shirt for the 2024/25 season featuring the iconic black and white stripes.', 89.99);

-- Insert products - National Team Shirts
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 2, 'England Home Shirt 2024', 'Official England home shirt as worn during Euro 2024. Features embroidered Three Lions crest.', 79.99),
(1, 2, 'Brazil Home Shirt 2024', 'Official Brazil home shirt in the iconic yellow with green trim. Breathable fabric perfect for hot weather.', 84.99),
(1, 2, 'France Home Shirt 2024', 'Official France home shirt in classic blue. Features embroidered FFF crest and star above the badge.', 84.99),
(1, 2, 'Germany Home Shirt 2024', 'Official Germany home shirt in traditional white with black details. Features embroidered DFB crest.', 79.99),
(2, 2, 'Argentina Home Shirt 2024', 'Official Argentina home shirt featuring the classic blue and white stripes. World champions badge included.', 84.99),
(2, 2, 'Spain Home Shirt 2024', 'Official Spain home shirt in traditional red. Features embroidered RFEF crest.', 79.99);

-- Insert products - Footballs
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 3, 'Premier League Official Match Ball 2024/25', 'The official Premier League match ball for the 2024/25 season. FIFA Quality Pro certified.', 124.99),
(1, 3, 'Champions League Official Ball 2024/25', 'The official UEFA Champions League match ball for the 2024/25 season. Professional quality.', 129.99),
(1, 3, 'Training Football - Size 5', 'Professional training football suitable for all surfaces. Durable construction with excellent touch.', 24.99),
(2, 3, 'Indoor Football - Size 4', 'Specially designed football for indoor play and futsal. Lower bounce and enhanced control.', 19.99);

-- Insert products - Shin Pads
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 4, 'Professional Shin Guards - Large', 'Professional-grade shin guards offering maximum protection with lightweight design. Includes ankle support.', 34.99),
(1, 4, 'Junior Shin Guards - Small', 'Lightweight shin guards designed specifically for junior players. Comfortable fit with secure straps.', 19.99),
(2, 4, 'Slip-In Shin Pads - Medium', 'Minimalist slip-in shin pads offering basic protection with maximum comfort. Ideal for amateur players.', 14.99);

-- Insert products - Football Boots
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 5, 'Pro Speed Football Boots - Firm Ground', 'Lightweight football boots designed for speed on firm ground surfaces. Available in various sizes.', 119.99),
(1, 5, 'Control Master Football Boots - Soft Ground', 'Football boots optimized for ball control on soft ground. Replaceable studs included.', 129.99),
(2, 5, 'Academy Football Boots - Artificial Grass', 'Affordable football boots perfect for artificial surfaces. Durable construction with good grip.', 59.99);

-- Insert product images - Club Shirts (one image per product)
INSERT INTO product_images (product_id, image_url) VALUES
(1, 'images/club-shirts/man-city-home-2024.jpg'),
(2, 'images/club-shirts/man-united-home-2024.jpg'),
(3, 'images/club-shirts/liverpool-home-2024.jpg'),
(4, 'images/club-shirts/chelsea-home-2024.jpg'),
(5, 'images/club-shirts/arsenal-home-2024.jpg'),
(6, 'images/club-shirts/psg-home-2024.jpg'),
(7, 'images/club-shirts/real-madrid-home-2024.jpg'),
(8, 'images/club-shirts/barcelona-home-2024.jpg'),
(9, 'images/club-shirts/bayern-home-2024.jpg'),
(10, 'images/club-shirts/juventus-home-2024.jpg');

-- Insert product images - National Team Shirts (one image per product)
INSERT INTO product_images (product_id, image_url) VALUES
(11, 'images/national-shirts/england-home-2024.jpg'),
(12, 'images/national-shirts/brazil-home-2024.jpg'),
(13, 'images/national-shirts/france-home-2024.jpg'),
(14, 'images/national-shirts/germany-home-2024.jpg'),
(15, 'images/national-shirts/argentina-home-2024.jpg'),
(16, 'images/national-shirts/spain-home-2024.jpg');

-- Insert product images - Footballs
INSERT INTO product_images (product_id, image_url) VALUES
(17, 'images/footballs/premier-league-ball-2024.jpg'),
(18, 'images/footballs/champions-league-ball-2024.jpg'),
(19, 'images/footballs/training-ball.jpg'),
(20, 'images/footballs/indoor-ball.jpg');

-- Insert product images - Shin Pads
INSERT INTO product_images (product_id, image_url) VALUES
(21, 'images/shin-pads/pro-large.jpg'),
(22, 'images/shin-pads/junior-small.jpg'),
(23, 'images/shin-pads/slip-in-medium.jpg');

-- Insert product images - Football Boots (one image per product)
INSERT INTO product_images (product_id, image_url) VALUES
(24, 'images/boots/pro-speed-fg.jpg'),
(25, 'images/boots/control-master-sg.jpg'),
(26, 'images/boots/academy-ag.jpg');
















Added Data 1:
-- Insert additional football boots (16 new ones)
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
-- Nike Boots
(1, 5, 'Nike Mercurial Vapor 15 Elite', 'Lightweight Nike boots designed for speed with enhanced touch and control. Features innovative NikeSkin upper and Vapor traction pattern.', 249.99),
(1, 5, 'Nike Mercurial Superfly 9', 'Premium Nike boots with Dynamic Fit collar for ankle support. Aerotrak zone in forefoot helps provide traction for explosive acceleration.', 274.99),
(1, 5, 'Nike Tiempo Legend 10 Elite', 'Premium kangaroo leather boots offering exceptional touch and comfort. Perfect for players who prioritize ball control.', 229.99),
(1, 5, 'Nike Phantom GX Elite', 'Innovative Nike boots with textured upper for enhanced ball control. Features off-center lacing for clean strike zone.', 259.99),
(2, 5, 'Nike Phantom Vision 3 Academy', 'Affordable Nike boots with synthetic upper and dynamic fit collar. Great for amateur players looking for professional features.', 89.99),
(2, 5, 'Nike Hypervenom Phantom 3', 'Strike-focused boots with textured upper for power and accuracy. Features flexible soleplate for agility and comfort.', 199.99),

-- Adidas Boots
(1, 5, 'Adidas Predator 24 Elite', 'Revolutionary control boots with Predator Zone technology for enhanced ball manipulation. Features laceless design for clean strike zone.', 279.99),
(1, 5, 'Adidas X Speedportal+', 'Ultra-lightweight Adidas speed boots designed for players who rely on pace. Features supportive Speedframe and Speedskin upper.', 269.99),
(1, 5, 'Adidas Copa Pure+', 'Premium leather Adidas boots offering exceptional touch and comfort. Features touch pods for enhanced ball control in all conditions.', 249.99),
(2, 5, 'Adidas Copa Sense.3', 'Mid-range leather boots with Sensepods for comfort and cushioning. Perfect for players looking for quality on a budget.', 119.99),
(2, 5, 'Adidas Predator Edge.2', 'Control boots with ribbed upper for improved ball spin and accuracy. Features molded heel for secure fit and comfort.', 139.99),

-- Puma Boots
(1, 5, 'Puma Future Ultimate', 'Innovative Puma boots with adaptable FUZIONFIT+ compression band for customizable fit. Features dynamic motion system for agility.', 239.99),
(1, 5, 'Puma Ultra Ultimate', 'Ultra-lightweight Puma speed boots weighing just 175g. Features ULTRAWEAVE upper for minimal weight with maximum durability.', 229.99),
(2, 5, 'Puma King Ultimate', 'Modern version of the classic Puma King with premium K-leather upper. Offers exceptional comfort and touch on the ball.', 199.99),
(2, 5, 'Puma Future Z 3.4', 'Affordable version of Puma''s Future silo with adaptive FitZone for comfortable fit. Ideal for amateur players on artificial surfaces.', 89.99),
(2, 5, 'Puma Ultra 3.4', 'Budget-friendly speed boots with lightweight synthetic upper. Features molded studs optimized for firm ground surfaces.', 79.99);

-- Insert additional products - Gear (replaces Shin Pads)
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 4, 'Elite Goalkeeper Gloves', 'Professional goalkeeper gloves with 4mm latex palm for excellent grip in all weather conditions. Features finger protection system.', 79.99),
(1, 4, 'Junior Goalkeeper Gloves', 'Goalkeeper gloves designed specifically for junior players. Offers good grip and protection at an affordable price.', 39.99),
(1, 4, 'Nike Mercurial Lite Shin Guards', 'Lightweight shin guards from Nike offering excellent protection with minimal weight. Features secure strap system.', 24.99),
(1, 4, 'Adidas X Pro Shin Guards', 'Professional shin guards from Adidas with hard shield and EVA backing for maximum protection and comfort.', 29.99),
(2, 4, 'Puma Future Shin Guards', 'Durable shin guards with flexible shell that adapts to your leg shape for optimal comfort and protection.', 22.99),
(2, 4, 'Football Grip Socks', 'Anti-slip socks with special grip zones to prevent foot movement inside boots. Enhances stability and reduces blisters.', 19.99),
(1, 4, 'Protective Ankle Guards', 'Lightweight ankle protectors that help prevent injuries during tackles and provide support for weak ankles.', 14.99);

-- Insert additional footballs
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 3, 'Nike Flight Premier League Official Ball 2024/25', 'The official Nike Flight ball for the Premier League 2024/25 season with AerowSculpt technology for true flight.', 124.99),
(1, 3, 'Adidas Al Rihla World Cup 2022 Ball', 'The official match ball from the 2022 FIFA World Cup in Qatar. Features Connected Ball Technology.', 139.99),
(1, 3, 'Nike Academy Team Ball', 'Training football with durable construction and high-contrast graphics for enhanced visibility. Suitable for all surfaces.', 24.99),
(1, 3, 'Adidas UEFA Europa League 2024/25 Match Ball', 'The official match ball for the 2024/25 UEFA Europa League. FIFA Quality Pro certified with seamless surface.', 119.99),
(2, 3, 'Puma TeamFINAL 21.1 FIFA Quality Pro Ball', 'Match ball with FIFA Quality Pro certification. Features 12-panel construction for perfect roundness and flight stability.', 99.99),
(2, 3, 'Nike Strike Winter Hi-Vis Ball', 'High-visibility winter ball with special yellow/orange design for low-light conditions. Enhanced with All Conditions Control technology.', 34.99),
(2, 3, 'Adidas Tango Rosario Classic Ball', 'Classic design inspired by the iconic Tango ball. Durable construction perfect for recreational play and collectors.', 49.99),
(1, 3, 'Mitre Delta Max Professional Ball', 'Professional quality ball with hyperseam technology for almost zero water uptake. Used in multiple professional leagues.', 89.99),
(2, 3, 'Puma FINAL 4 Futsal Ball', 'Specially designed low-bounce futsal ball with extra durability for indoor hard court play. Official futsal dimensions and weight.', 44.99);

-- Insert product images for new boots (one image per product)
INSERT INTO product_images (product_id, image_url) VALUES
-- Starting from product_id 27 for new boots
(27, 'images/boots/nike-mercurial-vapor-15.jpg'),
(28, 'images/boots/nike-mercurial-superfly-9.jpg'),
(29, 'images/boots/nike-tiempo-legend-10.jpg'),
(30, 'images/boots/nike-phantom-gx.jpg'),
(31, 'images/boots/nike-phantom-vision-3.jpg'),
(32, 'images/boots/nike-hypervenom-phantom-3.jpg'),
(33, 'images/boots/adidas-predator-24.jpg'),
(34, 'images/boots/adidas-x-speedportal.jpg'),
(35, 'images/boots/adidas-copa-pure.jpg'),
(36, 'images/boots/adidas-copa-sense-3.jpg'),
(37, 'images/boots/adidas-predator-edge-2.jpg'),
(38, 'images/boots/puma-future-ultimate.jpg'),
(39, 'images/boots/puma-ultra-ultimate.jpg'),
(40, 'images/boots/puma-king-ultimate.jpg'),
(41, 'images/boots/puma-future-z-3-4.jpg'),
(42, 'images/boots/puma-ultra-3-4.jpg');

-- Insert product images for new gear products
INSERT INTO product_images (product_id, image_url) VALUES
(43, 'images/gear/elite-goalkeeper-gloves.jpg'),
(44, 'images/gear/junior-goalkeeper-gloves.jpg'),
(45, 'images/gear/nike-mercurial-lite.jpg'),
(46, 'images/gear/adidas-x-pro.jpg'),
(47, 'images/gear/puma-future.jpg'),
(48, 'images/gear/grip-socks.jpg'),
(49, 'images/gear/ankle-guards.jpg');

-- Insert product images for new footballs
INSERT INTO product_images (product_id, image_url) VALUES
(50, 'images/footballs/nike-flight-pl-2024.jpg'),
(51, 'images/footballs/adidas-al-rihla.jpg'),
(52, 'images/footballs/nike-academy-team.jpg'),
(53, 'images/footballs/adidas-europa-league-2024.jpg'),
(54, 'images/footballs/puma-teamfinal-pro.jpg'),
(55, 'images/footballs/nike-strike-winter.jpg'),
(56, 'images/footballs/adidas-tango-rosario.jpg'),
(57, 'images/footballs/mitre-delta-max.jpg'),
(58, 'images/footballs/puma-final-futsal.jpg');

-- Update the existing shin pads images folder path to gear
UPDATE product_images 
SET image_url = REPLACE(image_url, 'images/shin-pads/', 'images/gear/') 
WHERE image_url LIKE '%images/shin-pads/%';



Added more data 2 add db columns to add more complexity:


-- 1. Add full_name column to users table
ALTER TABLE users ADD COLUMN full_name VARCHAR(100);

-- 2. Update existing users with sample full names
-- This creates sample names based on usernames to populate the new column
UPDATE users
SET full_name = CONCAT(
    UPPER(SUBSTRING(username, 1, 1)), 
    LOWER(SUBSTRING(username, 2)), 
    ' ', 
    UPPER(SUBSTRING(REVERSE(username), 1, 1)), 
    LOWER(SUBSTRING(REVERSE(username), 2))
)
WHERE full_name IS NULL;

-- 3. Enhance the orders table with additional columns
ALTER TABLE orders 
ADD COLUMN payment_method VARCHAR(50),
ADD COLUMN payment_id INT,
ADD COLUMN tax_amount DECIMAL(10,2),
ADD COLUMN subtotal DECIMAL(10,2);  



-- Add missing columns to orders table for customer info
ALTER TABLE orders 
ADD COLUMN full_name VARCHAR(100),
ADD COLUMN email VARCHAR(100),
ADD COLUMN phone_number VARCHAR(20),
ADD COLUMN shipping_address VARCHAR(255),
ADD COLUMN city VARCHAR(100),
ADD COLUMN state_province VARCHAR(100),
ADD COLUMN zip_postal_code VARCHAR(20),
ADD COLUMN country VARCHAR(100),
ADD COLUMN payment_method VARCHAR(50),
ADD COLUMN payment_id INT,
ADD COLUMN tax_amount DECIMAL(10,2),
ADD COLUMN subtotal DECIMAL(10,2);

-- Note: If your orders table already has some of these columns (like shipping_address),
-- you would want to skip those in the ALTER TABLE statement above.


-- Add subtotal column to order_items table
ALTER TABLE order_items 
ADD COLUMN subtotal DECIMAL(10,2);





------------------------------------------------------
------------OLD DATA BASECODE ABOVE BEFORE CRASH AND CORRUPTION----------------


------------NEW DATA BASECODE BELOW AFTER CRASH AND CORRUPTION----------------

-- Drop tables if they exist to avoid errors when recreating
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS users;

-- Create Users table (with full_name included from the start)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(255) NOT NULL,
    registration_date DATETIME NOT NULL,
    full_name VARCHAR(100)
);

-- Create User_roles table
CREATE TABLE user_roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_type ENUM('customer', 'seller', 'admin') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create Categories table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Create Products table
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (seller_id) REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Create Product_images table
CREATE TABLE product_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Create Orders table (with all additional columns included)
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    seller_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP,
    order_status ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL,
    shipping_address TEXT NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    phone_number VARCHAR(20),
    city VARCHAR(100),
    state_province VARCHAR(100),
    zip_postal_code VARCHAR(20),
    country VARCHAR(100),
    payment_method VARCHAR(50),
    payment_id INT,
    tax_amount DECIMAL(10,2),
    subtotal DECIMAL(10,2),
    FOREIGN KEY (customer_id) REFERENCES users(user_id),
    FOREIGN KEY (seller_id) REFERENCES users(user_id)
);

-- Create Order_items table (with subtotal column included)
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Create Payments table
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP,
    payment_method ENUM('credit', 'debit', 'eft') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- Add indexes for better performance
CREATE INDEX idx_products_seller ON products(seller_id);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_orders_customer ON orders(customer_id);
CREATE INDEX idx_orders_seller ON orders(seller_id);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_payments_order ON payments(order_id);

-- Reset auto-increment counters
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE user_roles AUTO_INCREMENT = 1;
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;
ALTER TABLE product_images AUTO_INCREMENT = 1;
ALTER TABLE orders AUTO_INCREMENT = 1;
ALTER TABLE order_items AUTO_INCREMENT = 1;
ALTER TABLE payments AUTO_INCREMENT = 1;

-- ========================================
-- INSERT MOCK DATA
-- ========================================

-- Insert mock users with full names
INSERT INTO users (email, username, password, phone_number, registration_date, full_name) VALUES
('admin@footballshop.com', 'admin', '1234', '+44123456789', '2025-01-01 10:00:00', 'Admin User'),
('seller@footballshop.com', 'seller', '1234', '+44987654321', '2025-01-15 14:30:00', 'Shop Seller'),
('customer@example.com', 'customer', '1234', '+44555666777', '2025-02-01 09:15:00', 'John Customer');

-- Assign roles to users
INSERT INTO user_roles (user_id, role_type) VALUES
(1, 'admin'),
(1, 'seller'),
(2, 'seller'),
(3, 'customer');

-- Create product categories
INSERT INTO categories (category_name, description) VALUES
('Club Shirts', 'Official football shirts from clubs around the world'),
('National Team Shirts', 'Official national team football shirts'),
('Footballs', 'Professional match and training footballs'),
('Gear', 'Football gear including shin pads, gloves, and accessories'),
('Football Boots', 'Professional football boots for all surfaces');

-- Insert products - Club Shirts
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 1, 'Manchester City Home Shirt 2024/25', 'Official Manchester City home shirt for the 2024/25 season. 100% polyester with moisture-wicking technology.', 89.99),
(1, 1, 'Manchester United Home Shirt 2024/25', 'Official Manchester United home shirt for the 2024/25 season. Features club crest and sponsor logo.', 89.99),
(1, 1, 'Liverpool FC Home Shirt 2024/25', 'Official Liverpool FC home shirt for the 2024/25 season in classic red. Features embroidered club crest.', 84.99),
(1, 1, 'Chelsea FC Home Shirt 2024/25', 'Official Chelsea home shirt for the 2024/25 season in royal blue. Lightweight breathable fabric.', 84.99),
(1, 1, 'Arsenal Home Shirt 2024/25', 'Official Arsenal home shirt for the 2024/25 season. Classic red with white sleeves design.', 84.99),
(1, 1, 'PSG Home Shirt 2024/25', 'Official Paris Saint-Germain home shirt for the 2024/25 season. Features club crest and sponsor logos.', 94.99),
(1, 1, 'Real Madrid Home Shirt 2024/25', 'Official Real Madrid home shirt for the 2024/25 season in classic white. Embroidered club crest.', 94.99),
(1, 1, 'Barcelona Home Shirt 2024/25', 'Official FC Barcelona home shirt for the 2024/25 season featuring the traditional blue and burgundy stripes.', 94.99),
(1, 1, 'Bayern Munich Home Shirt 2024/25', 'Official Bayern Munich home shirt for the 2024/25 season in classic red. Features embroidered club crest.', 89.99),
(2, 1, 'Juventus Home Shirt 2024/25', 'Official Juventus home shirt for the 2024/25 season featuring the iconic black and white stripes.', 89.99);

-- Insert products - National Team Shirts
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 2, 'England Home Shirt 2024', 'Official England home shirt as worn during Euro 2024. Features embroidered Three Lions crest.', 79.99),
(1, 2, 'Brazil Home Shirt 2024', 'Official Brazil home shirt in the iconic yellow with green trim. Breathable fabric perfect for hot weather.', 84.99),
(1, 2, 'France Home Shirt 2024', 'Official France home shirt in classic blue. Features embroidered FFF crest and star above the badge.', 84.99),
(1, 2, 'Germany Home Shirt 2024', 'Official Germany home shirt in traditional white with black details. Features embroidered DFB crest.', 79.99),
(2, 2, 'Argentina Home Shirt 2024', 'Official Argentina home shirt featuring the classic blue and white stripes. World champions badge included.', 84.99),
(2, 2, 'Spain Home Shirt 2024', 'Official Spain home shirt in traditional red. Features embroidered RFEF crest.', 79.99);

-- Insert products - Footballs
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 3, 'Premier League Official Match Ball 2024/25', 'The official Premier League match ball for the 2024/25 season. FIFA Quality Pro certified.', 124.99),
(1, 3, 'Champions League Official Ball 2024/25', 'The official UEFA Champions League match ball for the 2024/25 season. Professional quality.', 129.99),
(1, 3, 'Training Football - Size 5', 'Professional training football suitable for all surfaces. Durable construction with excellent touch.', 24.99),
(2, 3, 'Indoor Football - Size 4', 'Specially designed football for indoor play and futsal. Lower bounce and enhanced control.', 19.99),
(1, 3, 'Nike Flight Premier League Official Ball 2024/25', 'The official Nike Flight ball for the Premier League 2024/25 season with AerowSculpt technology for true flight.', 124.99),
(1, 3, 'Adidas Al Rihla World Cup 2022 Ball', 'The official match ball from the 2022 FIFA World Cup in Qatar. Features Connected Ball Technology.', 139.99),
(1, 3, 'Nike Academy Team Ball', 'Training football with durable construction and high-contrast graphics for enhanced visibility. Suitable for all surfaces.', 24.99),
(1, 3, 'Adidas UEFA Europa League 2024/25 Match Ball', 'The official match ball for the 2024/25 UEFA Europa League. FIFA Quality Pro certified with seamless surface.', 119.99),
(2, 3, 'Puma TeamFINAL 21.1 FIFA Quality Pro Ball', 'Match ball with FIFA Quality Pro certification. Features 12-panel construction for perfect roundness and flight stability.', 99.99),
(2, 3, 'Nike Strike Winter Hi-Vis Ball', 'High-visibility winter ball with special yellow/orange design for low-light conditions. Enhanced with All Conditions Control technology.', 34.99),
(2, 3, 'Adidas Tango Rosario Classic Ball', 'Classic design inspired by the iconic Tango ball. Durable construction perfect for recreational play and collectors.', 49.99),
(1, 3, 'Mitre Delta Max Professional Ball', 'Professional quality ball with hyperseam technology for almost zero water uptake. Used in multiple professional leagues.', 89.99),
(2, 3, 'Puma FINAL 4 Futsal Ball', 'Specially designed low-bounce futsal ball with extra durability for indoor hard court play. Official futsal dimensions and weight.', 44.99);

-- Insert products - Gear
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 4, 'Professional Shin Guards - Large', 'Professional-grade shin guards offering maximum protection with lightweight design. Includes ankle support.', 34.99),
(1, 4, 'Junior Shin Guards - Small', 'Lightweight shin guards designed specifically for junior players. Comfortable fit with secure straps.', 19.99),
(2, 4, 'Slip-In Shin Pads - Medium', 'Minimalist slip-in shin pads offering basic protection with maximum comfort. Ideal for amateur players.', 14.99),
(1, 4, 'Elite Goalkeeper Gloves', 'Professional goalkeeper gloves with 4mm latex palm for excellent grip in all weather conditions. Features finger protection system.', 79.99),
(1, 4, 'Junior Goalkeeper Gloves', 'Goalkeeper gloves designed specifically for junior players. Offers good grip and protection at an affordable price.', 39.99),
(1, 4, 'Nike Mercurial Lite Shin Guards', 'Lightweight shin guards from Nike offering excellent protection with minimal weight. Features secure strap system.', 24.99),
(1, 4, 'Adidas X Pro Shin Guards', 'Professional shin guards from Adidas with hard shield and EVA backing for maximum protection and comfort.', 29.99),
(2, 4, 'Puma Future Shin Guards', 'Durable shin guards with flexible shell that adapts to your leg shape for optimal comfort and protection.', 22.99),
(2, 4, 'Football Grip Socks', 'Anti-slip socks with special grip zones to prevent foot movement inside boots. Enhances stability and reduces blisters.', 19.99),
(1, 4, 'Protective Ankle Guards', 'Lightweight ankle protectors that help prevent injuries during tackles and provide support for weak ankles.', 14.99);

-- Insert products - Football Boots
INSERT INTO products (seller_id, category_id, product_name, description, price) VALUES
(1, 5, 'Pro Speed Football Boots - Firm Ground', 'Lightweight football boots designed for speed on firm ground surfaces. Available in various sizes.', 119.99),
(1, 5, 'Control Master Football Boots - Soft Ground', 'Football boots optimized for ball control on soft ground. Replaceable studs included.', 129.99),
(2, 5, 'Academy Football Boots - Artificial Grass', 'Affordable football boots perfect for artificial surfaces. Durable construction with good grip.', 59.99),
-- Nike Boots
(1, 5, 'Nike Mercurial Vapor 15 Elite', 'Lightweight Nike boots designed for speed with enhanced touch and control. Features innovative NikeSkin upper and Vapor traction pattern.', 249.99),
(1, 5, 'Nike Mercurial Superfly 9', 'Premium Nike boots with Dynamic Fit collar for ankle support. Aerotrak zone in forefoot helps provide traction for explosive acceleration.', 274.99),
(1, 5, 'Nike Tiempo Legend 10 Elite', 'Premium kangaroo leather boots offering exceptional touch and comfort. Perfect for players who prioritize ball control.', 229.99),
(1, 5, 'Nike Phantom GX Elite', 'Innovative Nike boots with textured upper for enhanced ball control. Features off-center lacing for clean strike zone.', 259.99),
(2, 5, 'Nike Phantom Vision 3 Academy', 'Affordable Nike boots with synthetic upper and dynamic fit collar. Great for amateur players looking for professional features.', 89.99),
(2, 5, 'Nike Hypervenom Phantom 3', 'Strike-focused boots with textured upper for power and accuracy. Features flexible soleplate for agility and comfort.', 199.99),
-- Adidas Boots
(1, 5, 'Adidas Predator 24 Elite', 'Revolutionary control boots with Predator Zone technology for enhanced ball manipulation. Features laceless design for clean strike zone.', 279.99),
(1, 5, 'Adidas X Speedportal+', 'Ultra-lightweight Adidas speed boots designed for players who rely on pace. Features supportive Speedframe and Speedskin upper.', 269.99),
(1, 5, 'Adidas Copa Pure+', 'Premium leather Adidas boots offering exceptional touch and comfort. Features touch pods for enhanced ball control in all conditions.', 249.99),
(2, 5, 'Adidas Copa Sense.3', 'Mid-range leather boots with Sensepods for comfort and cushioning. Perfect for players looking for quality on a budget.', 119.99),
(2, 5, 'Adidas Predator Edge.2', 'Control boots with ribbed upper for improved ball spin and accuracy. Features molded heel for secure fit and comfort.', 139.99),
-- Puma Boots
(1, 5, 'Puma Future Ultimate', 'Innovative Puma boots with adaptable FUZIONFIT+ compression band for customizable fit. Features dynamic motion system for agility.', 239.99),
(1, 5, 'Puma Ultra Ultimate', 'Ultra-lightweight Puma speed boots weighing just 175g. Features ULTRAWEAVE upper for minimal weight with maximum durability.', 229.99),
(2, 5, 'Puma King Ultimate', 'Modern version of the classic Puma King with premium K-leather upper. Offers exceptional comfort and touch on the ball.', 199.99),
(2, 5, 'Puma Future Z 3.4', 'Affordable version of Puma''s Future silo with adaptive FitZone for comfortable fit. Ideal for amateur players on artificial surfaces.', 89.99),
(2, 5, 'Puma Ultra 3.4', 'Budget-friendly speed boots with lightweight synthetic upper. Features molded studs optimized for firm ground surfaces.', 79.99);

-- Insert product images - Club Shirts
INSERT INTO product_images (product_id, image_url) VALUES
(1, 'images/club-shirts/man-city-home-2024.jpg'),
(2, 'images/club-shirts/man-united-home-2024.jpg'),
(3, 'images/club-shirts/liverpool-home-2024.jpg'),
(4, 'images/club-shirts/chelsea-home-2024.jpg'),
(5, 'images/club-shirts/arsenal-home-2024.jpg'),
(6, 'images/club-shirts/psg-home-2024.jpg'),
(7, 'images/club-shirts/real-madrid-home-2024.jpg'),
(8, 'images/club-shirts/barcelona-home-2024.jpg'),
(9, 'images/club-shirts/bayern-home-2024.jpg'),
(10, 'images/club-shirts/juventus-home-2024.jpg');

-- Insert product images - National Team Shirts
INSERT INTO product_images (product_id, image_url) VALUES
(11, 'images/national-shirts/england-home-2024.jpg'),
(12, 'images/national-shirts/brazil-home-2024.jpg'),
(13, 'images/national-shirts/france-home-2024.jpg'),
(14, 'images/national-shirts/germany-home-2024.jpg'),
(15, 'images/national-shirts/argentina-home-2024.jpg'),
(16, 'images/national-shirts/spain-home-2024.jpg');

-- Insert product images - Footballs
INSERT INTO product_images (product_id, image_url) VALUES
(17, 'images/footballs/premier-league-ball-2024.jpg'),
(18, 'images/footballs/champions-league-ball-2024.jpg'),
(19, 'images/footballs/training-ball.jpg'),
(20, 'images/footballs/indoor-ball.jpg'),
(21, 'images/footballs/nike-flight-pl-2024.jpg'),
(22, 'images/footballs/adidas-al-rihla.jpg'),
(23, 'images/footballs/nike-academy-team.jpg'),
(24, 'images/footballs/adidas-europa-league-2024.jpg'),
(25, 'images/footballs/puma-teamfinal-pro.jpg'),
(26, 'images/footballs/nike-strike-winter.jpg'),
(27, 'images/footballs/adidas-tango-rosario.jpg'),
(28, 'images/footballs/mitre-delta-max.jpg'),
(29, 'images/footballs/puma-final-futsal.jpg');

-- Insert product images - Gear
INSERT INTO product_images (product_id, image_url) VALUES
(30, 'images/gear/pro-large.jpg'),
(31, 'images/gear/junior-small.jpg'),
(32, 'images/gear/slip-in-medium.jpg'),
(33, 'images/gear/elite-goalkeeper-gloves.jpg'),
(34, 'images/gear/junior-goalkeeper-gloves.jpg'),
(35, 'images/gear/nike-mercurial-lite.jpg'),
(36, 'images/gear/adidas-x-pro.jpg'),
(37, 'images/gear/puma-future.jpg'),
(38, 'images/gear/grip-socks.jpg'),
(39, 'images/gear/ankle-guards.jpg');

-- Insert product images - Football Boots
INSERT INTO product_images (product_id, image_url) VALUES
(40, 'images/boots/pro-speed-fg.jpg'),
(41, 'images/boots/control-master-sg.jpg'),
(42, 'images/boots/academy-ag.jpg'),
(43, 'images/boots/nike-mercurial-vapor-15.jpg'),
(44, 'images/boots/nike-mercurial-superfly-9.jpg'),
(45, 'images/boots/nike-tiempo-legend-10.jpg'),
(46, 'images/boots/nike-phantom-gx.jpg'),
(47, 'images/boots/nike-phantom-vision-3.jpg'),
(48, 'images/boots/nike-hypervenom-phantom-3.jpg'),
(49, 'images/boots/adidas-predator-24.jpg'),
(50, 'images/boots/adidas-x-speedportal.jpg'),
(51, 'images/boots/adidas-copa-pure.jpg'),
(52, 'images/boots/adidas-copa-sense-3.jpg'),
(53, 'images/boots/adidas-predator-edge-2.jpg'),
(54, 'images/boots/puma-future-ultimate.jpg'),
(55, 'images/boots/puma-ultra-ultimate.jpg'),
(56, 'images/boots/puma-king-ultimate.jpg'),
(57, 'images/boots/puma-future-z-3-4.jpg'),
(58, 'images/boots/puma-ultra-3-4.jpg');





