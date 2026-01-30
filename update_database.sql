-- ============================================================
-- UPDATE DATABASE FOR ZBOOKS BOOKSTORE
-- Date: January 30, 2026
-- Description: Add 3 missing features required by professor
--   1. Import Stock System with Average Price Formula
--   2. Multiple Shipping Addresses
--   3. Complete Payment Methods (already done in frontend)
-- ============================================================

-- Run this file once in phpMyAdmin to add all new features
-- Database: zbook_db

USE zbook_db;

-- ============================================================
-- PART 1: IMPORT STOCK SYSTEM
-- ============================================================

-- Table: import_history (Lịch sử nhập hàng)
CREATE TABLE IF NOT EXISTS `import_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `quantity_imported` INT(11) NOT NULL,
  `import_price` DECIMAL(10,2) NOT NULL,
  `old_quantity` INT(11) NOT NULL,
  `old_original_price` DECIMAL(10,2) NOT NULL,
  `new_average_price` DECIMAL(10,2) NOT NULL,
  `new_selling_price` DECIMAL(10,2) NOT NULL,
  `profit_margin` DECIMAL(5,2) DEFAULT 0.00,
  `admin_id` INT(11) NOT NULL,
  `note` TEXT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `admin_id` (`admin_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add profit_margin column to products table (if not exists)
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `profit_margin` DECIMAL(5,2) DEFAULT 20.00 COMMENT 'Phần trăm lợi nhuận (%)';

