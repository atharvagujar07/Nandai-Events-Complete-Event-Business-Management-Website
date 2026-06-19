# Purple Invoice Generator

This PHP/MySQL project creates one-page Nandai Events invoices with a logo-matched espresso, gold, champagne, and white theme. It is configured for XAMPP localhost with database name `invoiceapp`.

## PDF Page 1 Analysis

- Visual style: white invoice page with purple header/accent sections.
- Main heading: large `INVOICE`.
- Required blocks: invoice-to name, date, phone, venue address, event name, item descriptions, quantity, subtotal, advance, total, balance, bank/account details, thank-you message.
- Example data found: Creature Events Management, client Vidya Moholkar, date 17 May 2026, event Naming ceremony, venue Bavdhan, bank details, and multiple invoice items.
- Generalized fields in this app: sender/company name, customer name, phones, email, venue, event, account details, dynamic items, quantities, prices, automatic totals, automatic invoice number, automatic date default.

## Features

- Login page protects the invoice system.
- Create invoices from a web form.
- Auto-generate invoice number.
- Auto-fill invoice date with today, with manual change allowed.
- Optional GST number field shown near invoice number.
- Add and delete unlimited invoice items.
- Auto-calculate subtotal, total, advance, and balance.
- Nandai Events logo on the printable invoice.
- One-page A4 print/PDF layout with compact print styling.
- Save invoice and item details in MySQL.
- View saved invoice panel anytime.
- Monthly sales report with date filters, event totals, total sales, received amount, and pending balance.
- Settings page to update logo, background color, font color, popup/message color, button color, login password, and invoice accent colors.
- Uploaded logos are auto-fitted into a fixed invoice/login logo size using `object-fit: contain`.
- Public 3D-style homepage with enquiry form.
- Admin enquiry list with delete action.
- Gallery category management and multiple image upload.
- Public gallery with category filters and 10-image pagination.
- PDF signature fix for JPG, PNG, WebP, and GIF uploads with fallback text.
- Open, update, delete, and download PDF from the saved invoice panel.
- Download PDF using a self-contained PHP PDF generator instead of `window.print()` or Composer.
- Send invoice email with PDF attachment when client email is available.
- Save email send success/failure records in MySQL.
- SMTP settings are stored only in this app's `config.php`; no XAMPP global mail configuration is required.
- Send generated PDF through official Meta WhatsApp Cloud API.
- Signature upload is supported; PNG/WebP is recommended for transparent background in the web invoice.
- Logo-matched espresso, gold, and champagne invoice design.

## Setup

1. Copy this folder to your PHP server folder, for example `C:\xampp\htdocs\invoice-generator`.
2. Create the database by importing `database.sql` into MySQL/phpMyAdmin.
3. Edit `config.php` only if your folder URL or MySQL username/password is different.
4. Open `http://localhost/invoice-generator/index.php`.

Default login:

```text
Username: admin
Password: admin123
```

Change this from `Settings` after first login.

If you already imported the earlier version of the database, run `migration_add_client_email.sql` once instead of recreating all tables.
If that migration says a column already exists, skip that line or import the fresh `database.sql` into a new `invoiceapp` database.
If you see `Unknown column 'signature_path'`, run `migration_update_existing_invoiceapp.sql` once in phpMyAdmin.
For this latest upgrade, run `migration_update_existing_invoiceapp.sql` once to add login/settings/GST/report support to an existing database.
The same migration also adds enquiry and gallery tables.

No Composer is required.

## Files

- `create_invoice.php` - invoice creation form.
- `login.php` / `logout.php` - authentication pages.
- `index.php` - public event-management homepage.
- `submit_enquiry.php` - secure public enquiry form handler.
- `all_enquiry.php` / `delete_enquiry.php` - admin enquiry module.
- `category_panel.php` - admin category and gallery upload panel.
- `save_category.php` / `delete_category.php` - category CRUD.
- `gallery_upload.php` / `delete_gallery_image.php` - image upload and delete.
- `gallery.php` - public gallery frontend.
- `invoice_panel.php` - compatibility redirect to invoice panel.
- `generate_invoice_pdf.php` - compatibility PDF endpoint.
- `style.css` / `script.js` - public website assets.
- `SIGNATURE_PDF_FIX.md` - root cause and PDF-library notes for signature rendering.
- `save_invoice.php` - validates and saves invoice data.
- `invoices.php` - saved invoice panel.
- `sales_report.php` - monthly sales, pending balance, and event report page.
- `settings.php` / `save_settings.php` - app logo, theme, and password settings.
- `invoice_view.php` - printable/downloadable invoice.
- `download_pdf.php` - direct PDF download endpoint.
- `invoice_pdf_lib.php` - self-contained PHP PDF generator used by download and email.
- `send_invoice_email.php` - email API that sends invoice PDF attachment and logs status.
- `send_whatsapp_invoice.php` - WhatsApp Cloud API endpoint that uploads and sends the generated PDF.
- `WHATSAPP_SETUP.md` - setup guide for sending generated PDF through WhatsApp Cloud API.
- `smtp_mailer.php` - no-Composer SMTP client used only by this app.
- `edit_invoice.php` - update existing invoice form.
- `update_invoice.php` - saves invoice edits.
- `delete_invoice.php` - deletes invoices from the panel.
- `database.sql` - MySQL database and tables.
- `migration_add_client_email.sql` - update script for older installs.
- `migration_update_existing_invoiceapp.sql` - adds missing signature/WhatsApp fields to an existing database.
- `XAMPP_EMAIL_SETUP.md` - app-only SMTP setup guide.
- `assets/js/app.js` - item add/delete and total calculations.
- `assets/css/styles.css` - UI and printable invoice design.
- `assets/images/nandai-events-logo.jpg` - invoice logo.
- `CODEX_PROMPT_ROADMAP.md` - detailed prompt and roadmap for building the app in Codex.

## Recommended Next Features

- PHPMailer/SMTP integration for more reliable production email.
- WhatsApp Business API integration for automatic PDF delivery.
- Edit existing invoices.
- Delete/cancel invoices with status tracking.
- Customer master table.
- Item/service master table.
- GST/tax fields.
- Payment status and receipt history.
- Logo upload and signature/stamp upload.
- Role-based login for admin/staff.

## Integration Steps

1. Copy the folder to `C:\xampp\htdocs\invoice-generator`.
2. Import `database.sql` for a fresh install, or run `migration_update_existing_invoiceapp.sql` for an existing database.
3. Make sure these folders are writable by PHP:
   - `uploads/signatures`
   - `uploads/logos`
   - `uploads/category`
4. Open `http://localhost/invoice-generator/index.php` for the public website.
5. Open `http://localhost/invoice-generator/login.php` for admin.

## Testing Steps

1. Submit an enquiry from homepage and confirm it appears in `All Enquiry`.
2. Create categories like Wedding, Birthday, Anniversary.
3. Upload multiple images and confirm files are stored under `uploads/category/category-name/`.
4. Open `gallery.php` and verify fixed-size thumbnails and pagination.
5. Create invoice with JPG, PNG, WebP, and GIF signatures.
6. Download invoice PDF and confirm signature appears or shows `Signature not available`.
7. Test missing signature file by deleting one uploaded file and downloading the PDF again.
