<?php
require __DIR__ . '/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: invoices.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$items = array_values(array_filter($_POST['items'] ?? [], static function ($item): bool {
    return trim((string)($item['description'] ?? '')) !== '';
}));

if ($id <= 0 || !$items) {
    http_response_code(422);
    exit('Invoice update needs a valid invoice and at least one item.');
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
$invoiceDate = $_POST['invoice_date'] ?: date('Y-m-d');
$currentStmt = db()->prepare('SELECT signature_path FROM invoices WHERE id = :id');
$currentStmt->execute([':id' => $id]);
$currentInvoice = $currentStmt->fetch();
$signaturePath = save_signature_upload($_FILES['signature_image'] ?? [], $currentInvoice['signature_path'] ?? null);

$pdo = db();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare(
        'UPDATE invoices SET
            invoice_date = :invoice_date,
            gst_no = :gst_no,
            sender_name = :sender_name,
            sender_tagline = :sender_tagline,
            sender_phone = :sender_phone,
            sender_email = :sender_email,
            invoice_to = :invoice_to,
            client_email = :client_email,
            client_phone = :client_phone,
            venue_address = :venue_address,
            event_name = :event_name,
            account_no = :account_no,
            account_name = :account_name,
            bank_name = :bank_name,
            ifsc_code = :ifsc_code,
            branch_address = :branch_address,
            signature_path = :signature_path,
            notes = :notes,
            subtotal = :subtotal,
            advance = :advance,
            total = :total,
            balance = :balance
         WHERE id = :id'
    );

    $stmt->execute([
        ':invoice_date' => $invoiceDate,
        ':gst_no' => trim((string)($_POST['gst_no'] ?? '')),
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
        ':id' => $id,
    ]);

    $pdo->prepare('DELETE FROM invoice_items WHERE invoice_id = :id')->execute([':id' => $id]);
    $itemStmt = $pdo->prepare(
        'INSERT INTO invoice_items
          (invoice_id, description, quantity, unit_price, line_total, sort_order)
         VALUES
          (:invoice_id, :description, :quantity, :unit_price, :line_total, :sort_order)'
    );

    foreach ($items as $index => $item) {
        $itemStmt->execute([
            ':invoice_id' => $id,
            ':description' => trim((string)$item['description']),
            ':quantity' => $item['quantity'],
            ':unit_price' => $item['unit_price'],
            ':line_total' => $item['line_total'],
            ':sort_order' => $index,
        ]);
    }

    $pdo->commit();
    header('Location: invoice_view.php?id=' . $id);
} catch (Throwable $error) {
    $pdo->rollBack();
    http_response_code(500);
    echo 'Invoice could not be updated: ' . htmlspecialchars($error->getMessage());
}
