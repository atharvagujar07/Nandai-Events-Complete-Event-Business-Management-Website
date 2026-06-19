<?php
declare(strict_types=1);

function smtp_read($socket): string
{
    $response = '';

    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (strlen($line) >= 4 && $line[3] === ' ') {
            break;
        }
    }

    return $response;
}

function smtp_expect($socket, array $codes): string
{
    $response = smtp_read($socket);
    $code = (int)substr($response, 0, 3);

    if (!in_array($code, $codes, true)) {
        throw new RuntimeException('SMTP error: ' . trim($response));
    }

    return $response;
}

function smtp_send_line($socket, string $line, array $expect = [250]): string
{
    fwrite($socket, $line . "\r\n");
    return smtp_expect($socket, $expect);
}

function smtp_dot_stuff(string $message): string
{
    $message = str_replace(["\r\n", "\r"], "\n", $message);
    $lines = explode("\n", $message);

    foreach ($lines as &$line) {
        if (str_starts_with($line, '.')) {
            $line = '.' . $line;
        }
    }

    return implode("\r\n", $lines);
}

function smtp_send_email(string $to, string $subject, string $mimeBody, array $headers, string $from): bool
{
    $socket = fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, SMTP_TIMEOUT);

    if (!$socket) {
        throw new RuntimeException("SMTP connection failed: {$errstr} ({$errno})");
    }

    stream_set_timeout($socket, SMTP_TIMEOUT);

    try {
        smtp_expect($socket, [220]);
        smtp_send_line($socket, 'EHLO localhost');

        if (SMTP_SECURE === 'tls') {
            smtp_send_line($socket, 'STARTTLS', [220]);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('SMTP TLS handshake failed.');
            }
            smtp_send_line($socket, 'EHLO localhost');
        }

        smtp_send_line($socket, 'AUTH LOGIN', [334]);
        smtp_send_line($socket, base64_encode(SMTP_USERNAME), [334]);
        smtp_send_line($socket, base64_encode(SMTP_PASSWORD), [235]);
        smtp_send_line($socket, 'MAIL FROM:<' . $from . '>');
        smtp_send_line($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
        smtp_send_line($socket, 'DATA', [354]);

        $messageHeaders = $headers;
        $messageHeaders[] = 'To: <' . $to . '>';
        $messageHeaders[] = 'Subject: ' . $subject;
        $messageHeaders[] = 'Date: ' . date(DATE_RFC2822);
        $messageHeaders[] = 'Message-ID: <' . bin2hex(random_bytes(12)) . '@invoiceapp.local>';

        fwrite($socket, smtp_dot_stuff(implode("\r\n", $messageHeaders) . "\r\n\r\n" . $mimeBody) . "\r\n.\r\n");
        smtp_expect($socket, [250]);
        smtp_send_line($socket, 'QUIT', [221]);
        fclose($socket);

        return true;
    } catch (Throwable $error) {
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        throw $error;
    }
}

