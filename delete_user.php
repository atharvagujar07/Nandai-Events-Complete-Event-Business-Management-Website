<?php
require __DIR__ . '/auth.php';
require_superadmin();

$id = (int)($_POST['id'] ?? 0);

if ($id > 0 && $id !== (int)$_SESSION['user_id']) {
    $stmt = db()->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: manage_users.php?message=' . urlencode('User deleted.'));

