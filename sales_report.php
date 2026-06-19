<?php
require __DIR__ . '/auth.php';
require_login();

$today = date('Y-m-d');
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? $today;

$summaryStmt = db()->prepare(
    'SELECT
      COUNT(*) AS invoice_count,
      COALESCE(SUM(total), 0) AS total_sales,
      COALESCE(SUM(advance), 0) AS received_amount,
      COALESCE(SUM(balance), 0) AS pending_balance
     FROM invoices
     WHERE invoice_date BETWEEN :from_date AND :to_date'
);
$summaryStmt->execute([':from_date' => $from, ':to_date' => $to]);
$summary = $summaryStmt->fetch();

$eventStmt = db()->prepare(
    'SELECT COALESCE(NULLIF(event_name, ""), "Unspecified Event") AS event_name,
      COUNT(*) AS invoice_count,
      COALESCE(SUM(total), 0) AS total_sales,
      COALESCE(SUM(balance), 0) AS pending_balance
     FROM invoices
     WHERE invoice_date BETWEEN :from_date AND :to_date
     GROUP BY COALESCE(NULLIF(event_name, ""), "Unspecified Event")
     ORDER BY total_sales DESC'
);
$eventStmt->execute([':from_date' => $from, ':to_date' => $to]);
$events = $eventStmt->fetchAll();

$invoiceStmt = db()->prepare(
    'SELECT invoice_no, invoice_date, invoice_to, event_name, total, advance, balance
     FROM invoices
     WHERE invoice_date BETWEEN :from_date AND :to_date
     ORDER BY invoice_date DESC, id DESC'
);
$invoiceStmt->execute([':from_date' => $from, ':to_date' => $to]);
$invoices = $invoiceStmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sales Report</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body>
  <main class="shell">
    <header class="topbar">
      <div>
        <p class="eyebrow">Monthly sales and event report</p>
        <h1>Sales Report</h1>
      </div>
      <nav class="top-actions">
        <a class="button ghost" href="create_invoice.php">Create invoice</a>
        <a class="button ghost" href="all_enquiry.php">Enquiries</a>
        <a class="button ghost" href="category_panel.php">Gallery Admin</a>
        <a class="button ghost" href="invoices.php">Invoices</a>
        <a class="button ghost" href="settings.php">Settings</a>
        <!-- <?php if (is_superadmin()): ?><a class="button ghost" href="manage_users.php">Manage Users</a><?php endif; ?> -->
     <a class="button ghost" style="background-color: #dc3545; color:white;" href="logout.php">Logout</a>
      </nav>
    </header>

    <form class="panel report-filter" method="get">
      <label>From Date
        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>">
      </label><br>
      <label>To Date
        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>">
      </label> <br>
            <div>
                <br>
                <button class="button" type="submit" style="" >Submit</button> &nbsp; &nbsp; &nbsp; &nbsp;
                <button class="button" type="submit" onclick="GenerateInvoice();">Generate Report</button>
                <br>
            </div>
    </form>
      <script>  function GenerateInvoice() {window.print();}</script>
    <br>
    <section class="panel">
      <br><article><h1>Total Sales</h1><br><p>Total Income</p><strong>Rs <?= money_value((float)$summary['total_sales']) ?></strong></article>
      <br><article><p>Received</p><strong>Rs <?= money_value((float)$summary['received_amount']) ?></strong></article>
      <br><article><p>Pending Balance</p><strong>Rs <?= money_value((float)$summary['pending_balance']) ?></strong></article>
      <br><article><p>Invoices</p><strong><?= (int)$summary['invoice_count'] ?></strong></article>
    </section>
    
     <br>
    <section class="panel">
      <h2>Event Sales Summary</h2>
      <div class="table-wrap">
        <table class="invoice-list">
          <thead><tr><th>Event</th><th>Invoices</th><th>Total Sales</th><th>Pending Balance</th></tr></thead>
          <tbody>
            <?php foreach ($events as $event): ?>
              <tr>
                <td><?= htmlspecialchars($event['event_name']) ?></td>
                <td><?= (int)$event['invoice_count'] ?></td>
                <td>Rs <?= money_value((float)$event['total_sales']) ?></td>
                <td>Rs <?= money_value((float)$event['pending_balance']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$events): ?><tr><td colspan="4">No event data for this period.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="panel">
      <h2>Invoice Details</h2>
      <div class="table-wrap">
        <table class="invoice-list">
          <thead><tr><th>Date</th><th>Invoice</th><th>Client</th><th>Event</th><th>Total</th><th>Received</th><th>Balance</th></tr></thead>
          <tbody>
            <?php foreach ($invoices as $invoice): ?>
              <tr>
                <td><?= htmlspecialchars(date('d M Y', strtotime($invoice['invoice_date']))) ?></td>
                <td><?= htmlspecialchars($invoice['invoice_no']) ?></td>
                <td><?= htmlspecialchars($invoice['invoice_to']) ?></td>
                <td><?= htmlspecialchars($invoice['event_name'] ?? '') ?></td>
                <td>Rs <?= money_value((float)$invoice['total']) ?></td>
                <td>Rs <?= money_value((float)$invoice['advance']) ?></td>
                <td>Rs <?= money_value((float)$invoice['balance']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$invoices): ?><tr><td colspan="7">No invoices for this period.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
