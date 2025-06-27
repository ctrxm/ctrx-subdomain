<?php

namespace App;

class TelegramBot
{
    private $client;
    private $botToken;
    private $chatId;
    private $baseUrl;

    public function __construct($botToken, $chatId)
    {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
        $this->baseUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    private function makeTelegramRequest($method, $endpoint, $payload = [])
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("Telegram cURL Error: " . $error);
            return false;
        }

        $data = json_decode($response, true);
        return $data['ok'] ?? false;
    }

    public function sendMessage($text, $parseMode = 'MarkdownV2', $replyMarkup = null)
    {
        $payload = [
            'chat_id'    => $this->chatId,
            'text'       => $text,
            'parse_mode' => $parseMode,
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }
        return $this->makeTelegramRequest('POST', 'sendMessage', $payload);
    }

    public function sendInlineKeyboard($text, $inlineKeyboardButtons, $parseMode = 'MarkdownV2')
    {
        $replyMarkup = ['inline_keyboard' => $inlineKeyboardButtons];
        return $this->sendMessage($text, $parseMode, $replyMarkup);
    }

    public function logEvent($event, $data, $addWebsiteButton = false) {
        $message = "🌐 *Kringg Ada Aktifitas Baru Nih🤖:* \n\n";
        $message .= "• *Event:* `" . $this->escapeMarkdownV2($event) . "`\n";
        foreach ($data as $key => $value) {
            $message .= "• *" . $this->escapeMarkdownV2($key) . ":* `" . $this->escapeMarkdownV2($value) . "`\n";
        }
        
        $inlineKeyboard = [];
        if ($addWebsiteButton) {
            $inlineKeyboard[] = [['text' => 'Kunjungi CTRX.ID', 'url' => 'https://ctrxl.id']]; // GANTI DENGAN URL WEBSITE MU!
        }
        
        return $this->sendInlineKeyboard($message, $inlineKeyboard);
    }

    private function escapeMarkdownV2($text) {
        $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        foreach ($specialChars as $char) {
            $text = str_replace($char, '\\' . $char, $text);
        }
        return $text;
    }

    public function answerCallbackQuery($callbackQueryId, $text = '', $showAlert = false) {
        $payload = [
            'callback_query_id' => $callbackQueryId,
            'text'              => $text,
            'show_alert'        => $showAlert
        ];
        return $this->makeTelegramRequest('POST', 'answerCallbackQuery', $payload);
    }
}