<?php
require __DIR__ . '/auth.php';
require_login();
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

    //invoice view fetch code
        $stmt = db()->prepare('SELECT * FROM invoices WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $invoice = $stmt->fetch();

        if (!$invoice) {
            http_response_code(404);
            exit('Invoice not found.');
        }

        $itemStmt = db()->prepare('SELECT * FROM invoice_items WHERE invoice_id = :id ORDER BY sort_order, id');
        $itemStmt->execute([':id' => $id]);
        $items = $itemStmt->fetchAll();


$itemRows = '';

foreach ($items as $item) {
    $itemRows .= '
    <tr>
        <td style="padding:10px;border-bottom:1px solid #dfd0c0;">
            ' . htmlspecialchars($item['description']) . '
        </td>
        <td style="padding:10px;border-bottom:1px solid #dfd0c0;text-align:center;">
            ' . money_value((float)$item['quantity']) . '
        </td>
        <td style="padding:10px;border-bottom:1px solid #dfd0c0;text-align:right;">
            Rs ' . money_value((float)$item['unit_price']) . '
        </td>
        <td style="padding:10px;border-bottom:1px solid #dfd0c0;text-align:right;">
            Rs ' . money_value((float)$item['line_total']) . '
        </td>
    </tr>';
}


$htmlMessage = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body {
  margin: 0;
  padding: 0;
  background: #f5efe8;
  font-family: Arial, Helvetica, sans-serif;
  color: #261b14;
}

/* MAIN CONTAINER */
.invoice-sheet {
  max-width: 650px;
  margin: auto;
  background: #ffffff;
  border: 1px solid #dfd0c0;
  box-shadow: 0 10px 30px rgba(36, 23, 15, 0.1);
}

/* HEADER */
.invoice-hero {
  background: #2b1a10;
  color: #ffffff;
  padding: 20px;
}

.logo-lockup {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-lockup img {
  width: 60px;
  height: 60px;
  background: #f6e7d8;
}

.brand {
  font-size: 16px;
  font-weight: bold;
  margin: 0;
}

/* TITLE */
.invoice-title h1 {
  margin: 0;
  font-size: 28px;
}

.invoice-title p {
  margin: 0;
  font-size: 12px;
}

/* META SECTIONS */
.invoice-meta {
  padding: 20px;
}

.invoice-meta h2 {
  font-size: 16px;
  margin-bottom: 10px;
}

.invoice-meta p {
  font-size: 12px;
  margin: 4px 0;
}

/* BOXES */
.date-box {
  background: #f3e5d3;
  padding: 12px;
  margin-bottom: 15px;
}

/* TABLE */
.print-items {
  width: 100%;
  border-collapse: collapse;
  margin: 0 0 20px 0;
}

.print-items th {
  background: #24170f;
  color: #ffffff;
  padding: 10px;
  font-size: 12px;
  text-align: left;
}

.print-items td {
  border-bottom: 1px solid #dfd0c0;
  padding: 10px;
  font-size: 12px;
}

/* TOTALS */
.print-totals {
  background: #f3e5d3;
  padding: 12px;
  font-size: 12px;
}

.print-totals p {
  display: flex;
  justify-content: space-between;
  margin: 6px 0;
}

/* ACCOUNT BOX */
.account-box {
  border-left: 4px solid #b8915d;
  padding-left: 12px;
  margin-bottom: 15px;
}

.account-box h3 {
  font-size: 13px;
  margin-bottom: 10px;
}

/* FOOTER */
footer {
  text-align: center;
  padding: 20px;
  font-size: 11px;
  border-top: 1px solid #dfd0c0;
}

.signature-block img {
  max-width: 120px;
}
</style>
</head>

<body style="margin:0;padding:0;background:#f5efe8;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5efe8;padding:20px;">
<tr>
<td align="center">

<table width="650" cellpadding="0" cellspacing="0" style="background:#fff;border:1px solid #dfd0c0;">

<!-- HEADER -->
<tr>
<td style="background:#2b1a10;color:#fff;padding:20px;">

<table width="100%">
<tr>
<td style="vertical-align:middle;">
    <img src="https://beeimg.com/images/d43463561674.png" style="width:60px;height:60px;background:#f6e7d8;">
</td>

<td style="text-align:left;padding-left:10px;">
    <div style="font-size:16px;font-weight:bold;">Nandai Events</div>
    <div style="font-size:12px;">' . htmlspecialchars($invoice['sender_tagline'] ?? '') . '</div>
</td>

<td style="text-align:right;">
    <div style="font-size:26px;font-weight:bold;">INVOICE</div>
    <div style="font-size:12px;">' . htmlspecialchars($invoice['invoice_no']) . '</div>
</td>
</tr>
</table>

</td>
</tr>

<!-- CLIENT INFO -->
<tr>
<td style="padding:20px;">

<table width="100%">
<tr>

<td width="50%" style="vertical-align:top;">
    <h3 style="margin:0 0 10px 0;">Invoice From</h3>
    <p style="margin:4px 0;">' . htmlspecialchars($invoice['sender_name']) . '</p>
    <p style="margin:4px 0;">' . htmlspecialchars($invoice['sender_phone'] ?? '') . '</p>
    <p style="margin:4px 0;">' . htmlspecialchars($invoice['sender_email'] ?? '') . '</p>
</td>

<td width="50%" style="vertical-align:top;">
    <h3 style="margin:0 0 10px 0;">Invoice To</h3>
    <p style="margin:4px 0;">' . htmlspecialchars($invoice['invoice_to']) . '</p>
    <p style="margin:4px 0;">' . htmlspecialchars($invoice['client_phone'] ?? '') . '</p>
    <p style="margin:4px 0;">' . htmlspecialchars($invoice['client_email'] ?? '') . '</p>
    <p style="margin:4px 0;">' . htmlspecialchars($invoice['venue_address'] ?? '') . '</p>
    <h4">Event Name: ' . htmlspecialchars($invoice['event_name'] ?? '') . '</h4>
</td>

</tr>
</table>

</td>
</tr>

<!-- TABLE -->
<tr>
<td style="padding:0 20px 20px 20px;">

<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">

<tr style="background:#24170f;color:#fff;">
    <th style="padding:10px;text-align:left;">Description</th>
    <th style="padding:10px;">Qty</th>
    <th style="padding:10px;text-align:right;">Rate</th>
    <th style="padding:10px;text-align:right;">Amount</th>
</tr>

' . $itemRows . '

</table>

</td>
</tr>

<!-- TOTALS -->
<tr>
<td style="padding:0 20px 20px 20px;">

<table width="100%" style="background:#f3e5d3;padding:10px;">
<tr><td>Sub Total</td><td align="right">Rs ' . money_value((float)$invoice['subtotal']) . '</td></tr>
<tr><td>Advance</td><td align="right">Rs ' . money_value((float)$invoice['advance']) . '</td></tr>
<tr><td>Total</td><td align="right">Rs ' . money_value((float)$invoice['total']) . '</td></tr>
<tr><td>Balance</td><td align="right">Rs ' . money_value((float)$invoice['balance']) . '</td></tr>
</table>

</td>
</tr>

<!-- FOOTER -->
<tr>
<td style="padding:20px;border-top:1px solid #dfd0c0;text-align:center;">

<p style="margin-top:10px;font-size:12px;">
' . htmlspecialchars($invoice['notes'] ?: 'Thank you for your business.') . '
</p>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
';

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
