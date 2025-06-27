<?php

define('CLOUDFLARE_API_TOKEN', 'YOUR_CF_API_TOKEN'); //Ganti Ini
define('CLOUDFLARE_EMAIL', 'CF_EMAIL'); // Ganti Ini

define('LICENSE_SERVER_API_URL', 'https://api.ctrxl.id/verify.php'); //JANGAN DI HAPUS
// --- Kredensial Database ---
define('DB_HOST', 'localhost'); // Ganti Dengan Host Databasemu
define('DB_USER', 'Ganti Ini'); // Ganti Dengan Username Databasemu
define('DB_PASS', 'Ganti Ini'); // Ganti Dengan Password Databasemu
define('DB_NAME', 'Ganti Ini'); // Ganti Dengan Nama Databasemu

define('APP_SESSION_PREFIX', 'app_server_');

// —- BOT TELEGRAM —-
// --- Ubah TELEGRAM_BOT_TOKEN Dan TELGGRAM_CHAT_ID Punya Milikmu ---
define('TELEGRAM_BOT_TOKEN', 'TG_BOT_TOKEN');
define('TELEGRAM_CHAT_ID', 'TG_CHAT_ID_CHANNEL');

// --- Kredensial Admin ---
// --- Ubah ADMIN_USERNAME Dan ADMIN_PASSWORD_HASH ---
define('ADMIN_USERNAME', 'gantiini');
define('ADMIN_PASSWORD_HASH', '$2a$12$klMJ0jcK6p9XkNcyIuYbPud6iq9OhfhmKEn6fYfCDWianHcEZmfaq'); // MD5 (Hendra123)


// Untuk reCAPTCHA v3
// --- Ubah RECAPTCHA_SITE_KEY Dan RECAPTCHA_SCREET_KEY ---
define('RECAPTCHA_SITE_KEY', 'RCPT_SITE_KEY');
define('RECAPTCHA_SECRET_KEY', 'RCPT_SCRT_KEY');
