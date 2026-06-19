<?php
require __DIR__ . '/config.php';
require __DIR__ . '/invoice_pdf_lib.php';
require __DIR__ . '/smtp_mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: invoices.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

try {
    [$invoice, $items] = pdf_fetch_invoice($id);
    $clientEmail = trim((string)($invoice['client_email'] ?? ''));
    $senderEmail = trim((string)($invoice['sender_email'] ?? '')) ?: MAIL_REPLY_TO;

    if ($clientEmail === '' || !filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Client email is empty or invalid. Add client email first, then send invoice.');
    }

    if (!filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
        $senderEmail = MAIL_REPLY_TO;
    }

    $pdf = generate_invoice_pdf($invoice, $items);
    $fileName = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string)$invoice['invoice_no']) . '.pdf';
    $subject = 'Your invoice ' . $invoice['invoice_no'] . ' from ' . $invoice['sender_name'];
    $invoiceUrl = app_url('invoice_view.php?id=' . (int)$invoice['id']);
    $downloadUrl = app_url('download_pdf.php?id=' . (int)$invoice['id']);

    $htmlMessage = '
      <div style="font-family:Arial,sans-serif;color:#261b14;line-height:1.6">
        <h2 style="color:#24170f;margin-bottom:8px">Thank you for choosing ' . htmlspecialchars((string)$invoice['sender_name']) . '</h2>
        <p>Dear ' . htmlspecialchars((string)$invoice['invoice_to']) . ',</p>
        <p>Your invoice <strong>' . htmlspecialchars((string)$invoice['invoice_no']) . '</strong> is ready. The PDF is attached with this email for your records.</p>
        <p><strong>Invoice Total:</strong> Rs ' . money_value((float)$invoice['total']) . '<br>
        <strong>Balance:</strong> Rs ' . money_value((float)$invoice['balance']) . '</p>
        <p>You can also view it online here:<br><a href="' . htmlspecialchars($invoiceUrl) . '">' . htmlspecialchars($invoiceUrl) . '</a></p>
        <p>Direct PDF download:<br><a href="' . htmlspecialchars($downloadUrl) . '">' . htmlspecialchars($downloadUrl) . '</a></p>
        <p style="margin-top:24px">Warm regards,<br><strong>' . htmlspecialchars((string)$invoice['sender_name']) . '</strong></p>
      </div>';

    $boundary = 'invoice_' . md5((string)microtime(true));
    $headers = [
        'MIME-Version: 1.0',
        'From: ' . MAIL_FROM_NAME . ' <' . $senderEmail . '>',
        'Reply-To: ' . $senderEmail,
        'Content-Type: multipart/mixed; boundary="' . $boundary . '"',
    ];

    $body = "--{$boundary}\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $htmlMessage . "\r\n";
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: application/pdf; name=\"{$fileName}\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"{$fileName}\"\r\n\r\n";
    $body .= chunk_split(base64_encode($pdf)) . "\r\n";
    $body .= "--{$boundary}--";

    $sent = smtp_send_email($clientEmail, $subject, $body, $headers, SMTP_USERNAME);
    $status = $sent ? 'sent' : 'failed';
    $error = $sent ? null : 'SMTP send returned false.';

    $log = db()->prepare(
        'INSERT INTO invoice_email_logs
          (invoice_id, sender_email, client_email, email_subject, email_body, status, error_message)
         VALUES
          (:invoice_id, :sender_email, :client_email, :email_subject, :email_body, :status, :error_message)'
    );
    $log->execute([
        ':invoice_id' => (int)$invoice['id'],
        ':sender_email' => $senderEmail,
        ':client_email' => $clientEmail,
        ':email_subject' => $subject,
        ':email_body' => $htmlMessage,
        ':status' => $status,
        ':error_message' => $error,
    ]);

    $message = $sent ? 'Invoice email sent successfully.' : 'Email could not be sent. Check app SMTP settings in config.php.';
    header('Location: invoice_view.php?id=' . (int)$invoice['id'] . '&email_status=' . urlencode($message));
} catch (Throwable $error) {
    if ($id > 0) {
        $log = db()->prepare(
            'INSERT INTO invoice_email_logs
              (invoice_id, sender_email, client_email, email_subject, email_body, status, error_message)
             VALUES
              (:invoice_id, :sender_email, :client_email, :email_subject, :email_body, :status, :error_message)'
        );
        $log->execute([
            ':invoice_id' => $id,
            ':sender_email' => '',
            ':client_email' => '',
            ':email_subject' => 'Invoice email failed',
            ':email_body' => '',
            ':status' => 'failed',
            ':error_message' => $error->getMessage(),
        ]);
    }

    header('Location: invoice_view.php?id=' . $id . '&email_status=' . urlencode($error->getMessage()));
}

