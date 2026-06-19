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

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$invoiceUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$emailSubject = rawurlencode('Invoice ' . $invoice['invoice_no'] . ' from ' . $invoice['sender_name']);
$emailBody = rawurlencode(
    "Dear " . $invoice['invoice_to'] . ",\n\nPlease find your invoice here:\n" .
    $invoiceUrl . "\n\nYou can also download the PDF from the invoice page.\n\nThank you,\n" .
    $invoice['sender_name']
);
$clientEmail = trim((string)($invoice['client_email'] ?? ''));
$whatsappPhone = preg_replace('/\D+/', '', (string)($invoice['client_phone'] ?? ''));
$whatsappText = rawurlencode(
    'Hello ' . $invoice['invoice_to'] . ', your invoice ' . $invoice['invoice_no'] .
    ' is ready: ' . $invoiceUrl . ' You can download the PDF from this invoice page.'
);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($invoice['invoice_no']) ?></title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body class="invoice-page">
  <div class="screen-actions">
    <a class="button ghost" href="invoices.php">Back</a>
    <div class="share-actions">
      <form class="inline-form" action="send_invoice_email.php" method="post">
        <input type="hidden" name="id" value="<?= (int)$invoice['id'] ?>">
        <button class="button ghost" type="submit">Send Email</button>
      </form>
      <form class="inline-form" action="send_whatsapp_invoice.php" method="post">
        <input type="hidden" name="id" value="<?= (int)$invoice['id'] ?>">
        <button class="button ghost" type="submit">Send WhatsApp PDF</button>
      </form>
      <a class="button" href="download_pdf.php?id=<?= (int)$invoice['id'] ?>">Download PDF</a>
    </div>
  </div>
  <?php if (!empty($_GET['email_status'])): ?>
    <div class="notice"><?= htmlspecialchars((string)$_GET['email_status']) ?></div>
  <?php endif; ?>

  <article class="invoice-sheet">
    <header class="invoice-hero">
      <div class="logo-lockup">
        <img src="<?= htmlspecialchars(app_logo_path()) ?>" alt="Nandai Events logo">
        <div>
        <p class="brand"><?= htmlspecialchars($invoice['sender_name']) ?></p>
        <p><?= htmlspecialchars($invoice['sender_tagline'] ?? '') ?></p>
        </div>
      </div>
      <div class="invoice-title">
        <h1>INVOICE</h1>
        <p><?= htmlspecialchars($invoice['invoice_no']) ?></p>
        <?php if (!empty($invoice['gst_no'])): ?>
          <p>GST No: <?= htmlspecialchars($invoice['gst_no']) ?></p>
        <?php endif; ?>
      </div>
    </header>

    <section class="invoice-meta">
      <div class="date-box invoice-from-box">
        <p class="label">Invoice From</p>
        <h2><?= htmlspecialchars($invoice['sender_name']) ?></h2>
        <p><?= htmlspecialchars(date('d M Y', strtotime($invoice['invoice_date']))) ?></p>
        <p><?= htmlspecialchars($invoice['sender_phone'] ?? '') ?></p>
        <p><?= htmlspecialchars($invoice['sender_email'] ?? '') ?></p>
      </div>
      <div>
        <p class="label">Invoice To</p>
        <h2><?= htmlspecialchars($invoice['invoice_to']) ?></h2>
        <p>Phone: <?= htmlspecialchars($invoice['client_phone'] ?? '') ?></p>
        <p>Email: <?= htmlspecialchars($invoice['client_email'] ?? '') ?></p>
        <p>Venue Address: <?= htmlspecialchars($invoice['venue_address'] ?? '') ?></p>
        <p>Event Name: <?= htmlspecialchars($invoice['event_name'] ?? '') ?></p>
      </div>
    </section>

    <table class="print-items">
      <thead>
        <tr>
          <th>Item Description</th>
          <th>Qty</th>
          <th>Rate</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['description']) ?></td>
            <td><?= money_value((float)$item['quantity']) ?></td>
            <td>Rs <?= money_value((float)$item['unit_price']) ?></td>
            <td>Rs <?= money_value((float)$item['line_total']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <section class="invoice-bottom">
      <div class="account-box">
        <h3>Account Details</h3>
        <p><strong>Account No:</strong> <?= htmlspecialchars($invoice['account_no'] ?? '') ?></p>
        <p><strong>Account Name:</strong> <?= htmlspecialchars($invoice['account_name'] ?? '') ?></p>
        <p><strong>Bank Name:</strong> <?= htmlspecialchars($invoice['bank_name'] ?? '') ?></p>
        <p><strong>IFSC Code:</strong> <?= htmlspecialchars($invoice['ifsc_code'] ?? '') ?></p>
        <p><strong>Branch:</strong> <?= htmlspecialchars($invoice['branch_address'] ?? '') ?></p>
      </div>

      <div class="print-totals">
        <p><span>Sub Total:</span><strong>Rs <?= money_value((float)$invoice['subtotal']) ?></strong></p>
        <p><span>Advance:</span><strong>Rs <?= money_value((float)$invoice['advance']) ?></strong></p>
        <p><span>Total:</span><strong>Rs <?= money_value((float)$invoice['total']) ?></strong></p>
        <p><span>Balance:</span><strong>Rs <?= money_value((float)$invoice['balance']) ?></strong></p>
      </div>
    </section>
      <?php if (!empty($invoice['signature_path'])): ?>
        <div class="signature-block" style="padding-inline-end:10%;">
          <img src="<?= htmlspecialchars($invoice['signature_path']) ?>" alt="Authorized signature">
          <span>Authorized Signature</span>
        </div>
      <?php endif; ?>
    <footer>
      
      <strong><?= htmlspecialchars($invoice['notes'] ?: 'Thank you for your business.') ?></strong>
    </footer>
  </article>
</body>
</html>
