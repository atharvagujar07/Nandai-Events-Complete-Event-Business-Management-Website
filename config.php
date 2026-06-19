<?php
declare(strict_types=1);

const DB_HOST = 'localhost';
const DB_NAME = 'invoiceapp';
const DB_USER = 'root';
const DB_PASS = '';
const APP_BASE_URL = 'http://localhost/invoice-generator';
const MAIL_FROM_NAME = 'Nandai Events';
const MAIL_REPLY_TO = 'nandaievents@gmail.com';
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_SECURE = 'tls';
const SMTP_USERNAME = 'nandaievents@gmail.com';
const SMTP_PASSWORD = 'gxmj xhcy ibxt howz';
const SMTP_TIMEOUT = 30;
const WHATSAPP_CLOUD_TOKEN = 'EAGBEq5hUXGMBRsGx9TRbPLIsBa6He7YX1kGaPMWZAZB8c7YIBOMWUdQHRO3lFtylQ629sD9CVNrS0Gqlf3Os34I8RfKeUZBlD6a0nbGWozfrZCmiFAqvGZA6D95v5JNAtiyul3djwFW8HMGvQ37T5jdjmZCmZCyhWckNtxA3lZBOaiNuBTbj7C13wCFreDC5dwfEqKSsjBE8fpYUZBPEtyyAXxdaAiDCtbARTYU98KNpNvymk0CZC80pr8oitpgut1xZCg6OEfJxtOab3JWyUvcROHlPaXw6AZDZD';
const WHATSAPP_PHONE_NUMBER_ID = '1256511130868021';
const WHATSAPP_GRAPH_VERSION = 'v20.0';
const WHATSAPP_API_BASE = 'https://graph.facebook.com';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function app_settings(): array
{
    static $settings = null;

    if (is_array($settings)) {
        return $settings;
    }

    try {
        $row = db()->query('SELECT * FROM app_settings WHERE id = 1')->fetch();
    } catch (Throwable) {
        $row = false;
    }

    $settings = $row ?: [
        'logo_path' => 'assets/images/nandai-events-logo.png',
        'theme_name' => 'custom',
        'primary_color' => '#7a5638',
        'secondary_color' => '#24170f',
        'accent_color' => '#b8915d',
        'soft_color' => '#f3e5d3',
        'page_bg_color' => '#f5efe8',
        'font_color' => '#261b14',
        'popup_color' => '#f3e5d3',
        'button_color' => '#7a5638',
    ];

    return $settings;
}

function app_logo_path(): string
{
    $settings = app_settings();
    return (string)($settings['logo_path'] ?: 'assets/images/nandai-events-logo.png');
}

function app_theme_style(): string
{
    $settings = app_settings();
    return sprintf(
        '<style>:root{--purple:%s;--purple-deep:%s;--gold:%s;--purple-soft:%s;--page:%s;--ink:%s;} .button,button.button{background:%s;} .notice{background:%s;color:%s;}</style>',
        htmlspecialchars((string)$settings['primary_color']),
        htmlspecialchars((string)$settings['secondary_color']),
        htmlspecialchars((string)$settings['accent_color']),
        htmlspecialchars((string)$settings['soft_color']),
        htmlspecialchars((string)($settings['page_bg_color'] ?? '#f5efe8')),
        htmlspecialchars((string)($settings['font_color'] ?? '#261b14')),
        htmlspecialchars((string)($settings['button_color'] ?? $settings['primary_color'])),
        htmlspecialchars((string)($settings['popup_color'] ?? $settings['soft_color'])),
        htmlspecialchars((string)($settings['font_color'] ?? '#261b14'))
    );
}

function money_value(float $value): string
{
    return number_format($value, 2);
}

function generate_invoice_number(): string
{
    return 'INV-' . date('Ymd-His') . '-' . random_int(100, 999);
}

function app_url(string $path): string
{
    return rtrim(APP_BASE_URL, '/') . '/' . ltrim($path, '/');
}

function save_signature_upload(array $file, ?string $existingPath = null): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $existingPath;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Signature upload failed.');
    }

    $allowed = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    $mime = mime_content_type((string)$file['tmp_name']);

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Signature must be PNG, JPG, WEBP, or GIF.');
    }

    $dir = __DIR__ . '/uploads/signatures';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $name = 'signature-' . date('YmdHis') . '-' . random_int(1000, 9999) . '.' . $allowed[$mime];
    $target = $dir . '/' . $name;

    if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save signature image.');
    }

    return 'uploads/signatures/' . $name;
}

function save_logo_upload(array $file, ?string $existingPath = null): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $existingPath;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Logo upload failed.');
    }

    $allowed = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
    ];
    $mime = mime_content_type((string)$file['tmp_name']);

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Logo must be PNG, JPG, or WEBP.');
    }

    $dir = __DIR__ . '/uploads/logos';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $name = 'logo-' . date('YmdHis') . '-' . random_int(1000, 9999) . '.' . $allowed[$mime];
    $target = $dir . '/' . $name;

    if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save logo image.');
    }

    return 'uploads/logos/' . $name;
}

function make_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');
    return $value !== '' ? $value : 'category-' . date('YmdHis');
}

function safe_upload_name(string $name): string
{
    $info = pathinfo($name);
    $base = make_slug($info['filename'] ?? 'image');
    $ext = strtolower($info['extension'] ?? 'jpg');
    return $base . '-' . date('YmdHis') . '-' . random_int(1000, 9999) . '.' . $ext;
}

function ensure_dir(string $dir): void
{
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

function validate_image_upload(string $tmpPath): array
{
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    $mime = mime_content_type($tmpPath);

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, WEBP, and GIF images are allowed.');
    }

    if (!getimagesize($tmpPath)) {
        throw new RuntimeException('Uploaded file is not a valid image.');
    }

    return [$mime, $allowed[$mime]];
}
