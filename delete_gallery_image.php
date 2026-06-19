<?php
require __DIR__ . '/auth.php';
require_login();

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = db()->prepare('SELECT file_path FROM gallery_images WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $image = $stmt->fetch();
    if ($image) {
        $path = __DIR__ . '/' . ltrim((string)$image['file_path'], '/');
        if (is_file($path)) {
            @unlink($path);
        }
    }
    db()->prepare('DELETE FROM gallery_images WHERE id = :id')->execute([':id' => $id]);
}
header('Location: category_panel.php');

