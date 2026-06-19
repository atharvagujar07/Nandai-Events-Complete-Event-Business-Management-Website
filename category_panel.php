<?php
require __DIR__ . '/auth.php';
require_login();

$categories = db()->query(
    'SELECT gc.*, COUNT(gi.id) AS image_count
     FROM gallery_categories gc
     LEFT JOIN gallery_images gi ON gi.category_id = gc.id
     GROUP BY gc.id
     ORDER BY gc.name'
)->fetchAll();

$images = db()->query(
    'SELECT gi.*, gc.name AS category_name
     FROM gallery_images gi
     JOIN gallery_categories gc ON gc.id = gi.category_id
     ORDER BY gi.created_at DESC
     LIMIT 100'
)->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gallery Admin</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <?= app_theme_style() ?>
</head>
<body>
  <main class="shell">
    <header class="topbar">
      <div><p class="eyebrow">Event gallery</p><h1>Category Panel</h1></div>
      <nav class="top-actions">
        <a class="button ghost" href="invoices.php">Invoice Panel</a>
        <a class="button ghost" href="gallery.php">View Gallery</a>
      </nav>
    </header>

    <section class="section-grid">
      <form class="panel" action="save_category.php" method="post">
        <h2>Create / Update Category</h2>
        <input type="hidden" name="id" value="">
        <label>Category Name
          <input name="name" placeholder="Wedding, Birthday, Anniversary" required>
        </label>
        <label>Description
          <textarea name="description" placeholder="Short category description"></textarea>
        </label>
        <button class="button" type="submit">Save category</button>
      </form>

      <form class="panel" action="gallery_upload.php" method="post" enctype="multipart/form-data">
        <h2>Upload Images</h2>
        <label>Category
          <select name="category_id" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= (int)$category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Images
          <input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required>
        </label>
        <button class="button" type="submit">Upload images</button>
      </form>
    </section>

    <section class="panel">
      <h2>Categories</h2>
      <div class="table-wrap">
        <table class="invoice-list">
          <thead><tr><th>Name</th><th>Slug</th><th>Images</th><th>Description</th><th>Delete</th></tr></thead>
          <tbody>
            <?php foreach ($categories as $category): ?>
              <tr>
                <td><?= htmlspecialchars($category['name']) ?></td>
                <td><?= htmlspecialchars($category['slug']) ?></td>
                <td><?= (int)$category['image_count'] ?></td>
                <td><?= htmlspecialchars($category['description'] ?? '') ?></td>
                <td>
                  <form action="delete_category.php" method="post" onsubmit="return confirm('Delete category and images?');">
                    <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">
                    <button class="button danger link" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="panel">
      <h2>Uploaded Images</h2>
      <div class="admin-gallery-grid">
        <?php foreach ($images as $image): ?>
          <figure>
            <img src="<?= htmlspecialchars($image['file_path']) ?>" alt="<?= htmlspecialchars($image['title'] ?: $image['category_name']) ?>">
            <figcaption><?= htmlspecialchars($image['category_name']) ?></figcaption>
            <form action="delete_gallery_image.php" method="post">
              <input type="hidden" name="id" value="<?= (int)$image['id'] ?>">
              <button class="button danger link" type="submit">Delete</button>
            </form>
          </figure>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>

