<?php

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/TelegramBot.php';

$telegramBot = new TelegramBot(TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID);

$input = file_get_contents('php://input');
$update = json_decode($input, true);

//file_put_contents('telegram_webhook_log.txt', date('Y-m-d H:i:s') . " - " . json_encode($update, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);


if (isset($update['callback_query'])) {
    $callbackQuery = $update['callback_query'];
    $data = $callbackQuery['data'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $messageId = $callbackQuery['message']['message_id'];

    if (strpos($data, 'verify_dns_') === 0) {
        $recordId = explode('_', $data)[2];
        $telegramBot->sendMessage("Memverifikasi DNS untuk record ID: {$recordId}...", 'MarkdownV2');
        $telegramBot->answerCallbackQuery($callbackQuery['id'], 'Verifikasi DNS sedang diproses!');
    }

} elseif (isset($update['message'])) {
    $message = $update['message'];
    $text = $message['text'] ?? '';
    $chatId = $message['chat']['id'];

    if ($text === '/start') {
        $telegramBot->sendMessage("Selamat datang! Saya adalah bot pengelola subdomain Cloudflare Anda.", 'MarkdownV2');
    } elseif ($text === '/help') {
        $telegramBot->sendMessage("Gunakan antarmuka web untuk mengelola subdomain. Saya akan memberi notifikasi di sini.", 'MarkdownV2');
    }

}

http_response_code(200);
echo "OK";
?>