-- Stored Procedure: sp_import_stock
-- Implements professor's formula: New Avg = (Old Qty × Old Price + Import Qty × Import Price) / Total Qty
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_import_stock`$$

CREATE PROCEDURE `sp_import_stock`(
    IN p_product_id INT,
    IN p_quantity_imported INT,
    IN p_import_price DECIMAL(10,2),
    IN p_profit_margin DECIMAL(5,2),
    IN p_admin_id INT,
    IN p_note TEXT
)
BEGIN
    DECLARE v_old_qty INT;
    DECLARE v_old_price DECIMAL(10,2);
    DECLARE v_new_avg_price DECIMAL(10,2);
    DECLARE v_new_selling_price DECIMAL(10,2);
    DECLARE v_new_total_qty INT;
    
    -- Get current product info
    SELECT qty, original_price 
    INTO v_old_qty, v_old_price
    FROM products 
    WHERE id = p_product_id;
    
    -- Calculate new average price (Professor's formula)
    SET v_new_total_qty = v_old_qty + p_quantity_imported;
    SET v_new_avg_price = (v_old_qty * v_old_price + p_quantity_imported * p_import_price) / v_new_total_qty;
    SET v_new_selling_price = v_new_avg_price * (1 + p_profit_margin / 100);
    
    -- Update product
    UPDATE products 
    SET qty = v_new_total_qty,
        original_price = v_new_avg_price,
        selling_price = v_new_selling_price,
        profit_margin = p_profit_margin
    WHERE id = p_product_id;
    
    -- Insert history record
    INSERT INTO import_history (
        product_id, quantity_imported, import_price, 
        old_quantity, old_original_price, 
        new_average_price, new_selling_price, 
        profit_margin, admin_id, note
    ) VALUES (
        p_product_id, p_quantity_imported, p_import_price,
        v_old_qty, v_old_price,
        v_new_avg_price, v_new_selling_price,
        p_profit_margin, p_admin_id, p_note
    );
END$$

DELIMITER ;

-- View: vw_import_history_detail (for easy querying)
CREATE OR REPLACE VIEW `vw_import_history_detail` AS
SELECT 
    ih.id,
    ih.product_id,
    p.name AS product_name,
    ih.quantity_imported,
    ih.import_price,
    ih.old_quantity,
    ih.old_original_price,
    ih.new_average_price,
    ih.new_selling_price,
    ih.profit_margin,
    u.name AS admin_name,
    ih.note,
    ih.created_at
FROM import_history ih
LEFT JOIN products p ON ih.product_id = p.id
LEFT JOIN users u ON ih.admin_id = u.id
ORDER BY ih.created_at DESC;

-- ============================================================
-- PART 2: MULTIPLE SHIPPING ADDRESSES
-- ============================================================

-- Table: user_addresses (Địa chỉ giao hàng của user)
CREATE TABLE IF NOT EXISTS `user_addresses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `address_name` VARCHAR(100) NOT NULL COMMENT 'Tên địa chỉ: Nhà riêng, Văn phòng...',
  `recipient_name` VARCHAR(100) NOT NULL COMMENT 'Tên người nhận',
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL COMMENT 'Địa chỉ chi tiết',
  `district` VARCHAR(100) DEFAULT NULL COMMENT 'Quận/Huyện',
  `city` VARCHAR(100) DEFAULT NULL COMMENT 'Tỉnh/Thành phố',
  `is_default` TINYINT(1) DEFAULT 0 COMMENT '1 = Địa chỉ mặc định',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stored Procedure: sp_set_default_address
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_set_default_address`$$

CREATE PROCEDURE `sp_set_default_address`(
    IN p_user_id INT,
    IN p_address_id INT
)
BEGIN
    -- Remove default from all addresses of this user
    UPDATE user_addresses 
    SET is_default = 0 
    WHERE user_id = p_user_id;
    
    -- Set new default
    UPDATE user_addresses 
    SET is_default = 1 
    WHERE id = p_address_id AND user_id = p_user_id;
END$$

DELIMITER ;

-- Migration: Create default address for existing users
INSERT INTO user_addresses (user_id, address_name, recipient_name, phone, address, is_default)
SELECT 
    id,
    'Địa chỉ mặc định',
    name,
    phone,
    address,
    1
FROM users 
WHERE role_as = 0 
  AND address IS NOT NULL 
  AND address != ''
  AND NOT EXISTS (
      SELECT 1 FROM user_addresses WHERE user_addresses.user_id = users.id
  );

-- ============================================================
-- PART 3: ENHANCE ORDERS TABLE (optional - for future use)
-- ============================================================

-- Add columns to orders table for better tracking
ALTER TABLE `orders` 
ADD COLUMN IF NOT EXISTS `shipping_address_id` INT(11) DEFAULT NULL COMMENT 'Liên kết tới user_addresses',
ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) DEFAULT 'cod' COMMENT 'cod, bacs, online';

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================

-- Check if all tables created successfully
SELECT 'import_history' AS table_name, COUNT(*) AS row_count FROM import_history
UNION ALL
SELECT 'user_addresses', COUNT(*) FROM user_addresses;

-- Check if profit_margin column added to products
SELECT COUNT(*) AS products_with_profit_margin 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'zbook_db' 
  AND TABLE_NAME = 'products' 
  AND COLUMN_NAME = 'profit_margin';

-- Sample query: Test professor's formula
-- Example: Product has 6 books @ 20,000đ, import 10 books @ 15,000đ
-- Expected result: (6×20000 + 10×15000) / 16 = 270,000 / 16 = 16,875đ
SELECT 
    'Formula Test' AS test_name,
    (6 * 20000 + 10 * 15000) / 16 AS expected_result,
    16875 AS professor_example,
    CASE 
        WHEN (6 * 20000 + 10 * 15000) / 16 = 16875 THEN '✓ PASS'
        ELSE '✗ FAIL'
    END AS test_status;

-- ============================================================
-- COMPLETION MESSAGE
-- ============================================================

SELECT 
    '✓ Database update completed successfully!' AS status,
    'Import Stock System: READY' AS feature_1,
    'Multiple Addresses: READY' AS feature_2,
    'Payment Methods: READY (frontend only)' AS feature_3,
    'Next step: Test import with professor example (6×20000 + 10×15000 / 16 = 16,875đ)' AS next_action;
