<?php
require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function ensure_default_admin(): void
{
    try {
        $count = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    } catch (Throwable) {
        return;
    }

    if ($count === 0) {
        $stmt = db()->prepare(
            'INSERT INTO users (username, password_hash, display_name, role)
             VALUES (:username, :password_hash, :display_name, :role)'
        );
        $stmt->execute([
            ':username' => 'admin',
            ':password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            ':display_name' => 'Administrator',
            ':role' => 'superadmin',
        ]);
    }
}

function require_login(): void
{
    ensure_default_admin();

    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user_name(): string
{
    return (string)($_SESSION['display_name'] ?? $_SESSION['username'] ?? 'User');
}

function current_user_role(): string
{
    return (string)($_SESSION['role'] ?? 'staff');
}

function is_superadmin(): bool
{
    return current_user_role() === 'superadmin';
}

function require_superadmin(): void
{
    require_login();

    if (!is_superadmin()) {
        http_response_code(403);
        exit('Only superadmin can access this page.');
    }
}
