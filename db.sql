CREATE DATABASE shoe_store;
USE shoe_store;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    size VARCHAR(50),
    color VARCHAR(50),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample data
INSERT INTO products (name, description, price, image, category, size, color, stock) VALUES
('Air Sport Runner', 'Comfortable running shoes with air cushion technology', 89.99, 'air_sport.jpg', 'Running', '42', 'Blue', 15),
('Classic Leather', 'Elegant leather shoes for formal occasions', 129.99, 'classic_leather.jpg', 'Formal', '43', 'Black', 8),
('Urban Sneakers', 'Stylish sneakers for everyday casual wear', 69.99, 'urban_sneakers.jpg', 'Casual', '41', 'White', 20);