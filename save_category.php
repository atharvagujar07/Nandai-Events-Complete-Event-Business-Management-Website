<?php
require __DIR__ . '/auth.php';
require_login();

$id = (int)($_POST['id'] ?? 0);
$name = trim((string)($_POST['name'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));

if ($name === '') {
    header('Location: category_panel.php');
    exit;
}

$slug = make_slug($name);

if ($id > 0) {
    $stmt = db()->prepare('UPDATE gallery_categories SET name = :name, slug = :slug, description = :description WHERE id = :id');
    $stmt->execute([':name' => $name, ':slug' => $slug, ':description' => $description, ':id' => $id]);
} else {
    $stmt = db()->prepare('INSERT INTO gallery_categories (name, slug, description) VALUES (:name, :slug, :description)');
    $stmt->execute([':name' => $name, ':slug' => $slug, ':description' => $description]);
}

header('Location: category_panel.php');

