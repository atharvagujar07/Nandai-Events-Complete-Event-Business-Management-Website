<?php
require __DIR__ . '/auth.php';
require_login();

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = db()->prepare('DELETE FROM enquiries WHERE id = :id');
    $stmt->execute([':id' => $id]);
}
header('Location: all_enquiry.php');

