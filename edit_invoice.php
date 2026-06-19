<?php
require __DIR__ . '/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM invoices WHERE id = :id');
$stmt->execute([':id' => $id]);
$invoice = $stmt->fetch();

if (!$invoice) {
    http_response_code(404);
    exit('Invoice not found.');
}

$itemStmt = db()->prepare('SELECT * FROM invoice_items WHERE invoice_id = :id ORDER BY sort_order, id');
$itemStmt->execute([':id' => $id]);
$items = $itemStmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update <?= htmlspecialchars($invoice['invoice_no']) ?></title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body>
  <main class="shell">
    <header class="topbar">
      <div>
        <p class="eyebrow">Update invoice</p>
        <h1><?= htmlspecialchars($invoice['invoice_no']) ?></h1>
      </div>
      <a class="button ghost" href="invoices.php">Saved invoices</a>
    </header>

    <form class="panel" action="update_invoice.php" method="post" id="invoiceForm" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int)$invoice['id'] ?>">
      <section class="section-grid">
        <div>
          <h2>Invoice Details</h2>
          <div class="grid two">
            <label>Invoice No
              <input name="invoice_no" value="<?= htmlspecialchars($invoice['invoice_no']) ?>" readonly>
            </label>
            <label>GST No Optional
              <input name="gst_no" value="<?= htmlspecialchars($invoice['gst_no'] ?? '') ?>">
            </label>
            <label>Invoice Date
              <input type="date" name="invoice_date" value="<?= htmlspecialchars($invoice['invoice_date']) ?>">
            </label>
            <label>Sender / Company Name
              <input name="sender_name" value="<?= htmlspecialchars($invoice['sender_name']) ?>" required>
            </label>
            <label>Tagline
              <input name="sender_tagline" value="<?= htmlspecialchars($invoice['sender_tagline'] ?? '') ?>">
            </label>
            <label>Sender Phone
              <input name="sender_phone" value="<?= htmlspecialchars($invoice['sender_phone'] ?? '') ?>">
            </label>
            <label>Sender Email
              <input type="email" name="sender_email" value="<?= htmlspecialchars($invoice['sender_email'] ?? '') ?>">
            </label>
          </div>
        </div>

        <div>
          <h2>Bill To</h2>
          <div class="grid two">
            <label>Invoice To
              <input name="invoice_to" value="<?= htmlspecialchars($invoice['invoice_to']) ?>" required>
            </label>
            <label>Client Email
              <input type="email" name="client_email" value="<?= htmlspecialchars($invoice['client_email'] ?? '') ?>">
            </label>
            <label>Client Phone
              <input name="client_phone" value="<?= htmlspecialchars($invoice['client_phone'] ?? '') ?>">
            </label>
            <label>Event Name
              <input name="event_name" value="<?= htmlspecialchars($invoice['event_name'] ?? '') ?>">
            </label>
            <label>Venue Address
              <input name="venue_address" value="<?= htmlspecialchars($invoice['venue_address'] ?? '') ?>">
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
        <?php foreach ($items as $index => $item): ?>
          <div class="item-row">
            <input name="items[<?= $index ?>][description]" value="<?= htmlspecialchars($item['description']) ?>" required>
            <input class="qty" type="number" name="items[<?= $index ?>][quantity]" value="<?= htmlspecialchars((string)$item['quantity']) ?>" min="0" step="0.01" required>
            <input class="price" type="number" name="items[<?= $index ?>][unit_price]" value="<?= htmlspecialchars((string)$item['unit_price']) ?>" min="0" step="0.01" required>
            <input class="line-total" value="<?= money_value((float)$item['line_total']) ?>" readonly>
            <button class="icon-button remove-item" type="button" title="Delete item">×</button>
          </div>
        <?php endforeach; ?>
      </div>

      <section class="section-grid">
        <div>
          <h2>Bank Details</h2>
          <div class="grid two">
            <label>Account No
              <input name="account_no" value="<?= htmlspecialchars($invoice['account_no'] ?? '') ?>">
            </label>
            <label>Account Name
              <input name="account_name" value="<?= htmlspecialchars($invoice['account_name'] ?? '') ?>">
            </label>
            <label>Bank Name
              <input name="bank_name" value="<?= htmlspecialchars($invoice['bank_name'] ?? '') ?>">
            </label>
            <label>IFSC Code
              <input name="ifsc_code" value="<?= htmlspecialchars($invoice['ifsc_code'] ?? '') ?>">
            </label>
            <label>Branch Address
              <input name="branch_address" value="<?= htmlspecialchars($invoice['branch_address'] ?? '') ?>">
            </label>
            <label>Signature Image
              <input type="file" name="signature_image" accept="image/png,image/jpeg,image/webp,image/gif">
            </label>
          </div>
          <?php if (!empty($invoice['signature_path'])): ?>
            <div class="signature-preview">
              <img src="<?= htmlspecialchars($invoice['signature_path']) ?>" alt="Current signature">
              <span>Current signature</span>
            </div>
          <?php endif; ?>
        </div>

        <aside class="totals">
          <label>Subtotal
            <input id="subtotal" name="subtotal" value="<?= money_value((float)$invoice['subtotal']) ?>" readonly>
          </label>
          <label>Advance
            <input id="advance" type="number" name="advance" value="<?= htmlspecialchars((string)$invoice['advance']) ?>" min="0" step="0.01">
          </label>
          <label>Total
            <input id="total" name="total" value="<?= money_value((float)$invoice['total']) ?>" readonly>
          </label>
          <label>Balance
            <input id="balance" name="balance" value="<?= money_value((float)$invoice['balance']) ?>" readonly>
          </label>
        </aside>
      </section>

      <label>Notes / Thank-you Message
        <textarea name="notes"><?= htmlspecialchars($invoice['notes'] ?? '') ?></textarea>
      </label>

      <div class="actions">
        <a class="button ghost" href="invoice_view.php?id=<?= (int)$invoice['id'] ?>">Cancel</a>
        <button class="button" type="submit">Update invoice</button>
      </div>
    </form>
  </main>
  <script src="assets/js/app.js"></script>
</body>
</html>
