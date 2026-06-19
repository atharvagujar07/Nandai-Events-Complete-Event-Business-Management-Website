<?php
declare(strict_types=1);

function pdf_text_escape(string $text): string
{
    $text = str_replace(["\r", "\n", "\t"], ' ', $text);
    $text = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text) ?: $text;
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
}

function pdf_money(float $value): string
{
    return 'Rs ' . number_format($value, 2);
}

function pdf_cmd_text(float $x, float $y, string $text, int $size = 10, string $font = 'F1', string $color = '0.149 0.106 0.078'): string
{
    return "{$color} rg BT /{$font} {$size} Tf {$x} {$y} Td (" . pdf_text_escape($text) . ") Tj ET\n";
}

function pdf_cmd_multiline(float $x, float $y, string $text, int $size = 9, int $lineHeight = 12, int $maxChars = 68, string $font = 'F1'): string
{
    $out = '';
    $lines = explode("\n", wordwrap(trim($text), $maxChars));
    foreach ($lines as $line) {
        if ($line !== '') {
            $out .= pdf_cmd_text($x, $y, $line, $size, $font);
        }
        $y -= $lineHeight;
    }
    return $out;
}

function pdf_short(string $text, int $maxChars): string
{
    $text = trim($text);
    return strlen($text) > $maxChars ? substr($text, 0, max(0, $maxChars - 3)) . '...' : $text;
}

function pdf_cmd_rect(float $x, float $y, float $w, float $h, string $rgb): string
{
    return "{$rgb} rg {$x} {$y} {$w} {$h} re f\n";
}

function pdf_cmd_line(float $x1, float $y1, float $x2, float $y2, string $rgb = '0.875 0.816 0.753', float $width = 1): string
{
    return "{$rgb} RG {$width} w {$x1} {$y1} m {$x2} {$y2} l S\n";
}

