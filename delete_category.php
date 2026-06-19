<?php
require __DIR__ . '/auth.php';
require_login();

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = db()->prepare('SELECT slug FROM gallery_categories WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $category = $stmt->fetch();
    db()->prepare('DELETE FROM gallery_categories WHERE id = :id')->execute([':id' => $id]);
}
header('Location: category_panel.php');

