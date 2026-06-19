<?php
require __DIR__ . '/auth.php';

ensure_default_admin();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM users WHERE username = :username AND is_active = 1');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, (string)$user['password_hash'])) {
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = (string)$user['username'];
        $_SESSION['display_name'] = (string)$user['display_name'];
        $_SESSION['role'] = (string)($user['role'] ?? 'staff');
        header('Location: create_invoice.php');
        exit;
    }

    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Invoice App</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body class="login-page">
  <main class="login-shell">
    <form class="login-card" method="post">
      <div style="padding-inline: 35%;"><img src="<?= htmlspecialchars(app_logo_path()) ?>" alt="Logo"></div>
       <h1 style=" text-align: center;">Invoice Login</h1>
      <p>Sign in to manage invoices, reports, settings, and PDF delivery.</p>
      <?php if ($error): ?><div class="notice"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <label>Username
        <input name="username" placeholder="Username/Email" required>
      </label>
      <label>Password
        <input type="password" name="password" placeholder="Password" required>
      </label>
      <button class="button" type="submit">Login</button>
      <small>For login Support : <a href="mailto:atharvagujar007@gmail.com?subject=Hello Support" style="color:#7a5638;">Contact us.</a>&nbsp;&nbsp; or&nbsp;&nbsp; Back to <a href="index.php" style="color:#7a5638;">Home</a></small>
    </form>
  </main>
</body>
</html>
