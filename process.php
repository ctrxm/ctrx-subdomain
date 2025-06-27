<?php
// =====================================================================
// ============== FILE PROSES FINAL (LENGKAP & TIDAK DIPOTONG) =========
// =====================================================================

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/session_manager.php'; // Memastikan session_start() dipanggil

use App\CloudflareManager;
use App\TelegramBot;
use App\Database;
use App\LicenseManager;

/**
 * Fungsi helper universal untuk mengirimkan respon standar dan menghentikan script.
 * @param string $status 'success' or 'error'
 * @param string $message Pesan yang akan ditampilkan ke pengguna
 * @param string $redirectUrl URL tujuan redirect
 * @param array $data Data tambahan (opsional)
 */
function send_response($status, $message, $redirectUrl, $data = []) {
    $_SESSION['status_message'] = $message;
    $_SESSION['status_type'] = ($status === 'success') ? 'success' : 'error';
    if ($status === 'success' && !empty($data['newly_created_domain'])) {
        $_SESSION['newly_created_domain'] = $data['newly_created_domain'];
    }
    header('Location: ' . $redirectUrl);
    exit();
}

// =================================================================
// ================== PERSIAPAN & VALIDASI AWAL ====================
// =================================================================

// Inisialisasi wajib
$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$telegramBot = new TelegramBot(TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID);

// Validasi request method harus POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit();
}

// Validasi lisensi untuk semua aksi
$licenseManager = new LicenseManager(LICENSE_SERVER_API_URL, ($_SERVER['HTTP_HOST'] ?? ''), $db);
if (!$licenseManager->getAndVerifyStoredLicense()['status']) {
    send_response('error', 'Lisensi tidak valid atau tidak ditemukan.', '/license.php');
}

// Ambil aksi utama dari form
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

// =================================================================
// ============== LOGIKA UTAMA BERDASARKAN AKSI ====================
// =================================================================

