<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/session_manager.php';

use App\CloudflareManager;
use App\TelegramBot;
use App\Database;
// ==========================================================


header('Content-Type: application/json');


function json_response($data) {
    ob_clean();
    echo json_encode($data);
    exit();
}

set_exception_handler(function($e) {
    error_log("AJAX Handler Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    json_response(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
});
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) { return; }
    error_log("AJAX Handler Error: [$severity] $message in $file on line $line");
    json_response(['status' => 'error', 'message' => 'Internal Server Error. Cek log server.']);
});

try {
    session_start();

    require_once __DIR__ . '/autoload.php';
    require_once __DIR__ . '/src/config.php';
    

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        json_response(['status' => 'error', 'message' => 'Akses ditolak.']);
    }

    $recordId = filter_input(INPUT_POST, 'recordId', FILTER_UNSAFE_RAW);
    $subdomain = filter_input(INPUT_POST, 'subdomainName', FILTER_UNSAFE_RAW);
    $selectedBaseDomain = filter_input(INPUT_POST, 'baseDomainSelect', FILTER_UNSAFE_RAW);
    $type = filter_input(INPUT_POST, 'recordType', FILTER_UNSAFE_RAW);
    $target = filter_input(INPUT_POST, 'targetValue', FILTER_UNSAFE_RAW);

    if (empty($recordId) || empty($subdomain) || empty($selectedBaseDomain) || empty($type) || empty($target)) {
        json_response(['status' => 'error', 'message' => 'Semua field harus diisi.']);
    }
    
    $db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $managedDomainsRaw = $db->fetchAll("SELECT domain_name, zone_id FROM managed_domains WHERE is_active = TRUE");
    $managedDomains = array_column($managedDomainsRaw, 'zone_id', 'domain_name');
    $selectedZoneId = $managedDomains[$selectedBaseDomain] ?? null;

    if (!$selectedZoneId) {
        json_response(['status' => 'error', 'message' => 'Domain dasar tidak valid.']);
    }

    $fullDomain = "{$subdomain}.{$selectedBaseDomain}";
    $cloudflareManager = new CloudflareManager(CLOUDFLARE_API_TOKEN, $selectedZoneId, CLOUDFLARE_EMAIL);
    $updateResult = $cloudflareManager->updateDnsRecord($recordId, $type, $fullDomain, $target, true, 120);

    if (isset($updateResult['success']) && $updateResult['success'] === true) {
        $telegramBot = new App\TelegramBot(TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID);
        $telegramBot->logEvent('Subdomain Diperbarui (via Modal) 🔄', ['Domain' => $fullDomain, 'Target' => $target]);
        
        json_response([
            'status' => 'success', 
            'message' => "Subdomain '{$fullDomain}' berhasil diperbarui!",
            'data' => [ 'updated_record' => [ 'name' => $fullDomain, 'type' => $type, 'content' => $target ]]
        ]);
    } else {
        json_response(['status' => 'error', 'message' => $updateResult['message'] ?? 'Gagal memperbarui di Cloudflare.']);
    }

} catch (Throwable $e) {
    error_log("Fatal Throwable in AJAX Handler: " . $e->getMessage());
    json_response(['status' => 'error', 'message' => 'Kesalahan fatal: ' . $e->getMessage()]);
}
