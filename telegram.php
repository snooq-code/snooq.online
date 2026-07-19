<?php
// telegram.php - Send notifications to Telegram

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Only POST requests allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'] ?? '';

if (empty($message)) {
    echo json_encode(['error' => 'Message is required']);
    exit();
}

// Your Telegram Bot Details
$botToken = '8797651346:AAGKGVsY37o7WTiopQx2dDk60IjoB9B9tQ0';
$chatId = '8797651346';

// Send to Telegram
$telegramUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";

$postData = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'Markdown'
];

$ch = curl_init($telegramUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo json_encode(['success' => true, 'message' => 'Telegram notification sent!']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to send']);
}
?>
