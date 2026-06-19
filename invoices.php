<?php
require __DIR__ . '/auth.php';
require_login();

$invoices = db()->query(
    'SELECT id, invoice_no, invoice_date, invoice_to, event_name, total, balance, created_at
     FROM invoices
     ORDER BY created_at DESC'
)->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Saved Invoices</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body>
  <main class="shell">
    <header class="topbar">
      <div>
        <p class="eyebrow">Invoice panel</p>
        <h1>Saved Invoices</h1>
      </div>
      <nav class="top-actions">
        <a class="button" href="create_invoice.php">Create invoice</a>
        <a class="button ghost" href="all_enquiry.php">Show Enquiry</a>
        <a class="button ghost" href="category_panel.php">Gallery Admin</a>
        <a class="button ghost" href="sales_report.php">Reports</a>
        <a class="button ghost" href="settings.php">Settings</a>
        <?php if (is_superadmin()): ?><a class="button ghost" href="manage_users.php">Manage Users</a><?php endif; ?>
     <a class="button ghost" style="background-color: #dc3545; color:white;" href="logout.php">Logout</a>
      </nav>
    </header>

    <section class="panel">
      <div class="table-wrap">
        <table class="invoice-list">
          <thead>
            <tr>
              <th>Invoice No</th>
              <th>Date</th>
              <th>Client</th>
              <th>Event</th>
              <th>Total</th>
              <th>Balance</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($invoices as $invoice): ?>
              <tr>
                <td><?= htmlspecialchars($invoice['invoice_no']) ?></td>
                <td><?= htmlspecialchars(date('d M Y', strtotime($invoice['invoice_date']))) ?></td>
                <td><?= htmlspecialchars($invoice['invoice_to']) ?></td>
                <td><?= htmlspecialchars($invoice['event_name'] ?? '') ?></td>
                <td>Rs <?= money_value((float)$invoice['total']) ?></td>
                <td>Rs <?= money_value((float)$invoice['balance']) ?></td>
                <td>
                  <div class="table-actions">
                    <a style="color:white;" class="button open link"  href="invoice_view.php?id=<?= (int)$invoice['id'] ?>">Open</a>
                    <a style="color:white;" class="button update link" href="edit_invoice.php?id=<?= (int)$invoice['id'] ?>">Update</a>
                    <a  style="color:white;" class="button link" href="download_pdf.php?id=<?= (int)$invoice['id'] ?>">Download PDF</a>
                    <form class="inline-form" action="delete_invoice.php" method="post" onsubmit="return confirm('Delete this invoice permanently?');">
                      <input type="hidden" name="id" value="<?= (int)$invoice['id'] ?>">
                      <button class="button danger link" type="submit">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$invoices): ?>
              <tr><td colspan="7">No invoices saved yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
