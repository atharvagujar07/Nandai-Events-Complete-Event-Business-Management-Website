<?php
require __DIR__ . '/auth.php';
require_superadmin();

$users = db()->query('SELECT id, username, display_name, role, is_active, created_at FROM users ORDER BY id')->fetchAll();
$message = $_GET['message'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Users</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body>
  <main class="shell">
    <header class="topbar">
      <div>
        <p class="eyebrow">Superadmin only</p>
        <h1>Manage Users</h1>
      </div>
      <nav class="top-actions">
        <a class="button ghost" href="create_invoice.php">Create invoice</a>
        <a class="button ghost" href="settings.php">Settings</a>
        <a class="button ghost" style="background-color: #dc3545; color:white;" href="logout.php">Logout</a>
      </nav>
    </header>

    <?php if ($message): ?><div class="notice"><?= htmlspecialchars((string)$message) ?></div><?php endif; ?>

    <section class="panel">
      <h2>Create User</h2>
      <form class="grid two" action="save_user.php" method="post">
        <input type="hidden" name="action" value="create">
        <label>Username
          <input name="username" required>
        </label>
        <label>Display Name
          <input name="display_name" required>
        </label>
        <label>Password
          <input type="password" name="password" required>
        </label>
        <label>Role
          <select name="role">
            <option value="staff">Staff</option>
            <option value="superadmin">Superadmin</option>
          </select>
        </label>
        <button class="button" type="submit">Create user</button>
      </form>
    </section>

    <section class="panel">
      <h2>Existing Users</h2>
      <div class="table-wrap">
        <table class="invoice-list">
          <thead><tr><th>User</th><th>Name</th><th>Role</th><th>Status</th><th>Update</th><th>Delete</th></tr></thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['display_name']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= (int)$user['is_active'] === 1 ? 'Active' : 'Disabled' ?></td>
                <td>
                  <form class="user-inline-form" action="save_user.php" method="post">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                    <input name="display_name" value="<?= htmlspecialchars($user['display_name']) ?>" required>
                    <select name="role">
                      <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                      <option value="superadmin" <?= $user['role'] === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                    </select>
                    <input type="password" name="password" placeholder="New password optional">
                    <button class="button small" type="submit">Update</button>
                  </form>
                </td>
                <td>
                  <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
                    <form action="delete_user.php" method="post" onsubmit="return confirm('Delete this user?');">
                      <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                      <button class="button danger link" type="submit">Delete</button>
                    </form>
                  <?php else: ?>
                    Current user
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>

