-- Database normalization/safety script (optional)
-- Apply on a backup first. Some constraints may fail if data is inconsistent.

SET FOREIGN_KEY_CHECKS = 0;

-- Normalize charset/collation
ALTER TABLE blog CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE categories CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE orders CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE order_detail CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE user_addresses CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE import_history CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE import_receipts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Rename column (optional)
ALTER TABLE orders CHANGE addtional additional VARCHAR(500) NOT NULL DEFAULT 'Ghi chú đặt hàng, ví dụ, thời gian hoặc địa điểm giao hàng chi tiết hơn.';

-- Unique constraints
ALTER TABLE users ADD UNIQUE KEY ux_users_email (email);
ALTER TABLE categories ADD UNIQUE KEY ux_categories_slug (slug);
ALTER TABLE products ADD UNIQUE KEY ux_products_slug (slug);
ALTER TABLE blog ADD UNIQUE KEY ux_blog_slug (slug);

-- Foreign keys (add missing)
ALTER TABLE import_history
  ADD CONSTRAINT fk_import_history_product
    FOREIGN KEY (product_id) REFERENCES products(id),
  ADD CONSTRAINT fk_import_history_admin
    FOREIGN KEY (admin_id) REFERENCES users(id),
  ADD CONSTRAINT fk_import_history_receipt
    FOREIGN KEY (receipt_id) REFERENCES import_receipts(id);

ALTER TABLE import_receipts
  ADD CONSTRAINT fk_import_receipts_admin
    FOREIGN KEY (admin_id) REFERENCES users(id);

ALTER TABLE orders
  ADD CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(id),
  ADD CONSTRAINT fk_orders_address
    FOREIGN KEY (shipping_address_id) REFERENCES user_addresses(id);

ALTER TABLE user_addresses
  ADD CONSTRAINT fk_user_addresses_user
    FOREIGN KEY (user_id) REFERENCES users(id);

SET FOREIGN_KEY_CHECKS = 1;
