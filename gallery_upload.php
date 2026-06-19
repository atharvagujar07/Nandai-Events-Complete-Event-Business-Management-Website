<?php
require __DIR__ . '/auth.php';
require_login();

$categoryId = (int)($_POST['category_id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM gallery_categories WHERE id = :id');
$stmt->execute([':id' => $categoryId]);
$category = $stmt->fetch();

if (!$category || empty($_FILES['gallery_images'])) {
    header('Location: category_panel.php');
    exit;
}

$folder = 'uploads/category/' . make_slug((string)$category['name']);
$absoluteFolder = __DIR__ . '/' . $folder;
ensure_dir($absoluteFolder);

$files = $_FILES['gallery_images'];
$count = is_array($files['name']) ? count($files['name']) : 0;

for ($i = 0; $i < $count; $i++) {
    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
        continue;
    }

    [$mime] = validate_image_upload($files['tmp_name'][$i]);
    $fileName = safe_upload_name($files['name'][$i]);
    $target = $absoluteFolder . '/' . $fileName;

    if (!move_uploaded_file($files['tmp_name'][$i], $target)) {
        continue;
    }

    $relativePath = $folder . '/' . $fileName;
    $insert = db()->prepare(
        'INSERT INTO gallery_images (category_id, title, file_path, original_name, mime_type, file_size)
         VALUES (:category_id, :title, :file_path, :original_name, :mime_type, :file_size)'
    );
    $insert->execute([
        ':category_id' => $categoryId,
        ':title' => pathinfo($files['name'][$i], PATHINFO_FILENAME),
        ':file_path' => $relativePath,
        ':original_name' => $files['name'][$i],
        ':mime_type' => $mime,
        ':file_size' => (int)$files['size'][$i],
    ]);
}

header('Location: category_panel.php');

