-- Restaurant Database Schema
-- MariaDB/MySQL

CREATE DATABASE restaurant_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'restouser'@'localhost' IDENTIFIED BY 'fjq2ElLyOvqMvQdF';
GRANT ALL PRIVILEGES ON restaurant_db.* TO 'restouser'@'localhost';
FLUSH PRIVILEGES;

CREATE DATABASE IF NOT EXISTS restaurant_db;
USE restaurant_db;

CREATE TABLE `user` (
  `user_id` integer PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(100) UNIQUE NOT NULL,
  `login` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `category` (
  `category_id` integer PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `image` varchar(200),
  `order` integer NOT NULL,
  `status` integer NOT NULL,
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `product` (
  `product_id` integer PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `image` varchar(200) NOT NULL,
  `category_id` integer NOT NULL,
  `price` double NOT NULL DEFAULT 0,
  `status` integer NOT NULL DEFAULT 1,
  `order` integer NOT NULL,
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `promotion` (
  `promotion_id` integer PRIMARY KEY AUTO_INCREMENT,
  `promo_code` varchar(50) NOT NULL,
  `discount` double NOT NULL,
  `description` text NOT NULL,
  `starts_at` datetime,
  `ends_at` datetime,
  `status` integer NOT NULL DEFAULT 1,
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `purchase_order` (
  `order_id` integer PRIMARY KEY AUTO_INCREMENT,
  `client_id` integer NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_address` varchar(255) NOT NULL,
  `client_phone` varchar(50) NOT NULL,
  `promo_code` varchar(50),
  `promotion_id` integer,
  `order_date` datetime NOT NULL,
  `subtotal` double NOT NULL,
  `discount_percent` double NOT NULL DEFAULT 0,
  `total_amount` double NOT NULL,
  `notes` text,
  `created_at` timestamp DEFAULT (now())
);

CREATE TABLE `purchar_order_line` (
  `order_id` integer,
  `line_number` integer,
  `product_id` integer NOT NULL,
  `price` double NOT NULL,
  `quantity` integer NOT NULL DEFAULT 1,
  PRIMARY KEY (`order_id`, `line_number`)
);

CREATE TABLE `log` (
  `log_id` integer PRIMARY KEY AUTO_INCREMENT,
  `user_id` integer NOT NULL,
  `type` integer NOT NULL DEFAULT 0,
  `data` integer NOT NULL,
  `operation` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT (now())
);

ALTER TABLE `product` ADD FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

ALTER TABLE `purchase_order` ADD FOREIGN KEY (`client_id`) REFERENCES `user` (`user_id`);

ALTER TABLE `purchar_order_line` ADD FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`order_id`);

ALTER TABLE `purchar_order_line` ADD FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

ALTER TABLE `purchase_order` ADD FOREIGN KEY (`promotion_id`) REFERENCES `promotion` (`promotion_id`);

ALTER TABLE `log` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

-- Sample Data (Optional)
-- Insert admin user (password: admin123)
INSERT INTO `user` (`name`, `address`, `phone`, `email`, `login`, `password`, `role`) VALUES
('Administrador', 'Admin Address', '123456789', 'admin@restaurant.com', 'admin', '$2y$12$MvXep20EIgl7p5I5xOLC9etxHz74QVMj89TYiOR4UZ8WX6/8KdyIS', 'admin');

-- Insert sample categories
INSERT INTO `category` (`nombre`, `image`, `order`, `status`) VALUES
('Pizzas', 'https://via.placeholder.com/300x200?text=Pizzas', 1, 1),
('Hamburguesas', 'https://via.placeholder.com/300x200?text=Hamburguesas', 2, 1),
('Bebidas', 'https://via.placeholder.com/300x200?text=Bebidas', 3, 1);

-- Insert sample products
INSERT INTO `product` (`nombre`, `image`, `category_id`, `price`, `status`, `order`) VALUES
('Pizza Margherita', 'https://via.placeholder.com/300x200?text=Pizza+Margherita', 1, 12.99, 1, 1),
('Pizza Pepperoni', 'https://via.placeholder.com/300x200?text=Pizza+Pepperoni', 1, 14.99, 1, 2),
('Hamburguesa Clásica', 'https://via.placeholder.com/300x200?text=Hamburguesa', 2, 8.99, 1, 1),
('Hamburguesa con Queso', 'https://via.placeholder.com/300x200?text=Hamburguesa+Queso', 2, 9.99, 1, 2),
('Coca Cola', 'https://via.placeholder.com/300x200?text=Coca+Cola', 3, 2.50, 1, 1),
('Agua Mineral', 'https://via.placeholder.com/300x200?text=Agua', 3, 1.50, 1, 2);

-- Insert sample promotion
INSERT INTO `promotion` (`promo_code`, `discount`, `description`, `status`) VALUES
('WELCOME10', 10, 'Descuento de bienvenida del 10%', 1);

