<?php
require __DIR__ . '/auth.php';
require_login();

function posted_color(string $name, string $fallback): string
{
    $value = (string)($_POST[$name] ?? $fallback);
    return preg_match('/^#[0-9A-Fa-f]{6}$/', $value) ? $value : $fallback;
}

try {
    $settings = app_settings();
    $logoPath = save_logo_upload($_FILES['logo_image'] ?? [], $settings['logo_path'] ?? null);
    $pageBg = posted_color('page_bg_color', '#f5efe8');
    $fontColor = posted_color('font_color', '#261b14');
    $buttonColor = posted_color('button_color', '#7a5638');
    $popupColor = posted_color('popup_color', '#f3e5d3');
    $secondaryColor = posted_color('secondary_color', '#24170f');
    $accentColor = posted_color('accent_color', '#b8915d');

    $stmt = db()->prepare(
        'INSERT INTO app_settings
          (id, logo_path, theme_name, primary_color, secondary_color, accent_color, soft_color, page_bg_color, font_color, popup_color, button_color)
         VALUES
          (1, :logo_path, :theme_name, :primary_color, :secondary_color, :accent_color, :soft_color, :page_bg_color, :font_color, :popup_color, :button_color)
         ON DUPLICATE KEY UPDATE
          logo_path = VALUES(logo_path),
          theme_name = VALUES(theme_name),
          primary_color = VALUES(primary_color),
          secondary_color = VALUES(secondary_color),
          accent_color = VALUES(accent_color),
          soft_color = VALUES(soft_color),
          page_bg_color = VALUES(page_bg_color),
          font_color = VALUES(font_color),
          popup_color = VALUES(popup_color),
          button_color = VALUES(button_color)'
    );
    $stmt->execute([
        ':logo_path' => $logoPath,
        ':theme_name' => 'custom',
        ':primary_color' => $buttonColor,
        ':secondary_color' => $secondaryColor,
        ':accent_color' => $accentColor,
        ':soft_color' => $popupColor,
        ':page_bg_color' => $pageBg,
        ':font_color' => $fontColor,
        ':popup_color' => $popupColor,
        ':button_color' => $buttonColor,
    ]);

    $newPassword = (string)($_POST['new_password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if ($newPassword !== '' || $confirmPassword !== '') {
        if ($newPassword !== $confirmPassword) {
            throw new RuntimeException('Password confirmation does not match.');
        }
        if (strlen($newPassword) < 6) {
            throw new RuntimeException('Password must be at least 6 characters.');
        }

        $passwordStmt = db()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
        $passwordStmt->execute([
            ':password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            ':id' => (int)$_SESSION['user_id'],
        ]);
    }

    header('Location: settings.php?message=' . urlencode('Settings saved successfully.'));
} catch (Throwable $error) {
    header('Location: settings.php?message=' . urlencode($error->getMessage()));
}
