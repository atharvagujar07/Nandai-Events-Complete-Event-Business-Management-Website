<?php
require __DIR__ . '/auth.php';
require_login();

$enquiries = db()->query('SELECT * FROM enquiries ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>All Enquiries</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body>
  <main class="shell">
    <header class="topbar">
      <div><p class="eyebrow">Customer leads</p><h1>All Enquiries</h1></div>
      <nav class="top-actions">
        <a class="button ghost" href="invoices.php">Invoice Panel</a>
        <a class="button ghost" href="category_panel.php">Gallery Admin</a>
      <a class="button ghost" style="background-color: #dc3545; color:white;" href="logout.php">Logout</a>
      </nav>
    </header>
    <section class="panel">
      <div class="table-wrap">
        <table class="invoice-list">
          <thead><tr><th>Date</th><th>Name</th><th>Phone</th><th>Email</th><th>Event</th><th>Budget</th><th>Message</th><th>Action</th></tr></thead>
          <tbody>
            <?php foreach ($enquiries as $enquiry): ?>
              <tr>
                <td><?= htmlspecialchars(date('d M Y', strtotime($enquiry['created_at']))) ?></td>
                <td><?= htmlspecialchars($enquiry['name']) ?></td>
                <td><?= htmlspecialchars($enquiry['phone']) ?></td>
                <td><?= htmlspecialchars($enquiry['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($enquiry['event_type'] ?? '') ?><br><?= $enquiry['event_date'] ? htmlspecialchars(date('d M Y', strtotime($enquiry['event_date']))) : '' ?></td>
                <td><?= $enquiry['budget'] !== null ? 'Rs ' . money_value((float)$enquiry['budget']) : '' ?></td>
                <td><?= nl2br(htmlspecialchars($enquiry['message'] ?? '')) ?></td>
                <td>
                  <form action="delete_enquiry.php" method="post" onsubmit="return confirm('Delete enquiry?');">
                    <input type="hidden" name="id" value="<?= (int)$enquiry['id'] ?>">
                    <button class="button danger link" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$enquiries): ?><tr><td colspan="8">No enquiries yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>

