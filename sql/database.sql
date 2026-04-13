-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    brand VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 5.0,
    reviews INT DEFAULT 0,
    origin VARCHAR(50) DEFAULT 'India',
    description VARCHAR(255),
    long_description TEXT,
    tag VARCHAR(50),
    image_bg_color VARCHAR(20) DEFAULT '#222',
    image_text VARCHAR(100)
);

-- Insert Demo Data (The same data we had in the array)
INSERT INTO products (name, category, brand, price, rating, reviews, description, long_description, tag, image_bg_color, image_text) VALUES 
('Senate House ''26 Hoodie', 'apparel', 'AU Heritage', 1899.00, 4.8, 124, 'Navy Blue // Heavyweight Cotton', 'Official University merchandise. Crafted from 400GSM heavyweight cotton fleece. Features high-density embroidery.', 'BESTSELLER', '#222', '[HOODIE_IMG]'),
('Heritage Graphic Tee', 'apparel', 'AU Basics', 799.00, 4.5, 89, 'Vintage Wash // Archival', 'A tribute to the 1926 founding year. Features screen-printed archival photograph.', '', '#f4f4f4', '[TEE_IMG]'),
('The Vice Chancellor Jacket', 'premium', 'AU Premium', 4999.00, 5.0, 12, 'Leather Sleeves // Exclusive', 'The pinnacle of the collection. Wool body with genuine leather sleeves.', 'EXCLUSIVE', '#002147', '[JACKET_IMG]'),
('Admin Block Tote', 'accessories', 'Eco-AU', 499.00, 4.2, 45, 'Heavy Canvas // Eco-Friendly', 'Durable canvas tote bag suitable for laptops and textbooks.', 'NEW', '#eee', '[TOTE_IMG]');

-- Create Orders Table (Was missing)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2),
    shipping_address TEXT,
    payment_method VARCHAR(50),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Order Items Table (Was missing)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2)
);

ALTER TABLE products ADD image VARCHAR(255);

UPDATE products 
SET image = 'uploads/products/hoodie.avif' 
WHERE id = 1;

UPDATE products 
SET image = 'uploads/products/T-shirt.webp' 
WHERE id = 2;

UPDATE products 
SET image = 'uploads/products/jacket.webp' 
WHERE id = 3;

UPDATE products 
SET image = 'uploads/products/adminBlock.jpg' 
WHERE id = 4;