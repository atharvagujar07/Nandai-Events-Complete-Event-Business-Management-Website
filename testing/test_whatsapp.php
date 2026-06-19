<?php
declare(strict_types=1);
require '../config.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

$phone = '918805545888'; // Replace with recipient number

$url = 'https://graph.facebook.com/' .
       WHATSAPP_GRAPH_VERSION . '/' .
       WHATSAPP_PHONE_NUMBER_ID . '/messages';

$payload = [
    'messaging_product' => 'whatsapp',
    'to' => $phone,
    'type' => 'text',
    'text' => [
        'preview_url' => false,
        'body' => 'Hello from WhatsApp Cloud API'
    ]
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . WHATSAPP_CLOUD_TOKEN,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);

$response = curl_exec($ch);

if ($response === false) {
    die('cURL Error: ' . curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "<h3>HTTP Status: {$httpCode}</h3>";
echo '<pre>';
print_r(json_decode($response, true));
echo '</pre>';

