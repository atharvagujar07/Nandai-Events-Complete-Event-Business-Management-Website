<?php
require __DIR__ . '/auth.php';
require_login();

$today = date('Y-m-d');
$draftInvoiceNo = generate_invoice_number();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice Generator</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body>
  <main class="shell">
    <header class="topbar">
      <div>
        <p class="eyebrow">Nandai Events invoice system</p>
        <h1>Invoice Generator</h1>
      </div>
      <nav class="top-actions">
        <a class="button ghost" href="invoices.php">Saved invoices</a>
        <a class="button ghost" href="all_enquiry.php">Enquiries</a>
        <a class="button ghost" href="category_panel.php">Gallery Admin</a>
        <a class="button ghost" href="sales_report.php">Reports</a>
        <a class="button ghost" href="settings.php">Settings</a>
        <?php if (is_superadmin()): ?><a class="button ghost" href="manage_users.php">Manage Users</a><?php endif; ?>
        <a class="button ghost" style="background-color: #198754; color:white;" href="index.php">Home</a>
     <a class="button ghost" style="background-color: #dc3545; color:white;" href="logout.php">Logout</a>
      </nav>
    </header>

    <form class="panel" action="save_invoice.php" method="post" id="invoiceForm" enctype="multipart/form-data">
      <section class="section-grid">
        <div>
          <h2>Invoice Details</h2>
          <div class="grid two">
            <label>Invoice No
              <input name="invoice_no" value="<?= htmlspecialchars($draftInvoiceNo) ?>" readonly>
            </label>
            <label>GST No Optional
              <input name="gst_no" placeholder="Enter GST number if available">
            </label>
            <label>Invoice Date
              <input type="date" name="invoice_date" value="<?= htmlspecialchars($today) ?>">
            </label>
            <label>Sender / Company Name
              <input name="sender_name" value="Nandai Events" required>
            </label>
            <label>Tagline
              <input name="sender_tagline" value="We turn ideas into action">
            </label>
            <label>Sender Phone
              <input name="sender_phone" value="+91 7719948722 / +91 8446847989">
            </label>
            <label>Sender Email
              <input type="email" name="sender_email" value="nandaievents@gmail.com">
            </label>
          </div>
        </div>

        <div>
          <h2>Bill To</h2>
          <div class="grid two">
             <label>Invoice To
              <input name="invoice_to" value="" placeholder="Sender Name" required>
            </label>
            <label>Client Email
              <input type="email" name="client_email" placeholder="client@example.com">
            </label>
            <label>Client Phone
              <input name="client_phone" value="+91 " placeholder="XXXXXXXXX">
            </label>
            <label>Event Name
              <input name="event_name" placeholder="Event Name">
            </label>
            <label>Venue Address
              <input name="venue_address" value="" placeholder="Your City Name">
            </label>
          </div>
        </div>
      </section>

      <section class="items-head">
        <h2>Items</h2>
        <button class="button small" type="button" id="addItem">Add item</button>
      </section>
      <div class="items-table" id="itemsTable">
        <div class="item-row item-title">
          <span>Item description</span>
          <span>Qty</span>
          <span>Unit price</span>
          <span>Total</span>
          <span></span>
        </div>
       <div class="item-row">
          <input name="items[0][description]" value="Ballon Decor" required>
          <input class="qty" type="number" name="items[0][quantity]" value="2" min="0" step="0.01" required>
          <input class="price" type="number" name="items[0][unit_price]" value="10000" min="0" step="0.01" required>
          <input class="line-total" value="20000.00" readonly>
          <button class="icon-button remove-item" type="button" title="Delete item">×</button>
        </div>
      </div>

      <section class="section-grid">
        <div>
          <h2>Bank Details</h2>
          <div class="grid two">
            <label>Account No
              <input name="account_no" value="38618100000128">
            </label>
            <label>Account Name
              <input name="account_name" value="Kunal Bhanudas More">
            </label>
            <label>Bank Name
              <input name="bank_name" value="Bank of Baroda">
            </label>
            <label>IFSC Code
              <input name="ifsc_code" value="BARB0KOTHRUD">
            </label>
            <label>Branch Address
              <input name="branch_address" value="Kothrud, Pune 411038">
            </label>
            <label>Signature Image
              <input type="file" name="signature_image" accept="image/png,image/jpeg,image/webp,image/gif">
            </label>
          </div>
        </div>

        <aside class="totals">
                <label>Subtotal
            <input id="subtotal" name="subtotal" value="" placeholder="Total Amount"  readonly>
          </label>
          <label>Advance
            <input id="advance" type="number" name="advance" value="" placeholder="Advance Amount" min="0" step="0.01">
          </label>
          <label>Total
            <input id="total" name="total" value=""  placeholder="Grand Total" readonly>
          </label>
          <label>Balance
            <input id="balance" name="balance" value="" placeholder="Balance Amount" readonly>
          </label>
        </aside>
      </section>

      <label>Notes / Thank-you Message
        <textarea name="notes">Thank you for your business.</textarea>
      </label>

      <div class="actions">
        <button class="button" type="submit">Save invoice</button>
        <a class="button ghost" href="invoices.php">Open invoice panel</a>
      </div>
    </form>
  </main>
  <script src="assets/js/app.js"></script>
</body>
</html>
