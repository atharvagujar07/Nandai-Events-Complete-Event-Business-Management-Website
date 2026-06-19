<?php
require __DIR__ . '/db.php';

$categorySlug = trim((string)($_GET['category'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$categories = $pdo->query('SELECT * FROM gallery_categories WHERE is_active = 1 ORDER BY name')->fetchAll();
$where = 'WHERE gc.is_active = 1';
$params = [];

if ($categorySlug !== '') {
    $where .= ' AND gc.slug = :slug';
    $params[':slug'] = $categorySlug;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM gallery_images gi JOIN gallery_categories gc ON gc.id = gi.category_id {$where}");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pages = max(1, (int)ceil($total / $limit));

$stmt = $pdo->prepare(
    "SELECT gi.*, gc.name AS category_name, gc.slug
     FROM gallery_images gi
     JOIN gallery_categories gc ON gc.id = gi.category_id
     {$where}
     ORDER BY gi.created_at DESC
     LIMIT {$limit} OFFSET {$offset}"
);
$stmt->execute($params);
$images = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Event Gallery - Nandai Events</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="site-nav">
    <a class="brand" href="index.php"><img src="assets/site/nandai-logo-large.png" alt="Nandai Events"><span>Nandai Events</span></a>
    <div><a href="index.php">Home</a><a href="index.php#enquiry">Enquiry</a>
  </nav>
  <main class="gallery-page">
    <div class="section-head"><div><p class="eyebrow">Our work</p><h1>Event Gallery</h1></div></div>
    <div class="category-tabs">
      <a class="<?= $categorySlug === '' ? 'active' : '' ?>" href="gallery.php">All</a>
      <?php foreach ($categories as $category): ?>
        <a class="<?= $categorySlug === $category['slug'] ? 'active' : '' ?>" href="gallery.php?category=<?= urlencode($category['slug']) ?>"><?= htmlspecialchars($category['name']) ?></a>
      <?php endforeach; ?>
    </div>
  
    <div class="site-gallery-grid">
  <?php foreach ($images as $image): ?>
    <figure>
      <img
        src="<?= htmlspecialchars($image['file_path']) ?>"
        alt="<?= htmlspecialchars($image['title'] ?: $image['category_name']) ?>"
        onclick="openModal(this.src)"
      >
      <figcaption><?= htmlspecialchars($image['category_name']) ?></figcaption>
    </figure>
  <?php endforeach; ?>

  <?php if (!$images): ?>
    <figure>
      <img src="assets/site/hero-events.png" alt="Event">
      <figcaption>Upload images from admin</figcaption>
    </figure>
  <?php endif; ?>
</div>
    
    <div class="pagination">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a class="<?= $i === $page ? 'active' : '' ?>" href="gallery.php?category=<?= urlencode($categorySlug) ?>&page=<?= $i ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </main>

  
  <!-- Fullscreen Image Modal -->
<div id="imageModal" class="image-modal">
    <span class="close-modal">&times;</span>
    <img id="modalImage" class="modal-content">
</div>

<script>
const modal = document.getElementById('imageModal');
const modalImg = document.getElementById('modalImage');
const closeBtn = document.querySelector('.close-modal');

function openModal(src) {
    modal.style.display = 'flex';
    modalImg.src = src;
}

closeBtn.onclick = function() {
    modal.style.display = 'none';
};

modal.onclick = function(e) {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
};

// ESC key closes modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        modal.style.display = 'none';
    }
});
</script>
</body>
</html>

