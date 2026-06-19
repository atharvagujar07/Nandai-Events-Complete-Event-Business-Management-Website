<?php
require __DIR__ . '/auth.php';
require_login();
require __DIR__ . '/invoice_pdf_lib.php';

$id = (int)($_GET['id'] ?? 0);

try {
    [$invoice, $items] = pdf_fetch_invoice($id);
    $pdf = generate_invoice_pdf($invoice, $items);
    $fileName = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string)$invoice['invoice_no']) . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
} catch (Throwable $error) {
    http_response_code(404);
    echo htmlspecialchars($error->getMessage());
}
