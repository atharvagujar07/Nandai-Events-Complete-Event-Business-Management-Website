<?php
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$eventType = trim((string)($_POST['event_type'] ?? ''));
$eventDate = $_POST['event_date'] ?: null;
$budget = $_POST['budget'] !== '' ? (float)$_POST['budget'] : null;
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $phone === '') {
    header('Location: index.php?status=' . urlencode('Name and phone are required.') . '#enquiry');
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?status=' . urlencode('Please enter a valid email.') . '#enquiry');
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO enquiries (name, phone, email, event_type, event_date, budget, message)
     VALUES (:name, :phone, :email, :event_type, :event_date, :budget, :message)'
);
$stmt->execute([
    ':name' => $name,
    ':phone' => $phone,
    ':email' => $email,
    ':event_type' => $eventType,
    ':event_date' => $eventDate,
    ':budget' => $budget,
    ':message' => $message,
]);

header('Location: index.php?status=' . urlencode('Thank you. Your enquiry has been submitted.') . '#enquiry');