switch ($action) {

    // --- AKSI PENGGUNA BIASA ---
    case 'add':
        $redirectUrl = '/create';
        try {
            if (!isset($_SESSION['user_id'])) throw new Exception('Sesi Anda telah berakhir. Silakan login kembali.');
            $user_id = $_SESSION['user_id'];

            $recaptchaResponse = filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_UNSAFE_RAW);
            if (empty($recaptchaResponse)) throw new Exception('Verifikasi reCAPTCHA gagal.');
            
            $recaptchaSecret = RECAPTCHA_SECRET_KEY;
            $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}";
            $verifyResponse = file_get_contents($verifyUrl);
            $responseData = json_decode($verifyResponse);
            if (!$responseData || !$responseData->success || $responseData->score < 0.5) throw new Exception('Verifikasi gagal, terdeteksi sebagai bot.');

            $subdomainName = strtolower(trim(filter_input(INPUT_POST, 'subdomainName', FILTER_SANITIZE_SPECIAL_CHARS)));
            $baseDomain = filter_input(INPUT_POST, 'baseDomainSelect', FILTER_SANITIZE_SPECIAL_CHARS);
            $recordType = filter_input(INPUT_POST, 'recordType', FILTER_SANITIZE_SPECIAL_CHARS);
            $targetValue = trim(filter_input(INPUT_POST, 'targetValue', FILTER_SANITIZE_SPECIAL_CHARS));

            if (empty($subdomainName) || empty($baseDomain) || empty($recordType) || empty($targetValue)) throw new Exception('Semua field wajib diisi.');
            if (!preg_match('/^[a-z0-9.-]+$/', $subdomainName)) throw new Exception('Nama subdomain hanya boleh berisi huruf (a-z), angka (0-9), titik (.), dan strip (-).');
            
            $managedDomainData = $db->fetch("SELECT zone_id FROM managed_domains WHERE domain_name = ?", [$baseDomain]);
            if (empty($managedDomainData)) throw new Exception('Domain dasar yang dipilih tidak valid.');
            
            $zoneId = $managedDomainData['zone_id'];
            $fullDomainName = $subdomainName . '.' . $baseDomain;

            $cfManager = new CloudflareManager(CLOUDFLARE_API_TOKEN, $zoneId, CLOUDFLARE_EMAIL);
            $result = $cfManager->addDnsRecord($recordType, $fullDomainName, $targetValue, true);

            if (isset($result['success']) && $result['success']) {
                $cloudflareRecordId = $result['result']['id'];
                $db->execute("INSERT INTO user_subdomain_records (user_id, cloudflare_record_id, record_name, record_type, zone_id) VALUES (?, ?, ?, ?, ?)", [$user_id, $cloudflareRecordId, $fullDomainName, $recordType, $zoneId]);
                send_response('success', "Subdomain '{$fullDomainName}' berhasil dibuat!", $redirectUrl, ['newly_created_domain' => $fullDomainName]);
            } else {
                throw new Exception($result['message'] ?? 'Gagal membuat subdomain di Cloudflare.');
            }
        } catch (Exception $e) {
            send_response('error', $e->getMessage(), $redirectUrl);
        }
        break;

    case 'delete_subdomain':
        $redirectUrl = '/dashboard';
        try {
            if (!isset($_SESSION['user_id'])) throw new Exception('Sesi Anda telah berakhir. Silakan login kembali.');
            
            $user_id = $_SESSION['user_id'];
            $cloudflareRecordId = filter_input(INPUT_POST, 'cloudflare_record_id', FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($cloudflareRecordId)) throw new Exception('ID Record tidak valid.');

            $recordData = $db->fetch("SELECT zone_id FROM user_subdomain_records WHERE cloudflare_record_id = ? AND user_id = ?", [$cloudflareRecordId, $user_id]);
            if (!$recordData) throw new Exception('Aksi tidak diizinkan. Anda bukan pemilik record ini.');
            
            $zoneId = $recordData['zone_id'];
            $cfManager = new CloudflareManager(CLOUDFLARE_API_TOKEN, $zoneId, CLOUDFLARE_EMAIL);
            $deleteResult = $cfManager->deleteDnsRecord($cloudflareRecordId);

            if (isset($deleteResult['success']) && $deleteResult['success']) {
                $db->execute("DELETE FROM user_subdomain_records WHERE cloudflare_record_id = ?", [$cloudflareRecordId]);
                send_response('success', 'Subdomain berhasil dihapus.', $redirectUrl);
            } else {
                throw new Exception($deleteResult['message'] ?? 'Gagal menghapus subdomain di Cloudflare.');
            }
        } catch (Exception $e) {
            send_response('error', $e->getMessage(), $redirectUrl);
        }
        break;

    // --- AKSI KHUSUS DARI ADMIN PANEL ---
    case 'delete': // Hapus Subdomain dari Admin Panel
        $redirectUrl = '/dashboard?view=admin';
        try {
            if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) throw new Exception('Akses ditolak.');
            
            $cloudflareRecordId = filter_input(INPUT_POST, 'recordId', FILTER_SANITIZE_SPECIAL_CHARS);
            $recordBaseDomain = filter_input(INPUT_POST, 'recordBaseDomain', FILTER_SANITIZE_SPECIAL_CHARS);

            if (empty($cloudflareRecordId) || empty($recordBaseDomain)) throw new Exception('Informasi Record ID dan Domain Dasar tidak lengkap.');

            $domainData = $db->fetch("SELECT zone_id FROM managed_domains WHERE domain_name = ?", [$recordBaseDomain]);
            if (empty($domainData)) throw new Exception('Gagal menemukan Zone ID untuk domain dasar.');
            
            $zoneId = $domainData['zone_id'];
            $cfManager = new CloudflareManager(CLOUDFLARE_API_TOKEN, $zoneId, CLOUDFLARE_EMAIL);
            $deleteResult = $cfManager->deleteDnsRecord($cloudflareRecordId);

            if (isset($deleteResult['success']) && $deleteResult['success']) {
                $db->execute("DELETE FROM user_subdomain_records WHERE cloudflare_record_id = ?", [$cloudflareRecordId]);
                send_response('success', 'Subdomain berhasil dihapus dari Cloudflare.', $redirectUrl);
            } else {
                throw new Exception($deleteResult['message'] ?? 'Gagal menghapus subdomain dari Cloudflare.');
            }
        } catch (Exception $e) {
            send_response('error', $e->getMessage(), $redirectUrl);
        }
        break;

    case 'domain_action': // Aksi untuk Domain Dasar (Tambah/Hapus)
        $redirectUrl = '/dashboard?view=admin';
        try {
            if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) throw new Exception('Akses ditolak.');

            $domainActionType = trim(filter_input(INPUT_POST, 'domain_action_type', FILTER_SANITIZE_SPECIAL_CHARS));
            
            if ($domainActionType === 'add_domain') {
                $domainName = trim(filter_input(INPUT_POST, 'domain_name', FILTER_SANITIZE_SPECIAL_CHARS));
                $zoneId = trim(filter_input(INPUT_POST, 'zone_id', FILTER_SANITIZE_SPECIAL_CHARS));

                if (empty($domainName) || empty($zoneId)) throw new Exception('Nama Domain dan Zone ID wajib diisi.');
                
                $existingDomain = $db->fetch("SELECT id FROM managed_domains WHERE domain_name = ?", [$domainName]);
                if ($existingDomain) throw new Exception("Domain '{$domainName}' sudah ada.");

                if ($db->execute("INSERT INTO managed_domains (domain_name, zone_id, is_active) VALUES (?, ?, TRUE)", [$domainName, $zoneId])) {
                    send_response('success', 'Domain dasar baru berhasil ditambahkan.', $redirectUrl);
                } else {
                    throw new Exception('Gagal menyimpan domain dasar ke database.');
                }
            
            } elseif ($domainActionType === 'delete_domain') {
                $domainId = filter_input(INPUT_POST, 'domain_id', FILTER_SANITIZE_NUMBER_INT);
                if (empty($domainId)) throw new Exception('ID Domain Dasar tidak valid.');
                
                if ($db->execute("DELETE FROM managed_domains WHERE id = ?", [$domainId])) {
                    send_response('success', 'Domain dasar berhasil dihapus.', $redirectUrl);
                } else {
                    throw new Exception('Gagal menghapus domain dasar dari database.');
                }
            
            } else {
                throw new Exception('Tipe aksi domain tidak valid.');
            }
        } catch (Exception $e) {
            send_response('error', $e->getMessage(), $redirectUrl);
        }
        break;

    // --- Aksi Default Jika Tidak Dikenali ---
    default:
        $fallbackUrl = isset($_SESSION['user_id']) ? '/dashboard.php' : '/login.php';
        send_response('error', "Aksi '{$action}' tidak dikenal.", $fallbackUrl);
        break;
}
?>
