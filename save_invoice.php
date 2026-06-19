<?php
require __DIR__ . '/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create_invoice.php');
    exit;
}

$items = array_values(array_filter($_POST['items'] ?? [], static function ($item): bool {
    return trim((string)($item['description'] ?? '')) !== '';
}));

if (!$items) {
    http_response_code(422);
    exit('Please add at least one invoice item.');
}

$subtotal = 0.0;
foreach ($items as $index => $item) {
    $quantity = (float)($item['quantity'] ?? 0);
    $unitPrice = (float)($item['unit_price'] ?? 0);
    $items[$index]['quantity'] = $quantity;
    $items[$index]['unit_price'] = $unitPrice;
    $items[$index]['line_total'] = $quantity * $unitPrice;
    $subtotal += $items[$index]['line_total'];
}

$advance = (float)($_POST['advance'] ?? 0);
$total = $subtotal;
$balance = max(0, $total - $advance);
$invoiceNo = trim((string)($_POST['invoice_no'] ?? '')) ?: generate_invoice_number();
$invoiceDate = $_POST['invoice_date'] ?: date('Y-m-d');
$signaturePath = save_signature_upload($_FILES['signature_image'] ?? []);

$pdo = db();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare(
        'INSERT INTO invoices (
            invoice_no, gst_no, invoice_date, sender_name, sender_tagline, sender_phone, sender_email,
            invoice_to, client_email, client_phone, venue_address, event_name, account_no, account_name,
            bank_name, ifsc_code, branch_address, signature_path, notes, subtotal, advance, total, balance
        ) VALUES (
            :invoice_no, :gst_no, :invoice_date, :sender_name, :sender_tagline, :sender_phone, :sender_email,
            :invoice_to, :client_email, :client_phone, :venue_address, :event_name, :account_no, :account_name,
            :bank_name, :ifsc_code, :branch_address, :signature_path, :notes, :subtotal, :advance, :total, :balance
        )'
    );

    $stmt->execute([
        ':invoice_no' => $invoiceNo,
        ':gst_no' => trim((string)($_POST['gst_no'] ?? '')),
        ':invoice_date' => $invoiceDate,
        ':sender_name' => trim((string)$_POST['sender_name']),
        ':sender_tagline' => trim((string)($_POST['sender_tagline'] ?? '')),
        ':sender_phone' => trim((string)($_POST['sender_phone'] ?? '')),
        ':sender_email' => trim((string)($_POST['sender_email'] ?? '')),
        ':invoice_to' => trim((string)$_POST['invoice_to']),
        ':client_email' => trim((string)($_POST['client_email'] ?? '')),
        ':client_phone' => trim((string)($_POST['client_phone'] ?? '')),
        ':venue_address' => trim((string)($_POST['venue_address'] ?? '')),
        ':event_name' => trim((string)($_POST['event_name'] ?? '')),
        ':account_no' => trim((string)($_POST['account_no'] ?? '')),
        ':account_name' => trim((string)($_POST['account_name'] ?? '')),
        ':bank_name' => trim((string)($_POST['bank_name'] ?? '')),
        ':ifsc_code' => trim((string)($_POST['ifsc_code'] ?? '')),
        ':branch_address' => trim((string)($_POST['branch_address'] ?? '')),
        ':signature_path' => $signaturePath,
        ':notes' => trim((string)($_POST['notes'] ?? '')),
        ':subtotal' => $subtotal,
        ':advance' => $advance,
        ':total' => $total,
        ':balance' => $balance,
    ]);

    $invoiceId = (int)$pdo->lastInsertId();
    $itemStmt = $pdo->prepare(
        'INSERT INTO invoice_items
          (invoice_id, description, quantity, unit_price, line_total, sort_order)
         VALUES
          (:invoice_id, :description, :quantity, :unit_price, :line_total, :sort_order)'
    );

    foreach ($items as $index => $item) {
        $itemStmt->execute([
            ':invoice_id' => $invoiceId,
            ':description' => trim((string)$item['description']),
            ':quantity' => $item['quantity'],
            ':unit_price' => $item['unit_price'],
            ':line_total' => $item['line_total'],
            ':sort_order' => $index,
        ]);
    }

    $pdo->commit();
    header('Location: invoice_view.php?id=' . $invoiceId);
} catch (Throwable $error) {
    $pdo->rollBack();
    http_response_code(500);
    echo 'Invoice could not be saved: ' . htmlspecialchars($error->getMessage());
}