function pdf_fetch_invoice(int $id): array
{
    $stmt = db()->prepare('SELECT * FROM invoices WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $invoice = $stmt->fetch();

    if (!$invoice) {
        throw new RuntimeException('Invoice not found.');
    }

    $itemStmt = db()->prepare('SELECT * FROM invoice_items WHERE invoice_id = :id ORDER BY sort_order, id');
    $itemStmt->execute([':id' => $id]);

    return [$invoice, $itemStmt->fetchAll()];
}

function pdf_image_to_jpeg_bytes(string $path): ?array
{
    if (!is_file($path)) {
        return null;
    }

    $mime = mime_content_type($path);
    if ($mime === 'image/jpeg') {
        [$width, $height] = getimagesize($path);
        return [$width, $height, file_get_contents($path)];
    }

    if (!function_exists('imagecreatetruecolor')) {
        return null;
    }

    $source = function_exists('imagecreatefromstring') ? imagecreatefromstring((string)file_get_contents($path)) : null;

    if (!$source) {
        return null;
    }

    $width = imagesx($source);
    $height = imagesy($source);
    $canvas = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($canvas, 255, 255, 255);
    imagefill($canvas, 0, 0, $white);
    imagecopy($canvas, $source, 0, 0, 0, 0, $width, $height);

    ob_start();
    imagejpeg($canvas, null, 92);
    $bytes = ob_get_clean();

    imagedestroy($source);
    imagedestroy($canvas);

    return [$width, $height, $bytes];
}

function pdf_build_document(array $objects): string
{
    $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
    $offsets = [0];

    foreach ($objects as $number => $body) {
        $offsets[$number] = strlen($pdf);
        $pdf .= "{$number} 0 obj\n{$body}\nendobj\n";
    }

    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    for ($i = 1; $i <= count($objects); $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }
    $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

    return $pdf;
}

function generate_invoice_pdf(array $invoice, array $items): string
{
    $configuredLogo = __DIR__ . '/' . ltrim(app_logo_path(), '/');
    $logoPath = is_file($configuredLogo) && mime_content_type($configuredLogo) === 'image/jpeg'
        ? $configuredLogo
        : __DIR__ . '/assets/images/nandai-events-logo.jpg';
    [$logoW, $logoH] = getimagesize($logoPath);
    $logoBytes = file_get_contents($logoPath);
    $signatureBytes = null;
    $signatureW = 1;
    $signatureH = 1;
    $signatureObject = '';

    if (!empty($invoice['signature_path'])) {
        $signaturePath = __DIR__ . '/' . ltrim((string)$invoice['signature_path'], '/');
        $signatureImage = pdf_image_to_jpeg_bytes($signaturePath);
        if ($signatureImage) {
            [$signatureW, $signatureH, $signatureBytes] = $signatureImage;
            $signatureObject = ' /Sign 8 0 R';
        }
    }

    $content = '';
    $content .= pdf_cmd_rect(0, 0, 595, 842, '1 1 1');
    $content .= pdf_cmd_rect(0, 730, 595, 112, '0.173 0.102 0.063');
    $content .= pdf_cmd_rect(309, 730, 286, 112, '0.392 0.255 0.157');
    $content .= "q 68 0 0 68 32 755 cm /Logo Do Q\n";
    $content .= pdf_cmd_text(112, 800, (string)$invoice['sender_name'], 17, 'F2', '1 1 1');
    $content .= pdf_cmd_text(112, 782, (string)($invoice['sender_tagline'] ?? ''), 8, 'F2', '1 1 1');
    $content .= pdf_cmd_text(405, 790, 'INVOICE', 38, 'F2', '1 1 1');
    $content .= pdf_cmd_text(435, 772, (string)$invoice['invoice_no'], 8, 'F2', '1 1 1');
    if (!empty($invoice['gst_no'])) {
        $content .= pdf_cmd_text(435, 758, 'GST NO: ' . (string)$invoice['gst_no'], 8, 'F2', '1 1 1');
    }

    $content .= pdf_cmd_rect(34, 578, 227, 130, '0.953 0.898 0.827');
    $content .= pdf_cmd_text(50, 682, 'INVOICE FROM', 7, 'F1', '0.478 0.337 0.220');
    $content .= pdf_cmd_text(50, 663, (string)$invoice['sender_name'], 14, 'F2');
    $content .= pdf_cmd_text(50, 646, date('d M Y', strtotime((string)$invoice['invoice_date'])), 9, 'F2');
    $content .= pdf_cmd_text(50, 628, (string)($invoice['sender_phone'] ?? ''), 8, 'F2');
    $content .= pdf_cmd_text(50, 608, (string)($invoice['sender_email'] ?? ''), 8);
    $content .= pdf_cmd_text(335, 694, 'INVOICE TO', 7, 'F1', '0.149 0.106 0.078');
    $content .= pdf_cmd_text(335, 674, (string)$invoice['invoice_to'], 18, 'F2');
    $content .= pdf_cmd_text(335, 648, 'Phone: ' . (string)($invoice['client_phone'] ?? ''), 8);
    $content .= pdf_cmd_text(335, 628, 'Email: ' . (string)($invoice['client_email'] ?? ''), 8);
    $content .= pdf_cmd_text(335, 608, 'Venue Address: ' . (string)($invoice['venue_address'] ?? ''), 8);
    $content .= pdf_cmd_text(335, 588, 'Event Name: ' . (string)($invoice['event_name'] ?? ''), 8);

    $tableY = 542;
    $content .= pdf_cmd_rect(36, $tableY, 523, 26, '0.141 0.091 0.059');
    $content .= pdf_cmd_text(43, $tableY + 9, 'Item Description', 9, 'F2', '1 1 1');
    $content .= pdf_cmd_text(338, $tableY + 9, 'Qty', 9, 'F2', '1 1 1');
    $content .= pdf_cmd_text(432, $tableY + 9, 'Rate', 9, 'F2', '1 1 1');
    $content .= pdf_cmd_text(514, $tableY + 9, 'Amount', 9, 'F2', '1 1 1');

    $rowY = $tableY - 24;
    foreach (array_slice($items, 0, 10) as $item) {
        $content .= pdf_cmd_rect(36, $rowY - 4, 523, 22, '1 1 1');
        $content .= pdf_cmd_text(43, $rowY + 3, pdf_short((string)$item['description'], 58), 8);
        $content .= pdf_cmd_text(338, $rowY + 3, number_format((float)$item['quantity'], 2), 8);
        $content .= pdf_cmd_text(392, $rowY + 3, pdf_money((float)$item['unit_price']), 8);
        $content .= pdf_cmd_text(485, $rowY + 3, pdf_money((float)$item['line_total']), 8);
        $content .= pdf_cmd_line(36, $rowY - 5, 559, $rowY - 5, '0.875 0.816 0.753', 0.8);
        $rowY -= 24;
    }

    if (count($items) > 10) {
        $content .= pdf_cmd_text(52, $rowY + 4, 'Additional items are saved in database; compact PDF shows first 10 items.', 8, 'F1', '0.478 0.337 0.220');
    }

    $bottomY = 306;
    $content .= pdf_cmd_rect(34, $bottomY, 3, 130, '0.721 0.569 0.365');
    $content .= pdf_cmd_text(50, $bottomY + 112, 'ACCOUNT DETAILS', 13, 'F2', '0.149 0.106 0.078');
    $content .= pdf_cmd_text(50, $bottomY + 88, 'Account No: ' . (string)($invoice['account_no'] ?? ''), 8, 'F2');
    $content .= pdf_cmd_text(50, $bottomY + 66, 'Account Name: ' . (string)($invoice['account_name'] ?? ''), 8, 'F2');
    $content .= pdf_cmd_text(50, $bottomY + 44, 'Bank Name: ' . (string)($invoice['bank_name'] ?? ''), 8, 'F2');
    $content .= pdf_cmd_text(50, $bottomY + 22, 'IFSC Code: ' . (string)($invoice['ifsc_code'] ?? ''), 8, 'F2');
    $content .= pdf_cmd_text(50, $bottomY + 2, 'Branch: ' . (string)($invoice['branch_address'] ?? ''), 8, 'F2');

    $content .= pdf_cmd_rect(335, $bottomY + 10, 227, 120, '0.953 0.898 0.827');
    $content .= pdf_cmd_text(348, $bottomY + 108, 'Sub Total:', 8);
    $content .= pdf_cmd_text(480, $bottomY + 108, pdf_money((float)$invoice['subtotal']), 8, 'F2');
    $content .= pdf_cmd_text(348, $bottomY + 84, 'Advance:', 8);
    $content .= pdf_cmd_text(480, $bottomY + 84, pdf_money((float)$invoice['advance']), 8, 'F2');
    $content .= pdf_cmd_text(348, $bottomY + 60, 'Total:', 8);
    $content .= pdf_cmd_text(480, $bottomY + 60, pdf_money((float)$invoice['total']), 8, 'F2');
    $content .= pdf_cmd_text(348, $bottomY + 36, 'Balance:', 8);
    $content .= pdf_cmd_text(480, $bottomY + 36, pdf_money((float)$invoice['balance']), 8, 'F2');

    $notes = trim((string)($invoice['notes'] ?: 'Thank you for your business.'));
    if ($signatureBytes !== null) {
        $sigMaxW = 150.0;
        $sigMaxH = 75.0;
        $sigScale = min($sigMaxW / max(1, $signatureW), $sigMaxH / max(1, $signatureH));
        $sigDrawW = $signatureW * $sigScale;
        $sigDrawH = $signatureH * $sigScale;
        $sigX = 535 - $sigDrawW;
        $sigY = 225;
        $content .= "q {$sigDrawW} 0 0 {$sigDrawH} {$sigX} {$sigY} cm /Sign Do Q\n";
        $content .= pdf_cmd_text(426, 214, 'Authorized Signature', 8, 'F2', '0.478 0.337 0.220');
    } else {
        $content .= pdf_cmd_text(420, 232, 'Authorized Person Signature', 9, 'F2', '0.478 0.337 0.220');
    }
    $content .= pdf_cmd_rect(34, 198, 528, 1, '0.875 0.816 0.753');
    $content .= pdf_cmd_text(207, 174, pdf_short(strtoupper($notes), 88), 10, 'F2', '0.149 0.106 0.078');

    $objects = [
        1 => '<< /Type /Catalog /Pages 2 0 R >>',
        2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
        3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> /XObject << /Logo 6 0 R' . $signatureObject . ' >> >> /Contents 7 0 R >>',
        4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
        6 => '<< /Type /XObject /Subtype /Image /Width ' . $logoW . ' /Height ' . $logoH . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' . strlen((string)$logoBytes) . " >>\nstream\n" . $logoBytes . "\nendstream",
        7 => '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream",
    ];

    if ($signatureBytes !== null) {
        $objects[8] = '<< /Type /XObject /Subtype /Image /Width ' . $signatureW . ' /Height ' . $signatureH . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' . strlen((string)$signatureBytes) . " >>\nstream\n" . $signatureBytes . "\nendstream";
    }

    return pdf_build_document($objects);
}
