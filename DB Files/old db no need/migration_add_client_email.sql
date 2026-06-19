USE invoiceapp;

ALTER TABLE invoices
  ADD COLUMN client_email VARCHAR(160) NULL AFTER invoice_to;

ALTER TABLE invoices
  ADD COLUMN signature_path VARCHAR(255) NULL AFTER branch_address;

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
