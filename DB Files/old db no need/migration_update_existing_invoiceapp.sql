USE invoiceapp;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(120) NOT NULL,
  role ENUM('superadmin','staff') NOT NULL DEFAULT 'staff',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS app_settings (
  id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
  logo_path VARCHAR(255) NULL DEFAULT 'assets/images/nandai-events-logo.png',
  theme_name VARCHAR(40) NOT NULL DEFAULT 'espresso',
  primary_color VARCHAR(20) NOT NULL DEFAULT '#7a5638',
  secondary_color VARCHAR(20) NOT NULL DEFAULT '#24170f',
  accent_color VARCHAR(20) NOT NULL DEFAULT '#b8915d',
  soft_color VARCHAR(20) NOT NULL DEFAULT '#f3e5d3',
  page_bg_color VARCHAR(20) NOT NULL DEFAULT '#f5efe8',
  font_color VARCHAR(20) NOT NULL DEFAULT '#261b14',
  popup_color VARCHAR(20) NOT NULL DEFAULT '#f3e5d3',
  button_color VARCHAR(20) NOT NULL DEFAULT '#7a5638',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS role ENUM('superadmin','staff') NOT NULL DEFAULT 'staff' AFTER display_name;

UPDATE users SET role = 'superadmin' WHERE username = 'admin';

INSERT IGNORE INTO app_settings
  (id, logo_path, theme_name, primary_color, secondary_color, accent_color, soft_color, page_bg_color, font_color, popup_color, button_color)
VALUES
  (1, 'assets/images/nandai-events-logo.png', 'custom', '#7a5638', '#24170f', '#b8915d', '#f3e5d3', '#f5efe8', '#261b14', '#f3e5d3', '#7a5638');

ALTER TABLE app_settings
  ADD COLUMN IF NOT EXISTS page_bg_color VARCHAR(20) NOT NULL DEFAULT '#f5efe8',
  ADD COLUMN IF NOT EXISTS font_color VARCHAR(20) NOT NULL DEFAULT '#261b14',
  ADD COLUMN IF NOT EXISTS popup_color VARCHAR(20) NOT NULL DEFAULT '#f3e5d3',
  ADD COLUMN IF NOT EXISTS button_color VARCHAR(20) NOT NULL DEFAULT '#7a5638';

ALTER TABLE invoices
  ADD COLUMN IF NOT EXISTS gst_no VARCHAR(40) NULL AFTER invoice_no;

ALTER TABLE invoices
  ADD COLUMN IF NOT EXISTS signature_path VARCHAR(255) NULL AFTER branch_address;

CREATE TABLE IF NOT EXISTS invoice_whatsapp_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT UNSIGNED NOT NULL,
  client_phone VARCHAR(40) NOT NULL,
  media_id VARCHAR(120) NULL,
  message_id VARCHAR(160) NULL,
  status ENUM('sent','failed') NOT NULL DEFAULT 'failed',
  error_message TEXT NULL,
  sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_invoice_whatsapp_logs_invoice
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS enquiries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(140) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(160) NULL,
  event_type VARCHAR(120) NULL,
  event_date DATE NULL,
  budget DECIMAL(12,2) NULL,
  message TEXT NULL,
  status ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gallery_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  slug VARCHAR(140) NOT NULL UNIQUE,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gallery_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  title VARCHAR(160) NULL,
  file_path VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NULL,
  mime_type VARCHAR(80) NOT NULL,
  file_size INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_gallery_images_category
    FOREIGN KEY (category_id) REFERENCES gallery_categories(id)
    ON DELETE CASCADE
);
