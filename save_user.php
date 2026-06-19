<?php
require __DIR__ . '/auth.php';
require_superadmin();

try {
    $action = (string)($_POST['action'] ?? '');
    $role = in_array($_POST['role'] ?? 'staff', ['superadmin', 'staff'], true) ? $_POST['role'] : 'staff';

    if ($action === 'create') {
        $password = (string)($_POST['password'] ?? '');
        if (strlen($password) < 6) {
            throw new RuntimeException('Password must be at least 6 characters.');
        }

        $stmt = db()->prepare(
            'INSERT INTO users (username, password_hash, display_name, role)
             VALUES (:username, :password_hash, :display_name, :role)'
        );
        $stmt->execute([
            ':username' => trim((string)$_POST['username']),
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ':display_name' => trim((string)$_POST['display_name']),
            ':role' => $role,
        ]);
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = db()->prepare('UPDATE users SET display_name = :display_name, role = :role WHERE id = :id');
        $stmt->execute([
            ':display_name' => trim((string)$_POST['display_name']),
            ':role' => $role,
            ':id' => $id,
        ]);

        $password = (string)($_POST['password'] ?? '');
        if ($password !== '') {
            if (strlen($password) < 6) {
                throw new RuntimeException('Password must be at least 6 characters.');
            }
            $passStmt = db()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
            $passStmt->execute([
                ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ':id' => $id,
            ]);
        }
    }

    header('Location: manage_users.php?message=' . urlencode('User saved successfully.'));
} catch (Throwable $error) {
    header('Location: manage_users.php?message=' . urlencode($error->getMessage()));
}

