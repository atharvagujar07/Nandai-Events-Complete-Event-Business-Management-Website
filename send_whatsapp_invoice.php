<?php
require __DIR__ . '/auth.php';
require_login();
require __DIR__ . '/invoice_pdf_lib.php';

function whatsapp_log(int $invoiceId, string $phone, ?string $mediaId, ?string $messageId, string $status, ?string $error): void
{
    $stmt = db()->prepare(
        'INSERT INTO invoice_whatsapp_logs
          (invoice_id, client_phone, media_id, message_id, status, error_message)
         VALUES
          (:invoice_id, :client_phone, :media_id, :message_id, :status, :error_message)'
    );
    $stmt->execute([
        ':invoice_id' => $invoiceId,
        ':client_phone' => $phone,
        ':media_id' => $mediaId,
        ':message_id' => $messageId,
        ':status' => $status,
        ':error_message' => $error,
    ]);
}

function whatsapp_api_url(string $path): string
{
    return rtrim(WHATSAPP_API_BASE, '/') . '/' . WHATSAPP_GRAPH_VERSION . '/' . ltrim($path, '/');
}

function whatsapp_request(string $url, array $headers, array $payload, bool $multipart = false): array
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('PHP cURL extension is disabled. Enable extension=curl in XAMPP php.ini and restart Apache.');
    }

    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $multipart ? $payload : json_encode($payload),
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_TIMEOUT => 60,
    ]);

    $response = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);

    if ($response === false) {
        throw new RuntimeException('WhatsApp API cURL failed: ' . $curlError);
    }

    $data = json_decode((string)$response, true);
    if ($status < 200 || $status >= 300) {
        $message = $data['error']['message'] ?? $response;
        throw new RuntimeException('WhatsApp API HTTP ' . $status . ': ' . $message);
    }

    return is_array($data) ? $data : [];
}

$id = (int)($_POST['id'] ?? 0);
$phone = '';
$mediaId = null;
$messageId = null;
$tmpFile = null;

try {
    if ($id <= 0) {
        throw new RuntimeException('Invalid invoice ID.');
    }

    [$invoice, $items] = pdf_fetch_invoice($id);
    $phone = preg_replace('/\D+/', '', (string)($invoice['client_phone'] ?? ''));

    if ($phone === '') {
        throw new RuntimeException('Client WhatsApp phone number is required.');
    }

    if (strlen($phone) < 10) {
        throw new RuntimeException('Client phone must include country code, for example +91 9420174884.');
    }

    if (WHATSAPP_CLOUD_TOKEN === '' || WHATSAPP_PHONE_NUMBER_ID === '') {
        throw new RuntimeException('WhatsApp API is not configured. Add WHATSAPP_CLOUD_TOKEN and WHATSAPP_PHONE_NUMBER_ID in config.php.');
    }

    $pdf = generate_invoice_pdf($invoice, $items);
    $fileName = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string)$invoice['invoice_no']) . '.pdf';
    $tmpFile = tempnam(sys_get_temp_dir(), 'invoice_pdf_');
    file_put_contents($tmpFile, $pdf);

    $uploadResponse = whatsapp_request(
        whatsapp_api_url(WHATSAPP_PHONE_NUMBER_ID . '/media'),
        ['Authorization: Bearer ' . WHATSAPP_CLOUD_TOKEN],
        [
            'messaging_product' => 'whatsapp',
            'type' => 'application/pdf',
            'file' => curl_file_create($tmpFile, 'application/pdf', $fileName),
        ],
        true
    );

    $mediaId = $uploadResponse['id'] ?? null;
    if (!$mediaId) {
        throw new RuntimeException('WhatsApp media upload succeeded but no media ID was returned.');
    }

    $messageResponse = whatsapp_request(
        whatsapp_api_url(WHATSAPP_PHONE_NUMBER_ID . '/messages'),
        [
            'Authorization: Bearer ' . WHATSAPP_CLOUD_TOKEN,
            'Content-Type: application/json',
        ],
        [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phone,
            'type' => 'document',
            'document' => [
                'id' => $mediaId,
                'filename' => $fileName,
                'caption' => 'Invoice ' . $invoice['invoice_no'] . ' from ' . $invoice['sender_name'],
            ],
        ]
    );

    $messageId = $messageResponse['messages'][0]['id'] ?? null;
    whatsapp_log((int)$invoice['id'], $phone, $mediaId, $messageId, 'sent', null);

    header('Location: invoice_view.php?id=' . (int)$invoice['id'] . '&email_status=' . urlencode('WhatsApp PDF sent successfully.'));
} catch (Throwable $error) {
    if ($id > 0) {
        try {
            whatsapp_log($id, $phone, $mediaId, $messageId, 'failed', $error->getMessage());
        } catch (Throwable) {
        }
    }

    header('Location: invoice_view.php?id=' . $id . '&email_status=' . urlencode($error->getMessage()));
} finally {
    if ($tmpFile && is_file($tmpFile)) {
        @unlink($tmpFile);
    }
}
