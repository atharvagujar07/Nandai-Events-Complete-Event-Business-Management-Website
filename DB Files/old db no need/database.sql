CREATE DATABASE IF NOT EXISTS invoiceapp
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

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

INSERT IGNORE INTO app_settings
  (id, logo_path, theme_name, primary_color, secondary_color, accent_color, soft_color, page_bg_color, font_color, popup_color, button_color)
VALUES
  (1, 'assets/images/nandai-events-logo.png', 'custom', '#7a5638', '#24170f', '#b8915d', '#f3e5d3', '#f5efe8', '#261b14', '#f3e5d3', '#7a5638');

CREATE TABLE IF NOT EXISTS invoices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_no VARCHAR(40) NOT NULL UNIQUE,
  gst_no VARCHAR(40) NULL,
  invoice_date DATE NOT NULL,
  sender_name VARCHAR(160) NOT NULL,
  sender_tagline VARCHAR(255) NULL,
  sender_phone VARCHAR(80) NULL,
  sender_email VARCHAR(160) NULL,
  invoice_to VARCHAR(160) NOT NULL,
  client_email VARCHAR(160) NULL,
  client_phone VARCHAR(80) NULL,
  venue_address VARCHAR(255) NULL,
  event_name VARCHAR(160) NULL,
  account_no VARCHAR(80) NULL,
  account_name VARCHAR(160) NULL,
  bank_name VARCHAR(160) NULL,
  ifsc_code VARCHAR(40) NULL,
  branch_address VARCHAR(180) NULL,
  signature_path VARCHAR(255) NULL,
  notes TEXT NULL,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  advance DECIMAL(12,2) NOT NULL DEFAULT 0,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  balance DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS invoice_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT UNSIGNED NOT NULL,
  description VARCHAR(255) NOT NULL,
  quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  line_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  CONSTRAINT fk_invoice_items_invoice
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS invoice_email_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT UNSIGNED NOT NULL,
  sender_email VARCHAR(160) NULL,
  client_email VARCHAR(160) NOT NULL,
  email_subject VARCHAR(255) NOT NULL,
  email_body TEXT NOT NULL,
  status ENUM('sent','failed') NOT NULL DEFAULT 'failed',
  error_message TEXT NULL,
  sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_invoice_email_logs_invoice
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)
    ON DELETE CASCADE
);

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
