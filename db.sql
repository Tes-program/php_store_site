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

-- Optional tables for enhanced functionality

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- For admin authentication (simple version)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$QWEJOIxX8xF55hUHI.JVbemVvuMPT31S04iiXZ6t8iSWYAqQjAYhS', 'admin');

-- Insert some sample data
INSERT INTO products (name, description, price, image, category, size, color, stock) VALUES
('Air Sport Runner', 'Comfortable running shoes with air cushion technology', 89.99, 'air_sport.png', 'Running', '42', 'Blue', 15),
('Classic Leather', 'Elegant leather shoes for formal occasions', 129.99, 'classic_leather.png', 'Formal', '43', 'Black', 8),
('Urban Sneakers', 'Stylish sneakers for everyday casual wear', 69.99, 'urban_sneakers.png', 'Casual', '41', 'White', 20);