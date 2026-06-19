# Codex Prompt and Roadmap

## Ready-to-use Codex Prompt

Build a complete PHP, JavaScript, HTML, CSS, and MySQL invoice generator application based on a modern Nandai Events invoice design. The app should have one clean interface where the user can enter invoice details and generate a professional downloadable invoice PDF.

Core requirements:
- Analyze the uploaded invoice PDF first page and recreate a generalized invoice layout with a purple/white business style.
- The form must allow updating sender/company name, customer/invoice-to name, phone, email, event name, venue address, bank/account details, advance amount, and notes.
- Invoice date should default automatically to today but still be editable.
- Invoice number should be generated automatically and saved.
- Users must be able to add and delete unlimited invoice item rows.
- Each item row must include item description, quantity, unit price, and automatic line total.
- Subtotal, total, advance, and balance must calculate automatically in JavaScript and be verified again in PHP before saving.
- Save each invoice and each invoice item in MySQL.
- Provide a saved invoices panel where old invoices can be opened and downloaded/printed anytime.
- Create a one-page A4 invoice PDF. Do not use `window.print()` for PDF download. Prefer a self-contained PHP PDF generator if Composer is unavailable.
- Add the supplied Nandai Events logo to the invoice header.
- Replace violet/purple with a theme matching the logo colors: espresso, warm gold, champagne, and white.
- Add a sharing module on the invoice view with email and WhatsApp actions for sending the invoice link/PDF download page to the client.
- Add an email API that attaches the generated invoice PDF, sends it to optional `client_email`, and logs sent/failed records in MySQL.
- In `invoices.php`, every invoice row must have Open, Update, Download PDF, and Delete actions.
- Use prepared statements/PDO for database safety.
- Keep the project simple to run on XAMPP/WAMP without Composer.

Additional ideal features:
- Search saved invoices by invoice number, customer, date, or event.
- Edit invoices after saving.
- Add invoice status: draft, sent, paid, partially paid, cancelled.
- Add GST/tax/discount options.
- Add company logo upload and signature/stamp upload.
- Add server-side PDF creation and email attachments without requiring Composer.
- Add WhatsApp Business API delivery for real PDF attachment sending.
- Add customer master and service/item master.
- Add payment history and receipt generation.
- Add export to CSV/Excel.
- Add login for admin and staff users.
- Add responsive design for desktop and mobile.

Deliverables:
- `database.sql`
- `config.php`
- `index.php`
- `create_invoice.php`
- `save_invoice.php`
- `invoices.php`
- `invoice_view.php`
- `assets/css/styles.css`
- `assets/js/app.js`
- `README.md`
- `invoice_pdf_lib.php`
- `send_invoice_email.php`

Acceptance criteria:
- App runs locally on a PHP server.
- Database tables import successfully.
- A user can create an invoice with multiple items.
- Totals update immediately on the form.
- Saved invoice appears in the panel.
- Invoice view visually resembles the purple/white uploaded PDF style.
- Browser print/download produces a clean invoice without form controls.

## Detailed Roadmap

### Phase 1: PDF Analysis and Field Mapping

Identify the invoice sections from the uploaded PDF:
- Header: company/sender name, invoice title, invoice number.
- Client block: invoice-to, phone, venue, event.
- Date block: invoice date.
- Item table: item description and quantity in the original, generalized to description, quantity, unit price, line total.
- Totals block: subtotal, advance, total, balance.
- Bank block: account number, account name, bank name, IFSC, branch.
- Footer: thank-you message.

### Phase 2: Database Design

Create two main tables:
- `invoices` for invoice-level details.
- `invoice_items` for unlimited item rows linked to one invoice.

Recommended future tables:
- `customers`
- `services`
- `payments`
- `users`
- `settings`

### Phase 3: Invoice Form

Build a single form with:
- Auto invoice number.
- Auto current date.
- Sender/company details.
- Customer/event details.
- Dynamic item rows.
- Bank details.
- Notes.
- Live totals.

### Phase 4: Save Logic

In PHP:
- Validate required fields.
- Recalculate totals on the server.
- Save invoice using PDO prepared statements.
- Save all item rows with the invoice ID.
- Redirect to printable invoice view.

### Phase 5: Saved Invoice Panel

Build a panel showing:
- Invoice number.
- Date.
- Customer.
- Event.
- Total.
- Balance.
- Open/download action.

Future enhancement:
- Add search, filters, pagination, status labels, and delete/cancel buttons.

### Phase 6: Printable Invoice

Create a clean invoice page:
- Purple header.
- White body.
- Invoice-to and date blocks.
- Item table.
- Bank details.
- Total summary.
- Thank-you footer.
- Print CSS hiding buttons and navigation.

### Phase 7: Testing

Test:
- Add one item.
- Add many items.
- Delete items.
- Advance less than total.
- Advance equal to total.
- Empty required fields.
- Saved invoice opens correctly.
- PDF downloads directly and has the same visible size, colors, logo, and graphics as the webpage invoice.
- Client email is optional, but if available the app can send the invoice PDF by email and save a log record.
- PDF page has no outside border.

### Phase 8: Production Hardening

Before using for real billing:
- Add authentication.
- Add invoice editing with audit history.
- Add PDF generation library.
- Add backups.
- Add GST/tax compliance fields if needed.
- Add unique numbering rules by financial year.
- Add input length validation and better error messages.
