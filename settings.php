<?php
require __DIR__ . '/auth.php';
require_login();

$settings = app_settings();
$message = $_GET['message'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Settings</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body>
  <main class="shell">
    <header class="topbar">
      <div>
        <p class="eyebrow">Invoice app settings</p>
        <h1>Settings</h1>
      </div>
      <nav class="top-actions">
        <a class="button ghost" href="create_invoice.php">Create invoice</a>
        <a class="button ghost" href="all_enquiry.php">Enquiries</a>
        <a class="button ghost" href="category_panel.php">Gallery Admin</a>
        <a class="button ghost" href="invoices.php">Invoices</a>
        <a class="button ghost" href="sales_report.php">Reports</a>
        <?php if (is_superadmin()): ?><a class="button ghost" href="manage_users.php">Manage Users</a><?php endif; ?>
      </nav>
    </header>

    <?php if ($message): ?><div class="notice"><?= htmlspecialchars((string)$message) ?></div><?php endif; ?>

    <form class="panel" action="save_settings.php" method="post" enctype="multipart/form-data">
      <section class="section-grid">
        <div>
          <h2>Logo</h2>
          <div class="settings-logo">
            <img src="<?= htmlspecialchars(app_logo_path()) ?>" alt="Current logo" height="300px">
            <label>Upload New Logo
              <input type="file" name="logo_image" accept="image/png,image/jpeg,image/webp">
            </label>
          </div>
        </div>

        <div>
          <h2>Colors</h2>
          <div class="color-grid">
            <label>Background
              <input type="color" name="page_bg_color" value="<?= htmlspecialchars($settings['page_bg_color'] ?? '#f5efe8') ?>">
            </label>
            <label>Font Color
              <input type="color" name="font_color" value="<?= htmlspecialchars($settings['font_color'] ?? '#261b14') ?>">
            </label>
            <label>Button Color
              <input type="color" name="button_color" value="<?= htmlspecialchars($settings['button_color'] ?? '#7a5638') ?>">
            </label>
            <label>Popup Box
              <input type="color" name="popup_color" value="<?= htmlspecialchars($settings['popup_color'] ?? '#f3e5d3') ?>">
            </label>
            <label>Header Dark
              <input type="color" name="secondary_color" value="<?= htmlspecialchars($settings['secondary_color'] ?? '#24170f') ?>">
            </label>
            <label>Accent
              <input type="color" name="accent_color" value="<?= htmlspecialchars($settings['accent_color'] ?? '#b8915d') ?>">
            </label>
          </div>
        </div>
      </section>

      <section class="section-grid">
        <div>
          <h2>Change Password</h2>
          <div class="grid two">
            <label>New Password
              <input type="password" name="new_password" placeholder="Leave blank to keep current">
            </label>
            <label>Confirm Password
              <input type="password" name="confirm_password" placeholder="Repeat new password">
            </label>
          </div>
        </div>
      </section>

      <div class="actions">
        <button class="button" type="submit">Save settings</button>
         <a class="button ghost" style="background-color: #dc3545; color:white;" href="logout.php">Logout</a>
      </div>
    </form>
  </main>
</body>
</html>
