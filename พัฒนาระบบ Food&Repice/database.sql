CREATE DATABASE IF NOT EXISTS noire_inventory
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE noire_inventory;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  description TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO products (name, category, price, stock, description) VALUES
('Everyday Canvas Tote', 'Accessories', 390.00, 18, 'กระเป๋าผ้าแคนวาสสำหรับใช้งานทุกวัน'),
('Ceramic Studio Cup', 'Home & Living', 250.00, 4, 'แก้วเซรามิกโทนธรรมชาติ'),
('Minimal Desk Lamp', 'Home & Living', 1290.00, 0, 'โคมไฟตั้งโต๊ะดีไซน์มินิมอล'),
('Classic Notebook', 'Stationery', 120.00, 32, 'สมุดปกแข็งกระดาษถนอมสายตา');
